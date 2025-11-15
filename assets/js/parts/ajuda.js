let loadMenu = '';
let oldSub = '';

class AJUDA {
    constructor(){
        $(window).on('resize', function() {
            reloadMenu();
        });
        $('aside.left').attr('data-anchor', 'perfil');

        $('#form-busca-ajuda').on('submit', function(event){
            event.preventDefault();

            let arrGroups = [];
            let arrTopics = [];
            let arrIDs = [];
            let busca = $('#form-busca-ajuda input[name="busca"]').val();

            $('.faq-area').each(function(){
                $(this).show();
            });
            $('.sub-area').each(function(){
                $(this).show();
            });
            $('.item').each(function(){
                $(this).show();
            });
            if(busca != ''){
                const params = { q:'faqs', query:busca };
                
                app.request(params, false, null, ( res ) => {

                    const groups = res.faq_groups;
                    if ( groups.length > 0 )  {
                        for(const group of groups) {
                            arrGroups.push(group.id);

                            const topics = group.faq_topics;
                            if ( topics.length > 0 )  {
                                for(const topic of topics) {
                                    arrTopics.push(topic.id);

                                    const faqs = topic.faqs;
                                    if(faqs){
                                        if ( faqs.length > 0 )  {
                                            for(const faq of faqs) {
                                                arrIDs.push(faq.id);
                                            }
                                        }
                                    } 
                                }
                            }
                        }
                    } 

                    $('.faq-area').each(function(){
                        let areaID = Number($(this).attr('data-group'));

                        if(!arrGroups.includes(areaID)){
                            $(this).hide();
                        }
                    }) 

                    $('.sub-area').each(function(){
                        let subareaID = Number($(this).attr('data-topic'));

                        if(!arrTopics.includes(subareaID)){
                            $(this).hide();
                        }
                    })  

                    $('.item').each(function(){
                        let itemID = Number($(this).attr('data-anchor').replace('item-',''));

                        if(!arrIDs.includes(itemID)){
                            $(this).hide();
                        }
                    })  

                });
            }

        })
    }

