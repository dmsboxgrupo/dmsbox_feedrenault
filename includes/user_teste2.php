<?php

class User {
	
	public $id;
	public $email;
	public $password;
	public $bir;
	public $name;
	public $date;
	public $origin;
	public $status;
	public $status_gerente;
	public $status_email;
	public $level;
	public $user_category;
	
	public function __construct($id = null) {
		
		global $db;
		
		if (!empty($id)) {
            
			//carrega dados originais do db
			$user = $db->content( $id, 'users' );
           //print_r($user); die();
			if( $user['id'] ){
				
				//carrega dados
				$this->id   	= $user['id'];
				$this->email 	= $user['email'];
				$this->password = $user['password'];
				$this->bir  	= $user['bir'];
				$this->name  	= $user['name'];
				$this->status  	= $user['status'];
				$this->origin  	= $user['origin'];
				$this->status_gerente  	= $user['status_gerente'];
				$this->status_email  	= $user['status_email'];
				$this->level            = $user['level'];
				$this->user_category    = $user['user_category'];
			
			}
			
        }

    }
	
	public function create_user_panel($user) {//echo "create_user_panel";
		//print_r($user); die();
		global $db;
		
		//Aguarda Confirmacao
		$user['status_gerente']=1;

		//regisrtra
		$retorno = $db->insert('users', $user);
		
		$this->id = $retorno;
		$this->origin = 1;
		
		//envia email confirmacao
		if( $retorno > 0 ) {
			/*
			if($user['level'] == 3) $this->enviaConfirmacao( $user['name'], $user['email']);
			else $this->enviaCompleto ( $user['name'], $user['email'],"app");
			*/
			
			if($user['level'] == 3) $this->enviaConfirmacao( $user['name'], $user['email']);
			else $this->enviaCompleto ( $user['name'], $user['email'], 2, "app" );
			
			
		}
		
		return $retorno;
		
	}
	
	public function active() {
		
		global $db;

		$datatu = date('Y-m-d\ H:i:s');

		$db->update('users', $this->id, 
			array(
				'status' => 1,
				'last_login' => $datatu
			));
		
		/*if( $this->status_email == 1 && $this->status_gerente == 1 ) 
			$this->enviaCompleto ( $this->name, $this->email,"app");
		else 
			$this->enviaConfirmacao ( $this->name, $this->email);*/
		
		$this->enviaReativada ( $this->name, $this->email);
		
		return;		
	}

	public function disable($origem="web") {
		
		global $db;
		
		$db->update('users', $this->id, array('status' => 0));
		
		$this->enviaDesativacao ( $this->name, $this->email, $origem);
		
		return;		
	}
	
	public function toggle_active() {
		
		//Alterna Status
		if( $this->status > 0 )
			
			$this->disable();
			
		else
			
			$this->active();
		
		return;
		
	}

	public function toggle_level() {
		global $db; 
		//Alterna Level
		
		$campos = array();
		
		if( $this->level == 3 ){
			
			$campos['level'] = 2;
			$campos['status_gerente'] = 1;
			$campos['status_email'] = 1;
			
		} elseif( $this->level == 2 ){
			
			$campos['level'] = 3;

		}
		$db->update('users', $this->id, $campos);
		
		return;
		
	}

	public function request_reactivation() {
		
		global $db;

		$this->enviaSolicitaReativacao ($this->name, $this->id, $this->bir, $this->email);
		
		return;		
	}
	
	public function get_file( $path ) {
		
		//$data = file( $path );
		//return implode( $data );
		
		return file_get_contents( $path );
		
	}
	
	public function mail_team_incentive( $mail_team_incentive ) {
		
		global $db;

		$datatu = date('Y-m-d\ H:i:s');
		
		$mail_team_incentive++;
//echo "mail_team_incentive: $mail_team_incentive"; //die();
		$db->update('users', $this->id, 
			array(
				'mail_team_incentive' => $mail_team_incentive
			));
			
			
		if( $mail_team_incentive == 1 ){
			
			$this->enviaIncentivo1 ( $this->name, $this->email,"app");
			
		}else {
			
			if( $mail_team_incentive == 2 ){
			
				$this->enviaIncentivo2 ( $this->name, $this->email,"app");
				
			}else{
				
				if( $mail_team_incentive == 3 ){
			
					$this->enviaIncentivo3 ( $this->name, $this->email,"app");
					
				}
				
			}
			
		}
		
		/*if( $this->status_email == 1 && $this->status_gerente == 1 ) 
			$this->enviaCompleto ( $this->name, $this->email,"app");
		else 
			$this->enviaConfirmacao ( $this->name, $this->email);*/
		
		//$this->enviaReativada ( $this->name, $this->email);
		
		return;		
	}
	
	public function mail_reminder_authorize( $mail_reminder_authorize ) {
		
		global $db;

		$datatu = date('Y-m-d\ H:i:s');
		
		$mail_reminder_authorize++;

		$db->update('users', $this->id, 
			array(
				'mail_reminder_authorize' => $mail_reminder_authorize
			));
			
		
		if( $mail_reminder_authorize == 1 ){
			
			$this->enviaLembreteGerente1 ( $this->name, $this->id,  $this->bir, $this->email,"app");
			//public function enviaLembreteGerente1($usuario_novo, $id_usuario, $bir_usuario, $email_usuario){
			
		}else {
			
			if( $mail_reminder_authorize == 2 ){
			
				$this->enviaLembreteGerente2 ( $this->name, $this->id,  $this->bir, $this->email,"app");
				
			}
			
		}
		
		return;		
	}
	
	public function mail_activation_booster( $mail_activation_booster ) {
		
		global $db;

		$datatu = date('Y-m-d\ H:i:s');
		
		$mail_activation_booster++;

		$db->update('users', $this->id, 
			array(
				'mail_activation_booster' => $mail_activation_booster
			));
			
		
		if( $mail_activation_booster == 1 ){
			
			//email Reforço Ativação
			
			
			$this->enviaReforcoAtivacao1 ( $this->name, $this->email,"app");
			//$this->enviaConfirmacao( $user['name'], $user['email']);
			
		}else {
			
			if( $mail_activation_booster == 2 ){
			
				$this->enviaReforcoAtivacao2 ( $this->name, $this->email,"app");
				
			}
			
		}
		
		return;		
	}
	
	public function mail_user_incentive( $mail_user_incentive ) {
		
		global $db;

		$datatu = date('Y-m-d\ H:i:s');
		
		$mail_user_incentive++;

		$db->update('users', $this->id, 
			array(
				'mail_user_incentive' => $mail_user_incentive
			));
			
		
		if( $mail_user_incentive == 1 ){
			
			//email Reforço Ativação
			$this->enviaIncentivoUsuario1 ( $this->name, $this->email,"app");
			//$this->enviaConfirmacao( $user['name'], $user['email']);
			
		}else {
			
			if( $mail_user_incentive == 2 ){
			
				$this->enviaIncentivoUsuario2 ( $this->name, $this->email,"app");
				
			}else{
				
				if( $mail_user_incentive == 3 ){
			
					$this->enviaContaBloqueada ( $this->name, $this->email,"app");
					
				}else{
					
					if( $mail_user_incentive == 4 ){
			
						$this->enviaIncentivoReaticao ( $this->name, $this->email,"app");
						
					}
					
				}
				
				
			}
			
		}
		
		return;		
	}
	
