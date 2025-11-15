<?php

define('LEVEL_MASTER', 1);
define('LEVEL_COMMON', 2);

class UserAdmin {
	
	private $level = 0;
	private $username = '';
	private $bir = '';
	private $id = 0;

	public function __construct() {
		
		global $session;
		
		if ($session->has('a_username')) {
			
			$this->login(
				$session->get('a_username'),
				$session->get('a_password')
			);
			
		}
		
    }
	
	public function get_property($name) {
		
		//return $this->data[ $name ];
		return $this->{$name};
	}
	
	public function login($username='', $password='') {
		
		global $session, $db;
		
		$this->$username = '';
		
		// na base de dados Master e Gerentes
		
		$user = current( $db->query("SELECT * FROM `users` 
				WHERE status=1 and level in (1, 2) and email='{$username}' and password='{$password}' LIMIT 1") );
		
		if($user){			
			$this->username = $user['name'];
			$this->level = $user['level'];
			$this->bir = $user['bir'];
			$this->id = $user['id'];
		}

		if ($this->logged()) {
			
			$session->set('a_username', $username);
			$session->set('a_password', $password);
			
		} else {
			
			$session->unset('a_username');
			$session->unset('a_password');
			
		}
		
		return $this->logged();
		
	}
	
	public function logout() {
		
		$this->login();
		
	}
	
	public function is_level($level) {

		return ($this->level <= $level || empty($this->bir));
		
	}

	public function level() {
		
		return $this->level;
		
	}
	
	public function bir() {
		
		return $this->bir;
		
	}
	
	public function manager_accesses() {
		
		global $db;
		
		return $db->get_metadata_value( "manager_accesses" );;
		
	}

	public function logged() {
		
		return strlen($this->username) > 0;
		
	}
}

?>