class LoginPage extends Page {

	constructor() {
		
		super();
		
		signal.on('page_complete', ( e ) => {

			if ( e.page == 'login-entrar.html' || e.page == 'termos.html' ) {
				$('#content').addClass('login-video');
			}else {
				$('#content').removeClass('login-video');
			}
			if ( e.page == 'login-entrar.html' ) {
			
				if (String(app.urlParams.p).indexOf( 'login-confirmacao' ) === 0 || 
					String(app.urlParams.p).indexOf( 'login-cadastro' ) === 0) {
			
					ui.loadPage('content', `${app.urlParams.p}.html`);	
				} 

			
			} else if ( e.page === 'login-carregando.html' ) {
				
				const loginReq = this.loginRequest;
				
				app.login( loginReq.email, loginReq.password, loginReq.remember );
				
			}

			if($(window).width() < 993) {
				$('.info-outside').removeClass('on');
			}
			
		});
		
		signal.on('login_response', ( e ) => {

			if ( app.logged ) {

				ui.loadPage('page', 'logado.html');

			} else {
				
				$('.login-msg-hide').removeClass('login-msg-hide');
			
				$('#login-loading').hide();
				
				$('#login-title').text( 'Falha ao fazer login' );
				$('#login-message').text( e.error );
				
			}
			
		} );
		
		signal.on('params', ( data ) => {

			if ( data.s == 'login' ) {
				$('#page').addClass('openForm');
				$('.info-outside').addClass('on');
			}
			
		} );
		
	}
	
	updateCreateUser() {
		
		$('#botao-input-cadastro').click( (e) => {
			
			const name = $("[name='nome']").val();
			const bir = $("[name='bir']").val();
			const email = $("[name='email']").val();
			const password = $("[name='senha-nova']").val();
			
			this.createUserRequest = {
				name,
				bir,
				email,
				password
			};

		});
		
	}
	
	updateCreateUserConfirm() {
		
		if (app.urlParams.p == 'login-cadastro-ok') {
			
			$(`[data-user="name"]`).text( this.createUserRequest.name );
			
			const request = Object.assign( { q:'create_user' }, this.createUserRequest );
			
			app.request(request, ( res ) => {
				
				$("#loading-panel").hide();			
				$("#loaded-panel").show();
					
				if (res.error) {
				
					ui.popupMensagem( res.error );
					
					location.hash = '#p=login-cadastro';
					
				}
				
				console.log( "CREATE USER ->", res );
				
			} );
				
		} else {
			
			location.hash = '#p=login-cadastro-ok';
			
		}
		
	}
	
	updatePasswordRequest() {
		
		$('#botao-input-esqueceu').click( (e) => {
			
			const email = $("[name='email']").val();
			
			this.passwordRequest = {
				email
			};
			
		});
		
	}
	
	updatePasswordRequestConfirm() {
		
		const email = this.passwordRequest.email;
		
		$(`[data-user="email"]`).text( email );
		
		app.request({ q:'password_request', email }, ( res ) => {
			
			const passproc = $('#passrecory-loading');
			
			console.log( "PASSWORD REQUEST ->", res );
			
			if ( !res.error ) {
				
				passproc.hide();
				$('#passrecory-complete').show();
				
			} else {
				
				passproc.find('.login-msg-hide').show();
				passproc.find('#login-loading').hide();
				
				passproc.find('#login-message').text( res.error );
				
			}
			
		} );
		
	}
	
	updateNewPassword() {
		$('#botao-input-cadastro').addClass('on');
		$('#botao-input-cadastro').click( (e) => {
			
			const password = $("[name='senha-nova']").val();
			
			this.confirmNewPasswordRequest = {
				password
			};

		});
		
	}
	
	updateNewPasswordOk() {

		const token = app.urlParams.token;
		
		const request = Object.assign( { q:'password_confirm', token }, this.confirmNewPasswordRequest );
		
		app.request(request, ( res ) => {
			
			$("#loading-panel").hide();			
			$("#loaded-panel").show();
				
			if (res.error) {
			
				ui.popupMensagem( res.error );
				
				location.href = '?refresh';
				
			}
			
		} );
	}
	
	updateConfirmUser() {
		
		const token = app.urlParams.token;

		$("#form-cadastro").hide();

		app.request({ q:'user_data', token }, ( res ) => {
				
			$("#form-cadastro").show();
				
			if (!res.error) {
			
				$("[name='nome']").val( res.name );
				$("[name='bir']").val( res.bir );
				$("[name='email']").val( res.email );
				
			} else {
				
				ui.popupMensagem( res.error );
				
				location.href = '?refresh';
				
			}
			
		} );
		
		$('#botao-input-cadastro').click( (e) => {
			
			const name = $("[name='nome']").val();
			const email = $("[name='email']").val();
			const password = $("[name='senha-nova']").val();
			
			this.confirmUserRequest = {
				name,
				email,
				password
			};

		});
		
	}
	
	updateConfirmEmail() {
		
		const token = app.urlParams.token;
		
		const request = Object.assign( { q:'confirm_user', token } );
		
		app.request(request, ( res ) => {
			
			$("#loading-panel").hide();			
			$("#loaded-panel").show();
				
			if (res.error) {
			
				ui.popupMensagem( res.error );
				
				location.href = '?refresh';
				
			}
			
		} );
		
	}
	
	updateFinishConfirmUser() {
		
		const token = app.urlParams.token;
		
		const request = Object.assign( { q:'confirm_user', token }, this.confirmUserRequest );
		
		app.request(request, ( res ) => {
			
			$("#loading-panel").hide();			
			$("#loaded-panel").show();
				
			if (res.error) {
			
				ui.popupMensagem( res.error );
				
				location.href = '?refresh';
				
			}
			
		} );
		
	}
	
	updateLogin() {
		
		app.logout();
		
		$('#botao-input-entrar').click( (e) => {
			
			const email = $("[name='email']").val();
			const password = $("[name='password']").val();
			const remember = $("[name='lembrar']").val() == 'on';
			
			this.loginRequest = {
				email,
				password,
				remember
			};

		});


		$('.bt-login a').on('click', function(event){
			window.location.href = "#s=login";
			$('#page').addClass('openForm');
		})

		var banners = Object.assign({}, this.feed, this.feedVehicles, {q:'global'});
		app.request(banners, ( res ) => {
			$('.login-fundo').css({'background-image': 'url('+res.login_screen+')'})

			$('.video-entrar').html(
				'<div class="hld">'+
				'     <video loop autoplay muted>'+
				'       <source src="'+res.login_screen_video+'" type="video/mp4">'+
				'     </video> '+
				'     <div class="bt-login">'+
				'       <a href="#s=login">'+
				'         Fazer login'+
				'       </a>'+
				'     </div>'+
				'  </div>'
				);
	
		})
	}

	update() {
		if (app.urlParams.s == 'login') {
			$('#page').addClass('openForm');
		} 
	}

}

LoginPage.load = () => {
	
	if ( !app.pages.login ) {
		
		app.pages.login = new LoginPage();
		
	}
	
	app.pages.login.update();
	
}
