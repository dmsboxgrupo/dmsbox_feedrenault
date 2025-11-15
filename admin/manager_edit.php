<?php

// API

include('includes/common.php');
//include('../includes/user.php');

$user_logado_id = $useradm->get_property('id');

if( $user_logado_id == 2054 || $user_logado_id == 1){
	
	include('../includes/user_teste.php');
	
}else{
	
	include('../includes/user.php');
	
}

// Codigo da pagina
need_login();

//id pagina
$content_id = (int)get('id');

$is_new = $content_id == 0;

//campo de erro
$error ="";

//Bloqueio de campos
$desabilita ="";

$user_categories = $db->query("SELECT id, name  
					FROM `user_categories` 
					WHERE `status`=1 ");

$id_gerente = current(array_column( $db->query("select id from user_categories where name like '%gerente%' limit 1"), 'id' ));

function get_content($insert=true) {
	
	return array(
		'name' => get_post('name'),
		'email' => get_post('email'),
		'bir' => get_post('bir'),
		'status_gerente' => get_post('status_gerente'),
		'password' => get_post('password'),
		'user_category' => get_post('user_category'),
		'level' => 2,
		'status_email' => 1,
		'status_gerente' => 1
		
	);
	
}

// Atuliza Usuario
if ($content_id > 0) {
	
	//recupera dados usuario
	$user = new User($content_id);	
	
	$content = (array) $user;
	
	$desabilita = "disabled";	
	
	$content["password"] = "";
	
	if (has('post')) {

		$campos = get_content("");		
		$campos['status_gerente'] = get_post('aprovar');
		//if($content['status']==3)$campos['status']=2;
		if($campos["password"]=="") unset($campos["password"]);

		$user->update($campos);

		redirect('managers.php');

	} elseif (has('toggle_active')) {
		
		$user = new User($content);
		
		$user->toggle_active();
		
		redirect('managers.php');
	}

} elseif (has('post')) {
	
	// Inclui Usuario
	$content = get_content(false);
	
	if($content['email']!=''){
		
		if (!filter_var($content['email'], FILTER_VALIDATE_EMAIL))
			$error = "Formato email inválido";
		
		//validando email
		$existe = $db->query("select 'S' from users where email='".$content['email']."' ");
		
		if($error==""){
			if( $existe && $existe[0]['S'] == 'S' ){
				
				$error = "E-mail ".$content['email']." ja cadastrado.";
				
			}
			else{
				
				$user = new User();
				
				$campos = get_content();

				$campos['level'] = 2;

				$campos['user_category'] = current(array_column( $db->query("select id from user_categories where name like '%gerente%' limit 1"), 'id' ));
				
				$retorno = $user->create_user_panel($campos);

				redirect('managers.php');
			}
		}
	}
} else {
	
	$content = get_content(false);
	$content['btn_txt'] = 'Salvar';
}

// Inicia HTML

$webadm->set_page( array( 'name' => $is_new ? 'Novo Gerente' : 'Editar Gerente' ) );
$webadm->add_parent( array( 'name' => 'Gerentes', 'url' => 'managers.php' ) );

if(!$is_new) $webadm->add_button( array( 'name' => 'Tornar Usuário', 'icon' => 'mdi mdi-account', 'url' => "user_edit.php?id={$content_id}&toggle_level" ) );
$webadm->add_button( array( 'name' => 'Importar Planilha', 'icon' => 'mdi mdi-account-multiple', 'url' => 'user_import.php?nivel=1' ) );

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
								<label>Nome completo</label>
								<input name="name" value="<?php echo html($content['name']); ?>" type="text" class="form-control" placeholder="Insira o nome completo" required>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md">
							<div class="form-group">
								<label>Email</label>
								<input name="email" value="<?php echo html($content['email']); ?>" class="form-control" placeholder="Insira o email">
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md">
							<div class="form-group">
								<label>Senha</label>
								<!--<input name="password" value="<?php echo html($content['password']); ?>" type="password" class="form-control" placeholder="Insira uma senha" required>-->
								<input name="password" value="<?php echo html($content['password']); ?>" type="search" class="form-control" id="Password" placeholder="Insira uma senha" style="text-security: disc;-webkit-text-security: disc;">
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md">
							<div class="form-group">
								<label>BIR</label>
								<input name="bir" value="<?php echo html($content['bir']); ?>" type="number" class="form-control" placeholder="Insira o BIR">
							</div>
						</div>
					</div>
					
					<div class="form-group m-b-10">
						<label>Categoria</label>
						<select id="user_category" class="form-control col-md-12 select2" name="user_category">
							
							<option value="0" $sel >. . .</option>
							<?php foreach($user_categories as $user_category) {
								if($is_new)$sel = (strpos($id_gerente, $user_category['id']) !== false)? "selected":"";
								else $sel = (strpos($content['user_category'], $user_category['id']) !== false)? "selected":"";
							?>
								<option <?php echo $sel ?> value="<?php echo html($user_category['id']); ?>" $sel ><?php echo html($user_category['name']); ?></option>
							<?php } ?>
						</select>
					</div>
					
					<div class="form-actions">
						<div class="row">
							<!--<div class="col-md-12">-->
							<div class="<?php echo html($is_new?"col-md-12":($content['status_gerente'] == ""?"col-md-6":"col-md-12")); ?>">
								<button type="submit" class="btn btn-block btn-<?php echo html($is_new?"info":(($content['status_gerente']=="0" || $content['status_gerente']=="")?"success":"info")); ?> text-uppercase" name="aprovar" value="1">
									<i class="fa fa-check m-r-5"></i> 
									<?php echo html($is_new?"Salvar":(($content['status_gerente']=="0" || $content['status_gerente']=="")?"Permitir Acesso":(($content['status_email']==1)?"Salvar":"Salvar e Reinviar Confirmação"))); ?>
								</button>
							</div>
							<!--<button type="submit" class="btn" name="submitAdd"><span><span>Ask Question!</span></span></button>-->
							<?php if(!$is_new && $content['status_gerente']==""){ ?>
								<div class="col-md-6">
									<button type="submit" class="btn btn-block btn-danger text-uppercase" name="aprovar" value="0">
										<i class="fas fa-times m-r-5"></i> Recusar Acesso
									</button>
								</div>
							<?php } ?>
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