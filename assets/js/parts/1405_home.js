/* TEMPLATES */
const HIGHLIGHT_TEMPLATE  = '<div class="item">'+
                            '    <a href="#" class="bt-modal" data-target="destaque" data-medium="destaque" data-sub="${this.count}">'+
                            '        <div class="bull">'+
                            '            <div class="hld" style="background-image: url(\'${this.thumbnail}\');"></div>'+
                            '            <div class="aro active"></div>'+
                            '        </div>'+
                            '    </a>'+
                            '</div>';


let arrOptions = [];
let arrSelectedOptions = [];

let maxSections = 4;
let minSections = 3;

let waitPost = false;
let page = 0;
let filter = 'novidades';
let oldFrame = '';

class HOME {
    constructor(){
        let dragCount = 0;
        let dragging = '';
        let draggingPos = '';

        $('#userOptions').val(keeper.get('categories'));

        $('input[name="conteudos"]').each(function(){
            arrOptions.push([$(this).val(), $(this).next('label').text()]);
        });

        arrSelectedOptions = $('#userOptions').val().split(',');
        loadMenu('initial');

        const params = { q:'feed', highlights:'true' };
        app.request(params, false, null, ( res ) => {
            if(res.error != null) {
            }else{
                let htmlSliderCode = '';
                let htmlModalCode = '';
                let loop = res.feed;
                let count = 0;

                if(loop.length > 0) {     
                    loop.forEach(function(item) {
                        item.count = count;
                        htmlSliderCode += util.factory(HIGHLIGHT_TEMPLATE, item);

                        if(item.pdf){
                            htmlModalCode +=    '<div class="slide">'+
                                                '    <div class="midia midia-pdf">'+
                                                '       <div class="aux-scroll" id="pdf-' + item.id + '" data-pdf="' + item.pdf + '">'+
                                                '           <div class="loader-panel"><div></div><span></span></div>'+
                                                '       </div>'+
                                                '    </div>'+
                                                '    <div class="infos">'+
                                                '        <div class="content">'+
                                                '            <div class="bt-info">'+
                                                '                <a href="#" class="bt-toggle-info" data-target="aux-content"><div class="ico"></div></a>'+
                                                '            </div>'+
                                                '            <h3>' + item.title + '</h3>'+
                                                '            <div class="aux-content">'+
                                                '                ' + item.message_highlight +
                                                '            </div>'+
                                                '        </div>'+
                                                '        <div class="bts">'+
                                                '            <div class="bt">'+
                                                '                <a href="#" class="bt-baixar" data-target="' + item.share_link + '">baixar</a>'+
                                                '            </div>'+
                                                '            <div class="bt">'+
                                                '                <a href="#" class="bt-compartilhar" data-target="' + item.id + '" data-share-title="' + encodeURIComponent(item.title) + '" data-share-text="' + encodeURIComponent(item.message) + '" data-share-link="' + encodeURIComponent(item.share_link) + '">compartilhar</a>'+
                                                '            </div>'+
                                                '        </div>'+
                                                '    </div>'+
                                                '    <div class="actions">'+
                                                '        <div class="bt-salvar black">';
                                                if (item.read_later !== undefined) {
                                                    if(item.read_later){
                            htmlModalCode +=    '            <a href="#" class="bt-save active" data-target="' + item.id + '">';
                                                    } else {
                            htmlModalCode +=    '            <a href="#" class="bt-save" data-target="' + item.id + '">';
                                                    }
                                                }
                            htmlModalCode +=    '                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" version="1.1" viewBox="0 0 24 24">'+
                                                '                  <g class="off">'+
                                                '                  <path d="M12,19.7l-6.8,4.3-1.1-.5V2.3c0-1.5.8-2.3,2.3-2.3h11.4c1.5,0,2.3.8,2.3,2.3v21.3l-1.1.4-6.8-4.3h0ZM18.7,22.3V2.5c0-.8-.3-1.2-1.1-1.2H6.5c-.8,0-1.1.4-1.1,1.2v19.7l6.7-4.2,6.7,4.2ZM14.9,14.1c-1.3-.6-2-1-3-1.4-.9.5-1.7.8-3,1.4l-.6-.4c.2-1.1.4-2.1.6-3.2-.8-.7-1.3-1.2-2.3-2.3l.2-.8c1.4-.2,2.2-.4,3.2-.5.5-1,.9-1.8,1.6-3.1h.5c.7,1.2,1.1,2.1,1.6,3.1,1,0,1.7.2,3.1.5l.2.8c-1,1-1.5,1.6-2.3,2.3.3,1.1.5,2.1.6,3.2l-.6.4s.2,0,.2,0ZM9.9,12.4c1-.5,1.5-.8,2.1-1.1.6.4,1.3.8,2,1.1-.2-1-.3-1.6-.4-2.1.8-.8,1.2-1.2,1.7-1.7-1.1-.2-1.7-.2-2.3-.3-.3-.7-.6-1.4-1-2.1-.3.6-.5,1.1-1,2.1-.7,0-1.2.2-2.4.3.5.6,1.1,1.1,1.7,1.7-.2.6-.2,1.1-.4,2.2"/>'+
                                                '                  </g>'+
                                                '                  <g class="on">'+
                                                '                    <path d="M12.1,6.1c-.3.6-.5,1.1-1,2.1-.7,0-1.2.2-2.4.3.5.6,1.1,1.1,1.7,1.7-.2.6-.2,1.1-.4,2.2,1-.5,1.5-.8,2.1-1.1.6.4,1.3.8,2,1.1-.2-1-.3-1.6-.4-2.1.8-.8,1.2-1.2,1.7-1.7-1.1-.2-1.7-.2-2.3-.3-.3-.7-.6-1.4-1-2.1"/>'+
                                                '                    <path d="M17.7,0H6.3C4.8,0,4,.8,4,2.3v21.3l1.1.5,6.8-4.3,6.8,4.3,1.1-.4V2.3c0-1.5-.8-2.3-2.3-2.3M14.9,10.6c.3,1.1.5,2.1.6,3.2l-.6.4c-1.3-.6-2-1-3-1.4-.9.5-1.7.8-3,1.4l-.6-.4c.2-1.1.4-2.1.6-3.2-.8-.7-1.3-1.2-2.3-2.3l.2-.8c1.4-.2,2.2-.4,3.2-.5.5-1,.9-1.8,1.6-3.1h.5c.7,1.2,1.1,2.1,1.6,3.1,1,0,1.7.2,3.1.5l.2.8c-1,1-1.5,1.6-2.3,2.3"/>'+
                                                '                  </g>'+
                                                '                </svg>'+
                                                '            </a>'+
                                                '        </div>'+
                                                '    </div>'+
                                                '</div>';



                        } else {
                            let thumbUrl = item.thumbnail;
                            let imgOriginal = thumbUrl.replace('q=highlight-thumbnail&', '');

                            htmlModalCode  +=   '<div class="slide">'+
                                                '    <div class="midia" style="background-image:url(\'' + imgOriginal + '\');"></div>'+
                                                '    <div class="infos">'+
                                                '        <div class="content">'+
                                                '            <div class="bt-info">'+
                                                '                <a href="#" class="bt-toggle-info" data-target="aux-content"><div class="ico"></div></a>'+
                                                '            </div>'+
                                                '            <h3>' + item.title + '</h3>'+
                                                '            <div class="aux-content">'+
                                                '                ' + item.message_highlight +
                                                '            </div>'+
                                                '        </div>'+
                                                '        <div class="bts">'+
                                                '            <div class="bt">'+
                                                '                <a href="#" class="bt-baixar" data-target="' + item.share_link + '">baixar</a>'+
                                                '            </div>'+
                                                '            <div class="bt">'+
                                                '                <a href="#" class="bt-compartilhar" data-target="' + item.id + '" data-share-title="' + encodeURIComponent(item.title) + '" data-share-text="' + encodeURIComponent(item.message) + '" data-share-link="' + encodeURIComponent(item.share_link) + '">compartilhar</a>'+
                                                '            </div>'+
                                                '        </div>'+
                                                '    </div>'+
                                                '    <div class="actions">'+
                                                '        <div class="bt-salvar black">';
                                                if (item.read_later !== undefined) {
                                                    if(item.read_later){
                            htmlModalCode +=    '            <a href="#" class="bt-save active" data-target="' + item.id + '">';
                                                    } else {
                            htmlModalCode +=    '            <a href="#" class="bt-save" data-target="' + item.id + '">';
                                                    }
                                                }
                            htmlModalCode +=    '                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" version="1.1" viewBox="0 0 24 24">'+
                                                '                  <g class="off">'+
                                                '                  <path d="M12,19.7l-6.8,4.3-1.1-.5V2.3c0-1.5.8-2.3,2.3-2.3h11.4c1.5,0,2.3.8,2.3,2.3v21.3l-1.1.4-6.8-4.3h0ZM18.7,22.3V2.5c0-.8-.3-1.2-1.1-1.2H6.5c-.8,0-1.1.4-1.1,1.2v19.7l6.7-4.2,6.7,4.2ZM14.9,14.1c-1.3-.6-2-1-3-1.4-.9.5-1.7.8-3,1.4l-.6-.4c.2-1.1.4-2.1.6-3.2-.8-.7-1.3-1.2-2.3-2.3l.2-.8c1.4-.2,2.2-.4,3.2-.5.5-1,.9-1.8,1.6-3.1h.5c.7,1.2,1.1,2.1,1.6,3.1,1,0,1.7.2,3.1.5l.2.8c-1,1-1.5,1.6-2.3,2.3.3,1.1.5,2.1.6,3.2l-.6.4s.2,0,.2,0ZM9.9,12.4c1-.5,1.5-.8,2.1-1.1.6.4,1.3.8,2,1.1-.2-1-.3-1.6-.4-2.1.8-.8,1.2-1.2,1.7-1.7-1.1-.2-1.7-.2-2.3-.3-.3-.7-.6-1.4-1-2.1-.3.6-.5,1.1-1,2.1-.7,0-1.2.2-2.4.3.5.6,1.1,1.1,1.7,1.7-.2.6-.2,1.1-.4,2.2"/>'+
                                                '                  </g>'+
                                                '                  <g class="on">'+
                                                '                    <path d="M12.1,6.1c-.3.6-.5,1.1-1,2.1-.7,0-1.2.2-2.4.3.5.6,1.1,1.1,1.7,1.7-.2.6-.2,1.1-.4,2.2,1-.5,1.5-.8,2.1-1.1.6.4,1.3.8,2,1.1-.2-1-.3-1.6-.4-2.1.8-.8,1.2-1.2,1.7-1.7-1.1-.2-1.7-.2-2.3-.3-.3-.7-.6-1.4-1-2.1"/>'+
                                                '                    <path d="M17.7,0H6.3C4.8,0,4,.8,4,2.3v21.3l1.1.5,6.8-4.3,6.8,4.3,1.1-.4V2.3c0-1.5-.8-2.3-2.3-2.3M14.9,10.6c.3,1.1.5,2.1.6,3.2l-.6.4c-1.3-.6-2-1-3-1.4-.9.5-1.7.8-3,1.4l-.6-.4c.2-1.1.4-2.1.6-3.2-.8-.7-1.3-1.2-2.3-2.3l.2-.8c1.4-.2,2.2-.4,3.2-.5.5-1,.9-1.8,1.6-3.1h.5c.7,1.2,1.1,2.1,1.6,3.1,1,0,1.7.2,3.1.5l.2.8c-1,1-1.5,1.6-2.3,2.3"/>'+
                                                '                  </g>'+
                                                '                </svg>'+
                                                '            </a>'+
                                                '        </div>'+
                                                '    </div>'+
                                                '</div>';
                        }

                        count++;
                    })

                    $('.menu-slider .slider').html(htmlSliderCode);
                    $('#destaque .slider').html(htmlModalCode);

                    $('.menu-slider.destaque').show();
                    
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
            }
        })

        $(window).on('resize', function() {
            checkText();
            reloadMenu();
        });
    }

    init(frame){
        
        if(oldFrame != frame){
            util.lockDown();
            $('#menu-dashboard a').removeClass('selected');
            $('#menu-dashboard a[data-target="'+ activeArea +'"]').addClass('selected');

            $('#internal-holder').html('');
            page = 0;
            waitPost = false;
            $(window).scrollTop(0); 

            makeLoading(page, 'post');

            if(activeArea == 'dashboard') {
                filter = 'novidades';
            }      
            if(activeArea == 'pos-vendas') {
                filter = 'careservices';
            }
            if(activeArea == 'noticias') {
                filter = 'noticias';
            }
            if(activeArea == 'redes-sociais') {
                filter = 'redes';
            }
            if(activeArea == 'universo-renault') {
                filter = 'universo';
            }
            if(activeArea == 'comunicados') {
                filter = 'comunicados';
            }
            if(activeArea == 'renault-academy') {
                filter = 'academy';
            }


            const params = { q:'feed', filter:filter, type:'posts', page:page };
            app.request(params, false, null, ( res ) => {
                makePost(page, res, 'dashboard');
                util.release();
                // $('main').attr('class', 'view-dashboard');
            })

            reloadMenu();
            oldFrame = frame;
        }
    }
}
const makeHOME = new HOME();
window.home = makeHOME;

$(document).on('scroll', function(event){
  event.preventDefault();
  
  if(activeArea == 'dashboard' || activeArea == 'pos-vendas' || activeArea == 'noticias' || activeArea == 'redes-sociais' || activeArea == 'universo-renault' || activeArea == 'universo-renault' || activeArea == 'comunicados' || activeArea == 'renault-academy') {
      if( (($(window).scrollTop() + $(window).height()) + 100) >= $(document).height() ){
        if(!waitPost) {
            waitPost = true;
            page++;

            makeLoading(page, 'post');
            const params = { q:'feed', filter:filter, type:'posts', page:page };

            app.request(params, false, null, ( res ) => {
                makePost(page, res, 'dashboard');
                if(res.feed){
                    waitPost = false;
                }
            })
        }
      }
  }
});

function loadMenu(type) {
    $('#menu-dashboard .aux .aux-scroll').html('');
    $('#menu-dashboard .aux .aux-scroll').append('<a href="#" class="bt-changeview selected" data-target="dashboard">ver tudo</a>');

    for (var i = 0; i < arrSelectedOptions.length; i++) {
        for (var j = 0; j < arrOptions.length; j++) {
            if(arrOptions[j][0] == arrSelectedOptions[i]){
                $('#menu-dashboard .aux .aux-scroll').append(
                    '<a href="#" class="bt-changeview" data-target="' + arrOptions[j][0] + '">' + arrOptions[j][1] + '</a>'
                )

                if(type != 'reload'){
                    $('.panel-config .grid #sortable').append(
                        '<li class="ui-sortable-handle" data-item="' + arrOptions[j][0] + '">'+
                        '    <div class="bt-drag">'+
                        '        <a href="#" class="bt-excluir" data-target="excluir-drag" data-check="' + arrOptions[j][0] + '"><div class="ico"></div></a>'+
                        '    </div>'+
                             arrOptions[j][1] + 
                        '</li>'
                    );
                }

                $('[value="' + arrOptions[j][0] + '"]').attr('checked', true);
            }
        }
    }

    $('#menu-dashboard .aux .aux-scroll').append('<div class="dash"></div>');

    $( "#sortable" ).sortable({
        update: function(event, ui){
        
            arrSelectedOptions = [];
            $('.panel-config .grid #sortable li').each(function(){
                if($(this).attr('data-item') != null){
                    arrSelectedOptions.push($(this).attr('data-item'));
                }
            });
        }
    });



    if(arrSelectedOptions.length == maxSections){
        $('input[name="conteudos"]').each(function(){
            if(!$(this).is(':checked')){
                $(this).parent().addClass('disabled');
            } else {
                $(this).parent().removeClass('disabled');
            }
        });
    }

    if(type == 'reload'){
        makeHash('dashboard', 'internal');
    }

    if($(window).width() < 992) {
        $('#menu-dashboard').after(`
            <div class="banners">
                <div class="slider"></div>
            </div>
        `)
    }

}

function reloadMenu() {
    let targetOBJ = $('#menu-dashboard .bt-changeview.selected');

    if(targetOBJ.length > 0){
        let position = targetOBJ.position();
        let width = targetOBJ.outerWidth() - 32;

        $('#menu-dashboard .dash').css({'left': Math.floor(position.left + 16) + 'px', 'width': width + 'px'});
    }
}

/* PAINEL CONFIG */
function salvaConfigForm(){
    $('#userOptions').val(arrSelectedOptions);
    
    let params = { q:'update_user', nomail:true, categories:arrSelectedOptions };
    app.request(params, false, null, ( res ) => {
        $('.panel-config').removeClass('open');

        const keeperParams = { categories:arrSelectedOptions };
        keeper.set(keeperParams, STORAGE_TYPE, 10);

        loadMenu('reload');
    });

}

function checkConfigForm(changed, type){
    let selectedOptions = [];
    let errors = 0;

    if(type == 'change') {
        let wait = setTimeout(function(){
          clearTimeout(wait);
            $('input[name="conteudos"]:checked').each(function(){
                selectedOptions.push($(this).val());
            });

            if($('[value="' + changed + '"]').is(':checked')){
                if($.inArray(changed, arrSelectedOptions) === -1){
                    arrSelectedOptions.push(changed);

                    let aplicou = false;
                    let index = '';
                    for (var i = 0; i < arrOptions.length; i++) {
                        if(arrOptions[i][0] == changed){
                            index = i;
                        }
                    }

                    $('.panel-config .grid #sortable').append(
                        '<li class="ui-sortable-handle" data-item="' + arrOptions[index][0] + '">'+
                        '    <div class="bt-drag">'+
                        '        <a href="#" class="bt-excluir" data-target="excluir-drag" data-check="' + arrOptions[index][0] + '"><div class="ico"></div></a>'+
                        '    </div>'+
                             arrOptions[index][1] + 
                        '</li>'
                    );

                    arrSelectedOptions = [];
                    $('.panel-config .grid #sortable li').each(function(){
                        if($(this).attr('data-item') != null){
                            arrSelectedOptions.push($(this).attr('data-item'));
                        }
                    });
                }
            } else {
                if($.inArray(changed, arrSelectedOptions) !== -1){
                    $('[data-item="' + changed + '"]').remove();

                    arrSelectedOptions = $.grep(arrSelectedOptions, function(value) {
                      return value != changed;
                    });

                }
            }


            if(selectedOptions.length < minSections){
                errors++;
            }
            if(selectedOptions.length == maxSections){
                $('input[name="conteudos"]').each(function(){
                    if(!$(this).is(':checked')){
                        $(this).parent().addClass('disabled');
                    } else {
                        $(this).parent().removeClass('disabled');
                    }
                });
            } else {
                $('input[name="conteudos"]').each(function(){
                    $(this).parent().removeClass('disabled');
                })
            }
            if(selectedOptions.length > maxSections){
                errors++;
            }

            if(errors == 0) {
                $('.panel-config .bt-aplicar').removeClass('disabled');
            } else {
                $('.panel-config .bt-aplicar').addClass('disabled');
            }

        }, 100);
    } else {
        $('[value="' + changed + '"]').attr('checked', false);
        $('[data-item="' + changed + '"]').remove();

        arrSelectedOptions = $.grep(arrSelectedOptions, function(value) {
          return value != changed;
        });

        if(arrSelectedOptions.length < minSections){
            errors++;
        }
        if(arrSelectedOptions.length == maxSections){
            $('input[name="conteudos"]').each(function(){
                if(!$(this).is(':checked')){
                    $(this).parent().addClass('disabled');
                } else {
                    $(this).parent().removeClass('disabled');
                }
            });
        } else {
            $('input[name="conteudos"]').each(function(){
                $(this).parent().removeClass('disabled');
            })
        }
        if(arrSelectedOptions.length > maxSections){
            errors++;
        }

        if(errors == 0) {
            $('.panel-config .bt-aplicar').removeClass('disabled');
        } else {
            $('.panel-config .bt-aplicar').addClass('disabled');
        }
    }
}