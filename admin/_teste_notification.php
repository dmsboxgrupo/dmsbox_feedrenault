<?php

// API

include('includes/common.php');

need_login();

$send_to = (int)get('user_id');

$users = $db->query("SELECT id, email 
					FROM `users` 
					WHERE `status`=1 and status_gerente=1 and status_email=1
					and id in (select devices.user from devices where devices.user=users.id)
					order by email ");

if (has('post')) {

	$text = get_post('text');
	
	if ($text) {
		
		include('../includes/push_notification.php');
			
		content_type('text/plain');
		
		// selecao usuarios com device
		$user_devices = empty(get_post('users')) ? "" : implode(",", get_post('users'));	
		$where = $user_devices ? "WHERE user IN ( {$user_devices} )" : "";
		
		$query = $db->query( "
			SELECT `user`, `device`, `os`
			FROM `devices`  
			$where 
			GROUP BY `device` 
			ORDER BY `id` DESC");
		
		$devices = array_column( $query, 'device' );
		$users = array_column( $query, 'user' );

		$pack_length = 1;
		$offset = 0;
		
		$send_success = 0;
		$send_error = 0;
		
		while($offset < count($devices)) {
			
			$devices_group = array_splice($devices, $offset, $pack_length);
			$users_group = array_splice($users, $offset, $pack_length);
			
			$offset += $pack_length;
		
			// print_r($devices_group); echo $text;

			$expoPushToken = 'ExponentPushToken[QFbyBWDTvQqZa6aAs_BqQq]'; // Substitua pelo token real
			$title = 'Feed Renault';
			$body = $text;
			
			$result = sendPushNotification($expoPushToken, $title, $body);
		
			//$result = push_notification( $devices_group, $text );

			
			if (isset($result['data']) && isset($result['data'][0]) && $result['data'][0]['status'] == 'ok') {
				
				$send_success++;
				
				//$user_data = $db->select_id('users', $users_group[0]);
				
			} else {
				
				$send_error++;

			}
			
		}
		
		echo "PUSH NOTIFICATION.\n\n";
		echo "- {$send_success} mensagens enviadas.\n";
		echo "- {$send_error} mensagens falharam no envio.\n";
		echo "- Mensagem enviada: {$text}\n\n";
		echo "* Use o navegador para voltar.";

		$push_id = $db->insert('push_notification', array(
			'devices' => implode(',', $devices),
			'text' => $text
		));
		
		//exit;

	}

//exit;

	redirect("users.php", array(
		'message' => 'Notificação enviada com sucesso.',
		'type' => 'success'
	));

}

// View

$webadm->set_page( array( 'name' => 'Enviar Notificação.' ) );

$webadm->add_parent( array( 'name' => 'Usuários', 'url' => 'users.php' ) );

$webadm->add_plugins('select2');

$webadm->start_panel();

?>
<!-- ============================================================== -->
<!-- Start Page Content -->
<form action="<?php echo "?post&send_to=$send_to"; ?>" method="post" enctype="multipart/form-data">
	<div class="card no-margin">
		<div class="card-header">
			<div class="row">
				<div class="col-md-11">					
					<input name="name" readonly type="text" class="form-control" value="Push Notification" />
				</div>
				<button id="save" type="submit" class="btn btn-info"><i class="fa fa-check m-r-10"></i>Enviar</button>
				<button id="loading" style="display:none" class="btn btn-warning">Enviando</button>
			</div>
		</div>
		<div class="card-body">
			
			<div class="form-group m-b-10">
				<label>Usuários - <span style='font-size:11px'>Caso o campo esteja vazio enviara para todos</span></label>
				<select id="users" class="form-control col-md-12 select2" name="users[]" multiple="multiple">
					<?php foreach($users as $user) {
						$sel = (strpos($content['users'], $user['id']) !== false)? "selected":"";
					?>
						<option <?php echo $sel ?> value="<?php echo html($user['id']); ?>" $sel ><?php echo html($user['email']); ?></option>
					<?php } ?>
				</select>
			</div>
			
			<div class="row">
				<div class="col-md-12">					
					<div class="form-group m-b-0">					
						<label>Mensagem</label>						
						<textarea class="form-control" id="text" name="text" style="height: 200px" placeholder="Digite a mesagem" ></textarea>
					</div>					
				</div>
			</div>
			
		</div>
	</div>
</form>

<script>
$(function() {
	
	$('#save').click(function() {
		
		$('#save').hide();
		$('#loading').show();

		//$('#text').prop('disabled', true);
		
	})
	
	$("#users").select2({
		placeholder: "Selecione o(s) Usuário(s)"
	});
	
});
</script>
<!-- ============================================================== -->
<!-- End Page Content -->
<!-- ============================================================== -->
<?php 

// Finaliza HTML

$webadm->end_panel();

?>