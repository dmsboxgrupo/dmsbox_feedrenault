<?php

// API

include('includes/common.php');
if (!isset($_SESSION['type'])) $_SESSION['type']='';

// Codigo da pagina

need_login();

$content_id = (int)get('content_id');

$manager_user_logado = $useradm->get_property('id');
$manager = ($useradm->is_level(LEVEL_MASTER))? "" : "and (user=$manager_user_logado)";

$versions = $db->query("SELECT * FROM `versions` where status=1 $manager");

// Inicia HTML

$webadm->set_page( array( 'name' => 'Versões' ) );

$webadm->add_parent( array( 'name' => 'Galerias WhatsApp', 'url' => 'whatsapp_galeries.php' ) );	
$webadm->add_parent( array( 'name' => 'Galeria WhatsApp', 'url' => "whatsapp_gallery_edit.php?id=$content_id" ) );

$webadm->add_button( array( 'name' => 'Nova Versão', 'icon' => 'mdi mdi-buffer m-r-5', 'url' => "version_edit.php?content_id=$content_id" ) );

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
					foreach($versions as &$version) {

						extract( $db->content($version, 'versions'), EXTR_PREFIX_ALL, 'i' );
					?>
					<tr content-id="<?php echo $i_id; ?>" >						
						
						<td class="dt-nowrap text-nowrap align-middle">
							<a href="<?php echo $i_edit_url."&content_id=$content_id"; ?>">
								<h4><?php echo htmltotext($i_name, 35); ?></h4>
							</a>
						</td>
						<td class="text-center align-middle">
							<div class="btn-group btn-group-justified">
								<?php if ($i_status) { ?>
								<a class="btn btn-danger text-white btn-sm toggle-version" data-toggle="tooltip" title="Remover Versão">
									<i class="mdi mdi-close"></i>
								</a>
								<?php } else { ?>
								<a class="btn btn-info text-white btn-sm toggle-version" data-toggle="tooltip" title="Ativar Versão">
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
			{ width: "1px", targets: [1] },
		],
		ordering: false	
	});
		
	$(document).ready(function() {
	  $("#success-alert").fadeTo(5000, 500).slideUp(500, function() {
		  $("#success-alert").slideUp(500);
		});	  
	});
	
	$('.toggle-version').click(function(){
		
		var playerId = $(this).closest('tr').attr( "player-id" );
		var contentId = $(this).closest('tr').attr( "content-id" );
		
		if($(this).hasClass( "btn-danger" )){
			
			swal({   
				title: "Deseja mesmo remover essa Versão?",
				type: "warning",   
				showCancelButton: true,   
				confirmButtonColor: "#DD6B55",   
				confirmButtonText: "Sim, remover!",   
				closeOnConfirm: false,
				cancelButtonText: "Cancelar"
			}, function(){
				
				window.location = "<?php echo url('version_edit.php'); ?>?id=" + contentId + "&toggle_active";
				
			});
			
		} else {
			
			window.location = "<?php echo url('version_edit.php'); ?>?id=" + contentId + "&toggle_active";
			
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