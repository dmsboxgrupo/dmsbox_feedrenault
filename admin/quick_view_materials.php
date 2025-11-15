<?php

// API

include('includes/common.php');

// Codigo da pagina

need_login();

$content_id = (int)get('content_id');

$quick_views = $db->content( $db->select_id('quick_views', $content_id), 'quick_views' );


$where = isset($quick_views['materials']) & !empty($quick_views['materials']) ? "  and id in ({$quick_views['materials']}) " : " and id in (0) ";

//print_r($where);die();

$quick_view_materials = $db->query("SELECT *,
(SELECT extension FROM `uploads` WHERE uploads.id=quick_view_materials.image) as img
FROM `quick_view_materials` WHERE status=1 $where 
order by date desc
");

//print_r($quick_view_materials);die();


/*
$quick_view_materials = $db->query("SELECT *,
(SELECT extension FROM `uploads` WHERE uploads.id=quick_view_materials.image) as img
FROM `quick_view_materials` WHERE category=15 and status=1 and quick_view=$content_id 
order by date desc
");*/

// Inicia HTML

$webadm->add_button( array( 'name' => 'Gerenciar tags', 'icon' => 'mdi mdi-tag-multiple', 'url' => "tags.php?category=15&content_id=$content_id" ) );

//$webadm->add_button( array( 'name' => 'Gerenciar Banners', 'icon' => 'mdi mdi-bulletin-board', 'url' => "banners.php?category=5" ) );



$webadm->set_page( array( 'name' => 'Materiais' ) );

$webadm->add_parent( array( 'name' => 'Fichas de Modelos', 'url' => 'quick_views.php' ) );
$webadm->add_parent( array( 'name' => 'Ficha de Modelo', 'url' => "quick_view_edit.php?id=$content_id"));

//$webadm->add_button( array( 'name' => 'Novo Material', 'icon' => 'mdi mdi-whatsapp', 'url' => "quick_view_material_edit.php" ) );
$webadm->add_button( array( 'name' => 'Novo Material', 'icon' => 'mdi mdi-format-list-bulleted', 'url' => "quick_view_material_edit.php?quick_view_id={$content_id}" ) );

$webadm->add_plugins('datatables', 'sweetalert', 'select2');

$webadm->start_panel();

?>
<!-- ============================================================== -->
<!-- Start Page Content -->
<!-- ============================================================== -->

<style>
#column-select {
	
	width: 160px;
	
}

#contents_filter label{
	
	display: none;
}

.dataTables_filter input {

   width: 100%;
}

#search-input {
	
	width: 100%;
	
}

.dataTables_filter input{
	
	width: calc(90% - 160px) !important;
	
}

</style>


<?php if ( $_SESSION['type']=='success' ) { $_SESSION['type']='';?>
	<div id="success-alert" class="alert alert-success">
	<?php echo $_SESSION['message']; $_SESSION['message']=''; ?>
	<button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">×</span></button>
	</div>
<?php } ?>


