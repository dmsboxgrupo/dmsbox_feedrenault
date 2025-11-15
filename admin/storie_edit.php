<?php

// API

include('includes/common.php');

need_login();

// Controller

$content_id = (int)get('id');

$is_new = $content_id == 0;

$categories = $db->query("SELECT id, name FROM categories WHERE status = 1");

function get_content($insert=true) {
		
	global $uploader;
	
	$content = array(		
		'category'=> get_post('categories'),
		'text' => get_post('text'),
		'link' => get_post('link')
	);
	
	if (!$insert) {
		
		$content['id'] = 0;
		$content['image'] = 0;
	}

	if (has_upload('image')) {
		
		$file_id = $uploader->upload( get_upload('image') );

		$content['image'] = $file_id;
	}

	return $content;

}

if ($content_id > 0) {

	$content = $db->content( $content_id , 'stories' );
	
	if( $content['image'] > 0 ) {
		
		$arquivo = $db->content( $content['image'], 'uploads' );
		
	}

	if (has('post')) {	
	
		$db->update('stories', $content_id, get_content());
		
		redirect('stories.php', array(
			'message' => 'Storie alterado com sucesso.',
			'type' => 'success'
		));

	} elseif (has('toggle_active')) {

		$db->update('stories', $content_id, array('status' => $content['status'] ? 0 : 1));
		
		redirect('stories.php', array(
			'message' => 'Status alterado com sucesso.',
			'type' => 'success'
		));
		
	}
	
} elseif (has('post')) {

	$content_id = $db->insert('stories', get_content(true));
	
	redirect('stories.php', array(
		'message' => 'Publicação adicionado com sucesso.',
		'type' => 'success'
	));
	
} else {
	
	$content = $db->content( get_content(false), 'stories' );
	
}

// View

$webadm->set_page( array( 'name' => $is_new ? 'Nova Storie' : 'Editar Storie' ) );
$webadm->add_parent( array( 'name' => 'Stories', 'url' => 'stories.php' ) );

$webadm->add_plugins('quilljs', 'dropify', 'select2');
$webadm->start_panel();

?>
<!-- ============================================================== -->
<!-- Start Page Content -->
<!-- ============================================================== -->
<style>
#editor-container {
	width: 100%;
	height: calc(100vh - 482px);
}

.ql-bubble .ql-tooltip {
  z-index: 1;
}

.dropify-wrapper{
	width: 100%;
	height: calc(100vh - 375px);
}
</style>
<form action="<?php echo "?id={$content_id}&post"; ?>" method="post" enctype="multipart/form-data">
	<div class="card no-margin">
		<div class="card-header">
			<div class="row">
				<div class="col-md-11">
					<div class="row">
						<!--
						<div class="col-md-8">
							<input name="name" type="text" class="form-control" placeholder="Nome da Storie" value="<?php echo html($content['name']); ?>" required />
						</div>
						-->
						<div class="col-md">
							<select id="categories" class="form-control select2" name="categories">
								<?php foreach($categories as $category) { ?>
									<option <?php 
									
										echo selected( $category['id'] == $content['category'] ) 
									
									?> value="<?php 

										echo html($category['id']); 
										
									?>"><?php 
									
										echo html($category['name']); 
									
									?></option>
								<?php } ?>
							</select>
						</div>
					</div>
				</div>
				<button id="save" type="submit" class="btn btn-info"><i class="fa fa-check m-r-10"></i>Salvar</button>
			</div>
		</div>
		<div class="card-body">
			<div class="row">
				<div class="col-md-6">
					<div class="form-group">
							<input data-bv-uri-allowlocal  name="link" type="url" class="form-control" placeholder="Link, Youtube" value="<?php echo html($content['link']); ?>"
						style="max-height:10px;"/>
					</div>						
					<div class="form-group m-b-0">
						<div id="editor-container"><?php echo $content['text']; ?></div>
						<textarea style="display: none" id="text" name="text"></textarea>
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group m-b-0">
						<input name="image" type="file" id="image" data-max-file-size="100M" 
						class="dropify" data-default-file="<?php echo url( $content['image_url'] ) ?>" data-allowed-file-extensions="mp4 jpg jpeg png gif" />
					</div>							
				</div>
			</div>
		</div>
	</div>
</form>
<script>
$(function() {

	$(".select2").select2();

	$('#text').val( $('#editor-container').html() );

	var toolbarOptions = [
		['bold', 'italic', 'underline'], ['link'],
		[{ 'list': 'ordered'}, { 'list': 'bullet' }]
	];
	
	var quill = new Quill('#editor-container', {
		modules: {
			toolbar: toolbarOptions
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
</script>
<!-- ============================================================== -->
<!-- End Page Content -->
<!-- ============================================================== -->
<?php 

// Finaliza HTML

$webadm->end_panel();

?>