	public function update($user)
    {
		
		global $db;
		
		//unset($user["email"]);
		$erro = $this->valida($user, 'painel');
		
		if( $erro == "" ) {
		
			if($user["email"] != $this->email) {
				
				$this->status_email == 0;
				$user['status_email'] = 0;
				
				$this->email = $user["email"];
				
			}
			
			//Apos confimcao enviar email Cadastro Completo
			if ( $this->status == 1 && $this->status_email == 1 && $user['status_gerente'] == 1 && $this->status_gerente == 0) {
	
				$this->enviaConfirmacaoGerente ( $this->name, $this->bir );	
				$this->enviaCompleto ( $this->name, $this->email, $this->level,"app");
				
			} else {
				//Apos confimcao enviar email Cadastro Completo
			
				if ( $this->status == 1 && $this->status_email != 1 ) {
						
					//FG_RC_02.html enviaConfirmacaoGerente($usuario_novo, $bir_usuario, $origem="web"){
					//$this->enviaConfirmacaoGerente ( $this->name, $this->bir );
					$this->enviaConfirmacao ( $this->name, $this->email);
					
				}else{
					
					if ( $this->status == 1 && $this->status_email == 1 && $user['status_gerente'] == 0 ) {
					
						$this->enviaRecusadoGerente ( $this->name, $this->id, $this->bir );
						$this->enviaRecusado ( $this->name, $this->email);
						
					}
					
				}
			}
			
			//if($user['email'] != $this->email)	$user['status_email'] = 0;
			
			$retorno = $db->update('users', $this->id, $user);
			
			
			if(!empty($user['password'])) $this->enviaSenhaAlterada( $this->name, $this->email);
			
			//$this->enviaRetornoAcesso( $this->name, $this->email, "web");
			
		} else {
			
			return $erro;
			
		}
		
		return $retorno;
        
    }
	
	public function update_user_app($user){
		
		global $db;
		
		$retorno = $db->update('users', $this->id, $user);
		
		if (!array_key_exists("background_image", $user) || count($user) != 1){
			
			$this->enviaContaAlterada( $this->name, $this->email, "app");
			
		}

		return $retorno;
	}
	
	public function create_user_app($user)
    {
		global $db;
		
		//Agurada Confirmacao email WebApp
		//$user['status'] = 4;
		//$this->status = 4;
		
		//Origem da inclusao Usuario App
		$user['origin'] = 2;
		$this->origin = 2;
		
		$user['level'] = 3;
		$this->level = 3;
		
		$erro = $this->valida($user, 'app');
		
		if( $erro == "" ) {

			$user['user_category'] = current(array_column( $db->query("select id from user_categories where name like '%vendedor%' limit 1"), 'id' ));

			//regisrtra
			$retorno = $db->insert('users', $user);
			
			$this->id = $retorno;
			
			//envia email confirmacao
			if( $retorno > 0 ) $this->enviaConfirmacao( $user['name'], $user['email'], "app");
			
		}else{
			
			return $erro;
			
		}
			
		
		return $retorno;

	}
	
	public function update_pass ($user) {

		global $db;
		
		//$user['status_email'] = 1;
		
		unset($user["email"]);
		
		$retorno = $db->update('users', $this->id, $user);
		
		$this->enviaSenhaAlterada( $this->name, $this->email, 'app' );
		
		
		
		return $retorno;
		
	}
	
	public function confirm ($user) {

		global $db;
		
		$user['status_email'] = 1;
		
		unset($user["email"]);
		if(empty($user["bir"])) unset($user["bir"]);
		if(empty($user["name"])) unset($user["name"]);
		if(empty($user["password"])) unset($user["password"]);
		
		$retorno = $db->update('users', $this->id, $user);
		
		if( $this->status == 1 && $this->status_gerente == 1) {
			
			$this->enviaCompleto ( $this->name, $this->email, 3 );
			
		} else {
			
			$this->enviaAguardaGerente( $this->name, $this->email );

			$this->enviaPendenciaGerente( $this->name, $this->id, $user['bir'], $this->email );
			
		}
		
		return $retorno;
		
	}
	
	public function password_request(){
		
		global $db;
		
		try {
		
			//$db->update('users', $this->id, array('status_email' => 0));
			
			$this->enviaTrocaSenha( $this->name, $this->email, "app");
			
		} catch (Exception $exc) {
            return $exc->getMessage();
        }
		
		return "Solitação concluida com sucesso.";
	}
	
	
	public function valida($user, $origem='app'){
		
		global $db;
		$erro ="";
		
		$metadata = $db->get_metadata( "blocked_domains" );
		$dominios_bloqs = array_filter( explode(',',$metadata['value']) );
		
		if( $user['name'] == '' ) $erro = "Nome obrigatório.";
		
		//validando email
		
		if( $user['email'] != '' ){
			
			if($user['email'] != $this->email){
				
				//testa formato
				if (!filter_var($user['email'], FILTER_VALIDATE_EMAIL))
					$erro = "Formato e-mail inválido";

				if($origem == 'app' ) {
					
					//testa dominio
					foreach($dominios_bloqs as $dominio_bloq) {
						if (strpos(strtolower($user['email']), $dominio_bloq) !== false) {
							//return "E-mails com domínio '".$dominio_bloq."', não são permitidos.";
							return "Serão permitidos apenas e-mails empresariais.";
						}
					}
					
				}
				
				//testa se e-mail ja existe
				if( $erro == ""){
				
					$existe = $db->query("select 'S' from users where email='".$user['email']."' ");
					
					if( $existe && $existe[0]['S'] == 'S' ) {
						
						$erro = "E-mail '".$user['email']."' já cadastrado.";
						
					}
					
				}
				
			}
			
		} else {
			
			$erro = "E-mail obrigatório.";
			
		}
		
		
		//if( $user['origin'] == 2 ) {
		//if( $action == 'U') {
		if($origem == 'app' ) {
			
			if( $user['bir'] == '' ) {
				
				$erro = "BIR obrigatório.";
			
			} else {

				if( strlen ($user['bir']) != 7  ) {
				
					$erro = "Número da BIR '{$user['bir']}' inválido, deve conter 7 dígitos.";
			
				} else {
				
					$existe = current($db->query("select 'S' from users where level=2 and bir=".$user['bir']." limit 1 "));
					
					if( empty($existe['S']) ) {

						$erro = "BIR '{$user['bir']}' não cadastrado.";
						
					}
				
				}

			}
		
		} elseif( !empty($user['bir']) && strlen ($user['bir']) != 7){
			
			$erro = "Número da BIR '{$user['bir']}' inválido, deve conter 7 dígitos.";
			
		}
		
		if( $origem=="app" && $user['password'] == '' ) $erro = "Senha obrigatória.";
		
		return $erro;
	}
	
