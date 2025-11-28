class GalleryPage extends Feed {

	constructor() {
		
		super();
		
		this.feed.filter = 'galeriawhats';

		this.galleryComplete = false;
		this.galleryPlayID = null;
		
		signal.on('page_load', ( e ) => {

			if ( e.page === 'galeriawhats.html' ) {
				
				this.galleryComplete = false;
				
			}
			
		});
		
		signal.on('params', ( e ) => {

			if ( e.p === 'galeriawhats' && e.id ) {
				
				if ( this.galleryComplete ) {
				
					this.playID( app.urlParams.id );
					
				} else {
					
					this.galleryPlayID = app.urlParams.id;
					
				}
				
			}
			
		});
		
		signal.on('search', ( data ) => {

			const search = data.value;

			this.updateSearchGallery( search );
			
		});

	}
	
	updateSearchGallery( search ) {
		
		const searchDom = $('#gallery-search');
		
		if (searchDom.length) {
			
			searchDom.empty();
			
			if (search) {
				
				const gallery = $(`<div class="galeria">`);
				const destaqueDom = $('#destaque');
				
				searchDom.append( gallery );
				
				destaqueDom.empty();
				
				const params = Object.assign({}, {q:'feed', filter:'galeria'}, this.feed, this.filterGallery);
				
				params.search = search;
				
				app.request(params, ( res ) => {
					
					const feed = res.feed;
					
					for(const post of feed) {

						this.parsePostData( post );

						gallery.append( this.createGallery( post ) );
						destaqueDom.append( this.createHighlight( post ) );
					
					}

				} );
				
			}
			
		}
		
	}

	playID( id = this.galleryPlayID ) {
		
		$(`[data-play="${id}"]`).click();
		
		location.hash = '#p=galeriawhats';
		
	}

	updateFilter() {
		
		this.galleryComplete = false;
		
		this.updateGallery(() => {
			
			this.galleryComplete = true;
			this.galleryPlayID = app.urlParams.id;
			
			if ( this.galleryPlayID ) {

				this.playID();

			} else if ( app.urlParams.search ) {
				
				ui.buscar( app.urlParams.search, false );
				
			}
			
		});
		
	} 

	update() {

		super.update();
		
		this.setTags( [
			'cards',
			'folhetos',
			'videos'
		] );

		this.updateHelpMessage('gallery');
		
		this.updateBanners();
		this.updateVehicles();
		this.updateTags();
		
		this.updateFilter();

	}

}

GalleryPage.load = () => {
	
	if ( !app.pages.gallery ) {
		
		app.pages.gallery = new GalleryPage();
		
	}
	
	app.pages.gallery.update();
	
}
