<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>Result</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" href="style.css" type="text/css" media="all" />
</head>
<body>
<?php

function normalize_size($size) {
	if ($size > 1024) {
		$size = $size / 1024;
	} else {
		$size .= 'B';
		return $size;
	}
	
	if ($size > 1024) {
		$size = $size / 1024;
	} else {
		$size = round($size, 2) . 'K';
		return $size;
	}
	
	$size = round($size, 2) . 'M';
	return $size;
}


$session_id = $_GET['id'];
if (!is_numeric($session_id)) {
	echo 'ERROR: Invalid session';
} else {
	$mobi = @filesize('result/'.$session_id.'/result.mobi');
	$gpi = @filesize('result/'.$session_id.'/result.gpi');
	$gpx = @filesize('result/'.$session_id.'/result.gpx');
	$html = @filesize('result/'.$session_id.'/result.html');
	
	if ($mobi === false) {
		$mobi = 'in progress...';
	} else {
		$mobi = normalize_size($mobi);
	}
	
	if ($gpi === false) {
		$gpi = 'in progress...';
	} else {
		$gpi = normalize_size($gpi);
	}
	
	if ($gpx === false) {
		$gpx = 'in progress...';
	} else {
		$gpx = normalize_size($gpx);
	}
	
	if ($html === false) {
		$html = 'in progress...';
	} else {
		$html = 'done';
	}
	
	echo '
	<a href="result/'.$session_id.'/result.mobi">.mobi ('.$mobi.')</a>
	<a href="result/'.$session_id.'/result.gpi">.gpi ('.$gpi.')</a>
	<a href="result/'.$session_id.'/result.gpx">.gpx ('.$gpx.')</a>
	<a href="result/'.$session_id.'/result.html">.html ('.$html.')</a>
	';
	
	echo '<pre>';
	echo file_get_contents('result/'.$session_id.'/log.txt');
	echo '</pre>';
	
	echo '<hr/>';
	
	echo '<pre>';
	$file = escapeshellarg('result/'.$session_id.'/wget.log');
	$line = `tail -n 10 $file`;
	echo $line;
	echo '</pre>';
}
?>
</body>
</html>