<?php

class Uploader {

	public $path = 'uploads';

	public function get( $id ) {
		
		global $db;
		
		$file_db = $db->select_id( 'uploads', $id );
		
		if ($file_db) {
			
			$file_db['url'] = $this->url( $id, $file_db['extension'] );

		}
		
		return $file_db;
		
	}
	
	public function filename( $id ) {
		
		$file = $this->get( $id );
		
		return $file ? basename( $file['url'] ) : null;
		
	}
	
	public function url( $id, $extension='bin' ) {
		
		return "{$this->path}/{$id}.{$extension}";
		
	}

	// renomear para -> upload_path
	public function upload_url( $id, $extension='bin', $path='' ) {

		return $_SERVER['DOCUMENT_ROOT'] . dirname( $_SERVER['PHP_SELF'], 2 ) . "$path/uploads/" . $id . "." . $extension;
		
	}
	
	function remote_filesize($url) {
		static $regex = '/^Content-Length: *+\K\d++$/im';
		if (!$fp = @fopen($url, 'rb')) {
			return false;
		}
		if (
			isset($http_response_header) &&
			preg_match($regex, implode("\n", $http_response_header), $matches)
		) {
			return (int)$matches[0];
		}
		return strlen(stream_get_contents($fp));
	}

	public function upload_content( $url ) {
		
		//name - veiculo6.png
		$name  = basename($url);
		
		//$extension = $type;		
		//$extension = pathinfo($name, PATHINFO_EXTENSION);
		
		//extension
		if ($url_p = parse_url($url)) { 
		   $extension = pathinfo($url_p['path'], PATHINFO_EXTENSION);
		}
		
		//type - image/png
		if($extension == 'mp4')$type ="video/$extension";
		else $type ="image/$extension";
		
		//size - tamanho
		$size = $this->remote_filesize($url);
		//$size=10;
		global $db;
	
		//$extension = strtolower( pathinfo($file['name'], PATHINFO_EXTENSION) );
	
		$id = $db->insert('uploads', array(
			'name' => $name,
			'type' => $type,
			'size' => $size,
			'extension' => $extension
		));
		
		//$url="http://www.google.co.in/intl/en_com/images/srpr/logo1w.png";
		$contents=file_get_contents($url);
		
		
		$save_path="uploads/{$id}.{$extension}";

		if ( file_put_contents($save_path, $contents)) {		
			
			return $id;
			
		} else {
			
			return 0;
			// TODO: remove database file			
		}
	}

	public function upload( $file, $path = "" ) {
	
		global $db;
	
		$extension = strtolower( pathinfo($file['name'], PATHINFO_EXTENSION) );
	
		$id = $db->insert('uploads', array(
			'name' => $file['name'],
			'type' => $file['type'],
			'size' => $file['size'],
			'extension' => $extension
		));
		//echo "teste= ".$file['tmp_name'];
		//echo "teste1= ".$this->upload_url( $id, $extension );//die();
		if ( move_uploaded_file($file['tmp_name'], $this->upload_url( $id, $extension, $path )) ) {		
			
			return $id;
			
		} else {
			
			return 0;
			// TODO: remove database file
			
		}
		
	}
	
	public function upload_webp( $file ) {
	
		global $db;
	
		$extension = strtolower( pathinfo($file['name'], PATHINFO_EXTENSION) );
	
		$id = $db->insert('uploads', array(
			'name' => $file['name'],
			'type' => $file['type'],
			'size' => $file['size'],
			'extension' => $extension
		));
		//echo "teste= ".$file['tmp_name'];
		//echo "teste1= ".$this->upload_url( $id, $extension );//die();
		
		
		
		$file = $file['tmp_name'];

		$image = imagecreatefromstring(file_get_contents($file));

		ob_start();

		imagejpeg($image,NULL,100);

		$cont = ob_get_contents();

		ob_end_clean();

		imagedestroy($image);

		$content = imagecreatefromstring($cont);

		//$output = 'log/output.webp';
		//$this->upload_url( $id, $extension )
		$output = $this->upload_url( $id, "webp" );
		
		imagewebp($content,$output);

		imagedestroy($content);
		
		
		
		
		/*if ( move_uploaded_file($file['tmp_name'], $this->upload_url( $id, $extension )) ) {		
			
			return $id;
			
		} else {
			
			return 0;
			// TODO: remove database file
			
		}*/
		
	}
	

}

?>