<div class="card no-margin">
	<div class="card-body">
		<div class="table-responsive">
		
			<div class="dataTables_filter">
				
				<label>Pesquisar
				
					<select id="column-select" style="" >
						<option selected value="99">Todas as Colunas</option>
						<option value="0">Data</option>
						<option value="1">Mensagem</option>
						<option value="2">Tags</option>
					</select>
					
					<input type="text" id="search-input" >
					
				</label>
				
			</div>
		
			<table id="contents" class="display pageResize nowrap table table-striped table-bordered"><!--class="table-hover"-->
				<thead>
					<tr>
						<th>Data</th>
						<th>Mensagem</th>
						<th>Tags</th>
						<th>Imagem</th>
						<th>Ação</th>
					</tr>
				</thead>
				<tbody>
					<?php
					foreach($quick_view_materials as &$quick_view_material) {

						extract( $db->content($quick_view_material, 'quick_view_materials'), EXTR_PREFIX_ALL, 'i' );
					?>
					<tr quick_view-id="<?php echo $content_id; ?>" content-id="<?php echo $i_id; ?>">					
						<td class="dt-nowrap text-nowrap align-middle">
							<a href="<?php echo $i_edit_url; ?>">
								<span style="color:<?php echo $i_date_color ?>"><?php echo $i_date_str; ?></span>
							</a>
						</td>
						<td class="dt-nowrap text-nowrap align-middle">
							<a href="<?php echo $i_edit_url; ?>">
								<h4><?php echo htmltotext($i_text, 20); ?></h4>
							</a>
						</td>
						
						<td class="dt-wrap text-wrap align-middle" >
							<a href="<?php echo html( $i_edit_url ); ?>">
								<?php							
								if (!empty($i_tags)){
									$tags_gal = $db->query("SELECT id, name FROM tags WHERE status = 1 and id in($i_tags)");								if (!empty($tags_gal))
									foreach($tags_gal as &$tag_gal) {
								?>
										<span class="badge badge-info"><?php echo $tag_gal['name']; ?></span><br/>
								<?php
								}}
								?>
							</a>
						</td>
										
						<td class="dt-nowrap text-nowrap align-middle" align="middle">
						
						<?php if($i_image>0 || $i_image_highlight>0) {
							$i_image_thumb = ($i_image_highlight)? $i_image_highlight : $i_image;
							?> 
							<img src="<?php echo url( "content.php?q=painel-thumbnail&id={$i_image_thumb}&type=image" ); ?>" style="max-width:80px; max-height:80px;" />
						<?php }?>
						
						<?php /*if($i_image>0){if($i_img=='mp4'){ ?>
							<video src="<?php echo url("/uploads/". $i_image.".".$i_img); ?>" style="max-width:80px; max-height:80px;" />
						<?php } else{ ?>
							<img src="<?php echo url("/uploads/". $i_image.".".$i_img); ?>" style="max-width:80px; max-height:80px;" />
						<?php }} */?>
							
						</td>
						<td class="text-center align-middle">
							<div class="btn-group btn-group-justified">
								<?php if ($i_status) { ?>
								<a class="btn btn-danger text-white btn-sm toggle-quick_view_material" data-toggle="tooltip" title="Remover Material">
									<i class="mdi mdi-close"></i>
								</a>
								<?php } else { ?>
								<a class="btn btn-info text-white btn-sm toggle-quick_view_material" data-toggle="tooltip" title="Ativar Material">
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
	
	var table = $('#contents').DataTable({
		language: { url: "<?php echo url( 'admin/plugins/datatables-language/Portuguese-Brasil.json' ) ?>" },
		pageLength: 50,
		lengthChange: false,
		scrollY: 'calc(100vh - 460px)',
		columnDefs: [
			{ width: "1px", targets: [0,2,3,4] },
		],
		ordering: false	
	});
	

	$('#column-select').on('change', function() {
	  
		var selected = $(this).val();
	  
		table.columns().search('').draw();
		table.search('').draw();

		if( selected != 99 ){

			table.columns(selected).search($('#search-input').val()).draw()

		} else{

			table.search( $('#search-input').val() ).draw();

		}
	  
	});
	
	$('#search-input').on('keyup', function() {

		var col_sel = $('#column-select').val();

		if( col_sel != 99 ){
		  
			table.columns(col_sel).search($(this).val()).draw();

		}else{
			
			table.search( $(this).val() ).draw();
			
		}
		
	});
	
	
	$("#column-select").select2({
		placeholder: ""
	});
		
	$(document).ready(function() {
	  $("#success-alert").fadeTo(5000, 500).slideUp(500, function() {
		  $("#success-alert").slideUp(500);
		});	  
	});
	
	$('.toggle-quick_view_material').click(function(){
		
		var contentId = $(this).closest('tr').attr( "content-id" );
		var quickViewId = $(this).closest('tr').attr( "quick_view-id" );
		
		if($(this).hasClass( "btn-danger" )){
			
			swal({   
				title: "Deseja mesmo remover esse Material?",
				type: "warning",   
				showCancelButton: true,   
				confirmButtonColor: "#DD6B55",   
				confirmButtonText: "Sim, remover!",   
				closeOnConfirm: false,
				cancelButtonText: "Cancelar"
			}, function(){
				
				window.location = "<?php echo url('admin/quick_view_material_edit.php'); ?>?quick_view_id=" + quickViewId + "&id=" + contentId + "&toggle_active";
				
			});
			
		} else {
			
			window.location = "<?php echo url('admin/quick_view_material_edit.php'); ?>?quick_view_id=" + quickViewId + "&id=" + contentId + "&toggle_active";
			
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