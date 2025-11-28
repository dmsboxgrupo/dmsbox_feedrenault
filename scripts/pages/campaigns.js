class CampaignsPage extends Feed {

	constructor() {
		
		super();
		
		this.feed.filter = 'campanha';
		
		this.campaignsComplete = false;
		
		signal.on('page_load', ( e ) => {

			if ( e.page === 'campanhas.html' ) {
				
				this.campaignsComplete = false;
				
			}
			
		});
		
		signal.on('params', ( params ) => {
			
			if (params.p == "campanhas") {
				
				if (this.campaignsComplete) {
					
					this.updateParams();
					
				}
				
			}
			
		});
		
		signal.on('search_campanhas', ( data ) => {

			const search = data.value;

			this.updateCampaigns();
			
			return false;
			
		});
		
	}
	
	updateParams() {
		
		if ( app.urlParams.id ) {
				
			$(`[data-postid="${app.urlParams.id}"]`).click();
			
			//location.hash = '#p=campanhas';
			
		} else if ( app.urlParams.search ) {
			
			ui.buscar( app.urlParams.search, false );
			
		} else {
			
			if ( app.lastParams.p == 'campanhas' && app.lastParams.id !== undefined ) {
				
				delete app.lastParams.id;
				
				ui.loadPage('content', 'campanhas.html' );
				
			}
			
			//$(`.comunicado-fechar`).click();
			
			//ui.loadPage('content', 'campanhas.html' );
			
		}
		
	}

	updateFilter() {
		
		this.updateCampaigns(() => {
			
			this.campaignsComplete = true;
			
			this.updateParams();
			
		});
		
	}

	update() {
		
		$('.header-interna').find('.header-title').html( 'campanhas' );
		
		super.update();
		
		this.setTags();
		
		this.updateHelpMessage('campaigns');
		
		this.updateBanners();
		this.updateVehicles();
		this.updateTags();
		
		this.updateFilter();

	}

}

CampaignsPage.load = () => {
	
	if ( !app.pages.campaigns ) {
		
		app.pages.campaigns = new CampaignsPage();
		
	}
	
	app.pages.campaigns.update();
	
}
