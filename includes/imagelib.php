<?php

class ImageLib {
	
	public function resize($source, $target, $width, $height) {
		
		include_once('includes/thirdparties/imagelib/WideImage.php');
		
		$img = WideImage::load($source);
		$img->resize($width, $height, 'outside', 'down')->crop('center', 'center', $width, $height)->saveToFile($target);
		
	}

}

?>