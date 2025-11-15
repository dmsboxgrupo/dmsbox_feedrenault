let loadMenu = '';

let queryString = ''
let urlParams = '';
let buscaTERM = '';
let buscaTAGS = '';

let waitBusca = false;
let page = 0;

class BUSCA {
    constructor(){
        $(window).on('resize', function() {
            reloadMenu();
        });
    }

    init(frame){
        queryString = window.location.hash;
        urlParams = new URLSearchParams(queryString);
        buscaTERM = urlParams.get('term');
        buscaTAGS = urlParams.get('tags');

        $('.pesquisado .posts').html('');

        $('.bt-changeview').each(function(){
            if($(this).attr('data-target') == frame){
                $(this).parent().addClass('selected');
            }
        })

        $('#internal-holder').html('');
        page = 0;
        waitBusca = false;
        $(window).scrollTop(0); 

        // $('main').attr('class', 'view-perguntas-frequentes');

        if(frame == 'busca' && buscaTERM == null) {
            $('#internal-holder').html('');
            let htmlCode = '';
            let htmlModal = '';
            let params = { q:'feed', filter:'compartilhar', order:'downloaded', limit:10 };
            
            app.request(params, false, null, ( res ) => {
                if(res.error != null) {
                } else {
                    let loopBaixados = res.feed;

                    htmlCode += '<div class="grid">';
                    htmlCode += '   <div class="title">';
                    htmlCode += '       <h2>mais baixados</h2>';
                    htmlCode += '   </div>';
                    htmlCode += '   <div class="hld-grid">';
            
                    loopBaixados.forEach(function(item, index) {
                        htmlCode += '   <div class="post" type="material" reference="story">';
                        htmlCode += '       <div class="midia">';
                        htmlCode += '           <a href="#" class="bt-modal" data-target="galeria" data-medium="story" data-sub="' + (index + 5) + '" selectable="true">';
                        if(item.content_type == 'video'){
                            htmlCode += '           <div class="img" style="background-image: url(\'' + item.thumbnail + '\');"></div>';
                        } else {
                            htmlCode += '           <div class="img" style="background-image: url(\'' + item.content + '\');"></div>';
                        }
                        htmlCode += '           </a>';
                        htmlCode += '       </div>';
                        htmlCode += '   </div>';

                        let saved = false;

                        if (item.read_later !== undefined) {
                            if(item.read_later){
                                saved = true;
                            }
                        }

                        if(item.content_type == 'video'){
                            htmlModal += '<div class="aux-slide" data-slideID="' + item.id + '" data-slideSaved="' + saved + '" data-slideDownload="' + item.share_link + '" data-slideTitle="' + encodeURIComponent(item.share_message.trim()) + '"  data-slideText=""  data-slideLink="' + encodeURIComponent(item.share_link) + '"><div class="slide" style="background-image:url(\'' + item.thumbnail + '\');"></div></div>';
                        } else {
                            htmlModal += '<div class="aux-slide" data-slideID="' + item.id + '" data-slideSaved="' + saved + '" data-slideDownload="' + item.share_link + '" data-slideTitle="' + encodeURIComponent(item.share_message.trim()) + '"  data-slideText=""  data-slideLink="' + encodeURIComponent(item.share_link) + '"><div class="slide" style="background-image:url(\'' + item.content + '\');"></div></div>';
                        }
                    })
                    htmlCode += '   </div>';
                    htmlCode += '</div>';

                    $('#internal-holder').html(htmlCode);
                    $('#galeria .slider').html(htmlModal);
                }
            });

        }
        
        if(frame == 'busca' && buscaTERM != null) {
            $('.pesquisado .text').text(buscaTERM);

            makeLoading(page, 'post');
            let params = '';

            if(buscaTAGS != null) {
                let tags = buscaTAGS.split(',');

                for (var i = 0; i < tags.length; i++) {
                    $('[value="' + tags[i] + '"]').attr('checked', true);
                }

                $('input[name="filtros"]').each(function(){
                    if($(this).is(':checked')){
                        $('.pesquisado .posts').append('<div class="post">'+ $(this).parent().find('label').text() +' <a class="remove-filter" data-target="'+ $(this).val() +'"></a></div>')
                        tags += $(this).val() + ',';
                    }
                });

                params = { q:'feed', search:buscaTERM, filter: buscaTAGS, page: page };
            } else {
                params = { q:'feed', search:buscaTERM, page: page };
            }

            app.request(params, false, null, ( res ) => {
                makePost(page, res, 'busca');
            })
        }
    }
}
const makeBUSCA = new BUSCA();
window.busca = makeBUSCA;

