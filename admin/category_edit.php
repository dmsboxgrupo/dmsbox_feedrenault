<?php

// API

include('includes/common.php');

// Codigo da pagina
need_login();

//id pagina
$content_id = (int)get('id');
//campo de erro
$error ="";
//Bloqueio de campos
$desabilita ="";
//Obrigada Senha
//$required="required";

function get_content($insert=true) {
	return array(
		'name' => get_post('name')
	);
}

// Atuliza Categoria
if ($content_id > 0) {
	//recupera dados usuario
	$content = $db->content( $db->select_id( 'categories', $content_id ), 'categories' );	
	
	if (has('post')) {
		$campos = get_content("");
		$db->update('categories', $content_id, $campos);
		
		$_SESSION['message'] = 'Registro alterado com sucesso.';	    
		$_SESSION['type'] = 'success';
		redirect('categories.php');

	} elseif (has('toggle_active')) {
		//alterana status
		$db->update('categories', $content_id, array('status' => $content['status'] ? 0 : 1));
		
		$_SESSION['message'] = 'Registro alterado com sucesso.';	    
		$_SESSION['type'] = 'success';
		redirect('categories.php');
	}
} elseif (has('post')) {
	
	// Inclui Categoria
	$content = get_content(false);
	//validando email
	$sqlTeste= "select 'S' from categories where name='".$content['name']."' ";
	//$existe = query($sqlTeste);
	$existe = $db->query($sqlTeste);
	
	//txtLog('_dbRuno_',"sqlTeste $sqlTeste"); 
	
	if($error==""){
		if($existe && $existe[0]['S']=='S'){
			//txtLog('_dbRuno_',"E-mail ".$existe[0]['S']." ja cadastrado.");
			$error = "Nome ".$content['nome']." ja cadastrado.";
		}
		else{
			$campos=get_content();
			$campos['status']=1;
			$retorno = $db->insert('categories', $campos);

			$_SESSION['message'] = 'Registro cadastrado com sucesso.';	    
			$_SESSION['type'] = 'success';
			
			redirect('categories.php');
		}
	}
	
} else {
	$content = get_content(false);
}

// Inicia HTML

$webadm->set_page( array( 'name' => 'Nova Categoria' ) );
$webadm->add_parent( array( 'name' => 'Categorias', 'url' => 'categories.php' ) );

//$webadm->add_button( array( 'name' => 'Importar Planilha', 'icon' => 'mdi mdi-account-multiple', 'url' => 'user_import.php' ) );

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
						<button type="submit" class="btn btn-block btn-info text-uppercase"><i class="fa fa-check m-r-5"></i> Salvar</button>
						<!--<input type="submit" value="Salvar" name="submit" class="btn btn-block btn-info text-uppercase"/>-->
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