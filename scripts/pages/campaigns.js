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

		});
	
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
