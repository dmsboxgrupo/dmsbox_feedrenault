/* TEMPLATES */
const BANNERS_TEMPLATE  =   '<div class="banner" style="background-image: url(\'${this.content}\');">'+
                            '   <a href="#" class="bt-changeview" data-target="campanha" data-sub="${this.link_campaign}">'+
                            '     <div class="titulo">'+
                            '       <h4>${this.name}</h4>'+
                            '       ${this.text}'+
                            '     </div>'+
                            '  </a>'+
                            '</div>';

let frameLoaded = '';
let sidebarLoaded = '';
let activeArea = '';
let baseLoaded = false;

class BASE {
  constructor(){
    const params = { q:'feed', notifications:'true' };
    app.request(params, false, null, ( res ) => {
        if(res.error != null) {
        }else{
          let htmlCode = '';
          let loop = res.feed;

          if(loop.length > 99) {
            $('nav .notificacao .num').text('99+');
            $('nav .notificacao .num').addClass('on');
          } else {
            if(loop.length > 0) {
              $('nav .notificacao .num').text(loop.length);
              $('nav .notificacao .num').addClass('on');
            }
          }          
        }
    })

    // const paramsCamapnha = { q:'feed', filter:'campanha'};
    const paramsCamapnha = { q:'material_library_tags', type:'campaign' };
    app.request(paramsCamapnha, false, null, ( res ) => {
        if(res.error != null) {
        }else{
          let htmlCode = '';
          let loopTags = res.material_library_tags;

          loopTags.forEach(function(item) { 
            let name = item.name;       
            let slugURL = util.slugfy(item.name);

            let campanhaURL = 'campanha-' + slugURL;
            window.hash.push({internal: campanhaURL, holder: 'campanhas', hash: campanhaURL});

             htmlCode += '<div class="bt">';
             htmlCode += '  <a href="#" class="bt-changeview" data-target="campanha-' + slugURL + '">';
             htmlCode +=      name;
             htmlCode += '  </a>';
             htmlCode += '</div>';
          })

          $('.sub-content.campanhas').html(htmlCode);
        }
    })


    const paramsMateriais = { q:'material_library_tags'};
    app.request(paramsMateriais, false, null, ( res ) => {
        if(res.error != null) {
        }else{
          let htmlCode = '';
          let loopGroups = res.material_library_tags;

          window.baseLoaded = true;

          loopGroups.forEach(function(item) { 
            let id = item.id;       
            let name = item.name;       
            let slugURL = util.slugfy(item.name);

            let materiaisURL = 'materiais-' + slugURL;
            window.hash.push({internal: materiaisURL, holder: 'materiais', hash: materiaisURL});

             htmlCode += '<div class="bt">';
             htmlCode += '  <a href="#" class="bt-changeview" data-target="' + materiaisURL + '">';
             htmlCode +=      name;
             htmlCode += '  </a>';
             htmlCode += '</div>';
          })

          $('.sub-content.materiais').html(htmlCode);
        }
    })

  }