	// Funcoes de Preparacao emails
	public function enviaAguardaGerente($usuario_novo, $email_destino){
		
		$prefixo="";
		
		//$layout = $prefixo.'admin/layoutsemails/LayoutEmailAguardaGerente.html';
		//$layout = $prefixo.'admin/emails/06_confirmacao_cad_funcion.html';
		$layout = $prefixo.'admin/emails/FV_VP_01.html';
		
		$corpo = $this->get_file($layout);
		//echo "usuario_novo= $usuario_novo"; die();
		$corpo = str_replace('$usuario_novo', $usuario_novo, $corpo);
		
		$assunto="Confirmação de Cadastro - Aguardando Confirmação";
		
		//Imagens		
		/*$IMG = $prefixo."admin/emails/img/logob.jpg;".
			   $prefixo."admin/emails/img/ico9.jpg;".
			   $prefixo."admin/emails/img/fundo4.jpg";*/
			   
		$IMG = $prefixo."admin/emails/img/topo.jpg;".
			   $prefixo."admin/emails/img/header.jpg;".
			   $prefixo."admin/emails/img/img_cel_01.jpg;".
			   $prefixo."admin/emails/img/footer.jpg";
		
		//Envia E-mail	
		envia_email($email_destino,$assunto,$corpo, "IMG=".$IMG);
	}
	
	public function enviaPendenciaGerente($usuario_novo, $id_usuario, $bir_usuario, $email_usuario){
		
		global $db;
		
		$prefixo="";
		
		$layout = $prefixo.'admin/emails/FG_RC_01.html';
		
		$corpo = $this->get_file($layout);
		
		//Primeiro nome do usuario		
		$nome_usuario = explode(' ', $usuario_novo);
		
		//Nome do usuario_novo				
		$corpo = str_replace('$nome_usuario', $nome_usuario['0'], $corpo);
		
		//email usuario
		$corpo = str_replace('$email_usuario', $email_usuario, $corpo);
		//bir
		$corpo = str_replace('$bir_usuario', $bir_usuario, $corpo);
		
		//nome completo
		$corpo = str_replace('$usuario_novo', $usuario_novo, $corpo);
		
		//$uuid = $token->generate(60 * 24 * 30, $this->id);
		//$link = HOST."/#p=login-confirmacao&token=".$uuid;
		
		//link aceita - recusa
		//$link_users = HOST."/admin/users.php";
		$link_users = "https://feedrenault.com.br/admin/users.php";
		
		$corpo = str_replace('$link_users', $link_users, $corpo);
		//
		//link usuarios
		//$link = HOST."/admin/user_edit.php?id={$id_usuario}&accept=";
		$link = "https://feedrenault.com.br/admin/user_edit.php?id={$id_usuario}&accept=";
		//
		$corpo = str_replace('$link', $link, $corpo);
		
		//$assunto="Aprovar novo acesso";
		$assunto="Tem um cadastro aguardando a sua aprovação no Feed Renault!";
		
		//Imagens		
		$IMG = $prefixo."admin/emails/img/topo.jpg;".
			   $prefixo."admin/emails/img/header.jpg;".
			   $prefixo."admin/emails/img/img_cel_01.jpg;".
			   //$prefixo."admin/emails/img/linha.jpg;".
			   $prefixo."admin/emails/img/bt_negar_acesso.jpg;".
			   $prefixo."admin/emails/img/bt_permitir_acesso.jpg;".
			   //$prefixo."admin/emails/img/linha.jpg;".
			   $prefixo."admin/emails/img/footer.jpg";

		$users = $db->query("SELECT * from users where level=2 and status=1 and bir=$bir_usuario ");
		foreach($users as &$user) {
			
			//nome gerente
			$nome_gerente = explode(' ', $user['name']);			
			
			$corpo = str_replace('$nome_gerente', $nome_gerente['0'], $corpo);
			
			
			//Envia E-mail	
			envia_email($user['email'],$assunto,$corpo, "IMG=".$IMG);
		
			//echo "Gerente={$user['name']} email={$user['email']}";
			
		}
		//die();
	}
	
	public function enviaSolicitaReativacao($usuario_novo, $id_usuario, $bir_usuario, $email_usuario){
		global $db;
		
		$prefixo="";
		
		$layout = $prefixo.'admin/emails/16_reativar_acesso_gerente.html';
		
		$corpo = $this->get_file($layout);
		
		//Primeiro nome do usuario		
		$nome_usuario = explode(' ', $usuario_novo);
		
		//Nome do usuario_novo				
		$corpo = str_replace('$nome_usuario', $nome_usuario['0'], $corpo);
		
		//email usuario
		$corpo = str_replace('$email_usuario', $email_usuario, $corpo);
		//bir
		$corpo = str_replace('$bir_usuario', $bir_usuario, $corpo);
		
		//nome completo
		$corpo = str_replace('$usuario_novo', $usuario_novo, $corpo);
		
		//$uuid = $token->generate(60 * 24 * 30, $this->id);
		//$link = HOST."/#p=login-confirmacao&token=".$uuid;
		
		//link aceita - recusa
		$link_users = HOST."/admin/users.php";
		
		$corpo = str_replace('$link_users', $link_users, $corpo);
		
		//link usuarios		
		$link = HOST."/admin/user_edit.php?id={$id_usuario}&toggle_active";
		
		$corpo = str_replace('$link', $link, $corpo);
		
		$assunto="Reativar acesso";
		
		//Imagens		
		$IMG = $prefixo."admin/emails/img/logoc.jpg;".
			   $prefixo."admin/emails/img/bot2_1.jpg;".			   
			   $prefixo."admin/emails/img/spacer.gif;".
			   $prefixo."admin/emails/img/bot2_2.jpg;".
			   $prefixo."admin/emails/img/bot2_3.jpg;".
			   $prefixo."admin/emails/img/bot2_4.jpg";

		$users = $db->query("SELECT * from users where level=2 and status=1 and bir=$bir_usuario ");
		foreach($users as &$user) {
			
			//nome gerente
			$nome_gerente = explode(' ', $user['name']);			
			
			$corpo = str_replace('$nome_gerente', $nome_gerente['0'], $corpo);
			
			
			//Envia E-mail	
			envia_email($user['email'],$assunto,$corpo, "IMG=".$IMG);
		
			//echo "Gerente={$user['name']} email={$user['email']}";
			
		}
		//die();
	}
	
	public function enviaCompleto_old($usuario_novo, $email_destino, $origem="web"){
		
		$prefixo="";
				
		if($origem=="app") $prefixo="../";
		
		//Preparando e-mail
		//$layout = $prefixo.'admin/layoutsemails/LayoutEmailCompleto.html';
		//if($origem=="web")
		if($this->origin==1){
			
			$layout = $prefixo.'admin/emails/04_conta_criada_funcion.html';
			
			//Imagens
			$IMG = $prefixo."admin/emails/img/logoc.jpg;".
					$prefixo."admin/emails/img/botstore.jpg;".
					$prefixo."admin/emails/img/botpplay.jpg";
		}
		else{
			
			$layout = $prefixo.'admin/emails/08_acesso_liberado_funcio.html';
			
			//Imagens
			$IMG = $prefixo."admin/emails/img/logob.jpg;".$prefixo."admin/emails/img/fundo2.jpg;".
				$prefixo."admin/emails/img/ico8.jpg;".$prefixo."admin/emails/img/botstore.jpg;".
				$prefixo."admin/emails/img/botpplay.jpg";
		}
		
		$corpo = $this->get_file($layout);
		
		$corpo = str_replace('$usuario_novo', $usuario_novo, $corpo);
		
		$assunto="Confirmação de Cadastro - Cadastro Completo";
		
		//Envia E-mail
		envia_email($email_destino,$assunto,$corpo, "IMG=".$IMG);
	}
	
