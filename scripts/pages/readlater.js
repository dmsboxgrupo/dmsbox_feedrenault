class ReadLaterPage extends Feed {

	constructor() {
		
		super();
		
		this.feedParams.read_later = true;
		
		signal.on('search_leituras', ( data ) => {

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
	
	get searchValue() {
		
		return '';
		
	}
	

	update() {
		
		$('.header-interna').find('.header-title').html( 'meus itens salvos' );
		
		this.feedParams.read_later = true;
		
		super.update();
		
		this.updateTagsLinks();
		
		this.updateHelpMessage('read_later');
		
		this.updateSubsessions();

	}

}

ReadLaterPage.load = () => {
	
	if ( !app.pages.read_later ) {
		
		app.pages.read_later = new ReadLaterPage();
		
	}
	
	app.pages.read_later.update();
	
}
