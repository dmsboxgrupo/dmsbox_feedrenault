/* CONFIG */
// const API_URL = 'https://feedrenault.com.br/api.php';
const API_URL = 'https://feedrenault.com.br/api.php';
const UPLOADS_URL = 'https://feedrenault.com.br/uploads/';
const BASE_URL = 'https://feedrenault.com.br/';

const STORAGE_TYPE = 'cookie';
const STORAGE_BASENAME = 'feedRenault_';
const STORAGE_TIME = 1;
const DEFAULT_CALLBACK = () => {};

/* CLASSES */
class API {
  constructor(apiURL){
    this.apiURL = apiURL;
  }

  request(params, upload, variable = null, callback = DEFAULT_CALLBACK){
    this.arrayURL = '';
    this.requestOptions = '';

    for (let [key, value] of Object.entries(params)) {
        this.arrayURL += key + '=' + value + '&';
    }

    if(variable == 'token'){
      this.arrayURL += 'token='+keeper.get('token');
    } else {
      this.arrayURL += 'secret_key='+keeper.get('token');
    }
    this.arrayURL += '&version=7';
    this.requestURL = this.apiURL + '?' + this.arrayURL;

    if(upload) {
      this.requestOptions = {
        method: "POST",
        body: upload,
        redirect: "follow"
      };
    } else {
      this.requestOptions = {
        method: "POST",
      };
    }
    
    console.log(this.requestURL);

    fetch( this.requestURL, this.requestOptions )
      .then(data => data.text())
      .then((text) => {
        const response = JSON.parse( text );

        if(response.error == 'Token expirado.') {
          console.log('TOKEN Expirado!');
          //keeper.clear();
          //window.location.href = baseURL;
        } else {
          callback( response );
        }
      }).catch((error) => {
          console.log(error);
          //keeper.clear();
          //window.location.href = baseURL;
      });
  }
}

class KEEPER {
  constructor(type, time){
    this.type = type; 
    this.time = time; 
  }

  set(data, type = this.type, time = this.time){ 
    if(type == 'local') {
      for (let item in data) {
        localStorage.setItem(STORAGE_BASENAME+item, data[item]);
      }
    }

    if(type == 'session') {
      for (let item in data) {
        sessionStorage.setItem(STORAGE_BASENAME+item, data[item]);
      }
    }

    if(type == 'cookie') {
      const date = new Date();
      date.setTime(date.getTime() + (time * 24 * 60 * 60 * 1000));
      let expires = date.toUTCString();

      for (let item in data) {
        document.cookie = STORAGE_BASENAME+item+'='+data[item]+'; expires='+expires+'; path=/; domain='+window.location.host+'; secure="";';
      }
    }

  }

  get(field, type = this.type){
    if(type == 'local') {
      return localStorage.getItem(STORAGE_BASENAME+field);
    }
    if(type == 'session') {
      return sessionStorage.getItem(STORAGE_BASENAME+field);
    }
    if(type == 'cookie') {
      const cookies = document.cookie.split(";");

      for (let i = 0; i < cookies.length; i++) {
          const cookie = cookies[i];
          const eqPos = cookie.indexOf("=");
          let name = eqPos > -1 ? cookie.substr(1, eqPos -1) : cookie;
          if(i == 0){
            name = eqPos > -1 ? cookie.substr(0, eqPos) : cookie;
          }
          const value = eqPos > -1 ? cookie.substr(eqPos + 1, cookie.length) : cookie;

          if(name == STORAGE_BASENAME+field) {
            return value;
          }
      }
    }
  }

  clear(type = this.type){
    if(type == 'local') {
      localStorage.clear();
    }
    if(type == 'session') {
      sessionStorage.clear();
    }
    if(type == 'cookie') {
      const cookies = document.cookie.split(";");

      for (let i = 0; i < cookies.length; i++) {
          const cookie = cookies[i];
          const eqPos = cookie.indexOf("=");
          let name = eqPos > -1 ? cookie.substr(1, eqPos -1) : cookie;
          if(i == 0){
            name = eqPos > -1 ? cookie.substr(0, eqPos) : cookie;
          }
          const nodeCookie = name.substr(0, STORAGE_BASENAME.length);

          if(nodeCookie == STORAGE_BASENAME){         
            document.cookie = cookie.replace(/^ +/, '').replace(/=.*/, '=;expires=' + new Date().toUTCString() + ';path=/; path=/; domain='+window.location.host+'; secure="";');
          }
      }
    }
  }
}

class UTILITY {
  constructor(){
  }

  factory(templateString, templateVars) {
    return new Function("return `"+templateString +"`;").call(templateVars);
  }

  formatBytes(bytes, decimals = 2) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const dm = decimals < 0 ? 0 : decimals;
    const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
  }

  download(link) {
    let mime = '';
    let name = '';
    let down = '';
    let dot = link.lastIndexOf('.');
    let dash = link.lastIndexOf('/');
    let ext = link.substring(dot + 1, link.length);

    if(ext == 'pdf') {
      mime = 'application/pdf';
    } else {
      mime = 'image/' + ext;
    }
    name = link.substring(dash + 1, link.length);
    
    down = BASE_URL + 'actions/download.php?mime=' + mime + '&name=' + name + '&url=' + link;

    if(ext == 'pdf') {
      window.open(link, "_blank"); 
    } else {
      window.open(down, "_self"); 
    }
  }

  slugfy(string, separator = '-'){
    let slug = string.normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase().trim().replace(/[^a-z0-9 ]/g, '').replace(/\s+/g, separator);
    return slug;
  }

  lockDown() {
    $('main').addClass('lockdown');
  }

  release() {
    $('main').removeClass('lockdown');
  }
}


class LOGINCHECK {
  constructor(){
  }

  check(){
    let name = keeper.get('name');
    let email = keeper.get('email');
    let token = keeper.get('token');

    if(name != null && email != null && token != null) {
      return true;
    } else {
      return false;
    }
  }
}

class INITIAL {
  constructor(){
    if(loginStatus.check()){
      dataLayer.push({
              event: 'form_submit',
              userId: keeper.get('bir'),
              userEmail: keeper.get('email'),
              userRole: keeper.get('role'),
              userName: keeper.get('name'),
           });
      
      $('main').load('dashboard.html');
    } else {
      $('main').load('login.html')
    }
  }
}

const app = new API(API_URL);
const keep = new KEEPER(STORAGE_TYPE, STORAGE_TIME);
const util = new UTILITY();
const logincheking = new LOGINCHECK();

window.app = app;
window.baseURL = BASE_URL;
window.uploadsURL = UPLOADS_URL; 
window.keeper = keep;
window.util = util;
window.loginStatus = logincheking;

// Verifica se o navegador suporta Service Workers
// if ('serviceWorker' in navigator) {
//     window.addEventListener('load', () => {
//         navigator.serviceWorker.register('/service-worker.js')
//             .then((registration) => {
//                 console.log('Service Worker registrado com sucesso: ', registration);
//             })
//             .catch((error) => {
//                 console.log('Falha ao registrar o Service Worker: ', error);
//             });
//     });
// }


new INITIAL();