class BackgroundPage extends Feed {

	constructor() {		
		super();
	}

	update() {

		var params = Object.assign({}, this.feed, {q:'get_background_images'});

		let domHtml = '';

		app.request(params, ( res ) => {
			const backs = res.background_images;
			const holder = $('.fundo-escolhas');

			domHtml = '';

			if ( backs.length > 0 )  {
				for(const back of backs) {
					domHtml += `<div class="fundo-item">`;
				    domHtml += `  <a href="#" id="${back.id}" class="bt-fundo" style="background-image: url(${back.background_image_url})" data-image="${back.background_image_url}" data-color="${back.text_color}"></a>`;
				    domHtml += `</div>`;
				}
			}

			holder.append( domHtml );
			
			if(app.user.background_image_id){
				$('#fundopopup .fundo-item #' + app.user.background_image_id).addClass('selected');
			}
		});

		$('body').on('click', '.bt-salvar-perfil-fundo', function(ev) {
		     ev.preventDefault();
		     ev.stopImmediatePropagation();
		     var itemSel = $('#fundopopup .bt-fundo.selected');

		     console.log(itemSel.parent().html())

		     console.log(itemSel.attr('data-image') +' | '+ itemSel.attr('data-color'));

		     $('#menu-main .menu-header').css({'background-image':'url('+ itemSel.attr('data-image') +')', 'color':itemSel.attr('data-color')});

		     var params = Object.assign({}, this.feed, {q:'update_user', 'background_image': itemSel.attr('id')});
		     app.request(params, ( res ) => {
		     	itemSel.parent().parent().parent().parent().stop().fadeOut();
		     });

		});

		super.update();
		this.updateHelpMessage('Background');
		
	}

}

BackgroundPage.load = () => {
	
	if ( !app.pages.Background ) {
		app.pages.Background = new BackgroundPage();
	}
	
	app.pages.Background.update();
	
}
