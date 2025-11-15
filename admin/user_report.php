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


$user_categories = $db->query("SELECT id, name  
					FROM `user_categories` 
					WHERE `status`=1 ");

function get_content($insert=true) {
	
	return array(
		'bir' => get_post('bir'),
		'status' => get_post('status'),
		'origin' => get_post('origin'),
		'level' => get_post('level'),
		'start_date' => get_post('start_date'),
		'end_date' => get_post('end_date'),
		'days_no_login' => get_post('days_no_login'),
		'user_category' => get_post('user_category'),
		
		
	);
	
}

// Atuliza Usuario
if (has('post')) {
	
	// Inclui Usuario
	$content = get_content(false);
	
	//print_r($content); echo "<br/>";
	
	$hoje = date('Y-m-d');
	$where = "";
	
	//BIR
	if($content['bir'] > 0 ) $where = " and bir='{$content['bir']}'";
	
	//Status
	if($content['status'] > 0 ) {

		if($content['status'] == 1 ){
			$where .= " and status=0";
		}else{
			$where .= " and status=1";
			
			if($content['status'] == 2 )
				$where .= " and status_gerente=1 and status_email=1";
			elseif($content['status'] == 3 )
				$where .= " and status_email=0";			
			elseif($content['status'] == 4 )
				$where .= " and status_gerente is null";
			elseif($content['status'] == 5 )
				$where .= " and status_gerente=0";
		}

	}
	
	//Origem do cadastro
	if($content['origin'] > 0 ) $where .= " and origin={$content['origin']}";
	
	// Perfil do usuario
	if($content['level'] > 0 ) {
		
		if($content['level'] < 4 )  $where .= " and level={$content['level']}";
		else $where .= " and level=2 and bir=''";
		
	}
	
	// Periodo de cadastro
	if( !empty($content['start_date']) || !empty($content['end_date']) ){

		if(empty($content['start_date'])) $content['start_date'] = '0';
		if(empty($content['end_date'])) $content['end_date'] = date('Y-m-d');
			
		$where .= "and date between '{$content['start_date']}' and '{$content['end_date']}'";

	}

	//Dias sem logar
	if($content['days_no_login'] > 0 ) $where .= " and DATEDIFF('{$hoje}', last_login) <= {$content['days_no_login']}";

	//Dias sem logar
	if($content['user_category'] > 0 ) $where .= " and user_category={$content['user_category']}";

//echo "$where"; die();
	
	$users_result = $db->query("SELECT 
						id,
						CASE
							WHEN level=1 THEN 'Master'
							WHEN level=2 THEN 'Gerente'
							ELSE 'Usuário'
						END as 'Nível'
						, name as 'Nome', email as 'E-mail', bir as 'BIR', 
						CASE
							WHEN status=1 THEN 'Ativo'
							ELSE 'Desativado'
						END  as 'Situação', 
						CASE
							WHEN status_email=1 THEN 'Confirmado'
							ELSE 'Aguardando Confirmação'
						END as 'Situação E-mail', 
						CASE
							WHEN status_gerente=1 THEN 'Permitido'
							WHEN status_gerente=0 THEN 'Recusado'
							ELSE 'Aguardando Aprovação'
						END as 'Situação Gerente', 
						date as 'Data',
						CASE
							WHEN origin=1 THEN 'Painel'							
							ELSE 'Auto Cadastro'
						END as 'Origem',
						(select user_categories.name from user_categories where user_categories.id = users.user_category) as 'Categoria',
						DATEDIFF('{$hoje}', last_login) as 'Dias Sem Acesso'
						FROM `users` 
						where id>0 {$where}
						");
	
	download_send_headers("Lista Usuários_" . date("Y-m-d") . ".csv");
	echo array2csv($users_result);
	die();
	
} else {
	
	$content = get_content(false);

}

// Inicia HTML

$webadm->set_page( array( 'name' => 'Relatório Usuário' ) );
$webadm->add_parent( array( 'name' => 'Gerentes', 'url' => 'managers.php' ) );

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
				<form action="?&post" method="post" enctype="multipart/form-data" class="tab-wizard wizard-circle" autocomplete="off">					
					<?php if ( $error ) { ?>
						<div class="alert alert-danger">
					<?php echo html( $error ); ?>
						<button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">x</span></button>
						</div>
					<?php } ?>
					
					<div class="row">
						<div class="col-md">
							<div class="form-group">
								<label>BIR</label>
								<input name="bir" type="number" class="form-control" placeholder="Insira o BIR">
							</div>
						</div>
					</div>
					
					<div class="row">
						<div class="col-md">
							<div class="form-group">
								<label>Status</label>								
								<select id="status" class="form-control col-md-12 select2" name="status">
									<option value=0>Todos</option>
									<option value=1>Desativado</option>									
									<option value=2>Ativado</option>
									<option value=3>Convite Enviado</option>
									<option value=4>Confirmar Acesso</option>
									<option value=5>Recusado</option>
								</select>								
							</div>
						</div>
					</div>
					
					<div class="row">
						<div class="col-md">
							<div class="form-group">
								<label>Origem</label>
								<select id="origin" class="form-control col-md-12 select2" name="origin">
									<option value=0>Todos</option>
									<option value=1>Painel</option>
									<option value=2>Auto Cadastro</option>
								</select>
							</div>
						</div>
					</div>
					
					<div class="row">
						<div class="col-md">
							<div class="form-group">
								<label>Perfil</label>
								<select id="level" class="form-control col-md-12 select2" name="level">
									<option value=0>Todos</option>
									<option value=1>Master</option>
									<option value=4>Gerente Sem BIR</option>
									<option value=2>Gerente</option>									
									<option value=3>Usuário</option>
								</select>
							</div>
						</div>
					</div>
					
					<div class="row">
						<div class="col-md">
							<div class="form-group">
								<label>Periodo de Cadastro</label>
								<div class="row">
									<span class="col-md-1" align="right"> De </span>
									<input name="start_date" type="date" class="col-md-4 form-control" placeholder="Insira a data incio">
									<span class="col-md-1" align="center"> à </span>
									<input name="end_date"   type="date" class="col-md-4 form-control" placeholder="Insira a data fim">
								</div>		
							</div>
						</div>
					</div>
					
					<div class="row">
						<div class="col-md">
							<div class="form-group">
								<label>Dias Sem Acessar</label>
								<input name="days_no_login" type="number" class="form-control" placeholder="Insira o número, para filtar o máximo de dias sem acesso ao portal">
							</div>
						</div>
					</div>
					
					<div class="form-group m-b-10">
						<label>Categoria</label>
						<select id="user_category" class="form-control col-md-12 select2" name="user_category">
							
							<option value="0" >. . .</option>
							<?php foreach($user_categories as $user_category) {
							?>
								<option value="<?php echo html($user_category['id']); ?>" ><?php echo html($user_category['name']); ?></option>
							<?php } ?>
						</select>
					</div>
					
					<div class="form-actions">
						<div class="row">
							
								<div class="col-md-12">
									<button type="submit" class="btn btn-block btn-info text-uppercase" name="aprovar" value="0">
										<i class="mdi mdi-download m-r-5"></i> Extrair Relatório
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