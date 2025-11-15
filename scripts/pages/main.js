class MainPage {

	constructor() {
		
		ui.options.isApp = true;
		ui.options.embedBrowser = false;
		ui.options.share = false;		

		signal.on('highlight_link', ( e ) => {
			
			const url = $(e.content).attr( 'content-url' );
			
			if (url) {
				
				if (url.indexOf('http') === 0) {
					
					window.open(url, '_blank');
					
				} else {
					
					location.hash = url;
					
				}
				
			}

		});

		signal.on('search', ( e ) => {
			
			const search = e.value;
			const searchTags = search.split(' ').filter(v => v.startsWith('#'));
			const searchText = search.replace( /#([^\s]*)/g, '' ).trim().toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, "");
			
			$(`[content-tags]`).each(function() {
				
				const post = $(this);
				
				if (search) {
					
					const postTags = post.attr('content-tags').split(',');
					
					let isVisible = searchTags.length == 0;
					
					for(const tag of searchTags) {
						
						const tagStr = tag.substr(1);
						
						if ( postTags.indexOf( tagStr ) === 0 ) {
							
							isVisible = true;
							break;
							
						}
						
					}
					
					if ( isVisible && searchText ) {
						
						const postTextDiv = post.find(`[content-text]`);
						const postText = postTextDiv.text().trim().toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, "");
						
						isVisible = postText.indexOf(searchText) !== -1;
						
					}
					
					if (isVisible) {
						
						post.show();
						
					} else {
						
						post.hide();
						
					}

					
				} else {
					
					post.show();
					
				}

			});

		});
		
		signal.on('viewed', ( data ) => {
			
			const params = { q:'view', category:data.category };
			
			if (data.ids) {
				
				params.ids = data.ids.join(',');
				
			} else {
				
				params.id = data.id;
				
			}
			
			app.request( params, ( res ) => {

				console.log( "VIEWED:", res );

			});

		});
		
		signal.on('highlight', ( data ) => {
			
			const contentDom = $(data.content);
			
			const id = contentDom.attr( 'content-id' );
			
			$(`[content-id='${id}']`).removeClass('novo');

			signal.emit('viewed', { id });

		});
		
		signal.on('share', ( data ) => {
			
			const contentDom = $(data.content);
			
			const id = contentDom.attr( 'content-id' );
			const message = contentDom.attr( 'content-message' ) ? decodeURI( contentDom.attr( 'content-message' ) ) + ' - ' : '';
			const url = decodeURI( this.toAbsoluteURL( contentDom.attr( 'content-url' ) ) );
			
			ui.mostraCompartilhar(url, message);
			
		});
		
		signal.on('rate', ( data ) => {
			
			const contentDom = $(data.content);
			const scoreCountDom = contentDom.find(`[data-custom='score-count']`);
			
			const id = contentDom.attr( 'content-id' );
			var content_tags = contentDom.attr( 'content-tags' );
			
			const userScore = parseInt( contentDom.find('.rateit').attr( 'data-rateit-user-value' ) );
			
			//const params = { q:'rank', id, score: data.value };
			
			var category = 0;
			if ( content_tags == 'enquete' ) category=7;
			
			const params = { q:'rank', id, score: data.value, category: category };
			
			if (userScore == 0) {
				
				scoreCountDom.text( parseInt( scoreCountDom.text() ) + 1 );
				
				contentDom.find('.rateit').attr( 'data-rateit-user-value', params.score );
				
			}
			
			app.request( params, ( res ) => {

				console.log( "RATE:", res );

			});
			
		});
		
		signal.on('download', ( data ) => {
			
			const contentDom = $(data.content);
			
			const downloadURL = contentDom.attr( 'content-download' );
			
			if (native.download) {
				
				native.download( downloadURL );
				
			} else {
				
				app.download( downloadURL );
				
			}
			
		});
		
		signal.on('notification', ( data ) => {
			
			const contentDom = $(data.content);
			
			const id = contentDom.attr( 'content-id' );

			const params = { q:'notification', id, value: data.value ? 1 : 0 };
			
			app.request( params, ( res ) => {

				console.log( "NOTIFICATION:", res );

			});
			
		});
		
		signal.on('favorite', ( data ) => {
			
			const contentDom = $(data.content);
			
			const id = contentDom.attr( 'content-id' );

			const params = { q:'favorite', id, value: data.value ? 1 : 0 };
			
			app.request( params, ( res ) => {

				console.log( "NOTIFICATION:", res );

			});
			
		});
		
		signal.on('read_later', ( data ) => {
			
			const contentDom = $(data.content);
			
			const id = contentDom.attr( 'content-id' );

			if(data.value == true){
				$('*[content-id="'+ id +'"]' ).find('.proxleitura').addClass('ativo');
			} else {
				$('*[content-id="'+ id +'"]' ).find('.proxleitura').removeClass('ativo');
			}

			//const params = { q:'read_later', id, value: data.value ? 1 : 0 };
			var content_tags = contentDom.attr( 'content-tags' );	
			
			var category = 0;
			if ( content_tags == 'enquete' ) category=7;

			const params = { q:'read_later', id, value: data.value ? 1 : 0, category: category  };
			
			app.request( params, ( res ) => {

				console.log( "READ LATER:", res );

			});
			
		});

		signal.on('open_url', ( e ) => {

			if (native.open) {
				
				native.open( e.href );
				
			} else {
				
				window.open( e.href, "_blank" );
				
			}

		});

		signal.on('page_complete', ( e ) => {
			
			$("[data-string]").each(function() {
				
				var text = '';
				
				switch( this.dataset.string ) {
					
					case 'name': 
					
						text = app.user.short_name || app.user.name;

						break;
					
					case 'email': 
					
						text = app.user.email;
						
						break;
					
				}
				
				$(this).text( text );

				if(app.user.background_image_url) {
					$('#menu-main .menu-header').css({'background-image':'url(' + app.user.background_image_url + ')'});
				}
				
			});

		});
		
	}
	
	toAbsoluteURL( url ) {
		
		if (!url) return '';
		
		if ( url.indexOf('http') !== 0 ) {
			
			return `https://feedrenault.com.br/${url}`;
			
		}
		
		return url;
		
	}

	update() {
		
		const page = $('#page');

		if ( String(app.urlParams.p).indexOf( 'login-confirmacao' ) === 0 ) {
			
			ui.loadPage('page', 'login.html');
			
		} else {
			
			app.restoreLogin( () => {

				if ( app.logged ) {
					
					ui.loadPage('page', 'logado.html');
					
				} else {
					
					ui.loadPage('page', 'login.html');
					
				}
				
			} );
				
		}

	}

}

MainPage.load = () => {
	
	if ( !app.pages.main ) {
		
		app.pages.main = new MainPage();
		
	}
	
	app.pages.main.update();

}
