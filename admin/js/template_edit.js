$(function() {

	$("#vehicles").select2({
		placeholder: "Selecione o(s) veiculo(s)"
	});

	$("#tags").select2({
		placeholder: "Tags"
	});
	/*
	$("#Cor do Texto").select2({
		placeholder: "Cores do Texto"
	});
	*/
/*	
	$("#text_style").select2({
		placeholder: "Estilos Texto"
	});
*/	/*
	$("#color_me").change(function(){
		var color = $("option:selected", this).attr("class");
		$("#color_me").attr("background", "red");
	});*/
	
	$("#field1_color").change(function(){
		var color = $("option:selected", this).attr("style");
		$("#field1_color").attr("style", color);
		
		var aux = color.replace('background','color');		
		$(".ql-editor").attr("style", aux);
	});
	
	$("#field2_color").change(function(){
		var color = $("option:selected", this).attr("style");
		$("#field2_color").attr("style", color);
		
		var aux = color.replace('background','color');		
		$(".ql-editor").attr("style", aux);
	});
	
	$("#field3_color").change(function(){
		var color = $("option:selected", this).attr("style");
		$("#field3_color").attr("style", color);
		
		var aux = color.replace('background','color');		
		$(".ql-editor").attr("style", aux);
	});
	
	$("#field4_color").change(function(){
		var color = $("option:selected", this).attr("style");
		$("#field4_color").attr("style", color);
		
		var aux = color.replace('background','color');		
		$(".ql-editor").attr("style", aux);
	});
	
	$("#template_texts").select2({
		placeholder: "Selecione o(s) Texto(s)"
	}).on('change', function (e) {
		
		var arr = $("#template_texts").val().join();
		$("#template_texts_values").val(arr);
		
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
	
	/************ Texto 1 **********************************************/
	/*
	$('#field1').val( $('#editor-container').html() );
	$('#field2').val( $('#editor-container2').html() );
	$('#field3').val( $('#editor-container3').html() );

	var toolbarOptions = {
        container: [
			//['bold', 'italic', 'underline'], ['link'],
			['italic', 'underline'], //['link'],
			//[{ 'size': ['small', false, 'large', 'huge'] }],
			[{ 'list': 'ordered'}, { 'list': 'bullet' }],
			['emoji'],
			//[{ 'color': ['#ffd500','white','#222','#777','#ccc']}],    
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
	
	var quill2 = new Quill('#editor-container2', {
		modules: {
			toolbar: toolbarOptions,
			"emoji-toolbar": true,
          "emoji-shortname": true,
		},
		placeholder: 'Escreva aqui...',
		theme: 'snow'
	});
	
	var quill3 = new Quill('#editor-container3', {
		modules: {
			toolbar: toolbarOptions,
			"emoji-toolbar": true,
          "emoji-shortname": true,
		},
		placeholder: 'Escreva aqui...',
		theme: 'snow'
	});
	
	quill.on('text-change', function() {
		$('#field1').val( quill.container.firstChild.innerHTML );
	});
	
	quill2.on('text-change', function() {
		$('#field2').val( quill2.container.firstChild.innerHTML );
	});
	
	quill3.on('text-change', function() {
		$('#field3').val( quill3.container.firstChild.innerHTML );
	});*/
	
	/**********************************************************************/
	// Add a custom DropDown Menu to the Quill Editor's toolbar:

	/*
	const dropDownItems = {
		'Mike Smith': 'mike.smith@gmail.com',
		'Jonathan Dyke': 'jonathan.dyke@yahoo.com',
		'Max Anderson': 'max.anderson@gmail.com'
	}
	*/

	const dropDownItems = {
		'Estilo 1': '1',
		'Estilo 2': '2',
		'Estilo 3': '3',
		'Estilo 4': '4',
		'Estilo 5': '5',
		'Estilo 6': '6',
		'Estilo 7': '7'
	}

	const myDropDown = new QuillToolbarDropDown({
		label: "Estilos",
		rememberSelection: false
	})

	myDropDown.setItems(dropDownItems)

	myDropDown.onSelect = function(label, value, quill) {
		// Do whatever you want with the new dropdown selection here

		// For example, insert the value of the dropdown selection:
		/*const { index, length } = quill.selection.savedRange
		quill.deleteText(index, length)
		quill.insertText(index, value)
		quill.setSelection(index + value.length)*/
		// For example, get the selected text and convert it to uppercase:
		
		/*
		const { index, length } = quill.selection.savedRange
		const selectedText = quill.getText(index, length)
		const newText = selectedText.toUpperCase()
		quill.deleteText(index, length)
		quill.insertText(index, newText)
		quill.setSelection(index, newText.length)
		*/
		
		const { index, length } = quill.selection.savedRange
		const selectedText = quill.getText(0, quill.getLength())
		
		//const newText = selectedText.toUpperCase()
		
		quill.deleteText(0, quill.getLength())
		//quill.insertText(index, newText)
		
		// font-size: 230%; line-height: 120%; font-weight: bold;
		quill.insertText(0, selectedText, {
		  //'color': 'red',
		  'bold' : 'true',
		  'fontSize' : 1
		  
		});
		quill.format('size', '300px');
		
		quill.setSelection(index, newText.length)
		
		
	}

	//myDropDown.attach(quill)


	// Add a custom Button to the Quill Editor's toolbar:
/*
	const myButton = new QuillToolbarButton({
		icon: `<svg viewBox="0 0 18 18"> <path class="ql-stroke" d="M5,3V9a4.012,4.012,0,0,0,4,4H9a4.012,4.012,0,0,0,4-4V3"></path></svg>`
	})
	myButton.onClick = function(quill) {
		// Do whatever you want here. You could use this.getValue() or this.setValue() if you wanted.

		// For example, get the selected text and convert it to uppercase:
		const { index, length } = quill.selection.savedRange
		const selectedText = quill.getText(index, length)
		const newText = selectedText.toUpperCase()
		quill.deleteText(index, length)
		quill.insertText(index, newText)
		quill.setSelection(index, newText.length)
	}
	myButton.attach(quill)
	*/

});