    init(frame){
        const queryString = window.location.hash;
        const urlParams = new URLSearchParams(queryString);
        const ajudaSUB = urlParams.get('sub');
        const ajudaID = urlParams.get('id');

        $('.bt-changeview').each(function(){
            if($(this).attr('data-target') == frame){
                $(this).parent().addClass('selected');
            }
        })
        if(oldSub != ajudaSUB) {
            /* AJUDA */
            if(frame == 'central-de-ajuda') {
                if(loadMenu != 'ajuda'){
                    $('#menu-ajuda .aux .aux-scroll').html(`
                        <a href="#" class="bt-changeview" data-target="central-de-ajuda" data-medium="tutoriais-em-video">tutoriais em vídeo</a>
                        <a href="#" class="bt-changeview" data-target="central-de-ajuda" data-medium="perguntas-frequentes">perguntas frequentes</a>

                        <div class="dash"></div>
                    `)
                    loadMenu = 'ajuda';
                    $('#menu-ajuda').show();
                }
            }


            /* AJUDA */
            // if(frame == 'central-de-ajuda' && ajudaSUB == null) {
            //     $('main').attr('class', 'view-central-de-ajuda');
            // }
            // if(frame == 'central-de-ajuda' && ajudaSUB == 'tutoriais-em-video') {
            //     $('main').attr('class', 'view-tutoriais-em-video');
            // }

            if(frame == 'central-de-ajuda' && ajudaSUB == 'tutoriais-em-video' || frame == 'central-de-ajuda' && ajudaSUB == null) {
                $('.busca-ajuda').slideUp();

                $('#internal-holder').html('');

                let htmlCode = '';
                const params = { q:'tutorials' };
                
                app.request(params, false, null, ( res ) => {
                    let looptutorial = res.tutorials;

                    looptutorial.forEach(function(item) {
                        htmlCode += '<div class="list">';
                        htmlCode += '    <div class="title">';
                        htmlCode += '        <h2>' + item.name + '</h2>';
                        htmlCode += '    </div>';
                        htmlCode += '    <div class="hld-video">';
                        htmlCode += '       <video controls><source src="' + item.file + '" type="video/mp4"></video>';
                        htmlCode += '    </div>';
                        htmlCode += '</div>';
                    })

                    htmlCode += '<div class="list">';
                    htmlCode += '     <div class="infos">';
                    htmlCode += '         <h2>não encontrou o que procurava?</h2>';
                    htmlCode += '         <p>entre em contato pelo e-mail:</p>';
                    htmlCode += '         <div class="bt-round">';
                    htmlCode += '             <a href="mailto:atendimento.renault@dmsbox.com.br">atendimento.renault@dmsbox.com.br</a>';
                    htmlCode += '         </div>';
                    htmlCode += '     </div>';
                    htmlCode += '</div>';

                    $('#internal-holder').html(htmlCode);
                });
                
            }

            if(frame == 'central-de-ajuda' && ajudaSUB == 'perguntas-frequentes') {
                $('.busca-ajuda').slideDown();
                $('#internal-holder').html('');

                // $('main').attr('class', 'view-perguntas-frequentes');

                let htmlCode = '';
                const params = { q:'faqs' };
                
                app.request(params, false, null, ( res ) => {
                    const groups = res.faq_groups;

                    htmlCode += '<div class="bts-scroll">';
                    if ( groups.length > 0 )  {
                        for(const group of groups) {
                            htmlCode += '<div class="bt">';
                            htmlCode += '    <a href="#" class="bt-scroll" data-target="' + group.title.toLowerCase() + '">';
                            htmlCode += '        <div class="ico"><img src="' + group.image + '"></div>';
                            htmlCode += '        <div class="infos">';
                            htmlCode += '            <div class="titulo">' + group.title + '</div>';
                            htmlCode += '            <div class="texto">' + group.subtitle + '</div>';
                            htmlCode += '        </div>';
                            htmlCode += '    </a>';
                            htmlCode += '</div>';
                        }
                    }
                    htmlCode += '</div>';

                    if ( groups.length > 0 )  {
                        for(const group of groups) {
                            htmlCode += '<div class="faq-area" data-group="' + group.id + '" data-anchor="' + group.title.toLowerCase() + '">';
                            htmlCode += '    <div class="titulo-area">';
                            htmlCode += '        <div class="ico"><img src="' + group.image + '"></div>';
                            htmlCode += '        <div class="infos">';
                            htmlCode += '            <div class="titulo">' + group.title + '</div>';
                            htmlCode += '            <div class="texto">' + group.subtitle + '</div>';
                            htmlCode += '        </div>';
                            htmlCode += '    </div>';

                            const topics = group.faq_topics;

                            if ( topics.length > 0 )  {
                                for(const topic of topics) {
                                    htmlCode += '<div class="sub-area" data-topic="' + topic.id + '">' + topic.title + '</div>';

                                    const faqs = topic.faqs;

                                    if ( faqs.length > 0 )  {
                                        for(const faq of faqs) {
                                            htmlCode += '<div class="item" data-anchor="item-' + faq.id + '">';
                                            htmlCode += '    <div class="pergunta">';
                                            htmlCode += '        <a href="#" class="bt-faq" data-target="item-' + faq.id + '">';
                                            htmlCode += '            <div class="txt">' + faq.question + '</div>';
                                            htmlCode += '            <div class="ico">';
                                            htmlCode += '                <svg xmlns="http://www.w3.org/2000/svg" width="14.097" height="9.857" viewBox="0 0 14.097 9.857">';
                                            htmlCode += '                  <path d="M0,12.589A77.639,77.639,0,0,0,8.15,6.717,74.545,74.545,0,0,0,0,.822L.564,0a101.053,101.053,0,0,1,8.69,6.294v.822a101.058,101.058,0,0,1-8.69,6.295Z" transform="translate(0.343 9.504) rotate(-90)" class="chevron" stroke-width="0.5"/>';
                                            htmlCode += '                </svg>';
                                            htmlCode += '            </div>';
                                            htmlCode += '        </a>';
                                            htmlCode += '    </div>';
                                            htmlCode += '    <div class="resposta">';
                                            htmlCode +=         faq.answer;
                                                
                                            if(faq.content_type == 'image') {
                                                htmlCode +=     '<div class="hld-img"><img src="' + faq.content + '"></div>';
                                            }
                                            if(faq.content_type == 'video') {
                                                htmlCode += '   <div class="hld-vid">';
                                                htmlCode += '       <video controls>';
                                                htmlCode += '           <source src="' + faq.content + '" type="video/mp4">';
                                                htmlCode += '       </video>';
                                                htmlCode += '   </div>';
                                            }
                                            htmlCode += '    </div>';
                                            htmlCode += '</div>';
                                        }
                                    }
                                }
                            }
                        
                            htmlCode += '</div>';
                        }
                    }
                    

                    $('#internal-holder').html(htmlCode);
                });
            }


            if(ajudaSUB){
                $('#menu-ajuda .bt-changeview').removeClass('selected');
                $('#menu-ajuda .bt-changeview[data-medium="' + ajudaSUB + '"]').addClass('selected');
            } else {
                $('#menu-ajuda .bt-changeview:first-child').addClass('selected');
            }

            let wait = setTimeout(function(){
                clearTimeout(wait);
                reloadMenu();
            }, 250);

            oldSub = ajudaSUB;
        }
    }
}
const makeAJUDA = new AJUDA();
window.ajuda = makeAJUDA;


function reloadMenu() {
    let targetOBJ = $('#menu-ajuda .bt-changeview.selected');

    if(targetOBJ.length > 0){
        let position = targetOBJ.position();
        let width = targetOBJ.outerWidth() - 32;

        $('#menu-ajuda .dash').css({'left': Math.floor(position.left + 16) + 'px', 'width': width + 'px'});
    }
}