  load(area){
    let globalArea = area.substring(0, 8);

    switch (area) {
      case 'dashboard':
      case 'pos-vendas':
      case 'noticias':
      case 'redes-sociais':
      case 'universo-renault':
      case 'renault-academy':
      case 'comunicados':
        if(frameLoaded != 'dashboard'){
          $('#content-holder').load('parts/dashboard/home.html', ()=>{
              holder.load('home', area);
          })
          frameLoaded = 'dashboard';
        } else {
          holder.load('home', area);
        }


        if(sidebarLoaded != 'gadgets'){
          $('#sidebar-holder').load('parts/sidebar/gadgets.html', ()=>{
              sidebar.load('gadgets');
          })
          sidebarLoaded = 'gadgets';
        }
      break;

      case 'campanhas':
      case 'campanha':
        if(frameLoaded != 'campanhas'){
          $('#content-holder').load('parts/dashboard/campanhas.html', ()=>{
              holder.load('campanhas', area);
          })
          frameLoaded = 'campanhas';
        } else {
          holder.load('campanhas', area);
        }

        if(sidebarLoaded != 'filtros'){
          $('#sidebar-holder').load('parts/sidebar/filtros.html', ()=>{
              sidebar.load('filtros');
          })
          sidebarLoaded = 'filtros';
        }
      break;

      case 'notificacoes':
      case 'notificacao':
        if(frameLoaded != 'notificacoes'){
          $('#content-holder').load('parts/dashboard/notificacoes.html', ()=>{
              holder.load('notificacoes', area);
          })
          frameLoaded = 'notificacoes';
        } else {
          holder.load('notificacoes', area);
        }

        if(sidebarLoaded != 'gadgets'){
          $('#sidebar-holder').load('parts/sidebar/gadgets.html', ()=>{
              sidebar.load('gadgets');
          })
          sidebarLoaded = 'gadgets';
        }
      break;
      
      case 'meus-itens-salvos':
      case 'item-salvo':
        if(frameLoaded != 'salvos'){
          $('#content-holder').load('parts/dashboard/salvos.html', ()=>{
              holder.load('salvos', area);
          })
          frameLoaded = 'salvos';
        } else {
          holder.load('salvos', area);
        }

        if(sidebarLoaded != 'gadgets'){
          $('#sidebar-holder').load('parts/sidebar/gadgets.html', ()=>{
              sidebar.load('gadgets');
          })
          sidebarLoaded = 'gadgets';
        }
      break;

      case 'biblioteca-de-materiais':
      case 'materiais-editaveis':
      case 'materiais-editaveis-whatsap':
      case 'materiais-editaveis-redes-sociais':
      case 'cartao-digital':
      case 'catalogos-compactos':
      case 'cards-whatsapp':
      case 'imagens-dos-veiculos':
      case 'guias-comparativos':
      case 'guias-institucionais':
      case 'on-demand':
      case 'videos-redes-sociais':
      case 'rdemos':
        if(frameLoaded != 'materiais'){
          $('#content-holder').load('parts/dashboard/materiais.html', ()=>{
              holder.load('materiais', area);
          })
          frameLoaded = 'materiais';
        } else {
          holder.load('materiais', area);
        }

        if(sidebarLoaded != 'filtros'){
          $('#sidebar-holder').load('parts/sidebar/filtros.html', ()=>{
              sidebar.load('filtros');
          })
          sidebarLoaded = 'filtros';
        }
      break;

    case 'central-de-ajuda':
        if(frameLoaded != 'ajuda'){
          $('#content-holder').load('parts/dashboard/ajuda.html', ()=>{
              holder.load('ajuda', area);
          })
          frameLoaded = 'ajuda';
        } else {
          holder.load('ajuda', area);
        }

        if(sidebarLoaded != 'gadgets'){
          $('#sidebar-holder').load('parts/sidebar/gadgets.html', ()=>{
              sidebar.load('gadgets');
          })
          sidebarLoaded = 'gadgets';
        }
      break;

    case 'busca':
    case 'buscado':
        if(frameLoaded != 'busca'){
          $('#content-holder').load('parts/dashboard/busca.html', ()=>{
              holder.load('busca', area);
          })
          frameLoaded = 'busca';
        } else {
          holder.load('busca', area);
        }

        if(sidebarLoaded != 'gadgets'){
          $('#sidebar-holder').load('parts/sidebar/gadgets.html', ()=>{
              sidebar.load('gadgets');
          })
          sidebarLoaded = 'gadgets';
        }
      break;

    case 'configuracoes':
        if(frameLoaded != 'configuracoes'){
          $('#content-holder').load('parts/dashboard/configuracoes.html', ()=>{
              holder.load('configuracoes', area);
          })
          frameLoaded = 'configuracoes';
        } else {
          holder.load('configuracoes', area);
        }

        if(sidebarLoaded != 'gadgets'){
          $('#sidebar-holder').load('parts/sidebar/gadgets.html', ()=>{
              sidebar.load('gadgets');
          })
          sidebarLoaded = 'gadgets';
        }
      break;

    default:
      if(globalArea == 'campanha') {
        if(frameLoaded != 'campanhas'){
          $('#content-holder').load('parts/dashboard/campanhas.html', ()=>{
              holder.load('campanhas', area);
          })
          frameLoaded = 'campanhas';
        } else {
          holder.load('campanhas', area);
        }

        if(sidebarLoaded != 'filtros'){
          $('#sidebar-holder').load('parts/sidebar/filtros.html', ()=>{
              sidebar.load('filtros');
          })
          sidebarLoaded = 'filtros';
        }
      }

      if(globalArea == 'materiai') {
        if(frameLoaded != 'materiais'){
          $('#content-holder').load('parts/dashboard/materiais.html', ()=>{
              holder.load('materiais', area);
          })
          frameLoaded = 'materiais';
        } else {
          holder.load('materiais', area);
        }

        if(sidebarLoaded != 'filtros'){
          $('#sidebar-holder').load('parts/sidebar/filtros.html', ()=>{
              sidebar.load('filtros');
          })
          sidebarLoaded = 'filtros';
        }
      }
      break;

      activeArea = area;
    }
  }

  switcher(type) {
      $('aside.left').attr('data-anchor', type);
  }

