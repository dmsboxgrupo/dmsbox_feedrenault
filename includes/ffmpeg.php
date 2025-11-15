<?php

class FFMPEG {
	
	public $ffmpeg;
	public $ffprobe;
	public $codec = '';
	
	public function __construct() {
		
		if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
			
			$path = 'D:\\ffmpeg\\bin\\';
			
			$this->ffmpeg = $path . 'ffmpeg.exe';
			$this->ffprobe = $path . 'ffprobe.exe';
			
		} else {
			
			$path = '';
			
			$this->ffmpeg = $path . 'ffmpeg';
			$this->ffprobe = $path . 'ffprobe';
			
			$this->codec = '-strict -2 -c:a aac -vcodec libx264 -acodec aac';
			
		}
		
    }
	
	public function convert_cmd($source, $path, $arguments='') {
		
		// video path
		$source = escapeshellarg( realpath($source) );
		
		// convert to path
		$target = escapeshellarg( $path );
		
		// command
		$ffmpeg = escapeshellarg( $this->ffmpeg );
		$command = "$ffmpeg -y -i $source {$this->codec} {$arguments} $target";
		
		return $command;
		
	}
	
	public function convert($source, $path, $arguments='') {
		
		shell_exec( $this->convert_cmd($source, $path, $arguments) );
		
		return (bool)realpath($path) ? filesize($path) > 0 : false;
		
	}
	
	public function snapshot_cmd($source, $path, $seek, $width, $height, $arguments='') {
		
		// video path
		$source = escapeshellarg( realpath($source) ); 
		
		// image path
		$target = escapeshellarg( $path ); 
		
		// command
		$ffmpeg = escapeshellarg( $this->ffmpeg );
		$command = "$ffmpeg -y -i $source -ss $seek -f image2 -s {$width}x{$height} -vframes 1 {$arguments} $target";
		
		return $command;
		
	}
	
	public function snapshot($source, $path, $seek, $width, $height, $arguments='') {
		
		shell_exec( $this->snapshot_cmd($source, $path, $seek, $width, $height, $arguments) );

		return (bool)realpath($path) ? filesize($path) > 0 : false;
		
	}
	
	public function info($filename) {
		
		$ffprobe = escapeshellarg( $this->ffprobe );
		$filename = escapeshellarg( realpath($filename) );
		$options = '-loglevel quiet -show_format -show_streams -print_format json';
		$command = "$ffprobe $options $filename";
		
		$output = json_decode( shell_exec($command), true );
		
		if (isset($output['streams'])) {
			
			$streams = $output['streams'];
			
			foreach($streams as &$stream) {
			
				if ((int)@$stream['width'] > 0 && (int)@$stream['height'] > 0) {
				
					// confirm if it is not an image
					if (isset($stream['duration'])) {
						
						$width = (int)$stream['width'];
						$height = (int)$stream['height'];
						
						if (isset($stream['tags']) && (int)@$stream['tags']['rotate'] != 0) {
							
							// auto dection portrait
							
							$temp = $width;
							$width = $height;
							$height = $temp;
							
						}
						
						return array(
							'codec' => $stream['codec_name'],
							'duration' => (double)$stream['duration'],
							'width' => $width,
							'height' => $height
						);
						
					}
					
				}
				
			}
		
		}
		
	}

}

?>