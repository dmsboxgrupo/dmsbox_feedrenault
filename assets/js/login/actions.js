/* HASH */
const hash = [
                {internal: 'login', hash: 'login'},
                {internal: 'video', hash: 'video'},
                {internal: 'forgot', hash: 'forgot-password'},
                {internal: 'account', hash: 'create-account'},
                {internal: 'password', hash: 'new-password'},
                {internal: 'password', hash: 'login-cadastro-senha'},
                {internal: 'password-ok', hash: 'password-ok'},
                {internal: 'error-inactive', hash: 'error-inactive'},
                {internal: 'error-deactivate', hash: 'error-deactivate'},
                {internal: 'login-confirmacao-email', hash: 'login-confirmacao-email'},
                {internal: 'login-confirmacao', hash: 'login-confirmacao'},
             ]


function makeHash(string, type) {
    let loadFrame = '';
    let unknown = true;

    if(type == 'internal'){
        for (let i = 0; i < hash.length; i++) {
            if(hash[i].internal == string){
                location.hash = hash[i].hash;
                loadFrame = string;
                unknown = false;
            }
        }
    } else {
        let url = string.substr(1);
        let index = url.indexOf('&');
        if(index > 0){
            str = url.substr(0, index);
        } else {
            str = url;
        }

        for (let i = 0; i < hash.length; i++) {
            if(hash[i].hash == str){
                loadFrame = hash[i].internal;
                unknown = false;
            }
        }
    }

    if(unknown){
        window.location.hash = 'login';
    }

    $('body').attr('class', loadFrame);
    
    switch (loadFrame) {
      case 'login':
        $('#holder').load('parts/login/form-login.html');
        form = $('#formLogin');
        break;
      case 'video':
        $('#holder').load('parts/login/video.html');
        break;
      case 'forgot':
        $('#holder').load('parts/login/form-password.html', ()=>{
            $('[data-anchor="step-1"]').addClass('selected');
        });
        break;
      case 'password':
        $('#holder').load('parts/login/form-new-password.html', ()=>{
            const queryString = window.location.hash;
            const urlParams = new URLSearchParams(queryString);
            const token = urlParams.get('token');

            const keeperParams = { token:token };
            keeper.set(keeperParams, STORAGE_TYPE, 1);
            $('[data-anchor="step-1"]').addClass('selected');
        });
        break;
      case 'password-ok':
        $('#holder').load('parts/login/form-new-password.html', ()=>{
            $('[data-anchor="step-2"]').addClass('selected');
        });
        break;
      case 'account':
        $('#holder').load('parts/login/form-account.html');
        currentForm = 1;
        break;
      case 'error-inactive':
        $('#holder').load('parts/login/error.html', ()=>{
            $('[data-anchor="step-1"]').addClass('selected');
        });
        break;
      case 'error-deactivate':
        $('#holder').load('parts/login/error.html', ()=>{
            $('[data-anchor="step-2"]').addClass('selected');
        });
        break;
      case 'login-confirmacao-email':
        let queryString = window.location.hash;
        let urlParams = new URLSearchParams(queryString);
        let token = urlParams.get('token');

        let keeperParams = { token:token };
        keeper.set(keeperParams, STORAGE_TYPE, 1);

        login.confirm();
        break;
      case 'login-confirmacao':
        let queryStringB = window.location.hash;
        let urlParamsB = new URLSearchParams(queryStringB);
        let tokenB = urlParamsB.get('token');

        let keeperParamsB = { token:tokenB };
        keeper.set(keeperParamsB, STORAGE_TYPE, 1);

        login.confirm();
        break;
    }
}

/* NAV FORMS */
let currentForm = 1;

function navForm(event){
    if(event == 'next') {
        let oks = $('#formCreate').find('.ok').length;

        if(currentForm == 1) {
            if(oks < 2){
                return;
            }
        } else if(currentForm == 2) {
            if(oks < 3){
                return;
            }
        } else if(currentForm == 3) {
            if(oks < 4){
                return;
            }
        } else if(currentForm == 4) {
            if(oks < 11){
                return;
            } else {
                $('#formCreate').submit();
                return;
            }
        } 

        $('[data-anchor="step-' + currentForm + '"]').removeClass('selected');
        $('[data-anchor="bull-' + currentForm + '"]').removeClass('selected');
        currentForm++;
        $('[data-anchor="step-' + currentForm + '"]').addClass('selected');
        $('[data-anchor="bull-' + currentForm + '"]').addClass('selected');
    } else {
        if(currentForm - 1 <= 0) {
            makeHash('login', 'internal');
        } else {
            $('[data-anchor="step-' + currentForm + '"]').removeClass('selected');
            $('[data-anchor="bull-' + currentForm + '"]').removeClass('selected');
            currentForm--;
            $('[data-anchor="step-' + currentForm + '"]').addClass('selected');
            $('[data-anchor="bull-' + currentForm + '"]').addClass('selected');
        }
    }
}

