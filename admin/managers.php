<?php

// API

include('includes/common.php');
$_SESSION['type']=$_SESSION['type'];
// Codigo da pagina

need_login();
/*
1 = Master
2 = Gerente
3 = Usuário do app ( Vendedor )
*/
$users = $db->query("SELECT *, 
	-- Desativado
		(case WHEN status=0 THEN 0 
	-- Ativado
			when status_gerente=1 and status_email=1 THEN 2 
	-- Convite Enviado
			when status_email=0 THEN 3 
	-- Confirmar Acesso
			when status_gerente is null THEN 4 
	-- Recusado
			when status_gerente=0 THEN 1 else 0 end) as ordem 
			FROM `users` 
			WHERE level=2 and
			(origin=1 or (origin=2 and status_email=1))
			order by ordem desc	");

// Inicia HTML

$webadm->set_page( array( 'name' => 'Gerentes' ) );
//$webadm->add_parent( array( 'name' => 'Usuários' ) );

//$webadm->add_button( array( 'name' => 'Tela Abertura', 'icon' => 'mdi mdi-bulletin-board', 'url' => "opening_screen_edit.php" ) );

$webadm->add_button( array( 'name' => 'Relatório Usuários', 'icon' => 'mdi mdi-format-list-bulleted', 'url' => "user_report.php" ) );

$webadm->add_button( array( 'name' => 'Categorias Usuários', 'icon' => 'mdi mdi-account-card-details', 'url' => "user_categories.php" ) );

$webadm->add_button( array( 'name' => 'Configurações Email', 'icon' => 'mdi mdi-email', 'url' => "mail_config_edit.php" ) );

$webadm->add_button( array( 'name' => 'Novo Gerente', 'icon' => 'mdi mdi-account', 'url' => 'manager_edit.php' ) );

$webadm->add_plugins('datatables', 'sweetalert');
$webadm->start_panel();

?>
<!-- ============================================================== -->
<!-- Start Page Content -->
<!-- ============================================================== -->

<?php if ( $_SESSION['type']=='success' ) { $_SESSION['type']='';?>
	<div id="success-alert" class="alert alert-success">
	<?php echo $_SESSION['message']; $_SESSION['message']=''; ?>
	<button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">×</span></button>
	</div>
<?php } ?>


<div class="card no-margin">
	<div class="card-body">
		<div class="table-responsive">
			<table id="contents" class="display pageResize nowrap table table-striped table-bordered"><!--class="table-hover"-->
				<thead>
					<tr>
						<th>Status</th>
						<th>Nome</th>
						<th>BIR</th>
						<th>Email</th>
						<th>Ação</th>
					</tr>
				</thead>
				<tbody>
					<?php
					foreach($users as &$user) {

						extract( $db->content($user, 'managers'), EXTR_PREFIX_ALL, 'i' );
					?>
					<tr content-id="<?php echo $i_id; ?>" content-name="<?php echo $i_name; ?>">
						<td class="dt-nowrap text-nowrap align-middle text-center">
								<h5>
									<?php 									
										if($i_status == "1"){
											
											echo"<div class='text-megna'>ATIVADO</div>";												
											
										} else {
										
											echo"<div class='text-danger'>DESATIVADO</div>";
											
										}
										
									?>
								</h5>
						</td>
						<td class="dt-nowrap text-nowrap align-middle">
							<a href="<?php echo $i_edit_url; ?>">
								<h4><?php echo html( $i_name ); ?></h4>
							</a>
						</td>
						<td class="dt-nowrap text-nowrap align-middle">
							<a href="<?php echo $i_edit_url; ?>">
								<h4><?php echo html( $i_bir ); ?></h4>
							</a>
						</td>
						<td class="dt-nowrap text-nowrap align-middle">
							<a href="<?php echo $i_edit_url; ?>">
								<h4><?php echo html( $i_email ); ?></h4>
							</a>
						</td>
						<td class="text-center align-middle">
							<!--<div class="btn-group btn-group-justified m-r-10">
								<a href="?php echo $i_edit_url; ?>" class="btn btn-primary text-white btn-sm" data-toggle="tooltip" title="Editar">
									<i class="mdi mdi-lead-pencil"></i>
								</a>
							</div>-->
							<div class="btn-group btn-group-justified">
								<?php if ($i_status_email && $i_status_gerente) { if ($i_status) { ?>
								<a class="btn btn-danger text-white btn-sm toggle-user" data-toggle="tooltip" title="Desativar Gerente">
									<i class="fas fa-lock"></i>
								</a>
								<?php } else { ?>
								<a class="btn btn-info text-white btn-sm toggle-user" data-toggle="tooltip" title="Ativar Gerente">
									<i class="fas fa-lock-open"></i>
								</a>
								<?php }} ?>
							</div>
						</td>
					</tr>
					<?php } ?>
				</tbody>
			</table>
		</div>
	</div>
</div>
<script>
$(function() {
		
	$('#contents').DataTable({
		language: { url: "<?php echo url( 'admin/plugins/datatables-language/Portuguese-Brasil.json' ) ?>" },
		pageLength: 50,
		lengthChange: false,
		scrollY: 'calc(100vh - 460px)',
		columnDefs: [
			{ width: "1px", targets: [0,4] },
		],
		ordering: false	
	});
		
	$(document).ready(function() {
	  $("#success-alert").fadeTo(5000, 500).slideUp(500, function() {
		  $("#success-alert").slideUp(500);
		});	  
	});
	
	$('.toggle-user').click(function(){
		
		var contentId = $(this).closest('tr').attr( "content-id" );
		
		if($(this).hasClass( "btn-danger" )){
			swal({   
				title: "Deseja mesmo desativar esse Gerente?",
				type: "warning",   
				showCancelButton: true,   
				confirmButtonColor: "#DD6B55",   
				confirmButtonText: "Sim, desativar!",   
				closeOnConfirm: false,
				cancelButtonText: "Cancelar"
			}, function(){
				
				window.location = "<?php echo url('admin/manager_edit.php'); ?>?id=" + contentId + "&toggle_active";
				
			});
		} else
			window.location = "<?php echo url('admin/manager_edit.php'); ?>?id=" + contentId + "&toggle_active";
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