<?php

// API

include('includes/common.php');
$_SESSION['type']=$_SESSION['type'];
// Codigo da pagina

need_login();

$user_categories = $db->query("SELECT *
			FROM `user_categories` 
			WHERE status=1");

// Inicia HTML

$webadm->set_page( array( 'name' => 'Categorias Usuários' ) );

$webadm->add_parent( array( 'name' => 'Gerentes', 'url' => 'managers.php' ) );

$webadm->add_button( array( 'name' => 'Nova Categoria', 'icon' => 'mdi mdi-account-card-details', 'url' => 'user_category_edit.php' ) );

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
						<th>Nome</th>
						<th>Ação</th>
					</tr>
				</thead>
				<tbody>
					<?php
					foreach($user_categories as &$user_category) {

						extract( $db->content($user_category, 'user_categories'), EXTR_PREFIX_ALL, 'i' );
					?>
					<tr content-id="<?php echo $i_id; ?>" content-name="<?php echo $i_name; ?>">
						
						<td class="dt-nowrap text-nowrap align-middle">
							<a href="<?php echo $i_edit_url; ?>">
								<h4><?php echo html( $i_name ); ?></h4>
							</a>
						</td>

						<td class="text-center align-middle">
							<!--<div class="btn-group btn-group-justified m-r-10">
								<a href="?php echo $i_edit_url; ?>" class="btn btn-primary text-white btn-sm" data-toggle="tooltip" title="Editar">
									<i class="mdi mdi-lead-pencil"></i>
								</a>
							</div>-->
							<div class="btn-group btn-group-justified">
								<?php if ($i_status) { ?>
								<a class="btn btn-danger text-white btn-sm toggle-user" data-toggle="tooltip" title="Desativar Categoira">
									<i class="fas fa-lock"></i>
								</a>
								<?php } else { ?>
								<a class="btn btn-info text-white btn-sm toggle-user" data-toggle="tooltip" title="Ativar Categoira">
									<i class="fas fa-lock-open"></i>
								</a>
								<?php } ?>
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
			{ width: "1px", targets: [1] },
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
				title: "Deseja mesmo desativar essa Categoira?",
				type: "warning",   
				showCancelButton: true,   
				confirmButtonColor: "#DD6B55",   
				confirmButtonText: "Sim, desativar!",   
				closeOnConfirm: false,
				cancelButtonText: "Cancelar"
			}, function(){
				
				window.location = "<?php echo url('admin/user_category_edit.php'); ?>?id=" + contentId + "&toggle_active";
				
			});
		} else
			window.location = "<?php echo url('admin/user_category_edit.php'); ?>?id=" + contentId + "&toggle_active";
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