  menu(target){
    if($(target).parent().parent().find('.sub-content').is(':visible')){
      $(target).parent().parent().removeClass('open');
      $(target).parent().parent().find('.sub-content').slideUp();  
    } else {
      $(target).parent().parent().addClass('open');
      $(target).parent().parent().find('.sub-content').slideDown();
    }
  }
}

class SIDEBAR {
  constructor(){
  }

  load(type){
    sidebarLoaded = type;

    if(type == 'gadgets'){
      const params = { q:'banners' };

      app.request(params, false, null, ( res ) => {
          if(res.error != null) {
          }else{
            let htmlCode = '';
            let loop = res.banners;

            loop.forEach(function(item) {
              htmlCode += util.factory(BANNERS_TEMPLATE, item);
            })


            if($(window).width() >= 992){
               $('#sidebar-holder .banners .slider').html(htmlCode);
               if( $('#sidebar-holder .banners .slider').length > 0 ){
                  $('#sidebar-holder .banners .slider').slick({
                    infinite: true,
                    arrows: false,
                    autoplay: true,
                    dots: true,
                    slidesToShow: 1,
                    slidesToScroll: 1,
                  });
              }
            }
          }
      })

      const paramsEnquete = { q:'feed', filter:'enquete', limit: 1 };
      app.request(paramsEnquete, false, null, ( res ) => {
          let htmlCode = '';
          if(res.error != null) {
          }else{
            let data = res.feed[0];
            let date = data.date;
            let date_0 = date.substring(0,10);
            let date_1 = new Date(date_0);
            let date_2 = new Date();
            let difference = date_1.getTime() - date_2.getTime();
            let TotalDays = Math.abs(Math.ceil(difference / (1000 * 3600 * 24)));

            let percertage = 0;
            data.survey_answers.forEach(function(resposta, i) {
              if(resposta.percentage > percertage) {
                percertage = resposta.percentage;
              }
            });

            if($(window).width() < 992){
              htmlCode += '<div class="bartop">';
              htmlCode += '  <a href="#" class="bt-expand-enquete" data-target="expand-enquete">';
              htmlCode += '    <div class="ico">';
              htmlCode += '      <img src="assets/images/ico_enquetes.svg">';
              htmlCode += '      enquete';
              htmlCode += '    </div>';

              let cleanText = data.message.replace(/<\/?[^>]+(>|$)/g, "");

              htmlCode += '     <div class="txt">' + cleanText + '</div>';
              htmlCode += '    <div class="arrow"><img src="assets/images/arrow_down.svg"></div>';
              htmlCode += '  </a>';
              htmlCode += '</div>';
              htmlCode += '<div class="acc-aux">';
            }


            htmlCode += '<div class="top">';
            htmlCode += '  <h4>'+ data.title +'</h4>';
            htmlCode +=    data.message;
            htmlCode += '</div>';
            
            htmlCode += '<div class="holder">';
            data.survey_answers.forEach(function(resposta, i) {
              if(resposta.percentage == percertage /*&& data.is_closed*/) {
                if(data.vote == resposta.id) {
                  htmlCode += '  <div class="item ganhador selecionado" data-anchor="' + i + '">';
                } else {
                  htmlCode += '  <div class="item ganhador" data-anchor="' + i + '">';
                }
              } else if(data.vote == resposta.id) {
                htmlCode += '  <div class="item selecionado" data-anchor="' + i + '">';
              } else {
                htmlCode += '  <div class="item" data-anchor="' + i + '">';
              }
              htmlCode += '    <div class="bt">';
              htmlCode += '      <a href="#" class="bt-voto" data-target="' + i + '" data-enquete="' + data.id + '" data-reposta="'+ resposta.id +'">'+ resposta.text +'</a>';
              htmlCode += '    </div>';
              htmlCode += '    <div class="resposta">';
              htmlCode += '      <div class="txt">'+ resposta.text +'</div>';
              htmlCode += '      <div class="num">'+ resposta.percentage +'%</div>';
              htmlCode += '      <div class="perc" style="--value: '+ resposta.percentage +'%"></div>';
              htmlCode += '    </div>';
              htmlCode += '  </div>';
            })
            htmlCode += '</div>';

            htmlCode += '<div class="bottom">';
            if(data.total < 2) {
              htmlCode += '<span class="votos">' + data.total + ' voto </span> | ';
            } else {
              htmlCode += '<span class="votos">' + data.total + ' votos </span> | ';
            }
            if(TotalDays == 0){
              htmlCode += ' . hoje';
            } else if( TotalDays == 1) {
              htmlCode += '<span class="tempo">' + TotalDays + ' dia atrás</span>';
            } else {
              htmlCode += '<span class="tempo">' + TotalDays + ' dias atrás</span>';
            }
            htmlCode += '</div>';

            if($(window).width() < 992){
              htmlCode += '</div>';
            }

            if($(window).width() < 992){
              $('.banners').after(`
                  <div class="enquete enquete-mobile"></div>
              `);

              $('.enquete-mobile').html(htmlCode);

            } else {
              $('.enquete').html(htmlCode);
            }

            if(data.vote != 0){
             $('.enquete').addClass('resultado');
            }


          }
      });

    }

    if(type == 'filtros'){
      const params = { q:'vehicles' };

      app.request(params, false, null, ( res ) => {
          if(res.error != null) {
          }else{
            let htmlCode = '';
            let loop = res.vehicles;

            loop.forEach(function(item) {
              htmlCode += '<div class="bt">';
              htmlCode += '  <a href="#" class="bt-filtro-veiculo" data-target="' + item.id + '" data-name="' + util.slugfy(item.name) + '">';
              if(item.name.includes('E-Tech') || item.name.includes('Elétrico') ){
                htmlCode += '    <div class="txt">' + item.name + ' <br> <small>100% elétrico</small></div>';
              } else {
                htmlCode += '    <div class="txt">' + item.name + '</div>';
              }
              htmlCode += '  </a>';
              htmlCode += '</div>';
            })

            $('#lista-filtro').html(htmlCode);
          }
      })
    }
  }

