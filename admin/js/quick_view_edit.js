$(function() {
	
	$('#message').val( $('#editor-container').html() );
	
	var toolbarOptions = {
        container: [
			['bold', 'italic', 'underline'], ['link'],
			[{ 'list': 'ordered'}, { 'list': 'bullet' }],
			['emoji'],  ['video']
		],
        handlers: {
          'emoji': function () {}
        }
      }
	
	var quill = new Quill('#editor-container', {
		modules: {
			toolbar: toolbarOptions,
			"emoji-toolbar": true,
          "emoji-shortname": true,
		},
		placeholder: 'Escreva aqui...',
		theme: 'snow'
	});
	
	quill.on('text-change', function() {
		$('#message').val( quill.container.firstChild.innerHTML );
	});
/**************************************************************************************/

	$('#strengths').val( $('#editor-container2').html() );

	var quill2 = new Quill('#editor-container2', {
		modules: {
			toolbar: toolbarOptions,
			"emoji-toolbar": true,
          "emoji-shortname": true,
		},
		placeholder: 'Escreva aqui...',
		theme: 'snow'
	});
	
	quill2.on('text-change', function() { 
		$('#strengths').val( quill2.container.firstChild.innerHTML );
	});
	
	/**************************************************************************************/

	$('#legal_text').val( $('#editor-container3').html() );

	var quill3 = new Quill('#editor-container3', {
		modules: {
			toolbar: toolbarOptions,
			"emoji-toolbar": true,
          "emoji-shortname": true,
		},
		placeholder: 'Escreva aqui...',
		theme: 'snow'
	});
	
	quill3.on('text-change', function() { 
		$('#legal_text').val( quill3.container.firstChild.innerHTML );
	});
	
	/**************************************************************************************/
/*
	$('#attributes').val( $('#editor-container4').html() );

	var quill4 = new Quill('#editor-container4', {
		modules: {
			toolbar: toolbarOptions,
			"emoji-toolbar": true,
          "emoji-shortname": true,
		},
		placeholder: 'Escreva aqui...',
		theme: 'snow'
	});
	
	quill4.on('text-change', function() { 
		$('#attributes').val( quill4.container.firstChild.innerHTML );
	});
	*/
	/************************************************************/
	
	

	const dropifyMessage = 'Arraste e solte um arquivo ou clique para fazer upload';

	$('.dropify').dropify({
		tpl: {
			//clearButton: ''
		},
		messages: {
			default: dropifyMessage,
			replace: dropifyMessage
		}
	});
	  
	 $('#image').on('dropify.afterClear', function(event, element) {

			$('#guarda_arq2').val('0');
    });

	 $('#image_highlight').on('dropify.afterClear', function(event, element) {

			$('#guarda_arq3').val('0');
    });


	$("#stamps_btn").click(function(){
		$("#target").submit();				 
	});
	
	$("#materials_btn").click(function(){
		$("#target").submit();				 
	});

});