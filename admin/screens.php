<?php

// API

include('includes/common.php');

// Codigo da pagina

need_login();
//FROM `posts` WHERE category not in ( 2, 4, 5, 6 ) 

//$content_id = $db->get_metadata_id( "intro_banner" );

$screens = $db->query("select * from metadata
							where property in(
											'intro_banner',
											'login_screen_video',
											'compartilhar_intro',
											'compartilhar_imagens',
											'compartilhar_catalogos',
											'compartilhar_cards_atributos',
											'compartilhar_comp_concorrencia',
											'compartilhar_comp_versoes',
											'compartilhar_on_demand')");
//print_r($screens); die();
// Inicia HTML

//$login_screen_video = $uploader->get( $login_screen_video_meta['value'] );

$webadm->set_page( array( 'name' => 'Telas' ) );
$webadm->add_parent( array( 'name' => 'Usuários', 'url' => 'users.php' ) );
//$webadm->add_button( array( 'name' => 'Nova Tela', 'icon' => 'mdi mdi-playlist-plus', 'url' => 'screen_edit.php' ) );

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
						<th>Imagem</th>
					</tr>
				</thead>
				<tbody>
					<?php
					foreach($screens as &$screen) {

						//extract( $db->content($survey, 'surveys'), EXTR_PREFIX_ALL, 'i' );
						$name = "";
						if($screen['property']=='intro_banner') $name ="Imagem Tela de Login";
						if($screen['property']=='login_screen_video') $name ="Video Tela de Login";
						if($screen['property']=='compartilhar_intro') $name ="Compartilhar Intro";
						if($screen['property']=='compartilhar_imagens') $name ="Compartilhar Imagens";
						if($screen['property']=='compartilhar_catalogos') $name ="Compartilhar Catálogos Comparados";
						if($screen['property']=='compartilhar_cards_atributos') $name ="Compartilhar Cards Atributos";
						if($screen['property']=='compartilhar_comp_concorrencia') $name ="Compartilhar Comparativo Concorrência";
						if($screen['property']=='compartilhar_comp_versoes') $name ="Compartilhar Comparativo Versões";
						if($screen['property']=='compartilhar_on_demand') $name ="Compartilhar On Demand";
						
					?>
						<tr content-id="<?php echo $screen['id']; ?>" >
							
							<td class="dt-nowrap text-nowrap align-middle">
								<a href="screen_edit.php?id=<?php echo $screen['id']; ?>">
									<h4><?php echo html( $name ); ?></h4>
								</a>
							</td>
							<!--
							<td class="dt-nowrap text-nowrap align-middle">
								<a href="screen_edit.php?id=?php echo $screen['id']; ?>">
									<h4>?php echo htmltotext($screen['value'], 50); ?></h4>
								</a>
							</td>
							-->
							<td class="dt-nowrap text-nowrap align-middle" align="middle">
								<?php 
								$screen_file = $uploader->get( $screen['value'] );
									
								if( $screen['value']  >0){if($screen_file['extension'] =='mp4'){ ?>
									<video src="<?php echo url("". $screen_file['url']); ?>" style="max-width:80px; max-height:80px;" />
								<?php } else{ ?>
									<img src="<?php echo url("/uploads/". $screen['value'] .".".$screen_file['extension'] ); ?>" style="max-width:80px; max-height:80px;" />
								<?php }} ?>
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
			{ width: "1px", targets: [1] },
		],
		ordering: false
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