	public function enviaCompleto($usuario_novo, $email_destino, $level, $origem="web"){
		
		$prefixo="";
				
		if($origem=="app") $prefixo="../";
	//print_r($level);die();	
		//if($this->origin==1){
		if($level == 2){
			
			// completo gerente 
			$layout = $prefixo.'admin/emails/FG_01.html';
			
			//Imagens
			$IMG = $prefixo."admin/emails/img/topo.jpg;".
					$prefixo."admin/emails/img/header.jpg;".
					$prefixo."admin/emails/img/img_cel_01.jpg;".
					$prefixo."admin/emails/img/img_cel_02.jpg;".
					$prefixo."admin/emails/img/bt_acessar_o_guia_de_gerentes.jpg;".
					$prefixo."admin/emails/img/bt_video_tutorial_feed_renault.jpg;".
					$prefixo."admin/emails/img/bt_apple_store.jpg;".
					$prefixo."admin/emails/img/bt_google_play.jpg;".
					$prefixo."admin/emails/img/footer.jpg";
			
			$link = HOST."/#p=login-cadastro-senha&token=".$uuid;
		
			$corpo = str_replace('$link', $link, $corpo);	
			
			$assunto="Bem-vindo ao Feed Renault!";
			
		}
		else{
			
			// completo vendedor 
			
			/*
			$layout = $prefixo.'admin/emails/08_acesso_liberado_funcio.html';
			//$layout = $prefixo.'admin/emails/BoasVindasV.html';
			
			//Imagens
			$IMG = $prefixo."admin/emails/img/logob.jpg;".$prefixo."admin/emails/img/fundo2.jpg;".
				$prefixo."admin/emails/img/ico8.jpg;".$prefixo."admin/emails/img/botstore.jpg;".
				$prefixo."admin/emails/img/botpplay.jpg";*/
				
			$layout = $prefixo.'admin/emails/BoasVindasV.html';
			//$layout = $prefixo.'admin/emails/BoasVindasV.html';
			
			//Imagens
			$IMG = $prefixo."admin/emails/img/topo.jpg;".$prefixo."admin/emails/img/header.jpg;".
			$prefixo."admin/emails/img/img_cel_01.jpg;".$prefixo."admin/emails/img/img_cel_02.jpg;".
			$prefixo."admin/emails/img/bt_video_tutorial_feed_renault.jpg;".$prefixo."admin/emails/img/bt_feedrenault.jpg;".
			$prefixo."admin/emails/img/bt_apple_store.jpg;".$prefixo."admin/emails/img/bt_google_play.jpg;".
			$prefixo."admin/emails/img/footer.jpg";
			
			$assunto="Confirmação de Cadastro - Cadastro Completo";
			
		}
		
		$corpo = $this->get_file($layout);
		
		//$link_gerentes= "https://feedrenault.com.br/admin/managers.php";		
		$link_gerentes= "https://renault-br.my.salesforce.com/sfc/p/D0000000rYsz/a/7T000000R6tO/q8RTdDSLa.UJ3alvbobLNQ351lxdGvwJRXTugHR2450";		
		$corpo = str_replace('$link_gerentes', $link_gerentes, $corpo);
		
		$link_tutorial="https://renault-br.my.salesforce.com/sfc/p/D0000000rYsz/a/7T000000R6tT/PNAKIC3Y49kid6nNUUoL20E1gyW9f_8AnmgNfSaG4e4";
		$corpo = str_replace('$link_tutorial', $link_tutorial, $corpo);
		
		//$corpo = str_replace('$usuario_novo', $usuario_novo, $corpo);
		
		//$assunto="Confirmação de Cadastro - Cadastro Completo";
		
		//Envia E-mail
		envia_email($email_destino,$assunto,$corpo, "IMG=".$IMG);
	}

	public function enviaConfirmacao($usuario_novo, $email_destino, $origem="web"){
		
		global $token;
		
		$prefixo="../";
		//if($this->origin==2) $prefixo="";
		//if($this->status==4 || $this->status==3) $prefixo="";
		if($origem=="app") $prefixo="";

		//gera token
		$uuid = $token->generate(60 * 60 * 24 * 30, $this->id);

		
		//----------------  FV_DMSG_01.html
		
		if($this->origin==1){
				
			/*$layout = $prefixo.'admin/emails/03_ativar_conta_funcion.html';	
			$layout = $prefixo.'admin/emails/FV_DMSG_01.html';	
			
			$IMG = $prefixo."admin/emails/img/logop.jpg;".$prefixo."admin/emails/img/ativ1.jpg;".
				 $prefixo."admin/emails/img/fundo.jpg;".$prefixo."admin/emails/img/functab.gif;".
				 $prefixo."admin/emails/img/ativ2.gif";
			
			$link = HOST."/#p=login-confirmacao&token=".$uuid;*/
			
			$layout = $prefixo.'admin/emails/FV_VP_02.html';	
			
			$IMG = $prefixo."admin/emails/img/topo.jpg;".$prefixo."admin/emails/img/header.jpg;".
				 $prefixo."admin/emails/img/img_cel_01.jpg;".
				 $prefixo."admin/emails/img/bt_ativar_agora.jpg;".
				 $prefixo."admin/emails/img/img_cel_02.jpg;".
				 $prefixo."admin/emails/img/bt_apple_store.jpg;".
				 $prefixo."admin/emails/img/bt_google_play.jpg;".
				 $prefixo."admin/emails/img/footer.jpg";
			
			//$link = HOST."/#p=login-confirmacao&token=".$uuid;
			$link = "https://feedrenault.com.br/#p=login-confirmacao&token=".$uuid;
			
			$assunto="Confirmação de Cadastro";
			
		}
		else{
			/*
			$layout = $prefixo.'admin/emails/05_confirmar_funcion.html';
			
			$IMG = $prefixo."admin/emails/img/logop.jpg;".$prefixo."admin/emails/img/botconfcad.gif";
			
			$link = HOST."/#p=login-confirmacao-email&token=".$uuid;
			*/
			
			$layout = $prefixo.'admin/emails/FV_DMSG_01.html';	
			
			$IMG = $prefixo."admin/emails/img/topo.jpg;".$prefixo."admin/emails/img/header.jpg;".
				 $prefixo."admin/emails/img/img_cel_01.jpg;".$prefixo."admin/emails/img/bt_ativar_a_minha_conta.jpg;".
				 $prefixo."admin/emails/img/footer.jpg";
			
			//$link = HOST."/#p=login-confirmacao-email&token=".$uuid;
			$link = "https://feedrenault.com.br/#p=login-confirmacao-email&token=".$uuid;
			
			$assunto="Ative a sua conta no Feed Renault!";
			
		}
		
		$corpo = $this->get_file($layout);
		
		$corpo = str_replace('$usuario_novo', $usuario_novo, $corpo);	
		
		$corpo = str_replace('$link', $link, $corpo);	
		
		//$assunto="Confirmação de Cadastro";

		//Envia E-mail	
		envia_email($email_destino,$assunto,$corpo, "IMG=".$IMG);
		
	}

