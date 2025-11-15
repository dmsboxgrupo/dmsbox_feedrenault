class Page {

	constructor() {

	}

	updateHelpMessage( name ) {
		
		const helpMessage = $('#help-mensagem');
		
		const property = 'readed_help_' + name;
		
		helpMessage.find('.botao').click(() => {
			
			app.setMeta( property, true );
			
			console.log( this );
			
		});
		
		if ( app.user.metadata[property] ) {
			
			helpMessage.hide();
			
		}
		
	}

	update() {
		

	}

}
