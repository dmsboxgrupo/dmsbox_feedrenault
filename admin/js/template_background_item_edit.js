$(function() {


	$("#tags").select2({
		placeholder: "Tags"
	});
	
	$("#template_texts").select2({
		placeholder: "Selecione o(s) Texto(s)"
	}).on('change', function (e) {
		//var str = $("#s2id_search_code .select2-choice span").text();
		//alert('teste');
		//DOSelectAjaxProd(this.value, str);
		//this.value
		//$('#template_texts').sortable();
		
		var arr = $("#template_texts").val().join();
		
		//alert(arr);
		$("#template_texts_values").val(arr);
		//$("#template_texts_values").val(arr.map(x =>x.value).join());
		
	});
	
	$("ul.select2-selection__rendered").sortable({
		
	  containment: 'parent',
	  stop: function(event, ui) {

		var arr = Array.from($(event.target).find('li:not(.select2-search)').map(function () { 
			return {name: $(this).data('data').text, value: $(this).data('data').id }; 
		}))
		
		//alert($("#teste").value);
		
		$("#template_texts_values").val(arr.map(x =>x.value).join());
		
	  }
	 
	});

	
	/*
	$("#template_texts").select2({
		placeholder: "Textos"
	});*/
	
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
	
	$('#img .dropify').on('dropify.afterClear', function(event, element) {

			$('#guarda_arq').val('0');
    });
	
	$('#thumb .dropify').on('dropify.afterClear', function(event, element) {

			$('#guarda_arq_thumb').val('0');
    });

});