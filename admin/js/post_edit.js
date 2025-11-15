
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

	$('#text_highlight').val( $('#editor-container2').html() );
/*
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
*/	
	if ($('#editor-container2').is(':visible')) {
		
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
	}

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

			$('#guarda_arq').val('0');
    });
	
	$('#image_highlight').on('dropify.afterClear', function(event, element) {

			$('#guarda_arq2').val('0');
    });
	
	$('#tell_client').on('dropify.afterClear', function(event, element) {

			$('#guarda_arq3').val('0');
    });
	

});