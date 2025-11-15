<?php

// API

include('includes/common.php');
include('../includes/user.php');

// Codigo da pagina
$nivel = (int)get('nivel');

need_login();
$error="";
content_type('text/plain');

$level_logado = $useradm->get_property('level');
$bir_logado = $level_logado == 2 ? $useradm->get_property('bir') : "";

//Apenas Master pode acessar Importa Gerentes
if($nivel==1 && $level_logado==2)$nivel=2;

function get_property_str( $str ) {
	
	$str = strtolower( trim($str) );
	
	preg_match('/[a-z]+/i', $str, $matches);
	
	return $matches[0];
	
}

function enviaConfirmacao($usuario_novo, $email_destino, $uuid){
	
	//Preparando e-mail
	$layout=file('layoutsemails/LayoutEmail.html');
	$corpo = implode($layout);
	$corpo = str_replace('$usuario_novo', $usuario_novo, $corpo);	
	
	$link = HOST."/#p=login-confirmacao&token=".$uuid;
	
	$corpo = str_replace('$link', $link, $corpo);	
	
	$assunto="Confirmação de Cadastro";	
	
	//Envia E-mail	
	envia_email($email_destino,$assunto,$corpo, "IMG=../admin/layoutsemails/renault.png");	
}

function csv_valid($type, &$arr, $indice){
	global $db, $level_logado, $bir_logado;
	
	$error="";
	$indice++;
	
	if (empty($arr)) $error = "Dados não encontrados.";
	{	
		if($type == 't'){
			//correcao primeiro titulo
			$arr[0] = get_property_str($arr[0]);
			
			foreach($arr as $key => $value) {
		
				if('nome' == strtolower($value))  $arr[$key]= 'name';
				if('email' == strtolower($value) || $value == strtolower('e-mail'))  $arr[$key]= 'email';
				if('bir' == strtolower($value))  $arr[$key]= 'bir';		
				
			}
			
			//valida tit name
			if(!in_array("name", $arr)) $error = "Título Nome não encontrado";
			
			//valida tit email
			if(!in_array("email", $arr)) $error = "Título Email não encontrado";
			
			//valida tit bir
			if(!in_array("bir", $arr)) $error = "Título Bir não encontrado";
			
			//valida tit bir
			//if($level_logado==1 && !in_array("level", $arr)) $error = "Título level não encontrado";
			
		} elseif ($type == 'f') {
			
			//print_r ($arr);
			
			//valida field name
			if(empty($arr['name'])) 
				$error = "Nome Completo <b>".$arr['name']."</b> na linha <b>".$indice." </b> inválido";
			
			//valida field email			
			if (!filter_var($arr['email'], FILTER_VALIDATE_EMAIL)) {
				
				if($error !="")$error .="</br>";
				$error .= "Email \n <b>".$arr['email']."</b> na linha <b>".$indice." </b> inválido";
			} else {
				
				$existe = current($db->query("select 'S' from users where email='". $arr['email'] ."' "));
				
				if( $existe ){
				
					$error = "E-mail <b>'".$arr['email']."'</b>,na linha <b>".$indice."</b>, já cadastrado.";
				
				}
				
			}
			
			//valida field bir
			if(!is_numeric($arr['bir'])) {
				
				if($error !="")$error .="</br>";
				$error .= "BIR <b>".$arr['bir']."</b> na linha <b>".$indice." </b> inválido";
				
			}else {
				
				if($level_logado == 2 && $arr['bir'] != $bir_logado && $bir_logado>0){

						if($error !="")$error .="</br>";
						$error .= "BIR <b>".$arr['bir']."</b>, na linha <b>".$indice." </b>, diferente do BIR '".$bir_logado."' do gerente logado";

				}
			}
			
			/*
			if($level_logado==1 && empty($arr['level'])){
				
				if($error !="")$error .="</br>";
				$error .= "Nível <b>".$arr['bir']."</b> na linha <b>".$indice." </b> inválido";
				
			}
			
			if ($arr['level'] <=  $level_logado){
				
				if($error !="")$error .="</br>";
				$error .= "Nível <b>".$arr['level']."</b>, na linha <b>".$indice." </b>, deve ser maior que o nível logado $level_logado.";
				
			}*/
			
		}
	}
	
	return $error;
}


