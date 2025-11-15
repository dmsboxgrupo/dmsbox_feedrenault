/* TEMPLATES */
const CAMPANHA_TEMPLATE =   '<div class="post" type="feed" reference="campanha-permanente">' +
                            '    <div class="midia">'+
                            '       <a href="#" class="bt-changeview" data-target="campanha" data-sub="${this.id}">'+
                            '           <div class="img" style="background-image: url(\'${this.content}\');"></div>'+
                            '       </a>'+
                            '    </div>'+
                            '    <div class="infos">'+
                            '        <div class="aux">'+
                            '            <div class="title">'+
                            '                <div class="aux">'+
                            '                   <div class="ico"><img src="assets/images/ico_campanha-permanente.svg"></div>'+
                            '                   <div class="txt">'+
                            '                       <h3>${this.title}</h3>'+
                            '                       <p>campanha permanente</p>'+
                            '                   </div>'+
                            '                </div>'+
                            '            </div>'+
                            '            <div class="aux-text">'+
                            '                <div class="text">${this.message}</div>'+
                            '            </div>'+
                            '        </div>'+
                            '    </div>'+
                            '</div>';

const MATERIAL_TEMPLATE =   '<div class="post" type="material">'+
                            '    <div class="midia">'+
                            '        <a href="#" class="bt-modal" data-target="galeria" data-medium="story" data-sub="${this.id}">'+
                            '            <div class="img" style="background-image: url(\'${this.content}\');"></div>'+
                            '        </a>'+
                            '    </div>'+
                            '</div>';

let page = 0;
let activeFrame = '';
let oldFrame = '';
let waitCampanhas = false;
let campanhabaseLoaded = false;
let arrTags = [];
let waitFrame = '';

class CAMPANHAS {
    constructor(){
        console.log('PASSOU CAMPANHAS')
        makeLoading(page, 'campanha');

        // const params = { q:'feed', filter:'campanha', page:page };
        const params = { q:'material_library_tags', type:'campaign' };
        app.request(params, false, null, ( res ) => {

            if(res.error != null) {
            }else{
                let htmlCode = '';
                let loopTags = res.material_library_tags;

                loopTags.forEach(function(item) { 
                    let name = item.name;       
                    let id = item.id;       
                    let slugURL = util.slugfy(item.name);
                    let slugIMG = util.slugfy(item.name, '_');
                    let campanhaURL = 'campanha-' + slugURL;

                    arrTags.push({campanhaURL, id});
                    window.hash.push({internal: campanhaURL, holder: 'campanhas', hash: campanhaURL});

                    htmlCode += '<div class="item">';   
                    htmlCode += '    <a href="#" class="bt-changeview" data-target="' + campanhaURL + '" data-group="campanhas">';    
                    htmlCode += '        <div class="bull">';   
                    htmlCode += '            <div class="hld">';    
                    htmlCode += '                <div class="ico" style="mask-image: url(\'' + item.thumbnail + '\');"></div>';   
                    htmlCode += '            </div>';   
                    htmlCode += '        </div>';   
                    htmlCode += '        <div class="txt">' + item.name + '</div>'; 
                    htmlCode += '    </a>'; 
                    htmlCode += '</div>';   
                })

                $('.menu-slider .slider').html(htmlCode);

                if( $('.menu-slider').length > 0 ){
                    $('.menu-slider .slider').slick({
                      infinite: false,
                      arrows: true,
                      autoplay: false,
                      dots: false,
                      slidesToShow: 7,
                      slidesToScroll: 1,
                      nextArrow: $('.menu-slider .nav-slider .arrow.right'),
                      prevArrow: $('.menu-slider .nav-slider .arrow.left'),
                      responsive: [
                        {
                          breakpoint: 1420,
                          settings: {
                            slidesToShow: 6,
                          }
                        },
                        {
                          breakpoint: 1290,
                          settings: {
                            slidesToShow: 5,
                          }
                        },
                        {
                          breakpoint: 1240,
                          settings: {
                            slidesToShow: 6,
                            arrows: false,
                          }
                        },
                        {
                          breakpoint: 1060,
                          settings: {
                            slidesToShow: 5,
                            arrows: false,
                          }
                        },
                        {
                          breakpoint: 992,
                          settings: {
                            slidesToShow: 6,
                            arrows: false,
                          }
                        },
                        {
                          breakpoint: 768,
                          settings: {
                            slidesToShow: 5,
                            arrows: false,
                          }
                        },
                        {
                          breakpoint: 664,
                          settings: {
                            slidesToShow: 4,
                            arrows: false,
                          }
                        },
                        {
                          breakpoint: 566,
                          settings: {
                            slidesToShow: 3,
                            arrows: false,
                          }
                        },
                        {
                          breakpoint: 420,
                          settings: {
                            slidesToShow: 2,
                            arrows: false,
                          }
                        },
                      ],
                    });
                }

                campanhabaseLoaded = true;

                if(waitFrame != '') {
                    this.init(waitFrame);
                }

                
            }

        })


    }

