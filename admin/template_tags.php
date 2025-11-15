<?php

// API

include('includes/common.php');

// Codigo da pagina
need_login();

//parametros
$category = (int)get('category');
$content_id = (int)get('content_id');

$par_content = ($content_id>0)?"content_id={$content_id}&":"";

// Codigo da pagina

need_login();

//$meta_property = $list_group_tags[$group]['tags'];
$meta_property = 'highlighted_backgrounds_tags';

$par_content_type = ($category == 8)? $par_content_type ="&type=2" : "";

//whatsapp_galeries_tags
//campaigns_tags

//METADATA
$metadata_id = $db->get_metadata_id( $meta_property );
if($metadata_id=="") $metadata_id = $db->set_metadata( $meta_property );

if ($metadata_id > 0) {
	
	$content = $db->content( $db->select_id('metadata', $metadata_id), 'metadata' );

	$arr_tags_templates = $db->query("SELECT tags.id as tag_id, tags.name, property as template_type, FIND_IN_SET(tags.id, metadata.value)  as ordem
											FROM `tags`
											left join metadata on property='highlighted_backgrounds_tags' and FIND_IN_SET(tags.id, metadata.value) 
											join templates on FIND_IN_SET(tags.id, templates.tags)  
											WHERE tags.status=1 and tags.category=7
											group by tags.id, tags.name
											order by ordem");
	
	$arr_tags_templates_idxs = array_column($arr_tags_templates, 'id');
	
	if (has('post')) {
		
		
		//$tags_novas = get_post('tags');
		$tags_novas = empty(get_post('tags'))?"":implode(",", get_post('tags'));
		if(!empty(get_post('tags_values'))) $tags_novas =get_post('tags_values');
		
		//$content['colors'] = empty(get_post('colors'))?"":implode(",", get_post('colors'));
		//if(!empty(get_post('colors_values'))) $content['colors'] =get_post('colors_values');
		
		
		//print_r($tags_novas); die();
		
		//$db->update('metadata', $metadata_id, $json);
		
		$db->update('metadata',  $metadata_id, array(
			'value' => $tags_novas
		));
		
		$_SESSION['message'] = 'Registro alterado com sucesso.';	    
		$_SESSION['type'] = 'success';
		
		redirect("template_tags.php");
		
		/*
		$json = array();
		
		if (has_post('value')) {
			
			$tags = get_post('value');
			
			$json['value'] = $tags;
			
		}
		
		$db->update('metadata', $metadata_id, $json);
		
		echo json_encode( $json, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT );
		
		$_SESSION['message'] = 'Registro alterado com sucesso.';	    
		$_SESSION['type'] = 'success';
		
		exit;*/
		
	}

}

//print_r($content); die();

// Inicia HTML

$webadm->set_page( array( 'name' => "Tags de Destaque" ) );
$webadm->add_parent( array( 'name' => "Backgrounds", 'url' => "templates.php"));

//if(!$is_new)$webadm->add_button( array( 'name' => 'Textos', 'icon' => 'mdi mdi-format-list-bulleted-type', 'url' => "template_texts.php?type={$type}&template_id={$id}" ) );
$webadm->add_button( array( 'name' => 'Gerenciar tags', 'icon' => 'mdi mdi-tag-multiple', 'url' => "tags.php?category=7" ) );

$webadm->add_plugins('quilljs', 'dropify', 'select2','tagsinput');
$webadm->start_panel();



?>
<!-- ============================================================== -->
<!-- Start Page Content -->
<!-- ============================================================== -->

<style>
#editor-container, #editor-container2, #editor-container3{
	width: 100%;
	height: calc(100vh - 777px);
}

.ql-bubble .ql-tooltip {
  z-index: 1;
}

.dropify-wrapper{
	width: 100%;
	height: calc(50vh - 220px);
}

#thumb .dropify-wrapper{
	width: 100%;
	height: calc(50vh - 220px);
}


.cor1 {
    background-color: green;

}
.cor2 {
    background-color: orange;

}
.cor3 {
    background-color: pink;
	
}


#color_me{
  width:250px;   
}

#estilo1 { font-size: 130%; line-height: 120%; font-weight: normal; }
#estilo2 { font-size: 130%; line-height: 120%; font-weight: bold; }
#estilo3 { font-size: 180%; line-height: 120%; font-weight: normal; }
#estilo4 { font-size: 180%; line-height: 120%; font-weight: bold; }
#estilo5 { font-size: 230%; line-height: 120%; font-weight: normal; }
#estilo6 { font-size: 230%; line-height: 120%; font-weight: bold; }
#estilo7 { font-size: 280%; line-height: 100%; font-weight: bold; background-color: green;}

.bootstrap-tagsinput {
	width: 100%;
    min-height: 38px;
    line-height: 30px;
}
</style>

<form action="<?php echo "?post"; ?>" method="post" enctype="multipart/form-data">
	<div class="card no-margin">
		<div class="card-header">
			<div class="row">
				<div class="col-md">
					<div class="col-md-12">
						<input name="name" type="text" class="form-control" placeholder="Tags de Destaque" readonly />
					</div>
				</div>
				<button id="save" type="submit" class="btn btn-info"><i class="fa fa-check m-r-10"></i>Salvar</button>
			</div>
		</div>
		<div class="card-body">
			<div class="row">
				
				<div class="col-md-6">
					
					
					
					<div class="form-group m-b-10">
						<label>Tags</label>
						<select id="tags" class="form-control col-md-12 select2" name="tags[]" multiple="multiple">

							<?php foreach($arr_tags_templates as $tag) {
								//if(in_array($tag, $arr_tags_templates_idxs)){
									
									$sel = (strpos($content['value'], $tag['tag_id']) !== false)? "selected":"";
								?>
									<option <?php echo $sel ?> value="<?php echo html($tag['tag_id']); ?>" $sel ><?php echo html($tag['name']); ?></option>
								
								<?php //}
								} ?>
							
						</select>
					</div>
					
					<input type="hidden" name="tags_values" id="tags_values" value="<?php echo html($content['value']); ?>" />
					
					<!--
					<div class="form-group m-b-10">
						<label>Tags de Busca</label>
							<input data-role="tagsinput" name="search_tags" type="text" 
							placeholder="Digite as Tags de Busca" value="?php echo html($content['search_tags']); ?>" />
					</div>
					-->
					
					
					
					
				</div>
				
			</div>
		
			
		</div>
	</div>
</form>

<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<script>
$(function() {

$("#tags").select2({
		placeholder: "Selecione a(s) Tag(s)"
	}).on('change', function (e) {
		//var str = $("#s2id_search_code .select2-choice span").text();
		//alert('teste');
		//DOSelectAjaxProd(this.value, str);
		//this.value
		//$('#colors').sortable();
		
		var arr = $("#tags").val().join();
		
		//alert(arr);
		$("#tags_values").val(arr);
		//$("#colors_values").val(arr.map(x =>x.value).join());
		
	});
	


	$("ul.select2-selection__rendered").sortable({
		
	  containment: 'parent',
	  stop: function(event, ui) {

		var arr = Array.from($(event.target).find('li:not(.select2-search)').map(function () { 
			return {name: $(this).data('data').text, value: $(this).data('data').id }; 
		}))
		
		//alert($("#teste").value);
		
		$("#tags_values").val(arr.map(x =>x.value).join());
		
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