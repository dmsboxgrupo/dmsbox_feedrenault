let loadMenu = '';
let page = 0;
let frameLoaded = 0;
let oldFrame = ''
let oldVersao = '';
let oldVeiculo = '';
let oldSUB = '';
let arrVehicles = [];
let openVehicle = 0;
let openVersion = 0;
let openGroup = 0;
let openSubGroup = 0;
let oldFiltro = [];


let waitMateriais = false;
let materiaisbaseLoaded = false;
let arrTags = [];
let arrSubs = [];
let waitFrame = '';
let holderCodeModal = '';
let holderCountModal = 0;

class MATERIAIS {
    constructor(){
        const params = { q:'material_library_tags' };
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
                    let materiaisURL = 'materiais-' + slugURL;
                    let hasSub = false;
                    let arrSub = [];
                    
                    if(item.subgroups){
                        hasSub = true;
                        let loopSubs = item.subgroups;

                        loopSubs.forEach(function(sub) {
                            let subname = sub.name;       
                            let subid = sub.id;

                            arrSub.push({subname, subid})
                        })
                    }

                    arrSubs.push(arrSub);
                    arrTags.push({materiaisURL, id, hasSub});
                    window.hash.push({internal: materiaisURL, holder: 'materiais', hash: materiaisURL});

                    htmlCode += '<div class="item">';   
                    htmlCode += '    <a href="#" class="bt-changeview" data-target="' + materiaisURL + '" data-group="materiais">';    
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
                            swipe: true,
                            touchMove: true,
                            draggable: true,
                            swipeToSlide: true,
                          }
                        },
                        {
                          breakpoint: 768,
                          settings: {
                            slidesToShow: 5,
                            arrows: false,
                            swipe: true,
                            touchMove: true,
                            draggable: true,
                            swipeToSlide: true,
                          }
                        },
                        {
                          breakpoint: 664,
                          settings: {
                            slidesToShow: 4,
                            arrows: false,
                            swipe: true,
                            touchMove: true,
                            draggable: true,
                            swipeToSlide: true,
                          }
                        },
                        {
                          breakpoint: 566,
                          settings: {
                            slidesToShow: 3,
                            arrows: false,
                            swipe: true,
                            touchMove: true,
                            draggable: true,
                            swipeToSlide: true,
                          }
                        },
                        {
                          breakpoint: 420,
                          settings: {
                            slidesToShow: 2,
                            arrows: false,
                            swipe: true,
                            touchMove: true,
                            draggable: true,
                            swipeToSlide: true,
                          }
                        },
                      ],
                    });
                }

                materiaisbaseLoaded = true;

                if(waitFrame != '') {
                    this.init(waitFrame);
                }

                $(window).on('resize', function() {
                    reloadMenu();
                });
                reloadMenu();
            }
        })
    }

    init(frame, filtrado = false){
        page = 0;
        $('html, body').animate({
            scrollTop: 0
        }, 0);

        if(materiaisbaseLoaded) {
            $('.bt-changeview').each(function(){
                if($(this).attr('data-target') == frame){
                    $(this).parent().addClass('selected');
                }
            })

            const queryString = window.location.hash;
            const urlParams = new URLSearchParams(queryString);
            const materiaisVeiculo = urlParams.get('veiculo');
            const materiaisVersao = urlParams.get('versao');
            const materiaisSUB = urlParams.get('sub');
            const materiaisID = urlParams.get('id');
            const onepageGroup = urlParams.get('grupo');
            const onepageFilter = urlParams.get('filtro');

            let mobile = false;
            if($(window).width() < 992){
                mobile = true;
            }

            frameLoaded = frame;
            page = 0;


            $('.bt-dropdown[data-target="materiais"]').parent().parent().addClass('selected');
            $('.nav-mobile [data-target="biblioteca-de-materiais"]').parent().addClass('selected');
            if(!$('.bt-dropdown[data-target="materiais"]').parent().parent().find('.sub-content').is(':visible')){
                $('.bt-dropdown[data-target="materiais"]').parent().parent().find('.sub-content').slideDown();
            }

            if($('#galeria .slider').hasClass('slick-initialized')) {
                $('#galeria .slider').slick('unslick');
            }
            $('#galeria').removeClass('story').removeClass('post');
            $('#galeria .slider').html('');

            let group = '';
            let groupIndex = '';
            let subgroup = '';


            for(let i=0; i <arrTags.length; i++){
                if(arrTags[i]['materiaisURL'] == frame) {
                    group = arrTags[i]['id'];
                    let menu = arrTags[i]['materiaisURL'];

                    if(arrTags[i]['hasSub']){
                        let loopSub = arrSubs[i];
                        let menuHTML = '';

                        if(materiaisSUB == null) {
                            subgroup = loopSub[0].subid;
                        }

                        $('#menu-materiais').removeClass('image-menu');
                        
                        if(loadMenu != menu){
                            menuHTML += '<div class="aux">';
                            menuHTML += '   <div class="aux-scroll">';

                            loopSub.forEach(function(sub) {
                                menuHTML += '<a href="#" class="bt-changeview" data-target="' + menu + '" data-medium="' + util.slugfy(sub.subname) + '" data-id="' + sub.subid + '">' + sub.subname + '</a>';
                            })

                            menuHTML += '       <div class="dash"></div>';
                            menuHTML += '   </div>';
                            menuHTML += '</div>';
                            loadMenu = menu;

                            $('#menu-materiais').html(menuHTML);
                            $('#menu-materiais').show();
                        }
                    } else {
                        loadMenu = '';
                        $('#menu-materiais').hide();
                    }
                    groupIndex = i;
                } 
            }

            let slides = $('.menu-slider .slider').slick('slickGetOption', 'slidesToShow');
            let totalSlides = $('.menu-slider .slider .item').length;
            let pagesSlides = totalSlides - slides;

            if(groupIndex > pagesSlides) {
                groupIndex = pagesSlides;
            }
            $('.menu-slider .slider').slick('slickGoTo', groupIndex)


            /* IMAGENS DOS VEICULOS */
            if(frame == 'materiais-imagens-dos-veiculos' || frame == 'materiais-ficha-de-modelos') {
                $('body').addClass('imgs-veiculos');

                if(filtro != '') {
                    window.filtro = [];
                }
            } else {
                $('body').removeClass('imgs-veiculos');

                if(filtro == '') {
                    $('#lista-filtro .bt-filtro-veiculo').removeClass('on');
                }
            }

            $('#submenu-materiais').hide();

            if(frame == 'materiais-materiais-editaveis' && materiaisSUB == 'redes-sociais') {
                $('#submenu-materiais').show();
            }

            if(materiaisID){
                $('#internal-holder').html('');
                $('#submenu-materiais').hide();

                const paramsTemplates = { q:'templates', id: materiaisID };

                // $('main').attr('class', 'view-material-' + materiaisID);

                app.request(paramsTemplates, false, null, ( res ) => {
                    if(res.error != null) {
                    }else{
                        let htmlCode = '';
                        let materialType = 'post';
                        let template = res.templates[0];
                        let templateType = '';
                        let channel = template.channel;
                        let channelName = '';

                        if(channel == '1'){
                            channelName = 'whatsapp';
                        } else {
                            channelName = 'redes-sociais';
                        }

                        if(template.format == '2'){
                            materialType = 'story';
                        }
                        
                        htmlCode += '<div class="area-mobile">';
                        htmlCode += '    <div class="txt">biblioteca de materiais</div>';
                        htmlCode += '    <div class="bt-fechar">';
                        htmlCode += '        <a href="#" class="bt-historyback"></a>';
                        htmlCode += '    </div>';
                        htmlCode += '</div>';

                        let fields = template.fields;

                        htmlCode += '<div class="edicao materiais">';
                        htmlCode += '    <div class="interna-post mat-'+ materialType +'">';
                        htmlCode += '        <div class="post" type="material" reference="'+ materialType +'">';
                        htmlCode += '            <div class="midia">';
                        if(template.template_type == '1') {
                            templateType = 'atributos';
                            $('#cards .txt.concessionaria').html(fields.field_0.text);
                            $('#cards .txt.telefones').html(fields.field_1.text);
                            if(channel == '1'){
                                $('#cards .txt.vendedor').html(fields.field_2.text);
                            }
                            $('#cards .txt.endereco').html(fields.field_3.text);
                            $('#cards .txt.cep').html(fields.field_4.text);

                            htmlCode += '           <div class="aux-text '+ templateType +'">';
                            htmlCode += '               <div class="custom-text concessionaria">' + fields.field_0.text + '</div>';
                            htmlCode += '               <div class="custom-text telefones">' + fields.field_1.text + '</div>';
                            if(channel == '1'){
                                htmlCode += '           <div class="custom-text vendedor">' + fields.field_2.text + '</div>';
                            }
                            htmlCode += '               <div class="custom-text endereco">' + fields.field_3.text + '</div>';
                            htmlCode += '               <div class="custom-text cep">' + fields.field_4.text + '</div>';
                            htmlCode += '           </div>';
                        }
                        if(template.template_type == '2') {
                            templateType = 'testdrive';
                            $('#cards .txt.concessionaria').html(fields.field_0.text);
                            $('#cards .txt.telefones').html(fields.field_1.text);
                            if(channel == '1'){
                                $('#cards .txt.vendedor').html(fields.field_2.text);
                            }
                            $('#cards .txt.endereco').html(fields.field_3.text);
                            $('#cards .txt.cep').html(fields.field_4.text);

                            htmlCode += '           <div class="aux-text '+ templateType +'">';
                            htmlCode += '               <div class="custom-text concessionaria">' + fields.field_0.text + '</div>';
                            htmlCode += '               <div class="custom-text telefones">' + fields.field_1.text + '</div>';
                            if(channel == '1'){
                                htmlCode += '           <div class="custom-text vendedor">' + fields.field_2.text + '</div>';
                            }
                            htmlCode += '               <div class="custom-text endereco">' + fields.field_3.text + '</div>';
                            htmlCode += '               <div class="custom-text cep">' + fields.field_4.text + '</div>';
                            htmlCode += '           </div>';
                        }
                        if(template.template_type == '3') {
                            templateType = 'valor';

                            let strValor = fields.field_0.text.replace('R$ ', '');
                            $('#cards .txt.valor').html(strValor);
                            $('#cards .txt.texto').html(fields.field_1.text);

                            htmlCode += '           <div class="aux-text '+ templateType +'">';
                            htmlCode += '               <div class="custom-text valor">' + strValor + '</div>';
                            htmlCode += '               <div class="custom-text texto">' + fields.field_1.text + '</div>';
                            htmlCode += '           </div>';
                        }
                        if(template.template_type == '4') {
                            templateType = 'parcela';

                            let strValor = fields.field_0.text.replace('R$ ', '');
                            $('#cards .txt.valor').html(strValor);
                            $('#cards .txt.texto').html(fields.field_1.text);

                            htmlCode += '           <div class="aux-text '+ templateType +'">';
                            htmlCode += '               <div class="custom-text valor">' + strValor + '</div>';
                            htmlCode += '               <div class="custom-text texto">' + fields.field_1.text + '</div>';
                            htmlCode += '           </div>';
                        }
                        if(template.template_type == '5') {
                            templateType = 'atributos-care';
                            $('#cards .txt.concessionaria').html(fields.field_0.text);
                            $('#cards .txt.telefones').html(fields.field_1.text);
                            if(channel == '1'){
                                $('#cards .txt.vendedor').html(fields.field_2.text);
                            }
                            $('#cards .txt.endereco').html(fields.field_3.text);
                            $('#cards .txt.cep').html(fields.field_4.text);

                            htmlCode += '           <div class="aux-text '+ templateType +'">';
                            htmlCode += '               <div class="custom-text concessionaria">' + fields.field_0.text + '</div>';
                            htmlCode += '               <div class="custom-text telefones">' + fields.field_1.text + '</div>';
                            if(channel == '1'){
                                htmlCode += '           <div class="custom-text vendedor">' + fields.field_2.text + '</div>';
                            }
                            htmlCode += '               <div class="custom-text endereco">' + fields.field_3.text + '</div>';
                            htmlCode += '               <div class="custom-text cep">' + fields.field_4.text + '</div>';
                            htmlCode += '           </div>';
                        }
                        if(template.template_type == '6') {
                            templateType = 'agendamento-care';
                            $('#cards .txt.concessionaria').html(fields.field_0.text);
                            $('#cards .txt.telefones').html(fields.field_1.text);
                            if(channel == '1'){
                                $('#cards .txt.vendedor').html(fields.field_2.text);
                            }
                            $('#cards .txt.endereco').html(fields.field_3.text);
                            $('#cards .txt.cep').html(fields.field_4.text);

                            htmlCode += '           <div class="aux-text '+ templateType +'">';
                            htmlCode += '               <div class="custom-text concessionaria">' + fields.field_0.text + '</div>';
                            htmlCode += '               <div class="custom-text telefones">' + fields.field_1.text + '</div>';
                            if(channel == '1'){
                                htmlCode += '               <div class="custom-text vendedor">' + fields.field_2.text + '</div>';
                            }
                            htmlCode += '               <div class="custom-text endereco">' + fields.field_3.text + '</div>';
                            htmlCode += '               <div class="custom-text cep">' + fields.field_4.text + '</div>';
                            htmlCode += '           </div>';
                        }
                        if(template.template_type == '7') {
                            templateType = 'comercial-care';
                            $('#cards .txt.servico').html(fields.field_0.text);

                            let strValor = fields.field_1.text.replace('R$ ', '');
                            $('#cards .txt.valor').html(strValor);
                            $('#cards .txt.oferta').html(fields.field_2.text);
                            $('#cards .txt.itens').html(fields.field_3.text);

                            htmlCode += '           <div class="aux-text '+ templateType +'">';
                            htmlCode += '               <div class="custom-text servico">' + fields.field_0.text + '</div>';
                            htmlCode += '               <div class="custom-text valor">' + strValor + '</div>';
                            htmlCode += '               <div class="custom-text oferta">' + fields.field_2.text + '</div>';
                            htmlCode += '               <div class="custom-text itens">' + fields.field_3.text + '</div>';
                            htmlCode += '           </div>';
                        }
                        htmlCode += '                <a href="#" class="bt-changeview" data-target="materiais-materiais-editaveis" data-medium="whatsapp" data-sub="13">';
                        htmlCode += '                    <div class="img" style="background-image: url(\''+ template.content +'\');"></div>';
                        htmlCode += '                </a>';
                        htmlCode += '            </div>';
                        htmlCode += '        </div>';
                        htmlCode += '    </div>';
                        htmlCode += '    <div class="formulario">';
                        htmlCode += '        <div class="aux">';
                        htmlCode += '            <div class="chamada">';
                        htmlCode += '                <div class="txt">Personalize para enviar por whatsapp</div>';
                        htmlCode += '                <div class="bt">';
                        if(channel == '1'){
                            htmlCode += '               <a href="#" class="bt-changeview" data-target="materiais-materiais-editaveis" data-medium="whatsapp"></a>';
                        } else {
                            htmlCode += '               <a href="#" class="bt-changeview" data-target="materiais-materiais-editaveis" data-medium="redes-sociais"></a>';
                        }
                        htmlCode += '               </div>';
                        htmlCode += '           </div>';
                        htmlCode += '           <div class="inputs">';

                        if(template.template_type == '1') { 
                            htmlCode += '           <div class="hld-inp label">';
                            htmlCode += '               <label>nome da concessionária</label>';
                            htmlCode += '               <input type="text" name="concessionaria" placeholder="' + fields.field_0.text + '">';
                            htmlCode += '           </div>';
                            htmlCode += '           <div class="hld-inp label">';
                            htmlCode += '               <label>telefones para contato</label>';
                            htmlCode += '               <input type="text" name="telefones" placeholder="' + fields.field_1.text + '">';
                            htmlCode += '           </div>';
                            if(channel == '1'){
                                htmlCode += '       <div class="hld-inp label">';
                                htmlCode += '           <label>nome do vendedor</label>';
                                htmlCode += '           <input type="text" name="vendedor" placeholder="' + fields.field_2.text + '">';
                                htmlCode += '       </div>';
                            }
                            htmlCode += '           <div class="hld-inp label">';
                            htmlCode += '               <label>endereço da concessionária</label>';
                            htmlCode += '               <input type="text" name="endereco" placeholder="' + fields.field_3.text + '">';
                            htmlCode += '           </div>';
                            htmlCode += '           <div class="hld-inp label">';
                            htmlCode += '               <label>CEP e cidade</label>';
                            htmlCode += '               <input type="text" name="cep" placeholder="' + fields.field_4.text + '">';
                            htmlCode += '           </div>';
                        }
                        if(template.template_type == '2') { 
                            htmlCode += '           <div class="hld-inp label">';
                            htmlCode += '               <label>nome da concessionária</label>';
                            htmlCode += '               <input type="text" name="concessionaria" placeholder="' + fields.field_0.text + '">';
                            htmlCode += '           </div>';
                            htmlCode += '           <div class="hld-inp label">';
                            htmlCode += '               <label>telefones para contato</label>';
                            htmlCode += '               <input type="text" name="telefones" placeholder="' + fields.field_1.text + '">';
                            htmlCode += '           </div>';
                            if(channel == '1'){
                                htmlCode += '       <div class="hld-inp label">';
                                htmlCode += '           <label>nome do vendedor</label>';
                                htmlCode += '           <input type="text" name="vendedor" placeholder="' + fields.field_2.text + '">';
                                htmlCode += '       </div>';
                            }
                            htmlCode += '           <div class="hld-inp label">';
                            htmlCode += '               <label>endereço da concessionária</label>';
                            htmlCode += '               <input type="text" name="endereco" placeholder="' + fields.field_3.text + '">';
                            htmlCode += '           </div>';
                            htmlCode += '           <div class="hld-inp label">';
                            htmlCode += '               <label>CEP e cidade</label>';
                            htmlCode += '               <input type="text" name="cep" placeholder="' + fields.field_4.text + '">';
                            htmlCode += '           </div>';
                        }
                        if(template.template_type == '3') {
                            htmlCode += '           <div class="hld-inp label">';
                            htmlCode += '               <label>valor</label>';
                            htmlCode += '               <input type="text" name="valor" class="mask-money" placeholder="' + fields.field_0.text + '">';
                            htmlCode += '           </div>';
                            htmlCode += '           <div class="hld-inp label">';
                            htmlCode += '               <label>texto em até 4 linhas</label>';
                            htmlCode += '               <textarea rows="4" maxlength="96" name="texto" placeholder="' + fields.field_1.text + '"></textarea>';
                            htmlCode += '           </div>';                        
                        }
                        if(template.template_type == '4') {
                            htmlCode += '           <div class="hld-inp label">';
                            htmlCode += '               <label>valor da parcela</label>';
                            htmlCode += '               <input type="text" name="valor" class="mask-money" placeholder="' + fields.field_0.text + '">';
                            htmlCode += '           </div>';
                            htmlCode += '           <div class="hld-inp label">';
                            htmlCode += '               <label>texto em até 4 linhas</label>';
                            htmlCode += '               <textarea rows="4" maxlength="96" name="texto" placeholder="' + fields.field_1.text + '"></textarea>';
                            htmlCode += '           </div>';   
                        }
                        if(template.template_type == '5') { 
                            htmlCode += '           <div class="hld-inp label">';
                            htmlCode += '               <label>nome da concessionária</label>';
                            htmlCode += '               <input type="text" name="concessionaria" placeholder="' + fields.field_0.text + '">';
                            htmlCode += '           </div>';
                            htmlCode += '           <div class="hld-inp label">';
                            htmlCode += '               <label>telefones para contato</label>';
                            htmlCode += '               <input type="text" name="telefones" placeholder="' + fields.field_1.text + '">';
                            htmlCode += '           </div>';
                            if(channel == '1'){
                                htmlCode += '       <div class="hld-inp label">';
                                htmlCode += '           <label>nome do vendedor</label>';
                                htmlCode += '           <input type="text" name="vendedor" placeholder="' + fields.field_2.text + '">';
                                htmlCode += '       </div>';
                            }
                            htmlCode += '           <div class="hld-inp label">';
                            htmlCode += '               <label>endereço da concessionária</label>';
                            htmlCode += '               <input type="text" name="endereco" placeholder="' + fields.field_3.text + '">';
                            htmlCode += '           </div>';
                            htmlCode += '           <div class="hld-inp label">';
                            htmlCode += '               <label>CEP e cidade</label>';
                            htmlCode += '               <input type="text" name="cep" placeholder="' + fields.field_4.text + '">';
                            htmlCode += '           </div>';
                        }
                        if(template.template_type == '6') { 
                            htmlCode += '           <div class="hld-inp label">';
                            htmlCode += '               <label>nome da concessionária</label>';
                            htmlCode += '               <input type="text" name="concessionaria" placeholder="' + fields.field_0.text + '">';
                            htmlCode += '           </div>';
                            htmlCode += '           <div class="hld-inp label">';
                            htmlCode += '               <label>telefones para contato</label>';
                            htmlCode += '               <input type="text" name="telefones" placeholder="' + fields.field_1.text + '">';
                            htmlCode += '           </div>';
                            if(channel == '1'){
                                htmlCode += '       <div class="hld-inp label">';
                                htmlCode += '           <label>nome do vendedor</label>';
                                htmlCode += '           <input type="text" name="vendedor" placeholder="' + fields.field_2.text + '">';
                                htmlCode += '       </div>';
                            }
                            htmlCode += '           <div class="hld-inp label">';
                            htmlCode += '               <label>endereço da concessionária</label>';
                            htmlCode += '               <input type="text" name="endereco" placeholder="' + fields.field_3.text + '">';
                            htmlCode += '           </div>';
                            htmlCode += '           <div class="hld-inp label">';
                            htmlCode += '               <label>CEP e cidade</label>';
                            htmlCode += '               <input type="text" name="cep" placeholder="' + fields.field_4.text + '">';
                            htmlCode += '           </div>';
                        }
                        if(template.template_type == '7') {
                            htmlCode += '           <div class="hld-inp label">';
                            htmlCode += '               <label>especificação do serviço</label>';
                            htmlCode += '               <input type="text" name="servico" placeholder="' + fields.field_0.text + '">';
                            htmlCode += '           </div>';
                            htmlCode += '           <div class="hld-inp label">';
                            htmlCode += '               <label>valor</label>';
                            htmlCode += '               <input type="text" name="valor" class="mask-money" placeholder="' + fields.field_1.text + '">';
                            htmlCode += '           </div>';
                            htmlCode += '           <div class="hld-inp label">';
                            htmlCode += '               <label>informação da oferta</label>';
                            htmlCode += '               <input type="text" name="oferta" placeholder="' + fields.field_2.text + '">';
                            htmlCode += '           </div>';
                            htmlCode += '           <div class="hld-inp label">';
                            htmlCode += '               <label>número de itens</label>';
                            htmlCode += '               <input type="text" name="itens" maxlength="3" placeholder="' + fields.field_3.text + '">';
                            htmlCode += '           </div>';
                        }
                        htmlCode += '           </div>';
                        htmlCode += '        </div>';
                        htmlCode += '        <div class="bts">';
                        htmlCode += '            <div class="bt">';
                        htmlCode += '                <a href="#" class="bt-geraimg" data-target="download" data-formato="' + materialType + '" data-canal="' + channelName + '">baixar</a>';
                        htmlCode += '            </div>';
                        htmlCode += '            <div class="bt">';
                        htmlCode += '                <a href="#" class="bt-geraimg" data-target="share" data-formato="' + materialType + '" data-canal="' + channelName + '">compartilhar</a>';
                        htmlCode += '            </div>';
                        htmlCode += '        </div>';
                        htmlCode += '    </div>';
                        htmlCode += '</div>';

                        $('#cards .card').removeClass('on');
                        $('#cards .' + materialType + '.' + templateType).addClass('on');
                        $('#cards .' + materialType + '.' + templateType).css({'background-image':'url(\'' + template.content + '\')'})


                        $('#internal-holder').html(htmlCode);
                        oldFrame = 'interna-materiais';

                        $('.mask-money').maskMoney({prefix:'R$ ', thousands:'.', decimal:','});
                    }
                })
            } else {
                if(oldFrame != frame || oldSUB != materiaisSUB || oldVersao != materiaisVersao || oldVeiculo != materiaisVeiculo || filtrado){
                    $('#internal-holder').html('');

                    /* BIBLIOTECA DE MATERIAIS */
                    if(frame == 'biblioteca-de-materiais'){
                        // $('main').attr('class', 'view-biblioteca-de-materiais');

                        $('#menu-materiais').hide();
                        let loadCode = '';

                        loadCode += '<div class="grid loading">';
                        loadCode += '    <div class="title">';
                        loadCode += '        <h2 style="width: 236px"></h2>';
                        loadCode += '    </div>';
                        loadCode += '    <div class="hld-grid">';

                        for(let i=0; i<5; i++){
                            loadCode += '   <div class="post" type="material" reference="loading">';
                            loadCode += '       <div class="midia"></div>';
                            loadCode += '   </div>';
                        }

                        loadCode += '    </div>';
                        loadCode += '    <div class="bt">';
                        loadCode += '        <a href="#" class="bt-mais-cards"></a>';
                        loadCode += '    </div>';
                        loadCode += '</div>';

                        loadCode += '<div class="grid loading">';
                        loadCode += '    <div class="title">';
                        loadCode += '        <h2 style="width: 124px"></h2>';
                        loadCode += '    </div>';
                        loadCode += '    <div class="hld-grid">';
                        
                        for(let i=0; i<5; i++){
                            loadCode += '   <div class="post" type="material" reference="loading">';
                            loadCode += '       <div class="midia"></div>';
                            loadCode += '   </div>';
                        }

                        loadCode += '    </div>';
                        loadCode += '    <div class="bt">';
                        loadCode += '        <a href="#" class="bt-mais-cards"></a>';
                        loadCode += '    </div>';
                        loadCode += '</div>';

                        $('#internal-holder').html(loadCode);

                        let params = { q:'feed', filter:'compartilhar', limit:5 };
                        let htmlCode = '';
                        let htmlModal = '';

                        if(filtro != '') {
                            params.vehicles = filtro;
                        }

                        app.request(params, false, null, ( res ) => {
                            $('#internal-holder').html('');
                            
                            if(res.error != null) {
                            } else {
                                let loopNovos = res.feed;

                                if(loopNovos) {
                                    htmlCode += '<div class="grid">';
                                    htmlCode += '    <div class="title">';
                                    htmlCode += '        <h2>novos materiais</h2>';
                                    htmlCode += '    </div>';
                                    htmlCode += '    <div class="selection-panel">';
                                    htmlCode += '        <div class="aux">';
                                    htmlCode += '            <div class="bt">';
                                    htmlCode += '                <a href="#" class="bt-fechar-selection" data-target="selection"></a>';
                                    htmlCode += '            </div>';
                                    htmlCode += '            <div class="num-selection">1 selecionado</div>';
                                    htmlCode += '        </div>';
                                    htmlCode += '        <div class="bts">';
                                    htmlCode += '            <div class="bt">';
                                    htmlCode += '                <a href="#" class="bt-baixar-lote" data-target="materiais">baixar</a>';
                                    htmlCode += '            </div>';
                                    htmlCode += '            <div class="bt">';
                                    htmlCode += '                <a href="#" class="bt-compartilhar-lote" data-target="materiais">compartilhar</a>';
                                    htmlCode += '            </div>';
                                    htmlCode += '        </div>';
                                    htmlCode += '    </div>';
                                    htmlCode += '    <div class="hld-grid">';

                                    loopNovos.forEach(function(item, index) {
                                        htmlCode += '   <div class="post" type="material" reference="story">';
                                        htmlCode += '       <div class="midia">';
                                        htmlCode += '           <a href="#" class="bt-modal" data-target="galeria" data-medium="story" data-sub="' + index + '" data-download="'+ item.content +'" selectable="true">';
                                        if(item.content_type == 'video'){
                                            htmlCode += '           <div class="img" style="background-image: url(\'' + item.thumbnail + '\');"></div>';
                                        } else {
                                            htmlCode += '           <div class="img" style="background-image: url(\'' + item.content + '\');"></div>';
                                        }
                                        htmlCode += '           </a>';
                                        htmlCode += '       </div>';
                                        htmlCode += '   </div>';
                                    })

                                    htmlCode += '    </div>';
                                    htmlCode += '    <div class="bt">';
                                    htmlCode += '        <a href="#" class="bt-mais-cards"></a>';
                                    htmlCode += '    </div>';
                                    htmlCode += '</div>';

                                    loopNovos.forEach(function(item) {   
                                        let saved = false;

                                        if (item.read_later !== undefined) {
                                            if(item.read_later){
                                                saved = true;
                                            }
                                        }

                                        if(item.content_type == 'video'){
                                            htmlModal += '<div class="aux-slide" data-slideID="' + item.id + '" data-slideSaved="' + saved + '" data-slideDownload="' + item.share_link + '" data-slideTitle="' + encodeURIComponent(item.share_message.trim()) + '"  data-slideText=""  data-slideLink="' + encodeURIComponent(item.share_link) + '" data-vehicle="' + item.vehicles + '" data-version="' + item.version + '"><div class="slide" style="background-image:url(\'' + item.thumbnail + '\');"></div></div>';
                                        } else {
                                            htmlModal += '<div class="aux-slide" data-slideID="' + item.id + '" data-slideSaved="' + saved + '" data-slideDownload="' + item.share_link + '" data-slideTitle="' + encodeURIComponent(item.share_message.trim()) + '"  data-slideText=""  data-slideLink="' + encodeURIComponent(item.share_link) + '" data-vehicle="' + item.vehicles + '" data-version="' + item.version + '"><div class="slide" style="background-image:url(\'' + item.content + '\');"></div></div>';
                                        }
                                    })

                                    let params = { q:'feed', filter:'compartilhar', order:'downloaded', limit:5 };

                                    if(filtro != '') {
                                        params.vehicles = filtro;
                                    }

                                    app.request(params, false, null, ( res ) => {
                                        if(res.error != null) {
                                        } else {
                                            let loopBaixados = res.feed;

                                            if(loopBaixados) {
                                                htmlCode += '<div class="grid">';
                                                htmlCode += '    <div class="title">';
                                                htmlCode += '        <h2>mais baixados</h2>';
                                                htmlCode += '    </div>';
                                                htmlCode += '    <div class="hld-grid">';
                                                
                                                loopBaixados.forEach(function(item, index) {
                                                    htmlCode += '   <div class="post" type="material" reference="story">';
                                                    htmlCode += '       <div class="midia">';
                                                    htmlCode += '           <a href="#" class="bt-modal" data-target="galeria" data-medium="story" data-sub="' + (index + 5) + '"  data-download="'+ item.content +'" selectable="true">';
                                                    if(item.content_type == 'video'){
                                                        htmlCode += '           <div class="img" style="background-image: url(\'' + item.thumbnail + '\');"></div>';
                                                    } else {
                                                        htmlCode += '           <div class="img" style="background-image: url(\'' + item.content + '\');"></div>';
                                                    }
                                                    htmlCode += '           </a>';
                                                    htmlCode += '       </div>';
                                                    htmlCode += '   </div>';
                                                })

                                                htmlCode += '    </div>';
                                                htmlCode += '    <div class="bt">';
                                                htmlCode += '        <a href="#" class="bt-mais-cards"></a>';
                                                htmlCode += '    </div>';
                                                htmlCode += '</div>';

                                                $('.loading').remove();
                                                $('#internal-holder').html(htmlCode);

                                                loopBaixados.forEach(function(item) {   
                                                    let saved = false;

                                                    if (item.read_later !== undefined) {
                                                        if(item.read_later){
                                                            saved = true;
                                                        }
                                                    }

                                                    if(item.content_type == 'video'){
                                                        htmlModal += '<div class="aux-slide" data-slideID="' + item.id + '" data-slideSaved="' + saved + '" data-slideDownload="' + item.share_link + '" data-slideTitle="' + encodeURIComponent(item.share_message.trim()) + '"  data-slideText=""  data-slideLink="' + encodeURIComponent(item.share_link) + '" data-vehicle="' + item.vehicles + '" data-version="' + item.version + '"><div class="slide" style="background-image:url(\'' + item.thumbnail + '\');"></div></div>';
                                                    } else {
                                                        htmlModal += '<div class="aux-slide" data-slideID="' + item.id + '" data-slideSaved="' + saved + '" data-slideDownload="' + item.share_link + '" data-slideTitle="' + encodeURIComponent(item.share_message.trim()) + '"  data-slideText=""  data-slideLink="' + encodeURIComponent(item.share_link) + '" data-vehicle="' + item.vehicles + '" data-version="' + item.version + '"><div class="slide" style="background-image:url(\'' + item.content + '\');"></div></div>';
                                                    }
                                                })

                                                $('#galeria .slider').html(htmlModal);
                                            }
                                        }
                                    })

                                }
                            }
                        });
                    }else {
                        let loadingCode = '';

                        if(frame == 'materiais-rdemos' || frame == 'materiais-ficha-de-modelos') {
                            loadingCode += '<div class="grid large loading">';
                            loadingCode += '    <div class="title">';
                            loadingCode += '        <h2 style="width: 236px"></h2>';
                            loadingCode += '    </div>';
                            loadingCode += '    <div class="hld-grid">';

                            for(let i=0; i<6; i++){
                                loadingCode += '   <div class="post" type="material" reference="loading" loading-type="post">';
                                loadingCode += '       <div class="midia"></div>';
                                loadingCode += '   </div>';
                            }

                            loadingCode += '    </div>';
                            loadingCode += '</div>';
                        } else {
                            loadingCode += '<div class="grid loading">';
                            loadingCode += '    <div class="title">';
                            loadingCode += '        <h2 style="width: 236px"></h2>';
                            loadingCode += '    </div>';
                            loadingCode += '    <div class="hld-grid">';

                            for(let i=0; i<10; i++){
                                loadingCode += '   <div class="post" type="material" reference="loading" loading-type="story">';
                                loadingCode += '       <div class="midia"></div>';
                                loadingCode += '   </div>';
                            }

                            loadingCode += '    </div>';
                            loadingCode += '</div>';
                        }

                        $('#internal-holder').html(loadingCode);
                        /* GROUP & SUB GROUP */
                        if(materiaisSUB != null){
                            let checkSub = arrSubs[groupIndex];

                            checkSub.forEach(function(sub) {
                                if(materiaisSUB == util.slugfy(sub.subname)){
                                    subgroup = sub.subid;
                                }
                            })
                        }
                        openGroup = group;
                        openSubGroup = subgroup;
  
                        /* EDITÁVEIS e CARTÃO */
                        if(frame == 'materiais-materiais-editaveis' && materiaisSUB == 'whatsapp' || frame == 'materiais-materiais-editaveis' && materiaisSUB == null) {
                            // $('main').attr('class', 'view-materiais-editaveis-whatsapp');

                            let htmlCode = '';
                            let params = { q:'templates', channel:'1' };

                            if(filtro != '') {
                                params.vehicles = filtro;
                            }

                            app.request(params, false, null, ( res ) => {
                                let tags = res.tags;
                                let loop = res.templates;

                                if(loop) {
                                    htmlCode += '<div class="grid">';

                                    for(const tag of tags) {
                                        let name = tag.name;
                                        let postsId = tag.posts;
                                        let numPosts = postsId.length;
                                        let indexIni = name.indexOf('(');
                                        let clearName = ''
                                        let internalLoop = 0;

                                        if(indexIni > 0) {
                                            clearName = name.substring(0, indexIni);
                                        } else {
                                            clearName = name;
                                        }

                                        htmlCode += '    <div class="title">';
                                        htmlCode += '         <h2>' + clearName + '</h2>';
                                        htmlCode += '    </div>';
                                        htmlCode += '    <div class="hld-grid">';

                                        for(const postId of postsId) {
                                            const post = loop[postId];

                                            if(internalLoop < 5) {
                                                htmlCode += '   <div class="post" type="material" reference="story">';
                                                htmlCode += '       <div class="midia">';
                                                htmlCode += '           <a href="#" class="bt-changeview" data-target="materiais-materiais-editaveis" data-medium="whatsapp" data-sub="' + post.id +'">';
                                                htmlCode += '               <div class="img" style="background-image: url(\'' + post.thumb_content +'\');"></div>';
                                                htmlCode += '           </a>';
                                                htmlCode += '       </div>';
                                                htmlCode += '   </div>';
                                                internalLoop++;
                                            }
                                        }
                                        htmlCode += '    </div>';

                                        if(numPosts > 5) {
                                            htmlCode += '<div class="bt">';
                                            htmlCode += '    <a href="#" class="bt-mais-cards" data-target="materiais" data-medium="'+ tag.id +'" data-sub="1">ver mais</a>';
                                            htmlCode += '</div>';
                                        }
                                    }
                                    htmlCode += '</div>';

                                    $('#internal-holder').html(htmlCode);
                                }
                            });

                            if(mobile) {
                                $('.filtro-mobile').show();
                            }
                        } else if(frame == 'materiais-materiais-editaveis' && materiaisSUB == 'redes-sociais') {
                            // $('main').attr('class', 'view-materiais-editaveis-redes-sociais');

                            $('#submenu-materiais').html(`
                                <div class="hld-inp inp-checkbox">
                                    <input type="checkbox" name="posttype" value="post" checked id="post">
                                    <label class="check-filter" data-check="post" for="post">post (1:1)</label>
                                </div>
                                <div class="hld-inp inp-checkbox">
                                    <input type="checkbox" name="posttype" value="story" checked id="story">
                                    <label class="check-filter" data-check="story" for="story">story (9:16)</label>
                                </div>
                            `)
                            $('#submenu-materiais').show();

                            let htmlCode = '';
                            let params = { q:'templates', channel:'2' };

                            if(filtro != '') {
                                params.vehicles = filtro;
                            }

                            app.request(params, false, null, ( res ) => {
                                let tags = res.tags;
                                let loop = res.templates;

                                if(loop) {
                                    htmlCode += '<div class="grid">';

                                    for(const tag of tags) {
                                        let name = tag.name;
                                        let postsId = tag.posts;
                                        let numPosts = postsId.length;
                                        let indexIni = name.indexOf('(');
                                        let clearName = '';
                                        let internalLoop = 0;

                                        if(indexIni > 0) {
                                            clearName = name.substring(0, indexIni);
                                        } else {
                                            clearName = name;
                                        }

                                        htmlCode += '    <div class="title">';
                                        htmlCode += '         <h2>' + clearName + '</h2>';
                                        htmlCode += '    </div>';
                                        htmlCode += '    <div class="hld-grid">';

                                        for(const postId of postsId) {
                                            const post = loop[postId];

                                            if(internalLoop < 5) {
                                                if(post.format == 1){
                                                    htmlCode += '<div class="post" type="material" reference="story" filter="post">';
                                                } else {
                                                    htmlCode += '<div class="post" type="material" reference="story" filter="story">';
                                                }
                                                htmlCode += '       <div class="midia">';
                                                htmlCode += '           <a href="#" class="bt-changeview" data-target="materiais-materiais-editaveis" data-medium="redes-sociais" data-sub="' + post.id +'">';
                                                htmlCode += '               <div class="img" style="background-image: url(\'' + post.thumb_content +'\');"></div>';
                                                htmlCode += '           </a>';
                                                htmlCode += '       </div>';
                                                htmlCode += '   </div>';
                                                internalLoop++;
                                            }
                                        }

                                        htmlCode += '    </div>';
                                        if(numPosts > 5) {
                                            htmlCode += '<div class="bt">';
                                            htmlCode += '    <a href="#" class="bt-mais-cards" data-target="materiais" data-medium="'+ tag.id +'" data-sub="2">ver mais</a>';
                                            htmlCode += '</div>';
                                        }
                                    }
                                    htmlCode += '</div>';

                                    $('#internal-holder').html(htmlCode);
                                }
                            });

                            if(mobile) {
                                $('.filtro-mobile').show();
                            }
                        } else if(frame == 'materiais-cartao-digital') {
                            // $('main').attr('class', 'view-cartao-digital');

                            $('.filtro-mobile').hide();
                            $('#internal-holder').html('');
                            let htmlCode = '';
                            let img = '';
                            let name = keeper.get('name');
                            let email = keeper.get('email');
                            let cargo = '';
                            let whatsapp = '';
                            let telefone = '';
                            let localizacao = '';
                            let concessionaria = '';

                            let params = { q:'get_digital_card' };
                            app.request(params, false, null, ( res ) => {
                                let dados = res.digital_card;
                                
                                if(dados.name != ''){
                                    name = dados.name;
                                }
                                if(dados.email != ''){
                                    email = dados.email;
                                }

                                img = dados.image;
                                cargo = dados.job;
                                whatsapp = dados.whatsapp;
                                telefone = dados.phone;
                                localizacao = dados.location;
                                concessionaria = dados.concessionaire;

                                htmlCode += '<div class="edicao edit-card">';
                                htmlCode += '    <div class="chamada-mobile">';
                                htmlCode += '        <h2>personalize o seu cartão de visitas digital</h2>';
                                htmlCode += '    </div>';
                                htmlCode += '    <div class="cartao">';
                                htmlCode += '        <div class="topo">';
                                if(img != ''){
                                    htmlCode += '        <div class="foto on" style="background-image: url(\'' + img + '\')">';
                                } else {
                                    htmlCode += '        <div class="foto">';
                                } 
                                htmlCode += '                <div class="bt">';
                                htmlCode += '                    <a href="#" class="bt-modal" data-target="edit-foto" data-medium="cartao"><div class="ico"></div></a>';
                                htmlCode += '                </div>';
                                htmlCode += '            </div>';
                                htmlCode += '        </div>';
                                htmlCode += '        <div class="infos">';
                                htmlCode += '            <div class="nome">'+ name +'</div>';
                                htmlCode += '            <div class="cargo">seu cargo</div>';
                                htmlCode += '        </div>';
                                htmlCode += '        <div class="bts-card">';
                                htmlCode += '            <div class="bt">';
                                htmlCode += '                <div class="ico whatsapp"></div>';
                                htmlCode += '                WhatsApp';
                                htmlCode += '            </div>';
                                htmlCode += '            <div class="bt">';
                                htmlCode += '                <div class="ico telefone"></div>';
                                htmlCode += '                telefone';
                                htmlCode += '            </div>';
                                htmlCode += '            <div class="bt">';
                                htmlCode += '                <div class="ico email"></div>';
                                htmlCode += '                e-mail';
                                htmlCode += '            </div>';
                                htmlCode += '            <div class="bt">';
                                htmlCode += '                <div class="ico localizacao"></div>';
                                htmlCode += '                localização';
                                htmlCode += '            </div>';
                                htmlCode += '        </div>';
                                htmlCode += '        <div class="assinatura">';
                                htmlCode += '            <div class="logo"><img src="assets/images/logo_renault_cartao.png"></div>';
                                htmlCode += '            <div class="dados">';
                                htmlCode += '                <div class="renault">Renault</div>';
                                htmlCode += '                <div class="concessionaria">nome da cc</div>';
                                htmlCode += '            </div>';
                                htmlCode += '        </div>';
                                htmlCode += '    </div>';
                                htmlCode += '    <div class="bts bts-mobile">';
                                htmlCode += '        <div class="bt alt">';
                                htmlCode += '            <a href="#" class="modal-cartao" data-target="cartao">personalizar informações</a>';
                                htmlCode += '        </div>';
                                htmlCode += '    </div>';
                                htmlCode += '    <div class="formulario">';
                                htmlCode += '        <div class="aux-form">';
                                htmlCode += '            <div class="chamada">';
                                htmlCode += '                <h2>personalize o seu cartão de visitas digital</h2>';
                                htmlCode += '            </div>';
                                htmlCode += '            <div class="chamada-mobile chamada-modal">';
                                htmlCode += '                personalize o seu cartão';
                                htmlCode += '                <div class="bt-fechar">';
                                htmlCode += '                    <a href="#" class="bt-fechar-form"></a>';
                                htmlCode += '                </div> ';
                                htmlCode += '            </div>';
                                htmlCode += '            <div class="inputs">';
                                htmlCode += '                <div class="hld-inp">';
                                htmlCode += '                    <input type="text" name="nome" placeholder="nome" value="' + name + '">';
                                htmlCode += '                    <input type="hidden" name="img-card" value="' + img + '">';
                                htmlCode += '                </div>';
                                htmlCode += '                <div class="hld-inp">';
                                htmlCode += '                    <input type="text" name="cargo" placeholder="seu cargo" value="' + cargo + '">';
                                htmlCode += '                </div>';
                                htmlCode += '                <div class="hld-inp">';
                                htmlCode += '                    <div class="inp">';
                                htmlCode += '                        <div class="ico whatsapp"></div>';
                                htmlCode += '                        <input type="text" name="celular" class="mask-tel" placeholder="(&nbsp; &nbsp; &nbsp;) WhatsApp"  value="' + whatsapp + '">';
                                htmlCode += '                    </div>';
                                htmlCode += '                    <div class="inp">';
                                htmlCode += '                        <div class="ico telefone"></div>';
                                htmlCode += '                        <input type="text" name="telefone" class="mask-tel" placeholder="(&nbsp; &nbsp; &nbsp;) telefone"  value="' + telefone + '">';
                                htmlCode += '                    </div>';
                                htmlCode += '                </div>';
                                htmlCode += '                <div class="hld-inp">';
                                htmlCode += '                    <div class="inp">';
                                htmlCode += '                        <div class="ico email"></div>';
                                htmlCode += '                    </div>';
                                htmlCode += '                    <input type="text" name="email" placeholder="e-mail" value="' + email + '">';
                                htmlCode += '                </div>';
                                htmlCode += '                <div class="hld-inp">';
                                htmlCode += '                    <div class="inp">';
                                htmlCode += '                        <div class="ico localizacao"></div>';
                                htmlCode += '                    </div>';
                                htmlCode += '                    <input type="text" name="localizacao" placeholder="localização da concessionária" value="' + localizacao + '">';
                                htmlCode += '                </div>';
                                htmlCode += '                <div class="hld-inp">';
                                htmlCode += '                    <div class="inp">';
                                htmlCode += '                        <div class="ico concessionaria"></div>';
                                htmlCode += '                    </div>';
                                htmlCode += '                    <input type="text" name="concessionaria" maxlength="50" placeholder="nome da cc" value="' + concessionaria + '">';
                                htmlCode += '                </div>';
                                htmlCode += '            </div>';
                                htmlCode += '            <div class="bts bts-desktop">';
                                htmlCode += '                <div class="bt">';
                                htmlCode += '                    <a href="#" class="bt-geracartao" data-target="download">baixar</a>';
                                htmlCode += '                </div>';
                                htmlCode += '                <div class="bt">';
                                htmlCode += '                    <a href="#" class="bt-geracartao" data-target="share">compartilhar</a>';
                                htmlCode += '                </div>';
                                htmlCode += '            </div>';
                                htmlCode += '            <div class="bts bts-mobile">';
                                htmlCode += '                <div class="bt">';
                                htmlCode += '                    <a href="#">cancelar</a>';
                                htmlCode += '                </div>';
                                htmlCode += '                <div class="bt alt">';
                                htmlCode += '                    <a href="#" class="bt-salvar-cartao" data-target="cartao">salvar</a>';
                                htmlCode += '                </div>';
                                htmlCode += '            </div>';
                                htmlCode += '        </div>';
                                htmlCode += '    </div>';
                                htmlCode += '    <div class="bts bts-mobile bts-editado">';
                                htmlCode += '        <div class="bt">';
                                htmlCode += '            <a href="#" class="bt-geracartao" data-target="download">baixar</a>';
                                htmlCode += '        </div>';
                                htmlCode += '        <div class="bt">';
                                htmlCode += '            <a href="#" class="bt-geracartao" data-target="share">compartilhar</a>';
                                htmlCode += '        </div>';
                                htmlCode += '    </div>';
                                htmlCode += '</div>';

                                $('#internal-holder').html(htmlCode);
                            })
                        } else if(frame == 'materiais-imagens-dos-veiculos' && materiaisVeiculo == null) {
                            // $('main').attr('class', 'view-imagens-dos-veiculos');

                            arrVehicles = [];
                            const params = { q:'vehicles' };
                            
                            let page = 0;
                            let paged = 3;

                            app.request(params, false, null, ( res ) => {
                                if(res.error != null) {
                                }else{
                                    let loop = res.vehicles;
                                    loop.forEach(function(item) {
                                        arrVehicles.push({id: item.id, name:item.name, slug: util.slugfy(item.name)});
                                    })

                                    let vehicleID = '';
                                    let vehicleName = '';
                                    let FixedVehicle = util.slugfy(loop[0].name);
                                    $('#lista-filtro .bt-filtro-veiculo').removeClass('on');
                                    $('#lista-filtro .bt-filtro-veiculo[data-name="'+ FixedVehicle +'"]').addClass('on');

                                    for (let i = 0; i < arrVehicles.length; i++) {
                                        if(arrVehicles[i].slug == FixedVehicle) {
                                            vehicleID = arrVehicles[i].id;
                                            vehicleName = arrVehicles[i].name;
                                        }
                                    }

                                    openVehicle = vehicleID;

                                    let menuHTML = '';
                                    const paramsVersions = { q:'vehicleversions', filter:'galeria', vehicles:vehicleID, content_type:'image', group:group };

                                    let countLoop = 0;
                                    let version = '';

                                    app.request(paramsVersions, false, null, ( res ) => {
                                        if(res.error != null) {
                                        }else{
                                            let loop = res.feed;
                                            menuHTML += '<div class="holder">';
                                            menuHTML += '   <h2>versões disponíveis do ' + vehicleName + '</h2>';
                                            menuHTML += '   <div class="bts">';
                                            loop.forEach(function(item) {
                                                let selClass = '';

                                                if(countLoop == 0) {
                                                    selClass = 'selected';
                                                    version = item.version;
                                                }
                                                menuHTML += '   <a href="#" class="bt-changeview bt-image ' + selClass + '" style="background-image: url(\'' + item.thumbnail + '\')" data-target="materiais-imagens-dos-veiculos" data-medium="' + util.slugfy(item.vehicle_name) + '" data-sub="' + util.slugfy(item.version_name) + '">' + item.version_name + '</a>';
                                                countLoop++;
                                            })
                                            menuHTML += '   </div>';
                                            menuHTML += '</div>';

                                            loadMenu = 'versoes';
                                            openVersion = version;

                                            $('#menu-materiais').html(menuHTML);
                                            $('#menu-materiais').addClass('image-menu');
                                            $('#menu-materiais').show();

                                            const paramsImages = { q:'feed', filter:'galeria', vehicles:vehicleID, vehicleversion: version, content_type:'image', group: group };
                                            app.request(paramsImages, false, null, ( res ) => {
                                                if(res.error != null) {
                                                }else{
                                                    $('#submenu-materiais').html(`
                                                        <div class="hld-inp inp-checkbox">
                                                            <input type="checkbox" name="posttype" value="exterior" checked id="exterior">
                                                            <label class="check-filter" data-check="exterior" for="exterior">exterior</label>
                                                        </div>
                                                        <div class="hld-inp inp-checkbox">
                                                            <input type="checkbox" name="posttype" value="interior" checked id="interior">
                                                            <label class="check-filter" data-check="interior" for="interior">interior</label>
                                                        </div>
                                                        <div class="hld-inp inp-checkbox">
                                                            <input type="checkbox" name="posttype" value="lifestyle" checked id="lifestyle">
                                                            <label class="check-filter" data-check="lifestyle" for="lifestyle">lyfestyle</label>
                                                        </div>
                                                    `)
                                                    $('#submenu-materiais').show();

                                                    let imagesHTML = '';

                                                    imagesHTML += '<div class="grid large">';
                                                    imagesHTML += '    <div class="hld-grid">';

                                                    let loopImages = res.feed;
                                                    let countLoop = 0;
                                                    loopImages.forEach(function(item) {
                                                        let imgType = '';
                                                        if(item.image_type == '0' || item.image_type == '1'){
                                                            imgType = 'exterior';
                                                        }
                                                        if(item.image_type == '2'){
                                                            imgType = 'interior';
                                                        }
                                                        if(item.image_type == '3'){
                                                            imgType = 'lifestyle';
                                                        }

                                                        imagesHTML += '<div class="post" type="material" reference="post-img" filter="' + imgType + '">';
                                                        imagesHTML += '    <div class="midia">';
                                                        imagesHTML += '        <a href="#" class="bt-modal" data-target="galeria" data-medium="post" data-sub="'+ countLoop +'">';
                                                        imagesHTML += '            <div class="img" style="background-image: url(\'' + item.content + '\');"></div>';
                                                        imagesHTML += '        </a>';
                                                        imagesHTML += '    </div>';
                                                        imagesHTML += '</div>';

                                                        countLoop++;
                                                    })

                                                    imagesHTML += '    </div>';
                                                    imagesHTML += '</div>';
                                                    if(res.vehicleversions) {
                                                        imagesHTML += '<div class="bt-grid">';
                                                        imagesHTML += '    <a href="' + res.vehicleversions[0].link + '" class="carregar-mais-imagens" target="_blank">mais imagens</a>';
                                                        imagesHTML += '</div>';
                                                    }

                                                    $('#internal-holder').html(imagesHTML);

                                                    let htmlModal = '';
                                                    loopImages.forEach(function(item) {   
                                                        let saved = false;

                                                        if (item.read_later !== undefined) {
                                                            if(item.read_later){
                                                                saved = true;
                                                            }
                                                        }

                                                        htmlModal += '<div class="aux-slide" data-slideID="' + item.id + '" data-slideSaved="' + saved + '" data-slideDownload="' + item.share_link + '" data-slideTitle="' + encodeURIComponent(item.share_message.trim()) + '"  data-slideText=""  data-slideLink="' + encodeURIComponent(item.share_link) + '" data-vehicle="' + item.vehicles + '" data-version="' + item.version + '"><div class="slide" style="background-image:url(\'' + item.content + '\');"></div></div>';
                                                    })

                                                    $('#galeria .slider').html(htmlModal);
                                                }
                                            });

                                        }
                                    })

                                }
                            })

                            if(mobile) {
                                $('.filtro-mobile').show();
                            }
                        } else if(frame == 'materiais-imagens-dos-veiculos' && materiaisVeiculo != null && materiaisVersao == null) {
                            arrVehicles = [];
                            const params = { q:'vehicles' };
                            
                            let page = 0;
                            let paged = 3;

                            app.request(params, false, null, ( res ) => {
                                if(res.error != null) {
                                }else{
                                  let loop = res.vehicles;
                                  loop.forEach(function(item) {
                                      arrVehicles.push({id: item.id, name:item.name, slug: util.slugfy(item.name)});
                                  })

                                    let vehicleID = '';
                                    let vehicleName = '';

                                    for (let i = 0; i < arrVehicles.length; i++) {
                                        if(arrVehicles[i].slug == materiaisVeiculo) {
                                            vehicleID = arrVehicles[i].id;
                                            vehicleName = arrVehicles[i].name;

                                        }
                                    }

                                    openVehicle = vehicleID;
                                    // $('main').attr('class', 'view-imagens-dos-veiculos-' + vehicleName);

                                    let menuHTML = '';
                                    const paramsVersions = { q:'vehicleversions', filter:'galeria', vehicles:vehicleID, content_type:'image', group:group };
                                    let countLoop = 0;
                                    let version = '';

                                    app.request(paramsVersions, false, null, ( res ) => {
                                        if(res.error != null) {
                                        }else{
                                            let loop = res.feed;
                                            menuHTML += '<div class="holder">';
                                            menuHTML += '   <h2>versões disponíveis do ' + vehicleName + '</h2>';
                                            menuHTML += '   <div class="bts">';
                                            loop.forEach(function(item) {
                                                let selClass = '';

                                                if(countLoop == 0) {
                                                    selClass = 'selected';
                                                    version = item.version;
                                                }
                                                menuHTML += '   <a href="#" class="bt-changeview bt-image ' + selClass + '" style="background-image: url(\'' + item.thumbnail + '\')" data-target="materiais-imagens-dos-veiculos" data-medium="' + util.slugfy(item.vehicle_name) + '" data-sub="' + util.slugfy(item.version_name) + '">' + item.version_name + '</a>';
                                                countLoop++;
                                            })
                                            menuHTML += '   </div>';
                                            menuHTML += '</div>';

                                            loadMenu = 'versoes';
                                            openVersion = version;

                                            $('#menu-materiais').html(menuHTML);
                                            $('#menu-materiais').addClass('image-menu');
                                            $('#menu-materiais').show();

                                            const paramsImages = { q:'feed', filter:'galeria', vehicles:vehicleID, vehicleversion: version, content_type:'image', group:group };
                                            app.request(paramsImages, false, null, ( res ) => {
                                                if(res.error != null) {
                                                }else{
                                                    $('#submenu-materiais').html(`
                                                        <div class="hld-inp inp-checkbox">
                                                            <input type="checkbox" name="posttype" value="exterior" checked id="exterior">
                                                            <label class="check-filter" data-check="exterior" for="exterior">exterior</label>
                                                        </div>
                                                        <div class="hld-inp inp-checkbox">
                                                            <input type="checkbox" name="posttype" value="interior" checked id="interior">
                                                            <label class="check-filter" data-check="interior" for="interior">interior</label>
                                                        </div>
                                                        <div class="hld-inp inp-checkbox">
                                                            <input type="checkbox" name="posttype" value="lifestyle" checked id="lifestyle">
                                                            <label class="check-filter" data-check="lifestyle" for="lifestyle">lyfestyle</label>
                                                        </div>
                                                    `)
                                                    $('#submenu-materiais').show();

                                                    let imagesHTML = '';

                                                    imagesHTML += '<div class="grid large">';
                                                    imagesHTML += '    <div class="hld-grid">';

                                                    let loopImages = res.feed;
                                                    let countLoop = 0;
                                                    loopImages.forEach(function(item) {
                                                        let imgType = '';
                                                        if(item.image_type == '0' || item.image_type == '1'){
                                                            imgType = 'exterior';
                                                        }
                                                        if(item.image_type == '2'){
                                                            imgType = 'interior';
                                                        }
                                                        if(item.image_type == '3'){
                                                            imgType = 'lifestyle';
                                                        }

                                                        imagesHTML += '<div class="post" type="material" reference="post-img" filter="' + imgType + '">';
                                                        imagesHTML += '    <div class="midia">';
                                                        imagesHTML += '        <a href="#" class="bt-modal" data-target="galeria" data-medium="post" data-sub="'+ countLoop +'">';
                                                        imagesHTML += '            <div class="img" style="background-image: url(\'' + item.content + '\');"></div>';
                                                        imagesHTML += '        </a>';
                                                        imagesHTML += '    </div>';
                                                        imagesHTML += '</div>';

                                                        countLoop++;
                                                    })

                                                    imagesHTML += '    </div>';
                                                    imagesHTML += '</div>';
                                                    if(res.vehicleversions) {
                                                        imagesHTML += '<div class="bt-grid">';
                                                        imagesHTML += '    <a href="' + res.vehicleversions[0].link + '" class="carregar-mais-imagens" target="_blank">mais imagens</a>';
                                                        imagesHTML += '</div>';
                                                    }

                                                    $('#internal-holder').html(imagesHTML);

                                                    let htmlModal = '';
                                                    loopImages.forEach(function(item) {   
                                                        let saved = false;

                                                        if (item.read_later !== undefined) {
                                                            if(item.read_later){
                                                                saved = true;
                                                            }
                                                        }

                                                        htmlModal += '<div class="aux-slide" data-slideID="' + item.id + '" data-slideSaved="' + saved + '" data-slideDownload="' + item.share_link + '" data-slideTitle="' + encodeURIComponent(item.share_message.trim()) + '"  data-slideText=""  data-slideLink="' + encodeURIComponent(item.share_link) + '" data-vehicle="' + item.vehicles + '" data-version="' + item.version + '"><div class="slide" style="background-image:url(\'' + item.content + '\');"></div></div>';
                                                    })

                                                    $('#galeria .slider').html(htmlModal);
                                                }
                                            });

                                        }
                                    })

                                }
                            })

                            if(mobile) {
                                $('.filtro-mobile').show();
                            }
                        } else if(frame == 'materiais-imagens-dos-veiculos' && materiaisVeiculo != null && materiaisVersao != null) {
                            $('#menu-materiais').html('');

                            arrVehicles = [];
                            let arrVersions = [];
                            const params = { q:'vehicles' };

                            let page = 0;
                            let paged = 1;

                            app.request(params, false, null, ( res ) => {
                                if(res.error != null) {
                                }else{
                                  let loop = res.vehicles;
                                  loop.forEach(function(item) {
                                      arrVehicles.push({id: item.id, name:item.name, slug: util.slugfy(item.name)});
                                  })

                                    let vehicleID = '';
                                    let vehicleName = '';

                                    for (let i = 0; i < arrVehicles.length; i++) {
                                        if(arrVehicles[i].slug == materiaisVeiculo) {
                                            vehicleID = arrVehicles[i].id;
                                            vehicleName = arrVehicles[i].name;
                                        }
                                    }

                                    openVehicle = vehicleID;

                                    let menuHTML = '';
                                    const paramsVersions = { q:'vehicleversions', filter:'galeria', vehicles:vehicleID, content_type:'image', group:group };
                                    let countLoop = 0;
                                    let version = '';

                                    app.request(paramsVersions, false, null, ( res ) => {
                                        if(res.error != null) {
                                        }else{
                                            let loop = res.feed;

                                            loop.forEach(function(item) {
                                                arrVersions.push({id: item.version, name:item.version_name, slug: util.slugfy(item.version_name)});
                                            })


                                            for (let i = 0; i < arrVersions.length; i++) {
                                                if(arrVersions[i].slug == materiaisVersao) {
                                                    version = arrVersions[i].id;
                                                }
                                            }

                                            // $('main').attr('class', 'view-imagens-dos-veiculos-' + vehicleName + '-' + version);

                                            menuHTML += '<div class="holder">';
                                            menuHTML += '   <h2>versões disponíveis do Renault ' + vehicleName + '</h2>';
                                            menuHTML += '   <div class="bts">';
                                            loop.forEach(function(item) {
                                                let selClass = '';

                                                if(item.version == version) {
                                                    selClass = 'selected';
                                                }
                                                menuHTML += '   <a href="#" class="bt-changeview bt-image ' + selClass + '" style="background-image: url(\'' + item.thumbnail + '\')" data-target="materiais-imagens-dos-veiculos" data-medium="' + util.slugfy(item.vehicle_name) + '" data-sub="' + util.slugfy(item.version_name) + '">' + item.version_name + '</a>';
                                                countLoop++;
                                            })
                                            menuHTML += '   </div>';
                                            menuHTML += '</div>';

                                            openVersion = version;

                                            $('#menu-materiais').html(menuHTML);
                                            $('#menu-materiais').addClass('image-menu');
                                            $('#menu-materiais').show();


                                            const paramsImages = { q:'feed', filter:'galeria', vehicles:vehicleID, vehicleversion: version, content_type:'image', group:group };
                                            app.request(paramsImages, false, null, ( res ) => {
                                                if(res.error != null) {
                                                }else{
                                                    $('#submenu-materiais').html(`
                                                        <div class="hld-inp inp-checkbox">
                                                            <input type="checkbox" name="posttype" value="exterior" checked id="exterior">
                                                            <label class="check-filter" data-check="exterior" for="exterior">exterior</label>
                                                        </div>
                                                        <div class="hld-inp inp-checkbox">
                                                            <input type="checkbox" name="posttype" value="interior" checked id="interior">
                                                            <label class="check-filter" data-check="interior" for="interior">interior</label>
                                                        </div>
                                                        <div class="hld-inp inp-checkbox">
                                                            <input type="checkbox" name="posttype" value="lifestyle" checked id="lifestyle">
                                                            <label class="check-filter" data-check="lifestyle" for="lifestyle">lifestyle</label>
                                                        </div>
                                                    `)
                                                    $('#submenu-materiais').show();

                                                    let imagesHTML = '';

                                                    imagesHTML += '<div class="grid large">';
                                                    imagesHTML += '    <div class="hld-grid">';

                                                    let loopImages = res.feed;
                                                    let countLoop = 0;
                                                    loopImages.forEach(function(item) {
                                                        let imgType = '';
                                                        if(item.image_type == '0' || item.image_type == '1'){
                                                            imgType = 'exterior';
                                                        }
                                                        if(item.image_type == '2'){
                                                            imgType = 'interior';
                                                        }
                                                        if(item.image_type == '3'){
                                                            imgType = 'lifestyle';
                                                        }

                                                        imagesHTML += '<div class="post" type="material" reference="post-img" filter="' + imgType + '">';
                                                        imagesHTML += '    <div class="midia">';
                                                        imagesHTML += '        <a href="#" class="bt-modal" data-target="galeria" data-medium="post" data-sub="' + countLoop + '">';
                                                        imagesHTML += '            <div class="img" style="background-image: url(\'' + item.content + '\');"></div>';
                                                        imagesHTML += '        </a>';
                                                        imagesHTML += '    </div>';
                                                        imagesHTML += '</div>';

                                                        countLoop++;
                                                    })

                                                    imagesHTML += '    </div>';
                                                    imagesHTML += '</div>';
                                                    if(res.vehicleversions) {
                                                        imagesHTML += '<div class="bt-grid">';
                                                        imagesHTML += '    <a href="' + res.vehicleversions[0].link + '" class="carregar-mais-imagens" target="_blank">mais imagens</a>';
                                                        imagesHTML += '</div>';
                                                    }
                                                    $('#internal-holder').html(imagesHTML);

                                                    let htmlModal = '';
                                                    loopImages.forEach(function(item) {   
                                                        let saved = false;

                                                        if (item.read_later !== undefined) {
                                                            if(item.read_later){
                                                                saved = true;
                                                            }
                                                        }

                                                        htmlModal += '<div class="aux-slide" data-slideID="' + item.id + '" data-slideSaved="' + saved + '" data-slideDownload="' + item.share_link + '" data-slideTitle="' + encodeURIComponent(item.share_message.trim()) + '"  data-slideText=""  data-slideLink="' + encodeURIComponent(item.share_link) + '" data-vehicle="' + item.vehicles + '" data-version="' + item.version + '"><div class="slide" style="background-image:url(\'' + item.content + '\');"></div></div>';
                                                    })

                                                    $('#galeria .slider').html(htmlModal);
                                                }
                                            });
                                        }
                                    })

                                }
                            })

                            if(mobile) {
                                $('.filtro-mobile').show();
                            }
                        } else if(frame == 'materiais-ficha-de-modelos' && materiaisVeiculo == null){
                            $('main').attr('class', 'view-ficha-de-modelos');
                            
                            $('#menu-materiais').hide();
                            let htmlCode = '';
                            
                            arrVehicles = [];
                            const params = { q:'vehicles' };

                            app.request(params, false, null, ( res ) => {
                                if(res.error != null) {
                                }else{
                                    let loop = res.vehicles;
                                    loop.forEach(function(item) {
                                        arrVehicles.push({id: item.id, name:item.name, slug: util.slugfy(item.name)});
                                    })

                                    let vehicleID = '';
                                    let vehicleName = '';
                                    let FixedVehicle = util.slugfy(loop[0].name);
                                    $('#lista-filtro .bt-filtro-veiculo').removeClass('on');

                                    for (let i = 0; i < arrVehicles.length; i++) {
                                        if(arrVehicles[i].slug == FixedVehicle) {
                                            vehicleID = arrVehicles[i].id;
                                            vehicleName = arrVehicles[i].name;
                                        }
                                    }

                                    openVehicle = vehicleID;

                                    let menuHTML = '';
                                    const paramsViews = { q:'quick_views' };

                                    let countLoop = 0;
                                    let version = '';

                                    app.request(paramsViews, false, null, ( res ) => {
                                        if(res.error != null) {
                                        }else{
                                            let loop = res.quick_views;
                                            htmlCode += '<div class="grid large">';
                                            htmlCode += '    <div class="title">';
                                            htmlCode += '        <h2>confira as principais informações dos veículos</h2>';
                                            htmlCode += '    </div>';
                                            htmlCode += '   <div class="hld-grid">';

                                            loop.forEach(function(item) {
                                                htmlCode += '      <div class="post" type="material" reference="post-link">';
                                                htmlCode += '           <div class="midia">';
                                                htmlCode += '               <a href="#" class="bt-changeview" data-target="materiais-ficha-de-modelos" data-medium="' + util.slugfy(item.vehicle_name) + '" data-timing="false">';
                                                htmlCode += '                   <div class="img" style="background-image: url(\'' + item.image_highlight + '\');"></div>';
                                                htmlCode += '               </a>';
                                                htmlCode += '           </div>';
                                                htmlCode += '      </div>';
                                            })

                                            htmlCode += '   </div>';
                                            htmlCode += '</div>';


                                            $('.loading').remove();
                                            $('#internal-holder').html(htmlCode);

                                        }
                                    })
                                }
                            })
                        } else if(frame == 'materiais-ficha-de-modelos' && materiaisVeiculo != null){
                                // $('main').attr('class', 'view-ficha-de-modelos');
                                $('#menu-materiais').hide();
                                $('#lista-filtro .bt-filtro-veiculo').removeClass('on');
                                $('#lista-filtro .bt-filtro-veiculo[data-name="'+ materiaisVeiculo +'"]').addClass('on');

                                arrVehicles = [];
                                const params = { q:'vehicles' };

                                app.request(params, false, null, ( res ) => {
                                    if(res.error != null) {
                                    }else{
                                        let loop = res.vehicles;
                                        loop.forEach(function(item) {
                                              arrVehicles.push({id: item.id, name:item.name, slug: util.slugfy(item.name)});
                                        })

                                        let vehicleID = '';
                                        let vehicleName = '';

                                        for (let i = 0; i < arrVehicles.length; i++) {
                                            if(arrVehicles[i].slug == materiaisVeiculo) {
                                                vehicleID = arrVehicles[i].id;
                                                vehicleName = arrVehicles[i].name;
                                            }
                                        }

                                        openVehicle = vehicleID;

                                        let htmlCode = '';
                                        let htmlModal = '';
                                        const paramsVersions = { q:'quick_views', vehicle:vehicleID };
                                        let countLoop = 0;
                                        let firtsTab = '';
                                        let arrFilters = [];

                                        app.request(paramsVersions, false, null, ( res ) => {
                                            if(res.error != null) {
                                            }else{
                                                let loop = res.quick_views;
                                                loop.forEach(function(item) {
                                                    htmlCode += '<div class="onepage">';
                                                    htmlCode += '    <div class="banner" style="background-image:url(\'' + item.image + '\')">';
                                                    htmlCode += '       <div class="bt-voltar"><a href="#" class="bt-changeview" data-target="materiais-ficha-de-modelos"><div class="ico"><img src="assets/images/ico_arrow_left.svg"></div>Voltar</a></div>';
                                                    htmlCode += '    </div>';
                                                    let name = item.name;
                                                    if(name){
                                                        htmlCode += '    <div class="secao">';
                                                        htmlCode += '       <div class="titulo">escrita do nome</div>';
                                                        htmlCode += '       <div class="conteudo">' + item.name + '</div>';
                                                        htmlCode += '    </div>';
                                                    }
                                                    let main_sentence = item.main_sentence;
                                                    if(main_sentence){
                                                        htmlCode += '    <div class="secao">';
                                                        htmlCode += '       <div class="titulo">frase principal</div>';
                                                        htmlCode += '       <div class="conteudo">' + item.main_sentence + '</div>';
                                                        htmlCode += '    </div>';
                                                    }
                                                    htmlCode += '    <div class="secao-acc">';
                                                    let message = item.message.replace(/(<([^>]+)>)/gi, "");
                                                    if(message != ''){
                                                        htmlCode += '       <div class="area-acc">';
                                                        htmlCode += '           <div class="titulo"><a href="#" class="bt-acc" data-target="secao-acc">apresentação de produto</a></div>';
                                                        htmlCode += '           <div class="conteudo">' + item.message + '</div>';
                                                        htmlCode += '       </div>';
                                                    }
                                                    let strengths = item.strengths.replace(/(<([^>]+)>)/gi, "");
                                                    if(strengths != ''){
                                                        htmlCode += '       <div class="area-acc">';
                                                        htmlCode += '           <div class="titulo"><a href="#" class="bt-acc" data-target="secao-acc">mapa das features</a></div>';
                                                        htmlCode += '           <div class="conteudo">' + item.strengths + '</div>';
                                                        htmlCode += '       </div>';
                                                    }
                                                    // let attributes = item.attributes.replace(/(<([^>]+)>)/gi, "");
                                                    // if(attributes != ''){
                                                    //     htmlCode += '       <div class="area-acc">';
                                                    //     htmlCode += '           <div class="titulo"><a href="#" class="bt-acc" data-target="secao-acc">atributos</a></div>';
                                                    //     htmlCode += '           <div class="conteudo">' + item.attributes + '</div>';
                                                    //     htmlCode += '       </div>';
                                                    // }
                                                    let selos = item.stamps;
                                                    if(selos){
                                                        htmlCode += '       <div class="area-acc">';
                                                        htmlCode += '           <div class="titulo"><a href="#" class="bt-acc" data-target="secao-acc">selos</a></div>';
                                                        htmlCode += '           <div class="conteudo">';
                                                            selos.forEach(function(selo) { 
                                                                htmlCode += '         <p><strong>•  ' + selo.name + '</strong> – ' + selo.text.replace(/(<([^>]+)>)/gi, "") + '</p>';
                                                            })

                                                        htmlCode += '             <div class="selos">';
                                                        selos.forEach(function(selo) { 
                                                            htmlCode += '           <img src="' + selo.image + '"/>';
                                                        })
                                                        htmlCode += '             </div>';

                                                        htmlCode += '           </div>';
                                                        htmlCode += '       </div>';
                                                    }
                                                    let legal_text = item.legal_text.replace(/(<([^>]+)>)/gi, "");
                                                    if(legal_text != ''){
                                                        htmlCode += '       <div class="area-acc">';
                                                        htmlCode += '           <div class="titulo"><a href="#" class="bt-acc" data-target="secao-acc">texto jurídico (TJ)</a></div>';
                                                        htmlCode += '           <div class="conteudo">' + item.legal_text + '</div>';
                                                        htmlCode += '       </div>';
                                                    }
                                                    htmlCode += '    </div>';
                                                    let hldGroups = res.groups;
                                                    if(hldGroups){
                                                        let groups = res.groups.groups_quick_view_tags;
                                                        if(groups){
                                                            htmlCode += '    <div class="menu">';
                                                            htmlCode += '       <div class="aux">';
                                                            htmlCode += '           <div class="aux-scroll">';
                                                            groups.forEach(function(group) { 
                                                                    htmlCode += '       <a href="#" class="bt-filter" data-check="group-' + group.id + '" data-target="filter">' + group.name + '</a>';
                                                                    if(countLoop == 0) {
                                                                        firtsTab = 'group-' + group.id;
                                                                    }
                                                                    countLoop++;
                                                            })
                                                            htmlCode += '               <div class="dash"></div>';
                                                            htmlCode += '           </div>';
                                                            htmlCode += '       </div>';
                                                            htmlCode += '    </div>';

                                                            groups.forEach(function(group) { 
                                                                htmlCode += '<div class="submenu filtered" filtergroup="group-' + group.id + '">';
                                                                let subgroups = group.subgroups;
                                                                subgroups.forEach(function(subgroup) { 
                                                                    htmlCode += '       <div class="hld-inp inp-checkbox hld-filter" filtersubgroup="' + subgroup.id + '">';
                                                                    htmlCode += '           <input type="checkbox" name="posttype" data-group="group-' + group.id + '" value="check-group-' + group.id + '-subgroup-' + subgroup.id  + '" checked id="' + util.slugfy(group.name) + '-' + util.slugfy(subgroup.name) + '">';
                                                                    htmlCode += '           <label class="bt-filter" data-check="subgroup-' + subgroup.id + '" for="' + util.slugfy(group.name) + '-' + util.slugfy(subgroup.name) + '">' + subgroup.name + '</label>';
                                                                    htmlCode += '       </div>';
                                                                })

                                                                htmlCode += '</div>';
                                                            })
                                                        }
                                                    }
                                                    htmlCode += '    <div class="grid">';
  
                                                    let tags = res.tags;
                                                    let materials = item.materials;
                                                    let index = 0;

                                                    if(tags) {
                                                        for(const tag of tags) {
                                                            let name = tag.name;
                                                            let postsId = tag.materials;   
                                                            let numPosts = postsId.length;
                                                            let indexIni = name.indexOf('(');
                                                            let clearName = ''
                                                            let internalLoop = 0;

                                                            if(indexIni > 0) {
                                                                clearName = name.substring(0, indexIni);
                                                            } else {
                                                                clearName = name;
                                                            }

                                                            htmlCode += '       <div class="title filtered" filtergroup="group-' + tag.tag_group + '" filtersubgroup="subgroup-' + tag.tag_subgroup + '">';
                                                            htmlCode += '           <h2>' + clearName + '</h2>';
                                                            htmlCode += '       </div>';


                                                            htmlCode += '    <div class="hld-grid filtered" filtergroup="group-' + tag.tag_group + '" filtersubgroup="subgroup-' + tag.tag_subgroup + '">';

                                                            arrFilters.push([tag.tag_group, tag.tag_subgroup]);

                                                            for(const postId of postsId) {
                                                                const post = materials[postId];

                                                                if(internalLoop < 5) {
                                                                    htmlCode += '   <div class="post" type="material" reference="story">';
                                                                    htmlCode += '       <div class="midia">';
                                                                    htmlCode += '           <a href="#" class="bt-modal" data-target="galeria" data-medium="story" data-sub="' + index + '" data-download="'+ post.content +'" data-unique="true" data-timing="false">';
                                                                    htmlCode += '               <div class="img" style="background-image: url(\'' + post.image_highlight +'\');"></div>';
                                                                    htmlCode += '           </a>';
                                                                    htmlCode += '       </div>';
                                                                    htmlCode += '   </div>';
                                                                    internalLoop++;
                                                                    index++;

                                                                     
                                                                    //htmlModal += '<div class="aux-slide" data-slideID="' + post.id + '" data-slideDownload="' + post.file + '" data-slideLink="' + encodeURIComponent(post.file) + '""><div class="slide" style="background-image:url(\'' + post.image + '\');"></div></div>';
                                                                    

                                                                    if(post.content_type == 'video'){
                                                                        htmlModal += '<div class="aux-slide" data-slideID="' + post.id + '" data-slideDownload="' + post.content + '" data-slideTitle="' + encodeURIComponent(post.name.trim()) + '"  data-slideText=""  data-slideLink="' + encodeURIComponent(post.content) + '" data-vehicle="' + post.vehicles + '" data-version="' + post.version + '">';
                                                                        htmlModal += '  <div class="slide sld-video" style="background-image:url(\'' + post.image_highlight + '\');">';
                                                                        htmlModal += '      <div class="hld-video">';
                                                                        htmlModal += '          <div class="source"></div>';
                                                                        htmlModal += '      </div>';
                                                                        htmlModal += '      <a href="#" class="bt-video-modal" data-target="video" data-video="' + post.content + '">';
                                                                        htmlModal += '          <div class="img" style="background-image: url(\'' + post.image_highlight + '\');"></div>';
                                                                        htmlModal += '      </a>';
                                                                        htmlModal += '  </div>';
                                                                        htmlModal += '</div>';
                                                                    } else if(post.pdf != null){
                                                                        htmlModal += '<div class="aux-slide" data-slideID="' + post.id + '" data-slideDownload="' + post.content + '" data-slideTitle="' + encodeURIComponent(post.name.trim()) + '"  data-slideText=""  data-slideLink="' + encodeURIComponent(post.content) + '" data-vehicle="' + post.vehicles + '" data-version="' + post.version + '">';
                                                                        htmlModal += '  <div class="slide" style="background-image:url(\'' + post.image_highlight + '\');">';

                                                                        htmlModal += '    <div class="midia midia-pdf">';
                                                                        htmlModal += '       <div class="aux-scroll" id="pdf-' + post.id + '" data-pdf="' + post.pdf + '">';
                                                                        htmlModal += '           <div class="loader-panel"><div></div><span></span></div>';
                                                                        htmlModal += '       </div>';
                                                                        htmlModal += '    </div>';

                                                                        htmlModal += '  </div>';
                                                                        htmlModal += '</div>';
                                                                    } else {
                                                                        htmlModal += '<div class="aux-slide" data-slideID="' + post.id + '" data-slideDownload="' + post.content + '" data-slideTitle="' + encodeURIComponent(post.name.trim()) + '"  data-slideText=""  data-slideLink="' + encodeURIComponent(post.content) + '" data-vehicle="' + post.vehicles + '" data-version="' + post.version + '"><div class="slide" style="background-image:url(\'' + post.image_highlight + '\');"></div></div>';
                                                                    }

                                                                    holderCountModal++;
                                                                }
                                                            }
                                                            htmlCode += '    </div>';

                                                            if(numPosts > 5) {
                                                                htmlCode += '<div class="bt filtered" filtergroup="group-' + tag.tag_group + '" filtersubgroup="subgroup-' + tag.tag_subgroup + '">';
                                                                htmlCode += '    <a href="#" class="bt-mais-ficha" data-target="' + openVehicle + '" data-medium="' + tag.tag_group + '" data-sub="' + tag.tag_subgroup + '">ver mais</a>';
                                                                htmlCode += '</div>';
                                                            }
                                                        }


                                                    }
                                                    htmlCode += '   </div>';
                                                    htmlCode += '</div>';
                                                });


                                                $('.loading').remove();
                                                $('#internal-holder').append(htmlCode);
                                                $('#galeria .slider').html(htmlModal);
                                                $('#galeria .bt-salvar a').hide();

                                                holderCodeModal = htmlModal;
                                                
                                                arrFilters.forEach(function(filter, index){
                                                    $('[filtergroup="group-' + filter[0] + '"] [filtersubgroup="' + filter[1] + '"]').addClass('on');
                                                })

                                                btContentFilter(firtsTab, 'init');
                                            }
                                        })
                                    }
                                });



                        } else {
                            let page = 0;
                            let paged = 10;
                            let type = 'story';
                            let grid = '';

                            /* PAGINAÇÃO */
                            if(frame == 'materiais-rdemos') {
                                paged = 6;
                                type = 'post';
                                grid = 'large';
                            }

                            // $('main').attr('class', 'view-' + frame);

                            let params = { q:'feed', group:group, subgroup:subgroup, image_no_crop: 1 };

                            if(filtro != '') {
                                params.vehicles = filtro;
                            }

                            app.request(params, false, null, ( res ) => {
                                if(res.error != null) {
                                } else {
                                    montaPagina(res, group, subgroup, grid, type, paged);
                                }
                            });

                            if(mobile) {
                                $('.filtro-mobile').show();
                            }
                        }

                        oldFrame = frame;
                        oldSUB = materiaisSUB;
                        oldVersao = materiaisVersao;
                        oldVeiculo = materiaisVeiculo;

                        if(filtro != '') {
                            oldFiltro = window.filtro;
                        }
                    }

                }

            }


            if(materiaisSUB){
                $('#menu-materiais .bt-changeview').removeClass('selected');
                $('#menu-materiais .bt-changeview[data-medium="' + materiaisSUB + '"]').addClass('selected');
            } else {
                $('#menu-materiais .bt-changeview:first-child').addClass('selected');
            }

            reloadMenu();


        } else { 
            waitFrame = frame; 
        }

        
        
    }
}
const makeMATERIAIS = new MATERIAIS();
window.materiais = makeMATERIAIS;

