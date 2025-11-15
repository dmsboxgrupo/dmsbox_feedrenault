$(function() {

	//$(".select2").select2();
	
	$("#vehicles").select2({
		placeholder: "Selecione o(s) veiculo(s)"
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
/*
	$('.fileinput-filename').on('DOMSubtreeModified',function(){
		arq_up = $('#arq_up').text();
		
		if (arq_up =="")
			$('#guarda_arq').val(arq_up);
		else
			$('#guarda_arq').val(arq_up);
		
		//alert($('#guarda_arq').val());
	})
*/
});