class Pesquisa extends Feed {

	constructor() {
		
		super();
		
		$( ".page-content" ).scroll(() => {

			this.updateAutoPlay();

		});
		
		signal.on('page_complete', ( data ) => {
		
			if ( data.page === 'pesquisa.html' ) {
				
				var lastSearch = '';
				
				const updateParams = ( data ) => {

					//if ( app.urlParams.p !== 'pesquisa' ) return;
					
					if (data && data.p == 'pesquisa' && data.q !== lastSearch && this.loaded) {

						lastSearch = data.q;

						this.param = data.q;
						this.updateSearch();

					}
					
				}
				
				if ( app.urlParams.q ) {
					
					$('input[name=q]').val( app.urlParams.q );

					
					updateParams( app.urlParams );
					
				}
				
				signal.on('params', updateParams );
				
			}
			
		} );
	}
	
	updateSearch() {
		
		this.loaded = true;

		const search = this.param || this.searchValue;

		$(".pesquisa-termo").text(search);
		
		if(search == '') {
			//$("#page").addClass("trending");
			$('#pesquisa-label').hide();
		} else {
			$('#pesquisa-label').show();
			//$("#page").removeClass("trending");
		}
		
		//$('#subsecao').empty();
		
		$('.page-content').scrollTop( 0 );
		
		this.loadSubsession( {
			filter: "todos",
			type: "todos",
			trending: search ? '' : 'true',
			search: search
		} );

	}

	update() {
		
		super.update();

		this.updateSearch();

		$(".page-content").scroll(() => {
			
			//- 10 = desired pixel distance from the bottom of the page while scrolling)
			if ($("#subsecao").attr("data-loaded") == "true" &&
				$("#subsecao").attr("data-reach-end") == "false" &&
				$(".page-content").scrollTop() > $("#subsecao .conteudo").height() - window.innerHeight) {
				
				// carrega quando faz o scroll
				
				if ( !this.param || app.currentPage !== 'pesquisa' ) return;
				
				$("#subsecao").attr("data-loaded", "false");
				
				this.loadSubsession( {
					filter: "todos",
					type: "todos",
					search: this.param,
					nextPage: true,
					clean: false
				});
				
			}
				
		});
	}

}

Pesquisa.load = () => {
	
	if ( !app.pages.pesquisa ) {
		
		app.pages.pesquisa = new Pesquisa();
		
	}
	
	app.pages.pesquisa.update();
	
}