function montaPagina(res, group, subgroup, grid, type, paged){
    let htmlCode = '';
    let htmlModal = '';
    let countLoop = 0;

    let tags = res.tags;
    let loop = res.feed;

    if(loop) {
        htmlCode += '<div class="grid ' + grid + '">';

        for(const tag of tags) {
            let name = tag.name;
            let postsId = tag.posts;
            let indexIni = name.indexOf('(');
            let clearName = ''
            let internalLoop = 0;

            if(indexIni > 0) {
                clearName = name.substring(0, indexIni);
            } else {
                clearName = name;
            }

            htmlCode += '    <div class="title">';
            htmlCode += '        <h2>' + clearName + '</h2>';
            htmlCode += '    </div>';
            htmlCode += '    <div class="hld-grid">';

            for(const postId of postsId) {
                const post = loop[postId];

                if(post) {
                    if(paged != 10) {
                        if(internalLoop < paged) {
                            if(post.content_type == 'video'){
                                htmlCode += '<div class="post" type="material" reference="'+ type +'-video">';
                            } else {
                                htmlCode += '<div class="post" type="material" reference="'+ type +'">';
                            }
                            htmlCode += '        <div class="midia">';
                            htmlCode += '            <a href="#" class="bt-modal" data-target="galeria" data-medium="'+ type +'" data-sub="' + ((page * paged) + countLoop) +  '" data-timing="false">';
                            if(post.content_type == 'video'){
                                htmlCode += '            <div class="img" style="background-image: url(\'' + post.thumbnail + '\');"></div>';
                            } else {
                                htmlCode += '            <div class="img" style="background-image: url(\'' + post.content + '\');"></div>';
                            }
                            htmlCode += '            </a>';
                            htmlCode += '        </div>';
                            htmlCode += '    </div>';

                            let saved = false;

                            if (post.read_later !== undefined) {
                                if(post.read_later){
                                    saved = true;
                                }
                            }

                            if(post.content_type == 'video'){
                                htmlModal += '<div class="aux-slide" data-slideID="' + post.id + '" data-slideSaved="' + saved + '" data-slideDownload="' + post.share_link + '" data-slideTitle="' + encodeURIComponent(post.share_message.trim()) + '"  data-slideText=""  data-slideLink="' + encodeURIComponent(post.share_link) + '" data-vehicle="' + post.vehicles + '" data-version="' + post.version + '">';
                                htmlModal += '  <div class="slide sld-video" style="background-image:url(\'' + post.thumbnail + '\');">';
                                htmlModal += '      <div class="hld-video">';
                                htmlModal += '          <div class="source"></div>';
                                htmlModal += '      </div>';
                                htmlModal += '      <a href="#" class="bt-video-modal" data-target="video" data-video="' + post.content + '">';
                                htmlModal += '          <div class="img" style="background-image: url(\'' + post.thumbnail + '\');"></div>';
                                htmlModal += '      </a>';
                                htmlModal += '  </div>';
                                htmlModal += '</div>';
                            } else if(post.pdf != null){
                                htmlModal += '<div class="aux-slide" data-slideID="' + post.id + '" data-slideSaved="' + saved + '" data-slideDownload="' + post.share_link + '" data-slideTitle="' + encodeURIComponent(post.share_message.trim()) + '"  data-slideText=""  data-slideLink="' + encodeURIComponent(post.share_link) + '" data-vehicle="' + post.vehicles + '" data-version="' + post.version + '">';
                                htmlModal += '  <div class="slide" style="background-image:url(\'' + post.content + '\');">';

                                htmlModal += '    <div class="midia midia-pdf">';
                                htmlModal += '       <div class="aux-scroll" id="pdf-' + post.id + '" data-pdf="' + post.pdf + '">';
                                htmlModal += '           <div class="loader-panel"><div></div><span></span></div>';
                                htmlModal += '       </div>';
                                htmlModal += '    </div>';

                                htmlModal += '  </div>';
                                htmlModal += '</div>';
                            } else {
                                htmlModal += '<div class="aux-slide" data-slideID="' + post.id + '" data-slideSaved="' + saved + '" data-slideDownload="' + post.share_link + '" data-slideTitle="' + encodeURIComponent(post.share_message.trim()) + '"  data-slideText=""  data-slideLink="' + encodeURIComponent(post.share_link) + '" data-vehicle="' + post.vehicles + '" data-version="' + post.version + '"><div class="slide" style="background-image:url(\'' + post.content + '\');"></div></div>';
                            }


                            internalLoop++;
                            countLoop++;
                        }

                    } else {
                        if(internalLoop < 5) {
                            if(post.content_type == 'video'){
                                htmlCode += '<div class="post" type="material" reference="'+ type +'-video">';
                            } else {
                                htmlCode += '<div class="post" type="material" reference="'+ type +'">';
                            }
                            htmlCode += '        <div class="midia">';
                            htmlCode += '            <a href="#" class="bt-modal" data-target="galeria" data-medium="'+ type +'" data-sub="' + ((page * paged) + countLoop) +  '" data-timing="false">';
                            if(post.content_type == 'video'){
                                htmlCode += '            <div class="img" style="background-image: url(\'' + post.thumbnail + '\');"></div>';
                            } else {
                                htmlCode += '            <div class="img" style="background-image: url(\'' + post.content + '\');"></div>';
                            }
                            htmlCode += '            </a>';
                            htmlCode += '        </div>';
                            htmlCode += '    </div>';

                            let saved = false;

                            if (post.read_later !== undefined) {
                                if(post.read_later){
                                    saved = true;
                                }
                            }

                            if(post.content_type == 'video'){
                                htmlModal += '<div class="aux-slide" data-slideID="' + post.id + '" data-slideSaved="' + saved + '" data-slideDownload="' + post.share_link + '" data-slideTitle="' + encodeURIComponent(post.share_message.trim()) + '"  data-slideText=""  data-slideLink="' + encodeURIComponent(post.share_link) + '" data-vehicle="' + post.vehicles + '" data-version="' + post.version + '">';
                                htmlModal += '  <div class="slide sld-video" style="background-image:url(\'' + post.thumbnail + '\');">';
                                htmlModal += '      <div class="hld-video">';
                                htmlModal += '          <div class="source"></div>';
                                htmlModal += '      </div>';
                                htmlModal += '      <a href="#" class="bt-video-modal" data-target="video" data-video="' + post.content + '">';
                                htmlModal += '          <div class="img" style="background-image: url(\'' + post.thumbnail + '\');"></div>';
                                htmlModal += '      </a>';
                                htmlModal += '  </div>';
                                htmlModal += '</div>';
                            } else if(post.pdf != null){
                                htmlModal += '<div class="aux-slide" data-slideID="' + post.id + '" data-slideSaved="' + saved + '" data-slideDownload="' + post.share_link + '" data-slideTitle="' + encodeURIComponent(post.share_message.trim()) + '"  data-slideText=""  data-slideLink="' + encodeURIComponent(post.share_link) + '" data-vehicle="' + post.vehicles + '" data-version="' + post.version + '">';
                                htmlModal += '  <div class="slide" style="background-image:url(\'' + post.content + '\');">';

                                htmlModal += '    <div class="midia midia-pdf">';
                                htmlModal += '       <div class="aux-scroll" id="pdf-' + post.id + '" data-pdf="' + post.pdf + '">';
                                htmlModal += '           <div class="loader-panel"><div></div><span></span></div>';
                                htmlModal += '       </div>';
                                htmlModal += '    </div>';

                                htmlModal += '  </div>';
                                htmlModal += '</div>';
                            } else {
                                htmlModal += '<div class="aux-slide" data-slideID="' + post.id + '" data-slideSaved="' + saved + '" data-slideDownload="' + post.share_link + '" data-slideTitle="' + encodeURIComponent(post.share_message.trim()) + '"  data-slideText=""  data-slideLink="' + encodeURIComponent(post.share_link) + '" data-vehicle="' + post.vehicles + '" data-version="' + post.version + '"><div class="slide" style="background-image:url(\'' + post.content + '\');"></div></div>';
                            }

                            internalLoop++;
                            countLoop++;
                        }
                    }
                }
            }
            htmlCode += '    </div>';
            if(paged == 10) {
                if(tag.posts.length > 5){
                    htmlCode += '    <div class="bt">';
                    htmlCode += '        <a href="#" class="bt-mais-cards" data-target="' + group + '" data-medium="' + subgroup + '" data-sub="' + tag.id + '">ver mais</a>';
                    htmlCode += '    </div>';
                } else {
                    htmlCode += '    <div class="space-bt"></div>';
                }
            }
        }
        htmlCode += '</div>';

        if(paged != 10) {
            if(paged < loop.length) {
                htmlCode += '<div class="bt-grid">';
                htmlCode += '    <a href="#" class="carregar-mais" data-target="'+ group +'" data-medium="' + subgroup + '" data-sub="' + paged + '">carregar mais</a>';
                htmlCode += '</div>';
            }
        }

        $('#galeria .slider').html(htmlModal);
        $('#internal-holder').html(htmlCode);
    }
}

