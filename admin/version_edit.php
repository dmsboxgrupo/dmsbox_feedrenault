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

$is_new = $id == 0;

function get_content($insert=true) {
		
	global $is_new, $manager_user_logado;
	
	$content = array(		
		'name'=> get_post('name')
	);
	
	if (!$insert) {
		
		$content['id'] = 0;	
		
	}
	
	return $content;

}

if ($id > 0) {

	$content = $db->content( $id , 'versions' );

	if (has('post')) {	
		
		$db->update('versions', $id, get_content());
		//update_car();
		redirect("versions.php?content_id={$content_id}", array(
				'message' => 'Tipo Apresentação alterado com sucesso.',
				'type' => 'success'
			));
		
	} elseif (has('toggle_active')) {

		$db->update('versions', $id, array('status' => $content['status'] ? 0 : 1));
		
		redirect("versions.php?content_id={$content_id}", array(
			'message' => 'Status alterado com sucesso.',
			'type' => 'success'
		));
		
	}
	
} elseif (has('post')) {

	
	$conteudo = get_content(true);

	$id = $db->insert('versions', $conteudo);
		
	redirect("versions.php?content_id={$content_id}", array(
		'message' => 'Versão adicionada com sucesso.',
		'type' => 'success'
	));
	
} else {

	$content = $db->content( get_content(false), 'versions' );

}

// View
//print_r($content); die();
$webadm->set_page( array( 'name' => $is_new ? 'Nova Versão' : 'Editar Versão' ) );

$webadm->add_parent( array( 'name' => 'Galerias WhatsApp', 'url' => 'whatsapp_galeries.php' ) );

$webadm->add_parent( array( 'name' => 'Galeria WhasApp', 'url' => "whatsapp_gallery_edit.php?id={$content_id}" ) );

$webadm->add_parent(array( 'name' => 'Versões', 'url' => "versions.php?content_id={$content_id}"));

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

</style>

<form id="target" action="<?php echo "?id={$id}&content_id={$content_id}&post"; ?>" method="post" enctype="multipart/form-data">
	<div class="card no-margin">
		<div class="card-header">
			<div class="row">
				<div class="col-md-11">
					<div class="row">

						<div class="col-md-12">
							<input name="name" type="text" class="form-control" placeholder="Tipo Apresentação" readonly />
						</div>
						
					</div>
				</div>

				<button name="save" id="save" type="submit" class="btn btn-info" value=1><i class="fa fa-check m-r-10"></i>Salvar</button>

			</div>
		</div>
		<div class="card-body">
			<div class="row">
				
				<div class="col-md-12">
				
					<div class="form-group m-b-10">
						<label>Nome</label>
						<input name="name" type="text" class="form-control" placeholder="Digite o Nome" value="<?php echo html($content['name']); ?>" required />
					</div>
					<!--
					<div class="form-group m-b-10">
						<label>Link</label>
						<input data-bv-uri-allowlocal name="link" type="url" class="form-control" placeholder="Insira um link" 
							value="<php echo html($content['link']); ?>"
						style="max-height:10px;"/>
					</div>
					-->
				
				</div>
			</div>
		</div>
	</div>
</form>

<!-- ============================================================== -->
<!-- End Page Content -->
<!-- ============================================================== -->
<?php 

// Finaliza HTML

$webadm->end_panel();

?>