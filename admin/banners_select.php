<?php

// API

include('includes/common.php');

// Codigo da pagina

need_login();

$post_banners = array_filter($list_categories,
	function($k) {
		return(	($k['parent']=='post' && $k['name']!='Stories')
				|| $k['parent']=='whats_gallery'
				|| $k['parent']=='campaign');
	}
);


// Inicia HTML

$webadm->set_page( array( 'name' => 'Seleciona Categoria Banners' ) );
$webadm->add_parent( array( 'name' => 'Postagens', 'url' => 'posts.php' ) );
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
					</tr>
				</thead>
				<tbody>
					<?php
					foreach($post_banners as &$post_banner) {

						//extract( $db->content($post_banner, 'vehicles'), EXTR_PREFIX_ALL, 'i' );
					?>
					<tr content-id="<?php echo $i_id; ?>" >
						<td class="dt-nowrap text-nowrap align-middle">
						
							<a href="banners.php?category=<?php echo $post_banner['id']; ?>">
								<h4><?php echo html( $post_banner['name'] ); ?></h4>
							</a>
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
			{ width: "1px", targets: [0] },
		],
		order: [ 0, "desc" ]
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