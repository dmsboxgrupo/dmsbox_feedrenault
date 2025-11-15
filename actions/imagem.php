<?php
	$data = str_replace('data:image/png;base64,', '', $_POST['img']);
	$data = str_replace(' ', '+', $data);
	$data = base64_decode($data);

	$img = imagecreatefromstring($data);
	$nome = md5(uniqid(rand(), true));

	while (is_file('../download/img/'.$nome.'.jpg')) {
	    $nome = md5(uniqid(rand(), true));
	}
	if (!is_dir('../uploads/media/image')) {
	    @mkdir('../uploads/media/image');
	}
	if (! @imagejpeg ( $img, '../download/img/'.$nome.'.jpg' ) ) {
	    @chmod('../uploads/media/image', 0755);
	    @imagejpeg ( $img, '../download/img/'.$nome.'.jpg' );
	}

	echo json_encode(array('name'=>$nome));
?>