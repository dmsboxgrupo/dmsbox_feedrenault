let loadMenu = '';

class CONFIGURACOES {
    constructor(){
        $('aside.left').attr('data-anchor', 'perfil');
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

        $('#email-senha').html(keeper.get('email'));

        /* CONFIGURACOES */
        if(frame == 'configuracoes' && ajudaSUB == 'perfil' || frame == 'configuracoes' && ajudaSUB == null) {
            // $('main').attr('class', 'view-configuracoes');

            $('#internal-holder').html(
                '<div class="configuracao">'+
                '    <div class="formulario">'+
                '        <div class="inputs">'+
                '            <div class="hld-inp">'+
                '                <div class="inp">'+
                '                    <div class="ico perfil"></div>'+
                '                </div>'+
                '                <input type="text" name="nome" placeholder="nome" value="' + keeper.get('name') + '">'+
                '            </div>'+
                '            <div class="hld-inp readonly">'+
                '                <div class="inp">'+
                '                    <div class="ico email"></div>'+
                '                </div>'+
                '                <input type="text" name="email" placeholder="e-mail" value="' + keeper.get('email') + '" readonly>'+
                '            </div>'+
                '            <div class="hld-inp readonly">'+
                '                <div class="inp">'+
                '                    <div class="ico bir"></div>'+
                '                </div>'+
                '                <input type="text" name="bir" placeholder="BIR da concessionária" value="' + keeper.get('bir') + '" readonly>'+
                '            </div>'+
                // '            <div class="hld-inp">'+
                // '                <div class="inp">'+
                // '                    <div class="ico senha"></div>'+
                // '                </div>'+
                // '                <input type="password" name="senha" placeholder="senha" value="">'+
                // '                <a href="#" class="bt-view" data-target="view-password">'+
                // '                    <svg xmlns="http://www.w3.org/2000/svg" version="1.1" width="28.9" height="21.9" viewBox="0 0 28.9 21.9">'+
                // '                      <g class="close">'+
                // '                        <path fill="#888b8d" d="M14.5,19.5c-4.5,0-8.1-3.8-8-8.3,0-4.5,3.5-8.2,8-8.3,4.5,0,8.1,3.8,8,8.3,0,4.5-3.5,8.2-8,8.3M14.5,4.9c-3.4,0-6.1,2.9-6.1,6.3,0,3.4,2.7,6.3,6.1,6.3,3.4,0,6.1-2.9,6.1-6.3,0-3.4-2.7-6.3-6.1-6.3"/>'+
                // '                        <path fill="#888b8d" d="M14.6,19.6c-4.9,0-9.8-2.6-14.4-7.6l-.3-.3v-1l.2-.3c.2-.3,5.4-7.2,13.4-7.7,5.2-.3,10.3,2.3,15.1,7.6l.3.3v1l-.2.3c-.2.3-5.4,7.2-13.4,7.7-.3,0-.5,0-.8,0M2,11.1c4.3,4.6,8.8,6.8,13.3,6.5,6-.3,10.4-4.9,11.7-6.5-4.3-4.6-8.8-6.8-13.3-6.5-6,.3-10.4,4.9-11.7,6.5"/>'+
                // '                      </g>'+
                // '                      <g class="open">'+
                // '                        <path fill="#888b8d" d="M0,12v-1.6C4,5.7,9.3,3,14.5,3c.9,0,1.8,0,2.7.3l-1.2,1.6c-.5,0-1-.1-1.5-.1-3.7,0-6,2.5-6,6.5,0,1,.1,1.9.4,2.8l-1.2,1.5c-.7-1.3-1.1-2.8-1-4.3,0-1.9.5-3.8,1.7-5.2-2.6,1.3-5,3-6.9,5.2,1.8,1.8,3.8,3.4,6,4.8l-1.1,1.3c-2.4-1.4-4.6-3.2-6.4-5.3M21.9,0l1.4,1.1L7.4,21.9l-1.4-1.1L21.9,0ZM14.5,17.7c3.7,0,6-2.5,6-6.5,0-1-.2-2-.5-2.9l1.2-1.5c.8,1.3,1.1,2.9,1.1,4.4,0,1.8-.5,3.6-1.5,5.1,2.5-1.3,4.8-3,6.7-5.1-1.8-1.9-3.8-3.5-6-4.8l1-1.3c2.5,1.4,4.7,3.2,6.5,5.3v1.6c-4,4.7-9.3,7.4-14.5,7.4-.9,0-1.9,0-2.8-.3l1.2-1.6c.5.1,1,.2,1.6.2"/>'+
                // '                      </g>'+
                // '                    </svg>'+
                // '                </a>'+
                // '            </div>'+
                '        </div>'+
                '        <div class="bts">'+
                '            <div class="bt alt">'+
                '                <a href="#" class="bt-salvar" data-target="name">salvar</a>'+
                '            </div>'+
                '            <div class="bt">'+
                '                <a href="#" class="bt-changeview" data-target="configuracoes" data-medium="alteracao-de-senha">quero mudar a minha senha</a>'+
                '            </div>'+
                '            <div class="bt-grid">'+
                '                <a href="#" class="bt-modal" data-target="deletar-perfil">deletar perfil</a>'+
                '            </div>'+
                '        </div>'+
                '    </div>'+
                '</div>'
            )
        }

        if(frame == 'configuracoes' && ajudaSUB == 'alteracao-de-senha') {
            // $('main').attr('class', 'view-alteracao-de-senha');
            
            $('#internal-holder').html(`
                <div class="configuracao">
                    <div class="msg-error">preencha todos os campos</div>
                    <div class="formulario">
                        <div class="inputs">
                            <div class="hld-inp">
                                <div class="inp">
                                    <div class="ico senha"></div>
                                </div>
                                <input type="password" class="senha-atual" name="senha-atual" placeholder="informe a senha atual">

                                <a href="#" class="bt-view" data-target="view-password">
                                    <svg xmlns="http://www.w3.org/2000/svg" version="1.1" width="28.9" height="21.9" viewBox="0 0 28.9 21.9">
                                      <g class="close">
                                        <path fill="#888b8d" d="M14.5,19.5c-4.5,0-8.1-3.8-8-8.3,0-4.5,3.5-8.2,8-8.3,4.5,0,8.1,3.8,8,8.3,0,4.5-3.5,8.2-8,8.3M14.5,4.9c-3.4,0-6.1,2.9-6.1,6.3,0,3.4,2.7,6.3,6.1,6.3,3.4,0,6.1-2.9,6.1-6.3,0-3.4-2.7-6.3-6.1-6.3"/>
                                        <path fill="#888b8d" d="M14.6,19.6c-4.9,0-9.8-2.6-14.4-7.6l-.3-.3v-1l.2-.3c.2-.3,5.4-7.2,13.4-7.7,5.2-.3,10.3,2.3,15.1,7.6l.3.3v1l-.2.3c-.2.3-5.4,7.2-13.4,7.7-.3,0-.5,0-.8,0M2,11.1c4.3,4.6,8.8,6.8,13.3,6.5,6-.3,10.4-4.9,11.7-6.5-4.3-4.6-8.8-6.8-13.3-6.5-6,.3-10.4,4.9-11.7,6.5"/>
                                      </g>
                                      <g class="open">
                                        <path fill="#888b8d" d="M0,12v-1.6C4,5.7,9.3,3,14.5,3c.9,0,1.8,0,2.7.3l-1.2,1.6c-.5,0-1-.1-1.5-.1-3.7,0-6,2.5-6,6.5,0,1,.1,1.9.4,2.8l-1.2,1.5c-.7-1.3-1.1-2.8-1-4.3,0-1.9.5-3.8,1.7-5.2-2.6,1.3-5,3-6.9,5.2,1.8,1.8,3.8,3.4,6,4.8l-1.1,1.3c-2.4-1.4-4.6-3.2-6.4-5.3M21.9,0l1.4,1.1L7.4,21.9l-1.4-1.1L21.9,0ZM14.5,17.7c3.7,0,6-2.5,6-6.5,0-1-.2-2-.5-2.9l1.2-1.5c.8,1.3,1.1,2.9,1.1,4.4,0,1.8-.5,3.6-1.5,5.1,2.5-1.3,4.8-3,6.7-5.1-1.8-1.9-3.8-3.5-6-4.8l1-1.3c2.5,1.4,4.7,3.2,6.5,5.3v1.6c-4,4.7-9.3,7.4-14.5,7.4-.9,0-1.9,0-2.8-.3l1.2-1.6c.5.1,1,.2,1.6.2"/>
                                      </g>
                                    </svg>
                                </a>
                            </div>
                            <div class="hld-inp">
                                <div class="inp">
                                    <div class="ico senha"></div>
                                </div>
                                <input type="password" class="senha-check" name="nova-senha" placeholder="nova senha">

                                <a href="#" class="bt-view" data-target="view-password">
                                    <svg xmlns="http://www.w3.org/2000/svg" version="1.1" width="28.9" height="21.9" viewBox="0 0 28.9 21.9">
                                      <g class="close">
                                        <path fill="#888b8d" d="M14.5,19.5c-4.5,0-8.1-3.8-8-8.3,0-4.5,3.5-8.2,8-8.3,4.5,0,8.1,3.8,8,8.3,0,4.5-3.5,8.2-8,8.3M14.5,4.9c-3.4,0-6.1,2.9-6.1,6.3,0,3.4,2.7,6.3,6.1,6.3,3.4,0,6.1-2.9,6.1-6.3,0-3.4-2.7-6.3-6.1-6.3"/>
                                        <path fill="#888b8d" d="M14.6,19.6c-4.9,0-9.8-2.6-14.4-7.6l-.3-.3v-1l.2-.3c.2-.3,5.4-7.2,13.4-7.7,5.2-.3,10.3,2.3,15.1,7.6l.3.3v1l-.2.3c-.2.3-5.4,7.2-13.4,7.7-.3,0-.5,0-.8,0M2,11.1c4.3,4.6,8.8,6.8,13.3,6.5,6-.3,10.4-4.9,11.7-6.5-4.3-4.6-8.8-6.8-13.3-6.5-6,.3-10.4,4.9-11.7,6.5"/>
                                      </g>
                                      <g class="open">
                                        <path fill="#888b8d" d="M0,12v-1.6C4,5.7,9.3,3,14.5,3c.9,0,1.8,0,2.7.3l-1.2,1.6c-.5,0-1-.1-1.5-.1-3.7,0-6,2.5-6,6.5,0,1,.1,1.9.4,2.8l-1.2,1.5c-.7-1.3-1.1-2.8-1-4.3,0-1.9.5-3.8,1.7-5.2-2.6,1.3-5,3-6.9,5.2,1.8,1.8,3.8,3.4,6,4.8l-1.1,1.3c-2.4-1.4-4.6-3.2-6.4-5.3M21.9,0l1.4,1.1L7.4,21.9l-1.4-1.1L21.9,0ZM14.5,17.7c3.7,0,6-2.5,6-6.5,0-1-.2-2-.5-2.9l1.2-1.5c.8,1.3,1.1,2.9,1.1,4.4,0,1.8-.5,3.6-1.5,5.1,2.5-1.3,4.8-3,6.7-5.1-1.8-1.9-3.8-3.5-6-4.8l1-1.3c2.5,1.4,4.7,3.2,6.5,5.3v1.6c-4,4.7-9.3,7.4-14.5,7.4-.9,0-1.9,0-2.8-.3l1.2-1.6c.5.1,1,.2,1.6.2"/>
                                      </g>
                                    </svg>
                                </a>
                            </div>
                            <div class="hld-inp">
                                <div class="inp">
                                    <div class="ico senha"></div>
                                </div>
                                <input type="password" class="senha-repeat" name="confirma-senha" placeholder="confirme sua nova senha">

                                <a href="#" class="bt-view" data-target="view-password">
                                    <svg xmlns="http://www.w3.org/2000/svg" version="1.1" width="28.9" height="21.9" viewBox="0 0 28.9 21.9">
                                      <g class="close">
                                        <path fill="#888b8d" d="M14.5,19.5c-4.5,0-8.1-3.8-8-8.3,0-4.5,3.5-8.2,8-8.3,4.5,0,8.1,3.8,8,8.3,0,4.5-3.5,8.2-8,8.3M14.5,4.9c-3.4,0-6.1,2.9-6.1,6.3,0,3.4,2.7,6.3,6.1,6.3,3.4,0,6.1-2.9,6.1-6.3,0-3.4-2.7-6.3-6.1-6.3"/>
                                        <path fill="#888b8d" d="M14.6,19.6c-4.9,0-9.8-2.6-14.4-7.6l-.3-.3v-1l.2-.3c.2-.3,5.4-7.2,13.4-7.7,5.2-.3,10.3,2.3,15.1,7.6l.3.3v1l-.2.3c-.2.3-5.4,7.2-13.4,7.7-.3,0-.5,0-.8,0M2,11.1c4.3,4.6,8.8,6.8,13.3,6.5,6-.3,10.4-4.9,11.7-6.5-4.3-4.6-8.8-6.8-13.3-6.5-6,.3-10.4,4.9-11.7,6.5"/>
                                      </g>
                                      <g class="open">
                                        <path fill="#888b8d" d="M0,12v-1.6C4,5.7,9.3,3,14.5,3c.9,0,1.8,0,2.7.3l-1.2,1.6c-.5,0-1-.1-1.5-.1-3.7,0-6,2.5-6,6.5,0,1,.1,1.9.4,2.8l-1.2,1.5c-.7-1.3-1.1-2.8-1-4.3,0-1.9.5-3.8,1.7-5.2-2.6,1.3-5,3-6.9,5.2,1.8,1.8,3.8,3.4,6,4.8l-1.1,1.3c-2.4-1.4-4.6-3.2-6.4-5.3M21.9,0l1.4,1.1L7.4,21.9l-1.4-1.1L21.9,0ZM14.5,17.7c3.7,0,6-2.5,6-6.5,0-1-.2-2-.5-2.9l1.2-1.5c.8,1.3,1.1,2.9,1.1,4.4,0,1.8-.5,3.6-1.5,5.1,2.5-1.3,4.8-3,6.7-5.1-1.8-1.9-3.8-3.5-6-4.8l1-1.3c2.5,1.4,4.7,3.2,6.5,5.3v1.6c-4,4.7-9.3,7.4-14.5,7.4-.9,0-1.9,0-2.8-.3l1.2-1.6c.5.1,1,.2,1.6.2"/>
                                      </g>
                                    </svg>
                                </a>
                            </div>
                        </div>

                        <div class="validacao">
                            <div class="item" data-anchor="numCaracter">8-12 caracteres</div>
                            <div class="item" data-anchor="specialCaracter">Caracter especial</div>
                            <div class="item" data-anchor="upperCase">Letra maiúscula</div>
                            <div class="item" data-anchor="lowerCase">Letra minúscula</div>
                            <div class="item" data-anchor="num">Número</div>
                        </div>

                        <div class="bts">
                            <div class="bt alt">
                                <a href="#" class="bt-salvar" data-target="password">alterar</a>
                            </div>
                            <div class="bt-grid">
                                <a href="#" class="bt-enviar-senha" data-target="senha-esqueci">esqueci minha senha</a>
                            </div>
                        </div>
                    </div>
                </div>
            `)
         }
    }
}
const makeCONFIGURACOES = new CONFIGURACOES();
window.configuracoes = makeCONFIGURACOES;


