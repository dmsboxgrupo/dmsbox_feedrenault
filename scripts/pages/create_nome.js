class CreateNome extends Feed {

	constructor() {		
		super();
	}

	update() {

		var params = Object.assign({}, this.feed, {q:'get_digital_card'});

		app.request(params, ( res ) => {
			const cardData = res.digital_card;

			if ( cardData )  {
				if(cardData.name) {
					$('input[name="cardnome"]').val(cardData.name);
					$('#valNome').val(cardData.name);
				}
				if(cardData.job) {
					$('input[name="cardcargo"]').val(cardData.job);
					$('#valCargo').val(cardData.job);
				}
			} else {
				if($('#valNome').val() != ''){
					$('input[name="cardnome"]').val($('#valNome').val());
				}
				if($('#valCargo').val() != ''){
					$('input[name="cardcargo"]').val($('#valCargo').val());
				}
			}

		});

		$('body').on('click', '.bt-cancelar-nome', function(ev) {
		    ev.preventDefault();
		    ev.stopImmediatePropagation();
		    $(this).parent().parent().parent().stop().fadeOut();
	  });


		$('body').on('click', '.bt-salvar-nome', function(ev) {
		     ev.preventDefault();
		     ev.stopImmediatePropagation();

		     $('.card-content .nome p').html($('*[name="cardcargo"]').val());
		     $('.card-content .nome h3 span').html($('*[name="cardnome"]').val());
		     
		     $('#valNome').val($('*[name="cardnome"]').val());
		     $('#valCargo').val($('*[name="cardcargo"]').val());

		     var params = Object.assign({}, this.feed, {q:'set_digital_card', 'name': $('#valNome').val(), 'job': $('#valCargo').val()});
		     app.request(params, ( res ) => {
		     	 $(this).parent().parent().parent().stop().fadeOut();
		     });

		});


		super.update();
		this.updateHelpMessage('CreateNome');
		
	}

}

CreateNome.load = () => {
	
	if ( !app.pages.CreateNome ) {
		app.pages.CreateNome = new CreateNome();
	}
	
	app.pages.CreateNome.update();
	
}