$(document).on('scroll', function(event){
  event.preventDefault();
  
  if(activeArea == 'busca') {
      if( (($(window).scrollTop() + $(window).height()) + 100) >= $(document).height() ){
        if(!waitBusca) {
            waitBusca = true;
            page++;

            makeLoading(page, 'post');
            let params = '';

            if(buscaTAGS != null) {
                let tags = buscaTAGS.split(' ').join(',');

                params = { q:'feed', search:buscaTERM, filter_tags: tags, page: page };
            } else {
                params = { q:'feed', search:buscaTERM, page: page };
            }

            app.request(params, false, null, ( res ) => {
                makePost(page, res, 'busca');
                if(res.feed){
                    waitBusca = false;
                }
            })
        }
      }
  }
});


$('#form-busca').on('submit', function(event){
    event.preventDefault();
    $('.pesquisado .text').text($(this).find('[name="busca"]').val());

    makeHash('busca', 'internal', $(this).find('[name="busca"]').val()); 
})

function applyFilter(){
    let selectedPosts = [];
    let tags = '';
    let term = '';

    $('input[name="filtros"]').each(function(){
        if($(this).is(':checked')){
            $('.pesquisado .posts').append('<div class="post">'+ $(this).parent().find('label').text() +' <a class="remove-filter" data-target="'+ $(this).val() +'"></a></div>')
            tags += $(this).val() + ',';
        }
    });

    $('.panel-filter').removeClass('open');

    if($('#form-busca [name="busca"]').val() == '') {
        term = $('.pesquisado .text').text();
    } else {
        term = $('#form-busca [name="busca"]').val();
    }

    makeHash('busca', 'internal', term, tags.substring(0, tags.length - 1)); 
}

function removeFilter(target){
    $('input[value="' + target + '"]').prop('checked', false);
    $('.remove-filter[data-target="' + target + '"]').parent().remove();

    let tags = '';
    let term = '';

    $('input[name="filtros"]').each(function(){
        if($(this).is(':checked')){
            $('.pesquisado .posts').append('<div class="post">'+ $(this).parent().find('label').text() +' <a class="remove-filter" data-target="'+ $(this).val() +'"></a></div>')
            tags += $(this).val() + ',';
        }
    });
    
    if($('#form-busca [name="busca"]').val() == '') {
        term = $('.pesquisado .text').text();
    } else {
        term = $('#form-busca [name="busca"]').val();
    }

    makeHash('busca', 'internal', term, tags.substring(0, tags.length - 1));
}

function setSearchFilter(type){
    if(type == 'all'){
        $('input[name="filtros"]').each(function(){
            $(this).prop('checked', true);
        });
        $('.panel-filter .bts').addClass('selected');
    } else {
        $('input[name="filtros"]').each(function(){
            $(this).prop('checked', false);
        });
        $('.panel-filter .bts').removeClass('selected');
    }
}

function checkSearchFilter(changed, type){
    let selectedPosts = [];

    if(type == 'change') {
        let wait = setTimeout(function(){
          clearTimeout(wait);
            $('input[name="filtros"]').each(function(){
                if($(this).is(':checked')){
                    selectedPosts.push($(this).val());
                }
            });

            if(selectedPosts.length > 0) {
                $('.panel-filter .bts').addClass('selected');
            } else {
                $('.panel-filter .bts').removeClass('selected');
            }

        }, 100);
    }

}