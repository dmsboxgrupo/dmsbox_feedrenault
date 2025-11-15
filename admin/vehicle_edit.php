<?php

// API

include('includes/common.php');

need_login();

// Controller

$content_id = (int)get('id');

$is_new = $content_id == 0;

$metadata_id = $db->get_metadata_id( 'vehicles' );

if ($metadata_id > 0) {
	
	$root_content = $db->content( $metadata_id, 'metadata' );

} else {

	redirect("vehicles.php");
	
}

function get_content($insert=true) {
		
	global $uploader, $metadata_id;
	
	$content = array(
		'metadata' => $metadata_id,
		'name'=> get_post('name'),
		'version'=> get_post('version')
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

function update_vehicle($new_topic=null) {
	
	global $db, $root_content;
	
	$vehicles = array();
	
	$arr =explode(",", $root_content['value']);
	foreach($arr as $vehicle) {
		
		$content = $db->content( $vehicle, 'vehicles' );
		
		if ( $content['status'] ) {
		
			array_push( $vehicles, $content['id'] );
			
		}
		
	}
	if ($new_topic) {
		
		array_push( $vehicles, $new_topic );
		
	}
	
	$db->update('metadata', $root_content['id'], array(
		'value' => implode(",", $vehicles)
	));
	
	$_SESSION['message'] = 'Registro alterado com sucesso.';
	$_SESSION['type'] = 'success';
	
}

if ($content_id > 0) {

	$content = $db->content( $content_id , 'vehicles' );
	
	if( $content['image'] > 0 ) {
		
		$arquivo = $db->content( $content['image'], 'uploads' );
		
	}

	if (has('post')) {
	
		$db->update('vehicles', $content_id, get_content());
		
		redirect('vehicles.php', array(
			'message' => 'Veículo alterado com sucesso.',
			'type' => 'success'
		));

	} elseif (has('toggle_active')) {

		$db->update('vehicles', $content_id, array('status' => $content['status'] ? 0 : 1));
		
		redirect('vehicles.php', array(
			'message' => 'Status alterado com sucesso.',
			'type' => 'success'
		));
		
	}
	
} elseif (has('post')) {

	$content_id = $db->insert('vehicles', get_content(true));
	
	update_vehicle( $content_id );
	
	redirect('vehicles.php', array(
		'message' => 'Publicação adicionado com sucesso.',
		'type' => 'success'
	));
	
} else {
	
	$content = $db->content( get_content(false), 'vehicles' );
	
}

// View

$webadm->set_page( array( 'name' => $is_new ? 'Novo Veículo' : 'Editar Veículo' ) );
$webadm->add_parent( array( 'name' => 'Veículos', 'url' => 'vehicles.php' ) );

$webadm->add_plugins('quilljs', 'dropify');
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
						<div class="col-md-6">
							<input name="name" type="text" class="form-control" placeholder="Nome do Veículo" value="<?php echo html($content['name']); ?>" required />
						</div>
						<div class="col-md-6">
							<input name="version" type="text" class="form-control" placeholder="Versão do Veículo" value="<?php echo html($content['version']); ?>" />
						</div>
						
					</div>
				</div>
				<button id="save" type="submit" class="btn btn-info"><i class="fa fa-check m-r-10"></i>Salvar</button>
			</div>
		</div>
		<div class="card-body">
			<div class="row">
				<div class="col-md-12">
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