	public function enviaTrocaSenha($usuario_novo, $email_destino, $origem="web"){
		
		global $token;
		
		$prefixo="../";
		
		if($origem=="app") $prefixo="";
		
		//gera token
		$uuid = $token->generate(60 * 60 * 24 * 30, $this->id);
		
		//Preparando e-mail 
		//$layout = $prefixo."admin/layoutsemails/LayoutEmail_TrocaSenha.html";
		
		//Novo layout C:\xampp\htdocs\renault\includes
		//$layout = $prefixo."admin/emails/12_redefinir_senha_funcion.html";
		$layout = $prefixo."admin/emails/AS_01.html";
		
		$corpo = $this->get_file($layout);
		
		$corpo = str_replace('$usuario_novo', $usuario_novo, $corpo);	
		
		//$link = HOST."/user_passrequest.php?token=".$uuid;
		$link = HOST."/#p=login-cadastro-senha&token=".$uuid;
		
		$corpo = str_replace('$link', $link, $corpo);	
		
		$assunto="Recuperação de senha";
		
		//Imagens		
		/*$IMG = $prefixo."admin/emails/img/logop.jpg;".
			   $prefixo."admin/emails/img/botaltsenha.gif";*/
			   
		$IMG = $prefixo."admin/emails/img/topo.jpg;".
				$prefixo."admin/emails/img/header.jpg;".
				$prefixo."admin/emails/img/img_cel_01.jpg;".
				$prefixo."admin/emails/img/bt_alterar_senha.jpg;".
				$prefixo."admin/emails/img/footer.jpg";

		//Envia E-mail	
		envia_email($email_destino,$assunto,$corpo, "IMG=".$IMG);
	}

	public function enviaSenhaAlterada($usuario_novo, $email_destino, $origem="web"){
		
		global $token;
		
		$prefixo="../";
		
		if($origem=="app") $prefixo="";
		
		//Novo layout
		//$layout = $prefixo."admin/emails/13_senha_alterada_funcion.html";
		$layout = $prefixo."admin/emails/AS_02.html";
		
		$corpo = $this->get_file($layout);
		
		$corpo = str_replace('$usuario_novo', $usuario_novo, $corpo);	
		
		$assunto="Senha Alterada";
		
		//Imagens		
		$IMG = $prefixo."admin/emails/img/topo.jpg;".
				$prefixo."admin/emails/img/header.jpg;".
				$prefixo."admin/emails/img/img_cel_01.jpg;".
				$prefixo."admin/emails/img/footer.jpg";
		
		//Envia E-mail	
		envia_email($email_destino,$assunto,$corpo, "IMG=".$IMG);
	}

	public function enviaDesativacao($usuario_novo, $email_destino, $origem){

		global $token;

		$prefixo="../";
		
		if($origem=="app") $prefixo="";

		//Preparando e-mail
		$layout = $prefixo."admin/emails/10_conta_desativada_funcion.html";
		
		$corpo 	= $this->get_file( $layout );
		
		$corpo 	= str_replace( '$usuario_novo', $usuario_novo, $corpo );

		$uuid = $token->generate(60 * 60 * 24 * 30, $this->id);

		//$link = HOST."/#p=request_reactivation&token=".$uuid;
		$link = HOST."/#p=login-confirmacao-gerente&token=".$uuid;
		
		$corpo = str_replace('$link', $link, $corpo);	
		
		$assunto="Conta Desativada";
		
		//Imagens		
		$IMG = $prefixo."admin/emails/img/logop.jpg;".
				$prefixo."admin/emails/img/ico6.jpg;".
				$prefixo."admin/emails/img/botsolireat.gif";
		
		//Envia E-mail	
		envia_email($email_destino,$assunto,$corpo, "IMG=".$IMG);
	}
	
	public function enviaReativada_old($usuario_novo, $email_destino){

		$prefixo="../";
		//if($this->origin==2)$prefixo="";

		//Preparando e-mail
		//$layout =  $prefixo."admin/layoutsemails/LayoutEmailDesativado.html";
		$layout = $prefixo."admin/emails/11_conta_reativada_funcion.html";
		
		$corpo 	= $this->get_file( $layout );
		
		$corpo 	= str_replace( '$usuario_novo', $usuario_novo, $corpo );

		$assunto="Conta Reativada";
		
		//Imagens		
		$IMG = $prefixo."admin/emails/img/logop.jpg;".
				$prefixo."admin/emails/img/ico5.jpg;".
			    $prefixo."admin/emails/img/botirparafr.gif";
		
		//Envia E-mail	
		envia_email($email_destino,$assunto,$corpo, "IMG=".$IMG);
	}
	
	public function enviaReativada($usuario_novo, $email_destino){

		$prefixo="../";
		//if($this->origin==2)$prefixo="";

		//Preparando e-mail
		//$layout =  $prefixo."admin/layoutsemails/LayoutEmailDesativado.html";
		$layout = $prefixo."admin/emails/FveG_03.html";
		
		$corpo 	= $this->get_file( $layout );
	print_r($corpo); die();	
		$corpo 	= str_replace( '$usuario_novo', $usuario_novo, $corpo );

		$assunto="Eba! A sua conta no Feed Renault foi reativada.";
		
		//Imagens		
		$IMG = $prefixo."admin/emails/img/topo.jpg;".
				$prefixo."admin/emails/img/header.jpg;".
				$prefixo."admin/emails/img/img_cel_01.jpg;".
				$prefixo."admin/emails/img/bt_acessar_agora.jpg;".
			    $prefixo."admin/emails/img/footer.jpg";
		
		//Envia E-mail	
		envia_email($email_destino,$assunto,$corpo, "IMG=".$IMG);
	}
	
	public function enviaContaAlterada($usuario_novo, $email_destino, $origem="web"){

		$prefixo="../";
		if($origem=="app") $prefixo="";

		//Preparando e-mail
		//$layout =  $prefixo."admin/layoutsemails/LayoutEmailDesativado.html";
		$layout = $prefixo."admin/emails/15_conta_alterada.html";
		
		$corpo 	= $this->get_file( $layout );
		
		$corpo 	= str_replace( '$usuario_novo', $usuario_novo, $corpo );

		$assunto="Conta Alterada";
		
		//Imagens		
		$IMG = $prefixo."admin/emails/img/logop.jpg;".
				$prefixo."admin/emails/img/ico5.jpg;".
				$prefixo."admin/emails/img/bot3_1.jpg;".
				$prefixo."admin/emails/img/spacer.gif;".
				$prefixo."admin/emails/img/bot3_2.jpg;".
				$prefixo."admin/emails/img/bot3_3.jpg;".
			    $prefixo."admin/emails/img/bot3_4.jpg";
		
		//Envia E-mail	
		envia_email($email_destino,$assunto,$corpo, "IMG=".$IMG);
	}
	
	public function enviaRecusado($usuario_novo, $email_destino){

		$prefixo="../";
		//if($this->origin==2)$prefixo="";

		//Preparando e-mail
		//$layout = $prefixo."admin/layoutsemails/LayoutEmail_Recusado.html";
		//$layout = $prefixo."admin/emails/09_cadastro_recusado_funcion.html";	
		$layout = $prefixo."admin/emails/FV_VP_03.html";				
		
		$corpo 	= $this->get_file( $layout );
		
		$corpo 	= str_replace( '$usuario_novo', $usuario_novo, $corpo );

		$assunto="Cadastro Recusado";
		
		/*$IMG = $prefixo."admin/emails/img/logob.jpg;".
				$prefixo."admin/emails/img/ico7.jpg;".
				$prefixo."admin/emails/img/spacer.gif";*/

		$IMG = $prefixo."admin/emails/img/topo.jpg;".
				$prefixo."admin/emails/img/header.jpg;".
				$prefixo."admin/emails/img/img_cel_01.jpg;".
				$prefixo."admin/emails/img/footer.jpg";

		//Envia E-mail	
		envia_email($email_destino,$assunto,$corpo, "IMG=".$IMG );
	}
	
