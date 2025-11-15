<?php

// API

include('includes/common.php');

need_login();

// Controller
//$content_id = (int)get('id');

//$is_new = $content_id == 0;

//$categories = $db->query("SELECT id, name FROM categories WHERE status = 1");
//$categories = $list_categories;

//$content_id = $db->get_metadata_id( "intro_banner" );
//$metadata_id = $db->set_metadata( "intro_banner", value );

$metadata = $db->get_metadata( "intro_banner" );

if ( empty($metadata) ) {
	
	$db->set_metadata( "intro_banner", value );
	$metadata = $db->get_metadata( "intro_banner" );
	
}
	
$content_id = $metadata['id'];	

$image = $uploader->get( $metadata['value'] );

$metadata['image_url'] = $image ? $image['url'] : '';


if ($content_id > 0) {

	if (has('post')) {	
	
		$file_id = 0;
		
		if (has_upload('image')) {
			
			$file_id = $uploader->upload( get_upload('image') );

		}
	
		$db->update('metadata', $content_id, array('value' => $file_id));
		
		redirect('users.php', array(
			'message' => 'Imagem Abertura alterada com sucesso.',
			'type' => 'success'
		));

	} 
	
}

// View

$webadm->set_page( array( 'name' => 'Editar Imagem Abertura' ) );
$webadm->add_parent( array( 'name' => 'Usuários', 'url' => 'users.php' ) );

$webadm->add_plugins('quilljs', 'dropify', 'select2');
$webadm->start_panel();

?>
<!-- ============================================================== -->
<!-- Start Page Content -->
<!-- ============================================================== -->
<style>

.ql-bubble .ql-tooltip {
  z-index: 1;
}

.dropify-wrapper{
	width: 100%;
	height: calc(100vh - 375px);
}
</style>
<form action="<?php echo "?post"; ?>" method="post" enctype="multipart/form-data">
	<div class="card no-margin">
		<div class="card-header">
			<div class="row">
				<div class="col-md-11">
					
				</div>
				<button id="save" type="submit" class="btn btn-info"><i class="fa fa-check m-r-10"></i>Salvar</button>
			</div>
		</div>
		<div class="card-body">
			<div class="row">
				
				<div class="col-md">
					<div class="form-group m-b-0">
						<input name="image" type="file" id="image" data-max-file-size="100M" 
						class="dropify" data-default-file="<?php echo url( $metadata['image_url'] ) ?>" data-allowed-file-extensions="webp jpg jpeg png gif" />
					</div>							
				</div>
			</div>
		</div>
	</div>
</form>
<script src="js/opening_screen_edit.js"></script>

<!-- ============================================================== -->
<!-- End Page Content -->
<!-- ============================================================== -->
<?php 

// Finaliza HTML

$webadm->end_panel();

?>