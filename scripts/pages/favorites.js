class FavoritesPage extends Feed {

	constructor() {
		
		super();
		
		this.feedParams.favorites = true;
		
		signal.on('search_favoritos', ( data ) => {

			const search = data.value;

			$(".page-content").scrollTop(0);

			this.loadSubsession( {
				filter: $("#menu-subsecoes .active-nav").attr('data-filter'), 
				type: $("#menu-subsecoes .active-nav").attr('data-type')
			});
			
			if (!search) {
				
				$('.fullpage').show();
				
			}
			
			return false;
			
		});
		
		signal.on('share', ( data ) => {

			const contentDom = $(data.content);

			const id = contentDom.attr( 'content-id' );
			const message = contentDom.attr( 'content-message' ) ? decodeURI( contentDom.attr( 'content-message' ) ) + ' - ' : '';
			const url = decodeURI( this.toAbsoluteURL( contentDom.attr( 'content-url' ) ) );
			const download = contentDom.attr( 'content-download' );

			// Se houver arquivo para download, compartilha como imagem
			if (download) {
				ui.mostraCompartilhar(url, message, download);
			} else {
				ui.mostraCompartilhar(url, message);
			}

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

		super.update();

		this.updateTagsLinks();
		
		this.updateHelpMessage('favorites');
		
		this.updateSubsessions();

	}

}

FavoritesPage.load = () => {
	
	if ( !app.pages.favorites ) {
		
		app.pages.favorites = new FavoritesPage();
		
	}
	
	app.pages.favorites.update();
	
}
