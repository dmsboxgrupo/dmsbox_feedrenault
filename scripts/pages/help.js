class HelpPage extends Feed {

	constructor() {
		super();
	}

	updateFilter(tipo, pergunta) {
		const faqDom = $('#help-menu');
		const contentDom = $('#help-content');

		faqDom.empty();
		faqDom.html('');
		contentDom.empty();
		contentDom.html('');
		let domHtml = '';

		if(tipo == 'home') {
			var params = Object.assign({}, this.feed, {q:'faqs'});
		} else if(tipo == 'duvida') {
			var params = Object.assign({}, this.feed, {q:'faqs', faq: pergunta});
		}

		app.request(params, ( res ) => {
			const groups = res.faq_groups;
			
			if ( groups.length > 0 )  {
				for(const group of groups) {

					if(tipo == 'home') {

						domHtml += `<h5 class="ico-titulo">`;
						domHtml += `	<div class="ico"><img src="${group.image}"></div>`;
						domHtml += 		group.title;
						domHtml += `</h5>`;
						const topics = group.faq_topics;

						if ( topics.length > 0 )  {
							for(const topic of topics) {

								domHtml += `<div class="accordion acc-help">`;
								domHtml += `    <div class="accordion-title">`;
								domHtml += 			topic.title;
								domHtml += `    </div>`;
								domHtml += `    <div class="accordion-content" style="display: none">`;
								domHtml += `      <div class="duvidas">`;

								const faqs = topic.faqs;

								if ( faqs.length > 0 )  {
									for(const faq of faqs) {
										domHtml += `      	<a href="#p=help-duvida&d=${faq.id}">`;
										domHtml += 				faq.question;
										domHtml += `      	</a>`;
									}
								}

								domHtml += `      </div>`;
								domHtml += `    </div>`;
								domHtml += `</div>`;

							}
						}
						

					} else if(tipo == 'duvida') {
						const topics = group.faq_topics;

						if ( topics.length > 0 )  {
							for(const topic of topics) {

								const faqs = topic.faqs;

								if ( faqs.length > 0 )  {
									for(const faq of faqs) {
										if(faq.id == pergunta) {
											domHtml += `<h5>`;
											domHtml += 		faq.question;
											domHtml += `</h5>`;
											domHtml += 	faq.answer;
											
											if(faq.content_type == 'image') {
												domHtml += `<div class="hld-img"><img src="${faq.content}"></div>`;
											}
											if(faq.content_type == 'video') {
												domHtml += `<div class="hld-vid">`;
												domHtml += `	<video controls>`;
												domHtml += `		<source src="${faq.content}" type="video/mp4">`;
												domHtml += `	</video>`;
												domHtml += `</div>`;
											}
										}
									}
								}

							}
						}


					}
				}

				contentDom.append( domHtml );
				faqDom.append( domHtml );
			}


			var accordion = $('.accordion-title:not(.done)');
		    function activate_accordions(){
		      accordion.on("click", function(ev) {
		        ev.stopImmediatePropagation();
		        var accordion_content = $(this).next(".accordion-content");
		        if (!accordion_content.hasClass("ativo")) {
		          accordion_content.slideDown(200).addClass("ativo");
		          $(this).addClass('ativo');
		        } else {
		          accordion_content.slideUp(200).removeClass("ativo");
		          $(this).removeClass('ativo');
		        }
		      }).addClass("done");
		    }
		    if(accordion.length){activate_accordions();}
		});
	}

	update() {
		$('.header-interna').find('.header-title').html( 'central de ajuda' );

		super.update();

		if (app.urlParams.p == 'help') {
			this.updateFilter('home');
		} else if (app.urlParams.p == 'help-duvida') {
			this.updateFilter('duvida', app.urlParams.d);
		}
	}

}

HelpPage.load = () => {
	
	if ( !app.pages.help ) {
		app.pages.help = new HelpPage();
	}
	
	app.pages.help.update();
	
}