  enquete(target, escolha) {
    let enquete = $(target).attr('data-enquete');
    let voto = $(target).attr('data-reposta');

    const params = { q:'set_survey_vote', survey_id:enquete, survey_answer_id:voto };
      
    app.request( params, false, null, ( res ) => {
      $('.enquete').addClass('resultado');
      if(res.survey.total < 2) {
        $('.enquete .votos').html(res.survey.total + ' voto');
      } else {
        $('.enquete .votos').html(res.survey.total + ' votos');
      }

      res.survey.survey_answers.forEach(function(resposta, i) {
        $('.item[data-anchor="' + i + '"] .resposta .num').html(resposta.percentage +'%');
        $('.item[data-anchor="' + i + '"] .resposta .perc').attr('style', '--value: '+ resposta.percentage +'%');
      })

      $('.enquete').find('.item[data-anchor="' + escolha + '"]').addClass('selecionado');
    });

  }
}


class HOLDER {
  constructor(){
  }

  load(type, area){
    if(type == 'home') {
         try {
          home.init(area);

          if($(window).width() < 992){
            const params = { q:'banners'};

            app.request(params, false, null, ( res ) => {
                if(res.error != null) {
                }else{
                  let htmlCode = '';
                  let loop = res.banners;

                  loop.forEach(function(item) {
                    htmlCode += util.factory(BANNERS_TEMPLATE, item);
                  })

                  if(!$('#content-holder .banners .slider').hasClass('slick-slider')){
                    $('#content-holder .banners .slider').html(htmlCode);

                    $('#content-holder .banners .slider').slick({
                      infinite: true,
                      arrows: false,
                      autoplay: true,
                      dots: true,
                      slidesToShow: 1,
                      slidesToScroll: 1,
                    });
                  }
                }
            })
          }
        } catch (error) {
          // silence is gold
        }
    }
    if(type == 'campanhas') {
       try {
          campanhas.init(area);
        } catch (error) {
          // silence is gold
        }
    }
    if(type == 'notificacoes') {
       try {
          notificacoes.init(area);
        } catch (error) {
          // silence is gold
        }
    }
    if(type == 'salvos') {
       try {
          salvos.init(area);
        } catch (error) {
          // silence is gold
        }
    }
    if(type == 'materiais') {
        try {
          materiais.init(area);
        } catch (error) {
          // silence is gold
        }
    }
    if(type == 'ajuda') {
        try {
          ajuda.init(area);
        } catch (error) {
          // silence is gold
        }
    }
    if(type == 'busca') {
        try {
          busca.init(area);
        } catch (error) {
          // silence is gold
        }
    }
    if(type == 'configuracoes') {
        try {
          configuracoes.init(area);
        } catch (error) {
          // silence is gold
        }
    }
  }
}

const makeBASE = new BASE();
window.base = makeBASE;

const makeSIDEBAR = new SIDEBAR();
window.sidebar = makeSIDEBAR;

const makeHOLDER = new HOLDER();
window.holder = makeHOLDER;

window.frameLoaded = frameLoaded;
window.baseLoaded = baseLoaded;
window.sidebarLoaded = sidebarLoaded;
window.activeArea = activeArea;