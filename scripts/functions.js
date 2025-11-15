function encodeHTML(str){
    return str.replace(/([\u00A0-\u9999<>&])(.|$)/g, function(full, char, next) {
      if(char !== '&' || next !== '#'){
        if(/[\u00A0-\u9999<>&]/.test(next))
          next = '&#' + next.charCodeAt(0) + ';';

        return '&#' + char.charCodeAt(0) + ';' + next;
      }

      return full;
    });
}

function decodeHTML(str){
    return str.replace(/&#([0-9]+);/g, function(full, int) {
        return String.fromCharCode(parseInt(int));
    });
}
