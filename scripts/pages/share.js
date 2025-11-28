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

	updateGallery( callback = CALLBACK, tipo, veiculo, modelo) {
		
		const destaque = $('#destaque');
		const galleryDom = $('#gallery');
		const galleryDestaqueDom = $('#gallery-destaque');
		const loading = $('.loading-gallery');
		const noPost = $('#no-post');
		
		var isMobile = {
		  Android: function() {return navigator.userAgent.match(/Android/i);},
		  iOS: function() {return navigator.userAgent.match(/iPhone|iPad|iPod/i);},
		  Windows: function() {return navigator.userAgent.match(/IEMobile/i);},
		  any: function() {return (isMobile.Android() || isMobile.iOS() || isMobile.Windows());}
		};
   

		$('.page-content').addClass('fixed-header');
		
		galleryDom.empty();
		galleryDom.html('');
		let domHtml = '';
		let hightlights = [];
		
		loading.show();

console.log('TIPO -> ' + tipo)
		
		if(tipo == 'imagens') {
			if(veiculo != undefined) {
				var params = Object.assign({}, this.feed, this.filterGallery, {q:'feed', filter:'galeria', group:'images', content_type: 'image', vehicles: veiculo, vehicleversions : ''});
			} else {
				var params = Object.assign({}, this.feed, this.filterGallery, {q:'vehicleversions'});
			}
		} else if(tipo == 'catalogos') {
			var params = Object.assign({}, this.feed, this.filterGallery, {q:'feed', filter:'galeria', content_type: 'image', tags:'12'});

			var banners = Object.assign({}, this.feed, this.feedVehicles, {q:'global'});
				app.request(banners, ( res ) => {
				$('.banner-img').css({'background-image': 'url('+res.compartilhar_catalogos+')'})
			})
		} else if(tipo == 'imagens-interna') {
			if(modelo !=  null){
				if (isMobile.any()) {
					var params = Object.assign({}, this.feed, this.filterGallery, {q:'feed', filter:'galeria', content_type: 'image', group:'images', vehicleversion: modelo, vehicles: veiculo, limit : 5});
				}else{
					var params = Object.assign({}, this.feed, this.filterGallery, {q:'feed', filter:'galeria', content_type: 'image', group:'images', vehicleversion: modelo, vehicles: veiculo});
				}
			} else {
				var params = Object.assign({}, this.feed, this.filterGallery, {q:'vehicleversions', filter:'galeria', content_type: 'image', group:'images', tags: veiculo});
			}
		} else if(tipo == 'atributos') {
			var params = Object.assign({}, this.feed, this.filterGallery, {q:'feed', filter:'galeria', content_type: 'image', group:'cards_attributes'});

			var banners = Object.assign({}, this.feed, this.feedVehicles, {q:'global'});
				app.request(banners, ( res ) => {
				$('.banner-img').css({'background-image': 'url('+res.compartilhar_cards_atributos+')'})
			})
		}  else if(tipo == 'atributos-todos') {
			if(veiculo != ''){
				var params = Object.assign({}, this.feed, this.filterGallery, {q:'feed', filter:'galeria', content_type: 'image', tags: veiculo});
			} else {
				var params = Object.assign({}, this.feed, this.filterGallery, {q:'feed', filter:'galeria', content_type: 'image', group:'cards_attributes'});
			}

			var banners = Object.assign({}, this.feed, this.feedVehicles, {q:'global'});
				app.request(banners, ( res ) => {
				$('.banner-img').css({'background-image': 'url('+res.compartilhar_cards_atributos+')'})
			})
		} else if(tipo == 'concorrencia') {
			var params = Object.assign({}, this.feed, this.filterGallery, {q:'feed', filter:'galeria', content_type: 'image', group:'competition_comparatives'});
		} else if(tipo == 'concorrencia-todos') {
			var params = Object.assign({}, this.feed, this.filterGallery, {q:'feed', filter:'galeria', content_type: 'image', tags: veiculo});
		} else if(tipo == 'versao') {
			var params = Object.assign({}, this.feed, this.filterGallery, {q:'feed', filter:'galeria', content_type: 'image', tags:'19'});

			var banners = Object.assign({}, this.feed, this.feedVehicles, {q:'global'});
				app.request(banners, ( res ) => {
				$('.banner-img').css({'background-image': 'url('+res.compartilhar_comp_versoes+')'})
			})
		} else if(tipo == 'ondemand') {
			var params = Object.assign({}, this.feed, this.filterGallery, {q:'feed', filter:'galeria', group:'renault_on_demand'});

			var banners = Object.assign({}, this.feed, this.feedVehicles, {q:'global'});
				app.request(banners, ( res ) => {
				$('.banner-img').css({'background-image': 'url('+res.compartilhar_on_demand+')'})
			})
		} else if(tipo == 'ondemand-todos') {
			var params = Object.assign({}, this.feed, this.filterGallery, {q:'feed', filter:'galeria', group:'renault_on_demand'});

			var banners = Object.assign({}, this.feed, this.feedVehicles, {q:'global'});
				app.request(banners, ( res ) => {
				$('.banner-img').css({'background-image': 'url('+res.compartilhar_on_demand+')'})
			})
		} else if(tipo == 'imagens-carro') {
			var params = Object.assign({}, this.feed, this.filterGallery, {q:'feed'});
			var paramsAll = Object.assign({}, this.feed, this.filterGallery, {q:'feed_vehicle', filter:'galeria', content_type: 'image', vehicles: veiculo});
			
			var loopID = 0;
			
			app.request(paramsAll, ( res ) => {
				loading.hide();	
				galleryDom.empty();
				galleryDom.html('');
				domHtml = '';
				hightlights = [];

				var feed_cat = '';
				var tags_cat = '';
				var feed_com = '';
				var tags_com = '';
				var feed_img = '';
				var tags_img = '';
				var version_img = '';
				var feed_atr = '';
				var tags_atr = '';
				var feed_ccp = '';
				var tags_ccp = '';


				if( res.catalogos_compactos ){
					feed_cat = res.catalogos_compactos.feed;
					tags_cat = res.catalogos_compactos.tags;
				}
				if( res.card_comparativo ){
					feed_com = res.card_comparativo.feed;
					tags_com = res.card_comparativo.tags;
				}
				if( res.imagens_veiculos ){
					feed_img = res.imagens_veiculos.feed;
					tags_img = res.imagens_veiculos.tags;
					version_img = res.imagens_veiculos.vehicleversions;
				}
				if( res.cards_atributos ){
					feed_atr = res.cards_atributos.feed;
					tags_atr = res.cards_atributos.tags;
				}
				if( res.comparativos_concorrencia ){
					feed_ccp = res.comparativos_concorrencia.feed;
					tags_ccp = res.comparativos_concorrencia.tags;
				}


						domHtml += `<div class="secao" style="margin-bottom: 60px;">`						
						domHtml += `<div class="scroll-horizontal">`;

				if ( feed_cat.length > 0 )  {	
					
					for(const tag of tags_cat) {
						const postsId = tag.posts;


						for(const postId of postsId) {
							
							const post = this.parsePostData(feed_cat[postId]);

							domHtml += `<div class="template template-g">`
							domHtml += `<img data-postid="${loopID}" data-play="${post.id}" ${post.header_div} ${post.tags_div} class="galeria-card" `;
							hightlights.push( this.createHighlight( post ) );
							domHtml += `data-destaque="${hightlights.length - 1}" `;

							if ( post.content_type === "image" ) {
							
								domHtml += `src="${post.content}" />`;

							} else {

								domHtml += `src="${post.thumbnail}" />`;
								
							}
						    domHtml += `</div>`;
						}
						loopID++;
					}
				}
				if ( feed_com.length > 0 )  {	
					for(const tag of tags_com) {
						const postsId = tag.posts;


						for(const postId of postsId) {
							
							const post = this.parsePostData(feed_com[postId]);

							domHtml += `<div class="template template-g">`
							domHtml += `<img data-postid="${loopID}" data-play="${post.id}" ${post.header_div} ${post.tags_div} class="galeria-card" `;
							hightlights.push( this.createHighlight( post ) );
							domHtml += `data-destaque="${hightlights.length - 1}" `;
								
							if ( post.content_type === "image" ) {
							
								domHtml += `src="${post.content}" />`;

							} else {

								domHtml += `src="${post.thumbnail}" />`;
								
							}
						    domHtml += `</div>`;
						}
						loopID++;
					}
				}
						domHtml += `</div>`;
						domHtml +=  `</div>`;

			
				if ( feed_img.length > 0 )  {	
					for(const tag of tags_img) {
						const postsId = tag.posts;

						domHtml += `<div class="secao" style="margin-bottom: 60px;">`						
						domHtml += `<h4>${tag.name}</h4>`;
						domHtml += `<div class="scroll-horizontal p-left">`;

						for(const postId of postsId) {
							
							const post = feed_img[postId];

							domHtml += `<div class="imagem">`;
							domHtml += `<a href="#p=compartilhar-imagens-interna&v=${post.vehicle}&m=${post.version}">`;
													
								var img = '';
								if ( post.content_type === "image" ) {
									img = post.content;
								} else {
									img =  post.thumbnail;
								}
							    domHtml += `<div class="hld-bg img-p" style="background-image: url('${img}');">`;
							    domHtml += `<div class="nome"><span>${post.version_name}</span></div>`;

							    domHtml += `<img `;
								domHtml += `src="${img}" />`;
							    domHtml += `</div>`;		
								
							domHtml += `</a>`;
							domHtml += `</div>`;
						}

						domHtml += `</div>`;
						domHtml +=  `</div>`;
					}
				}

				if ( feed_atr.length > 0 )  {	
					for(const tag of tags_atr) {
						const postsId = tag.posts;

						domHtml += `<div class="secao" style="margin-bottom: 60px;">`						
						domHtml += `<h4>${tag.name}</h4>`;
						domHtml += `<div class="scroll-horizontal">`;

						for(const postId of postsId) {
							
							const post = this.parsePostData(feed_atr[postId]);

							domHtml += `<div class="template">`
							domHtml += `<img data-postid="${postId}" data-play="${post.id}" ${post.header_div} ${post.tags_div} class="galeria-card" `;
							hightlights.push( this.createHighlight( post ) );
							domHtml += `data-destaque="${hightlights.length - 1}" `;

								
							if ( post.content_type === "image" ) {
							
								domHtml += `src="${post.content}" />`;

							} else {

								domHtml += `src="${post.thumbnail}" />`;
								
							}
						    domHtml += `</div>`;
						}

						domHtml += `</div>`;
						domHtml +=  `</div>`;
					}
				}

				if ( feed_ccp.length > 0 )  {	
					for(const tag of tags_ccp) {
						const postsId = tag.posts;

						domHtml += `<div class="secao" style="margin-bottom: 60px;">`						
						domHtml += `<h4>${tag.name}</h4>`;
						domHtml += `<div class="scroll-horizontal">`;

						for(const postId of postsId) {
							
							const post = this.parsePostData(feed_ccp[postId]);

							domHtml += `<div class="template">`
							domHtml += `<img data-postid="${postId}" data-play="${post.id}" ${post.header_div} ${post.tags_div} class="galeria-card" `;
							hightlights.push( this.createHighlight( post ) );
							domHtml += `data-destaque="${hightlights.length - 1}" `;

								
							if ( post.content_type === "image" ) {
							
								domHtml += `src="${post.content}" />`;

							} else {

								domHtml += `src="${post.thumbnail}" />`;
								
							}
						    domHtml += `</div>`;
						}

						domHtml += `</div>`;
						domHtml +=  `</div>`;
					}
				}

				var dom = '';
				dom = $(domHtml);
				
				dom.find('[data-destaque]').click(function(){

					destaque.empty();
					
					destaque.append( hightlights );
					
					ui.destaqueMostra(parseInt(this.dataset.destaque), hightlights.length, true);
					
				});
				
				galleryDom.append( dom );

				ui.adicionaSetasScrollHorizontal();
			})

			callback();

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

		} else if(tipo == 'compartilhar-cartao') {
			
			var params = Object.assign({}, this.feed, {q:'get_digital_card'});

			$('body').on('click', '.bt-fechar-fundo', function(ev) {
		        ev.preventDefault();
		        ev.stopImmediatePropagation();
		        $(this).parent().parent().parent().stop().fadeOut();
		  	});

		
		  $('body').on('click', '.bt-gera-cartao', function(ev) {
		     ev.preventDefault();
		     ev.stopImmediatePropagation();

		     var whats = 'https://api.whatsapp.com/send?phone=550' + $('#valWhatsapp').val().replace(/\D/g, '');
		     var tel = 'tel:0' + $('#valTelefone').val().replace(/\D/g, '');
		     var mail = 'mailto:' + $('#valEmail').val();
		     var loc = 'https://www.google.com.br/maps/dir//' + encodeURI($('#valLocalizacao').val());

		     $.ajax({
		        url: 'cartao.php',
		        type: 'POST',
				dataType: 'json',
		        data: {
		          bg: $('#valBg').val(),
		          img: $('#valImg').val(),
		          nome: $('#valNome').val(),
		          cargo: $('#valCargo').val(),
		          whatsapp: whats,
		          telefone: tel,
		          email: mail,
		          localizacao: loc,
		          concessionaria: $('#valConcessionaria').val(),
		        },
		        success: function (data) {
					//window.open(data); 
					/*var a = document.createElement('a');
					a.href = data.url;
					a.download = data.file_name; // Defina o nome do arquivo aqui
					a.click();*/
					
					//var down_card = "/content.php?card=" + data.folder_name;
					var down_card = app.url("/content.php?card=" + data.folder_name);
					
					if (native.download) {
				
						native.download( down_card, "Cartao Digital.pdf" );
						
					} else {
						
						app.download( down_card, "Cartao Digital.pdf" );
						
					}
						
		        }
		      });
		   });
		   
		} else {
			var params = Object.assign({}, this.feed, this.filterGallery, {q:'feed', filter:'compartilhar', content_type: 'image', limit: '10'});

			var banners = Object.assign({}, this.feed, this.feedVehicles, {q:'global'});
				app.request(banners, ( res ) => {
				$('.banner-img').css({'background-image': 'url('+res.compartilhar_intro+')'})
			})
		}

		
			var countDest = 0;
			var countDestOndemand = 0;
			var countTag = 0;
		
		app.request(params, ( res ) => {
			
			loading.hide();
		
			const feed = res.feed;
			const tags = res.tags;
			const versions = res.vehicleversions;
			
			var count = 0;
			
			if ( feed.length > 0 )  {
				
				for(const tag of tags) {
					
					const postsId = tag.posts;


					if(tipo == 'imagens') {

						domHtml += `<div class="secao" style="margin: 45px 0px;">`						
						domHtml += `<h4>${tag.name}</h4>`;
						domHtml += `<div class="scroll-horizontal">`;

						for(const postId of postsId) {
							const post = feed[postId];

							domHtml += `<div class="imagem">`;
							domHtml += `<a href="#p=compartilhar-imagens-interna&v=${post.vehicle}&m=${post.version}">`;
													
								img =  post.thumbnail;
							    domHtml += `<div class="hld-bg img-p" style="background-image: url('${img}');">`;
							    domHtml += `<div class="nome"><span>${post.version_name}</span></div>`;
							    domHtml += `<img `;
								hightlights.push( this.createHighlight( post ) );
								domHtml += `src="${img}" />`;
							    domHtml += `</div>`;		
								
							domHtml += `</a>`;
							domHtml += `</div>`;
						}
						
						domHtml += `</div>`;
						domHtml += `<div class="margin-spacer"></div>`;
						domHtml +=  `</div>`;

					} else if(tipo == 'imagens-interna'){

						var vversions = res.vehicleversions[0];	

						for(const postId of postsId) {
							const post = this.parsePostData( feed[postId] );
							var img = '';
							if ( post.content_type === "image" ) {
								img = post.content;
							} else {
								img =  post.thumbnail;
							}

							for(const version of versions) {
								if(version.id == post.version) {
									$('#linkMais').attr('href', version.link);
								}
							}

							$('#linkMais').attr('href', vversions.link);

							if(count == 0) {
								domHtml += `<div class="imagem destaque" style="background-image: url('${img}');" content-id="${post.id}">`;
								domHtml += `<img data-postid="${postId}" data-play="${post.id}" ${post.header_div} ${post.tags_div} class="galeria-card" `;
								domHtml += `data-destaque="${hightlights.length}" `;
								hightlights.push( this.createHighlight( post ) );
								domHtml += `src="${img}" />`;
							    domHtml += `	<div class="titulo"><span>${vversions.title}</span></div>`;
							    domHtml += `    <div class="bts">`;
							    //domHtml += `      <a content-action="proxleitura"><img src="images/ico_salvar_imagem.svg?v=2"></a>`;
								domHtml += `      <a class="acao-icone branco proxleitura ${ post.read_later ? 'ativo' : '' } push no-text"></a> `;
							    domHtml += `      <a download="feedrenault_${post.id}" content-id="${post.id}" content-message="" content-url="${img}" content-download="${post.download}" class="download"><img src="images/ico_download_imagem.svg?v=2"></a>`;
								
							    domHtml += `    </div>`;
							    domHtml += `</div>`;
							} else {
							    domHtml += `<div class="imagem" style="background-image: url('${img}');" content-id="${post.id}">`;
							    domHtml += `<img data-postid="${postId}" data-play="${post.id}" ${post.header_div} ${post.tags_div} class="galeria-card" `;
								domHtml += `data-destaque="${hightlights.length}" `;
								hightlights.push( this.createHighlight( post ) );
								domHtml += `src="${img}" />`;
							    domHtml += `    <div class="bts">`;
							    //domHtml += `      <a content-action="proxleitura"><img src="images/ico_salvar_imagem.svg?v=2"></a>`;
								domHtml += `      <a class="acao-icone branco proxleitura ${ post.read_later ? 'ativo' : '' } push no-text"></a> `;
							    domHtml += `      <a download="feedrenault_${post.id}" content-id="${post.id}" content-message="" content-url="${img}" content-download="${post.download}" class="download"><img src="images/ico_download_imagem.svg?v=2"></a>`;
							    domHtml += `    </div>`;
							    domHtml += `</div>`;
							}
							count++;

						}

					} else if(tipo == 'catalogos'){

						for(const postId of postsId) {
							
							const post = this.parsePostData( feed[postId] );
							domHtml += `<div class="template template-m">`
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

						if(countTag == 0) {
							for(const postId of postsId) {

								destdomHTML += `<div class="template template-g">`
								
								const post = this.parsePostData( feed[postId] );
								
								var img = '';
								if ( post.content_type === "image" ) {
								
									img = post.content;

								} else {

									img =  post.thumbnail;
									
								}

							    destdomHTML += `<img data-postid="${postId}" data-play="${post.id}" ${post.header_div} ${post.tags_div} class="galeria-card destaque-card" `;
								destdomHTML += `data-destaque="${hightlights.length}" `;
								hightlights.push( this.createHighlight( post ) );
								destdomHTML += `src="${img}" />`;
								
							    destdomHTML += `</div>`;

							}

							const domdest = $(destdomHTML);
						
							domdest.find('[data-destaque]').click(function(){
								
								destaque.empty();
								
								destaque.append( hightlights );
								
								ui.destaqueMostra(parseInt(this.dataset.destaque), hightlights.length, true);
								
							});
							
							galleryDestaqueDom.append( domdest );
						}

						if(countTag > 0) {

						domHtml += `<div class="secao" style="margin: 45px 0px;">`						
						domHtml += `<h4>${tag.name}</h4>`;
					    domHtml += `<div class="bt-top"> <a href="#p=compartilhar-cards-atributos-todos&v=${tag.id}">ver todos</a></div>`;
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

							    domHtml += `<img data-postid="${postId}" data-play="${post.id}" ${post.header_div} ${post.tags_div} class="galeria-card destaque-card" `;
								domHtml += `data-destaque="${hightlights.length}" `;
								hightlights.push( this.createHighlight( post ) );
								domHtml += `src="${img}" />`;

										
								
							domHtml += `</div>`;
						}
						
						domHtml += `</div>`;
						domHtml += `<div class="margin-spacer"></div>`;
						domHtml +=  `</div>`;
						}

						countTag++;

					} else if(tipo == 'concorrencia'){
						domHtml += `<div class="secao" style="margin-top: 45px;">`						
						domHtml += `<h4>${tag.name}</h4>`;
					    domHtml += `<div class="bt-top"> <a href="#p=compartilhar-comparativo-concorrencia-todos&v=${tag.id}">ver todos</a></div>`;
						domHtml += `<div class="scroll-horizontal alt-padding">`;

						for(const postId of postsId) {

						domHtml += `<div class="template template-m">`;
						
							
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

						if(countTag == 0) {
							for(const postId of postsId) {

								destdomHTML += `<div class="template template-g">`
								
								const post = this.parsePostData( feed[postId] );
								
								var img = '';
								if ( post.content_type === "image" ) {
								
									img = post.content;

								} else {

									img =  post.thumbnail;
									
								}

							    destdomHTML += `<img data-postid="${postId}" data-play="${post.id}" ${post.header_div} ${post.tags_div} class="galeria-card destaque-card" `;
								destdomHTML += `data-destaque="${hightlights.length}" `;
								hightlights.push( this.createHighlight( post ) );
								destdomHTML += `src="${img}" />`;
								
							    destdomHTML += `</div>`;

							}

							const domdest = $(destdomHTML);
						
							domdest.find('[data-destaque]').click(function(){
								
								destaque.empty();
								
								destaque.append( hightlights );
								
								ui.destaqueMostra(parseInt(this.dataset.destaque), hightlights.length, true);
								
							});
							
							galleryDestaqueDom.append( domdest );
						}

						if(countTag > 0) {

						domHtml += `<div class="secao" style="margin: 45px 0px;">`						
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

							    domHtml += `<img data-postid="${postId}" data-play="${post.id}" ${post.header_div} ${post.tags_div} class="galeria-card destaque-card" `;
								domHtml += `data-destaque="${hightlights.length}" `;
								hightlights.push( this.createHighlight( post ) );
								domHtml += `src="${img}" />`;

										
								
							domHtml += `</div>`;
						}
						
						domHtml += `</div>`;
						domHtml += `<div class="margin-spacer"></div>`;
						domHtml +=  `</div>`;
						}

						countTag++;
						$('.create-content .create-menu').scrollLeft(500);

					} else {
						if(tipo != 'imagens-carro'){
							for(const postId of postsId) {
								if(veiculo){
									$('#titulo-area-interna').text(tag.name);
								} else {
									$('#titulo-area-interna').text('materiais que acabaram de chegar');
								}
								
								// const post = this.parsePostData( feed[postId] );
								// domHtml += `<div class="template">`
								// domHtml += `<img data-postid="${postId}" data-play="${post.id}" ${post.header_div} ${post.tags_div} class="galeria-card" `;
								// hightlights.push( this.createHighlight( post ) );
								// domHtml += `data-destaque="${hightlights.length}" `;

									
								// if ( post.content_type === "image" ) {
								
								// 	domHtml += `src="${post.content}" />`;

								// } else {

								// 	domHtml += `src="${post.thumbnail}" />`;
									
								// }
							    // domHtml += `</div>`;
							    domHtml += `<div class="template">`;
						
							
							const post = this.parsePostData( feed[postId] );
							var img = '';
							if ( post.content_type === "image" ) {
							
								img = post.content;

							} else {

								img =  post.thumbnail;
								
							}

						    domHtml += `<img data-postid="${postId}" data-play="${post.id}" ${post.header_div} ${post.tags_div} class="galeria-card destaque-card" `;
							domHtml += `data-destaque="${hightlights.length}" `;
							hightlights.push( this.createHighlight( post ) );
							domHtml += `src="${img}" />`;

									
							
						domHtml += `</div>`;
							}
						}
						
					}
					
				}
				
				if(tipo != 'imagens-carro'){
					const dom = $(domHtml);
					
					dom.find('[data-destaque]').click(function(){
						
						destaque.empty();
						
						destaque.append( hightlights );
						
						ui.destaqueMostra(parseInt(this.dataset.destaque), hightlights.length, true);
						
					});
					
					galleryDom.append( dom );

					ui.adicionaSetasScrollHorizontal();
				}
				
			} else {
				
				galleryDom.append( noPost.clone().show() );
				
			}
			
			callback();
			
		} );

	}

	updateFilter(tipo, veiculo, modelo) {
		
		this.galleryComplete = false;
		
		this.updateGallery(() => {
			
			this.galleryComplete = true;
			this.galleryPlayID = app.urlParams.id;
			
			if ( this.galleryPlayID ) {

				this.playID();

			} else if ( app.urlParams.search ) {
				
				ui.buscar( app.urlParams.search, false );
				
			}
			
		}, tipo, veiculo, modelo);
		
	} 

	updateVehicles() {
		
		const vehiclesDom = $('#veiculos').removeClass('selected');
		const loading = $('.loading-vehicles');
		
		loading.show();

		this.feed.vehicles = "";
		
		var vehicles = {};
		
		const params = Object.assign({}, this.feed, this.feedVehicles, {q:'vehicles'});
		
		const createVehicle = ( vehicle ) => {
			const button = $( `<div class="veiculo"><img data-vehicle="${vehicle.id}" class="veiculo-item link-interna" data-subinterna="true" data-bt="back" data-title="compartilhar" src="${vehicle.content}" /> <div class="nome">${vehicle.name}</div> <div class="seta"><img src="images/setadir.svg?v=2" /></div></div>` );
			
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
		
		$('.header-interna').find('.header-title').html( 'compartilhar' );
		
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
			this.updateFilter('imagens-interna', app.urlParams.v, app.urlParams.m);
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
		} else if(app.urlParams.p == 'compartilhar-comparativo-concorrencia-todos') {
			this.updateFilter('concorrencia-todos', app.urlParams.v);
		} else if(app.urlParams.p == 'compartilhar-comparativo-versao') {
			this.updateFilter('versao');
		} else if(app.urlParams.p == 'compartilhar-ondemand') {
			this.updateFilter('ondemand');
		} else if(app.urlParams.p == 'compartilhar-ondemand-todos') {
			this.updateFilter('ondemand-todos');
		} else if(app.urlParams.p == 'compartilhar-cartao') {
			//$('.header-interna').find('.header-title').html( 'cartão <strong>digital</strong>' );
			this.updateFilter('compartilhar-cartao');	
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
