
const CALLBACK = () => {};

const tagsLib = {
	facebook: 'Facebook',
	instagram: 'Instagram',
	youtube: 'YouTube',
	twitter: 'Twitter',
	campanhas: { name: 'Campanhas', hashtag:'campanha' },
	universo: { name: 'Universo Renault', hashtag:'universo' },
	noticias: { name: 'Notícias', hashtag:'noticias' },
	comunicados: { name: 'Comunicados', hashtag:'comunicado' },
	cartarede: { name:'Carta Rede', hashtag:'cartarede', icon: 'noticias' },
	renault: { name:'Notícias da Renault', hashtag:'noticiarenault', icon: 'renault' },
	segmento: { name:'Notícias do segmento', hashtag:'noticiasegmento', icon: 'noticias' },
	emkt: 'Emkt',
	novidades: { name: 'Novidades', hashtag:'novidades' },
	videos: { name: 'Vídeos', hashtag:'video' },
	comparativos: { name: 'Comparativos', icon: 'comunicados', hashtag:'comunicado' },
	socials: { name:'Redes sociais', icon:'comunicados', hashtag:'social' },
	enxoval: { name:'Enxoval PDV', icon:'enxoval', hashtag:'enxoval' },
	comerciais: { name:'Comerciais', icon:'campanhas', hashtag:'campanha' },
	cards: { name:'Cards', hashtag:'card' },
	anuncios: { name:'Anúncios', icon:'noticias', hashtag:'anuncio' },
	folhetos: { name:'Folhetos', icon:'noticias', hashtag:'folheto' },
	enquete: { name:'Enquetes', hashtag:'enquetes', icon: 'enquete' }
}

app.global.muted = false;
app.global.autoplay = false;

const copyCommonAttributes = ( target, source ) => {
	
	if (source.jquery) {
		
		target
			.attr( 'content-id', source.attr('content-id') )
			.attr( 'content-message', source.attr('content-message') )
			.attr( 'content-url', source.attr('content-download') || source.attr('content-url') )
			.attr( 'content-download', source.attr('content-download') );
		
	} else {
		
		target
			.attr( 'content-id', source.id )
			.attr( 'content-message', source.share_message )
			.attr( 'content-url', source.download || source.share_link )
			.attr( 'content-download', source.download );
		
	}
	
}

class Feed extends Page {

	constructor() {

		super();

		this.feed = {};

		this.feedParams = {};
		this.feedStories = {};
		this.feedVehicles = {};
		this.feedTags = {};
		this.feedGallery = {};
		this.feedBanners = {};
		this.filterGallery = {};
		this.filterCampaigns = {};
		
		this.controllers = {};
		
		this.enabledSearch = true;

	}
	
	updateAutoPlay( dom = "#content video[data-autoplay='true']" ) {
		
		const self = this;
		
		$( dom ).each(function() {
			
			const video = $( this );
			const videoDom = this;
			
			if (app.global.autoplay) {
				
				const playing = video.isInViewport();
				
				if ( playing ) {
					
					$("#content video[data-playing='true']").each(function() {
						
						videoDom.pause();
						
					});
					
					videoDom.play();
					videoDom.muted = app.global.muted;

				} else {
					
					videoDom.pause();
					
				}
				
				videoDom.dataset.playing = playing;

			}
			
		});
	
	}
	
