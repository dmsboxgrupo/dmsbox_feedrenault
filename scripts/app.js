const RENAULT_USER = 'renault_user';

const DEFAULT_CALLBACK = () => {};

class App {
	
	constructor() {
		
		this.page = {};
		this.pages = {};
		this.user = {};
		this.global = {};
		this.urlParams = {};
		
		signal.on('page_load', ( e ) => {
			
			let page = e.page;
			
			page = page.substr( 0, page.indexOf( '.' ) );
			
			this.page[e.target] = page;
			
		});
		
		signal.on( 'params', ( data ) => {
			
			app.lastParams = app.currentParams || {};
			app.currentParams = Object.assign( {}, data );
			
			app.lastPage = app.currentPage;
			app.currentPage = data.p;
			
		} );

		signal.on('search', ( e ) => {

			const name = this.page['content'];
			const eventName = `search_${name}`;
			
			if ( signal.has( eventName ) ) {
				
				signal.emit( eventName, e );
				
				return false;
				
			}
			
		});

/*
		signal.on('params', ( e ) => {
			
			const search = e.search;
			
			if (search) {
				
				ui.buscar( search, false );
				
			}
			
		});
*/

		window.addEventListener('hashchange', () => {

			this.loadPage();
		  
		}, false);
		
		this.loadHash();
		
	}
	
	loadHash() {
		
		const hash = location.hash.substr(1);
		const params = new URLSearchParams(hash);
		
		this.urlParams = {};
		
		for (const key of params.keys()) {
			
			this.urlParams[ key ] = params.get( key );
			
		}
		
	}
	
	loadPage() {
		
		const lastPage = this.urlParams.p;
		
		this.loadHash();

		const page = this.urlParams.p;

		if (page != lastPage) { 
		
			$(`[page]`).removeClass('active-nav ativo');
			$(`[page="${page}"]`).addClass('active-nav ativo');
			$(`.subpage-${page}`).addClass('active-nav ativo');
			ui.loadPage('content', `${page}.html`);
		
		}
		
		signal.emit( 'params', this.urlParams );
		
	}
	
	setStorage( name, value ) {
		
		localStorage.setItem( name, value ? JSON.stringify( value ) : null );
		
	}
	
	getStorage( name, callback ) {
		
		const browserData = localStorage.getItem( name );
		
		if ( browserData ) {
			
			callback( JSON.parse( browserData ) );
			
		} else {
			
			callback( null );
			
		}
		
	}
	
	async share( data ) {
		
		try {
			
			await navigator.share( data );
		
		} catch(e) {
			
			ui.popupMensagem(`<b>Não foi possível compartilhar.</b><br><br>${e}`, "OK");
			
		}
		
	}
	
	restoreLogin( callback ) {
		
		this.getStorage( RENAULT_USER, ( data ) => {
			
			if (data) {
				
				this.login( data.email, data.password, true, callback );
				
			} else {
			
				callback();
				
			}
			
		})
		
	}
	
	logout( callback ) {
		
		this.setStorage( RENAULT_USER, null );
		
		signal.emit( 'logout', this.user );
		
		this.user = {};
		
	}
	
	setMeta( name, value = null ) {
		
		const params = {
			q: 'set_meta',
			name
		};
		
		if ( value !== null ) {
			
			params.type = typeof value;
			params.value = value;
			
		}
		
		this.request( params, ( res ) => {
			
			this.user.metadata = res.metadata;
			
		} );
		
	}
	
	download( uri, name = '' ) {
		
		const link = document.createElement("a");
		const filename = uri.split(/(\\|\/)/g).pop();
		// If you don't know the name or want to use
		// the webserver default set name = ''
		//link.setAttribute('download', 'feedrenault_' + filename);
		if( name ) link.setAttribute('download', name);
		else link.setAttribute('download', 'feedrenault_' + filename);
		link.href = uri;
		link.target = '_blank';
		document.body.appendChild(link);
		link.click();
		link.remove();
		
		//$.fileDownload(uri);
		
	}
	
	login( email, password, store, callback = DEFAULT_CALLBACK ) {
		
		const params = {
			q: 'login',
			email,
			password
		};
		
		const url = new URL( window.location );
		
		const platformName = url.searchParams.get('app');
		const buildVersion = url.searchParams.get('build');
		const deviceKey = url.searchParams.get('device');
		
		if (deviceKey) {
			
			params.os = platformName;
			params.build = buildVersion;
			params.device = deviceKey;
			
		}
		
		signal.emit( 'login_request', params );
		
		this.setStorage( RENAULT_USER, null );
		
		this.request( params, ( res ) => {
			
			if (res.secret_key) {
				
				this.user = res;
				
				if (store) {
					
					this.setStorage( RENAULT_USER, {
						email, password
					} );
					
				}

				// Armazena ID em dataLayer para registro pelo GA
				if ( window.dataLayer && window.dataLayer[0] ) {

					window.dataLayer[0].user_name = res.name;
					window.dataLayer[0].user_email = email;
					window.dataLayer[0].user_bir = res.bir;
					window.dataLayer[0].user_level = res.category_name;

				}

			} else {
				
				this.user = {};
				
			}
			
			signal.emit( 'login_response', res );
			
			callback( res );
			
		} );
		
	}
	
	url( path ) {
		
		//const HOST = 'http://localhost/renault/';
		const HOST = 'https://feedrenault.com.br/';
		
		return HOST + path;
		
	}
	
	request( params, callback = DEFAULT_CALLBACK ) {
	
		const API = this.url( 'api.php' );
		
		if (this.secretKey) {
			
			params.secret_key = this.secretKey;
			
		}
		
		params.time = Date.now();
		params.version = 3;
		
		const controller = new AbortController();
		const signal = controller.signal;
		
		const urlParams = new URLSearchParams(params);
		
		console.log( "REQUEST ->", `${API}?${urlParams}` );
		
		fetch( API, { signal, method: 'POST', body: urlParams } )
			.then(data => data.text())
			.then((text) => {
				
				const response = JSON.parse( text );
				
				//callback( response );
				
				// simulate latency
				setTimeout( () => { callback( response ) }, 0 );
				
			}).catch((error) => {
				
				//ui.popupMensagem(`<b>Falha ao acessar a internet.</b><br>${error}`);
				
			});

		return controller;

	}

	async uploadImage( file ) {
	
		return new Promise((resolve, reject) => {

			let url = this.url( 'api.php' );
			
			url += "?q=upload";

			if (!this.secretKey) throw ("Secret Key not found");

			url += "&secret_key=" + this.secretKey;
			
			console.log( "UPLOAD ->", `${url}` );
			
			var body = new FormData();
			body.append('file', file);
			
			fetch( url, { method: 'POST', body } )
				.then(data => data.text())
				.then((text) => {
					
					const response = JSON.parse( text );
					resolve(response);
					
				}).catch((error) => {
					
					reject(error);
					
				});

		});

	}
	
	get secretKey() {
		
		return this.user.secret_key;
		
	}
	
	get logged() {
		
		return !!this.secretKey;
		
	}
	
}

const app = new App();

window.app = app;
