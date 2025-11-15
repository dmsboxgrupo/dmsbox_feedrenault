$(function() {

	$('#text').val( $('#editor-container').html() );

	var toolbarOptions = {
        container: [
          ['bold', 'italic', 'underline'], ['link'],
			[{ 'list': 'ordered'}, { 'list': 'bullet' }],
			['emoji'],
          
        ],
        handlers: {
          'emoji': function () {}
        }
      }

      var quill = new Quill('#editor-container', {
        modules: {
          "toolbar": toolbarOptions,
          "emoji-toolbar": true,
          "emoji-shortname": true,
          //"emoji-textarea": true
        },
        placeholder: 'Escreva aqui...',
        theme: 'snow',
      });

    
	quill.on('text-change', function() {
		$('#text').val( quill.container.firstChild.innerHTML );
	});
	
	const dropifyMessage = 'Arraste e solte um arquivo ou clique para fazer upload';

	$('.dropify').dropify({
		tpl: {
			clearButton: ''
		},
		messages: {
			default: dropifyMessage,
			replace: dropifyMessage
		}
	});


});