let waitPost = false;
let page = 0;

class SALVOS {
    constructor(){
        $('aside.left').attr('data-anchor', 'perfil');
    }

    init(frame){
        const queryString = window.location.hash;
        const urlParams = new URLSearchParams(queryString);
        const salvosSUB = urlParams.get('sub');
        const salvosID = urlParams.get('id');


        $('.bt-changeview').each(function(){
            if($(this).attr('data-target') == frame){
                $(this).parent().addClass('selected');
            }
        })


        if(frame == 'meus-itens-salvos' && salvosSUB == null) {
            page = 0;

            // $('main').attr('class', 'view-meus-itens-salvos');

            $('#internal-holder').html(`
                <div class="area-mobile">
                    <div class="txt">itens salvos</div>
                    <div class="bt-fechar">
                        <a href="#" class="bt-changeview" data-target="dashboard"></a>
                    </div>
                </div>
                <div class="salvos">
                    <div class="title">
                        <h2>salvos para ver de novo</h2>
                    </div>`);
            makeLoading(page, 'salvos');
            $('#internal-holder').append(`</div>`);


            const params = { q:'feed', filter:'itens_salvos', read_later:'true', type:'posts', page:page };
            app.request(params, false, null, ( res ) => {
                let htmlCode = '';
                let loop = res.feed;

                htmlCode += '<div class="area-mobile">';
                htmlCode += '    <div class="txt">itens salvos</div>';
                htmlCode += '    <div class="bt-fechar">';
                htmlCode += '        <a href="#" class="bt-changeview" data-target="dashboard"></a>';
                htmlCode += '    </div>';
                htmlCode += '</div>';
                htmlCode += '<div class="salvos">';
                htmlCode += '    <div class="title">';
                htmlCode += '        <h2>salvos para ver de novo</h2>';
                htmlCode += '    </div>';
                
                if(loop.length > 0) {
                    loop.forEach(function(item) {
                        let dia = item.date;
                        let diaFormat = new Date(dia).toLocaleDateString();

                        if(item.type != 'twitter' && item.type != 'youtube' && item.type != 'instagram' && item.type != 'facebook'){
                            item.reference = 'interno';
                            item.origin = 'Renault';
                        } else {
                            item.reference = item.type;
                            if(item.type == 'twitter') {
                                item.title = 'X';
                                item.origin = '@RenaultBrasil';
                            }

                            if(item.type == 'youtube') {
                                item.title = 'YouTube';
                                item.origin = '@RenaultBrasil';
                            }

                            if(item.type == 'instagram') {
                                item.title = 'Instagram';
                                item.origin = '@renaultbrasil';
                            }

                            if(item.type == 'facebook') {
                                item.title = 'Facebook';
                                item.origin = '@Renault';
                            }
                        }

                        htmlCode += '<div class="post" type="feed" reference="' + item.reference + '">';
                        htmlCode += '   <div class="midia">';
                        if(item.youtube != undefined && item.youtube != null){
                            htmlCode += '   <div class="hld-youtube">';
                            htmlCode += '       <div class="source"></div>';
                            htmlCode += '       <div class="thumb" style="background-image: url(\'https://img.youtube.com/vi/' + item.youtube + '/hqdefault.jpg\');"></div>';
                            htmlCode += '       <div class="bt-youtube-close"></div>';
                            htmlCode += '   </div>';
                            htmlCode += '   <a href="#" class="bt-changeview" data-target="meus-itens-salvos" data-medium="item-salvo" data-sub="' + item.id + '">';
                            htmlCode += '       <div class="img" style="background-image: url(\'https://img.youtube.com/vi/' + item.youtube + '/hqdefault.jpg\');"></div>';
                        } else {
                            if(item.content_type == 'video') {
                                htmlCode += '   <div class="hld-video">';
                                htmlCode += '       <div class="source"></div>';
                                htmlCode += '       <div class="thumb" style="background-image: url(\'' + item.thumbnail + '\');"></div>';
                                htmlCode += '       <div class="bt-video-close"></div>';
                                htmlCode += '   </div>';
                                htmlCode += '   <a href="#" class="bt-changeview" data-target="meus-itens-salvos" data-medium="item-salvo" data-sub="' + item.id + '">';
                                htmlCode += '       <div class="img" style="background-image: url(\'' + item.thumbnail + '\');"></div>';
                            } else {
                                if(item.description != 'universo') {
                                    if(item.link != '') {
                                        htmlCode += '<a href="'+ item.link +'" class="link-externo" target="_blank">';
                                    } else {
                                        htmlCode += '<a href="#" class="bt-changeview" data-target="meus-itens-salvos" data-medium="item-salvo" data-sub="' + item.id + '">';
                                    }
                                }
                                htmlCode += '       <div class="img" style="background-image: url(\'' + item.content + '\');"></div>';
                            }
                        } 
                        htmlCode += '           <div class="description">';
                        htmlCode += '               <h3>' + item.description + '</h3>';
                        htmlCode += '               <h4>' + item.cta + '</h4>';
                        htmlCode += '           </div>';
                        if(item.description != 'universo') {
                            htmlCode += '   </a>';
                        }
                        htmlCode += '   </div>';
                        htmlCode += '   <div class="infos">';
                        htmlCode += '        <a href="#" class="bt-changeview" data-target="meus-itens-salvos" data-medium="item-salvo" data-sub="' + item.id + '">';
                        htmlCode += '            <div class="aux">';
                        htmlCode += '                <div class="title">';
                        htmlCode += '                    <div class="aux">';
                        htmlCode += '                       <div class="ico"><img src="assets/images/ico_instagram.svg"></div>';
                        htmlCode += '                       <div class="txt">';
                        htmlCode += '                           <h3>' + item.title + '</h3>';
                        htmlCode += '                           <p>' + item.origin + '</p>';
                        htmlCode += '                       </div>';
                        htmlCode += '                   </div>';
                        htmlCode += '                </div>';
                        htmlCode += '            </div>';
                        htmlCode += '        </a>';
                        htmlCode += '       <div class="actions">';
                        htmlCode += '           <a href="#" class="bt-changeview" data-target="meus-itens-salvos" data-medium="item-salvo" data-sub="' + item.id + '">';
                        htmlCode += '                <div class="data">Salvo em ' + diaFormat + '</div>';
                        htmlCode += '           </a>';
                        htmlCode += '           <div class="buttons">';
                        htmlCode += '               <div class="hld-bt hld-save">';
                        htmlCode += '                    <a href="#" class="bt-save active" data-target="' + item.id + '">';
                        htmlCode += '                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" version="1.1" viewBox="0 0 24 24">';
                        htmlCode += '                          <g class="off">';
                        htmlCode += '                          <path d="M12,19.7l-6.8,4.3-1.1-.5V2.3c0-1.5.8-2.3,2.3-2.3h11.4c1.5,0,2.3.8,2.3,2.3v21.3l-1.1.4-6.8-4.3h0ZM18.7,22.3V2.5c0-.8-.3-1.2-1.1-1.2H6.5c-.8,0-1.1.4-1.1,1.2v19.7l6.7-4.2,6.7,4.2ZM14.9,14.1c-1.3-.6-2-1-3-1.4-.9.5-1.7.8-3,1.4l-.6-.4c.2-1.1.4-2.1.6-3.2-.8-.7-1.3-1.2-2.3-2.3l.2-.8c1.4-.2,2.2-.4,3.2-.5.5-1,.9-1.8,1.6-3.1h.5c.7,1.2,1.1,2.1,1.6,3.1,1,0,1.7.2,3.1.5l.2.8c-1,1-1.5,1.6-2.3,2.3.3,1.1.5,2.1.6,3.2l-.6.4s.2,0,.2,0ZM9.9,12.4c1-.5,1.5-.8,2.1-1.1.6.4,1.3.8,2,1.1-.2-1-.3-1.6-.4-2.1.8-.8,1.2-1.2,1.7-1.7-1.1-.2-1.7-.2-2.3-.3-.3-.7-.6-1.4-1-2.1-.3.6-.5,1.1-1,2.1-.7,0-1.2.2-2.4.3.5.6,1.1,1.1,1.7,1.7-.2.6-.2,1.1-.4,2.2"></path>';
                        htmlCode += '                          </g>';
                        htmlCode += '                          <g class="on">';
                        htmlCode += '                            <path d="M12.1,6.1c-.3.6-.5,1.1-1,2.1-.7,0-1.2.2-2.4.3.5.6,1.1,1.1,1.7,1.7-.2.6-.2,1.1-.4,2.2,1-.5,1.5-.8,2.1-1.1.6.4,1.3.8,2,1.1-.2-1-.3-1.6-.4-2.1.8-.8,1.2-1.2,1.7-1.7-1.1-.2-1.7-.2-2.3-.3-.3-.7-.6-1.4-1-2.1"></path>';
                        htmlCode += '                            <path d="M17.7,0H6.3C4.8,0,4,.8,4,2.3v21.3l1.1.5,6.8-4.3,6.8,4.3,1.1-.4V2.3c0-1.5-.8-2.3-2.3-2.3M14.9,10.6c.3,1.1.5,2.1.6,3.2l-.6.4c-1.3-.6-2-1-3-1.4-.9.5-1.7.8-3,1.4l-.6-.4c.2-1.1.4-2.1.6-3.2-.8-.7-1.3-1.2-2.3-2.3l.2-.8c1.4-.2,2.2-.4,3.2-.5.5-1,.9-1.8,1.6-3.1h.5c.7,1.2,1.1,2.1,1.6,3.1,1,0,1.7.2,3.1.5l.2.8c-1,1-1.5,1.6-2.3,2.3"></path>';
                        htmlCode += '                          </g>';
                        htmlCode += '                        </svg>';
                        htmlCode += '                    </a>';
                        htmlCode += '                    <div class="over">';
                        htmlCode += '                        Salvar';
                        htmlCode += '                    </div>';
                        htmlCode += '               </div>';
                        htmlCode += '               <div class="hld-bt hld-share">';
                        htmlCode += '                    <a href="#" class="bt-compartilhar" data-target="' + item.id + '" data-share-title="' + encodeURIComponent(item.title) + '" data-share-text="' + encodeURIComponent(item.message) + '" data-share-link="' + encodeURIComponent(item.share_link) + '">';
                        htmlCode += '                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 24 24">';
                        htmlCode += '                            <path d="M9,12.8l6.1,2.8c.7-1.5,2.2-2.3,3.9-2.3,2.2-.1,4.2,1.6,4.3,3.8,0,.2,0,.3,0,.5.1,2.2-1.5,4.2-3.8,4.3-.2,0-.3,0-.5,0-2.3.1-4.2-1.6-4.3-3.9,0-.1,0-.3,0-.4,0-.2,0-.5,0-.7l-6.1-2.9c-.7,1.4-2.2,2.3-3.8,2.2-2.3.1-4.2-1.6-4.3-3.9,0-.1,0-.3,0-.4-.1-2.3,1.6-4.2,3.9-4.3.1,0,.3,0,.4,0,1.6-.1,3.1.8,3.8,2.2l6.1-2.9c0-.2,0-.5,0-.7-.1-2.3,1.6-4.2,3.9-4.3.1,0,.3,0,.4,0,2.2-.1,4.2,1.5,4.3,3.8,0,.2,0,.3,0,.5.1,2.2-1.6,4.2-3.8,4.3-.2,0-.3,0-.5,0-1.6,0-3.1-.8-3.9-2.3l-6.1,2.8c0,.3,0,.5,0,.8,0,.3,0,.5,0,.8M4.8,14.9c1.4.2,2.7-.9,2.9-2.3,0-.2,0-.4,0-.6.2-1.4-.9-2.8-2.3-2.9-.2,0-.4,0-.6,0-1.4-.2-2.7.9-2.9,2.3,0,.2,0,.4,0,.6-.2,1.4.9,2.8,2.3,2.9.2,0,.4,0,.6,0M19,9.2c1.4.2,2.7-.9,2.9-2.3,0-.2,0-.4,0-.6.2-1.4-.9-2.8-2.3-2.9-.2,0-.4,0-.6,0-1.4-.2-2.7.9-2.9,2.3,0,.2,0,.4,0,.6-.2,1.4.9,2.8,2.3,2.9.2,0,.4,0,.6,0M19,20.6c1.4.2,2.7-.9,2.9-2.3,0-.2,0-.4,0-.6.2-1.4-.9-2.8-2.3-2.9-.2,0-.4,0-.6,0-1.4-.2-2.7.9-2.9,2.3,0,.2,0,.4,0,.6-.2,1.4.9,2.8,2.3,2.9.2,0,.4,0,.6,0"></path>';
                        htmlCode += '                        </svg>';
                        htmlCode += '                    </a>';
                        htmlCode += '               </div>';
                        htmlCode += '           </div>';
                        htmlCode += '       </div>';
                        htmlCode += '   </div>';
                        htmlCode += '</div>';
                    });

                    htmlCode += '</div>';
                } else {
                    htmlCode += '   <div class="header-text gray">';
                    htmlCode += '       <h3>você ainda não tem itens salvos!</h3> ';
                    htmlCode += '       <div class="ico"><img src="assets/images/ico_notificacoes_ok.svg"></div>';
                    htmlCode += '   </div>';
                    htmlCode += '</div>';
                }
                $('#internal-holder').html(htmlCode);
            })

        }

        if(frame == 'meus-itens-salvos' && salvosSUB == 'item-salvo') {
            const params = { q:'feed', search:'id:' + salvosID };
            // $('main').attr('class', 'view-item-salvo-'+ salvosID);

            app.request(params, false, null, ( res ) => {
                $('#internal-holder').html('');
                makePost('0', res, 'meus-itens-salvos');
            });
        }
    }
}
const makeSALVOS = new SALVOS();
window.salvos = makeSALVOS;