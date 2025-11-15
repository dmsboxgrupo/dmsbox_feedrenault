class SharePage extends Feed {

	constructor() {
		super();

		this.feed.filter = 'galeriawhats';

		this.galleryComplete = false;
		this.galleryPlayID = null;
		
		signal.on('page_load', ( e ) => {

			if ( e.page === 'compartilhar.html' ) {
				
				this.galleryComplete = false;
				
			}
			
		});
		
		signal.on('params', ( e ) => {

			if ( e.p === 'compartilhar' && e.id ) {
				
				if ( this.galleryComplete ) {
				
					this.playID( app.urlParams.id );
					
				} else {
					
					this.galleryPlayID = app.urlParams.id;
					
				}
				
			}
			
			if ( e.p === 'compartilhar-imagens-carro' ) {
				this.update();
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
		
		location.hash = '#p=compartilhar';
		
	}

	updateGallery( callback = CALLBACK, tipo, veiculo ) {
		
		const destaque = $('#destaque');
		const galleryDom = $('#gallery');
		const galleryDestaqueDom = $('#gallery-destaque');
		const loading = $('.loading-gallery');
		const noPost = $('#no-post');
		
		galleryDom.empty();
		
		loading.show();

		/*
		FILTROS DE PÁGINAS
		var params = Object.assign({}, this.feed, this.filterGallery, {q:'feed', filter:'galeria', tags: veiculo});

		TAGS:
		catálogos compactos: 12
		cards atributos: 11,20,25,26,27,28,29,32,57,66,68
		comparativos concorrencia: 10
		comparativos versões: 19
		on demanda: 63
		*/
		
		if(tipo == 'imagens') {
			if(veiculo != 'undefined') {
				var params = Object.assign({}, this.feed, this.filterGallery, {q:'feed', filter:'galeria', group:'images', content_type: 'image', vehicles: veiculo});
			} else {
				var params = Object.assign({}, this.feed, this.filterGallery, {q:'feed', filter:'galeria', group:'images', content_type: 'image'});
			}
		} else if(tipo == 'catalogos') {
			var params = Object.assign({}, this.feed, this.filterGallery, {q:'feed', filter:'galeria', content_type: 'image', tags:'12'});
		} else if(tipo == 'imagens-interna') {
			var params = Object.assign({}, this.feed, this.filterGallery, {q:'feed', filter:'galeria', content_type: 'image', group:'images', tags: veiculo});
		} else if(tipo == 'atributos') {
			var params = Object.assign({}, this.feed, this.filterGallery, {q:'feed', filter:'galeria', content_type: 'image', tags:'11,20,25,26,27,28,29,32,57,66,68'});
		}  else if(tipo == 'atributos-todos') {
			if(veiculo != ''){
				var params = Object.assign({}, this.feed, this.filterGallery, {q:'feed', filter:'galeria', content_type: 'image', tags: veiculo});
			} else {
				var params = Object.assign({}, this.feed, this.filterGallery, {q:'feed', filter:'galeria', content_type: 'image', tags:'11,20,25,26,27,28,29,32,57,66,68'});
			}
		} else if(tipo == 'concorrencia') {
			var params = Object.assign({}, this.feed, this.filterGallery, {q:'feed', filter:'galeria', content_type: 'image', tags:'10'});
		} else if(tipo == 'versao') {
			var params = Object.assign({}, this.feed, this.filterGallery, {q:'feed', filter:'galeria', content_type: 'image', tags:'19'});
		} else if(tipo == 'ondemand') {
			var params = Object.assign({}, this.feed, this.filterGallery, {q:'feed', filter:'galeria', content_type: 'image', tags:'63'});
		} else if(tipo == 'ondemand-todos') {
			var params = Object.assign({}, this.feed, this.filterGallery, {q:'feed', filter:'galeria', content_type: 'image', tags:'63'});
		} else if(tipo == 'imagens-carro') {
			var params = Object.assign({}, this.feed, this.filterGallery, {q:'feed', filter:'galeria', content_type: 'image', group:'images', vehicles: veiculo});

			var paramsVeiculo = Object.assign({}, this.feed, this.feedVehicles, {q:'vehicles'});
			var arrVeiculos = [];
			var indexVeiculo;
			var countVeiculo = 0;

			app.request(paramsVeiculo, ( res ) => {
				for(const vehicle of res.vehicles) {
					arrVeiculos.push(vehicle);
					if(vehicle.id == veiculo){
						indexVeiculo = countVeiculo;
						$('#car-name').html('Templates ' + vehicle.name);
						$('#car-image').html('<img class="veiculo-item" src="'+vehicle.content+'">');
					}
					countVeiculo++;
				}

				var prev;
				var next;

				if(arrVeiculos[indexVeiculo - 1]){
					prev = arrVeiculos[indexVeiculo - 1];
				} else {
					var ant = arrVeiculos.length - 1;
					prev = arrVeiculos[ant];
				}

				if(arrVeiculos[indexVeiculo + 1]){
					next = arrVeiculos[indexVeiculo + 1];
				} else {
					next = arrVeiculos[0];
				}
				
				$('#car-prev').attr('href', '#p=compartilhar-imagens-carro&v=' + prev.id);
				$('#car-next').attr('href', '#p=compartilhar-imagens-carro&v=' + next.id);

			} );

		} else {
			var params = Object.assign({}, this.feed, this.filterGallery, {q:'feed', filter:'galeria', content_type: 'image'});
		}

		
		let domHtml = '';
		
		const hightlights = [];

		
		app.request(params, ( res ) => {
			
			loading.hide();
		
			const feed = res.feed;
			const tags = res.tags;

			var count = 0;
			
			if ( feed.length > 0 )  {
				
				for(const tag of tags) {
					
					const postsId = tag.posts;

					if(tipo == 'imagens') {

						domHtml += `<div class="secao" style="margin-bottom: 30px;">`						
						domHtml += `<h4>${tag.name}</h4>`;
					    domHtml += `<div class="bt-top"> <a href="#p=compartilhar-imagens-interna&v=${tag.id}">ver todas</a></div>`;
						domHtml += `<div class="scroll-horizontal">`;

						for(const postId of postsId) {

						domHtml += `<div class="imagem">`;
						
							
							const post = this.parsePostData( feed[postId] );
							var img = '';
							if ( post.content_type === "image" ) {
							
								img = post.content;

							} else {

								img =  post.thumbnail;
								
							}
						    domHtml += `<div class="hld-bg img-p" style="background-image: url('${img}');">`;
						    domHtml += `<div class="nome"><span>${post.title}</span></div>`;
						    domHtml += `<img data-postid="${postId}" data-play="${post.id}" ${post.header_div} ${post.tags_div} class="galeria-card" `;
							domHtml += `data-destaque="${hightlights.length}" `;
							hightlights.push( this.createHighlight( post ) );
							domHtml += `src="${img}" />`;
						    domHtml += `</div>`;
									
							
						domHtml += `</div>`;
						}
						
						domHtml += `</div>`;
						domHtml += `<div class="margin-spacer"></div>`;
						domHtml +=  `</div>`;

					} else if(tipo == 'imagens-interna'){

						for(const postId of postsId) {
							const post = this.parsePostData( feed[postId] );
							var img = '';
							if ( post.content_type === "image" ) {
								img = post.content;
							} else {
								img =  post.thumbnail;
							}

							if(count == 0) {
								domHtml += `<div class="imagem destaque" style="background-image: url('${img}');">`;
							    domHtml += `	<div class="titulo"><span>${post.title}</span></div>`;
							    domHtml += `    <div class="bts">`;
							    domHtml += `      <a content-action="proxleitura"><img src="images/ico_salvar_imagem.svg"></a>`;
							    domHtml += `      <a download="feedrenault_${post.id}" content-id="${post.id}" content-message="" content-url="${img}" content-download="${post.download}" class="download"><img src="images/ico_download_imagem.svg"></a>`;
							    domHtml += `    </div>`;
							    domHtml += `</div>`;
							} else {
							    domHtml += `<div class="imagem" style="background-image: url('${img}');">`;
							    domHtml += `    <div class="bts">`;
							    domHtml += `      <a content-action="proxleitura"><img src="images/ico_salvar_imagem.svg"></a>`;
							    domHtml += `      <a download="feedrenault_${post.id}" content-id="${post.id}" content-message="" content-url="${img}" content-download="${post.download}" class="download"><img src="images/ico_download_imagem.svg"></a>`;
							    domHtml += `    </div>`;
							    domHtml += `</div>`;
							}
							count++;

						}

					} else if(tipo == 'imagens-carro'){
						if(postsId.length > 1) {
							$('#count-templates').html(postsId.length + ' templates <br> encontrados');
						} else {
							$('#count-templates').html(postsId.length + ' template <br> encontrado');
						}

						for(const postId of postsId) {
							
							const post = this.parsePostData( feed[postId] );
							domHtml += `<div class="template">`
							domHtml += `<img data-postid="${postId}" data-play="${post.id}" ${post.header_div} ${post.tags_div} class="galeria-card" `;
							domHtml += `data-destaque="${hightlights.length}" `;

							hightlights.push( this.createHighlight( post ) );
								
							if ( post.content_type === "image" ) {
							
								domHtml += `src="${post.content}" />`;

							} else {

								domHtml += `src="${post.thumbnail}" />`;
								
							}
						    domHtml += `</div>`;
						}

						
					}  else if(tipo == 'catalogos'){

						for(const postId of postsId) {
							
							const post = this.parsePostData( feed[postId] );
							domHtml += `<div class="template">`
							domHtml += `<img data-postid="${postId}" data-play="${post.id}" ${post.header_div} ${post.tags_div} class="galeria-card" `;
							domHtml += `data-destaque="${hightlights.length}" `;

							hightlights.push( this.createHighlight( post ) );
								
							if ( post.content_type === "image" ) {
							
								domHtml += `src="${post.content}" />`;

							} else {

								domHtml += `src="${post.thumbnail}" />`;
								
							}
						    domHtml += `</div>`;
						}

						
					} else if(tipo == 'atributos'){
						var destdomHTML = '';
						for(const postId of postsId) {
							
							const post = this.parsePostData( feed[postId] );
							destdomHTML += `<div class="template template-g">`
							destdomHTML += `<img data-postid="${postId}" data-play="${post.id}" ${post.header_div} ${post.tags_div} class="galeria-card" `;
							destdomHTML += `data-destaque="${hightlights.length}" `;

							hightlights.push( this.createHighlight( post ) );
								
							if ( post.content_type === "image" ) {
							
								destdomHTML += `src="${post.content}" />`;

							} else {

								destdomHTML += `src="${post.thumbnail}" />`;
								
							}
						    destdomHTML += `</div>`;

						}
						galleryDestaqueDom.append(destdomHTML);

						domHtml += `<div class="secao" style="margin-bottom: 30px;">`						
						domHtml += `<h4>${tag.name}</h4>`;
					    domHtml += `<div class="bt-top"> <a href="#p=compartilhar-cards-atributos-todos&v=${tag.id}">ver todos</a></div>`;
						domHtml += `<div class="scroll-horizontal">`;

						for(const postId of postsId) {

							console.log(postsId);

						domHtml += `<div class="template">`;
						
							
							const post = this.parsePostData( feed[postId] );
							var img = '';
							if ( post.content_type === "image" ) {
							
								img = post.content;

							} else {

								img =  post.thumbnail;
								
							}

						    domHtml += `<img data-postid="${postId}" data-play="${post.id}" ${post.header_div} ${post.tags_div} class="galeria-card" `;
							domHtml += `data-destaque="${hightlights.length}" `;
							hightlights.push( this.createHighlight( post ) );
							domHtml += `src="${img}" />`;

									
							
						domHtml += `</div>`;
						}
						
						domHtml += `</div>`;
						domHtml += `<div class="margin-spacer"></div>`;
						domHtml +=  `</div>`;	

					} else if(tipo == 'concorrencia'){
						for(const postId of postsId) {
							
							const post = this.parsePostData( feed[postId] );
							domHtml += `<div class="template">`
							domHtml += `<img data-postid="${postId}" data-play="${post.id}" ${post.header_div} ${post.tags_div} class="galeria-card" `;
							domHtml += `data-destaque="${hightlights.length}" `;

							hightlights.push( this.createHighlight( post ) );
								
							if ( post.content_type === "image" ) {
							
								domHtml += `src="${post.content}" />`;

							} else {

								domHtml += `src="${post.thumbnail}" />`;
								
							}
						    domHtml += `</div>`;
						}
						$('.create-content .create-menu').scrollLeft(500);

					} else if(tipo == 'versao'){
						for(const postId of postsId) {
							
							const post = this.parsePostData( feed[postId] );
							domHtml += `<div class="template">`
							domHtml += `<img data-postid="${postId}" data-play="${post.id}" ${post.header_div} ${post.tags_div} class="galeria-card" `;
							domHtml += `data-destaque="${hightlights.length}" `;

							hightlights.push( this.createHighlight( post ) );
								
							if ( post.content_type === "image" ) {
							
								domHtml += `src="${post.content}" />`;

							} else {

								domHtml += `src="${post.thumbnail}" />`;
								
							}
						    domHtml += `</div>`;
						}
						$('.create-content .create-menu').scrollLeft(500);
						
					} else if(tipo == 'ondemand'){
						var destdomHTML = '';
						for(const postId of postsId) {
							
							const post = this.parsePostData( feed[postId] );
							destdomHTML += `<div class="template  template-g">`
							destdomHTML += `<img data-postid="${postId}" data-play="${post.id}" ${post.header_div} ${post.tags_div} class="galeria-card" `;
							destdomHTML += `data-destaque="${hightlights.length}" `;

							hightlights.push( this.createHighlight( post ) );
								
							if ( post.content_type === "image" ) {
							
								destdomHTML += `src="${post.content}" />`;

							} else {

								destdomHTML += `src="${post.thumbnail}" />`;
								
							}
						    destdomHTML += `</div>`;

						}
						galleryDestaqueDom.append(destdomHTML);

						domHtml += `<div class="secao" style="margin-bottom: 30px;">`						
						domHtml += `<h4>${tag.name}</h4>`;
					    domHtml += `<div class="bt-top"> <a href="#p=compartilhar-ondemand-todos&v=${tag.id}">ver todos</a></div>`;
						domHtml += `<div class="scroll-horizontal">`;

						for(const postId of postsId) {

						domHtml += `<div class="template">`;
						
							
							const post = this.parsePostData( feed[postId] );
							var img = '';
							if ( post.content_type === "image" ) {
							
								img = post.content;

							} else {

								img =  post.thumbnail;
								
							}

						    domHtml += `<img data-postid="${postId}" data-play="${post.id}" ${post.header_div} ${post.tags_div} class="galeria-card" `;
							domHtml += `data-destaque="${hightlights.length}" `;
							hightlights.push( this.createHighlight( post ) );
							domHtml += `src="${img}" />`;

									
							
						domHtml += `</div>`;
						}
						
						domHtml += `</div>`;
						domHtml += `<div class="margin-spacer"></div>`;
						domHtml +=  `</div>`;		

						$('.create-content .create-menu').scrollLeft(500);

					} else {
						for(const postId of postsId) {
							if(veiculo){
								$('#titulo-area-interna').text(tag.name);
							} else {
								$('#titulo-area-interna').text('materiais que acabaram de chegar');
							}
							
							const post = this.parsePostData( feed[postId] );
							domHtml += `<div class="template">`
							domHtml += `<img data-postid="${postId}" data-play="${post.id}" ${post.header_div} ${post.tags_div} class="galeria-card" `;
							domHtml += `data-destaque="${hightlights.length}" `;

							hightlights.push( this.createHighlight( post ) );
								
							if ( post.content_type === "image" ) {
							
								domHtml += `src="${post.content}" />`;

							} else {

								domHtml += `src="${post.thumbnail}" />`;
								
							}
						    domHtml += `</div>`;
						}
						
					}
					
				}
				
				const dom = $(domHtml);
				
				dom.find('[data-destaque]').click(function(){
					
					destaque.empty();
					
					destaque.append( hightlights );
					
					ui.destaqueMostra(parseInt(this.dataset.destaque), hightlights.length, true);
					
				});
				
				galleryDom.append( dom );

				ui.adicionaSetasScrollHorizontal();
				
			} else {
				
				galleryDom.append( noPost.clone().show() );
				
			}
			
			callback();
			
		} );

	}

	updateFilter(tipo, veiculo) {
		
		this.galleryComplete = false;
		
		this.updateGallery(() => {
			
			this.galleryComplete = true;
			this.galleryPlayID = app.urlParams.id;
			
			if ( this.galleryPlayID ) {

				this.playID();

			} else if ( app.urlParams.search ) {
				
				ui.buscar( app.urlParams.search, false );
				
			}
			
		}, tipo, veiculo);
		
	} 

	updateVehicles() {
		
		const vehiclesDom = $('#veiculos').removeClass('selected');
		const loading = $('.loading-vehicles');
		
		loading.show();

		this.feed.vehicles = "";
		
		var vehicles = {};
		
		const params = Object.assign({}, this.feed, this.feedVehicles, {q:'vehicles'});
		
		const createVehicle = ( vehicle ) => {
			const button = $( `<div class="veiculo"><img data-vehicle="${vehicle.id}" class="veiculo-item" src="${vehicle.content}" /> <div class="nome">${vehicle.name}</div> <div class="seta"><img src="images/setadir.svg" /></div></div>` );
			
			button.click(() => {
				window.location.href = '#p=compartilhar-imagens-carro&v=' + vehicle.id;
			});
			
			return button;
			
		}
		
		app.request(params, ( res ) => {
			
			loading.hide();
			
			for(const vehicle of res.vehicles) {
				
				vehiclesDom.append( createVehicle( vehicle ) );
			
			}
			
			vehiclesDom.append(`<div class="margin-spacer"></div>`);
			
		} );
		
	}

	update() {
		super.update();

		this.updateHelpMessage('compartilhar');
		
		this.updateVehicles();

		if (app.urlParams.p == 'compartilhar-imagens') {
			if (app.urlParams.v != '') {
				this.updateFilter('imagens', app.urlParams.v);
			}else{
				this.updateFilter('imagens');
			}
		} else if(app.urlParams.p == 'compartilhar-imagens-interna') {
			this.updateFilter('imagens-interna', app.urlParams.v);
		} else if(app.urlParams.p == 'compartilhar-imagens-carro') {
			this.updateFilter('imagens-carro', app.urlParams.v);
		} else if(app.urlParams.p == 'compartilhar-catalogos') {
			this.updateFilter('catalogos');
		} else if(app.urlParams.p == 'compartilhar-cards-atributos') {
			this.updateFilter('atributos');
		} else if(app.urlParams.p == 'compartilhar-cards-atributos-todos') {
			this.updateFilter('atributos-todos', app.urlParams.v);
		} else if(app.urlParams.p == 'compartilhar-comparativo-concorrencia') {
			this.updateFilter('concorrencia');
		} else if(app.urlParams.p == 'compartilhar-comparativo-versao') {
			this.updateFilter('versao');
		} else if(app.urlParams.p == 'compartilhar-ondemand') {
			this.updateFilter('ondemand');
		} else if(app.urlParams.p == 'compartilhar-ondemand-todos') {
			this.updateFilter('ondemand-todos');
		} else {
			this.updateFilter('initial');
		}
	}

}

SharePage.load = () => {
	
	if ( !app.pages.share ) {
		app.pages.share = new SharePage();
	}
	
	app.pages.share.update();

	
}
