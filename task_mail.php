<?php

// API
	
include('includes/common.php');
//include('includes/socials.php');
include('includes/logtxt.php');

// 1 - Atualizar last login


/*

	adicionar flags na tabela users:
	
	mail_team_incentive - começa em 0 e atuliza para 1, 2 e 3
	
	(a cada X dias do gerente cadastrado)
	Incentivo Equipe 1
	Incentivo Equipe 2
	Incentivo Equipe 3
	
	mail_reminder_authorize - começa em 0 e atuliza para 1 e 2
	
	(para gerente lembrar de autorizar/recuzar)
	Email Lembrete 1
	Email Lembrete 2
	
	mail_activation_booster - começa em 0 e atuliza para 1 e 2
	
	manda email Reforço Ativação 1
	manda email Reforço Ativação 2
	
	mail_user_incentive - começa em 0 e atuliza para 1, 2, 3 e 4
	
	(para Gerentes e Vendedores)
	manda Email incentivo 1 (após 30 dias)
	manda Email incentivo 2 (após 30 + 50 = 80 dias)
	manda Email Conta Bloqueada (após 30 + 50 + 61 = 141 dias)
	manda Email incentivo Reativação (após 30 + 50 + 61 + 15 = 156 dias)
	
	mail_team_incentive, mail_reminder_authorize, mail_activation_booster, mail_user_incentive
*/


set_time_limit ( 60 * 5 );

header("Access-Control-Allow-Origin: *");

content_type('text/plain');

