class CreateInfos extends Feed {

	constructor() {		
		super();
	}

	update() {

		var params = Object.assign({}, this.feed, {q:'get_digital_card'});

		app.request(params, ( res ) => {
			const cardData = res.digital_card;

			console.log(cardData);

			if ( cardData )  {
				if(cardData.whatsapp) {
					$('input[name="cardwhatsapp"]').val(cardData.whatsapp);
					$('#valWhatsapp').val(cardData.whatsapp);
				}
				if(cardData.phone) {
					$('input[name="cardtelefone"]').val(cardData.phone);
					$('#valTelefone').val(cardData.phone);
				}
				if(cardData.email) {
					$('input[name="cardemail"]').val(cardData.email);
					$('#valEmail').val(cardData.email);
				}
				if(cardData.location) {
					$('input[name="cardlocalizacao"]').val(cardData.location);
					$('#valLocalizacao').val(cardData.location);
				}
				if(cardData.concessionaire) {
					$('input[name="cardconcessionaria"]').val(cardData.concessionaire);
					$('#valConcessionaria').val(cardData.concessionaire);
		     		$('.concessionaria .txt').html(cardData.concessionaire);
				}
			} else {
				if($('#valWhatsapp').val() != ''){
					$('input[name="cardwhatsapp"]').val($('#valWhatsapp').val());
				}
				if($('#valTelefone').val() != ''){
					$('input[name="cardtelefone"]').val($('#valTelefone').val());
				}
				if($('#valEmail').val() != ''){
					$('input[name="cardemail"]').val($('#valEmail').val());
				}
				if($('#valLocalizacao').val() != ''){
					$('input[name="cardlocalizacao"]').val($('#valLocalizacao').val());
				}
				if($('#valConcessionaria').val() != ''){
					$('input[name="cardconcessionaria"]').val($('#valConcessionaria').val());
				}
			}

		});

		$('body').on('click', '.bt-cancelar-infos', function(ev) {
		    ev.preventDefault();
		    ev.stopImmediatePropagation();

		    $(this).parent().parent().parent().stop().fadeOut();
	  });


		$('body').on('click', '.bt-salvar-infos', function(ev) {
		     ev.preventDefault();
		     ev.stopImmediatePropagation();

		     $('#valWhatsapp').val($('*[name="cardwhatsapp"]').val());

		     $('#valTelefone').val($('*[name="cardtelefone"]').val());

		     $('#valEmail').val($('*[name="cardemail"]').val());

		     $('#valLocalizacao').val($('*[name="cardlocalizacao"]').val());

		     $('#valConcessionaria').val($('*[name="cardconcessionaria"]').val());
		     $('.concessionaria .txt').html($('*[name="cardconcessionaria"]').val());

		     var params = Object.assign({}, this.feed, {q:'set_digital_card', 'whatsapp': $('#valWhatsapp').val(), 'phone': $('#valTelefone').val(), 'email': $('#valEmail').val(), 'location': $('#valLocalizacao').val(), 'concessionaire': $('#valConcessionaria').val() });
		     app.request(params, ( res ) => {
		     	 $(this).parent().parent().parent().stop().fadeOut();
		     });

		});







		super.update();
		this.updateHelpMessage('CreateInfos');
		
	}

}

CreateInfos.load = () => {
	
	if ( !app.pages.CreateInfos ) {
		app.pages.CreateInfos = new CreateInfos();
	}
	
	app.pages.CreateInfos.update();
	
}
