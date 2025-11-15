<?php

// API

include('includes/common.php');

// Codigo da pagina

need_login();
//FROM `posts` WHERE category not in ( 2, 4, 5, 6 ) 
$posts = $db->query("SELECT *,
(SELECT extension FROM `uploads` WHERE uploads.id=posts.image) as img
FROM `posts` WHERE category in ( 3 ) 
and status=1 
ORDER by date DESC
");

// Inicia HTML
//$webadm->add_button( array( 'name' => 'Gerenciar Banners', 'icon' => 'mdi mdi-bulletin-board', 'url' => "banners.php" ) );
$webadm->add_button( array( 'name' => 'Gerenciar Banners', 'icon' => 'mdi mdi-bulletin-board', 'url' => "banners.php?category=3" ) );

$webadm->set_page( array( 'name' => 'Notícias' ) );

$webadm->add_button( array( 'name' => 'Nova Notícia', 'icon' => 'mdi mdi-format-list-bulleted', 'url' => 'new_edit.php' ) );

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
						<th>Data</th>
						<th>Texto</th>
						<th>Imagem</th>
						<th>Ação</th>
					</tr>
				</thead>
				<tbody>
					<?php
					foreach($posts as &$post) {

						extract( $db->content($post, 'news'), EXTR_PREFIX_ALL, 'i' );
					?>
					<tr content-id="<?php echo $i_id; ?>" >						
						<td class="dt-nowrap text-nowrap align-middle">
							<a href="<?php echo $i_edit_url; ?>">
								<span style="color:<?php echo $i_date_color ?>"><?php echo $i_date_str; ?></span>
							</a>
						</td>
						
						<td class="dt-nowrap text-nowrap align-middle">
							<a href="<?php echo $i_edit_url; ?>">
								<h4><?php echo htmltotext($i_text, 50); ?></h4>
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
								<a class="btn btn-danger text-white btn-sm toggle-new" data-toggle="tooltip" title="Remover Notícia">
									<i class="mdi mdi-close"></i>
								</a>
								<?php } else { ?>
								<a class="btn btn-info text-white btn-sm toggle-new" data-toggle="tooltip" title="Mostrar Notícia">
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
			{ width: "1px", targets: [0,2,3] },
		],
		ordering: false
	});
		
	$(document).ready(function() {
	  $("#success-alert").fadeTo(5000, 500).slideUp(500, function() {
		  $("#success-alert").slideUp(500);
		});	  
	});
	
	$('.toggle-new').click(function(){
		
		var contentId = $(this).closest('tr').attr( "content-id" );
		
		if($(this).hasClass( "btn-danger" )){
			
			swal({   
				title: "Deseja mesmo remover essa Notícia?",
				type: "warning",   
				showCancelButton: true,   
				confirmButtonColor: "#DD6B55",   
				confirmButtonText: "Sim, remover!",   
				closeOnConfirm: false,
				cancelButtonText: "Cancelar"
			}, function(){
				
				window.location = "<?php echo url('admin/new_edit.php'); ?>?id=" + contentId + "&toggle_active";
				
			});
			
		} else {
			
			window.location = "<?php echo url('admin/new_edit.php'); ?>?id=" + contentId + "&toggle_active";
			
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