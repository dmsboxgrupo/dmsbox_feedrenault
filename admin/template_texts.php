<?php

// API

include('includes/common.php');

need_login();

// Codigo da pagina

$type = get('type');
$template_id = (int)get('template_id');
$item_id = (int)get('item_id');

$template_texts = $db->query("SELECT *
	FROM `template_item_texts` 
	where status=1 and type={$type} and template_item={$item_id}
order by date desc
");

// Inicia HTML

$webadm->set_page( array( 'name' => 'Textos' ) );

$webadm->add_button( array( 'name' => 'Novo Texto', 'icon' => 'mdi mdi-format-list-bulleted-type', 'url' => "template_text_edit.php?type={$type}&template_id={$template_id}&item_id={$item_id}") );

$webadm->add_parent( array( 'name' => ($type == 2)? 'Templates - Estilos' : 'Templates - Backgrounds', 'url' => 'templates.php' ) );

	
$webadm->add_parent( array( 'name' => 'Itens', 'url' => "template_items.php?id={$template_id}&type={$type}" ) );
$webadm->add_parent( array( 'name' => 'Item', 'url' => "template_item_edit.php?template_id={$template_id}&id={$item_id}&type={$type}" ) );

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
						<th>Título</th>						
						<th>Cor</th>
						<th>Ação</th>
					</tr>
				</thead>
				<tbody>
					<?php
					foreach($template_texts as &$template_text) {

						extract( $db->content($template_text, 'template_texts'), EXTR_PREFIX_ALL, 'i' );
					?>
					<tr content-id="<?php echo $i_id; ?>" >						
						
						<td class="dt-nowrap text-nowrap align-middle">
							<a href="<?php echo $i_edit_url."&template_id={$template_id}&item_id={$item_id}&type={$type}"; ?>">
								<h4><?php echo htmltotext($i_name, 35); ?></h4>
							</a>
						</td>
						
						<td class="dt-nowrap text-nowrap align-middle" style="background:<?php echo $i_template_color; ?>" align="middle">
							<a href="<?php echo $i_edit_url."&template_id={$template_id}&item_id={$item_id}&type={$type}"; ?>">
								
							</a>
						</td>

						<td class="text-center align-middle">
							<div class="btn-group btn-group-justified">
								<a class="btn btn-danger text-white btn-sm toggle-car-color" data-toggle="tooltip" title="Remover Texto">
									<i class="mdi mdi-close"></i>
								</a>
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
			{ width: "1px", targets: [1,2] },
		],
		ordering: false	
	});
		
	$(document).ready(function() {
	  $("#success-alert").fadeTo(5000, 500).slideUp(500, function() {
		  $("#success-alert").slideUp(500);
		});	  
	});
	
	$('.toggle-car-color').click(function(){
		
		var contentId = $(this).closest('tr').attr( "content-id" );
		
		if($(this).hasClass( "btn-danger" )){
			
			swal({   
				title: "Deseja mesmo remover esse Texto?",
				type: "warning",   
				showCancelButton: true,   
				confirmButtonColor: "#DD6B55",   
				confirmButtonText: "Sim, remover!",   
				closeOnConfirm: false,
				cancelButtonText: "Cancelar"
			}, function(){
								
				window.location = "<?php echo url('admin/template_text_edit.php'); ?>?item_id=" + <?php echo $item_id;?> + "&id=" + contentId + "&template_id="+<?php echo $template_id;?>+ "&type="+<?php echo $type;?>+"&toggle_active";
				
			});
			
		} else {

			window.location = "<?php echo url('admin/template_text_edit.php'); ?>?item_id=" + <?php echo $item_id;?> + "&id=" + contentId + "&template_id="+<?php echo $template_id;?>+ "&type="+<?php echo $type;?>+ "&toggle_active";
			
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