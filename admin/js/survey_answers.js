$(function() {
	
	
	
	/*$("#tags_btn").click(function(){
		
		$("#target").submit();
	});*/
	/*
	var contents = $('#contents').DataTable({		
		language: { url: "<?php echo url( 'admin/plugins/datatables-language/Portuguese-Brasil.json' ) ?>" },

		rowReorder: true,
		paging: false,
		searching: false,
		scrollY: 'calc(100vh - (540px - 52px))',
		columnDefs: [
			{ targets: 0, visible: false },
			{ orderable: true, className: 'reorder', targets: 1 },
			{ orderable: false, targets: '_all' },
			{ width: "1px", targets: [0,1,3] }
		],
		order: [[ 0, "asc" ]]
	});
	
	contents.on( 'row-reorder', function ( e, diff, edit ) {

	var seq = [];

	$('#contents tbody tr').each(function() {
		
		seq.push( parseInt( $(this).attr('gallery-image-id') ) );
		
	});

	$.post( "gallery_images.php?id=<?php echo $survey_id ?>&update", { gallery_images: seq.join(',') })
		.done(function( data ) {

			console.log( data );

		});

	} );
	
	$('.toggle-gallery_image').click(function(){
		
		var gallery_imageId = $(this).closest('tr').attr( "gallery-image-id" );
		var survey_id = $(this).closest('tr').attr( "gallery-id" );
		
		if($(this).hasClass( "btn-danger" )){
			swal({   
				title: "Deseja mesmo remover essa Imagem?",
				type: "warning",   
				showCancelButton: true,   
				confirmButtonColor: "#DD6B55",   
				confirmButtonText: "Sim, remover!",   
				closeOnConfirm: false,
				cancelButtonText: "Cancelar"
			}, function(){
				
				window.location = "<?php echo url('gallery_image_edit.php'); ?>?id=" + gallery_imageId + "&survey_id=" + survey_id +
					"&toggle_active";
				
			});
		} else
			window.location = "<?php echo url('gallery_image_edit.php'); ?>?id=" + gallery_imageId + "&survey_id=" + survey_id +
			"&toggle_active";
	});
	
	$(document).ready(function() {
	  $("#success-alert").fadeTo(5000, 500).slideUp(500, function() {
		  $("#success-alert").slideUp(500);
		});	  
	});
	
	
	/******************************************************/
	
	$('#text').val( $('#survey_text').html() );
	
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
	
	var quill = new Quill('#survey_text', {
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
	
	
	
});