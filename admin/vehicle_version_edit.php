<?php

// API

include('includes/common.php');

need_login();

// Controller
$id = (int)get('id');

$content_id = (int)get('content_id');

//$redirect_par = ($player_id > 0)? "player_id={$player_id}" : "";

/*
if ( empty($player_id) ) {
	
	redirect("players.php");
	
}*/

//$manager_user_logado = $useradm->get_property('id');
//$manager = ($useradm->is_master(LEVEL_MASTER))? "" : "and (user=$manager_user_logado)";

$metadata_id = $db->get_metadata_id( 'vehicle_version' );

if ($metadata_id > 0) {
	
	$root_content = $db->content( $metadata_id, 'metadata' );

} else {

	redirect("vehicle_version.php&content_id=$content_id");
	
}


$vehicles = $db->query("SELECT id, name FROM vehicles WHERE status = 1");

$versions = $db->query("SELECT id, name FROM versions WHERE status = 1 ");

$is_new = $id == 0;

function get_content($insert=true) {
		
	global $is_new, $manager_user_logado, $uploader;
	
	$content = array(		
		'vehicle'=> get_post('vehicles'),
		'version'=> get_post('versions'),
		'link'=> get_post('link')
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
	
	$vehicle_version = array();
	
	$arr =explode(",", $root_content['value']);
	foreach($arr as $item) {
		
		$content = $db->content( $item, 'vehicle_version' );
		
		if ( $content['status'] ) {
		
			array_push( $vehicle_version, $content['id'] );
			
		}
		
	}
	if ($new_topic) {
		
		array_push( $vehicle_version, $new_topic );
		
	}
	
	$db->update('metadata', $root_content['id'], array(
		'value' => implode(",", $vehicle_version)
	));
	
	$_SESSION['message'] = 'Registro alterado com sucesso.';
	$_SESSION['type'] = 'success';
	
}


if ($id > 0) {

	$content = $db->content( $id , 'vehicle_version' );
	
	if( $content['image'] > 0 ) {
		
		$arquivo = $db->content( $content['image'], 'uploads' );
		
	}

	if (has('post')) {	
		
		$db->update('vehicle_version', $id, get_content());
		//update_car();
		redirect("vehicle_version.php?content_id={$content_id}", array(
				'message' => 'Tipo Apresentação alterado com sucesso.',
				'type' => 'success'
			));
		
	} elseif (has('toggle_active')) {

		$db->update('vehicle_version', $id, array('status' => $content['status'] ? 0 : 1));
		
		redirect("vehicle_version.php?content_id={$content_id}", array(
			'message' => 'Status alterado com sucesso.',
			'type' => 'success'
		));
		
	}
	
} elseif (has('post')) {

	
	$conteudo = get_content(true);

	$id = $db->insert('vehicle_version', $conteudo);
	
	update_vehicle( $id );
		
	redirect("vehicle_version.php?content_id={$content_id}", array(
		'message' => 'Versão adicionada com sucesso.',
		'type' => 'success'
	));
	
} else {

	$content = $db->content( get_content(false), 'vehicle_version' );

}

// View
//print_r($content); die();

if (!$is_new && $useradm->is_level(LEVEL_MASTER)) $webadm->add_button( array( 'name' => 'Gerenciar Versões', 'icon' => 'mdi mdi-buffer m-r-5', 'url' => "versions.php?content_id=$content_id" ) );


$webadm->set_page( array( 'name' => $is_new ? 'Nova Veículo Versão' : 'Editar Veículo Versão' ) );

$webadm->add_parent( array( 'name' => 'Galerias WhatsApp', 'url' => 'whatsapp_galeries.php' ) );

$webadm->add_parent( array( 'name' => 'Galeria WhasApp', 'url' => "whatsapp_gallery_edit.php?id={$content_id}" ) );

$webadm->add_parent(array( 'name' => 'Veículo Versão', 'url' => "vehicle_version.php?content_id={$content_id}"));

$webadm->add_plugins('quilljs', 'dropify', 'select2', 'tagsinput', 'switcher');

$webadm->start_panel();

?>
<!-- ============================================================== -->
<!-- Start Page Content -->
<!-- ============================================================== -->
<style>
.ql-bubble .ql-tooltip {
  z-index: 1;
}

.ql-snow.ql-toolbar button, .ql-snow .ql-toolbar button{
	width: 30px;
	
}

.col-md-5 col-8 align-self-center {
	flex: unset !important;
	max-width: unset !important;	
}

.dropify-wrapper{
	width: 100%;
	height: 420px;
}

</style>

<form id="target" action="<?php echo "?id={$id}&content_id={$content_id}&post"; ?>" method="post" enctype="multipart/form-data">
	<div class="card no-margin">
		<div class="card-header">
			<div class="row">
				<div class="col-md-11">
					<div class="row">

						<div class="col-md-12">
							<input name="name" type="text" class="form-control" placeholder="Veículo Versão" readonly />
						</div>
						
					</div>
				</div>

				<button name="save" id="save" type="submit" class="btn btn-info" value=1><i class="fa fa-check m-r-10"></i>Salvar</button>

			</div>
		</div>
		<div class="card-body">
			<div class="row">
				
				
				<!--
					<div class="form-group m-b-10">
						<label>Nome</label>
						<input name="name" type="text" class="form-control" placeholder="Digite o Nome" value="?php echo html($content['name']); ?>" required />
					</div>-->
					
					
					<div class="col-md-6">
						<div class="form-group m-b-10">
							<label>Thumbnail</label>
							<input name="image" type="file" id="image" data-max-file-size="80M" 
							class="dropify" data-default-file="<?php echo url( $content['image_url'] ) ?>" data-allowed-file-extensions="mp4 jpg jpeg png gif webp" />
						</div>
						
					</div>
					
					
					<div class="col-md-6">
						
						<div class="form-group m-b-10">
							<label>Veículos</label>
							<select id="vehicle" class="form-control col-md-12 select2" name="vehicles" >
								
								<option value="0" >...</option>
								
								<?php foreach($vehicles as $vehicle) {
									$sel = ( $content['vehicle'] == $vehicle['id'] )? "selected":"";
									//$sel = (strpos($content['vehicles'], $vehicle['id']) !== false)? "selected":"";
								?>
									<option <?php echo $sel ?> value="<?php echo html($vehicle['id']); ?>" $sel ><?php echo html($vehicle['name']); ?></option>
								<?php } ?>
								
							</select>
						</div>
						
						<div class="form-group m-b-10">
							<label>Versão</label>
							<select id="versions" class="form-control col-md-12 select2" name="versions" >
								
								<option value="0" >...</option>
								
								<?php foreach($versions as $version) {
									$sel = (strpos($content['version'], $version['id']) !== false)? "selected":"";
								?>
									<option <?php echo $sel ?> value="<?php echo html($version['id']); ?>" $sel ><?php echo html($version['name']); ?></option>
								<?php } ?>
							</select>
						</div>
						
						<div class="form-group m-b-10">
							<label>Link</label>
							<input data-bv-uri-allowlocal name="link" type="url" class="form-control" placeholder="Insira um link" 
								value="<?php echo html($content['link']); ?>"
							style="max-height:10px;"/>
						</div>
						
					</div>
				
				
			</div>
		</div>
	</div>
</form>

<script src="js/vehicle_version.js"></script>

<!-- ============================================================== -->
<!-- End Page Content -->
<!-- ============================================================== -->
<?php 

// Finaliza HTML

$webadm->end_panel();

?>