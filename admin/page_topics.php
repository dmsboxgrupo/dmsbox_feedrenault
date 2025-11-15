<?php

// API

include('includes/common.php');

// Codigo da pagina

need_login();

$content_id = (int)get('id');

if ($content_id > 0) {
	
	$content = $db->content( $db->select_id('pages', $content_id), 'pages' );

	if (has('update')) {
		
		$json = array();
		
		if (has_post('topics')) {
			
			$topics = get_post('topics');
			
			$json['topics'] = $topics;
			
		}
		
		$db->update('pages', $content_id, $json);
		
		echo json_encode( $json, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT );
		
		$_SESSION['message'] = 'Registro alterado com sucesso.';	    
		$_SESSION['type'] = 'success';
		
		exit;
		
	}

} else {
	
	redirect('pages.php');
	
}
/*
$aux =	explode(",", $content['topics']);
print_r($aux);
echo "<br/>";
$tesR = count($aux);
if(!empty ($aux[0])) echo "tes =$tesR";
die();*/

// Inicia HTML
/*
$website->page_info = array('name' => 'Gerenciador de tópicos', 'description' => 'Tópicos');
$website->add_button('Adicionar tópico', "technology_topic_edit.php?technology={$content_id}", 'mdi mdi-file-document');
$website->add_parent('Tecnogias', 'pages.php');
$website->add_parent($content['name'], "technology_edit.php?id={$content_id}");
$website->add_plugins('datatables', 'sweetalert', 'redirect');
$website->start_panel();*/

$webadm->set_page( array( 'name' => 'Tópicos') );
$webadm->add_button( array( 'name' => 'Novo tópico', 'icon' => 'mdi mdi-format-list-bulleted', 'url' => "page_topic_edit.php?page={$content_id}" ) );
$webadm->add_parent( array( 'name' => 'Páginas', 'url' => 'pages.php' ) );
$webadm->add_parent( array( 'name' => $content['name'], 'url' => "Page_edit.php?id=$content_id"));
$webadm->add_plugins('datatables', 'sweetalert', 'redirect');
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
					
					//for($i = 0; $i < count($content['topics']); $i++) {
					$aux =	explode(",", $content['topics']);
					if(!empty ($aux[0]))
					for($i = 0; $i < count($aux); $i++) {
						extract( $db->content( $aux[$i], 'page_topics' ), EXTR_PREFIX_ALL, 'i' );
					
					?>
					<tr page-id="<?php echo $content_id; ?>" content-id="<?php echo $i_id; ?>" content-name="<?php echo html( $i_name ); ?>">
						<td><?php echo $i; ?></td>
						<td class="align-middle dt-nowrap">
							<i class="mdi mdi-drag-vertical"></i>
						</td>
						<td class="dt-nowrap text-nowrap align-middle">
							<a href="<?php echo html( $i_edit_url ); ?>">
								<h4><?php echo html( $i_name ); ?></h4>
							</a>
						</td>
						
						<td class="text-center align-middle">
							
							<div class="btn-group btn-group-justified">
								<?php if ($i_status) { ?>
								<a class="btn btn-danger text-white btn-sm toggle-topic" data-toggle="tooltip" title="Remover Tópico">
									<i class="fas fa-eye-slash"></i>
								</a>
								<?php } else { ?>
								<a class="btn btn-info text-white btn-sm toggle-topic" data-toggle="tooltip" title="Ativar Tópico">
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
			{ width: "1px", targets: [1,3] }
		],
		order: [[ 0, "asc" ]]
	});
	
	contents.on( 'row-reorder', function ( e, diff, edit ) {

	var seq = [];

	$('#contents tbody tr').each(function() {
		
		seq.push( parseInt( $(this).attr('content-id') ) );
		
	});
	
	$.post( "page_topics.php?id=<?php echo $content_id ?>&update", { topics: seq.join(',') })
		.done(function( data ) {

			console.log( data );

		});

	} );

	/*$('.remove-content').click(function(){
		
		var contentId = $(this).closest('tr').attr( "content-id" );
		
		swal({   
			title: "Deseja mesmo excluir este tópico?",    
			type: "warning",   
			showCancelButton: true,   
			confirmButtonColor: "#DD6B55",   
			confirmButtonText: "Sim, exluir!",   
			closeOnConfirm: false,
			cancelButtonText: "Cancelar"
		}, function(){
			
			window.location = "<?php echo url('technology_topic_edit.php'); ?>?technology=<?php echo $content_id; ?>&id=" + contentId + "&remove";
			
		});
	});*/
	
	$('.toggle-topic').click(function(){
		
		var contentId = $(this).closest('tr').attr( "content-id" );
		var pageId = $(this).closest('tr').attr( "page-id" );
		
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
				
				window.location = "<?php echo url('admin/page_topic_edit.php'); ?>?page=" + pageId + "&id=" + contentId + "&toggle_active";
				
			});
		} else
			window.location = "<?php echo url('admin/page_topic_edit.php'); ?>?page=" + pageId + "&id=" + contentId + "&toggle_active";
	});
	
	$(document).ready(function() {
	  $("#success-alert").fadeTo(5000, 500).slideUp(500, function() {
		  $("#success-alert").slideUp(500);
		});	  
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