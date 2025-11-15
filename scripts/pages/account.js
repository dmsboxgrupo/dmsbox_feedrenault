class AccountPage extends Page {

	constructor() {
		
		super();
		
	}

	updateDisableAccount() {
		
		$('#confirmacao-deletar-sim').click(() => {
			
			ui.popupMensagem('<img class="loading top-10" src="images/loading-spinner.svg?v=2"/>', null);

			const request = Object.assign( { q:'disable_user' } );

			app.request(request, ( res ) => {
				
				if (!res.error) {
					
					ui.fechaPopupConfirmacao();
					
					ui.loadPage('page', 'login.html');
					
				} else {
					
					ui.popupMensagem( res.error );
					
				}
				
			} );
			
		} );
		
	}

	updatePassword() {
		
		/*
		
			q: update_user
			name: Bruno Berlim Teste2
			email: bberlim84@hotmail.com
			
			current_password: 123456
			new_password: 12345678
			
			secret_key: f2b924fe0d9a2a6273acb63ef46a89590911763a
			time: 1678153843518
		*/
		
		super.update();
		
		const form = $('#form-senha');
		/*
		form.find(`[name="senha-nova"]`).val( app.user.name );
		form.find(`[name="senha-confirmacao"]`).val( app.user.email );
		form.find(`[name="senha"]`).val( app.user.bir );*/
		
		$('#botao-input-configuracao-senha').click(() => {
			
			//alert('teste');
			
			if ( form.find('.com-erro').length === 0 ) {
				
				ui.popupMensagem('<img class="loading top-10" src="images/loading-spinner.svg?v=2"/>', null);


				const name = app.user.name;
				const email = app.user.email;
				const current_password = $("[name='senha-atual']").val();
				const new_password = $("[name='senha-nova']").val();
				
				const request = Object.assign( { q:'update_user', name, email, current_password, new_password } );

				app.request(request, ( res ) => {
					
					if (!res.error) {
						
						ui.fechaPopupConfirmacao();
						
						//ui.loadPage('page', 'home.html');
						ui.loadPage('page', 'logado.html');
						
					} else {
						
						ui.popupMensagem( res.error );
						
					}
					
				} );

			}
			
			
		});
		
		
	}

	update() {
		
		super.update();
		
		const form = $('#form-config');
		
		form.find(`[name="nome"]`).val( app.user.name );
		form.find(`[name="email"]`).val( app.user.email );
		form.find(`[name="bir"]`).val( app.user.bir );
		
		$('#botao-input-configuracao').click(() => {
			
			if ( form.find('.com-erro').length === 0 ) {
				
				ui.popupMensagem('<img class="loading top-10" src="images/loading-spinner.svg?v=2"/>', null);
				
				const name = $("[name='nome']").val();
				const email = $("[name='email']").val();
				const current_password = $("[name='senha']").val();
				
				const request = Object.assign( { q:'update_user', name, email, current_password } );

				app.request(request, ( res ) => {
					
					if (!res.error) {
						
						ui.fechaPopupConfirmacao();
						
						//ui.loadPage('page', 'home.html');
						ui.loadPage('page', 'logado.html');
						
					} else {
						
						ui.popupMensagem( res.error );
						
					}
					
				} );

			}
			
		});

	}

}

AccountPage.load = () => {
	
	if ( !app.pages.account ) {
		
		app.pages.account = new AccountPage();
		
	}
	
	app.pages.account.update();
	app.pages.account.updatePassword();
}
