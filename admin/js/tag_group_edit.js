$(function() {
	
	$("#tag_subgroups").select2({
		placeholder: "Subgrupos"
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
	
	$('#thumbnail').on('dropify.afterClear', function(event, element) {

			$('#guarda_arq2').val('0');
    });
	
	/*$("ul.select2-selection__rendered").sortable({
		
	  containment: 'parent',
	  stop: function(event, ui) {

		var arr = Array.from($(event.target).find('li:not(.select2-search)').map(function () { 
			return {name: $(this).data('data').text, value: $(this).data('data').id }; 
		}))
		
		//alert($("#teste").value);
		
		$("#template_texts_values").val(arr.map(x =>x.value).join());
		console.log($("#template_texts_values").val());
	  }
	 
	});*/
	/*
	$('#template_texts_values').on('change', function() {
		console.log($(this).val());
	});
	*/
	
	function atualizarTemplateTextsValues() {
        var arr = Array.from($("ul.select2-selection__rendered li:not(.select2-search)").map(function () {
            return { name: $(this).data('data').text, value: $(this).data('data').id };
        }));

        $("#template_texts_values").val(arr.map(x => x.value).join());
        console.log($("#template_texts_values").val());
    }

    // Ativar a funcionalidade de sortable
    $("ul.select2-selection__rendered").sortable({
        containment: 'parent',
        stop: function (event, ui) {
            atualizarTemplateTextsValues();
        }
    });

    // Atualizar o campo quando um item for adicionado/removido
    $("#tag_subgroups").on("change", function () {
        setTimeout(atualizarTemplateTextsValues, 100); // Delay curto para garantir que o select2 tenha atualizado
    });

    // Atualizar na inicialização para manter os valores corretos ao carregar a página
    atualizarTemplateTextsValues();
	
	console.log($("#template_texts_values").val());
});