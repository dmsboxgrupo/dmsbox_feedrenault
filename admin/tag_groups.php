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

//$meta_property = $list_group_tags[$group]['tags'];
$meta_property = "groups_".$list_categories[$category]['tags'];

$par_content_type = ($category == 8)? $par_content_type ="&type=2" : "";

//whatsapp_galeries_tags
//campaigns_tags

//METADATA

//$metadata_id = $db->get_metadata_id( 'tag_groups' );
//if($metadata_id=="") $metadata_id = $db->set_metadata( 'tag_groups' );


//METADATA
$metadata_id = $db->get_metadata_id( $meta_property );
if($metadata_id=="") $metadata_id = $db->set_metadata( $meta_property );

//print_r($meta_property);die();
if ($metadata_id > 0) {
	
	$content = $db->content( $db->select_id('metadata', $metadata_id), 'metadata' );

	if (has('update')) {
		
		$json = array();
		
		if (has_post('value')) {
			
			$tag_groups = get_post('value');
			
			$json['value'] = $tag_groups;
			
		}
		
		$db->update('metadata', $metadata_id, $json);
		
		echo json_encode( $json, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT );
		
		$_SESSION['message'] = 'Registro alterado com sucesso.';	    
		$_SESSION['type'] = 'success';
		
		exit;
		
	}

}

$webadm->set_page( array( 'name' => 'Grupos Tags' ) );

$webadm->add_button( array( 'name' => 'Novo Grupo Tag', 'icon' => 'mdi mdi-tag-plus', 'url' => "tag_group_edit.php?{$par_content}category={$category}" ) );

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
						<th>Seq.</th>
						<th><i class="mdi mdi-drag-vertical"></i></th>
						<th>Nome</th>
						<th>Ação</th>
					</tr>
				</thead>
				<tbody>
					<?php 
					$aux =	explode(",", $content['value']);
					if(!empty ($aux[0]))
					for($i = 0; $i < count($aux); $i++) {
						extract( $db->content( $aux[$i], 'tag_groups'), EXTR_PREFIX_ALL, 'i' );
							if (!empty ($i_status)) {
					?>
					<tr category-id="<?php echo $category; ?>" content-id="<?php echo $content_id; ?>" tag_group-id="<?php echo $i_id; ?>" content-name="<?php echo $i_name; ?>">					
						<td><?php echo $i; ?></td>
						<td class="align-middle dt-nowrap">
							<i class="mdi mdi-drag-vertical"></i>
						</td>
						
						<td class="dt-nowrap text-nowrap align-middle">
							<a href="<?php echo $i_edit_url."&{$par_content}"; ?>">
								<h4><?php echo html( $i_name ); ?></h4>
							</a>
						</td>
						
						<td class="text-center align-middle">
							
							<div class="btn-group btn-group-justified">
								<?php if ($i_status) { ?>
								<a class="btn btn-danger text-white btn-sm toggle-tag_group" data-toggle="tooltip" title="Remover Tag">
									<i class="mdi mdi-close"></i>
								</a>
								<?php } else { ?>
								<a class="btn btn-info text-white btn-sm toggle-tag_group" data-toggle="tooltip" title="Ativar Tag">
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
	var contents = $('#contents').DataTable({		
		language: { url: "<?php echo url( 'admin/plugins/datatables-language/Portuguese-Brasil.json' ) ?>" },
		rowReorder: true,
		paging: false,
		searching: false,
		scrollY: 'calc(100vh - (440px - 52px))',
		columnDefs: [
			{ targets: 0, visible: false },
			{ orderable: true, className: 'reorder', targets: 1 },
			{ orderable: false, targets: '_all' },
			{ width: "1px", targets: [0,1,3] }
		],
		order: [[ 0, "asc" ]]
	});
	
	contents.on( 'row-reorder', function ( e, diff, edit ) {

	var seq = [];

	$('#contents tbody tr').each(function() {
		
		seq.push( parseInt( $(this).attr('tag_group-id') ) );
		
	});
	
	//$.post( "tags.php?update", { value: seq.join(',') })
	$.post( "tag_groups.php?id=<?php echo $content_id ?>&category=<?php echo $category ?>&content_id=<?php echo $content_id ?>&update", { value: seq.join(',') })
		.done(function( data ) {

			console.log( data );

		});

	} );
	
	$('.toggle-tag_group').click(function(){
		
		var taggroupId = $(this).closest('tr').attr( "tag_group-id" );
		var contentId = $(this).closest('tr').attr( "content-id" );
		var categoryId = $(this).closest('tr').attr( "category-id" );
		
		if($(this).hasClass( "btn-danger" )){
			swal({   
				title: "Deseja mesmo remover essa Grupo Tag?",
				type: "warning",   
				showCancelButton: true,   
				confirmButtonColor: "#DD6B55",   
				confirmButtonText: "Sim, remover!",   
				closeOnConfirm: false,
				cancelButtonText: "Cancelar"
			}, function(){
				
				window.location = "<?php echo url('admin/tag_group_edit.php'); ?>?id=" + taggroupId + 
					"&toggle_active&category=" + categoryId + "&content_id=" + contentId;
				
			});
		} else
			window.location = "<?php echo url('admin/tag_group_edit.php'); ?>?id=" + taggroupId + 
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