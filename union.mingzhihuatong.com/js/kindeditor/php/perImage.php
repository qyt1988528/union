<?php
function perImage($filename, $dst_w, $flag = true, $destination = "uploads") {
	$file = getimagesize ( $filename );
    list ( $src_w, $src_h, $type ) = $file;
    $percent=$dst_w/$src_w;
    $dst_h=$src_h*$percent;
	$mime = $file ['mime'];
	$createFunc = str_replace ( "/", "createfrom", $mime );
	$outputFunc = str_replace ( "/", "", $mime );
	$src_image = $createFunc ( $filename );
	$dst_image = imagecreatetruecolor ( $dst_w, $dst_h );
	imagecopyresampled ( $dst_image, $src_image, 0, 0, 0, 0, $dst_w, $dst_h, $src_w, $src_h );
	if ($flag) {
		header ( "content-type:" . $mime );
		$outputFunc ( $dst_image );
	} else {
		$outputFunc ( $dst_image, $destination);
	}
	imagedestroy ( $src_image );
	imagedestroy ( $dst_image );
}
