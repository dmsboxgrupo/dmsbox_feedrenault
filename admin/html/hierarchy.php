<!-- ============================================================== -->
<!-- Bread crumb and right sidebar toggle -->
<!-- ============================================================== -->
<div class="row page-titles">
	<div class="col-md-5 col-8 align-self-center">
		<ol class="breadcrumb">
		
			<?php foreach ($this->parent_list as &$page) : ?>
			
				<li class="breadcrumb-item"><a href="<?php echo html(@$page['url']); ?>"><?php echo html($page['name']); ?></a></li>
				
			<?php endforeach; ?>
			
			<li class="breadcrumb-item active"><?php echo html( $this->page['name'] ); ?></li>
			
		</ol>
	</div>
	<div class="col-md-7 col-4 align-self-center m-r-0">
		<div class="d-flex m-t-3 justify-content-end">
			<?php foreach ($this->button_list as &$button) : ?>
		
			<a class="inserir btn btn-warning btn-rounded text-uppercase m-l-15" <?php 
			
				if (@$button['url']) { 
				
					echo 'href="' . html( $button['url'] ) . '" ';
				
				}
			
				if (@$button['attribs']) {
					
					foreach($button['attribs'] as $key => $value) {
						
						echo $key . '="' . html( $value ) . '" ';
						
					}
					
				}
			
			?>>
				<i class="<?php echo html( @$button['icon'] ); ?> m-r-5"></i>
				<?php echo html( $button['name'] ); ?>
			</a>
			
			<?php endforeach; ?>
			
			<?php if ( $this->contains_plugin('intro') ) : ?>
			
			<a class="inserir btn btn-help btn-rounded" onclick="intro()">
				<i class="mdi mdi-help"></i>				
			</a>
			
			<?php endif ?>
			
		</div>
	</div>
</div>
<!-- ============================================================== -->
<!-- End Bread crumb and right sidebar toggle -->
<!-- ============================================================== -->
