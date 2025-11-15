<?php

// API

include('includes/common.php');

// Codigo da pagina

$redirect = get('redirect');
if($redirect=='') $redirect="users.php";
else $redirect = str_replace('&', '%26', $redirect);

$error = '';

if (has('logout')) {
	
	$useradm->logout();
	
	redirect('signin.php');
	
} elseif (has('post')) {
	
	if ($useradm->login(get_post('username'), get_post('password'))) {
		$redirect = str_replace('%26', '&', $redirect);
		redirect($redirect);
		
	} else {
		
		$error = 'Nome de usuário ou senha inválida.';
		
	}
	
} elseif ($useradm->logged()) {
	
	redirect($redirect);	
	
}

// Inicia HTML

$webadm->start();

?>
<!-- ============================================================== -->
<!-- Start Page Content -->
<!-- ============================================================== -->
<style>
.box-title img {
    height: 160px;
    padding: 10px;
}
.login-register {
	background: #fbfbfb;
}
.btn-default {
	border-radius: 20px;
	padding: 10px 55px;
}
.card-no-border .card {
	border-radius: 20px;
}
</style>
<div class="login-register">
	<div class="login-box card">
		<div class="card-body">
			<form action="signin.php?post<?php if($redirect!="")echo "&redirect={$redirect}&post"; ?>" method="post" class="form-horizontal form-material">
				<h3 class="box-title text-center">
					<img width="103px" src="img/R_RENAULT_EMBLEM_RGB_Positive_v1.svg"></img>
					<p style="color:#000" class="text-center"><b class="m-r-5">FEED</b> RENAULT</p>
				</h3>
				<?php if ( $error ) { ?>
				<div class="alert alert-danger">
					<?php echo html( $error ); ?>
					<button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">×</span></button>
				</div>
				<?php } ?>
				<div class="form-group m-t-30">
					<div class="col-xs-12">
						<input class="form-control" name="username" type="text" required="" placeholder="Usuário">
					</div>
				</div>
				<div class="form-group">
					<div class="col-xs-12">
						<input class="form-control" name="password" type="password" required="" placeholder="Senha">
					</div>
				</div>
				<div class="form-group text-center m-t-40 mb-2" style="margin-top: 50px">
					<div class="col-xs-12">
						<button class="btn btn-default btn-md btn-block text-uppercase waves-effect waves-light" type="submit">Entrar</button>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>
<!-- ============================================================== -->
<!-- End Page Content -->
<!-- ============================================================== -->
<?php 

// Finaliza HTML

$webadm->end();

?>