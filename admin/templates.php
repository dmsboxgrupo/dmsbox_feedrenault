<?php

// API

include('includes/common.php');
$_SESSION['type']=$_SESSION['type'];

// Codigo da pagina

need_login();

// Controller
//$template_id = (int)get('id');
//$type = (int)get('type');

$type = (int)get('type');

if($type==2){
	
	$template_name ='styles';
	$template_label ='Estilo';
	$category_highlighted_tags = 10;
	
}else{
	
	$template_name ='templates';
	$template_label ='Template';
	$category_highlighted_tags = 9;
	
}

$metadata_id = $db->get_metadata_id( $template_name );
if($metadata_id=="") $metadata_id = $db->set_metadata( $template_name );


//echo "metadata_id= $metadata_id"; die();
if ($metadata_id > 0) {
	
	$content = $db->content( $db->select_id('metadata', $metadata_id), 'metadata' );

	if (has('update')) {
		
		$json = array();
		
		if (has_post('value')) {
			
			$banners = get_post('value');
			
			$json['value'] = $banners;
			
		}
		
		$db->update('metadata', $metadata_id, $json);
		
		echo json_encode( $json, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT );
		
		$_SESSION['message'] = 'Registro alterado com sucesso.';	    
		$_SESSION['type'] = 'success';
		
		exit;
		
	}

} else {
	
	redirect("templates.php");
	
}

$webadm->set_page( array( 'name' => "{$template_label}s" ) );

//if ( $type!=2 )$webadm->add_button( array( 'name' => 'Tags de Destaque', 'icon' => 'mdi mdi-tag-multiple', 'url' => "template_tags.php?category={$category_highlighted_tags}" ) );

//$webadm->add_button( array( 'name' => ($type==2)? 'Backgrounds' : 'Estilos', 'icon' => 'mdi mdi-note-multiple-outline', 'url' => ($type==2)?"templates.php" : "templates.php?type=2" ) );

$webadm->add_button( array( 'name' => "Novo {$template_label}", 'icon' => 'mdi mdi-apps', 'url' => ($type==2)? "template_edit.php?type=2" : "template_edit.php" ) );

$webadm->add_plugins('datatables', 'sweetalert', 'select2');
$webadm->start_panel();

?>
<!-- ============================================================== -->
<!-- Start Page Content -->
<!-- ============================================================== -->
<style>
#editor-container {
	width: 100%;
	height: calc(100vh - 700px);
}

.card-body{
	 min-height: 510px;
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
				<table id="contents" class="display pageResize nowrap table table-striped table-bordered">
					<thead>
						<tr>
							<th>Seq.</th>
							<th><i class="mdi mdi-drag-vertical"></i></th>
							<th>Nome</th>
							<th>Imagem</th>
							<th>Ação</th>
						</tr>
					</thead>
					<tbody>
						<?php 
						$aux =	explode(",", $content['value']);
						if(!empty ($aux[0])){
						for($i = 0; $i < count($aux); $i++) {
							extract( $db->content( $aux[$i], 'templates'), EXTR_PREFIX_ALL, 'i' );
								if (!empty ($i_status)) {
						?>						
						<tr type-id="<?php echo $i_type; ?>" template-id="<?php echo $i_id; ?>" content-name="<?php echo html( $i_name ); ?>">
							<td><?php echo $i; ?></td>
							<td class="align-middle dt-nowrap">
								<i class="mdi mdi-drag-vertical"></i>
							</td>
							
							<td class="dt-nowrap text-nowrap align-middle">
								<a href="<?php echo $i_edit_url; ?>">
									<h4><?php echo html( $i_name ); ?></h4>
								</a>
							</td>
							
							<td class="dt-nowrap text-nowrap align-middle" align="middle">
							
								<img src="<?php if($i_image)echo url( $i_image_url); ?>" style="max-width:80px; max-height:80px;" />
								
							</td>
							
							<td class="text-center align-middle">
								
								<div class="btn-group btn-group-justified">
									<a class="btn btn-danger text-white btn-sm toggle-template_background_item" data-toggle="tooltip" title="Remover Item">
										<i class="mdi mdi-close"></i>
									</a>
								</div>
							</td>
						</tr>
						<?php }} } ?>
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
		scrollY: 'calc(100vh - (540px - 52px))',
		columnDefs: [
			{ targets: 0, visible: false },
			{ orderable: true, className: 'reorder', targets: 1 },
			{ orderable: false, targets: '_all' },
			{ width: "1px", targets: [0,1,3,4] }
		],
		order: [[ 0, "asc" ]]
	});
	
	contents.on( 'row-reorder', function ( e, diff, edit ) {

	var seq = [];

	$('#contents tbody tr').each(function() {
		
		seq.push( parseInt( $(this).attr('template-id') ) );
		
	});

	$.post( "templates.php?type=<?php echo $type ?>&update", { value: seq.join(',') })
		.done(function( data ) {

			console.log( data );

		});

	} );
	
	$('.toggle-template_background_item').click(function(){
		
		var template_id = $(this).closest('tr').attr( "template-id" );
		var type_id = $(this).closest('tr').attr( "type-id" );
		
		if($(this).hasClass( "btn-danger" )){
			swal({   
				title: "Deseja mesmo remover esse Item?",
				type: "warning",   
				showCancelButton: true,   
				confirmButtonColor: "#DD6B55",   
				confirmButtonText: "Sim, remover!",   
				closeOnConfirm: false,
				cancelButtonText: "Cancelar"
			}, function(){
				//type=$type&
				window.location = "<?php echo url('admin/template_edit.php'); ?>?id=" + template_id +
					"&toggle_active";
				
			});
		} else
			window.location = "<?php echo url('admin/template_edit.php'); ?>?id=" + template_id +
			"&toggle_active";
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