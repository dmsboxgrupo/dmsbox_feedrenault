class CreatePage extends Feed {

	constructor() {
		super();

		this.galleryComplete = false;
		
		signal.on('page_load', ( e ) => {

			if ( e.page === 'create-renault.html' ) {
				
				this.galleryComplete = false;
				
			}
		
		});

		signal.on('page_complete', ( e ) => {
			if ( e.page === 'card-img-perfil.html' ) {


				var params = Object.assign({}, this.feed, {q:'get_digital_card'});

				app.request(params, ( res ) => {
					const cardData = res.digital_card;

					if ( cardData )  {
						if(cardData.image) {
							$('#image').attr('src', cardData.image);
						}
					}
				});

				$('body').on('click', '.bt-cancelar-imagem', function(ev) {
				    ev.preventDefault();
				    ev.stopImmediatePropagation();
				    
				    $(this).parent().parent().parent().stop().fadeOut();
			  });


			 	/* CARTÃO */
			    var fileInput = $('#file');
			    var image = $('#image');
			    var cropper = '';
			    var fileName = '';
			    var range = $('#scale');

			    fileInput.on('change', function(){
			      var reader = new FileReader();
			      fileName = fileInput.prop('files')[0].name.split(".")[0];

			      reader.readAsDataURL(fileInput.prop('files')[0]);
			      reader.onload = () => {
			        image.attr("src", reader.result);
			        $('.hld-foto').addClass('on');
					/*
			        if (cropper) {
			          cropper.destroy();
			        }*/

			        cropper = new Cropper(image[0], {
			            dragMode: 'move',
			            aspectRatio: 1,
			            autoCropArea: 1,
			            restore: false,
			            guides: false,
			            center: false,
			            highlight: false,
			            cropBoxMovable: false,
			            cropBoxResizable: false,
			            toggleDragModeOnDblclick: false,
			            ready() {
			              this.cropper.zoomTo(1);
			              $('.slide').addClass('on');
			            },
			          });
			      };

			      $('.bt.amarelo').removeClass('off');
			      $('.mascara').addClass('off');
			      $('.crop-foto .holder .info').addClass('on');

			      var wait = setTimeout(function(){
			        clearTimeout(wait);
			      	$('.crop-foto .holder .info').removeClass('on');
			      }, 2000);
			      
			      function updateRange() {
			        var max = range.prop('max');
			        var min = range.prop('min');
			        var wid = range.width() - 20;
			        var perc = (range.val() * 100) / max;
			        var step = ((perc * wid) / 100) + 10;

			        var currentValue = Number(range.val());
			        var zoomValue = parseFloat(currentValue);

			        cropper.zoomTo(zoomValue.toFixed(4));
			      }

			      $(document).on('input', range, function() {
			          updateRange();
			      });

			      $('.slide .bt-mais').on("click", function(event) {
			        event.preventDefault();
			        range.val(Number(range.val()) + 0.1);
			        updateRange();
			      });

			      $('.slide .bt-menos').on("click", function(event) {
			        event.preventDefault();
			        range.val(Number(range.val()) - 0.1);
			        updateRange();
			      });
				  
			      $('.bt-salvar-imagem').on('click', function(ev) {
						
			      //$('body').on('click', '.bt-salvar-imagem', function(ev) {
					    ev.preventDefault();
					    ev.stopImmediatePropagation();

			      		var imgSrc = cropper.getCroppedCanvas({}).toDataURL();
						//alert(imgSrc);
				        $.ajax({
				          url: 'imagem.php',
				          type: 'POST',
				          dataType: 'json',
				          data: {
				            img: imgSrc
				          },
				          success: function (data) {
				          	var imgURL = 'uploads/img/' + data.name + '.jpg';
			      			var img = document.createElement("img");
            				img.src = imgURL;
            				$('.img-perfil').html(img).addClass('on');

            				$('#valImg').val(imgURL);

            				var params = Object.assign({}, this.feed, {q:'set_digital_card', 'image': $('#valImg').val()});
						     app.request(params, ( res ) => {
						     	 $(this).parent().parent().parent().stop().fadeOut();
						     });
				          }
				        });

			      		$(this).parent().parent().parent().stop().fadeOut();
			      	});

			    });


			}
			if ( e.page === 'edit-cartao.html' ) {
				var params = Object.assign({}, this.feed, {q:'get_background_images'});

				let defaulImageID = '';
				let defaulImageURL = '';

				$('.page-content').removeClass('fixed-header');

				app.request(params, ( res ) => {
					const backs = res.background_images;

					if ( backs.length > 0 )  {
						var countBack = 0;

						for(const back of backs) {
						    if(countBack == 0){
						    	defaulImageID = back.id;
								defaulImageURL = back.background_image_url;
						    }
						    countBack++;
						}
					}

					var paramSave = Object.assign({}, this.feed, {q:'get_digital_card'});


					app.request(paramSave, ( res ) => {
						const cardData = res.digital_card;

						if ( cardData )  {
							if(cardData.background_image) {
								$('#fundopopup .fundo-item #' + cardData.background_image).addClass('selected');
								$('.create-header.card').css({'background-image':'url('+ cardData.background_image_url +')'});

								$('#valBg').val(cardData.background_image_url);
								$('#valBgId').val(cardData.background_image);
							} else {
								$('#fundopopup .fundo-item #' + defaulImageID).addClass('selected');
								$('.create-header.card').css({'background-image':'url('+ defaulImageURL +')'});

								$('#valBg').val(defaulImageURL);
								$('#valBgId').val(defaulImageID);
							}


							if(cardData.name) {
								$('.nome h3 span').text(cardData.name);
								$('#valNome').val(cardData.name);
							}
							if(cardData.job) {
								$('.nome p').text(cardData.job);
								$('#valCargo').val(cardData.job);
							}
							if(cardData.concessionaire) {
					     		$('.concessionaria .txt').html(cardData.concessionaire);
								$('#valConcessionaria').val(cardData.concessionaire);
							}
							if(cardData.image) {
								$('.img-perfil').html('<img src="'+cardData.image+'">').addClass('on');
								$('#valImg').val(cardData.image);
							}

							if(cardData.whatsapp) {
								$('#valWhatsapp').val(cardData.whatsapp);
							}

							if(cardData.phone) {
								$('#valTelefone').val(cardData.phone);
							}

							if(cardData.email) {
								$('#valEmail').val(cardData.email);
							}

							if(cardData.location) {
								$('#valLocalizacao').val(cardData.location);
							}

						} else {
							if($('#valBgId').val() != ''){
								$('#fundopopup .fundo-item #' + $('#valBgId').val()).addClass('selected');
								$('.create-header.card').css({'background-image':'url('+ $('#valBg').val() +')'});
							} else {
								$('#fundopopup .fundo-item #' + defaulImageID).addClass('selected');
								$('.create-header.card').css({'background-image':'url('+ defaulImageURL +')'});

								$('#valBg').val(defaulImageURL);
								$('#valBgId').val(defaulImageID);
							}
						}
					});


				});

			}

			if ( e.page === 'card-infos.html' ) {
				var inputEnd = document.getElementById('cartao-localizacao');
		    	new google.maps.places.Autocomplete(inputEnd);
			}
		});

		signal.on('params', ( e ) => {
			if ( e.p === 'create-para-atrair-carro' ) {
				this.update();
			}
			if ( e.p === 'create-home' ) {
				this.update();
			}
		});

		signal.on('filtrar', ( e ) => {
			var order = '';

			if(e.value == 'maior relevância') {
				order = '2';
			}

			if(e.value == 'mais novo para mais antigo') {
				order = '0';
			}

			if(e.value == 'mais antigo para mais novo') {
				order = '1';
			}

			app.urlParams.f = order;

			this.update();
		});

	}

	updateFilter(tipo, veiculo, modelo, order) {
		
		this.galleryComplete = false;
		
		this.updateGallery(() => {
			
			this.galleryComplete = true;
			this.galleryPlayID = app.urlParams.id;
			
			if ( this.galleryPlayID ) {

				this.playID();

			} else if ( app.urlParams.search ) {
				
				ui.buscar( app.urlParams.search, false );
				
			}
			
		}, tipo, veiculo, modelo, order);
		
	} 

	updateGallery( callback = CALLBACK, tipo, veiculo, modelo, order = null) {
		const destaque = $('#destaque');
		const galleryDom = $('#gallery');
		const galleryDestaqueDom = $('#gallery-destaque');
		const btsDom = $('#header-bts');
		const loading = $('.loading-gallery');
		const noPost = $('#no-post');

		var isMobile = {
		  Android: function() {return navigator.userAgent.match(/Android/i);},
		  iOS: function() {return navigator.userAgent.match(/iPhone|iPad|iPod/i);},
		  Windows: function() {return navigator.userAgent.match(/IEMobile/i);},
		  any: function() {return (isMobile.Android() || isMobile.iOS() || isMobile.Windows());}
		};

		$('body').on('click', '.bt-gera-cartao', function(ev) {
		     ev.preventDefault();
		     ev.stopImmediatePropagation();

		     var whats = 'https://api.whatsapp.com/send?phone=550' + $('#valWhatsapp').val().replace(/\D/g, '');
		     var tel = 'tel:0' + $('#valTelefone').val().replace(/\D/g, '');
		     var mail = 'mailto:' + $('#valEmail').val();
		     var loc = 'https://www.google.com.br/maps/dir//' + encodeURI($('#valLocalizacao').val());
			
			//import { saveAs } from 'file-saver';
			//import { saveAs } from './FileSaver.js';

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
					//var pdfUrl = data.url;
					// Redirecionar o navegador para o arquivo PDF
					//window.location.href = pdfUrl;
					/*const blob = new Blob([data], { type: 'application/pdf' });

					// Utilizar o file-saver para forçar o download
					saveAs(blob, 'Cartao Digital');*/
					/*
					fetch(data.url)
					  .then(response => response.blob())
					  .then(blob => {
						saveAs(blob, data.file_name);
					  });*/
					  /*
					  if (native.download) {
				
							native.download( data.url, "Cartao_Digital.pdf" );
							
						} else {
							
							app.download( data.url, "Cartao_Digital.pdf" );
							
						}*/
						
					//var down_card = "/renault/content.php?card=" + data.folder_name;
					var down_card = app.url("/content.php?card=" + data.folder_name);
					
					if (native.download) {
				
						native.download( down_card, "Cartao Digital.pdf" );
						
					} else {
						
						app.download( down_card, "Cartao Digital.pdf" );
						
					}
				  
		        },
				error: function (jqXHR, textStatus, errorThrown) {
					// Manipular o erro da requisição
					console.log('Erro na requisição:', errorThrown);
					// Outras manipulações de erro aqui
				  }
		      });
		   });
   

		$('.page-content').addClass('fixed-header');
		
		galleryDom.empty();
		galleryDom.html('');
		btsDom.empty();
		btsDom.html('');
		let domHtml = '';
		let domBts = '';
		let hightlights = [];

		var refs = []
		var typeID = 0;
		
		loading.show();

		var paramsBts = Object.assign({}, this.feed, this.filterGallery, {q:'get_template_types'});

		app.request(paramsBts, ( res ) => {
			const template_types = res.template_types;
			if ( template_types.length > 0 )  {
				for(const types of template_types) {
					var name = types.name;
					var slug = name.toLowerCase().replace(/ /g, '-').replace(/[^\w-]+/g, '');

					refs.push({
		                id : types.id, 
		                slug : slug
		          	})

					domBts += `<div class="bt">`;
					domBts += `    <a href="#p=create-home&t=${slug}" page="create" class="link-interna" data-bt="back" data-title="criar <span>+</span>">`;
					domBts += `      <img src="${types.template_type_url}">`;
					domBts += `    </a>`;
					domBts += `</div>`;
				}

				btsDom.html(domBts);

				$('.bts-header a').removeClass('on');
				$('.bts-header a[href="#p=create-home&t=' + veiculo + '"]').addClass('on');
				$('.create-menu a[href="#p=create-para-atrair"]').attr('href', '#p=create-para-atrair&t=' + veiculo);
				$('.create-menu a[href="#p=create-para-apresentar"]').attr('href', '#p=create-para-apresentar&t=' + veiculo);
				$('.create-menu a[href="#p=create-para-vender"]').attr('href', '#p=create-para-vender&t=' + veiculo);
				$('.create-menu a[href="#p=create-cartao"]').attr('href', '#p=create-cartao&t=' + veiculo);
				$('.bt-top a[href="#p=create-todos"]').attr('href', '#p=create-todos&t=' + veiculo);
			}


			$.each( refs, function( key, value ) {
				if(value.slug == veiculo) {
					typeID = value.id;
				}
	        });

	        $('.link-interna.selected').attr('href', '#p=create-home&t=' + veiculo);

			if(tipo == 'create-home') {	
				var params = Object.assign({}, this.feed, this.filterGallery, {q:'templates', template_type: typeID, limit: 10 });

			} else if(tipo == 'create-todos') {
				var params = Object.assign({}, this.feed, this.filterGallery, {q:'templates', template_type: typeID, order: order });
				
			} else if(tipo == 'create-carro') {
				if(order) {
					var params = Object.assign({}, this.feed, this.filterGallery, {q:'templates', vehicle : modelo, template_type: typeID, order: order });
				} else {
					var params = Object.assign({}, this.feed, this.filterGallery, {q:'templates', vehicle : modelo, template_type: typeID, order: '2' });
				}

				var paramsVeiculo = Object.assign({}, this.feed, this.feedVehicles, {q:'vehicles'});
				var arrVeiculos = [];
				var indexVeiculo;
				var countVeiculo = 0;

				app.request(paramsVeiculo, ( res ) => {
					for(const vehicle of res.vehicles) {
						arrVeiculos.push(vehicle);
						if(vehicle.id == modelo){
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
					
					$('#car-prev').attr('href', '#p=create-para-atrair-carro&t='+ veiculo +'&v=' + prev.id);
					$('#car-next').attr('href', '#p=create-para-atrair-carro&t='+ veiculo +'&v=' + next.id);

				} );
			} else if(tipo == 'create-atrair') {
				if(order) {
					var params = Object.assign({}, this.feed, this.filterGallery, {q:'templates', group:'attract', template_type: typeID, order: order });
				} else {
					var params = Object.assign({}, this.feed, this.filterGallery, {q:'templates', group:'attract', template_type: typeID, order: '2' });
				}
			} else if(tipo == 'create-apresentar') {
				if(order) {
					var params = Object.assign({}, this.feed, this.filterGallery, {q:'templates', group:'present', template_type: typeID, order: order });
				} else {
					var params = Object.assign({}, this.feed, this.filterGallery, {q:'templates', group:'present', template_type: typeID, order: '2' });
				}
			} else if(tipo == 'create-vender') {
				if(order) {
					var params = Object.assign({}, this.feed, this.filterGallery, {q:'templates', group:'sell', template_type: typeID, order: order });
				} else {
					var params = Object.assign({}, this.feed, this.filterGallery, {q:'templates', group:'sell', template_type: typeID, order: '2' });
				}
			} else if(tipo == 'create-cartao') {
				var params = Object.assign({}, this.feed, {q:'get_digital_card'});
			}  


			var countDest = 0;
			var countDestOndemand = 0;
			var countTag = 0;

			if(tipo != 'create-cartao' && tipo != 'create') {
			
				app.request(params, ( res ) => {
					
					loading.hide();
				
					const templates = res.templates;
					//const tags = res.tags;
					// const versions = res.vehicleversions;
					var count = 0;
					$('#quantidade').html('');
					
					if ( templates.length > 0 )  {

						if(templates.length == 1) {
							$('#quantidade').html('1 template <br class="mobile"> encontrado');
						} else {
							$('#quantidade').html(templates.length + ' templates <br class="mobile"> encontrados');
						}
						
						for(const template of templates) {
							
						
							//const post = this.parsePostData(template);
							domHtml += `<div class="template">`;
							var img = template.thumb_content;

							domHtml += `<div class="ico-editar" data-id="${template.id}"></div>`;
						    domHtml += `<img class="img-editar" data-id="${template.id}"`;
							//hightlights.push( this.createHighlight( post ) );
							domHtml += `src="${img}" />`;
							domHtml += `</div>`;

							count++;


						}
						const dom = $(domHtml);
						
						
						galleryDom.append( dom );

						ui.adicionaSetasScrollHorizontal();

					} else {
						galleryDom.append( noPost.clone().show() );
					}

					callback();
				} );

			}
		});



		

	}

	updateVehicles(tipo) {
		const vehiclesDom = $('#veiculos').removeClass('selected');
		const loading = $('.loading-vehicles');

		vehiclesDom.empty();
		vehiclesDom.html('');
		
		loading.show();

		this.feed.vehicles = "";
		
		var vehicles = {};
		
		const params = Object.assign({}, this.feed, this.feedVehicles, {q:'vehicles'});
		
		const createVehicle = ( vehicle ) => {
			const button = $( `<div class="veiculo"><img data-vehicle="${vehicle.id}" class="veiculo-item link-interna" data-subinterna="true" data-bt="back" data-title="<span>#</span>para <strong>atrair</strong>" src="${vehicle.content}" /> <div class="nome">${vehicle.name}</div> <div class="seta"><img src="images/setadir.svg?v=2" /></div></div>` );
			
			button.click(() => {
				window.location.href = '#p=create-para-atrair-carro&t='+ tipo +'&v=' + vehicle.id;
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

		this.updateHelpMessage('criar');
		this.updateVehicles(app.urlParams.t);

		if (app.urlParams.p == 'create') {
			$('.header-interna').find('.interna-fechar').addClass('back-home');
			$('.header-interna').find('.interna-fechar').removeClass('back-create');
			$('.header-interna').find('.interna-fechar').removeClass('back-create-home');
		} else if (app.urlParams.p == 'create-home') {
			$('.header-interna').find('.interna-fechar').addClass('back-create-home');
			$('.header-interna').find('.interna-fechar').removeClass('back-create');
			$('.header-interna').find('.interna-fechar').removeClass('back-home');
		} else {
			$('.header-interna').find('.interna-fechar').addClass('back-create');
			$('.header-interna').find('.interna-fechar').removeClass('back-home');
			$('.header-interna').find('.interna-fechar').removeClass('back-create-home');
		}

		if (app.urlParams.p == 'create') {
			$('.header-interna').find('.header-title').html( 'criar <span>+</span>' );
			this.updateFilter('create', app.urlParams.t);
		} else if (app.urlParams.p == 'create-home') {
			$('.header-interna').find('.header-title').html( 'criar <span>+</span>' );
			this.updateFilter('create-home', app.urlParams.t);
		} else if (app.urlParams.p == 'create-todos') {
			$('.header-interna').find('.header-title').html( 'criar <span>+</span>' );
			this.updateFilter('create-todos', app.urlParams.t, '', app.urlParams.f);
		} else if(app.urlParams.p == 'create-para-atrair-carro') {
			$('.header-interna').find('.header-title').html( '<span>#</span>para <strong>atrair</strong>' );
			this.updateFilter('create-carro', app.urlParams.t, app.urlParams.v, app.urlParams.f);
		} else if(app.urlParams.p == 'create-para-atrair') {
			$('.header-interna').find('.header-title').html( '<span>#</span>para <strong>atrair</strong>' );
			this.updateFilter('create-atrair', app.urlParams.t, '', app.urlParams.f);
		} else if(app.urlParams.p == 'create-para-apresentar') {
			$('.header-interna').find('.header-title').html( '<span>#</span>para <strong>apresentar</strong>' );
			this.updateFilter('create-apresentar', app.urlParams.t, '', app.urlParams.f);
		} else if(app.urlParams.p == 'create-para-vender') {
			$('.header-interna').find('.header-title').html( '<span>#</span>para <strong>vender</strong>' );
			this.updateFilter('create-vender', app.urlParams.t, '', app.urlParams.f);
		} else if(app.urlParams.p == 'create-cartao') {
			$('.header-interna').find('.header-title').html( 'cartão <strong>digital</strong>' );
			this.updateFilter('create-cartao', app.urlParams.t);
		} 
	}

}

CreatePage.load = () => {
	
	if ( !app.pages.create ) {
		app.pages.create = new CreatePage();
	}
	
	app.pages.create.update();
	
}