	public function enviaConvite($usuario_novo, $email_destino, $origem="web"){

		$prefixo="../";
		if($origem=="app") $prefixo="";

		//Preparando e-mail
		$layout = $prefixo."admin/emails/01_convite_funcion.html";		
		
		$corpo 	= $this->get_file( $layout );
		
		$corpo 	= str_replace( '$usuario_novo', $usuario_novo, $corpo );

		$assunto="Feed Renault";
		
		$link = "https://feedrenault.com.br/";
		
		$corpo = str_replace('$link', $link, $corpo);
		
		$IMG = $prefixo."admin/emails/img/logop.jpg;".$prefixo."admin/emails/img/cad1.jpg;".$prefixo."admin/emails/img/fundo.jpg;".
			$prefixo."admin/emails/img/functab.gif;".$prefixo."admin/emails/img/cadbot.gif";
		
		//Envia E-mail	
		envia_email($email_destino,$assunto,$corpo, "IMG=".$IMG );
	}
	
	public function enviaConviteGerente($usuario_novo, $email_destino, $origem="web"){

		$prefixo="../";
		if($origem=="app") $prefixo="";

		//Preparando e-mail
		$layout = $prefixo."admin/emails/02_convite_gerente.html";
		
		$corpo 	= $this->get_file( $layout );
		
		$corpo 	= str_replace( '$usuario_novo', $usuario_novo, $corpo );

		$link = "https://feedrenault.com.br/";
		
		$corpo = str_replace('$link', $link, $corpo);
		
		$assunto="Feed Renault";
		
		$IMG = $prefixo."admin/emails/img/logop.jpg;".$prefixo."admin/emails/img/cad2.jpg;".$prefixo."admin/emails/img/gertab.gif;".
			$prefixo."admin/emails/img/ger2.gif;".$prefixo."admin/emails/img/ger3.gif";
		
		//Envia E-mail	
		envia_email($email_destino,$assunto,$corpo, "IMG=".$IMG );
	}
	
	public function enviaRetornoAcesso($usuario_novo, $email_destino, $origem="web"){

		$prefixo="../";
		if($origem=="app") $prefixo="";

		//Preparando e-mail
		$layout = $prefixo."admin/emails/14_retorno_acesso.html";
		
		$corpo 	= $this->get_file( $layout );
		
		$corpo 	= str_replace( '$usuario_novo', $usuario_novo, $corpo );

		$link = "https://feedrenault.com.br/";
		
		$corpo = str_replace('$link', $link, $corpo);
		
		$assunto="Feed Renault";
		
		$IMG = $prefixo."admin/emails/img/logob.jpg;" .$prefixo."admin/emails/img/bot3_1.jpg;".$prefixo."admin/emails/img/spacer.gif;".$prefixo."admin/emails/img/fundo3.jpg;".
			$prefixo."admin/emails/img/bot3_2.jpg;".$prefixo."admin/emails/img/bot3_3.jpg;".$prefixo."admin/emails/img/bot3_4.jpg";
		
		//Envia E-mail	
		envia_email($email_destino,$assunto,$corpo, "IMG=".$IMG );
	}
	
	public function enviaIncentivo1($usuario_novo, $email_destino, $origem="web"){
		
		$prefixo="";
		
		//ativar via task
		//if($origem=="app") $prefixo="../";
		
		//Preparando e-mail
		$layout = $prefixo.'admin/emails/FG_02.html';

		$IMG = $prefixo."admin/emails/img/topo.jpg;".
				$prefixo."admin/emails/img/header.jpg;".
				$prefixo."admin/emails/img/img_cel_01.jpg;".
				$prefixo."admin/emails/img/img_cel_02.jpg;".
				$prefixo."admin/emails/img/footer.jpg";
		
		
		$corpo = $this->get_file($layout);
		
		$corpo = str_replace('$usuario_novo', $usuario_novo, $corpo);

		$assunto="A sua equipe já está usando o Feed Renault?";
		
		//Envia E-mail
		envia_email( $email_destino, $assunto,$corpo, "IMG=".$IMG);
	}
	
	public function enviaIncentivo2($usuario_novo, $email_destino, $origem="web"){
		
		$prefixo="";
		
		//ativar via task
		//if($origem=="app") $prefixo="../";
		
		//Preparando e-mail
		$layout = $prefixo.'admin/emails/FG_03.html';

		$IMG = $prefixo."admin/emails/img/topo.jpg;".
				$prefixo."admin/emails/img/header.jpg;".
				$prefixo."admin/emails/img/img_cel_01.jpg;".
				$prefixo."admin/emails/img/img_cel_02.jpg;".
				$prefixo."admin/emails/img/bt_atualizar_a_minha_equipe.jpg;".
				$prefixo."admin/emails/img/bt_acessar_o_guia_de_gerentes.jpg;".
				$prefixo."admin/emails/img/footer.jpg";
		
		
		$corpo = $this->get_file($layout);
		
		$corpo = str_replace('$usuario_novo', $usuario_novo, $corpo);
		
		//link usuarios		
		$link_users = HOST."/admin/users.php";
		
		$corpo = str_replace('$link_users', $link_users, $corpo);
		
		$link_ajuda = "https://renault-br.my.salesforce.com/sfc/p/D0000000rYsz/a/7T000000R6tO/q8RTdDSLa.UJ3alvbobLNQ351lxdGvwJRXTugHR2450";
		
		$corpo = str_replace('$link_ajuda', $link_ajuda, $corpo);
		
		$assunto = "Lembrete de hoje: atualizar a sua equipe no Feed Renault!";
		
		//Envia E-mail
		envia_email( $email_destino, $assunto,$corpo, "IMG=".$IMG);
	}
	
	public function enviaIncentivo3($usuario_novo, $email_destino, $origem="web"){
		
		$prefixo="";
		
		//ativar via task
		//if($origem=="app") $prefixo="../";
		
		//Preparando e-mail
		$layout = $prefixo.'admin/emails/FG_04.html';

		$IMG = $prefixo."admin/emails/img/topo.jpg;".
				$prefixo."admin/emails/img/header.jpg;".
				$prefixo."admin/emails/img/img_cel_01.jpg;".
				$prefixo."admin/emails/img/bt_quero_dar_uma_sugestao.jpg;".
				$prefixo."admin/emails/img/footer.jpg";
		
		
		$corpo = $this->get_file($layout);
		
		$corpo = str_replace('$usuario_novo', $usuario_novo, $corpo);
		
		//link usuarios		
		$link_feedback = "mailto:atendimento.renault@dmsbox.com.br";
		
		$corpo = str_replace('$link_feedback', $link_feedback, $corpo);
		
		$assunto = "Mensagem importante do Feed Renault para você, gerente!";
		
		//Envia E-mail
		envia_email( $email_destino, $assunto,$corpo, "IMG=".$IMG);
		
	}
	
