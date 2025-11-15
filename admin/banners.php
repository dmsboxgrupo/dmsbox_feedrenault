<?php

// API

include('includes/common.php');

// Codigo da pagina

need_login();

//Parametros
$category_id = (int)get('category');

if($category_id=="")
	redirect("banners_select.php");


$category = $list_categories[$category_id];

//variaveis do grupo
$metadata_id = $db->get_metadata_id( $category['property'] );
if($metadata_id=="") $metadata_id = $db->set_metadata( $category['property'] );

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
	
	redirect($category['url_list']);
	
}

$webadm->set_page( array( 'name' => 'Banners' ) );

$webadm->add_button( array( 'name' => 'Novo Banner', 'icon' => 'mdi mdi-bulletin-board', 'url' => "banner_edit.php?category={$category_id}" ) );

$webadm->add_parent( array( 'name' => $category['name_list'], 'url' => $category['url_list'] ) );
//if($category['name_list']=='Postagens') $webadm->add_parent( array( 'name' => 'Seleciona Categoria Banner', 'url' => 'banners_select.php' ) );

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
						<th>Imagem</th>
						<th>Ação</th>
					</tr>
				</thead>
				<tbody>
					<?php 
					$aux =	explode(",", $content['value']);
					if(!empty ($aux[0]))
					for($i = 0; $i < count($aux); $i++) {
						extract( $db->content( $aux[$i], 'banners'), EXTR_PREFIX_ALL, 'i' );
							if ($i_status) {
					?>
					<tr banner-id="<?php echo $i_id; ?>" content-name="<?php echo html( $i_name ); ?>">
						<td><?php echo $i; ?></td>
						<td class="align-middle dt-nowrap">
							<i class="mdi mdi-drag-vertical"></i>
						</td>
						
						<td class="dt-nowrap text-nowrap align-middle">
							<a href="<?php echo $i_edit_url."&category={$category_id}" ; ?>">
								<h4><?php echo html( $i_name ); ?></h4>
							</a>
						</td>
						<td class="dt-nowrap text-nowrap align-middle" align="middle">
							<?php if($i_image>0){if($i_extension =='mp4'){ ?>
								<video src="<?php echo url("/uploads/". $i_image_url); ?>" style="max-width:80px; max-height:80px;" />
							<?php } else{ ?>
								<img src="<?php echo url("/uploads/". $i_image.".".$i_extension ); ?>" style="max-width:80px; max-height:80px;" />
							<?php }} ?>
						</td>
						<td class="text-center align-middle">
							
							<div class="btn-group btn-group-justified">
								<?php if ($i_status) { ?>
								<a class="btn btn-danger text-white btn-sm toggle-banner" data-toggle="tooltip" title="Remover Tópico">
									<i class="mdi mdi-close"></i>
								</a>
								<?php } else { ?>
								<a class="btn btn-info text-white btn-sm toggle-banner" data-toggle="tooltip" title="Ativar Tópico">
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
			{ width: "1px", targets: [0,1,3,4] }
		],
		order: [[ 0, "asc" ]]
	});
	
	contents.on( 'row-reorder', function ( e, diff, edit ) {

	var seq = [];

	$('#contents tbody tr').each(function() {
		
		seq.push( parseInt( $(this).attr('banner-id') ) );
		
	});
	
	$.post( "banners.php?category=<?php echo $category_id ?>&update", { value: seq.join(',') })
		.done(function( data ) {

			console.log( data );

		});

	} );
	
	$('.toggle-banner').click(function(){
		
		var bannerId = $(this).closest('tr').attr( "banner-id" );
		var contentId = $(this).closest('tr').attr( "content-id" );
		
		if($(this).hasClass( "btn-danger" )){
			swal({   
				title: "Deseja mesmo remover essa Tópico?",
				type: "warning",   
				showCancelButton: true,   
				confirmButtonColor: "#DD6B55",   
				confirmButtonText: "Sim, remover!",   
				closeOnConfirm: false,
				cancelButtonText: "Cancelar"
			}, function(){
				
				window.location = "<?php echo url('admin/banner_edit.php'); ?>?category=<?php echo $category_id ?>&id=" + bannerId + "&toggle_active";
				
			});
		} else
			window.location = "<?php echo url('admin/banner_edit.php'); ?>?category=<?php echo $category_id ?>&id=" + bannerId + "&toggle_active";
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