$(document).on('input', '.senha-check', function(event){
  event.preventDefault();

  let erros = 0;
  let senha = $(this).val();
  let specialCaracter = /[-’/`~!#*$@_%+=.,^&(){}[\]|;:”<>?\\]/g;
  let lowerCaseLetters = /[a-z]/g;
  let upperCaseLetters = /[A-Z]/g;
  let numbers = /[0-9]/g;

  if(senha.length > 7 && senha.length < 13){
    $('[data-anchor="numCaracter"]').removeClass('error').addClass('ok');
  } else {
    $('[data-anchor="numCaracter"]').removeClass('ok').addClass('error');
    erros++;
  }

  if(senha.match(specialCaracter)){
    $('[data-anchor="specialCaracter"]').removeClass('error').addClass('ok');
  } else {
    $('[data-anchor="specialCaracter"]').removeClass('ok').addClass('error');
    erros++;
  }

  if(senha.match(lowerCaseLetters)){
    $('[data-anchor="lowerCase"]').removeClass('error').addClass('ok');
  } else {
    $('[data-anchor="lowerCase"]').removeClass('ok').addClass('error');
    erros++;
  }

  if(senha.match(upperCaseLetters)){
    $('[data-anchor="upperCase"]').removeClass('error').addClass('ok');
  } else {
    $('[data-anchor="upperCase"]').removeClass('ok').addClass('error');
    erros++;
  }

  if(senha.match(numbers)){
    $('[data-anchor="num"]').removeClass('error').addClass('ok');
  } else {
    $('[data-anchor="num"]').removeClass('ok').addClass('error');
    erros++;
  }

  if(erros == 0) {
    $(this).addClass('ok');
  } else {
    $(this).removeClass('ok');
  }
});

$(document).on('input', '.senha-repeat', function(event){
  event.preventDefault();

  if($(this).val() != $('.senha-check').val()) { 
    $(this).parent().removeClass('ok').addClass('error');
  }else {
    $(this).parent().removeClass('error').addClass('ok');
  }
})