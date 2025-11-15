$(function() {

	//$(".select2").select2();

	$("#vehicles").select2({
		placeholder: "Selecione o(s) Veículos"
	});

	$("#tags").select2({
		placeholder: "Selecione a(s) Tags"
	});
	
	$("#whatsapp_tags").select2({
		placeholder: "Selecione a(s) Tags Whatsapp"
	});
	
	$("#content_type").select2();

	$('#text').val( $('#editor-container').html() );

	$("#tags_btn").click(function(){
		$("#target").submit();				 
	});

	var toolbarOptions = {
        container: [
			['bold', 'italic', 'underline'], ['link'],
			[{ 'list': 'ordered'}, { 'list': 'bullet' }],
			['emoji']
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
			clearButton: ''
		},
		messages: {
			default: dropifyMessage,
			replace: dropifyMessage
		}
	});
	
	$("#content_type").change(function(){
		if($("#content_type").val()==2){
			$("#link").hide();
			$("#up_file").show();
			
		}else{
			$("#up_file").hide();
			$("#link").show();
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
	  
});