/* PASSWORD */
function viewPassword(event){
  if($(event.target).parent().hasClass('esconder')){
    $(event.target).parent().removeClass('esconder');
    $(event.target).parent().find('input').attr('type', 'password');
  } else {
    $(event.target).parent().addClass('esconder');
    $(event.target).parent().find('input').attr('type', 'text');
  }
};


/* FROM SUBMITED */
function formSubmit(event) {
    let thisForm = event.target;
    let id = event.target.id;
    let fields = $(thisForm).attr('data-fields');
    let oks = $(thisForm).find('.ok').length;

    let formData = new FormData(thisForm);

    switch (id) {
      case 'formLogin':
        if(fields == oks){
            login.enter(formData);
        } 
        break;
      case 'formPassword':
        if(fields == oks){
            $('#formPasswordResend input[name="email"]').val(formData.get('email'));
            login.password(formData);
        } 
        break;
      case 'formPasswordResend':
        login.password(formData);
        break;
      case 'formCreate':
        login.create(formData);
        break;
      case 'formNewPassword':
        if(fields <= oks){
            login.newPassword(formData);
        }
        break;
    }
}

/* VALIDATION */
function checkInput(input) {
    let value = input.val();

    if(input.hasClass('valid-email')){
        if(value == '') { 
            input.parent().find('.msg').html('preencha com seu e-mail');
            input.removeClass('ok').addClass('error');
            return;
        }
        if(!validateEmail(value)) { 
            input.parent().find('.msg').html('insira um e-mail válido.');
            input.removeClass('ok').addClass('error');
            return;
        }

        input.removeClass('error').addClass('ok');
    }

    if(input.hasClass('valid-senha')){
        if(value == '') { 
            input.parent().find('.msg').html('insira sua senha');
            input.removeClass('ok').addClass('error');
            return;
        }
        // if(value.length < 8 || value.length > 12) { 
        //     input.parent().find('.msg').html('verifique sua senha');
        //     input.removeClass('ok').addClass('error');
        //     return;
        // }
        
        input.removeClass('error').addClass('ok');
    }

    if(input.hasClass('valid-text')){
        let inpName = input.attr('name');

        if(value == '') { 
            input.parent().find('.msg').html('preencha com seu '+inpName);
            input.removeClass('ok').addClass('error');
            return;
        }
        if(value.length < 2) { 
            input.parent().find('.msg').html('verifique seu '+inpName);
            input.removeClass('ok').addClass('error');
            return;
        }

        input.removeClass('error').addClass('ok');
    }

    if(input.hasClass('num-bir')){
        if(value == '') { 
            input.parent().find('.msg').html('preencha com o BIR da concessionaria');
            input.removeClass('ok').addClass('error');
            return;
        }
        if(value.length != 7) { 
            input.parent().find('.msg').html('verifique o BIR da concessionaria');
            input.removeClass('ok').addClass('error');
            return;
        }

        input.removeClass('error').addClass('ok');
    }
}

/* MODAL */
function modal(type, target){
  if(type == 'open') {
    $('[data-anchor="' + target + '"]').addClass('open');
  } else {
    $('[data-anchor="' + target + '"]').removeClass('open');
  }
};


function validateEmail(email) {
  var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
  return re.test(email);
}

/* GLOBAL CLICK */
function globalLoginClick(event, target) {
    let targetClassList = target.classList;
    let btTarget = target.getAttribute('data-target');
    let type = target.getAttribute('data-type');
    
    if(btTarget != null) {
        event.preventDefault();
    }

    switch (true) {
      case targetClassList.contains('bt-changeview'):
        makeHash(btTarget, 'internal');
        break;
      case targetClassList.contains('bt-view'):
        viewPassword(event);
        break;
      case targetClassList.contains('nav-form'):
        navForm(btTarget);
        break;
      case targetClassList.contains('bt-modal'):
        modal(type, btTarget);
        break;
    }
};


/* LISTENERS */
$(document).on('submit', 'form', function(event){
  event.preventDefault();

  formSubmit(event);
});

window.addEventListener('hashchange', function() {
    makeHash(location.hash, 'hash');
}, false);

document.addEventListener('click', function(event) {
    globalLoginClick(event, event.target);
}, false);

$(document).on('input', 'input[type="password"]', function(event){
  event.preventDefault();

  var tamanho = $(this).val().length;
  if(tamanho > 0){
    $(this).parent().addClass('preenchido');
  } else {
    $(this).parent().removeClass('preenchido');
  }
});

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
    $(this).removeClass('ok').addClass('error');
  }else {
    $(this).removeClass('error').addClass('ok');
  }
})

$(document).on('input', 'input', function(event){
  checkInput($(this));
});

/* INIT */
$('.num-bir').mask('0000000');


if(location.hash) {
    makeHash(location.hash, 'external');
} else {
    if($(window).width() < 992 ) {
        makeHash('video', 'internal');
    } else {
        makeHash('login', 'internal');
    }
}

$('body').removeClass('preload');