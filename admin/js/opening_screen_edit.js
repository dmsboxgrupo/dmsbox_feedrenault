$(function() {

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

});