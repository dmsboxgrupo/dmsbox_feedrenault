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
