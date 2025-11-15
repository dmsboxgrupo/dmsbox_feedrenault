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
