class HomePage extends Feed {

	constructor() {
		
		super();
		
		$( ".page-content" ).scroll(() => {

			this.updateAutoPlay();

		});
		
		signal.on('params', ( data ) => {
			
			if ( data.subsection ) { 
				
				$(`a[data-filter=${data.subsection}]`).click();
				
				if (data.search) {
					
					ui.buscar( data.search, false );
					
				}
				
			}
			
		} );
		
		signal.on('search_home', ( data ) => {
			
			const search = data.value;

			$(".page-content").scrollTop(0);

			this.loadSubsession( {
				filter: $("#menu-subsecoes .active-nav").attr('data-filter'), 
				type: $("#menu-subsecoes .active-nav").attr('data-type')
			});
			
			if (search) {
				
				$('.comunicado-fechar').click();
			
			} else {
				
				$('.fullpage').show();
				
			}
			
			return false;
			
		});
	
	}

	update() {
		
		$('.header-interna').find('.header-title').html('');
        $('.header-interna').removeClass('on');
		$('.page-content').scrollTop( 0 );
		
		super.update();
		
		this.updateTagsLinks();
		
		/*
		this.setTags( [
			'facebook',
			'instagram',
			'youtube',
			'twitter',
			'campanhas',
			'noticias',
			'comunicados'
		] );
		*/
		
		this.updateHelpMessage('home');
		
		if (app.urlParams.q) {

			ui.buscar(app.urlParams.q, false);
			
		}
		
		if (app.urlParams.clean) {
			
			$(".page-content").scrollTop(0);
			$('.fullpage').hide();
			
		}
		
		/*var par_filter="";

		if( app.urlParams.backtopage = "notificacoes" ){
			
			//var par_filter = ( app.urlParams.filter == "enquete" )? app.urlParams.filter  : null;
			//var par_filter = app.urlParams.filter;
			var par_filter = ( app.urlParams.filter == "enquete" )? app.urlParams.filter  : "todos";
			
		}*/
		
		
		
		this.updateStories();
		this.updateSubsessions();

	}

}

HomePage.load = () => {
	
	if ( !app.pages.home ) {
		
		app.pages.home = new HomePage();
		
	}
	
	app.pages.home.update();
	
}