function loadFicha(target, group, subgroup){
    let params =  { q:'quick_views', vehicle:target, group: group, subgroup: subgroup };

    let htmlCode = '';
    let htmlModal = holderCodeModal;

    app.request(params, false, null, ( res ) => {
        let htmlCode = '';
        let index = 1;
        let internalLoop = 5;

        let tag = res.tags[0];
        let postsId = tag.materials;
        let materials = res.quick_views[0].materials;

        for(const post of materials) {

            if(index > internalLoop){
                htmlCode += '   <div class="post" type="material" reference="story">';
                htmlCode += '       <div class="midia">';
                htmlCode += '           <a href="#" class="bt-modal" data-target="galeria" data-medium="story" data-sub="' + (holderCountModal + internalLoop) + '" data-download="'+ post.content +'" data-unique="true" data-timing="false">';
                htmlCode += '               <div class="img" style="background-image: url(\'' + post.image_highlight +'\');"></div>';
                htmlCode += '           </a>';
                htmlCode += '       </div>';
                htmlCode += '   </div>';
                 
                if(post.content_type == 'video'){
                    htmlModal += '<div class="aux-slide" data-slideID="' + post.id + '" data-slideDownload="' + post.content + '" data-slideTitle="' + encodeURIComponent(post.name.trim()) + '"  data-slideText=""  data-slideLink="' + encodeURIComponent(post.content) + '" data-vehicle="' + post.vehicles + '" data-version="' + post.version + '">';
                    htmlModal += '  <div class="slide sld-video" style="background-image:url(\'' + post.image_highlight + '\');">';
                    htmlModal += '      <div class="hld-video">';
                    htmlModal += '          <div class="source"></div>';
                    htmlModal += '      </div>';
                    htmlModal += '      <a href="#" class="bt-video-modal" data-target="video" data-video="' + post.content + '">';
                    htmlModal += '          <div class="img" style="background-image: url(\'' + post.image_highlight + '\');"></div>';
                    htmlModal += '      </a>';
                    htmlModal += '  </div>';
                    htmlModal += '</div>';
                } else if(post.pdf != null){
                    htmlModal += '<div class="aux-slide" data-slideID="' + post.id + '" data-slideDownload="' + post.content + '" data-slideTitle="' + encodeURIComponent(post.name.trim()) + '"  data-slideText=""  data-slideLink="' + encodeURIComponent(post.content) + '" data-vehicle="' + post.vehicles + '" data-version="' + post.version + '">';
                    htmlModal += '  <div class="slide" style="background-image:url(\'' + post.image_highlight + '\');">';

                    htmlModal += '    <div class="midia midia-pdf">';
                    htmlModal += '       <div class="aux-scroll" id="pdf-' + post.id + '" data-pdf="' + post.pdf + '">';
                    htmlModal += '           <div class="loader-panel"><div></div><span></span></div>';
                    htmlModal += '       </div>';
                    htmlModal += '    </div>';

                    htmlModal += '  </div>';
                    htmlModal += '</div>';
                } else {
                    htmlModal += '<div class="aux-slide" data-slideID="' + post.id + '" data-slideDownload="' + post.content + '" data-slideTitle="' + encodeURIComponent(post.name.trim()) + '"  data-slideText=""  data-slideLink="' + encodeURIComponent(post.content) + '" data-vehicle="' + post.vehicles + '" data-version="' + post.version + '"><div class="slide" style="background-image:url(\'' + post.image_highlight + '\');"></div></div>';
                }


                internalLoop++;
                holderCountModal++;
            }

            index++;
        }



        $('.hld-grid[filtergroup="group-' + group + '"][filtersubgroup="subgroup-' + subgroup + '"]').append(htmlCode);
        $('.bt[filtergroup="group-' + group + '"][filtersubgroup="subgroup-' + subgroup + '"]').attr('filtergroup', '').attr('filtersubgroup', '').hide();
        if($('#galeria .slider').hasClass('slick-slider')){
            $('#galeria .slider').slick('unslick');
        }
        $('#galeria .slider').html(htmlModal);

        holderCodeModal = htmlModal;
    })
}