function mail_managers_sellers( $level ) {

	global $db;
	
	$where = "";

	if( $level != "all" ) $where = " and level = $level ";
	
	$data_base = '2023-07-14';
	
	// vendedor incluido pelo app 3673
	// gerente 2054
	$users = $db->query( "
					SELECT 
					 (SELECT email from users b where b.level=2 and b.status=1 and b.bir=a.bir limit 1) as email_gerente,
					id, name, email, password, bir, origin, level, user_category, mail_team_incentive, mail_reminder_authorize, mail_activation_booster, mail_user_incentive,
						status,
						status_email,
						status_gerente,
						date,
						coalesce(DATEDIFF(CURDATE(), GREATEST(last_login, '$data_base')), 0) as dias_sem_logar, 
						coalesce(DATEDIFF(CURDATE(), GREATEST('$data_base', date)), 0) as dias_cadastrado 
					FROM `users` a 
					WHERE  
					  
					status=1 and level in ( 2, 3 ) $where
				" );

	foreach($users as &$user) {
//print_r($user); echo "<br/><br/><br/>";	
		$user_db = new User($user);
//print_r($user); die();	
		$suser = implode(', ', $user);
		
		//txtLog('_taskmail_','/----------------------------------------------------------------------------------------------------------/'); 
		//txtLog('_taskmail_',$suser); 
		
		//Gerentes level=2
		
		/*
			emails para o gerente:

			dias_cadastrado>30 - manda Email Incentivo 1
			dias_cadastrado>60 - manda Email Incentivo 2
			dias_cadastrado>90 - manda Email Incentivo 3

			emails para o gerente - Aceita/Recusa:

			se status_email=1 e status_gerente=null e DATEDIFF(CURDATE(), data_cadastro_user) = 2  - manda Email Lembrete 1 
			se status_email=1 e status_gerente=null e DATEDIFF(CURDATE(), data_cadastro_user) > 4  - manda Email Lembrete 2
		*/
		//echo $user['dias_cadastrado']; 
		if( $user['level'] == 2 ){
			
			if( $user['dias_cadastrado'] >= 90 && $user['mail_team_incentive'] < 3 ){
				//echo "Entrou 90"; //die();
				txtLog("_".$data_base."_taskmail_","Email para Gerente - manda Incentivo Equipe 3  - {$user['id']} - {$user['name']} - {$user['email']}");
				$user_db->mail_team_incentive ( 2 );
				
			}else{
				
				if( $user['dias_cadastrado'] >= 60 && $user['mail_team_incentive'] < 2){
					//echo "Entrou 60"; //die();
					txtLog("_".$data_base."_taskmail_","Email para Gerente - manda Incentivo Equipe 2 - {$user['id']} - {$user['name']} - {$user['email']}");
					$user_db->mail_team_incentive ( 1 );
				
				}else{
					
					if( $user['dias_cadastrado'] >= 30 && $user['mail_team_incentive'] < 1){
						//echo "Entrou 30"; //die();
						txtLog("_".$data_base."_taskmail_","Email para Gerente - manda Incentivo Equipe 1 - {$user['id']} - {$user['name']} - {$user['email']}");
						$user_db->mail_team_incentive ( 0 );
						
					}
					
				}
				
			}
			
		}
		
		if( $user['level'] == 3 && $user['status_email'] == 1 && $user['status_gerente'] != 1){
			
			if( $user['dias_cadastrado'] >= 4 && $user['mail_reminder_authorize'] < 2 ){
				
				txtLog("_".$data_base."_taskmail_","Email para Gerente - Lembrete 2 - {$user['id']} - {$user['name']} - {$user['email']} - {$user['email_gerente']} ");
				
				$user_db->mail_reminder_authorize ( 1 );
				
				//txtLog('_taskmail_','Email para Gerente (pegar pelo bir) - se status_email=1 e status_gerente=null e DATEDIFF(CURDATE(), data_cadastro_user) > 4  - manda Email Lembrete 2');
				//Email para Gerente (pegar pelo bir)
				//se status_email=1 e status_gerente=null e DATEDIFF(CURDATE(), data_cadastro_user) > 4  - manda Email Lembrete 2
				
			}else{
				
				if( $user['dias_cadastrado'] >= 2 && $user['mail_reminder_authorize'] < 1){
				
					txtLog("_".$data_base."_taskmail_","Email para Gerente - Lembrete 1 - {$user['id']} - {$user['name']} - {$user['email']} - {$user['email_gerente']} ");
					
					$user_db->mail_reminder_authorize ( 0 );
					
					//txtLog('_taskmail_','Email para Gerente (pegar pelo bir) - se status_email=1 e status_gerente=null e DATEDIFF(CURDATE(), data_cadastro_user) = 2  - manda Email Lembrete 1');
					//Email para Gerente (pegar pelo bir)
					//se status_email=1 e status_gerente=null e DATEDIFF(CURDATE(), data_cadastro_user) = 2  - manda Email Lembrete 1 
				
				}
				
			}
			
			
			
		}else{
		
			//Vendedores level=3
			
			/*Emails Usuario - Cadastrado via painel

				se status_email=0 e DATEDIFF(CURDATE(), data_cadastro_user) = 3 - manda email Reforço Ativação
				se status_email=0 e DATEDIFF(CURDATE(), data_cadastro_user) = 6 - manda email Reforço Ativação 2*/
			
			if( $user['level'] == 3 && $user['status_email'] == 0 && $user['status_gerente'] != 1 ){
				
				if( $user['dias_cadastrado'] >= 6 && $user['mail_activation_booster'] < 2  ){
				
					txtLog("_".$data_base."_taskmail_","Email para Vendedor - Reforco Ativacao 2 - {$user['id']} - {$user['name']} - {$user['email']}");
				
					$user_db->mail_activation_booster ( 1 );
					
					//txtLog('_taskmail_','Email para Vendedor - se status_email=0 e DATEDIFF(CURDATE(), data_cadastro_user) = 6 - manda email Reforço Ativação 2');
					//se status_email=0 e DATEDIFF(CURDATE(), data_cadastro_user) = 6 - manda email Reforço Ativação 2*/
					
				}else{
				
					if( $user['dias_cadastrado'] >= 3 && $user['mail_activation_booster'] < 1 ){
					
						$user_db->mail_activation_booster ( 0 );
						
						txtLog("_".$data_base."_taskmail_","Email para Vendedor - Reforco Ativacao 1 - {$user['id']} - {$user['name']} - {$user['email']}");
						
						//txtLog('_taskmail_','Email para Vendedor - se status_email=0 e DATEDIFF(CURDATE(), data_cadastro_user) = 3 - manda email Reforço Ativação');
						//se status_email=0 e DATEDIFF(CURDATE(), data_cadastro_user) = 3 - manda email Reforço Ativação
					
					}
					
					
				}		
				
			}
		
		}
	
		//Todos os usuarios
		
		/*
			Todos Usuarios - Retenção de interesse

			se status=1 e status_email=1 e status_gerente=1 e dias_sem_logar > 30  - manda Email incentivo 1
			se status=1 e status_email=1 e status_gerente=1 e dias_sem_logar > 50  - manda Email incentivo 2
			se status=1 e status_email=1 e status_gerente=1 e dias_sem_logar > 61  - manda Email Conta Bloqueada
			se status=1 e status_email=1 e status_gerente=1 e dias_sem_logar > 76  - manda Email incentivo Reativação
		*/
		
		//echo "Dias sem logar= ".$user['dias_sem_logar'];
		
		if( $user['status'] == 1 && $user['status_email'] == 1 && $user['status_gerente'] == 1 ){
		
			if( $user['dias_sem_logar'] >= 156 && $user['mail_user_incentive'] < 4  ){
				//echo "email 4 "; 
				
				$user_db->mail_user_incentive ( 3 );
				
				txtLog("_".$data_base."_taskmail_","Email para Todos - envia envia Incentivo Reaticao - {$user['id']} - {$user['name']} - {$user['email']}");
				
				//txtLog('_taskmail_','Email para Todos - manda Email incentivo Reativação >= 156');
				//manda Email incentivo Reativação
				
			}else {
				
				if( $user['dias_sem_logar'] >= 141 && $user['mail_user_incentive'] < 3  ){
					//echo "email 3 "; 
					
					$user_db->mail_user_incentive ( 2 );
					
					txtLog("_".$data_base."_taskmail_","Email para Todos - envia Conta Bloqueada - {$user['id']} - {$user['name']} - {$user['email']}");
					
					//txtLog('_taskmail_','Email para Todos - manda Email Conta Bloqueada >= 141');
					//manda Email Conta Bloqueada
					
				} else {
					
					if( $user['dias_sem_logar'] >= 80 && $user['mail_user_incentive'] < 2  ){
						//echo "email 2 "; 
						
						$user_db->mail_user_incentive ( 1 );
						
						txtLog("_".$data_base."_taskmail_","Email para Todos - Incentivo Usuario 2 - {$user['id']} - {$user['name']} - {$user['email']}");
						
						//txtLog('_taskmail_','Email para Todos - manda Email incentivo 2 >= 80');
						//manda Email incentivo 2
						
						
					} else {	
					
						if( $user['dias_sem_logar'] >= 30 && $user['mail_user_incentive'] < 1  ){
							//echo "email 1 "; 
							
							$user_db->mail_user_incentive ( 0 );
							
							txtLog("_".$data_base."_taskmail_","Email para Todos - Incentivo Usuario - {$user['id']} - {$user['name']} - {$user['email']}");
							
							//txtLog('_taskmail_','Email para Todos - manda Email incentivo 1 >= 30');
							//manda Email incentivo 1
						
						
						}
					
					}
				
				
				}
				
			}
			
		}

	}
	
}

// Inicia API Externa (JSON)

$q = get_request('q');

$json = array();

//inactivate_user();

switch($q) {

	case 'gerentes':
		
		mail_managers_sellers( 2 );
		
		break;
	
	case 'vendedores':

		mail_managers_sellers( 3 );
		
		break;
	
	case 'all':
	
		mail_managers_sellers( 2 );
		mail_managers_sellers( 3 );
		
		break;
	
}

// print

echo json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

?>
