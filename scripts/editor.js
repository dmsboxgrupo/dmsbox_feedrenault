window.Editor = (function (window, document) {

var _templates = null;
var _fundo = null;
var _selecionado = null;

let arrStyles = [
  {'size' : 14, 'newsize' : 14, 'line' : 1.2, 'newline' : 1.2, 'weight' : 400},
  {'size' : 14, 'newsize' : 14, 'line' : 1.2, 'newline' : 1.2, 'weight' : 400},
  {'size' : 14, 'newsize' : 14, 'line' : 1.2, 'newline' : 1.2, 'weight' : 600},
  {'size' : 20, 'newsize' : 20, 'line' : 1.2, 'newline' : 1.2, 'weight' : 400},
  {'size' : 20, 'newsize' : 20, 'line' : 1.2, 'newline' : 1.2, 'weight' : 600},
  {'size' : 25, 'newsize' : 25, 'line' : 1.2, 'newline' : 1.2, 'weight' : 400},
  {'size' : 25, 'newsize' : 25, 'line' : 1.2, 'newline' : 1.2, 'weight' : 600},
  {'size' : 30, 'newsize' : 30, 'line' : 1, 'newline' : 1, 'weight' : 600},
];

async function _show(id) {
  // if (!_templates) {
  //   _templates = await (await fetch("https://feedrenault.com.br/api.php?q=templates")).json();
  //   //_analisaTags();
  // }

  //<div id="createpopup" class="create-popup"></div>

  let div = document.createElement('div');
  div.classList.add('create-popup');
  div.setAttribute("id", "createpopup");
  document.body.appendChild(div);

  var params = Object.assign({}, this.feed, this.filterGallery, {q:'templates', id: id });


  function HEXtoRGB(hex){
      var c;
      if(/^#([A-Fa-f0-9]{3}){1,2}$/.test(hex)){ 
        c= hex.substring(1).split('');
        if(c.length== 3){c= [c[0], c[0], c[1], c[1], c[2], c[2]];}
        c= '0x'+c.join('');
        return 'rgb('+[(c>>16)&255,(c>>8)&255, c&255].join(', ')+')';
      }
    } 

  app.request(params, ( res ) => {
    const template = res.templates[0];
    var result = `
    <div class="hld-print" id="cardBase">
      <div class="elementos">`;

      if(template.fields.field_0.text != '' && template.fields.field_0.text != null) {
        result += `<div class="texto pos-1" data-style="${template.fields.field_0.style}" style="font-weight: ${arrStyles[template.fields.field_0.style]['weight']}; font-size: ${arrStyles[template.fields.field_0.style]['size']}px; line-height: ${arrStyles[template.fields.field_0.style]['line']}; color: ${HEXtoRGB(template.fields.field_0.color)}; background: transparent; justify-content: flex-start; text-transform: uppercase; letter-spacing: 0px;">${template.fields.field_0.text}</div>`;
      } else {
        result += `<div class="texto pos-1"></div>`;
      }
      if(template.fields.field_1.text != '' && template.fields.field_1.text != null) {
        result += `<div class="texto pos-2" data-style="${template.fields.field_1.style}" style="font-weight: ${arrStyles[template.fields.field_1.style]['weight']}; font-size: ${arrStyles[template.fields.field_1.style]['size']}px; line-height: ${arrStyles[template.fields.field_1.style]['line']}; color: ${HEXtoRGB(template.fields.field_1.color)}; background: transparent; text-align: left; text-transform: uppercase; letter-spacing: 0px;">${template.fields.field_1.text}</div>`;
      } else {
        result += `<div class="texto pos-2"></div>`;
      }
      if(template.fields.field_2.text != '' && template.fields.field_2.text != null) {
        result += `<div class="texto pos-3" data-style="${template.fields.field_2.style}" style="font-weight: ${arrStyles[template.fields.field_2.style]['weight']}; font-size: ${arrStyles[template.fields.field_2.style]['size']}px; line-height: ${arrStyles[template.fields.field_2.style]['line']}; color: ${HEXtoRGB(template.fields.field_2.color)}; background: transparent; text-align: left; text-transform: uppercase; letter-spacing: 0px;">${template.fields.field_2.text}</div>`;
      } else {
        result += `<div class="texto pos-3"></div>`;
      }
      if(template.fields.field_3.text != '' && template.fields.field_3.text != null) {
        result += `<div class="texto pos-4" data-style="${template.fields.field_3.style}" style="font-weight: ${arrStyles[template.fields.field_3.style]['weight']}; font-size: ${arrStyles[template.fields.field_3.style]['size']}px; line-height: ${arrStyles[template.fields.field_3.style]['line']}; color: ${HEXtoRGB(template.fields.field_3.color)}; background: transparent; justify-content: center; text-transform: uppercase; letter-spacing: 0px;">${template.fields.field_3.text}</div>`;
      } else {
        result += `<div class="texto pos-4"></div>`;
      }
 
 result += `</div>
      <img src="${template.content}">
    </div>
    <div class="create-editar">
      <input type="hidden" id="postTitle" value="${template.name}">
      <div class="hld-img">
        <div class="elementos">`;

        if(template.fields.field_0.text != '' && template.fields.field_0.text != null) {
          result += `<div class="texto pos-1" data-id="1" data-style="${template.fields.field_0.style}" contenteditable="true" style="font-weight: ${arrStyles[template.fields.field_0.style]['weight']}; font-size: ${arrStyles[template.fields.field_0.style]['size']}px; line-height: ${arrStyles[template.fields.field_0.style]['line']}; color: ${HEXtoRGB(template.fields.field_0.color)}; background: transparent; justify-content: flex-start; text-transform: uppercase; letter-spacing: 0px;">${template.fields.field_0.text}</div>`;
        } else {
          result += `<div class="texto pos-1"></div>`;
        }
        if(template.fields.field_1.text != '' && template.fields.field_1.text != null) {
          result += `<DIV class="texto pos-2" data-id="2" data-style="${template.fields.field_1.style}" contenteditable="true" style="font-weight: ${arrStyles[template.fields.field_1.style]['weight']}; font-size: ${arrStyles[template.fields.field_1.style]['size']}px; line-height: ${arrStyles[template.fields.field_1.style]['line']}; color: ${HEXtoRGB(template.fields.field_1.color)}; background: transparent; text-align: left; text-transform: uppercase; letter-spacing: 0px;">${template.fields.field_1.text}</DIV>`;
        } else {
          result += `<div class="texto pos-2"></div>`;
        }
        if(template.fields.field_2.text != '' && template.fields.field_2.text != null) {
          result += `<DIV class="texto pos-3" data-id="3" data-style="${template.fields.field_2.style}" contenteditable="true" style="font-weight: ${arrStyles[template.fields.field_2.style]['weight']}; font-size: ${arrStyles[template.fields.field_2.style]['size']}px; line-height: ${arrStyles[template.fields.field_2.style]['line']}; color: ${HEXtoRGB(template.fields.field_2.color)}; background: transparent; text-align: left; text-transform: uppercase; letter-spacing: 0px;">${template.fields.field_2.text}</DIV>`;
        } else {
          result += `<div class="texto pos-3"></div>`;
        }
        if(template.fields.field_3.text != '' && template.fields.field_3.text != null) {
          result += `<DIV class="texto pos-4" data-id="4" data-style="${template.fields.field_3.style}" contenteditable="true" style="font-weight: ${arrStyles[template.fields.field_3.style]['weight']}; font-size: ${arrStyles[template.fields.field_3.style]['size']}px; line-height: ${arrStyles[template.fields.field_3.style]['line']}; color: ${HEXtoRGB(template.fields.field_3.color)}; background: transparent; justify-content: center; text-transform: uppercase; letter-spacing: 0px;">${template.fields.field_3.text}</DIV>`;
        } else {
          result += `<div class="texto pos-4"></div>`;
        }
   
   result += `</div>
        <img src="${template.content}">
      </div>
      <div class="funcoes on">
        <div class="scroll-horizontal">
          <div class="funcao fonte">
            <a href="#" class="bt-opcoes">
              <div class="ico">
                <img src="images/ico_func_fonte.svg?v=2">
              </div>
              <span>fonte</span>
              <div class="close"></div>
            </a>
            <div class="opcoes">
              <a href="#" class="font-type font-light" data-var="100">
                <div class="ico">
                  <img src="images/ico_font_light.svg?v=2">
                </div>
                <span>nouvelR light</span>
              </a>
              <a href="#" class="font-type font-book selected" data-var="300">
                <div class="ico">
                  <img src="images/ico_font_book.svg?v=2">
                </div>
                <span>nouvelR book</span>
              </a>
              <a href="#" class="font-type font-regular" data-var="400">
                <div class="ico">
                  <img src="images/ico_font_regular.svg?v=2">
                </div>
                <span>nouvelR regular</span>
              </a>
              <a href="#" class="font-type font-semibold" data-var="500">
                <div class="ico">
                  <img src="images/ico_font_semibold.svg?v=2">
                </div>
                <span>nouvelR semibold</span>
              </a>
              <a href="#" class="font-type font-bold" data-var="600">
                <div class="ico">
                  <img src="images/ico_font_bold.svg?v=2">
                </div>
                <span>nouvelR bold</span>
              </a>
              <a href="#" class="font-type font-extrabold" data-var="700">
                <div class="ico">
                  <img src="images/ico_font_extrabold.svg?v=2">
                </div>
                <span>nouvelR extrabold</span>
              </a>
            </div>
          </div>
          <div class="funcao tamanho">
            <a href="#" class="bt-opcoes">
              <div class="ico">
                <img src="images/ico_func_tamanho.svg?v=2">
              </div>
              <span>tamanho</span>
              <div class="close"></div>
            </a>
            <div class="opcoes font-size">
              <a href="#" class="font-menos">
                <div class="ico">
                  <img src="images/ico_font_menos.svg?v=2">
                </div>
              </a>
              <input type="number" name="font-size" value="14">
              <a href="#" class="font-mais">
                <div class="ico">
                  <img src="images/ico_font_mais.svg?v=2">
                </div>
              </a>
            </div>
          </div>
          <div class="funcao cor">
            <a href="#" class="bt-opcoes">
              <div class="ico">
                <img src="images/ico_func_cores.svg?v=2">
              </div>
              <span>cores</span>
              <div class="close"></div>
            </a>
            <div class="opcoes font-color">
              <div class="grupo">
                <div class="tipo">
                  <div class="ico">
                    <img src="images/ico_cor_texto.svg?v=2">
                  </div>
                  <span>texto</span>
                </div>
                <a href="#" class="font-cor">
                  <div class="amostra cor-1" data-var="rgb(255, 255, 255)"></div>
                </a>
                <a href="#" class="font-cor">
                  <div class="amostra cor-2" data-var="rgb(217, 217, 214)"></div>
                </a>
                <a href="#" class="font-cor">
                  <div class="amostra cor-3" data-var="rgb(187, 188, 188)"></div>
                </a>
                <a href="#" class="font-cor">
                  <div class="amostra cor-4" data-var="rgb(136, 139, 141)"></div>
                </a>
                <a href="#" class="font-cor selected">
                  <div class="amostra cor-5" data-var="rgb(0, 0, 0)"></div>
                </a>
                <a href="#" class="font-cor">
                  <div class="amostra cor-6" data-var="rgb(112, 112, 112)"></div>
                </a>
                <a href="#" class="font-cor">
                  <div class="amostra cor-7" data-var="rgb(239, 223, 0)"></div>
                </a>
              </div>
              <div class="grupo">
                <div class="tipo fundo">
                  <div class="ico">
                    <img src="images/ico_cor_fundo.svg?v=2">
                  </div>
                  <span>fundo</span>
                </div>
                <a href="#" class="font-bg selected">
                  <div class="amostra cor-0" data-var="transparent"></div>
                </a>
                <a href="#" class="font-bg">
                  <div class="amostra cor-1" data-var="rgb(255, 255, 255)"></div>
                </a>
                <a href="#" class="font-bg">
                  <div class="amostra cor-2" data-var="rgb(217, 217, 214)"></div>
                </a>
                <a href="#" class="font-bg">
                  <div class="amostra cor-3" data-var="rgb(187, 188, 188)"></div>
                </a>
                <a href="#" class="font-bg">
                  <div class="amostra cor-4" data-var="rgb(136, 139, 141)"></div>
                </a>
                <a href="#" class="font-bg">
                  <div class="amostra cor-5" data-var="rgb(0, 0, 0)"></div>
                </a>
                <a href="#" class="font-bg">
                  <div class="amostra cor-6" data-var="rgb(112, 112, 112)"></div>
                </a>
                <a href="#" class="font-bg">
                  <div class="amostra cor-7" data-var="rgb(239, 223, 0)"></div>
                </a>
              </div>
            </div>
          </div>
          <div class="funcao formatacao">
            <a href="#" class="bt-opcoes">
              <div class="ico">
                <img src="images/ico_func_formatacao.svg?v=2">
              </div>
              <span>formatação</span>
              <div class="close"></div>
            </a>
            <div class="opcoes formatacao">
              <div class="grupo">
                <a href="#" class="font-align selected" data-var="left">
                  <div class="ico">
                    <img src="images/ico_align_left.svg?v=2">
                  </div>
                </a>
                <a href="#" class="font-align" data-var="center">
                  <div class="ico">
                    <img src="images/ico_align_center.svg?v=2">
                  </div>
                </a>
                <a href="#" class="font-align" data-var="right">
                  <div class="ico">
                    <img src="images/ico_align_right.svg?v=2">
                  </div>
                </a>
              </div>
              <div class="grupo">
                <a href="#" class="font-case selected" data-var="uppercase">
                  <div class="ico">
                    <img src="images/ico_case_upper.svg?v=2">
                  </div>
                </a>
                <a href="#" class="font-case" data-var="lowercase">
                  <div class="ico">
                    <img src="images/ico_case_lower.svg?v=2">
                  </div>
                </a>
                <a href="#" class="font-case" data-var="capitalize">
                  <div class="ico">
                    <img src="images/ico_case_captalize.svg?v=2">
                  </div>
                </a>
              </div>
              <div class="grupo size">
                <div class="tipo">
                  <div class="ico">
                    <img src="images/ico_space_line.svg?v=2">
                  </div>
                </div>
                <a href="#" class="line-menos">
                  <div class="ico">
                    <img src="images/ico_font_menos.svg?v=2">
                  </div>
                </a>
                <input type="number" name="line-size" value="1">
                <a href="#" class="line-mais">
                  <div class="ico">
                    <img src="images/ico_font_mais.svg?v=2">
                  </div>
                </a>
              </div>
              <div class="grupo size">
                <div class="tipo">
                  <div class="ico">
                    <img src="images/ico_space_caracter.svg?v=2">
                  </div>
                </div>
                <a href="#" class="caracter-menos">
                  <div class="ico">
                    <img src="images/ico_font_menos.svg?v=2">
                  </div>
                </a>
                <input type="number" name="caracter-size" value="0">
                <a href="#" class="caracter-mais">
                  <div class="ico">
                    <img src="images/ico_font_mais.svg?v=2">
                  </div>
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="acoes on">
        <div class="acao alt">
          <a href="#" id="create-finalizar">
            <div class="ico">
              <img src="images/ico_acao_pronto.svg?v=2">
            </div>
            <span>tudo pronto</span>
          </a>
        </div>
      </div>
      <div class="finalizacao">
        <a href="#" id="create-download">
          <div class="ico"><img src="images/ico_acao_download.svg?v=2"></div>
          <span>baixar imagem</span>
        </a>
        <a href="#" id="create-compartilhar">
          <div class="ico">
            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="22" height="19.271" viewBox="0 0 22 19.271">
                <path d="M8.221,10.425l5.919,2.729a3.888,3.888,0,0,1,3.716-2.17A3.918,3.918,0,0,1,22,15.127a3.9,3.9,0,0,1-4.143,4.144,3.935,3.935,0,0,1-4.144-4.144,4.856,4.856,0,0,1,.033-.691L7.826,11.674a3.909,3.909,0,0,1-3.683,2.1A3.935,3.935,0,0,1,0,9.636,3.935,3.935,0,0,1,4.143,5.492,3.843,3.843,0,0,1,7.826,7.6l5.919-2.762a4.856,4.856,0,0,1-.033-.691A3.935,3.935,0,0,1,17.856,0,3.9,3.9,0,0,1,22,4.144a3.918,3.918,0,0,1-4.143,4.144A3.889,3.889,0,0,1,14.14,6.117L8.221,8.846a6.544,6.544,0,0,1,.066.79,6.532,6.532,0,0,1-.066.789M4.143,12.464a2.54,2.54,0,0,0,2.8-2.828,2.54,2.54,0,0,0-2.8-2.828A2.54,2.54,0,0,0,1.348,9.636a2.54,2.54,0,0,0,2.795,2.828M17.856,6.972a2.54,2.54,0,0,0,2.795-2.828,2.54,2.54,0,0,0-2.795-2.828,2.54,2.54,0,0,0-2.8,2.828,2.54,2.54,0,0,0,2.8,2.828m0,10.984a2.54,2.54,0,0,0,2.795-2.829A2.54,2.54,0,0,0,17.856,12.3a2.54,2.54,0,0,0-2.8,2.828,2.54,2.54,0,0,0,2.8,2.829" transform="translate(0 0)" class="ico-fill"/>
            </svg>
          </div>
          <span>compartilhar</span>
        </a>
      </div>
      <div class="compartilhar">
        <a href="#" id="createShareWhats">
          <div class="ico"><img src="images/ico_social_whatsapp.svg?v=2"></div>
          <span>Whatsapp</span>
        </a>
        <a href="#" id="createShareInsta">
          <div class="ico"><img src="images/ico_social_instagram.svg?v=2"></div>
          <span>Instagram</span>
        </a>
        <a href="#" id="createShareFace">
          <div class="ico"><img src="images/ico_social_facebook.svg?v=2"></div>
          <span>Facebook</span>
        </a>
        <a href="#" id="createShareTwitter">
          <div class="ico"><img src="images/ico_social_twitter.svg?v=2"></div>
          <span>Twitter</span>
        </a>
      </div>
    </div>`;

    $("#createpopup").append(result);
    $("#createpopup").css({'display':'block'});
    $(".create-header-editar").addClass('on');
    _iniciaEventos();
  });
  
}

async function _uploadImagem(blob) {
  const data = await app.uploadImage(new File([blob], 'fr_editor.jpg', { type: blob.type }));;
  if (!data || !data.upload || !data.upload.content) throw new Error("Wrong response");
  return data.upload.content;
}

function _getBlob(canvas) {
  return new Promise((resolve, reject) => {
    try {
      canvas.toBlob(blob => resolve(blob));
    } catch (e) {
      reject(e);
    }
  });
}


function _iniciaEventos() {
  const editor = $("#createpopup");
  this.createBackup = [];
  this.createHistory = [];
  this.stepHistory = 0;

  $('.header-actions').removeClass('off');
  $('#bt-undo').addClass('off');
  $('#bt-redo').addClass('off');

  $('.create-editar .elementos').find('.texto').each(function(){
    var thisID = $(this).attr('data-id');
    var thisContent = $(this).html();
    var thisStyle = $(this).attr('style');
    createBackup.push({
        id : thisID, 
        content : thisContent, 
        style : thisStyle
    });
  });

  createHistory.push({
        id : '0', 
        content : '', 
        style : ''
  });


   var ajuste = $('.create-header-editar').height() + 155;

   var resize = ((($(document).height() - ajuste) * 100 / 1920) / 100);

   if($('.create-editar .elementos .pos-1').length > 0) {
     var size1 = $('.create-editar .elementos .pos-1').css('font-size').replace('px', '') / resize;
     var line1 = $('.create-editar .elementos .pos-1').css('line-height').replace('px', '') / resize;
     
     $('.create-editar .elementos .pos-1').css({'font-size': size1 + 'px', 'line-height': line1 + 'px'});
   }
   if($('.create-editar .elementos .pos-2').length > 0) {
     var size2 = $('.create-editar .elementos .pos-2').css('font-size').replace('px', '') / resize;
     var line2 = $('.create-editar .elementos .pos-2').css('line-height').replace('px', '') / resize;
     
     $('.create-editar .elementos .pos-2').css({'font-size': size2 + 'px', 'line-height': line2 + 'px'});
   }
   if($('.create-editar .elementos .pos-3').length > 0) {
     var size3 = $('.create-editar .elementos .pos-3').css('font-size').replace('px', '') / resize;
     var line3 = $('.create-editar .elementos .pos-3').css('line-height').replace('px', '') / resize;
     
     $('.create-editar .elementos .pos-3').css({'font-size': size3 + 'px', 'line-height': line3 + 'px'});
   }
   if($('.create-editar .elementos .pos-4').length > 0) {
     var size4 = $('.create-editar .elementos .pos-4').css('font-size').replace('px', '') / resize;
     var line4 = $('.create-editar .elementos .pos-4').css('line-height').replace('px', '') / resize;

     $('.create-editar .elementos .pos-4').css({'font-size': size4 + 'px', 'line-height': line4 + 'px'});
   }

   var confirmSize;

  function resizeImg() {
      clearTimeout(confirmSize);
       resize = ((($(document).height() - ajuste) * 100 / 1920) / 100);
       ajuste = $('.create-header-editar').height() + 155;

       $('.create-editar .hld-img').css({'transform':'scale('+resize+')'});

       var confirmSize = setTimeout(resizeImg, 100);
  }

  resizeImg();

  $(window).on('resize', resizeImg);


  function checkHistory(type = null){
    if(type) {
      if (type == 'undo') {
        if(stepHistory > 0) {
          stepHistory--;
        }
      } else {
        stepHistory++;
      }

      if(stepHistory > 0){
        $('.create-editar .texto.pos-' + createHistory[stepHistory].id).html(createHistory[stepHistory].content);
        $('.texto.pos-' + createHistory[stepHistory].id).attr('style', createHistory[stepHistory].style);

        $('.hld-print .elementos .texto.pos-' +  createHistory[stepHistory].id).html(createHistory[stepHistory].content);
        
        updateDefs(createHistory[stepHistory].id);
      } else {
        $.each( createBackup, function( key, value ) {
          $('.create-editar .texto.pos-' + value.id).html(value.content);
          $('.texto.pos-' + value.id).attr('style', value.style);

          $('.hld-print .elementos .texto.pos-' + value.id).html(value.content);
         });

        var ajuste = $('.create-header-editar').height() + 155;

         var resize = ((($(document).height() - ajuste) * 100 / 1920) / 100);

         if($('.create-editar .elementos .pos-1').length > 0) {
           var size1 = $('.create-editar .elementos .pos-1').css('font-size').replace('px', '') / resize;
           var line1 = $('.create-editar .elementos .pos-1').css('line-height').replace('px', '') / resize;
           
           $('.create-editar .elementos .pos-1').css({'font-size': size1 + 'px', 'line-height': line1 + 'px'});
         }
         if($('.create-editar .elementos .pos-2').length > 0) {
           var size2 = $('.create-editar .elementos .pos-2').css('font-size').replace('px', '') / resize;
           var line2 = $('.create-editar .elementos .pos-2').css('line-height').replace('px', '') / resize;
           
           $('.create-editar .elementos .pos-2').css({'font-size': size2 + 'px', 'line-height': line2 + 'px'});
         }
         if($('.create-editar .elementos .pos-3').length > 0) {
           var size3 = $('.create-editar .elementos .pos-3').css('font-size').replace('px', '') / resize;
           var line3 = $('.create-editar .elementos .pos-3').css('line-height').replace('px', '') / resize;
           
           $('.create-editar .elementos .pos-3').css({'font-size': size3 + 'px', 'line-height': line3 + 'px'});
         }
         if($('.create-editar .elementos .pos-4').length > 0) {
           var size4 = $('.create-editar .elementos .pos-4').css('font-size').replace('px', '') / resize;
           var line4 = $('.create-editar .elementos .pos-4').css('line-height').replace('px', '') / resize;
  
           $('.create-editar .elementos .pos-4').css({'font-size': size4 + 'px', 'line-height': line4 + 'px'});
         }

         $('.texto').removeClass('selected');
      }
    } else {
      stepHistory++;
    }

    if(stepHistory > 0) {
      $('#bt-undo').removeClass('off');
    } else {
      $('#bt-undo').addClass('off');
    }

    if(createHistory.length - 1 > stepHistory) {
      $('#bt-redo').removeClass('off');
    } else {
      $('#bt-redo').addClass('off');
    }
  }

  function pushHistory(id){
    createHistory.push({
          id : id, 
          content : $('.create-editar .elementos .texto.pos-' + id).html(), 
          style : $('.create-editar .elementos .texto.pos-' + id).attr('style')
    });
    checkHistory();
  }

  editor.on("blur", ".texto", function(ev) {
        ev.preventDefault();

        var thisID = $(this).attr('data-id');
        var thisContent = $(this).html();
        var thisStyle = $(this).attr('style');

        var alterou = false;

        for (var i = createHistory.length - 1; i >= 0; i--) {
           if(createHistory[i].id == thisID) {
              alterou = true;
              if(createHistory[i].content != thisContent) {
                createHistory.push({
                    id : thisID, 
                    content : thisContent, 
                    style : thisStyle
                });
                
                checkHistory();
                return false;
              }
           }
        }

        if(!alterou) {
          $.each( createBackup, function( key, value ) {
            if(value.id == thisID) {
              if(value.content != thisContent) {
                createHistory.push({
                    id : thisID, 
                    content : thisContent, 
                    style : thisStyle
                });
                
                checkHistory();
                return false;
              }
            }
          });
        }
    

  });

  function updateDefs(id) {
        var element = $('.create-editar .elementos .texto.pos-' + id);

        var color = element.css('color');
        $('.font-cor').each(function(){
          if($(this).find('.amostra').attr('data-var') == color) {
            $(this).addClass('selected');
          } else {
            $(this).removeClass('selected');
          }
        })

        var background = element.css('background');
        $('.font-bg').each(function(){
          if($(this).find('.amostra').attr('data-var') == background) {
            $(this).addClass('selected');
          } else {
            $(this).removeClass('selected');
          }
        })

        var alignT = element.css('text-align');
        var alignF = element.css('justify-content');
        $('.font-align').each(function(){
          if(id == 1 || id == 4) {
            if(alignF == 'flex-start') {
              alignF = 'left';
            }
            if(alignF == 'flex-end') {
              alignF = 'right';
            }

            if($(this).attr('data-var') == alignF) {
              $(this).addClass('selected');
            } else {
              $(this).removeClass('selected');
            }

          } else {
            if($(this).attr('data-var') == alignT) {
              $(this).addClass('selected');
            } else {
              $(this).removeClass('selected');
            }
          }
        })

        var transform = element.css('text-transform');
        $('.font-case').each(function(){
          if($(this).attr('data-var') == transform) {
            $(this).addClass('selected');
          } else {
            $(this).removeClass('selected');
          }
        })


        var size = arrStyles[$('.create-editar .elementos .texto.selected').attr('data-style')]['newsize'];
        $('.font-mais').parent().find('input').val(size);

        var line = arrStyles[$('.create-editar .elementos .texto.selected').attr('data-style')]['newline'];
        $('.line-mais').parent().find('input').val(line);

        var space = element.css('letter-spacing').replace('px','');
        $('.caracter-mais').parent().find('input').val(space);

        var weight = element.css('font-weight');
        $('.font-type').each(function(){
          if($(this).attr('data-var') == weight) {
            $(this).addClass('selected');
          } else {
            $(this).removeClass('selected');
          }
        })
  }

      editor.on("focus", ".texto", function(ev) {
            ev.preventDefault();

            $('.create-editar .elementos .texto').removeClass('selected');
            $(this).addClass('selected');

            updateDefs($(this).attr('data-id'));
      });

      editor.on("input", ".texto", function(ev) {
            ev.preventDefault();

            var selector = $(this).attr('data-id');
            var val = $(this).html();
            var txt = val.replace(/\r\n/, '<br/>')

            $('.hld-print .elementos .texto.pos-' + selector).html(txt);
      });

      editor.on("click", ".bt-opcoes", function(ev) {
        ev.preventDefault();
        $(this).parent().toggleClass('open');
      });

      editor.on("click", ".font-type", function(ev) {
        ev.preventDefault();
        $('.font-type').removeClass('selected');

        var valor = $(this).attr('data-var');
        var selector = $('.elementos .texto.selected').attr('data-id');
        $('.elementos .texto.pos-' + selector).css({ 'font-weight': valor });

        pushHistory($('.elementos .texto.selected').attr('data-id'));

        $(this).addClass('selected');
      });

      editor.on("click", ".font-cor", function(ev) {
        ev.preventDefault();
        $('.font-cor').removeClass('selected');

        var valor = $(this).find('.amostra').attr('data-var');
        var selector = $('.elementos .texto.selected').attr('data-id');
        $('.elementos .texto.pos-' + selector).css({ 'color': valor });

        pushHistory($('.elementos .texto.selected').attr('data-id'));

        $(this).addClass('selected');
      });

      editor.on("click", ".font-bg", function(ev) {
        ev.preventDefault();
        $('.font-bg').removeClass('selected');

        var valor = $(this).find('.amostra').attr('data-var');
        var selector = $('.elementos .texto.selected').attr('data-id');
        $('.elementos .texto.pos-' + selector).css({ 'background': valor });

        pushHistory($('.elementos .texto.selected').attr('data-id'));

        $(this).addClass('selected');
      });

      editor.on("click", ".font-align", function(ev) {
        ev.preventDefault();
        $('.font-align').removeClass('selected');

        var valor = $(this).attr('data-var');
        var selector = $('.elementos .texto.selected').attr('data-id');

        if(selector == '1' || selector == '4') {
          if(valor == 'left') {
            $('.elementos .texto.pos-' + selector).css({ 'justify-content': 'flex-start' });
          }
          if(valor == 'center') {
            $('.elementos .texto.pos-' + selector).css({ 'justify-content': 'center' });
          }
          if(valor == 'right') {
            $('.elementos .texto.pos-' + selector).css({ 'justify-content': 'flex-end' });
          }
        } else {
          $('.elementos .texto.pos-' + selector).css({ 'text-align': valor });
        }


        pushHistory($('.elementos .texto.selected').attr('data-id'));

        $(this).addClass('selected');
      });

      editor.on("click", ".font-case", function(ev) {
        ev.preventDefault();
        $('.font-case').removeClass('selected');

        var valor = $(this).attr('data-var');

        var selector = $('.elementos .texto.selected').attr('data-id');
        $('.elementos .texto.pos-' + selector).css({ 'text-transform': valor });

        pushHistory($('.elementos .texto.selected').attr('data-id'));

        $(this).addClass('selected');
      });


      var limitSizeTop = 48;
      var limitSizeBottom = 10;

      editor.on("click", ".font-menos", function(ev) {
        ev.preventDefault();
        if($('.create-editar .elementos .texto.selected').length > 0) {
          var valor = Number(arrStyles[$('.create-editar .elementos .texto.selected').attr('data-style')]['newsize']);

          if(valor > limitSizeBottom) {
             var novoValor = Number($('.create-editar .elementos .texto.selected').css('font-size').replace('px', '')) - 1;
             var newVal = Number(arrStyles[$('.create-editar .elementos .texto.selected').attr('data-style')]['newsize']) - 1;
             
             arrStyles[$('.create-editar .elementos .texto.selected').attr('data-style')]['newsize'] = newVal;

             var selector = $('.create-editar .elementos .texto.selected').attr('data-id');

             $('.elementos .texto.pos-' + selector).css({ 'font-size': novoValor + 'px' });
             $(this).parent().find('input').val(newVal);

             pushHistory($('.create-editar .elementos .texto.selected').attr('data-id'));
          }
        }
      });

      editor.on("click", ".font-mais", function(ev) {
        ev.preventDefault();
        if($('.create-editar .elementos .texto.selected').length > 0) {
          var valor = Number(arrStyles[$('.create-editar .elementos .texto.selected').attr('data-style')]['newsize']);
          
          if(valor < limitSizeTop) {
             var novoValor = Number($('.create-editar .elementos .texto.selected').css('font-size').replace('px', '')) + 1;
             var newVal = Number(arrStyles[$('.create-editar .elementos .texto.selected').attr('data-style')]['newsize']) + 1;
             
             arrStyles[$('.create-editar .elementos .texto.selected').attr('data-style')]['newsize'] = newVal;

             var selector = $('.create-editar .elementos .texto.selected').attr('data-id');

             $('.elementos .texto.pos-' + selector).css({ 'font-size': novoValor + 'px' });
             $(this).parent().find('input').val(newVal);

             pushHistory($('.create-editar .elementos .texto.selected').attr('data-id'));
          }
        }
      });


      var limitLineTop = 2;
      var limitLineBottom = 1;

      editor.on("click", ".line-menos", function(ev) {
        ev.preventDefault();
        if($('.create-editar .elementos .texto.selected').length > 0) {
          var valor = Number(arrStyles[$('.create-editar .elementos .texto.selected').attr('data-style')]['newline']);

          if(valor > limitLineBottom) {
             var novoValor = Number(valor) - 0.1;
             var newVal = novoValor.toFixed(1);

             if(newVal == 1.0) {
               newVal = 1;
             }

             $(this).parent().find('input').val(newVal);
             arrStyles[$('.create-editar .elementos .texto.selected').attr('data-style')]['newline'] = newVal;

             var selector = $('.elementos .texto.selected').attr('data-id');
             $('.elementos .texto.pos-' + selector).css({ 'line-height': newVal });

             pushHistory($('.elementos .texto.selected').attr('data-id'));
          }
        }
      });

      editor.on("click", ".line-mais", function(ev) {
        ev.preventDefault();
        if($('.create-editar .elementos .texto.selected').length > 0) {
          var valor = Number(arrStyles[$('.create-editar .elementos .texto.selected').attr('data-style')]['newline']);

          if(valor < limitLineTop) {
             var novoValor = Number(valor) + 0.1;
             var newVal = novoValor.toFixed(1);

             if(newVal == 2.0) {
               newVal = 2;
             }

             $(this).parent().find('input').val(newVal);
             arrStyles[$('.create-editar .elementos .texto.selected').attr('data-style')]['newline'] = newVal;

             var selector = $('.elementos .texto.selected').attr('data-id');
             $('.elementos .texto.pos-' + selector).css({ 'line-height': newVal });

             pushHistory($('.elementos .texto.selected').attr('data-id'));
          }
        }
      });

      var limitCaracterTop = 10;
      var limitCaracterBottom = 0;

      editor.on("click", ".caracter-menos", function(ev) {
        ev.preventDefault();
        var valor = $(this).parent().find('input').val();
        if(valor > limitCaracterBottom) {
           var novoValor = Number(valor) - 1;
           $(this).parent().find('input').val(novoValor);

           var selector = $('.elementos .texto.selected').attr('data-id');
           $('.elementos .texto.pos-' + selector).css({ 'letter-spacing': novoValor });

           pushHistory($('.elementos .texto.selected').attr('data-id'));
        }
      });

      editor.on("click", ".caracter-mais", function(ev) {
        ev.preventDefault();
        var valor = $(this).parent().find('input').val();
        if(valor < limitCaracterTop) {
           var novoValor = Number(valor) + 1;
           $(this).parent().find('input').val(novoValor);

           var selector = $('.elementos .texto.selected').attr('data-id');
           $('.elementos .texto.pos-' + selector).css({ 'letter-spacing': novoValor });;

           pushHistory($('.elementos .texto.selected').attr('data-id'));
        }
      });


      $('body').on('click', '.editar-fechar', function(ev) {
         ev.preventDefault();
         ev.stopImmediatePropagation();

         createBackup = null;
         createHistory = null;
         stepHistory = 0;

         $('.create-header-editar').removeClass('on');
         $('#createpopup').css({'display':'none'});
         $('#createpopup').html('');
         $('#createpopup').empty();

         const element = document.getElementById("createpopup");
         element.remove();
       });

      $('body').on('click', '#bt-undo', function(ev) {
         ev.preventDefault();
         ev.stopImmediatePropagation();

         checkHistory('undo');
      });
      
      $('body').on('click', '#bt-redo', function(ev) {
         ev.preventDefault();
         ev.stopImmediatePropagation();

         checkHistory('redo');
      });
      
      $('body').on('click', '.editar-lixo', function(ev) {
         ev.preventDefault();
         ev.stopImmediatePropagation();

         $.each( createBackup, function( key, value ) {
          $('.create-editar .texto.pos-' + value.id).html(value.content);
          $('.texto.pos-' + value.id).attr('style', value.style);

          $('.hld-print .elementos .texto.pos-' + value.id).html(value.content);
         });

         var ajuste = $('.create-header-editar').height() + 155;

         var resize = ((($(document).height() - ajuste) * 100 / 1920) / 100);

         if($('.create-editar .elementos .pos-1').length > 0) {
           var size1 = $('.create-editar .elementos .pos-1').css('font-size').replace('px', '') / resize;
           var line1 = $('.create-editar .elementos .pos-1').css('line-height').replace('px', '') / resize;
           
           $('.create-editar .elementos .pos-1').css({'font-size': size1 + 'px', 'line-height': line1 + 'px'});
         }
         if($('.create-editar .elementos .pos-2').length > 0) {
           var size2 = $('.create-editar .elementos .pos-2').css('font-size').replace('px', '') / resize;
           var line2 = $('.create-editar .elementos .pos-2').css('line-height').replace('px', '') / resize;
           
           $('.create-editar .elementos .pos-2').css({'font-size': size2 + 'px', 'line-height': line2 + 'px'});
         }
         if($('.create-editar .elementos .pos-3').length > 0) {
           var size3 = $('.create-editar .elementos .pos-3').css('font-size').replace('px', '') / resize;
           var line3 = $('.create-editar .elementos .pos-3').css('line-height').replace('px', '') / resize;
           
           $('.create-editar .elementos .pos-3').css({'font-size': size3 + 'px', 'line-height': line3 + 'px'});
         }
         if($('.create-editar .elementos .pos-4').length > 0) {
           var size4 = $('.create-editar .elementos .pos-4').css('font-size').replace('px', '') / resize;
           var line4 = $('.create-editar .elementos .pos-4').css('line-height').replace('px', '') / resize;

           $('.create-editar .elementos .pos-4').css({'font-size': size4 + 'px', 'line-height': line4 + 'px'});
         }

         $('.texto').removeClass('selected');
         
         stepHistory = 0;
         createHistory = [];

         createHistory.push({
                id : '0', 
                content : '', 
                style : ''
          });

         $('#bt-undo').addClass('off');
         $('#bt-redo').addClass('off');
      });

      var imgURL = '';
      var titPost = '';
	  var file_name = '', complete_name = ''; // Variáveis para armazenar os valores

      var loc = window.location.pathname;
      var dir = loc.substring(0, loc.lastIndexOf('/'));

     $('body').on('click', '#create-finalizar', function(ev) {
         ev.preventDefault();
         ev.stopImmediatePropagation();

         $('.elementos .texto').removeClass('selected');
         $('.funcoes').removeClass('on');
         $('.acoes').removeClass('on');
         $('.finalizacao').addClass('on');
         $('.create-editar .hld-img').addClass('share');

         $('.header-actions').addClass('off');
         
         var resize = (($('.create-editar .elementos').height() * 100 / 1920) / 100);

         if($('.create-editar .elementos .pos-1').length > 0) {
           var size1 = $('.create-editar .elementos .pos-1').css('font-size').replace('px', '') / resize;
           var line1 = $('.create-editar .elementos .pos-1').css('line-height').replace('px', '') / resize;
           
           $('#cardBase .elementos .pos-1').css({'font-size': size1 + 'px', 'line-height': line1 + 'px'});
         }
         if($('.create-editar .elementos .pos-2').length > 0) {
           var size2 = $('.create-editar .elementos .pos-2').css('font-size').replace('px', '') / resize;
           var line2 = $('.create-editar .elementos .pos-2').css('line-height').replace('px', '') / resize;
           
           $('#cardBase .elementos .pos-2').css({'font-size': size2 + 'px', 'line-height': line2 + 'px'});
         }
         if($('.create-editar .elementos .pos-3').length > 0) {
           var size3 = $('.create-editar .elementos .pos-3').css('font-size').replace('px', '') / resize;
           var line3 = $('.create-editar .elementos .pos-3').css('line-height').replace('px', '') / resize;
           
           $('#cardBase .elementos .pos-3').css({'font-size': size3 + 'px', 'line-height': line3 + 'px'});
         }
         if($('.create-editar .elementos .pos-4').length > 0) {
           var size4 = $('.create-editar .elementos .pos-4').css('font-size').replace('px', '') / resize;
           var line4 = $('.create-editar .elementos .pos-4').css('line-height').replace('px', '') / resize;

           $('#cardBase .elementos .pos-4').css({'font-size': size4 + 'px', 'line-height': line4 + 'px'});
         }

         $('body').addClass('printing');

          html2canvas($("#cardBase"), {
            useCORS: true,
            width: 1080,
            height: 1920,
            onrendered: function(canvas) {
                var imgSrc = canvas.toDataURL();
				var titulo = $('#postTitle').val();
				
                $.ajax({
                  url: 'imagem_card.php',
                  type: 'POST',
                  dataType: 'json',
                  data: {
                    img: imgSrc,
					titPost: titulo
                  },
                  success: function (data) {
                    $('body').removeClass('printing');
					
					if(dir == '/feedrenault_webapp'){
                      imgURL = 'http://localhost/feedrenault_webapp/uploads/img/' + data.complete_name;
                    } else if(dir == '/dev'){
                      imgURL = 'https://feedrenault.com.br/dev/uploads/img/' + data.complete_name;
                    } else {
                      imgURL = 'https://feedrenault.com.br/uploads/img/' + data.complete_name;
                    }
					
					file_name = data.file_name;
					complete_name = data.complete_name;
					
					/*
					if (native.download) {
				
						native.download( data.url, data.name );
						
					} else {
						
						app.download( data.url, data.name );
						
					}
					
					$('body').on('click', 'a[content-download]', function(ev) {
						signal.emit('download', { content: $(this)[0] });
					  });*/
					  
					 // $(document).ready(function() {
						/* $('#create-download').click(function(event) {
						  //$('#create-editar').find('#create-download').click(function(event) {
							// Evite o comportamento padrão do link
							event.preventDefault();
							
							if (native.download) {
				
							native.download( imgURL, data.file_name );
								
							} else {
								
								app.download( imgURL, data.file_name );
								
							}
					
							// Coloque aqui o código que você deseja executar quando o link for clicado
							
							
						  });*/
						//});
					
					/*
                    if(dir == '/feedrenault'){
                      imgURL = 'http://localhost/feedrenault//uploads/img/' + data.name + '.jpg';
                    } else if(dir == '/dev'){
                      imgURL = 'https://feedrenault.com.br/dev/uploads/img/' + data.name + '.jpg';
                    } else {
                      imgURL = 'https://feedrenault.com.br/uploads/img/' + data.name + '.jpg';
                    }*/
                    /*titPost = $('#postTitle').val();

                    var downloadLink = $('#create-download');
                    downloadLink.attr('download', titPost);
                    downloadLink.attr('href', imgURL.replace("image/jpeg", "image/octet-stream"));*/
					
					 var downloadLink = $('#create-download');
                    downloadLink.attr('download', imgURL);
                    //downloadLink.attr('href', imgURL.replace("image/jpeg", "image/octet-stream"));

                  }
                });
            }
          });
    
       });
	   
	   $('body').on('click', '#create-download', function(ev) {
         ev.preventDefault();
         ev.stopImmediatePropagation();
		 
			var downloadLink = $('#create-download');

			var image_dwn = downloadLink.attr('download');
		 
			var fileName = image_dwn.split('/').pop().substring(0, image_dwn.lastIndexOf('.'));
		 
			if (native.download) {
					
				native.download( image_dwn, fileName );
					
			} else {
				
				app.download( image_dwn, fileName );
				
			}
		 
	   });

      $('body').on('click', '#create-compartilhar', function(ev) {
        ev.preventDefault();
        ev.stopImmediatePropagation();

         if (navigator.share) {
            if ( native.browser ) {
          
              native.post({
                query: 'share',
                message: titPost + ' - ' + imgURL,
                url: imgURL
              });
              
            } else {
              navigator.share({title: titPost, url: imgURL});
            }
        } else {
            var url = encodeURIComponent(imgURL);
            var tit = encodeURIComponent(titPost);

            var c = $('.compartilhar-popup');
            c.find('.compartilhar-whatsapp').attr('href', 'https://wa.me/?text=' + tit + '%20' + url);
            c.find('.compartilhar-twitter').attr('href', 'https://twitter.com/intent/tweet?text=' + tit + '%20' + url);
            c.find('.compartilhar-email').attr('href', 'mailto:?subject=Compartilhamento Renault&body=' + tit + '%20' + url + '.');
            c.find('.compartilhar-facebook').attr('href', 'https://www.facebook.com/sharer.php?u=' + url + '&title=' + tit);
            c.find('.compartilhar-linkedin').attr('href', 'https://www.linkedin.com/sharing/share-offsite/?url=' + url + '&title=' + tit);
            c.find('.compartilhar-telegram').attr('href', 'https://telegram.me/share/url?url=' + url + '&text=' + tit);
            $('.compartilhar-popup').stop().fadeIn();
            $('.compartilhar-container').stop().animate({bottom: 0});
        }
           
      });

}

function _init() {
  // $("body").on("click", ".editor-card", e => {
  //   _show();
  // });
}

return {
  show: _show,
  init: _init
}  

})(window, document);

Editor.init();