function loadCards(target, group, subgroup){
    let params =  '';

    if(target == 'materiais') {
        params = { q:'templates', channel:subgroup, tag: group };
    } else {
        params = { q:'feed', tags: subgroup };
    }

    let htmlCode = '';
    let htmlModal = '';

    if(filtro != '') {
        params.vehicles = filtro;
    }

    app.request(params, false, null, ( res ) => {
        let loop = '';
        let tags = res.tags;
        let countLoop = 0;

        if(target == 'materiais') {
            loop = res.templates;
        } else {
            loop = res.feed;
        }

        if(loop) {
            htmlCode += '<div class="grid">';

            for(const tag of tags) {
                let name = tag.name;
                let postsId = tag.posts;
                let indexIni = name.indexOf('(');
                let clearName = ''

                if(indexIni > 0) {
                    clearName = name.substring(0, indexIni);
                } else {
                    clearName = name;
                }

                htmlCode += '    <div class="title">';
                htmlCode += '         <h2>' + clearName + '</h2>';
                htmlCode += '    </div>';
                htmlCode += '    <div class="hld-grid">';

                for(const postId of postsId) {
                    const post = loop[postId];

                    if(target == 'materiais') {    
                        htmlCode += '   <div class="post" type="material" reference="story">';
                        htmlCode += '       <div class="midia">';
                        htmlCode += '           <a href="#" class="bt-changeview" data-target="materiais-materiais-editaveis" data-medium="whatsapp" data-sub="' + post.id +'">';
                        htmlCode += '               <div class="img" style="background-image: url(\'' + post.thumb_content +'\');"></div>';
                        htmlCode += '           </a>';
                        htmlCode += '       </div>';
                        htmlCode += '   </div>';
                    } else {
                        if(post.content_type == 'video'){
                            htmlCode += '<div class="post" type="material" reference="story-video">';
                        } else {
                            htmlCode += '<div class="post" type="material" reference="story">';
                        }
                        htmlCode += '        <div class="midia">';
                        htmlCode += '            <a href="#" class="bt-modal" data-target="galeria" data-medium="story" data-sub="' + countLoop +  '" data-timing="false">';
                        if(post.content_type == 'video'){
                            htmlCode += '            <div class="img" style="background-image: url(\'' + post.thumbnail + '\');"></div>';
                        } else {
                            htmlCode += '            <div class="img" style="background-image: url(\'' + post.content + '\');"></div>';
                        }
                        htmlCode += '            </a>';
                        htmlCode += '        </div>';
                        htmlCode += '    </div>';
                        countLoop++;
                    }

                }

                htmlCode += '    </div>';
            }
            htmlCode += '</div>';

            loop.forEach(function(item) {   
                let saved = false;

                if (item.read_later !== undefined) {
                    if(item.read_later){
                        saved = true;
                    }
                }

                if(item.content_type == 'video'){
                    htmlModal += '<div class="aux-slide" data-slideID="' + item.id + '" data-slideSaved="' + saved + '" data-slideDownload="' + item.share_link + '" data-slideTitle="' + encodeURIComponent(item.share_message.trim()) + '"  data-slideText=""  data-slideLink="' + encodeURIComponent(item.share_link) + '" data-vehicle="' + item.vehicles + '" data-version="' + item.version + '">';
                    htmlModal += '  <div class="slide sld-video" style="background-image:url(\'' + item.thumbnail + '\');">';
                    htmlModal += '      <div class="hld-video">';
                    htmlModal += '          <div class="source"></div>';
                    htmlModal += '      </div>';
                    htmlModal += '      <a href="#" class="bt-video-modal" data-target="video" data-video="' + item.content + '">';
                    htmlModal += '          <div class="img" style="background-image: url(\'' + item.thumbnail + '\');"></div>';
                    htmlModal += '      </a>';
                    htmlModal += '  </div>';
                    htmlModal += '</div>';
                } else if(item.pdf != null){
                    htmlModal += '<div class="aux-slide" data-slideID="' + item.id + '" data-slideSaved="' + saved + '" data-slideDownload="' + item.share_link + '" data-slideTitle="' + encodeURIComponent(item.share_message.trim()) + '"  data-slideText=""  data-slideLink="' + encodeURIComponent(item.share_link) + '" data-vehicle="' + item.vehicles + '" data-version="' + item.version + '">';
                    htmlModal += '  <div class="slide" style="background-image:url(\'' + item.content + '\');">';

                    htmlModal += '    <div class="midia midia-pdf">';
                    htmlModal += '       <div class="aux-scroll" id="pdf-' + item.id + '" data-pdf="' + item.pdf + '">';
                    htmlModal += '           <div class="loader-panel"><div></div><span></span></div>';
                    htmlModal += '       </div>';
                    htmlModal += '    </div>';

                    htmlModal += '  </div>';
                    htmlModal += '</div>';
                } else {
                    htmlModal += '<div class="aux-slide" data-slideID="' + item.id + '" data-slideSaved="' + saved + '" data-slideDownload="' + item.share_link + '" data-slideTitle="' + encodeURIComponent(item.share_message.trim()) + '"  data-slideText=""  data-slideLink="' + encodeURIComponent(item.share_link) + '" data-vehicle="' + item.vehicles + '" data-version="' + item.version + '"><div class="slide" style="background-image:url(\'' + item.content + '\');"></div></div>';
                }
            })

            if($('#galeria .slider').hasClass('slick-initialized')){
                $('#galeria .slider').slick('unslick');
                $('#galeria .slider').html(htmlModal);
            } else {
                $('#galeria .slider').html(htmlModal);
            }

            $('#internal-holder').html(htmlCode);
        }
    });
}

