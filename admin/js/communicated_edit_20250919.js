$(function() {

	//$(".select2").select2();
	
	$("#vehicles").select2({
		placeholder: "Selecione o(s) veiculo(s)"
	});

	$("#tags").select2({
		placeholder: "Selecione a(s) Tag(s)"
	});

	$('#text').val( $('#editor-container').html() );

	$("#topics_btn").click(function(){
		$("#target").submit();				 
	});
	
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
		$('#text').val( quill.container.firstChild.innerHTML );
	});

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

	$('.fileinput-filename').on('DOMSubtreeModified',function(){
		arq_up = $('#arq_up').text();
		
		if (arq_up =="")
			$('#guarda_arq').val(arq_up);
		else
			$('#guarda_arq').val(arq_up);
		
		//alert($('#guarda_arq').val());
	})
	
	$('#tell_client').on('dropify.afterClear', function(event, element) {

			$('#guarda_arq3').val('0');
    });
	
	$('#image').on('dropify.afterClear', function(event, element) {

			$('#guarda_arq2').val('0');
    });
	
	$('#image_highlight').on('dropify.afterClear', function(event, element) {

			$('#guarda_arq4').val('0');
    });


	$('#text_highlight').val( $('#editor-container2').html() );

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
		$('#text_highlight').val( quill2.container.firstChild.innerHTML );
	});
});