    init(frame, filtrado = false){
        page = 0;
        $('html, body').animate({
            scrollTop: 0
        }, 0);

        if(campanhabaseLoaded) {
            $('.bt-changeview').each(function(){
                if($(this).attr('data-target') == frame){
                    $(this).parent().addClass('selected');
                }
            })

            activeFrame = frame;

            $('.bt-dropdown[data-target="campanhas"]').parent().parent().addClass('selected');
            $('.nav-mobile [data-target="campanhas"]').parent().addClass('selected');
            $('.bt-dropdown[data-target="campanhas"]').parent().parent().find('.sub-content').slideDown();

            if(frame == 'campanha') {
                /* INTERNA CAMPANHA */
                const queryString = window.location.hash;
                const urlParams = new URLSearchParams(queryString);
                const campanhaID = urlParams.get('id');

                if($(window).width() < 992){
                    $('.menu-slider').hide();
                }

                const paramsCampanha = { q:'feed', filter:'campanha', id: campanhaID };
                let gallery = '';

                // $('main').attr('class', 'view-campanha-'+ campanhaID);

                app.request(paramsCampanha, false, null, ( res ) => {
                    if(res.error != null) {
                    }else{
                        let htmlCode = '';
                        let loopCampanha = res.feed;
                        

                        htmlCode += '<div class="area-mobile">';
                        htmlCode += '    <div class="txt">campanhas</div>';
                        htmlCode += '    <div class="bt-fechar">';
                        htmlCode += '        <a href="#" class="bt-historyback"></a>';
                        htmlCode += '    </div>';
                        htmlCode += '</div>';
                        htmlCode += '<div class="interna-post">';

                        loopCampanha.forEach(function(item) {
                            htmlCode += util.factory(CAMPANHA_TEMPLATE, item);
                            gallery = item.search_galeries;
                        })
                        htmlCode += '</div>';

                        const paramsGaleria = { q:'feed', filter:'galeria', open_tags: gallery };

                        app.request(paramsGaleria, false, null, ( res ) => {
                            if(res.error != null) {
                            }else{
                                let loopGaleria = res.feed;

                                if(loopGaleria.length > 0) {
                                    htmlCode += '<div class="grid">';
                                    htmlCode += '    <div class="title">';
                                    htmlCode += '        <h2>materiais associados</h2>';
                                    htmlCode += '    </div>';
                                    htmlCode += '    <div class="hld-grid">';
                                    loopGaleria.forEach(function(item) {   
                                        htmlCode += util.factory(MATERIAL_TEMPLATE, item);
                                    })
                                    htmlCode += '    </div>';
                                    htmlCode += '</div>';
                                }
                            }
                            

                            $('#internal-holder').html(htmlCode);
                        })


                    }
                })
            } else if(frame == 'campanhas') {
                /* HOME CAMPANHAS */
                if($(window).width() < 992){
                    $('.menu-slider').show();
                }
                $('#internal-holder').html('');
                let htmlCode = '';
                makeLoading(page, 'campanha');

                let params = { q:'feed', filter:'campanha', page:page };

                if(filtro != '') {
                    params.vehicles = filtro;
                }

                app.request(params, false, null, ( res ) => {
                    makePost(page, res, frame);
                })
            } else {
                if($(window).width() < 992){
                    $('.menu-slider').show();
                }
                $('#internal-holder').html('');
                let htmlCode = '';
                makeLoading(page, 'campanha');

                let tags = '';
                let ind = 0;
                for(let i=0; i <arrTags.length; i++){
                    if(arrTags[i]['campanhaURL'] == frame) {
                        tags = arrTags[i]['id'];
                        ind = i;
                    }
                }

                let slides = $('.menu-slider .slider').slick('slickGetOption', 'slidesToShow');
                let totalSlides = $('.menu-slider .slider .item').length;
                let pagesSlides = totalSlides - slides;

                if(ind > pagesSlides) {
                    ind = pagesSlides;
                }
                $('.menu-slider .slider').slick('slickGoTo', ind);


                let params = { q:'feed', filter:'campanha', group:tags, page:page };
                if(filtro != '') {
                    params.vehicles = filtro;
                }

                if(oldFrame != frame || filtrado) {
                    app.request(params, false, null, ( res ) => {
                        makePost(page, res, frame);
                    })
                }
                // $('main').attr('class', 'view-'+frame);
                oldFrame = frame;
            }
        } else { 
            waitFrame = frame; 
        }
    }
}
const makeCAMPANHAS = new CAMPANHAS();
window.campanhas = makeCAMPANHAS;


$(document).on('scroll', function(event){
  event.preventDefault();

  if(activeFrame == 'campanhas' || activeFrame == 'campanha-materiais-de-pdv' || activeFrame == 'campanha-redes-sociais' || activeFrame == 'campanha-digital' || activeFrame == 'campanha-posvenda') {
      if( (($(window).scrollTop() + $(window).height()) + 100) >= $(document).height() ){
        if(!waitCampanhas) {
            waitCampanhas = true;
            page++;
            let params = '';

            makeLoading(page, 'campanha');

            let tags = '';
            for(let i=0; i <arrTags.length; i++){
                if(arrTags[i]['campanhaURL'] == activeFrame) {  
                    tags = arrTags[i]['id'];
                }
            }

            params = { q:'feed', filter:'campanha', page:page };

            if(activeFrame != 'campanhas'){
                params.tags = tags;
            } 

            if(filtro != '') {
                params.vehicles = filtro;
            }

            app.request(params, false, null, ( res ) => {
                let loopCampanha = res.feed;
                let htmlCode = '';

                makePost(page, res, activeFrame);
                
                if(res.feed){
                    waitCampanhas = false;
                }
            })
        }
      }
  }
});

function checkText() {
    $('.aux-text').each(function(){
        var thisHeight = $(this).height() - 30;
        var textHeight = $(this).find('.text').height();

        if(thisHeight < textHeight){
            $(this).addClass('txt-expande');
            if($(window).width() < 992){
                $(this).find('.bt-expand-text').text('ler mais...');
            }
        } else {
            $(this).removeClass('txt-expande');
        }
    })
}