<?php

// API

include('includes/common.php');
include('../includes/user.php');

// Codigo da pagina
need_login();

//id pagina
$content_id = (int)get('id');

$is_new = $content_id == 0;

//campo de erro
$error ="";

//Bloqueio de campos
$desabilita ="";

function get_content($insert=true) {
	
	return array(
		'name' => get_post('name')
	);
	
}

// Atuliza Usuario
if ($content_id > 0) {
	
	//recupera dados usuario
	$content = $db->content( $db->select_id( 'user_categories', $content_id ), 'user_categories' );	

	if (has('post')) {

		$campos = get_content("");
		$db->update('user_categories', $content_id, $campos);

		redirect('user_categories.php');

	} elseif (has('toggle_active')) {
		
		$db->update('user_categories', $content_id, array('status' => $content['status'] ? 0 : 1));
		
		redirect('user_categories.php');
	}

} elseif (has('post')) {
	
	// Inclui Categoria
	$campos=get_content();
	
	$retorno = $db->insert('user_categories', $campos);

	$_SESSION['message'] = 'Registro cadastrado com sucesso.';	    
	$_SESSION['type'] = 'success';
	
	redirect('user_categories.php');

	
} else {
	
	$content = get_content(false);
	
}

// Inicia HTML

$webadm->set_page( array( 'name' => $is_new ? 'Nova Categoria' : 'Editar Categoria' ) );

$webadm->add_parent( array( 'name' => 'Gerentes', 'url' => 'managers.php' ) );
$webadm->add_parent( array( 'name' => 'Categorias Usuários', 'url' => 'user_categories.php' ) );

$webadm->add_plugins('select2');
$webadm->start_panel();
?>

<!-- ============================================================== -->
<!-- Start Page Content -->
<!-- ============================================================== -->
<div class="row">
	<div class="col-12">
		<div class="card no-margin">
			<div class="card-body wizard-content">
				<form action="?id=<?php echo $content_id; ?>&post" method="post" enctype="multipart/form-data" class="tab-wizard wizard-circle" autocomplete="off">					
					<?php if ( $error ) { ?>
						<div class="alert alert-danger">
					<?php echo html( $error ); ?>
						<button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">x</span></button>
						</div>
					<?php } ?>
					
					<div class="row">
						<div class="col-md">
							<div class="form-group">
								<label>Nome</label>
								<input name="name" value="<?php echo html($content['name']); ?>" type="text" class="form-control" placeholder="Insira o nome" required>
							</div>
						</div>
					</div>
					
					<div class="form-actions">
						<div class="row">
							
								<div class="col-md-12">
									<button type="submit" class="btn btn-block btn-info text-uppercase" name="aprovar" value="0">
										<i class="fa fa-check m-r-5"></i>  Salvar
									</button>
								</div>
							
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
<script>
$(function() {
	
	//$(".select2").select2();

	//$('#cpf').mask('000.000.000-00', {reverse: true});

});
</script>
<!-- ============================================================== -->
<!-- End Page Content -->
<!-- ============================================================== -->
<?php 

// Finaliza HTML

$webadm->end_panel();

?>