function loadImages(target, paged){
    page++;
    const paramsImages = { q:'feed', filter:'galeria', vehicles:openVehicle, vehicleversion: openVersion, content_type:'image', group:openGroup, page:page, page_limit:paged };
    let htmlCode = '';
    let htmlModal = '';

    app.request(paramsImages, false, null, ( res ) => {
        if(res.error != null) {
        } else {
            let loopImages = res.feed;

            if(loopImages.length > 0) {
                loopImages.forEach(function(item, index) {
                    let imgType = '';
                    if(item.image_type == '0' || item.image_type == '1'){
                        imgType = 'exterior';
                    }
                    if(item.image_type == '2'){
                        imgType = 'interior';
                    }
                    if(item.image_type == '3'){
                        imgType = 'lifestyle';
                    }

                    htmlCode += '<div class="post" type="material" reference="post-img" filter="' + imgType + '">';
                    htmlCode += '    <div class="midia">';
                    htmlCode += '        <a href="#" class="bt-modal" data-target="galeria" data-medium="post" data-sub="' + ((page * paged) + index) +  '">';
                    htmlCode += '            <div class="img" style="background-image: url(\'' + item.content + '\');"></div>';
                    htmlCode += '        </a>';
                    htmlCode += '    </div>';
                    htmlCode += '</div>';
                })

                loopImages.forEach(function(item) {   
                    let saved = false;

                    if (item.read_later !== undefined) {
                        if(item.read_later){
                            saved = true;
                        }
                    }

                    htmlModal += '<div class="aux-slide" data-slideID="' + item.id + '" data-slideSaved="' + saved + '" data-slideDownload="' + item.share_link + '" data-slideTitle="' + encodeURIComponent(item.share_message.trim()) + '"  data-slideText=""  data-slideLink="' + encodeURIComponent(item.share_link) + '" data-vehicle="' + item.vehicles + '" data-version="' + item.version + '"><div class="slide" style="background-image:url(\'' + item.content + '\');"></div></div>';
                })

                if($('#galeria .slider').hasClass('slick-initialized')){
                    $('#galeria .slider').slick('unslick');
                    $('#galeria .slider').append(htmlModal);
                } else {
                    $('#galeria .slider').append(htmlModal);
                }

                $('#internal-holder .hld-grid').append(htmlCode);

                if(loopImages.length < paged) {
                    $('#internal-holder').append('<div class="txt-fim">Isso é tudo! &nbsp;&nbsp;&nbsp; :)</div>');
                    $('.carregar-mais-imagens').parent().hide();
                }
            } else {
                $('#internal-holder').append('<div class="txt-fim">Isso é tudo! &nbsp;&nbsp;&nbsp; :)</div>');
                $('.carregar-mais-imagens').parent().hide();
            }

        }
    });
}

