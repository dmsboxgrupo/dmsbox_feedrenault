<?php

/**
 * API
 *
 * @author: Jean Carlo Deconto ( 2019 )
 *
 * (C) SUNAG - www.sunag.com.br / contact@sunag.com.br
**/

class WebAdmin {
	
	public $title = TITLE;
	public $description = DESCRITION;
	public $language = 'pt-br';
	
	public $logged = false;
	public $footer = false;
	public $overflow = true;
	public $container = true;
	
	public $parent_list = array();
	public $plugin_list = array();
	
	public $button_list = array();
	
	public $page = array();
	
	private $plugins_library = array(
		'datatables' => array(
			'admin/plugins/datatables/DataTables-1.10.18/css/dataTables.bootstrap4.css',
			'admin/plugins/datatables/datatables.min.js',
			'admin/plugins/datatables-sort/datatables.sort.js',
			'admin/plugins/datatables-rowreorder/rowReorder.dataTables.min.css',
			'admin/plugins/datatables-rowreorder/dataTables.rowReorder.min.js',
			'admin/plugins/datatables-css/style.css'
		),
		'mask' => array(
			'plugins/jquery.mask/jquery.mask.js'
		),
		'datetimepicker' => array(
			// Plugin JavaScript
			'admin/template/assets/plugins/bootstrap-material-datetimepicker/css/bootstrap-material-datetimepicker.css',
			'admin/template/assets/plugins/moment/moment.js',
			'admin/template/assets/plugins/bootstrap-material-datetimepicker/js/bootstrap-material-datetimepicker.js',
			// Clock Plugin JavaScript
			'admin/template/assets/plugins/clockpicker/dist/jquery-clockpicker.min.css',
			'admin/template/assets/plugins/clockpicker/dist/jquery-clockpicker.min.js',
			// Color Picker Plugin JavaScript
			'admin/template/assets/plugins/jquery-asColorPicker-master/css/asColorPicker.css',
			'admin/template/assets/plugins/jquery-asColorPicker-master/libs/jquery-asColor.js',
			'admin/template/assets/plugins/jquery-asColorPicker-master/libs/jquery-asGradient.js',
			'admin/template/assets/plugins/jquery-asColorPicker-master/dist/jquery-asColorPicker.min.js',
			// Date Picker Plugin JavaScript
			'plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css',
			'plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js',
			'plugins/bootstrap-datepicker/dist/locales/bootstrap-datepicker.pt-BR.min.js',
			// Date range Plugin JavaScript
			'admin/template/assets/plugins/timepicker/bootstrap-timepicker.min.css',
			'admin/template/assets/plugins/daterangepicker/daterangepicker.css',
			'admin/template/assets/plugins/timepicker/bootstrap-timepicker.min.js',
			'admin/template/assets/plugins/daterangepicker/daterangepicker.js',
			// Custom
			'plugins/daterangepicker-locale/pt-br.js'
		),
		'select2' => array(
			'admin/template/assets/plugins/select2/dist/css/select2.min.css',
			'admin/template/assets/plugins/select2/dist/js/select2.full.min.js'
		),
		'dropzone' => array(
			'assets/plugins/dropzone-master/dist/dropzone.css',
			'assets/plugins/dropzone-master/dist/dropzone.js'
		),
		'redirect' => array(
			'admin/plugins/jquery.redirect/jquery.redirect.js',
		),
	);
	
	public function __construct() {
    }
	
	public function contains_plugin($name) {
		
		return in_array($name, $this->plugin_list);
		
	}
	
	public function set_page($page) {
		
		$this->page = $page;
		
	}
	
	public function add_parent($page) {

		array_push($this->parent_list, $page);
		
	}
	
	public function add_button($button) {
		
		array_push($this->button_list, $button);
		
	}

	public function add_plugins() {

		$this->plugin_list = array_merge($this->plugin_list, func_get_args());
		
	}
	
	public function css($url, $id='') {

		?><link href="<?php echo url($url); ?>" <?php if ($id) echo "id=\"$id\" "; ?>rel="stylesheet" type="text/css">
		<?php
		
	}

	public function script($url) {
		
		?><script src="<?php echo url($url); ?>"></script>
		<?php
		
	}

	public function start($class='blue-theme') {
		
		global $useradm;
		
		content_type('text/html');
	
		if (!$this->overflow) $class .= ' overflow-hidden';
	
		include('admin/html/start.php');

	}
	
	public function start_panel( $class='blue-theme' ) {
		
		$this->start( $class );
		
		//include('admin/html/hierarchy.php');
		
	}

	public function plugins_format($format) {
		
		foreach ($this->plugin_list as &$plugin) {
			
			$plugins = $this->plugins_library[ $plugin ];
			
			foreach ($plugins as &$url) {
				
				$extension = pathinfo($url, PATHINFO_EXTENSION);
				
				if ($extension == $format) {

					if ($extension === 'js') $this->script($url);
					elseif ($extension === 'css') $this->css($url);

				}
				
			}
			
		}
		
	}
	
	public function end() {
		
		global $useradm;
		
		include('admin/html/end.php');
		
	}
	
	public function end_panel() {
		
		$this->end();
		
	}

}

?>