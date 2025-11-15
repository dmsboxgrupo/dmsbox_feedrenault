
$("#categories").change(function(){
	//apenas para a categoria 2 - Comunicados
	if($("#categories").val()==2){
		
		$("#topics_btn").click(function(){
			 $("#target").submit();				 
		});
		
		$("#topics_btn").show();
		
	}else{
		
		$("#topics_btn").hide();
		
	}
});

$("#categories").trigger("change");


$(function() {






	$(".select2").select2();

	$('#text').val( $('#editor-container').html() );

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

});