function loadMore(group, subgroup, paged){
    page++;

    let type = '';
    let limit = 10;
    if(frameLoaded == 'materiais-rdemos') {
        type = 'post';
        limit = 6;
    } else {
        type = 'story';
    }

    let params = { q:'feed', group:group, subgroup:subgroup, page:page, page_limit:limit };
    let htmlCode = '';
    let htmlModal = '';

    app.request(params, false, null, ( res ) => {
        if(res.error != null) {
        } else {
            let loopPosts = res.feed;

            if(loopPosts.length > 0) {
                loopPosts.forEach(function(item, index) {
                    if(item.content_type == 'video'){
                        htmlCode += '<div class="post" type="material" reference="'+ type +'-video">';
                    } else {
                        htmlCode += '<div class="post" type="material" reference="'+ type +'">';
                    }
                    htmlCode += '        <div class="midia">';
                    htmlCode += '            <a href="#" class="bt-modal" data-target="galeria" data-medium="'+ type +'" data-sub="' + ((page * paged) + index) +  '" data-timing="false">';
                    if(item.content_type == 'video'){
                        htmlCode += '            <div class="img" style="background-image: url(\'' + item.thumbnail + '\');"></div>';
                    } else {
                        htmlCode += '            <div class="img" style="background-image: url(\'' + item.content + '\');"></div>';
                    }
                    htmlCode += '            </a>';
                    htmlCode += '        </div>';
                    htmlCode += '    </div>';
                })

                loopPosts.forEach(function(item) {   
                    let saved = false;

                    if (item.read_later !== undefined) {
                        if(item.read_later){
                            saved = true;
                        }
                    }

                    if(item.content_type == 'video'){
                        htmlModal += '<div class="aux-slide" data-slideID="' + item.id + '" data-slideSaved="' + saved + '" data-slideDownload="' + item.share_link + '" data-slideTitle="' + encodeURIComponent(item.share_message.trim()) + '"  data-slideText=""  data-slideLink="' + encodeURIComponent(item.share_link) + '" data-vehicle="' + item.vehicles + '" data-version="' + item.version + '">';
                        htmlModal += '  <div class="slide sld-video" style="background-image:url(\'' + item.thumbnail + '\');">';
                        htmlModal += '      <div class="hld-video">';
                        htmlModal += '          <div class="source"></div>';
                        htmlModal += '      </div>';
                        htmlModal += '      <a href="#" class="bt-video-modal" data-target="video" data-video="' + item.content + '">';
                        htmlModal += '          <div class="img" style="background-image: url(\'' + item.thumbnail + '\');"></div>';
                        htmlModal += '      </a>';
                        htmlModal += '  </div>';
                        htmlModal += '</div>';
                    } else if(item.pdf != null){
                        htmlModal += '<div class="aux-slide" data-slideID="' + item.id + '" data-slideSaved="' + saved + '" data-slideDownload="' + item.share_link + '" data-slideTitle="' + encodeURIComponent(item.share_message.trim()) + '"  data-slideText=""  data-slideLink="' + encodeURIComponent(item.share_link) + '" data-vehicle="' + item.vehicles + '" data-version="' + item.version + '">';
                        htmlModal += '  <div class="slide" style="background-image:url(\'' + item.content + '\');">';

                        htmlModal += '    <div class="midia midia-pdf">';
                        htmlModal += '       <div class="aux-scroll" id="pdf-' + item.id + '" data-pdf="' + item.pdf + '">';
                        htmlModal += '           <div class="loader-panel"><div></div><span></span></div>';
                        htmlModal += '       </div>';
                        htmlModal += '    </div>';

                        htmlModal += '  </div>';
                        htmlModal += '</div>';
                    } else {
                        htmlModal += '<div class="aux-slide" data-slideID="' + item.id + '" data-slideSaved="' + saved + '" data-slideDownload="' + item.share_link + '" data-slideTitle="' + encodeURIComponent(item.share_message.trim()) + '"  data-slideText=""  data-slideLink="' + encodeURIComponent(item.share_link) + '" data-vehicle="' + item.vehicles + '" data-version="' + item.version + '"><div class="slide" style="background-image:url(\'' + item.content + '\');"></div></div>';
                    }
                })

                if($('#galeria .slider').hasClass('slick-initialized')){
                    $('#galeria .slider').slick('unslick');
                    $('#galeria .slider').append(htmlModal);
                } else {
                    $('#galeria .slider').append(htmlModal);
                }

                $('#internal-holder .hld-grid').append(htmlCode);

                if(loopPosts.length < paged) {
                    $('#internal-holder').append('<div class="txt-fim">Isso é tudo! &nbsp;&nbsp;&nbsp; :)</div>');
                    $('.carregar-mais').parent().hide();
                }
            } else {
                $('#internal-holder').append('<div class="txt-fim">Isso é tudo! &nbsp;&nbsp;&nbsp; :)</div>');
                $('.carregar-mais').parent().hide();
            }
        }
    });
}