if (has('post') && has_upload('file')) {
	
	$error = "";
	
	$csv = file_get_contents( get_upload('file')['tmp_name'] );
	$arr = explode("\n", $csv);
	
	$array = array_map("str_getcsv", $arr);
	
	$titles = explode(";", trim($array[0][0]));
	
	$error = csv_valid('t', $titles, 0);
	
	//echo "error= $error"; die();
	
	if($error == ""){
		
		$fields = array();
		
		for($j = 1; $j < count($array); $j++){
			
			if(!empty($array[$j][0])){
				
				$campos = explode(";", trim($array[$j][0]));
				
				//carrega os dados do usuario
				for($i = 0; $i < count($titles); $i++){
				
					$fields[ get_property_str($titles[$i]) ] = $campos[$i];			
				}
				
				//valida linha - para ao primeiro erro
				$error = csv_valid('f', $fields, $j);
				if($error != "") break;
				
				//Gerente so pode incluir usuario comum
				if($nivel == 1) $fields['level'] = 2;
				else $fields['level'] = 3;
				
				if($fields['level'] == 2) $fields['status_email'] = 1;
				
				$fields['origin'] = 1;
				
				//SELECT * FROM `user_categories` WHERE name like '%GERENTE%'
				
				$categoria = "gerente";
				if($fields['level'] == 3) $categoria = "vendedor";

				$categoria_id = current(array_column( $db->query("select id from user_categories where name like '%". $categoria ."%' limit 1"), 'id' ));

				$fields['user_category'] = $categoria_id;
				
				$user = new User();
				
				$error = $user->valida($fields, 'painel');
				if( !empty($error) ) $error .= " (Linha $j)";
				
				//echo "error= $error"; die();
				if( empty($error) ) {
					
					$retorno = $user->create_user_panel($fields);
				
					if($retorno=="")$error="Erro na importação.";
					
				} else {
					
					break;
					
				}
			}
		}
		
		if($error == ""){
			
			if($nivel==1){
				
				redirect('managers.php' , array(
					'message' => 'Registros incluidos com sucesso.',
					'type' => 'success'
				));
				
			} else {
				
				redirect('users.php' , array(
					'message' => 'Registros incluidos com sucesso.',
					'type' => 'success'
				));
				
			}
		}

	}
	
}

// Inicia HTML

$webadm->set_page( array( 'name' => 'Importar Planilha' ) );

$webadm->add_parent( array( 'name' =>  (($nivel==1)? 'Gerentes' : 'Usuários'), 'url' => (($nivel==1)? 'managers.php' : 'users.php') ) );

$webadm->add_parent( array( 'name' => (($nivel==1)? 'Novo Gerente' : 'Novo Usuário'), 'url' => (($nivel==1)? 'manager_edit.php' : 'user_edit.php') ) );

$webadm->add_plugins('dropzone');
$webadm->start_panel();

?>
<!-- ============================================================== -->
<!-- Start Page Content -->
<!-- ============================================================== -->

<?php if ( $error ) { ?>
	<div class="alert alert-danger">
<?php echo $error; ?>
	<button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">x</span></button>
	</div>
<?php } ?>

<div class="row">
	<div class="col-12">
	<div class="card no-margin">
		<div id="alerts"></div>
		<div class="card-body">
			<div class="row">
				<div class="col-md">
					<p>Importe a planilha no formato <b>.CSV</b>.</p>
					<p>Cada coluna deve conter o nome do campo e seus respectivos valores como no exemplo ao lado:</p>
					
					
					<form  action="?nivel=<?php echo $nivel; ?>&post" method="post" enctype="multipart/form-data">
						<div class="dropzone">
							<input name="file" type="file" required />
						</div>
						<div class="form-actions m-t-20">
							<button type="submit" class="btn btn-block btn-info text-uppercase"><i class="fa fa-check m-r-5"></i> Importar</button>
						</div>
					</form>
				</div>
				<div class="col-md">
					<div class="table-responsive">
						<table class="table">
							<thead>
								<tr>
									<th>Nome</th>
									<th>Email</th>
									<th>Bir</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td>Lorem Ipsum A</td>
									<td>lorem_a@ipsum.com</td>
									<td>123456</td>
								</tr>
								<tr>
									<td>Lorem Ipsum B</td>
									<td>lorem_b@ipsum.com</td>
									<td>123456</td>
								</tr>
								<tr>
									<td>Lorem Ipsum C</td>
									<td>lorem_c@ipsum.com</td>
									<td>123456</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- ============================================================== -->
<!-- End Page Content -->
<!-- ============================================================== -->
<?php 

// Finaliza HTML

$webadm->end_panel();

?>