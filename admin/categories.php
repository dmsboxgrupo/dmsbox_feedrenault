<?php

// API

include('includes/common.php');

// Codigo da pagina

need_login();

$categories = $db->query("SELECT * FROM `categories`");

// Inicia HTML

$webadm->set_page( array( 'name' => 'Categorias' ) );

$webadm->add_button( array( 'name' => 'Nova Categoria', 'icon' => 'mdi mdi-image-filter-none', 'url' => 'category_edit.php' ) );

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
						<th>Ação</th>
					</tr>
				</thead>
				<tbody>
					<?php
					foreach($categories as &$category) {

						extract( $db->content($category, 'categories'), EXTR_PREFIX_ALL, 'i' );
					?>
					<tr content-id="<?php echo $i_id; ?>" content-name="<?php echo $i_name; ?>">
						<td class="dt-nowrap text-nowrap align-middle text-center">
								<h5>
									<?php 
										//echo $i_status ? "<div class='text-muted'>Ativado</div>" : "<div class='text-danger'>Desativado</div>"; 
										//ECHO"TESTE=".$i_status;
										if($i_status=="0")
											echo"<div class='text-danger'>DESATIVADO</div>";
										elseif($i_status=="1")
											echo"<div class='text-megna'>ATIVADO</div>";
									?>
								</h5>
						</td>
						<td class="dt-nowrap text-nowrap align-middle">
							<a href="<?php echo $i_edit_url; ?>">
								<h4><?php echo html( $i_name ); ?></h4>
							</a>
						</td>
						
						<td class="text-center align-middle">
							<div class="btn-group btn-group-justified">
								<?php if ($i_status) { ?>
								<a class="btn btn-danger text-white btn-sm toggle-category" data-toggle="tooltip" title="Remover categoria">
									<i class="fas fa-eye-slash"></i>
								</a>
								<?php } else { ?>
								<a class="btn btn-info text-white btn-sm toggle-category" data-toggle="tooltip" title="Ativar categoria">
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
			{ width: "1px", targets: [0,2] },
		],
		order: [[ 0, "asc" ], [ 1, "asc" ]]
	});
		
	$(document).ready(function() {
	  $("#success-alert").fadeTo(5000, 500).slideUp(500, function() {
		  $("#success-alert").slideUp(500);
		});	  
	});
	
	$('.toggle-category').click(function(){
		
		var contentId = $(this).closest('tr').attr( "content-id" );
		
		if($(this).hasClass( "btn-danger" )){
			swal({   
				title: "Deseja mesmo remover essa categoria?",
				type: "warning",   
				showCancelButton: true,   
				confirmButtonColor: "#DD6B55",   
				confirmButtonText: "Sim, remover!",   
				closeOnConfirm: false,
				cancelButtonText: "Cancelar"
			}, function(){
				
				window.location = "<?php echo url('admin/category_edit.php'); ?>?id=" + contentId + "&toggle_active";
				
			});
		} else
			window.location = "<?php echo url('admin/category_edit.php'); ?>?id=" + contentId + "&toggle_active";
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