function checkContentFilter(changed, type){
    let selectedPosts = [];

    if(type == 'change') {
        let wait = setTimeout(function(){
          clearTimeout(wait);
            $('input[group="posttype"]').each(function(){
                if($(this).is(':checked')){
                    selectedPosts.push($(this).val());
                }
            });

            if(selectedPosts.length <= 1) {
                $('[value="' + selectedPosts[0] + '"]').parent().addClass('disabled');
            } else {
                for (var i = 0; i < selectedPosts.length; i++) {
                    $('[value="' + selectedPosts[i] + '"]').parent().removeClass('disabled');
                }
            }

            if($('[value="' + changed + '"]').is(':checked')){
                $('[filter="' + changed + '"]').show();
            } else {
                $('[filter="' + changed + '"]').hide();
            }
        }, 100);
    }
}

let groupSelected = '';
function btContentFilter(changed, type){
    let selectedPosts = [];

    if(type == 'change') {
        let wait = setTimeout(function(){
          clearTimeout(wait);

          let tipo = changed.substring(0,3);


          if(tipo == 'sub') {
            $('[filtergroup="' + groupSelected + '"] .inp-checkbox.on input').each(function(){
                if($(this).is(':checked')){
                    selectedPosts.push($(this).val());
                }
            });

            if(selectedPosts.length <= 1) {
                $('[value="' + selectedPosts[0] + '"]').parent().addClass('disabled');
            } else {
                for (var i = 0; i < selectedPosts.length; i++) {
                    $('[value="' + selectedPosts[i] + '"]').parent().removeClass('disabled');
                }
            }

            if($('[value="check-' + groupSelected + '-' + changed + '"]').is(':checked')){
                $('[filtergroup="' + groupSelected + '"][filtersubgroup="' + changed + '"]').show();
            } else {
                $('[filtergroup="' + groupSelected + '"][filtersubgroup="' + changed + '"]').hide();
            }
            $('.submenu[filtergroup="' + groupSelected + '"]').show();

          } else {
            $('.filtered').hide();
            
            $('.bt-filter').removeClass('selected');
            $('.bt-filter[data-check="' + changed + '"]').addClass('selected');
            $('[filtergroup="' + changed + '"]').show();

            $('[filtergroup="' + changed + '"] .inp-checkbox.on input').each(function(){
                $(this).prop('checked', true);
                if($(this).is(':checked')){
                    selectedPosts.push($(this).val());
                }
            });

            if(selectedPosts.length <= 1) {
                $('[value="' + selectedPosts[0] + '"]').parent().addClass('disabled');
            } else {
                for (var i = 0; i < selectedPosts.length; i++) {
                    $('[value="' + selectedPosts[i] + '"]').parent().removeClass('disabled');
                }
            }


            let targetOBJ = $('.menu .bt-filter.selected');

            if(targetOBJ.length > 0){
                let position = targetOBJ.position();
                let width = targetOBJ.outerWidth() - 32;

                $('.menu .dash').css({'left': Math.floor(position.left + 16) + 'px', 'width': width + 'px'});
                $('.menu .dash').removeClass('hard');
            }

            groupSelected = changed;
          }
        }, 100);
    } else {
        $('.filtered').hide();

        $('[filtergroup="' + changed + '"] .inp-checkbox.on input').each(function(){
            if($(this).is(':checked')){
                selectedPosts.push($(this).val());
            }
        });

        if(selectedPosts.length <= 1) {
            $('[value="' + selectedPosts[0] + '"]').parent().addClass('disabled');
        } else {
            for (var i = 0; i < selectedPosts.length; i++) {
                $('[value="' + selectedPosts[i] + '"]').parent().removeClass('disabled');
            }
        }

        $('[filtergroup="' + changed + '"]').show();
        $('.submenu[filtergroup="' + changed + '"]').show();

        $('.bt-filter').removeClass('selected');
        $('.bt-filter[data-check="' + changed + '"]').addClass('selected');


        let targetOBJ = $('.menu .bt-filter.selected');

        if(targetOBJ.length > 0){
            let position = targetOBJ.position();
            let width = targetOBJ.outerWidth() - 32;

            $('.menu .dash').css({'left': Math.floor(position.left + 16) + 'px', 'width': width + 'px'});
            $('.menu .dash').removeClass('hard');
        }

        groupSelected = changed;
    }
}

