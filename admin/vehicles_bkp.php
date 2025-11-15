<?php

// API

include('includes/common.php');

// Codigo da pagina

need_login();
//SELECT name FROM `uploads` WHERE id=52
$vehicles = $db->query("SELECT *,
(SELECT extension FROM `uploads` WHERE uploads.id=vehicles.image) as img
FROM `vehicles` WHERE status=1
order by date desc
");

// Inicia HTML

$webadm->set_page( array( 'name' => 'Veículos' ) );

$webadm->add_button( array( 'name' => 'Novo Veículo', 'icon' => 'mdi mdi-car', 'url' => 'vehicle_edit.php' ) );

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
						<th>Versão</th>
						<th>Imagem</th>
						<th>Ação</th>
					</tr>
				</thead>
				<tbody>
					<?php
					foreach($vehicles as &$vehicle) {

						extract( $db->content($vehicle, 'vehicles'), EXTR_PREFIX_ALL, 'i' );
					?>
					<tr content-id="<?php echo $i_id; ?>" >
						<td class="dt-nowrap text-nowrap align-middle">
							<a href="<?php echo $i_edit_url; ?>">
								<h4><?php echo html( $i_name ); ?></h4>
							</a>
						</td>						
						<td class="dt-nowrap text-nowrap align-middle">
							<a href="<?php echo $i_edit_url; ?>">
								<h4><?php echo html( $i_version ); ?></h4>
							</a>
						</td>
						
						<td class="dt-nowrap text-nowrap align-middle" align="middle">
						<?php if($i_image>0){if($i_img=='mp4'){ ?>
							<video src="<?php echo url("/uploads/". $i_image.".".$i_img); ?>" style="max-width:80px; max-height:80px;" />
						<?php } else{ ?>
							<img src="<?php echo url("/uploads/". $i_image.".".$i_img); ?>" style="max-width:80px; max-height:80px;" />
						<?php }} ?>
							
						</td>
						<td class="text-center align-middle">
							<div class="btn-group btn-group-justified">
								<?php if ($i_status) { ?>
								<a class="btn btn-danger text-white btn-sm toggle-vehicle" data-toggle="tooltip" title="Remover veículo">
									<i class="mdi mdi-close"></i>
								</a>
								<?php } else { ?>
								<a class="btn btn-info text-white btn-sm toggle-vehicle" data-toggle="tooltip" title="Ativar veículo">
									<i class="fas fa-eye"></i>
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
			{ width: "1px", targets: [1,2,3] },
		],
		ordering: false
	});
		
	$(document).ready(function() {
	  $("#success-alert").fadeTo(5000, 500).slideUp(500, function() {
		  $("#success-alert").slideUp(500);
		});	  
	});
	
	$('.toggle-vehicle').click(function(){
		
		var contentId = $(this).closest('tr').attr( "content-id" );
		
		if($(this).hasClass( "btn-danger" )){
			
			swal({   
				title: "Deseja mesmo remover esse Veículo?",
				type: "warning",   
				showCancelButton: true,   
				confirmButtonColor: "#DD6B55",   
				confirmButtonText: "Sim, remover!",   
				closeOnConfirm: false,
				cancelButtonText: "Cancelar"
			}, function(){
				
				window.location = "<?php echo url('admin/vehicle_edit.php'); ?>?id=" + contentId + "&toggle_active";
				
			});
			
		} else {
			
			window.location = "<?php echo url('admin/vehicle_edit.php'); ?>?id=" + contentId + "&toggle_active";
			
		}
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