<?php

/**
 * @author: Jean Carlo Deconto ( 2019 )
 *
 * (C) SUNAG - www.sunag.com.br / contact@sunag.com.br
**/

class DataBase extends PDO {
	
	public function __construct($host, $databank, $username, $password) {
		
		parent::__construct("mysql:host=$host;dbname=$databank;charset=utf8mb4", $username, $password);
		
		//$this->exec("SET CHARACTER SET utf8mb4");
		//$this->exec("SET NAMES utf8 COLLATE utf8mb4_unicode_ci");
		//$this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
		$this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
    }

	public function update($table, $id, $params, $alt_params=null) {
		
		$properties = array();
		
		foreach ($params as $key => $val) {
			
			array_push($properties, "`$key` = :$key");
			
		}
		
		if ($alt_params) {
		
			foreach ($alt_params as $key => $val) {
				
				array_push($properties, "`$key` = {$alt_params[$key]}");
				
			}
			
		}
		
		//$properties = join($properties, ', ');
		$properties = implode (', ',$properties);
		$where = "`id` = $id";
		
		$sql = "UPDATE `$table` SET $properties WHERE $where";
		
		$query = $this->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
		$result = $query->execute($params);
		
	}
	
	public function select_id($table, $id, $select='*', $where='') {
		
		if ( is_array($id) ) {
			
			$output = array();
			
			foreach($id as $i_id) {
				
				array_push($output, $this->select_id($table, $i_id, $select, $where));
				
			}
			
			return $output;
			
		}
		
		$id = (int)$id;
		$sql = "SELECT $select FROM `$table` WHERE `id` = $id";
		
		if ($where) $sql .= " AND $where";
		
		$query = $this->prepare($sql);
		$query->execute();

		return $query->fetch(PDO::FETCH_ASSOC);
		
	}
	
	public function remove($table, $id) {
		
		$id = (int)$id;
		$sql = "DELETE FROM `$table` WHERE `id` = $id";
		
		$query = $this->prepare($sql);
		$query->execute();

		return $query->fetch(PDO::FETCH_ASSOC);
		
	}
	
	public function insert($table, $params, $alt_params=null) {
		
		$properties = array();
		$values = array();
		
		foreach ($params as $key => $val) {
			
			array_push($properties, "`$key`");
			array_push($values, ":$key");
			
		}
		
		if ($alt_params) {
		
			foreach ($alt_params as $key => $val) {
				
				array_push($properties, "`$key`");
				array_push($values, $alt_params[$key]);
				
			}
			
		}
		
		$properties = join(', ', $properties);
		$values = join(', ', $values);
		
		$sql = "INSERT INTO `$table` ( $properties ) VALUES ( $values )";//; SELECT @@IDENTITY AS lastId;
		//txtLog('_db_'," execquery=$sql");
		
		try {
			$query = $this->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
			$result = $query->execute($params);
		} catch(PDOException $e){			
			$msgerr = $e->getMessage();			
			//txtLog('_db_',"ERRO:  ".$e->getMessage());			
			//if($e->errorInfo[1] === 1062) echo 'Duplicate entry';
		}
		
		if (isset($result)) {
			return $this->lastInsertId();
		}else
			return 0;
	}

	public function query($sql, $params=null) {
		
		$query = $this->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
		$query->execute($params);
		
		return $query->fetchAll(PDO::FETCH_ASSOC);
		
	}
	
	public function call() {
		
		$params = func_get_args();
		$method = array_shift($params);
		
		$count = count($params);
		
		$sql = "CALL $method( ";
		$sql .= str_repeat("?, ", $count - 1);
		if ($count > 0) $sql .= "?";
		$sql .= " )";
		
		$query = $this->prepare($sql);
		$query->execute($params);
		
		return $query->fetchAll(PDO::FETCH_ASSOC);
		
	}

}

?>