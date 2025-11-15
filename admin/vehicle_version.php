<?php

// API

include('includes/common.php');
if (!isset($_SESSION['type'])) $_SESSION['type']='';

// Codigo da pagina

need_login();

$content_id = (int)get('content_id');

$manager_user_logado = $useradm->get_property('id');
$manager = ($useradm->is_level(LEVEL_MASTER))? "" : "and (user=$manager_user_logado)";

//$vehicle_version = $db->query("SELECT * FROM `vehicle_version` where status=1 $manager");

//METADATA
$metadata_id = $db->get_metadata_id( 'vehicle_version' );
if($metadata_id=="") $metadata_id = $db->set_metadata( 'vehicle_version' );

if ($metadata_id > 0) {
	
	$content = $db->content( $db->select_id('metadata', $metadata_id), 'metadata' );

	if (has('update')) {
		
		$json = array();
		
		if (has_post('value')) {
			
			$vehicle_versions = get_post('value');
			
			$json['value'] = $vehicle_versions;
			
		}
		
		$db->update('metadata', $metadata_id, $json);
		
		echo json_encode( $json, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT );
		
		$_SESSION['message'] = 'Registro alterado com sucesso.';	    
		$_SESSION['type'] = 'success';
		
		exit;
		
	}

}


// Inicia HTML

$webadm->set_page( array( 'name' => 'Veículos Versões' ) );

$webadm->add_parent( array( 'name' => 'Galerias WhatsApp', 'url' => 'whatsapp_galeries.php' ) );	
$webadm->add_parent( array( 'name' => 'Galeria WhatsApp', 'url' => "whatsapp_gallery_edit.php?id=$content_id" ) );

$webadm->add_button( array( 'name' => 'Novo Veículo Versão', 'icon' => 'mdi mdi-buffer m-r-5', 'url' => "vehicle_version_edit.php?content_id=$content_id" ) );

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
						<th>Seq.</th>
						<th><i class="mdi mdi-drag-vertical"></i></th>
						<th>Veículo</th>
						<th>Versão</th>
						<th>Ação</th>
					</tr>
				</thead>
				<tbody>
					<?php
					$aux =	explode(",", $content['value']);
					if(!empty ($aux[0]))
					
						for($i = 0; $i < count($aux); $i++) {
							extract( $db->content( $aux[$i], 'vehicle_version'), EXTR_PREFIX_ALL, 'i' );
							//if ($i_status) {
								
						//foreach($vehicle_version as &$vehicle_version_item) {

						//extract( $db->content($vehicle_version_item, 'vehicle_version'), EXTR_PREFIX_ALL, 'i' );
					?>
					<tr content-id="<?php echo $i_id; ?>" >						
						
						<td><?php echo $i; ?></td>
						<td class="align-middle dt-nowrap">
							<i class="mdi mdi-drag-vertical"></i>
						</td>
						
						<td class="dt-nowrap text-nowrap align-middle">
							<a href="<?php echo $i_edit_url."&content_id=$content_id"; ?>">
								<h4><?php echo htmltotext($i_vehicle_name, 35); ?></h4>
							</a>
						</td>
						
						<td class="dt-nowrap text-nowrap align-middle">
							<a href="<?php echo $i_edit_url."&content_id=$content_id"; ?>">
								<h4><?php echo htmltotext($i_version_name, 35); ?></h4>
							</a>
						</td>
						
						<td class="text-center align-middle">
							<div class="btn-group btn-group-justified">
								<?php if ($i_status) { ?>
								<a class="btn btn-danger text-white btn-sm toggle-vehicle_version" data-toggle="tooltip" title="Remover Veículo Versão">
									<i class="fas fa-eye-slash"></i>
								</a>
								<?php } else { ?>
								<a class="btn btn-info text-white btn-sm toggle-vehicle_version" data-toggle="tooltip" title="Ativar Veículo Versão">
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
		/*
	$('#contents').DataTable({
		language: { url: "<?php echo url( 'admin/plugins/datatables-language/Portuguese-Brasil.json' ) ?>" },
		pageLength: 50,
		lengthChange: false,
		scrollY: 'calc(100vh - 460px)',
		columnDefs: [
			{ width: "1px", targets: [ 2] },
		],
		ordering: false	
	});*/
	
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
			{ width: "1px", targets: [1,4] }
		],
		order: [[ 0, "asc" ]]
	});
	
	contents.on( 'row-reorder', function ( e, diff, edit ) {

		var contentId = $(this).closest('tr').attr( "content-id" );
		
		var seq = [];

		$('#contents tbody tr').each(function() {
			
			seq.push( parseInt( $(this).attr('content-id') ) );
			
		});
		
		$.post( "vehicle_version.php?update&content_id="+contentId, { value: seq.join(',') })
			.done(function( data ) {

				console.log( data );

			});

	} );
		
	$(document).ready(function() {
	  $("#success-alert").fadeTo(5000, 500).slideUp(500, function() {
		  $("#success-alert").slideUp(500);
		});	  
	});
	
	$('.toggle-vehicle_version').click(function(){
		
		var playerId = $(this).closest('tr').attr( "player-id" );
		var contentId = $(this).closest('tr').attr( "content-id" );
		
		if($(this).hasClass( "btn-danger" )){
			
			swal({   
				title: "Deseja mesmo remover Veículo Versão?",
				type: "warning",   
				showCancelButton: true,   
				confirmButtonColor: "#DD6B55",   
				confirmButtonText: "Sim, remover!",   
				closeOnConfirm: false,
				cancelButtonText: "Cancelar"
			}, function(){
				
				window.location = "<?php echo url('admin/vehicle_version_edit.php'); ?>?id=" + contentId + "&toggle_active";
				
			});
			
		} else {
			
			window.location = "<?php echo url('admin/vehicle_version_edit.php'); ?>?id=" + contentId + "&toggle_active";
			
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