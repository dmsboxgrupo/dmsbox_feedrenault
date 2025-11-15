<?php

// API

include('includes/common.php');
include('../includes/user.php');
/*$user_logado_id = $useradm->get_property('id');
//print_r($user_logado_id); die();

if( $user_logado_id == 2054 || $user_logado_id == 1){
	
	include('../includes/user_teste.php');
	
}else{
	
	include('../includes/user.php');
	
}*/
// Codigo da pagina
need_login();

//id pagina
$content_id = (int)get('id');
$accept = (int)get('accept');

$is_new = $content_id == 0;

//campo de erro
$error ="";

//Bloqueio de campos
$desabilita ="";

//bir gerente ou bir aberto
$level_logado = $useradm->get_property('level');
$bir_logado = $level_logado == 2 ? $useradm->get_property('bir') : "";

$user_categories = $db->query("SELECT id, name  
					FROM `user_categories` 
					WHERE `status`=1 ");

$id_vendedor = current(array_column( $db->query("select id from user_categories where name like '%vendedor%' limit 1"), 'id' ));

function get_content($insert=true) {
	global $bir_logado, $is_new;
	
	$content = array(
		'name' => get_post('name'),
		'email' => get_post('email'),
		'bir' => get_post('bir'),
		'status_gerente' => get_post('status_gerente'),
		'password' => get_post('password'),
		'user_category' => get_post('user_category'),
		'level' => 3
	);
	
	if (!$insert) {
		$content['bir'] = $is_new ? $bir_logado : $content['bir'];
	}
	
	if($is_new){
		
		$content['origin'] = 1;
		
	}
	
	return $content;
	
}

// Atuliza Usuario
if ($content_id > 0) {
	
	//recupera dados usuario
	$user = new User($content_id);	
	
	$content = (array) $user;
	
	if (!empty($bir_logado)) {$desabilita = "readonly";	}
	
	$content["password"] = "";
	
	if (has('post')) {

		$campos = get_content("");

		$campos['status_gerente'] = get_post('aprovar');
		
		$status_email = current( $db->query("SELECT status_email FROM users WHERE id = :id;", array( 'id' => $content_id )) )['status_email'];
		
		if(!$status_email) unset($campos["status_gerente"]);
		
		$campos["origin"] = $content["origin"];
	//print_r($content);

		//if($content['status']==3)$campos['status']=2;
		if($campos["password"]=="") unset($campos["password"]);

		
		
		$error = $user->update($campos);
//die();
		if(empty($error)) redirect('users.php');

	} elseif (has('toggle_active')) {
		
		$user = new User($content);
		
		$user->toggle_active();
		
		redirect('users.php');
		
	} elseif (has('toggle_level')) {
		
		$user = new User($content);
		
		$user->toggle_level();
		
		//echo "teste= {$user->level}"; die();
		
		if( $user->level==2 ) {
		
			redirect("user_edit.php?id={$content_id}");
		
		} else {
			
			redirect("manager_edit.php?id={$content_id}");
			
		}
		
	} elseif (has('accept')) {
		//print_r($content); die();
		if($user->status_gerente != 1){
			
			//$content['status_gerente'] = 1;
			$content['status_gerente'] = $accept;
			if($content["password"]=="") unset($content["password"]);
			unset($content["date"]);

			$user->update($content);
			
		}
		
		redirect('users.php');
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
				
				$dados = get_content();

				$error = $user->valida($dados, 'painel');

				if( empty($error) ) {
				
					$retorno = $user->create_user_panel($dados);

					redirect('users.php');
					
				}
			}
		}
	}
} else {
	
	$content = get_content(false);
	$content['btn_txt'] = 'Salvar';
}

// Inicia HTML

$webadm->set_page( array( 'name' => $is_new ? 'Novo Usuário' : 'Editar Usuário' ) );
$webadm->add_parent( array( 'name' => 'Usuários', 'url' => 'users.php' ) );

/*
if(!$is_new){

	$webadm->add_button( array( 'name' => 'Enviar Notificação', 'icon' => 'mdi mdi-message-alert', 'url' => "notification.php?user_id=$content_id" ) );

}
*/

if ($useradm->is_level(LEVEL_MASTER)){
	//id=" + contentId + "&toggle_active";
	if(!$is_new && empty($bir_logado)) $webadm->add_button( array( 'name' => 'Tornar Gerente', 'icon' => 'mdi mdi-account', 'url' => "user_edit.php?id={$content_id}&toggle_level" ) );
	
	$webadm->add_button( array( 'name' => 'Importar Planilha', 'icon' => 'mdi mdi-account-multiple', 'url' => 'user_import.php' ) );
	
}

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
								<input name="email" value="<?php echo html($content['email']); ?>" class="form-control" placeholder="Insira o email" <?php echo $desabilita; ?> required>
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
								<input name="bir" value="<?php echo html($content['bir']); ?>" type="number" class="form-control"
									placeholder="Insira o BIR" maxlength="7"
									<?php if ( $bir_logado>0 ) echo "readonly"; ?>
									
								>
							</div>
						</div>
					</div>
					
					<div class="form-group m-b-10">
						<label>Categoria</label>
						<select id="user_category" class="form-control col-md-12 select2" name="user_category" >
							
							<option value="0" $sel >. . .</option>
							<?php foreach($user_categories as $user_category) {
								if($is_new)$sel = (strpos($id_vendedor, $user_category['id']) !== false)? "selected":"";
								else $sel = (strpos($content['user_category'], $user_category['id']) !== false)? "selected":"";
							?>
								<option <?php echo $sel ?> value="<?php echo html($user_category['id']); ?>" $sel ><?php echo html($user_category['name']); ?></option>
							<?php } ?>
						</select>
					</div>
					

					<div class="form-actions">
						<div class="row">
							<!--<div class="col-md-12">-->
							<div class="<?php echo html(($is_new || !$content['status_email'])?"col-md-12":($content['status_gerente'] == ""?"col-md-6":"col-md-12")); ?>">
								<button type="submit" class="btn btn-block btn-<?php echo html(($is_new || !$content['status_email'])?"info":(($content['status_gerente']=="0" || $content['status_gerente']=="")?"success":"info")); ?> text-uppercase" 
									name="aprovar" value="1">
									<i class="fa fa-check m-r-5"></i> 
									
									<?php 
									//echo html($is_new?"Salvar":(($content['status_gerente']=="0" || $content['status_gerente']=="")?"Permitir Acesso":(($content['status_email']==1)?"Salvar":"Salvar e Reinviar Confirmação"))); ?>
									
									<?php echo html(($is_new || ($content['status_email']=="1" && $content['status_gerente']=="1"))?"Salvar":
										(
											(!$content['status_email'])?"Salvar e Reinviar Confirmação":
											(($content['status_gerente']=="0" || $content['status_gerente']=="")?"Permitir Acesso":"Salvar")
										)
										); ?>
									
								</button>
							</div>
							<!--<button type="submit" class="btn" name="submitAdd"><span><span>Ask Question!</span></span></button>-->
							<?php if(!$is_new && $content['status_gerente']=="" && $content['status_email']){ ?>
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
	
	$(".select2").select2();

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