	parsePostLinks( data ) {
		
		if (data.link_gallery) {
			
			data.app_link = `#p=galeriawhats&id=${data.link_gallery}`;
			
		} else if (data.link_campaign) {
			
			data.app_link = `#p=campanhas&id=${data.link_campaign}`;
			
		} else {
			
			data.app_link = '';
			
		}
		
		data.url = data.app_link || data.link || '';
		
		data.href = data.app_link ? `href="${data.app_link}"` : data.link;
		
		if (data.link) {
			
			var m = data.link.match(/^(https?\:\/\/)?(www\.)?([^\/:?#]+).*/i);
			
			if (m && m[3]) data.link_domain = m[3].toUpperCase();
			
			data.link_domain = data.link_domain || '';
			
		} else {
			
			data.link_domain = '';
			
		}
		
		data.contains_link = data.type == 'campanha' || data.type == 'universo' || data.type == 'comunicados' || data.app_link || data.link;

	}

	parsePostData( data ) {
		
		this.parsePostLinks( data );
		
		const type = data.type;
		
		data.icon = data.type;
		
		let origin = '';

		if (type == 'twitter') origin = 'Twitter';
		else if (type == 'youtube') origin = 'YouTube';
		else if (type == 'instagram') origin = 'Instagram';
		else if (type == 'facebook') origin = 'Facebook';
		else if (type == 'campanha') origin = 'Campanha';
		else if (type == 'comunicados') origin = 'Comunicado';
		else if (type == 'enquete') origin = 'Enquetes';
		else if (type == 'universo') origin = 'Universo Renault';
		else if (type == 'noticias') {
			
			origin = 'Notícia';
			
			if ( data.label === 'Renault' ) {
				
				origin += ` da ${data.label}`;
				
				data.icon = 'renault';
				
			} else {
				
				origin += ` do ${data.label}`;
				
			}
			
		} else if (type == 'galeria') {
			
			data.icon = 'galeria';
			
			origin = 'Galeria WhatsApp';
			
			if(data.share_type) origin = data.share_type;
			
		}
		
		data.origin = origin;

		data.message = data.message.replace(/\[nome\]/g, app.user.short_name || app.user.name);
		if (data.share_message) data.share_message = data.share_message.replace(/\[nome\]/g, app.user.short_name || app.user.name);

		data.header_div  = `content-id="${data.id}" `;
		data.header_div += `content-message="${encodeURI(data.share_message)}" `;
		if (data.share_link) data.header_div += `content-url="${encodeURI(data.url || data.share_link)}" `;
		//if (type != 'campanha') 
		data.header_div += `content-download="${encodeURI(data.download)}" `;

		if( data.color != undefined ) {

			data.header_div += `data-background="${encodeURI(data.color)}"`;

		}
		
		if( data.tell_client ) {
			
			data.header_div += `tell_client="${encodeURI(data.tell_client)}" `;
			data.header_div += `tell_client_type="${encodeURI(data.tell_client_type)}" `;
			
		}
		
		if( data.pdf ) {
			
			data.header_div += `data-universo-pdf="${encodeURI(data.pdf)}" `;
			data.header_div += `data-universo-pdf-file="${encodeURI(data.pdf_download)}" `;
			
		}
		
		
		var noticia = data.filter_tags.includes('noticia');
		
		data.tags_div = `content-tags="${encodeURI(data.filter_tags.join(','))}" `;
		
		data.date_string = new Date( data.date ).toLocaleDateString('pt-BR', { 
			weekday: 'long', 
			year: 'numeric', 
			month: 'long', 
			day: 'numeric'
		});
		
		return data;
		
	}
	
	updateTagsLinks( filters = '' ) {
		
		if (filters) {
			
			this.setTagsFromSubsection( filters );
			
		} else {
		
			const buttons = $('#menu-subsecoes a');
			const self = this;
			
			buttons.each(function() {
				
				const button = $(this);
				const dataset = this.dataset;
				
				if (button.hasClass('active-nav')) {
					
					self.setTagsFromSubsection( dataset.filters )
					
				}
				
				button.click(function() {

					self.setTagsFromSubsection( dataset.filters )
					
				});
				
			});
			
		}
		
	}
	
	setTagsFromSubsection( tags ) {
		
		this.setTags( tags ? tags.split(',') : [] );
		
	}
	
	setTags( tags = [] ) {
		
		const pesquisaTags = $('.top-tags');
		
		let html = '';
		
		for(const tagData of tags) {
			
			let tag = tagData;
			
			if (typeof tag === 'string') {
				
				tag = tagsLib[ tag ] ? tagsLib[ tag ] : { name : tag };
				
			}
				
			tag = typeof tag === 'string' ? { name : tag } : tag;
			
			const name = tag.name;
			const nametag = name.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '').replace(/\s/g, '_');
			const icon = 'pesquisa-' + (tag.icon || nametag);
			const hashtag = tag.hashtag || nametag;
			
			html += `<label><input type="checkbox" name="${hashtag}" /><span><i class="${icon}"></i>${name}</span></label>`;
			
		}
		
		if (tags.length > 0) pesquisaTags.css('opacity', 1);
		else pesquisaTags.css('opacity', 0);
		
		pesquisaTags.html( html );
		
	}
	
	createHighlight( data ) {
		
		let html = 
			`<div class="destaque-item ${data.link ? "havelink" : ""}" data-background="${data.color}" ${data.header_div}
				${data.pdf ? `data-pdfurl=${data.pdf} data-duracao="10s"` : ""}
				>
			  <a class="destaque-linktopo">
				<div class="icone icone-${data.icon}"></div>
				<span>${data.origin}</span>
			  </a>`;

		let border = data.href ? '' : 'branco';

		if (data.content_type === 'video') {
			html += `<div class="destaque-controlevideo">
				<a class="destaque-play"></a>
				<a class="destaque-mute"></a>
			</div>`;
		}

		html += `<div class="destaque-inferioresq">`;
		
		if (data.content_type === 'image') {
			
			html += `<a class="acao-icone branco editar ${border}"></a>`;

		}

		if (data.favorite !== undefined) {
			
			html += `<a class="acao-icone branco favoritar ${ data.favorite ? 'ativo' : '' } ${border}"></a>`;

		}
		
		if (data.read_later !== undefined) {
			
			html += `<div class="acao-icone branco proxleitura ${ data.read_later ? 'ativo' : '' } ${border}"></div>`;
			
		}

		html += `</div>`;
		html += `<div class="destaque-inferior">`;
		
		if (data.download !== undefined && !data.href) {
			
			html += `<a ${data.header_div} class="acao-icone branco download">baixar</a>`;
			
		}
		
		if (data.share_link !== undefined) {
		
			html += `<a class="acao-icone branco compartilhar destaque-compartilhar">compartilhar</a>`;
			
		}
		
		html += `</div>`;

		html += `<div class="destaque-conteudo">`;

		if (!data.href) {
			html += `<div class="destaque-degradeinferior"></div>`;
		}
		
		html += `<div class="destaque-tela">`;
				
		if (data.content_type === 'image') {
			
			html += `<img src="${data.content}" class="media"/>`;
			
		} else if (data.content_type === 'video') {

			html += `<video class="media" muted><source src="${data.content}" type="video/mp4"></video>`;
			
		}

		if (data.href) {
			
			html += 
				`<div class="fundo-branco"></div>
					<div class="destaque-texto">
					 <div class="link-arraste">
					  Arraste para cima e leia a matéria completa
					</div>
					<p class="font-16" style="color: #000;">
					  ${data.message}
					</p>
				</div>`;
			
		} else {

			html += 
				`<div class="destaque-texto">
					<div class="destaque-texto-solo solo font-16">
					  ${data.message}
					</div>
				</div>`;

		}

		html += `</div></div></div>`;
		
		return $(html);
	}

	createCard( data ) {
			
		return $( `<div class="destaque-card ${ data.viewed ? '' : 'novo' }" ${data.header_div} style="background-image:url('${data.thumbnail}')"></div>` );

	}

	
	createGalleryDefault( data, style='' ) {

		let html = '<div class="galeria-item template template-m">';
		
		if (data.content_type == "image") {

			html += `<img src="${data.content}" style="${style}"/>`;
			
		} else {

			html += `<img src="${data.thumbnail}" style="${style}"/>`;

		}

		html += `	<div class="card-acao">`;
		if (data.read_later !== undefined) {
			html += `	<div class="acao-icone proxleitura ${ data.read_later ? 'ativo' : '' }"></div>`;
		}
		html += `	</div>`;

		const dom = $(html);

		dom.click(() => {
			
			const itens = dom.parent().find('.galeria-item');
			const index = itens.index( dom );
			
			ui.destaqueMostra(index, itens.length);
			
		});
		
		return dom;
		
	}

	createGallery( data, style='' ) {

		let html = '<div class="galeria-item template template-m">';
		
		if (data.content_type == "image") {

			html += `<img src="${data.content}" style="${style}"/>`;
			
		} else {

			html += `<img src="${data.thumbnail}" style="${style}"/>`;

		}


		html += `	<div class="card-acao">`;
		if (data.read_later !== undefined) {
			html += `	<div class="acao-icone proxleitura ${ data.read_later ? 'ativo' : '' }"></div>`;
		}
		html += `	</div>`;

		const dom = $(html);

		dom.click(() => {
			
			const itens = dom.parent().find('.galeria-item');
			const index = itens.index( dom );
			
			ui.destaqueMostra(index, itens.length);
			
		});
		
		return dom;
		
	}
	
	createCampaign( data ) {
		
		let html = `<div class="card-campanha" ${data.header_div} ${data.tags_div}>`;
			
		if (data.content) {
			
			html += `<a class="link-interna" data-bt="back" data-title="campanhas" href="#p=campanhas&id=${data.id}"><img class="imagem-100vw" src="${data.content}" /></a>`;
			
		}
			
		html += `<div class="campanha-barra">`;
		if ( data.download !== undefined ) {
			
			html += `<a ${data.header_div} href="${data.download}" target="_blank"><div class="botao baixar-material">Baixar materiais</div></a>`;
			html += `<div class="botao-ico"><div class="acao-icone proxleitura ${ data.read_later ? 'ativo' : '' }"></div></div>`;
			
		}
		html += `</div>`;

		html += `<div class="campanha-titulo">${data.title}</div>`;

		// if (data.open_tags.length > 0) {
			
		// 	html += `<div class="campanha-tags">`;
		// 	html += data.open_tags.join(` <i>•</i> `);
		// 	html += `</div>`;
			
		// }
			
		html += 
			`<div class="campanha-texto">
			  ${data.message}
			</div>`;
			
		html += `<div class="campanha-barra barra-left">`;

		
		if ( data.search_galeries !== undefined ) {
		
			//html += `<a href="#p=galeriawhats&search=${encodeURI(data.search_galeries)}" class="botao-cinza botao-galeria"></a>`;
			html += `<a href="#p=campanhas&id=${data.id}" class="botao-cinza botao-galeria"></a>`;
			
		}
		
		if ( data.pdf ) {
			
			html += `<a class="botao-cinza botao-manual" data-pdf="${data.pdf}" ${data.header_div} ></a>`;
			
		}
		
		/*
		if (data.favorite !== undefined) {
			
			html += `<a class="acao-icone favoritar ${ data.favorite ? 'ativo' : '' } push"></a>`;
			
		}
		
		if (data.notification !== undefined) {
			
			html += `<a class="acao-icone notificar ${ data.notification ? 'ativo' : '' }"></a>`;
			
		}
		*/
		
		html += `<div data-postid="${data.id}"></div>`;
		
		html += `</div></div>`
		
		const dom = $( html );
		
		dom.find('[data-postid]').click((e) => {
			
			ui.loadPage('content', '', this.createCampaignModal( data ));
			
		});
		
		dom.find('[data-pdf]').click((e) => {
			
			const itens = dom.parent().find('.card');
			const index = itens.index( dom );

			copyCommonAttributes( $('#destaque'), data );

			ui.destaqueMostra(index, itens.length);
			
		});
		/*
		dom.find('.baixar-material').click(() => {
			
			app.download( data.download );
			
		});
		*/
		return dom;
		
	}
	
	createFlyer( data ) {
		
		let html = `<div class="card card-folheto" ${data.header_div} data-pdf="${data.pdf}">
		  <div style="background-image: url( ${data.content} )" class="card-folheto-imagem"></div>
		  <div class="card-folheto-titulo">`;
		  
		html += data.message;
		
		html +=
			`<div class="card-acao">
			  <div class="acao-icone favoritar ${ data.favorite ? 'ativo' : '' }"></div>
			  <div class="acao-icone proxleitura ativo"></div>
			  <div class="acao-icone compartilhar"></div>
			</div>
		  </div>
		</div>`
		
		const dom = $(html);
		
		dom.click((e) => {
			
			const itens = dom.parent().find('.card');
			const index = itens.index( dom );

			copyCommonAttributes( $('#destaque'), data );

			ui.destaqueMostra(index, itens.length);
			
		});

		return dom;

	}

	createPost( data ) {
		let dia = data.date;
		let diaFormat = new Date(dia).toLocaleDateString();;
	
		let contains_link = false;

		let perfil = '';

		if (data.origin == 'Twitter') perfil = '@RenaultBrasil';
		else if (data.origin == 'YouTube') perfil = '@RenaultBrasil';
		else if (data.origin == 'Instagram') perfil = '@renaultbrasil';
		else if (data.origin == 'Facebook') perfil = '@Renault';
	
		let html = `<div class="card" ${data.header_div} ${data.tags_div}>`
			if (data.is_pin == '1') {
			  html += `<div class="card-titulo fixo">`
			}   else {
			  html += `<div class="card-titulo">`
			}
			
			html += `<div class="titulo-icone">
				  <div class="icone icone-${data.icon}"></div>
				</div>`;

		if(perfil != '') {
			html += `<div class="titulo-texto">
				   <b>${data.origin}</b>
				   <p>${perfil} <span>&nbsp;&nbsp;∙&nbsp;&nbsp;${diaFormat}</span></p>
				</div>`;

		} else {
			if(data.type === 'universo') {
				html += `<div class="titulo-texto" style="text-transform: none;">
				   <b>${data.title}</b>
				   <p>${data.origin} <span>&nbsp;&nbsp;∙&nbsp;&nbsp;${diaFormat}</span></p>
				</div>`;

			} else {
				html += `<div class="titulo-texto">
					   <b>${data.title}</b>
					   <p>${data.origin} <span>&nbsp;&nbsp;∙&nbsp;&nbsp;${diaFormat}</span></p>
					</div>`;
			}
		}

		html += `</div>
			  <div class="card-corpo" content-text>
				<p>${data.message}</p>
			  </div>`;
  
		if (data.iframe) {
			
			html +=
			`<div class="card-iframe">
			  <iframe width="560" height="315" scrolling="no" allowvr="yes" allow="xr-spatial-tracking" allowfullscreen="yes" src="${data.iframe}" allowfullscreen></iframe>
			</div>`;
			
		} else if (data.youtube) {
  
			if (data.content_type === 'image') {
				
				html += 
				`<div class="card-imagem">
					<a class="card-youtube-overlay" data-fancybox 
					  href="https://www.youtube-nocookie.com/embed/${data.youtube}">
					  <img src="${data.content}" />
					</a>
				  </div>`;
				  
			} else {

				html +=
				`<div class="card-iframe">
				  <iframe width="560" height="315" class="card-youtube" src="https://www.youtube.com/embed/${data.youtube}" allowfullscreen></iframe>
				</div>`;

			}

		} else if (data.type === 'enquete') {
			let date = data.date;
			let date_0 = date.substring(0,10);
			let date_1 = new Date(date_0);
			let date_2 = new Date();
		    let difference = date_1.getTime() - date_2.getTime();
		    let TotalDays = Math.abs(Math.ceil(difference / (1000 * 3600 * 24)));

		    if(data.vote == 0) {
				html += '<div class="card-enquete" data-enquete-id="' + data.id +'" data-voted="false">';
		    } else {
				html += '<div class="card-enquete" data-enquete-id="' + data.id +'" data-voted="true">';
		    }

		    let percertage = 0;
			data.survey_answers.forEach(function(resposta, i) {
				if(resposta.percentage > percertage) {
					percertage = resposta.percentage;
				}
			});
			data.survey_answers.forEach(function(resposta, i) {
				if(data.vote == resposta.id) {
					html += '  <div class="hld-item selected" id="resposta-'+ data.id +'-'+ resposta.id +'">';
				} else {
					html += '  <div class="hld-item" id="resposta-'+ data.id +'-'+ resposta.id +'">';
				}

				html += '    <label for="enquete-'+ data.id +'-'+ resposta.id +'">'+ resposta.text +'</label>';
				html += '    <input type="radio" id="enquete-'+ data.id +'-'+ resposta.id +'" name="enquete-'+ i +'" data-enquete-id="'+ data.id +'" value="'+ resposta.id +'">';
				html += '    <div class="info">';
				html += '      <div class="item">'+ resposta.text + '</div>';
				html += '      <div class="porcentagem">'+ resposta.percentage +'%</div>';
				html += '    </div>';
				if(resposta.percentage == percertage && data.is_closed) {
					html += '    <div class="bar win" style="width: '+ resposta.percentage +'%"></div>';
				} else {
					html += '    <div class="bar" style="width: '+ resposta.percentage +'%"></div>';
				}
				html += '  </div>';
			})

			html += '<div class="card-info">';
			if(data.total < 2) {
				html += '<span>' + data.total + ' voto </span>';
			} else {
				html += '<span>' + data.total + ' votos </span>';
			}

			if(TotalDays == 0){
				html += ' . hoje';
			} else if( TotalDays == 1) {
				html += ' . ' + TotalDays + ' dia atrás';
			} else {
				html += ' . ' + TotalDays + ' dias atrás';
			}

			html += '</div>';
		} else {

			if (data.content_type === 'image') {

				//console.log(data);
				
				if (data.contains_link) {
					
					contains_link = true;
					
					html += 
						`<div class="card-imagem">
						  <a>
							<img src="${data.content}" />
						  </a>
						</div>`;
					
				} else {
						
					html += 
						`<div class="card-imagem">
						  <a data-fancybox href="${data.content}">
							<img src="${data.content}" />
						  </a>
						</div>`;
						
				}
			
			} else if (data.content_type === 'video') {

				if(app.urlParams.p == 'leituras') {
					html += `<div class="card-imagem">
					  <a data-fancybox href="${data.content}">
						<img src="${data.thumbnail}" />
					  </a>
					</div>`;
				} else {
					contains_link = false;
					
					html += 
						`<div class="card-imagem">
						  <video data-autoplay="true" controls>
							<source src="${data.content}" type="video/mp4">
						  </video>
						</div>`;
				}
				
			} else {
			
				html += 
					`<div class="card-wlink">
					</div>`;
				
				contains_link = data.contains_link;
				
			}

			if(data.type == 'universo' && data.pdf){
				html +=
				  `<a class="card-box box-seta-dir acao-universo">
						<div>
							${data.title || data.link_domain}
							<b>Conheça todos os detalhes</b>
						</div>
				  </a>`;
			} 

			if (contains_link) {
				if(data.type != 'universo'){

					html +=
					  `<a class="card-box box-seta-dir open-feed">
							<div>
								${data.title || data.link_domain}
								<b>Conheça todos os detalhes</b>
							</div>
					  </a>`;
				}
				
			}
			
		}
  
			  //<div class="rateit" data-rateit-user-value="${data.user_score}" data-rateit-value="${data.score}"></div>  
			  if( data.type == 'universo') {
				html += `<div class="card-footer ${ data.pdf ? 'link-on' : 'link-off' }">`;
			  } else {
				html += `<div class="card-footer ${ contains_link ? 'link-on' : 'link-off' }">`;
			  } 
		html += `	<div class="card-acao">`;
		
		if( data.user_score > 0 ){
			
			html += `	  <div class="acao-icone curtir ativo"></div>`;
			html += `	  <div class="acao-texto"><span>`;
		
		} else {
			
			html += `	  <div class="acao-icone curtir"></div>`;
			html += `	  <div class="acao-texto ativo">`;
		
		}
		
		if(data.score_count > 0 ){
			
			html += `<span data-custom="score-count">${data.score_count}</span> ${ data.score_count <= 1 ? 'Curtida' : 'Curtidas' }</div>`;
			
		} else{
			
			html += `</span> Curtir</div>`;
			
		}
		
		
		if (data.read_later !== undefined) {
			
			html += `<div class="acao-icone proxleitura ${ data.read_later ? 'ativo' : '' }"></div><div class="acao-texto ${ data.read_later ? 'ativo' : '' }">${ data.read_later ? 'Salvo!' : 'Salvar' }</div>`;

		}
		html += `	</div>`;

		
		if (data.tell_client_type) {
		 	html += '<div class="acao-conte"><div class="ico"><img src="images/acaoconte.svg?v=2" /></div> conte para <br> seu cliente</div>';
 		}


		html += `	<div class="card-share">`;

		// if (data.content_type === 'image') {
		
		// 	html += `<div class="acao-icone editar"></div>`;

		// }
			
		// html += `<div class="acao-icone favoritar ${ data.favorite ? 'ativo' : '' }"></div>`;
			  
			html += `<div class="acao-icone download"></div>`;
		
		if (data.share_link !== undefined) {
			
			html += `<div class="acao-icone compartilhar"></div>`;

		}
		html += `</div></div></div>`;
		
		const dom = $(html);

		if ( data.ignoreEvents === true ) return dom;
		
		if (!data.youtube) {
			
			dom.find('.open-feed').click((e) => {

				if (data.type == 'campanha') {
					$('.header-interna').find('.header-title').html('campanhas');
      				$('.header-interna').addClass('on');
					/*ui.loadPage('comunicado', '', this.createCampaignModal( data, { backlink: '' } ));*/
					location.hash = data.app_link;
				
				} else if (data.type == 'universo') {
					$('.header-interna').find('.header-title').html('Universo Renault');
      				$('.header-interna').addClass('on');
					//ui.loadPage('comunicado', '', this.createHandoutModal( data, { backlink: '' } ));

					location.hash = data.app_link;
				
				} else if (data.type == 'comunicados') {
					$('.header-interna').find('.header-title').html('comunicados');
      				$('.header-interna').addClass('on');
					ui.loadPage('comunicado', '', this.createHandoutModal( data, { backlink: '' } ));
					
				} else if (data.app_link) {
					location.hash = data.app_link;
				
				} else if (data.link) {
					if (window.innerWidth > 1000 || !data.embed) {
						
						e.preventDefault();
						e.stopImmediatePropagation();
						
						signal.emit('open_url', { href: data.link });

					} else {
						console.log('Passou 5');
						ui.abreLink(data.link);

					}
					
				}
				
			});
			
		}

		dom.find('.rateit').rateit({
			step: .1,
			value: Math.round( data.score ),
			min: 0,
			max: 5,
			starwidth: 15,
			starheight: 15,
			readonly: true
		});
		
		const video = dom.find('video')[0];
		
		if (video) {
			
			 if (!navigator.userAgent.match(/iPhone|iPad|iPod/i)) {
			
				const player = new Plyr( video );
				player.pip = false;
				
				video.muted = app.global.muted;
			
				//player.on('play', () => { console.log("player video") });
			
				dom.find('[data-plyr="mute"]').click(function() {
					
					app.global.muted = !$(this).hasClass('plyr__control--pressed');
					
					$("#content video").each(function() {
						
						this.muted = app.global.muted;
						
					});
					
				});
			
				dom.find('[data-plyr="play"]').click(function() {
					
					app.global.autoplay = !app.global.autoplay;
					
				});
			}

		}
		
		return dom;
	
	}
	
	createCampaignModal( data, options = { backlink: "javascript:history.back()" } ) {
		
		const domGalleryID = `camp-gallery-${data.id}`;
		const domLoadingID = `camp-loading-${data.id}`;

		let dia = data.date;
		let diaFormat = new Date(dia).toLocaleDateString();
		
		let html =  `<h5 class="divisoria full clean">Campanhas</h5>`;
			html += `<div class="create-content hld-content-scroll proxy-scroll">`;
			//<-
		html += `<div class="secao campanha">`;
		if (data.content) {
			
			html += `<img class="imagem-full" src="${data.content}" />`
			
		}


		if (data.search_galeries) {
		html += `<h4>materiais associados</h4>`;
		html += `<div id="${domGalleryID}" class="grid">`;
		html += `</div>`;
		

			html += `<div id="${domLoadingID}" class="loading-line"><img class="loading" src="images/loading-spinner.svg?v=2"/></div>`;

		}


		html += 
		`<div class="card-spacer-ios"></div>
		<div class="card-spacer-bottom"></div>`


			html += `</div>`;

			//->
			html += `</div>`;

		if (data.search_galeries) {
			
			const params = Object.assign({}, this.feed, this.filterGallery, {q:'feed', filter:'galeria', open_tags:data.search_galeries});
			
			app.request(params, ( res ) => {
				
				const gallery = $(`#${domGalleryID}`);

				// Guarda conteudo original dos destaques
				$('#destaque').attr("id", "destaque-saved").appendTo("body");
				$("#destaque-popup").append("<div id='destaque' class='destaque-container'>");
				
				const destaqueDom = $('#destaque');
				
				destaqueDom.empty();
				
				$(`#${domLoadingID}`).hide();
				
				const feed = res.feed;
				
				for(const postData of feed) {

					const post = this.parsePostData( postData );

					gallery.append( this.createGallery( post, 'height:180px' ) );
					destaqueDom.append( this.createHighlight( post ) );
				
				}

				$(`.botao-galeria`).on('click', function() {
					
					$('.comunicado-fechar').click();
					
				})
				
			} );
			
		}

		return html;
		
	}

	createHandoutModal( data, options = { backlink: "javascript:history.back()" }  ) {
		
		let html = 
		`<div id="comunicado-${data.id}" ${data.header_div} class="comunicado-container">
		
		<div class="header header-fixed header-logo-center header-comunicado">
		  <div class="header-title"><img src='images/cabecalho-comunicado.svg?v=2' /><span>${data.origin}</span></div>
		  <a ${options.backlink ? 'href="' + options.backlink + '"' : ''} class="header-icon header-icon-1 comunicado-fechar"><img class="comunicado-fecharimg" src='images/cabecalho-retornar.svg?v=2'/></a>
		  <img class="header-icon header-icon-4" src='images/icorenaultnovo.svg?v=2'/>
		</div>  

		<div class="page-content scroll-vertical corpo-comunicado">
		<div class="comunicado-fechar-desktop"></div>

		  <img src="${data.content}" class="imagem-100vw" />`;
		  
		if (data.label) {
			
			html +=
			  `<div class="titulo-amarelo text-center font-18 bold">
				${data.label}
			  </div>`;

		}

		html +=
		  `
		  <p class="comunicado-titulo">
			${data.title}
		  </p>
		  <div class="comunicado-subtitulo">
			${data.message}
		  </div>
		  <hr class="center top-15 bottom-15 comunicado-hr" />
		  <div class="comunicado-conheca">
			Conheça todos os detalhes
		  </div>`;

		for(const topic of data.topics) {
			
			html +=
				`<div class="accordion-title">
					${topic.title}
				</div>
				<div class="accordion-content">
					<div class="top-20 bottom-20">${topic.text}</div>
				</div>`;
			
		}

		  
		html +=
		`<div class="comunicado-avaliacao">
			<div class="avaliacao">
			  <div class="rateit" data-rateit-user-value="${data.user_score}" data-rateit-value="${data.score}"></div>   
			  <div class="avaliacao-texto"><span data-custom="score-count">${data.score_count}</span> avaliaram</div>
			</div>
		</div>`;

		html += 
		`<div class="card-spacer-ios"></div>
		<div class="card-spacer-bottom"></div>`;

		html += `</div>`;

		html += `<div id="footer-comunicado">`;
		
		if (data.search_galeries !== undefined) {
			
			html += `<a href="#p=galeriawhats&search=${encodeURI(data.search_galeries)}" class="botao-cinza botao-galeria right-15"></a>`;
			
		}
		
		if ( data.pdf ) {
			
			html += `<a class="botao-cinza botao-manual right-15" data-pdf="${data.pdf}" ${data.header_div} ></a>`;
			
		}
		  
		if (data.search_news !== undefined) {
		  
			html += `<a href="#p=home&subsection=novidades&search=${encodeURI(data.search_news)}" class="botao-cinza botao-seta">Mais sobre ${encodeHTML(data.search_news)}</a>`
			
		}
		  
		// html += 
		// `<a class="acao-icone curtir ${ data.favorite ? 'ativo' : '' } push right-15"></a>        
		//   <a class="acao-icone proxleitura ${ data.read_later ? 'ativo' : '' }"></a>
		// </div>`;
		html += `	<div class="card-acao">`;
		
		if( data.user_score > 0 ){
			
			html += `	  <div class="acao-icone curtir ativo"></div>`;
			html += `	  <div class="acao-texto"><span>`;
		
		} else {
			
			html += `	  <div class="acao-icone curtir"></div>`;
			html += `	  <div class="acao-texto ativo">`;
		
		}
		
		if(data.score_count > 0 ){
			
			html += `<span data-custom="score-count">${data.score_count}</span> ${ data.score_count <= 1 ? 'Curtida' : 'Curtidas' }</div>`;
			
		} else{
			
			html += `</span> Curtir</div>`;
			
		}
		
		
		if (data.read_later !== undefined) {
			
			html += `<div class="acao-icone proxleitura ${ data.read_later ? 'ativo' : '' }"></div><div class="acao-texto ${ data.read_later ? 'ativo' : '' }">${ data.read_later ? 'Salvo!' : 'Salvar' }</div>`;

		}
		html += `	</div>`;
		
		html += `</div>`;

		setTimeout(function() {
			
			const dom = $(`#comunicado-${data.id}`)

			dom.find(`[data-pdf]`).click((e) => {

				setTimeout( () => {

					copyCommonAttributes( $('#destaque'), data );

				}, 100 );
				
			});
			
			dom.find(`.botao-galeria`).on('click', () => {

				$('.comunicado-fechar').click();

			});

		}, 500);

		return html;

	}
	
	setSearchVisible( visible ) {
		/*
		$('#on-search,#off-search').hide();
		
		if (visible) $('#on-search').show();
		else $('#off-search').show();
		*/
	}
	
	updateVehicles() {
		
		const vehiclesDom = $('#veiculos').removeClass('selected');
		const loading = $('.loading-vehicles');
		
		loading.show();

		this.feed.vehicles = "";
		
		var vehicles = {};
		
		const params = Object.assign({}, this.feed, this.feedVehicles, {q:'vehicles'});
		
		const createVehicle = ( vehicle ) => {
			const button = $( `<div class="veiculo"><img data-vehicle="${vehicle.id}" class="veiculo-item" src="${vehicle.content}" /> <div class="nome">${vehicle.name}</div> <div class="seta"><img src="images/setadir.svg?v=2" /></div></div>` );
			
			button.click(() => {
				
				button.toggleClass('ativo');
				
				vehicles[ vehicle.id ] = !vehicles[ vehicle.id ];
				
				var filter = [];
				
				for(var id in vehicles) {
					
					if ( vehicles[id] ) {
						
						filter.push( id );
						
					}
					
				}
				
				if (filter.length > 0) {
					
					vehiclesDom.addClass('selected');
					
				} else {
					
					vehiclesDom.removeClass('selected');
					
				}
				
				this.feed.vehicles = filter.join(',');
				
				this.updateFilter();

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
	
	updateTags( options = {} ) {
		
		const filter = options.filter || 'novidades';
		
		const tagsDom = $('#tags');
		const loading = $('.loading-tags');
		
		loading.show();
		
		var tags = {};

		this.feed.tags = "";
		
		const params = Object.assign({}, this.feed, this.feedTags, {q:'tags'});
		
		const createTag = ( tag ) => {
			
			const button = $( `<div class="categoria">${tag.name}</div>` );
			
			button.click(() => {
				
				button.toggleClass('selecionado');
				
				tags[ tag.id ] = !tags[ tag.id ];
				
				var filter = [];
				
				for(var id in tags) {
					
					if ( tags[id] ) {
						
						filter.push( id );
						
					}
					
				}
				
				this.feed.tags = filter.join(',');
				
				this.updateFilter();

			});
			
			return button;
			
		}
		
		app.request(params, ( res ) => {
			
			loading.hide();
			
			for(const tag of res.tags) {

				tagsDom.append( createTag( tag ) );
			
			}
			
			tagsDom.append(`<div class="margin-spacer"></div>`);
			
		} );
		
	}
	
	updateCampaigns( callback = CALLBACK ) {
		
		const destaque = $('#destaque');
		const campaignsDom = $('#campaigns');
		const loading = $('.loading-campaigns');
		const noPost = $('#no-post');
		
		campaignsDom.empty();
		
		loading.show();
		
		const params = Object.assign({}, this.feed, this.filterCampaigns, {q:'feed', filter:'campanha', search: this.searchValue});
		
		app.request(params, ( res ) => {
			
			loading.hide();
		
			const feed = res.feed;
			
			if ( feed.length > 0 )  {
				
				for(const post of feed) {
					
					this.parsePostData( post );
					
					campaignsDom.append( this.createCampaign( post ) );
					$('.botao-cinza.botao-manual').removeAttr('content-download');
					
				}
				
			} else {
				
				campaignsDom.append( noPost.clone().show() );
				
			}
			
			callback();
			
		} );
		
	}
	
	updateGallery( callback = CALLBACK ) {
		
		const destaque = $('#destaque');
		const galleryDom = $('#gallery');
		const loading = $('.loading-gallery');
		const noPost = $('#no-post');
		
		galleryDom.empty();
		
		loading.show();
		
		const params = Object.assign({}, this.feed, this.filterGallery, {q:'feed', filter:'galeria'});
		
		let domHtml = '';
		
		const hightlights = [];
		
		app.request(params, ( res ) => {
			
			loading.hide();
		
			const feed = res.feed;
			const tags = res.tags;
			
			if ( feed.length > 0 )  {
				
				for(const tag of tags) {
					
					const postsId = tag.posts;
					
					// adicionar filtro
					domHtml += `<div>`
					
					domHtml += `<h3>${tag.name}</h3>`;
					
					domHtml += `<div class="scroll-horizontal scroll-galeria">`;
					
					for(const postId of postsId) {
						
						const post = this.parsePostData( feed[postId] );
					
						domHtml += `<img data-postid="${postId}" data-play="${post.id}" ${post.header_div} ${post.tags_div} class="galeria-card" `;
						
						domHtml += `data-destaque="${hightlights.length}" `;

						hightlights.push( this.createHighlight( post ) );
							
						if ( post.content_type === "image" ) {
						
							domHtml += `src="${post.content}" />`;

						} else {

							domHtml += `src="${post.thumbnail}" />`;
							
						}
						
					}
					
					domHtml += `<div class="margin-spacer"></div>`;
					
					domHtml +=  `</div><hr /></div>`;

				}
				
				const dom = $(domHtml);
				
				dom.find('[data-destaque]').click(function(){
					
					destaque.empty();
					
					destaque.append( hightlights );
					
					ui.destaqueMostra(parseInt(this.dataset.destaque), hightlights.length);
					
				});
				
				galleryDom.append( dom );

				ui.adicionaSetasScrollHorizontal();
				
			} else {
				
				galleryDom.append( noPost.clone().show() );
				
			}
			
			callback();
			
		} );

	}
	
	updateStories() {

		const storiesDom = $('#stories');
		const storiesTitleDom = $('#stories-title');
		const destaqueDom = $('#destaque');
		const loading = $('.loading-stories');
		
		loading.show();
		
		destaqueDom.empty();
		
		const params = Object.assign({}, this.feed, this.feedStories, {q:'feed', highlights:true});
		
		app.request(params, ( res ) => {
			
			loading.hide();
			
			const feed = res.feed;

			if (feed.length > 0) {
				
				for(const post of feed) {
					
					this.parsePostData( post );
					
					if (post.content_type === 'image' || post.content_type == 'video') {
						
						const highlight = this.createHighlight( post );
						const card = this.createCard( post );

						destaqueDom.append( highlight );
						storiesDom.append( card );
						
					}

				}
				
				storiesDom.append(`<div class="margin-spacer"></div>`);
				
			} else {
				
				storiesDom.hide();
				storiesTitleDom.hide();
				
			}

		});
		
	}

	get searchValue() {
		
		if ($("#pesquisa-desktop-input").is(":visible")) {
			
			return $('#pesquisa-desktop-input').val();
			
		} else {
		
			return $('#pesquisa-input').val();
			
		}
		
		//return $('#pesquisa-input').val() || $('#pesquisa-desktop-input').val();
		
	}

	loadSubsession( options = {} ) {
		$('.page-content').removeClass('fixed-header');
		function alturaElemento(el) { el = $(el); return (el.is(":visible") ? el.outerHeight() : 0); }

		function alturaAntesMenu() {
		  return $("#subsecao").offset().top 
					- $("#content").offset().top
			- alturaElemento("#banners")
			- alturaElemento("#menu-subsecoes") + 15;
		}
					
		let filter = options.filter === "todos" ? "" : options.filter || 'novidades';
		//const filter = 'novidades';
		const type = options.type === "todos" ? "" : options.type || 'posts';
		
		var page = options.page || 0;

		var feedDom = $( options.target || '#subsecao .conteudo'  );
		const destaqueDom = $('#destaque');
		const loading = $( options.loading || '.loading-subsecao' );
		const noPost = $('#no-post');

		const mudandoSubsecao = (!options.page && !options.target && !options.nextPage);
		const scrollpos = $(".page-content").scrollTop();
		const ajustarScroll = mudandoSubsecao && (scrollpos > alturaAntesMenu())

		this.updateBanners( { filter } );
		
		if (options.clean !== false) {
			
			if (!mudandoSubsecao) {
				
				feedDom.empty();
				
			} else {
				
				feedDom.attr("class", "fadeout").css({opacity: 0});
				
				window.setTimeout(function() { $("#subsecao .fadeout").remove()}, 1000);
				
				feedDom.parent().append("<div class='conteudo' style='opacity: 0'></div>");
				feedDom = $('#subsecao .conteudo');
				
			}
			
		}

		if (options.nextPage) {
			
			page = parseInt($("#subsecao").attr("data-current-page")) + 1;
			
		}

		$("#subsecao").attr("data-current-page", page);
		$("#subsecao").attr("data-loaded", "false");

		if (mudandoSubsecao) {
			
			$("#subsecao").attr("data-reach-end", "false");
			$("#subsecao").append('<div class="loading-sub loading-line"><img class="loading" src="images/loading-spinner.svg?v=2"/></div>');
			
		} else if (options.page == 0) {
			
			loading.show();
			
		}
		
		let searchValue = this.searchValue;
		
		if( searchValue.indexOf( 'enquete:' ) === 0 ) {
			
			filter='enquete';
			searchValue = 'id:' + searchValue.split(':')[1];
			//searchValue = 'id:' + searchValue.split('key: "value", ')[1];
			
		}else {
			
			if( searchValue ) {
			
				filter='';
				
			}
			
		}

		let trend = options.trending;
		var params = '';

		if(trend != 'undefined' && options.trending == 'true'){
			params = Object.assign({}, this.feed, this.feedParams, {q:'feed', filter: 'galeria', type, page, content_type: 'image', search: searchValue, trending:''});
		} else {
			params = Object.assign({}, this.feed, this.feedParams, {q:'feed', filter, type, page, search: searchValue});
		}
			

		if (this.controllers.subsession) { 
		
			this.controllers.subsession.abort();
		
		}
  
		const loadPosts = ( feed ) => {
				
			for(const post of feed) {

				if($("#subsecao").attr("class") == 'subsecao readlater'){
					var arrPost = this.parsePostData( post );

					// if(post.message.length > 130){
					// 	post.message = post.message.substring(0, 150) + '...';
					// }
					if(post.title.length > 25){
						post.title = post.title.substring(0, 25) + '...';
					}
					
					const postDOM = this.createPost( this.parsePostData( post ) );
					
					postDOM.click((e) => {
					
						const id = post.type == 'enquete' ? 'enquete:' : 'id:';
						
						document.location = "#p=pesquisa&q=" + id + post.id;
						
						e.preventDefault();
						e.stopImmediatePropagation();
						
					});
					
					feedDom.append( postDOM );
				} else {
					feedDom.append( this.createPost( this.parsePostData( post ) ) );
				}
			
			}
	  
		}

		
		const loadGalleries = ( feed ) => {
			
			const gallery = $(`<div class="galeria">`);
			
			feedDom.append( gallery );
			
			destaqueDom.empty();
			
			for(const post of feed) {

				this.parsePostData( post );

				gallery.append( this.createGalleryDefault( post ) );
				destaqueDom.append( this.createHighlight( post ) );
			
			}

		}
		
		const loadGalleriesSalvos = ( feed ) => {
			
			const gallery = $(`<div class="galeria">`);
			
			//feedDom.append( gallery );
			
			destaqueDom.empty();
			
			for(const post of feed) {

				const data = this.parsePostData( post );
				data.ignoreEvents = true;

				//gallery.append( this.createGallery( post ) );
				const postDOM = this.createPost( data )
				postDOM.addClass( 'destaque-card' );
				
				feedDom.append( postDOM );
				destaqueDom.append( this.createHighlight( post ) );
			
			}

		}
		
		const loadFlyers = ( feed ) => {
			
			const flyers = $(`<div class="flyers">`);
			
			feedDom.append( flyers );
			
			destaqueDom.empty();
			
			for(const post of feed) {
				
				this.parsePostData( post );

				flyers.append( this.createFlyer( post ) );
				destaqueDom.append( this.createHighlight( post ) );

			}
			
		}
		
		this.controllers.subsession = app.request(params, ( res ) => {
			
			if (mudandoSubsecao) {
				
				$("#subsecao .loading-sub").remove();
				
			}
				
			loading.hide();
			
			const feed = res.feed;
			
			if (feed.length > 0) {
				
				if ( options.trending == 'true' ) loadGalleries( feed );
				else if ( type == 'posts' || type == '' ) loadPosts( feed );
				else if ( type == 'gallery' ) loadGalleries( feed );
				else if ( type == 'galeria' ) loadGalleriesSalvos( feed );
				else if ( type == 'flyers' ) loadFlyers( feed );
				
				this.updateAutoPlay();

			} else {
				
				$("#subsecao").attr("data-reach-end", "true");

				if (options.clean !== false) {
					
					feedDom.append( noPost.clone().show() );
					
				}
				
			}

			if (ajustarScroll) $(".page-content").animate({scrollTop: alturaAntesMenu()});
			
			feedDom.css({opacity: 1});
			
			$("#subsecao").css({minHeight: 
				Math.max(alturaElemento("#subsecao .conteudo"), (window.innerHeight - 170)) - 
				alturaElemento("#header") - alturaElemento("#footer-menu")});
				
			$("#subsecao").attr("data-loaded", "true");


		});
		
	}

	updateSubsessions() {
		
		const self = this;
		const buttons = $('#menu-subsecoes a');
		
		buttons.each(function() {
			
			const button = $(this);
			
			if (button.hasClass('active-nav')) {
				
				self.loadSubsession( {
					filter: button.attr('data-filter'), 
					type: button.attr('data-type') 
				} );
				
			}
			
			button.click(function() {
				
				buttons.removeClass('active-nav');
				
				button.addClass('active-nav');
				
				self.loadSubsession( {
					filter: button.attr('data-filter'), 
					type: button.attr('data-type')
				} );
				
			});
			
		});

		$(".page-content").scroll(function () {
			
			// evitar rechamadas
			if (buttons[0] == $('#menu-subsecoes a')[0]) {
				
				//- 10 = desired pixel distance from the bottom of the page while scrolling)
				if ($("#subsecao").attr("data-loaded") == "true" &&
					$("#subsecao").attr("data-reach-end") == "false" &&
					$(".page-content").scrollTop() > $("#subsecao .conteudo").height() - window.innerHeight) {
					$("#subsecao").attr("data-loaded", "false");
					
					self.loadSubsession( {
						filter: $("#menu-subsecoes .active-nav").attr('data-filter'), 
						type: $("#menu-subsecoes .active-nav").attr('data-type'),
						nextPage: true,
						clean: false
					});
					
				}
				
			}
			
		});
	
	}
	
	updateBanners( options = {} ) {
		
		const filter = options.filter || 'novidades';
		const bannersDom = $('#banners');
		
		if (!bannersDom.length) return;

		const loading = $('.loading-banner');
		
		let html = `<div class="carrossel carrossel-home">
						<div class="single-slider-full slider-full owl-carousel">`;
		
		bannersDom.css({maxHeight: 0, marginBottom: 0}).find(".carrossel").addClass("fadeout").css({opacity: 0});
		
		setTimeout(function() {
			
			$('#banners .fadeout').remove();
			
		}, 1000);
		
		const params = Object.assign({ filter }, this.feed, this.feedBanners, {q:'banners'});
		
		app.request(params, ( res ) => {
			
			const banners = res.banners;

			if (!banners.length) return;

			for(const banner of banners) {
				
				this.parsePostLinks( banner );
				
				html +=
					`<a ${banner.href}>
						<div class="titulo">
						  <b>${banner.name}</b>
						  ${banner.text}</sup>
						</div>
						<div class="bg owl-lazy" data-src="${banner.content}"></div>
					</a>`;
			
			}
			
			html += `</div></div>`;
			
			bannersDom.append( html ).slider();

			window.setTimeout(function() {
				
				bannersDom.css({maxHeight: $("#banners .carrossel").outerHeight() + 60,
					marginBottom: window.innerWidth > 1000 ? -30: 6});
					
			}, 100);
			
			if ( options.callback ) options.callback();

		});
		
		$('.page-content').removeClass('fixed-header');

	}

	updateHelpMessage( name ) {
		
		const helpMessage = $('#help-mensagem');
		
		const property = 'readed_help_' + name;
		
		helpMessage.find('.botao').click(() => {
			
			app.setMeta( property, true );
			
		});
		
		if ( app.user.metadata[property] ) {
			
			helpMessage.hide();
			
		}
		
	}

	updateFilter() {
		
		
		
	}

	update() {
		
		if (this.enabledSearch) {
			
			$('.header-pesquisa').fadeIn(300);
			
		} else {
			
			$('.header-pesquisa').fadeOut(300);
			
		}

		signal.on("highlight", e => {
			
			var pdf = e.content.dataset.pdfurl;
			if (pdf) {

				var message = e.content.getAttribute("content-message");

				$(e.content).find(".destaque-degradeinferior, .destaque-conteudo").remove();
				$(e.content).append(`<div class="destaque-degradeinferior"></div>
					<div class="destaque-conteudo pdf-container">
						<div class="loader-panel"><div></div><span></span></div>
						${message ? 
						`<div class="destaque-tela">
							<div class="fundo-branco"></div>
							<div class="destaque-texto">
								<div class="link-arraste">
									Arraste para ver mais
								</div>
								<p class="font-16" style="color: #000;">
									${decodeURI(message)}
								</p>
							</div>
						</div>` : ''}
					</div>`);
				$(e.content).find(".pdf-container").on("scroll", e => {
					console.log("SCroll");
					$(e.target).find(".destaque-tela").addClass("esconde");
				});
				ui.carregaPDF(pdf, $(e.content).find(".pdf-container"));
				
			}
			
		});

	}

}
