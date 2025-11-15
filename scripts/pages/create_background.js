class CreateBackground extends Feed {

	constructor() {		
		super();
	}

	update() {

		var params = Object.assign({}, this.feed, {q:'get_background_images'});

		let domHtml = '';
		let defaulImageID = '';
		let defaulImageURL = '';

		console.log('Chamou BACKGROUD');


		app.request(params, ( res ) => {
			const backs = res.background_images;
			const holder = $('.fundo-escolhas');

			domHtml = '';

			if ( backs.length > 0 )  {
				var countBack = 0;

				for(const back of backs) {
					domHtml += `<div class="fundo-item">`;
				    domHtml += `  <a href="#" id="${back.id}" class="bt-fundo" style="background-image: url(${back.background_image_url})" data-image="${back.background_image_url}"></a>`;
				    domHtml += `</div>`;

				    if(countBack == 0){
				    	defaulImageID = back.id;
						defaulImageURL = back.background_image_url;
				    }
				    countBack++;
				}
			}

			var paramSave = Object.assign({}, this.feed, {q:'get_digital_card'});


			app.request(paramSave, ( res ) => {
				const cardData = res.digital_card;

				if ( cardData )  {
					if(cardData.background_image) {
						$('#fundopopup .fundo-item #' + cardData.background_image).addClass('selected');
						$('.create-header.card').css({'background-image':'url('+ cardData.background_image_url +')'});
					} else {
						$('#fundopopup .fundo-item #' + defaulImageID).addClass('selected');
						$('.create-header.card').css({'background-image':'url('+ defaulImageURL +')'});
					}
				} else {
					if($('#valBgId').val() != ''){
						$('#fundopopup .fundo-item #' + $('#valBgId').val()).addClass('selected');
						$('.create-header.card').css({'background-image':'url('+ $('#valBg').val() +')'});
					} else {
						$('#fundopopup .fundo-item #' + defaulImageID).addClass('selected');
						$('.create-header.card').css({'background-image':'url('+ defaulImageURL +')'});
					}
				}
			});

			holder.append( domHtml );
		});


		$('body').on('click', '.bt-salvar-cartao-fundo', function(ev) {
		     ev.preventDefault();
		     ev.stopImmediatePropagation();

		     var itemSel = $('.bt-fundo.selected');
		     
		     $('.create-header.card').css({'background-image':'url('+ itemSel.attr('data-image') +')'});
		     $('#valBg').val(itemSel.attr('data-image'));
		     $('#valBgId').val(itemSel.attr('id'));

		     var params = Object.assign({}, this.feed, {q:'set_digital_card', 'background_image': itemSel.attr('id')});
		     app.request(params, ( res ) => {
		     	 $(this).parent().parent().parent().stop().fadeOut();
		     });

		});


		super.update();
		this.updateHelpMessage('CreateBackground');
		
	}

}

CreateBackground.load = () => {
	
	if ( !app.pages.CreateBackground ) {
		app.pages.CreateBackground = new CreateBackground();
	}
	
	app.pages.CreateBackground.update();
	
}
