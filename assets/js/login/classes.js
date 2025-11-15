class LOGIN {
  constructor(type){
    this.type = type;
  }

  enter(formData){
    let email = formData.get('email');
    let password = formData.get('senha');
    let remember = formData.get('conectado');

    const params = { q:'login', email:email, password:password };

    const url = new URL( window.location );
    
    const platformName = url.searchParams.get('app');
    const buildVersion = url.searchParams.get('build');
    const deviceKey = url.searchParams.get('device');
    
    if (deviceKey) {
        params.os = platformName;
        params.build = buildVersion;
        params.device = deviceKey;
    }


    app.request(params, false, 'token', ( res ) => {
       if(res.error != null) {
        if(res.error == 'Email não cadastrado.'){
           $('input[type="email"]').removeClass('ok');
           $('input[type="email"]').addClass('error');
           $('input[type="email"]').parent().find('.msg').text(res.error);
        }
        if(res.error == 'Senha Inválida.'){
           $('input[type="password"]').removeClass('ok');
           $('input[type="password"]').addClass('error');
           $('input[type="password"]').parent().find('.msg').text(res.error);
        }
        return;
       } else {
           const keeperParams = { name:res.name, short: res.short_name, email: res.email, bir:res.bir, level:res.level, token:res.secret_key, image:res.image_url, categories:res.categories, role:res.category_name };

           if(BASE_URL != 'http://localhost/newfeed/'){
               dataLayer.push({
                  event: 'form_submit',
                  userId: res.bir,
                  userEmail: res.email,
                  userRole: res.category_name,
                  userName: res.name,
               });
           }

           if(remember != null) {
               keeper.set(keeperParams, STORAGE_TYPE, 10);
           } else {
               keeper.set(keeperParams, STORAGE_TYPE, 1);
           }
           $('body').addClass('preload');
           $('main').load('dashboard.html');
       }
    });
    
  }

  password(formData){
    let email = formData.get('email');

    const params = { q:'password_request', email:email};

    app.request(params, false, 'token', ( res ) => {
        if(res.error != null) {
            if(res.error == 'Email não cadastrado.'){
               $('input[type="email"]').removeClass('ok');
               $('input[type="email"]').addClass('error');
               $('input[type="email"]').parent().find('.msg').text(res.error);
            }
            return;
        } else {
            $('input[type="email"]').removeClass('error').removeClass('ok');
            $('[data-anchor="step-1"]').removeClass('selected');
            $('[data-anchor="step-2"]').addClass('selected');
        }
    });
    
  }

  newPassword(formData){
    let password = formData.get('senha');

    const params = { q:'password_confirm', password:password };

    app.request(params, false, 'token', ( res ) => {
        if(res.error != null) {
            return;
        } else {
            $('[data-anchor="step-1"]').removeClass('selected');
            $('[data-anchor="step-2"]').addClass('selected');
        }
    });
    
  }

  create( formData){
    let name = formData.get('nome') + ' ' + formData.get('sobrenome');
    let email = formData.get('email');
    let bir = formData.get('bir');
    let password = formData.get('senha');

    const params = { q:'create_user', name:name, email:email, bir:bir, password:password };

    app.request(params, false, 'token', ( res ) => {
        if(res.error != null) {
            if(res.error == 'Serão permitidos apenas e-mails empresariais.'){
              $('[data-anchor="step-4"]').removeClass('selected');
              $('[data-anchor="bull-4"]').removeClass('selected');
              $('[data-anchor="step-2"]').addClass('selected');
              $('[data-anchor="bull-2"]').addClass('selected');

              $('input[type="email"]').removeClass('ok');
              $('input[type="email"]').addClass('error');
              $('input[type="email"]').parent().find('.msg').text(res.error);
            } else {
              $('[data-anchor="step-4"]').removeClass('selected');
              $('[data-anchor="bull-4"]').removeClass('selected');
              $('[data-anchor="step-3"]').addClass('selected');
              $('[data-anchor="bull-3"]').addClass('selected');

              $('input[name="bir"]').removeClass('ok');
              $('input[name="bir"]').addClass('error');
              $('input[name="bir"]').parent().find('.msg').text(res.error);
            }
            return;
        } else {
            $('.bullets').hide();
            $('.bts').hide();
            $('.txt-legal').hide();

            $('[data-anchor="step-4"]').removeClass('selected');
            $('[data-anchor="bull-4"]').removeClass('selected');
            $('[data-anchor="step-5"]').addClass('selected');
            $('[data-anchor="bull-5"]').addClass('selected');
        }
    });
  }

  confirm(){
    const params = { q:'confirm_user'};

    app.request(params, false, 'token', ( res ) => {
        if(res.error != null) {
            return;
        } else {
            makeHash('login');
        }
    });
    
  }
}

const loging = new LOGIN();
window.login = loging;
