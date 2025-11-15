<?php

class Session {
	
	public $name;
	public $expire;	
	public $data = array();

	public function __construct($name, $expire) {
		
		$this->name = $name;
		$this->expire = $expire;

		session_name($this->name);
		session_start(array(
			'cookie_lifetime' => $this->expire
		));

    }
	
	public function has($property) {
		
		return isset($_SESSION[$property]);
		
	}
	
	public function get($property) {
		
		return $_SESSION[$property];
		
	}
	
	public function set($property, $value) {

		$_SESSION[$property] = $value;
		
	}
	
	public function clear($destroy=false) {
		
		session_unset();
		
		if ($destroy) {
			
			session_destroy();
			
		}

	}
	
	public function unset($property) {
		
		unset($this->data[$property]);

	}
	
	public function destroy() {
		
		$this->clear(true);
		
	}

}

?>