	public function enviaLembreteGerente1($usuario_novo, $id_usuario, $bir_usuario, $email_usuario, $origem="web"){
		
		global $db;
		
		$prefixo="";
		//if($origem=="app") $prefixo="../";
		
		$layout = $prefixo.'admin/emails/FG_RC_04.html';
		
		$corpo = $this->get_file($layout);
		
		//Primeiro nome do usuario		
		$nome_usuario = explode(' ', $usuario_novo);
		
		//Nome do usuario_novo				
		$corpo = str_replace('$nome_usuario', $nome_usuario['0'], $corpo);
		
		//email usuario
		$corpo = str_replace('$email_usuario', $email_usuario, $corpo);
		//bir
		$corpo = str_replace('$bir_usuario', $bir_usuario, $corpo);
		
		//nome completo
		$corpo = str_replace('$usuario_novo', $usuario_novo, $corpo);
		
		//link usuarios
		//$link = HOST."/admin/user_edit.php?id={$id_usuario}&accept=";
		$link = "https://feedrenault.com.br/admin/user_edit.php?id={$id_usuario}&accept=";
		
		//
		$corpo = str_replace('$link', $link, $corpo);
		
		$assunto="Não se esqueça de aprovar";
		
		//Imagens		
		$IMG = $prefixo."admin/emails/img/topo.jpg;".
			   $prefixo."admin/emails/img/header.jpg;".
			   $prefixo."admin/emails/img/img_cel_01.jpg;".
			   $prefixo."admin/emails/img/bt_negar_acesso.jpg;".
			   $prefixo."admin/emails/img/bt_permitir_acesso.jpg;".
			   //$prefixo."admin/emails/img/linha.jpg;".
			   $prefixo."admin/emails/img/footer.jpg";

		$users_managers = $db->query("SELECT * from users where level=2 and status=1 and bir=$bir_usuario ");
		foreach($users_managers as &$user_manager) {
			
			//nome gerente
			$nome_gerente = explode(' ', $user_manager['name']);			
			
			$corpo = str_replace('$nome_gerente', $nome_gerente['0'], $corpo);

			//Envia E-mail	
			envia_email($user_manager['email'],$assunto,$corpo, "IMG=".$IMG);
			
		}

	}
	
	public function enviaLembreteGerente2($usuario_novo, $id_usuario, $bir_usuario, $email_usuario, $origem="web"){
		
		global $db;
		
		$prefixo="";
		//if($origem=="app") $prefixo="../";
		
		$layout = $prefixo.'admin/emails/FG_RC_05.html';
		
		$corpo = $this->get_file($layout);
		
		//Primeiro nome do usuario		
		$nome_usuario = explode(' ', $usuario_novo);
		
		//Nome do usuario_novo				
		$corpo = str_replace('$nome_usuario', $nome_usuario['0'], $corpo);
		
		//email usuario
		$corpo = str_replace('$email_usuario', $email_usuario, $corpo);
		//bir
		$corpo = str_replace('$bir_usuario', $bir_usuario, $corpo);
		
		//nome completo
		$corpo = str_replace('$usuario_novo', $usuario_novo, $corpo);
		
		//link usuarios
		//$link = HOST."/admin/user_edit.php?id={$id_usuario}&accept=";
		$link = "https://feedrenault.com.br/admin/user_edit.php?id={$id_usuario}&accept=";
		
		//
		$corpo = str_replace('$link', $link, $corpo);
		
		$assunto="Aprove agora mesmo o cadastro!";
		
		//Imagens		
		$IMG = $prefixo."admin/emails/img/topo.jpg;".
			   $prefixo."admin/emails/img/header.jpg;".
			   //$prefixo."admin/emails/img/linha.jpg;".
			   $prefixo."admin/emails/img/bt_negar_acesso.jpg;".
			   $prefixo."admin/emails/img/bt_permitir_acesso.jpg;".
			   //$prefixo."admin/emails/img/linha.jpg;".
			   $prefixo."admin/emails/img/img_cel_01.jpg;".
			   $prefixo."admin/emails/img/footer.jpg";

		$users_managers = $db->query("SELECT * from users where level=2 and status=1 and bir=$bir_usuario ");
		foreach($users_managers as &$user_manager) {
			
			//nome gerente
			$nome_gerente = explode(' ', $user_manager['name']);			
			
			$corpo = str_replace('$nome_gerente', $nome_gerente['0'], $corpo);

			//Envia E-mail	
			envia_email($user_manager['email'],$assunto,$corpo, "IMG=".$IMG);
			
		}

	}
	
	public function enviaReforcoAtivacao1($usuario_novo, $email_destino, $origem="web"){
		
		global $token;
		
		$prefixo="";

		$uuid = $token->generate(60 * 60 * 24 * 30, $this->id);

		$layout = $prefixo.'admin/emails/FV_DMSG_02.html';
			
		$IMG = $prefixo."admin/emails/img/topo.jpg;".$prefixo."admin/emails/img/header.jpg;".
			 $prefixo."admin/emails/img/img_cel_01.jpg;".$prefixo."admin/emails/img/bt_ativar_a_minha_conta.jpg;".
			 $prefixo."admin/emails/img/footer.jpg";
		
		

		
		$corpo = $this->get_file($layout);
		
		$corpo = str_replace('$usuario_novo', $usuario_novo, $corpo);	
		
		$link = HOST."/#p=login-confirmacao-email&token=".$uuid;
		//$link = "localhost/feedrenault_webapp/#p=login-confirmacao-email&token=".$uuid;
		
		$corpo = str_replace('$link', $link, $corpo);	
		
		$assunto="$usuario_novo, não perca mais nenhuma venda. Ative o seu Feed Renault!";
		

		//Envia E-mail	
		envia_email($email_destino,$assunto,$corpo, "IMG=".$IMG);
		
	}
	
	public function enviaReforcoAtivacao2($usuario_novo, $email_destino, $origem="web"){
			
		global $token;
		
		$prefixo="";

		$uuid = $token->generate(60 * 60 * 24 * 30, $this->id);

		$layout = $prefixo.'admin/emails/FV_DMSG_03.html';
			
		$IMG = $prefixo."admin/emails/img/topo.jpg;".$prefixo."admin/emails/img/header.jpg;".
			 $prefixo."admin/emails/img/img_cel_01.jpg;".$prefixo."admin/emails/img/bt_concluir_meu_cadastro.jpg;".
			 $prefixo."admin/emails/img/footer.jpg";
		
		

		
		$corpo = $this->get_file($layout);
		
		$corpo = str_replace('$usuario_novo', $usuario_novo, $corpo);	
		
		//$link = HOST."/#p=login-confirmacao-email&token=".$uuid;
		//$link = "localhost/feedrenault_webapp/#p=login-confirmacao-email&token=".$uuid;
		$link = "https://feedrenault.com.br/#p=login-confirmacao-email&token=".$uuid;
		
		$corpo = str_replace('$link', $link, $corpo);	
		
		$assunto="Falta só um clique para você ativar o Feed Renault!";
		

		//Envia E-mail	
		envia_email($email_destino,$assunto,$corpo, "IMG=".$IMG);
		
	}
	
