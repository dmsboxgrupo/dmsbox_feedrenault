$(function() {

	// Specify Quill fonts
	var fontList = ['Arial', 'Courier', 'Garamond', 'Poppins', 'Tahoma', 'Times New Roman', 'Verdana'];
	var fontNames = fontList.map(font => getFontName(font));
	var fonts = Quill.import('attributors/class/font');
	fonts.whitelist = fontNames;
	Quill.register(fonts, true);

	// Add fonts to CSS style
	var fontStyles = "";
	fontList.forEach(function(font) {
		var fontName = getFontName(font);
		fontStyles += ".ql-snow .ql-picker.ql-font .ql-picker-label[data-value=" + fontName + "]::before, .ql-snow .ql-picker.ql-font .ql-picker-item[data-value=" + fontName + "]::before {" +
			"content: '" + font + "';" +
			"font-family: '" + font + "', sans-serif;" +
			"}" +
			".ql-font-" + fontName + "{" +
			" font-family: '" + font + "', sans-serif;" +
			"}";
	});

	var toolbarOptions = {
        container: [
          ['bold', 'italic', 'underline', 'strike'],
		  ['divider'],
          ['blockquote', 'code-block'],
          [{ 'header': 1 }, { 'header': 2 }],
          [{ 'list': 'ordered' }, { 'list': 'bullet' }],
          [{ 'script': 'sub' }, { 'script': 'super' }],
          [{ 'indent': '-1' }, { 'indent': '+1' }],
          [{ 'direction': 'rtl' }],
          [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
          [{ 'color': ['#F0F0F0','#64C6CB','#FFE667','#FFC37E','#C4944A','#E6EA00','#EA5045','#B297CC',
				'#BEBEBE','#6BCEFD','#FFD500','#FF8900','#724500','#9ED268','#FF0000','#6600E3',
				'#8A8A8A','#088ECE','#FFBA00','#D07000','#4D3818','#646500','#89000B','#3C1661',
				'#000000','#002E57']
		   }, { 'background': [] }],          
          [{ 'align': [] }],
          ['clean'],
          ['emoji'],
          ['link', 'image', 'video'],
		  //[{ 'font': fonts.whitelist }]
        ],
        handlers: {
          'emoji': function () {}
        }
      }

      /*var quill = new Quill('#editor-container', {
        modules: {
			toolbar: toolbarOptions,
			divider: { cssText: 'border: none;border-bottom: 1px inset;' },
			//"divider": true,
			"emoji-toolbar": true,
			"emoji-shortname": true
			//"emoji-textarea": true,
        },
        placeholder: 'Escreva aqui...',
        theme: 'snow'
      });*/

	/*var quill;
	$(function() {
		// Append the CSS stylesheet to the page
		var node = document.createElement('style');
		node.innerHTML = fontStyles;
		document.body.appendChild(node);

		quill = new Quill('#editor-container', {			
			modules: {
				toolbar: toolbarOptions,
				divider: { cssText: 'border: none;border-bottom: 1px inset;' },
				"emoji-toolbar": true,
				"emoji-shortname": true
				
			},
			placeholder: 'Escreva aqui...',
			theme: 'snow'
		});
	});
*/
	// Generate code-friendly font names
	function getFontName(font) {
		return font.toLowerCase().replace(/\s/g, "-");
	}

    
	var quill = new Quill('#editor-container', {
        modules: {
			toolbar: toolbarOptions,
			divider: { cssText: 'border: none;border-bottom: 1px inset;' },
			//"divider": true,
			"emoji-toolbar": true,
			"emoji-shortname": true
			//"emoji-textarea": true,
        },
        placeholder: 'Escreva aqui...',
        theme: 'snow'
      });
	
	quill.on('text-change', function() {
		$('#text').val( quill.container.firstChild.innerHTML );
	});


});