function montaCard(type){
    $('#cards').addClass('on');

    html2canvas(document.querySelector("#cards"), {
        allowTaint: true,
        useCORS: true
    }).then(function(canvas) {
        $.ajax({
            url: 'actions/imagem.php',
            type: 'POST',
            dataType: 'json',
            data: {
                img: canvas.toDataURL()
            },
            success: function (data) {
                $('#cards').removeClass('on');

                if(type == 'download') {
                    util.download(BASE_URL + 'download/img/'+ data.name +'.jpg')
                } else {
                    manualShare('','', BASE_URL + 'download/img/'+ data.name +'.jpg');
                }
            },
            error: function(data){
                console.log(data);
            }
        });
    })
}

function montaCartao(type){
    let error = 0;
    let img = $('.inputs [name="img-card"]').val();
    let nome = $('.inputs [name="nome"]').val();
    let cargo = $('.inputs [name="cargo"]').val();
    let whatsapp = $('.inputs [name="celular"]').val();
    let telefone = $('.inputs [name="telefone"]').val();
    let email = $('.inputs [name="email"]').val();
    let localizacao = $('.inputs [name="localizacao"]').val();
    let concessionaria = $('.inputs [name="concessionaria"]').val();

    $('.hld-inp').removeClass('error');
    $('.inp').removeClass('error');
    
    if(nome == ''){
        $('.inputs input[name="nome"]').parent().addClass('error');
        error++;
    }
    if(cargo == ''){
        $('.inputs input[name="cargo"]').parent().addClass('error');
        error++;
    }
    if(whatsapp == '' || whatsapp.length < 14){
        $('.inputs input[name="celular"]').parent().addClass('error');
        error++;
    }
    if(telefone == '' || telefone.length < 14){
        $('.inputs input[name="telefone"]').parent().addClass('error');
        error++;
    }
    if(email == '' || !validateEmail(email)){
        $('.inputs input[name="email"]').parent().addClass('error');
        error++;
    }
    if(localizacao == ''){
        $('.inputs input[name="localizacao"]').parent().addClass('error');
        error++;
    }
    if(concessionaria == ''){
        $('.inputs input[name="concessionaria"]').parent().addClass('error');
        error++;
    }
    if(error > 0){
        return;
    }
    
    const params = { q:'set_digital_card', name: nome, job: cargo, whatsapp: whatsapp, phone: telefone, email: email, location: localizacao, concessionaire: concessionaria };
        
    app.request(params, false, null, ( res ) => {
        if(res.error != null) {
        }else{
            let ajuste = 25;
            let posBaseL = $('.assinatura').position().left;
            let posBaseT = $('.assinatura').position().top;
            let widBase = $('.assinatura').width();
            let posLogo = $('.assinatura .logo').position().left - ajuste;
            let posDadosL = $('.assinatura .dados').position().left;
            let posDadosT = $('.assinatura .dados').position().top;
            let logoLeft = ((posLogo - posBaseL) * 100) / widBase;
            let dadosLeft = ((posDadosL - posBaseL) * 100) / widBase;
            let dadosTop = ((posDadosT - posBaseT) * 4) + 60;

            let numberPattern = /\d+/g;

            whatsapp = 'https://api.whatsapp.com/send?phone=550' + $('.inputs [name="celular"]').val().match( numberPattern ).join('');
            telefone = 'tel:0' + $('.inputs [name="telefone"]').val().match( numberPattern ).join('');
            localizacao = 'https://www.google.com.br/maps/dir//' + encodeURI($('.inputs [name="localizacao"]').val());

            $.ajax({
                url: 'actions/cartao.php',
                type: 'POST',
                dataType: 'json',
                data: {
                    img: img,
                    nome: nome,
                    cargo: cargo,
                    whatsapp: whatsapp,
                    telefone: telefone,
                    email: email,
                    localizacao: localizacao,
                    concessionaria: concessionaria,
                    leftlogo: logoLeft,
                    leftDados: dadosLeft,
                    topDados: dadosTop,
                },
                success: function (data) {
                    if(type == 'download') {
                        util.download(BASE_URL + 'download/pdf/'+ data.name +'.pdf');
                    } else {
                        if (navigator.canShare) {
                            let link = BASE_URL + 'download/pdf/'+ data.name +'.pdf';
                            let archivename = data.name + '.pdf';
                            shareArchive(link, archivename);
                        } else {
                            manualShare('','', BASE_URL + 'download/pdf/'+ data.name +'.pdf');
                        }
                    }
                },
                error: function(data){
                    console.log(data);
                }
            });
        }
    });
}

$(document).on('input', '.edicao .inputs input[type="text"]', function (event) {
    let name = $(this).attr('name');
    let value = $(this).val();

    if(name == 'nome' && value == '') {
        value = 'Nome';
    }
    if(name == 'cargo' && value == '') {
        value = 'seu cargo';
    }
    if(name == 'concessionaria' && value == '') {
        value = 'nome da cc';
    }

    $('.edicao .cartao .' + name).html(value);
})


$(document).on('input', '.edicao.materiais input', function (event) {
    let name = $(this).attr('name');
    let placeholder = $(this).attr('placeholder');
    let value = $(this).val();

    if(value == '') {
        value = placeholder;
    }

    $('.edicao .midia .custom-text.' + name).html(value);
    $('#cards .txt.' + name).html(value);
})

$(document).on('input', '.edicao.materiais textarea', function (event) {
    let name = $(this).attr('name');
    let placeholder = $(this).attr('placeholder');
    let value = $(this).val();

    if(value == '') {
        value = placeholder;
    }

    $('.edicao .midia .custom-text.' + name).html(value);
    $('#cards .txt.' + name).html(value);
})

$(document).on('focus', '.edicao.materiais [name="valor"]', function (event) {
    let name = $(this).attr('name');
    let value = $(this).val().replace('R$ ', '');
    let placeholder = $(this).attr('placeholder').replace('R$ ', '');

    if(value == '') {
        value = '0,00';
    }

    $('.edicao .midia .custom-text.' + name).html(value);
    $('#cards .txt.' + name).html(value);
})

$(document).on('blur', '.edicao.materiais [name="valor"]', function (event) {
    let name = $(this).attr('name');
    let value = $(this).val().replace('R$ ', '');
    let placeholder = $(this).attr('placeholder').replace('R$ ', '');

    if(value == '0,00' || value == '') {
        value = placeholder;
    }

    $('.edicao .midia .custom-text.' + name).html(value);
    $('#cards .txt.' + name).html(value);
})

$(document).on('keyup', '.edicao.materiais [name="valor"]', function (event) {
    let name = $(this).attr('name');
    let placeholder = $(this).attr('placeholder').replace('R$ ', '');
    let value = $(this).val().replace('R$ ', '');

    if(value == '') {
        value = placeholder;
    }

    $('.edicao .midia .custom-text.' + name).html(value);
    $('#cards .txt.' + name).html(value);
})


function reloadMenu() {
    let targetOBJ = $('#menu-materiais .bt-changeview.selected');

    if(targetOBJ.length > 0){
        let position = targetOBJ.position();
        let width = targetOBJ.outerWidth() - 32;

        $('#menu-materiais .dash').css({'left': Math.floor(position.left + 16) + 'px', 'width': width + 'px'});
        $('#menu-materiais .dash').removeClass('hard');
    }
}

let SPMaskBehavior = function (val) {
  return val.replace(/\D/g, '').length === 11 ? '(00) 00000-0000' : '(00) 0000-00009';
},
spOptions = {
  onKeyPress: function(val, e, field, options) {
      field.mask(SPMaskBehavior.apply({}, arguments), options);
    }
};
$('.mask-tel').mask(SPMaskBehavior, spOptions);

function validateEmail(email) {
  var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
  return re.test(email);
}