	public function enviaIncentivoUsuario1($usuario_novo, $email_destino, $origem="web"){
			
		global $token;
		
		$prefixo="";

		//$uuid = $token->generate(60 * 60 * 24 * 30, $this->id);

		$layout = $prefixo.'admin/emails/FVeG_ER_01.html';
			
		$IMG = $prefixo."admin/emails/img/topo.jpg;".$prefixo."admin/emails/img/header.jpg;".
			 $prefixo."admin/emails/img/img_cel_01.jpg;".$prefixo."admin/emails/img/bt_quero_me_atualizar.jpg;".
			 $prefixo."admin/emails/img/footer.jpg";
		
		

		
		$corpo = $this->get_file($layout);
		
		$corpo = str_replace('$usuario_novo', $usuario_novo, $corpo);	

		$link = "https://www.feedrenault.com.br";
		
		$corpo = str_replace('$link', $link, $corpo);	
		
		//$assunto="Sentimos a sua falta!";
		$assunto="Veja o que você está perdendo no Feed Renault...";

		//Envia E-mail	
		envia_email($email_destino,$assunto,$corpo, "IMG=".$IMG);
		
	}
	
	public function enviaIncentivoUsuario2($usuario_novo, $email_destino, $origem="web"){
			
		global $token;
		
		$prefixo="";

		$layout = $prefixo.'admin/emails/FVeG_ER_02.html';
			
		$IMG = $prefixo."admin/emails/img/topo.jpg;".$prefixo."admin/emails/img/header.jpg;".
			 $prefixo."admin/emails/img/img_cel_01.jpg;".$prefixo."admin/emails/img/bt_quero_me_atualizar.jpg;".
			 $prefixo."admin/emails/img/footer.jpg";
		
		$corpo = $this->get_file($layout);
		
		$corpo = str_replace('$usuario_novo', $usuario_novo, $corpo);	

		$link = "https://www.feedrenault.com.br";
		
		$corpo = str_replace('$link', $link, $corpo);	
		
		//$assunto="Não deixe a sua conta ser bloqueada!";
		$assunto="Cuidado para não perder o seu acesso no Feed Renault!";

		//Envia E-mail	
		envia_email($email_destino,$assunto,$corpo, "IMG=".$IMG);
		
	}
	
	public function enviaContaBloqueada($usuario_novo, $email_destino, $origem="web"){
			
		global $token;
		
		$prefixo="";

		$layout = $prefixo.'admin/emails/FVeG_01.html';
			
		$IMG = $prefixo."admin/emails/img/topo.jpg;".$prefixo."admin/emails/img/header.jpg;".
			 $prefixo."admin/emails/img/img_cel_01.jpg;".
			 $prefixo."admin/emails/img/footer.jpg";
		
		$corpo = $this->get_file($layout);
		
		$corpo = str_replace('$usuario_novo', $usuario_novo, $corpo);
		
		//$assunto="A sua conta foi bloqueada!";
		$assunto="O seu acesso no Feed Renault foi bloqueado! Veja como desbloquear.";

		//Envia E-mail	
		envia_email($email_destino,$assunto,$corpo, "IMG=".$IMG);
		
	}
	
	public function enviaIncentivoReaticao($usuario_novo, $email_destino, $origem="web"){
			
		global $token;
		
		$prefixo="";

		$layout = $prefixo.'admin/emails/FVeG_02.html';
			
		$IMG = $prefixo."admin/emails/img/topo.jpg;".$prefixo."admin/emails/img/header.jpg;".
			 $prefixo."admin/emails/img/img_cel_01.jpg;".
			 $prefixo."admin/emails/img/footer.jpg";
		
		$corpo = $this->get_file($layout);
		
		$corpo = str_replace('$usuario_novo', $usuario_novo, $corpo);	
		
		$assunto="Olha tudo o que você está perdendo... reative agora!";

		//Envia E-mail	
		envia_email($email_destino,$assunto,$corpo, "IMG=".$IMG);
		
	}
	
	public function enviaConfirmacaoGerente($usuario_novo, $bir_usuario){
	
		global $db;
		
		$prefixo="../";
		
		$layout = $prefixo.'admin/emails/FG_RC_02.html';
		
		$corpo = $this->get_file($layout);
		
		//Primeiro nome do usuario		
		$nome_usuario = explode(' ', $usuario_novo);
		
		//Nome do usuario_novo				
		$corpo = str_replace('$nome_usuario', $nome_usuario['0'], $corpo);
		
		//link usuarios
		$link_gerentes= "https://feedrenault.com.br/admin/users.php";
		
		$corpo = str_replace('$link_gerentes', $link_gerentes, $corpo);
		
		$assunto="Cadastro liberado com sucesso!";
		
		//Imagens		
		$IMG = $prefixo."admin/emails/img/topo.jpg;".
			   $prefixo."admin/emails/img/header.jpg;".
			   $prefixo."admin/emails/img/img_cel_01.jpg;".
			   $prefixo."admin/emails/img/bt_verificar_todas_as_permissoes_pendentes.jpg;".
			   $prefixo."admin/emails/img/footer.jpg";

		$users_managers = $db->query("SELECT * from users where level=2 and status=1 and bir=$bir_usuario ");
		
		foreach($users_managers as &$user_manager) {
			
			//nome gerente
			$nome_gerente = explode(' ', $user_manager['name']);			
			
			$corpo = str_replace('$nome_gerente', $nome_gerente['0'], $corpo);

			//Envia E-mail	
			envia_email($user_manager['email'],$assunto,$corpo, "IMG=".$IMG);
			
		}

	}
	
	public function enviaRecusadoGerente($usuario_novo, $id_usuario, $bir_usuario){
	
		global $db;
		
		$prefixo="../";
		
		$layout = $prefixo.'admin/emails/FG_RC_03.html';
		
		$corpo = $this->get_file($layout);
		
		//Primeiro nome do usuario		
		$nome_usuario = explode(' ', $usuario_novo);
		
		//Nome do usuario_novo				
		$corpo = str_replace('$nome_usuario', $nome_usuario['0'], $corpo);
		
		//link usuarios
		//$link_gerentes= "https://feedrenault.com.br/admin/users.php";
		//$link = HOST."/admin/user_edit.php?id={$id_usuario}&accept=1";
		$link = "https://feedrenault.com.br/admin/user_edit.php?id={$id_usuario}&accept=1";
		$corpo = str_replace('$link', $link, $corpo);
		
		$assunto="Cadastro negado com sucesso.";
		
		//Imagens		
		$IMG = $prefixo."admin/emails/img/topo.jpg;".
			   $prefixo."admin/emails/img/header.jpg;".
			   $prefixo."admin/emails/img/img_cel_01.jpg;".
			   $prefixo."admin/emails/img/bt_reativar_cadastro.jpg;".
			   $prefixo."admin/emails/img/footer.jpg";

		$users_managers = $db->query("SELECT * from users where level=2 and status=1 and bir=$bir_usuario ");
		
		foreach($users_managers as &$user_manager) {
			
			//nome gerente
			$nome_gerente = explode(' ', $user_manager['name']);			
			
			$corpo = str_replace('$nome_gerente', $nome_gerente['0'], $corpo);

			//Envia E-mail	
			envia_email($user_manager['email'],$assunto,$corpo, "IMG=".$IMG);
			
		}

	}
	
}

?>