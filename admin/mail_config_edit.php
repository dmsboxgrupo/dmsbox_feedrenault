<?php

// API

include('includes/common.php');

need_login();

$metadata = $db->get_metadata( "blocked_domains" );

if ( empty($metadata) ) {
	
	$db->set_metadata( "blocked_domains" );
	$metadata = $db->get_metadata( "blocked_domains" );
	
}
	
$content_id = $metadata['id'];	

$dominios =  $metadata['value'] ;

if ($content_id > 0) {

	if (has('post')) {
		
		$dominios = get_post('dominios');
		
		$db->update('metadata', $content_id, array('value' => $dominios));

		redirect('managers.php', array(
			'message' => 'Configurações de email alteradas com sucesso.',
			'type' => 'success'
		));

	}

}

// View

$webadm->set_page( array( 'name' => 'Editar Configuração email' ) );
$webadm->add_parent( array( 'name' => 'Gerentes', 'url' => 'managers.php' ) );

$webadm->add_plugins('quilljs', 'dropify', 'select2', 'tagsinput');
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

.bootstrap-tagsinput {
	width: 100%;
    min-height: 38px;
    line-height: 30px;
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
				<div class="col-md-12">
					<label>Domínios bloqueados</label>
					<div class="form-group m-b-10">
						<input data-role="tagsinput" name="dominios" type="text"
						placeholder="Digite os Domínios" value="<?php echo html($metadata['value']); ?>" />
					</div>
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