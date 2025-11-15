<?php

// API

include('includes/common.php');
$_SESSION['type']=$_SESSION['type'];

//$group = (int)get('group');
$category = (int)get('category');
$content_id = (int)get('content_id');

$par_content = ($content_id>0)?"content_id={$content_id}&":"";

// Codigo da pagina

need_login();


$tag_subgroups = $db->query("SELECT *
FROM `tag_subgroups` WHERE status=1 
ORDER by date DESC
");

//$meta_property = $list_group_tags[$group]['tags'];
//$meta_property = $list_categories[$category]['tags'];

$par_content_type = ($category == 8)? $par_content_type ="&type=2" : "";

//whatsapp_galeries_tags
//campaigns_tags

//METADATA
/*
$metadata_id = $db->get_metadata_id( 'tag_subgroups' );
if($metadata_id=="") $metadata_id = $db->set_metadata( 'tag_subgroups' );

if ($metadata_id > 0) {
	
	$content = $db->content( $db->select_id('metadata', $metadata_id), 'metadata' );

	if (has('update')) {
		
		$json = array();
		
		if (has_post('value')) {
			
			$tag_subgroups = get_post('value');
			
			$json['value'] = $tag_subgroups;
			
		}
		
		$db->update('metadata', $metadata_id, $json);
		
		echo json_encode( $json, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT );
		
		$_SESSION['message'] = 'Registro alterado com sucesso.';	    
		$_SESSION['type'] = 'success';
		
		exit;
		
	}

}*/

$webadm->set_page( array( 'name' => 'Subgrupos Tags' ) );

$webadm->add_button( array( 'name' => 'Novo Subgrupo Tag', 'icon' => 'mdi mdi-tag-plus', 'url' => "tag_subgroup_edit.php?{$par_content}category={$category}" ) );

$webadm->add_parent( array( 'name' => $list_categories[$category]['name_list'], 'url' => $list_categories[$category]['url_list'] ) );

if($content_id>0)
	$webadm->add_parent( array( 'name' => $list_categories[$category]['name'], 'url' => $list_categories[$category]['url']."?id=$content_id{$par_content_type}"));
elseif($category == 7 and empty($content_id))
	$webadm->add_parent( array( 'name' => 'Tags de Destaque', 'url' => 'template_tags.php'));

$webadm->add_parent( array( 'name' => 'Tags', 'url' => "tags.php?{$par_content}category={$category}" ) );

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
			<table id="contents" class="display pageResize nowrap table table-striped table-bordered">
				<thead>
					<tr>
						<th>Nome</th>
						<th>Ação</th>
					</tr>
				</thead>
				<tbody>
					<?php 
					//$aux =	explode(",", $content['value']);
					//if(!empty ($aux[0]))
					//for($i = 0; $i < count($aux); $i++) {
					foreach($tag_subgroups as &$tag_subgroup) {	
						
						extract( $db->content($tag_subgroup, 'tag_subgroups'), EXTR_PREFIX_ALL, 'i' );
							if (!empty ($i_status)) {
					?>
					<tr category-id="<?php echo $category; ?>" content-id="<?php echo $content_id; ?>" tag_subgroup-id="<?php echo $i_id; ?>" content-name="<?php echo $i_name; ?>">					
						<!--<td>?php echo $i; ?></td>
						<td class="align-middle dt-nowrap">
							<i class="mdi mdi-drag-vertical"></i>
						</td>
						-->
						<td class="dt-nowrap text-nowrap align-middle">
							<a href="<?php echo $i_edit_url."&category={$category}&{$par_content}"; ?>">
								<h4><?php echo html( $i_name ); ?></h4>
							</a>
						</td>
						
						<td class="text-center align-middle">
							
							<div class="btn-group btn-group-justified">
								<?php if ($i_status) { ?>
								<a class="btn btn-danger text-white btn-sm toggle-tag_subgroup" data-toggle="tooltip" title="Remover Subgrupo Tag">
									<i class="mdi mdi-close"></i>
								</a>
								<?php } else { ?>
								<a class="btn btn-info text-white btn-sm toggle-tag_subgroup" data-toggle="tooltip" title="Ativar Subgrupo Tag">
									<i class="fas fa-eye"></i>
								</a>
								<?php } ?>
							</div>
						</td>
						
						
					</tr>
					<?php }} ?>
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
	
	$('.toggle-tag_subgroup').click(function(){
		
		var taggroupId = $(this).closest('tr').attr( "tag_subgroup-id" );
		var contentId = $(this).closest('tr').attr( "content-id" );
		var categoryId = $(this).closest('tr').attr( "category-id" );
		
		if($(this).hasClass( "btn-danger" )){
			swal({   
				title: "Deseja mesmo remover esse Subgrupo Tag?",
				type: "warning",   
				showCancelButton: true,   
				confirmButtonColor: "#DD6B55",   
				confirmButtonText: "Sim, remover!",   
				closeOnConfirm: false,
				cancelButtonText: "Cancelar"
			}, function(){
				
				window.location = "<?php echo url('admin/tag_subgroup_edit.php'); ?>?id=" + taggroupId + 
					"&toggle_active&category=" + categoryId + "&content_id=" + contentId;
				
			});
		} else
			window.location = "<?php echo url('admin/tag_subgroup_edit.php'); ?>?id=" + taggroupId + 
			"&toggle_active&category=" + categoryId + "&content_id=" + contentId;
	});
	
	$(document).ready(function() {
	  $("#success-alert").fadeTo(5000, 500).slideUp(500, function() {
		  $("#success-alert").slideUp(500);
		});	  
	});
});

</script>
<!-- ============================================================== -->
<!-- End post Content -->
<!-- ============================================================== -->
<?php 

// Finaliza HTML

$webadm->end_panel();

?>