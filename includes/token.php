<?php

class Token {
	
	public function generate($duration=0, $uid=null) {
		
		global $db;
		
		$id = $db->insert('tokens', array(
			'duration' => $duration,
			'uid' => $uid
		), array(
			'uuid' => 'UUID()'
		));
		
		$token = $db->select_id('tokens', $id);
		
		return $token['uuid'];
		
	}
	
	public function get($uuid) {
		
		global $db;
		
		return current( $db->query("SELECT * FROM `tokens` WHERE `uuid` = :uuid", array( 'uuid' => $uuid )) );
		
	}
	
	public function is_valid($token) {
		
		return $token['status'] == 1 && ( $token['duration'] == 0 || time() <= strtotime($token['date']) + $token['duration'] );
		
	}
	
	public function getFromNameUID($uid, $duration=0) {
		
		global $db;
		
		$token = current( $db->query("SELECT * FROM `tokens` WHERE `uid` = :uid", array( 'uid' => $uid )) );
		
		if ($token) {
			
			return $token['uuid'];
			
		} else {
			
			return $this->generate($duration, $uid);
			
		}
		
	}
	
	public function burn($uuid) {
		
		global $db;
		
		$token = $this->get($uuid);
		
		if ($token && $this->is_valid($token)) {
			
			$db->update('tokens', $token['id'], array(
				'status' => 0,
				'uid' => null
			));
			
			return true;
			
		}
		
		return false;
		
	}
	
}

?>