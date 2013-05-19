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
	$loc = @filesize('result/'.$session_id.'/result.loc');
	$gpx = @filesize('result/'.$session_id.'/result.gpx');
	
	
	if ($mobi === false) {
		$mobi = 'in progress...';
	} else {
		$mobi = normalize_size($mobi);
	}
	
	if ($loc === false) {
		$loc = 'in progress...';
	} else {
		$loc = normalize_size($loc);
	}
	
	if ($gpx === false) {
		$gpx = 'in progress...';
	} else {
		$gpx = normalize_size($gpx);
	}
	
	echo '
	<a href="result/'.$session_id.'/result.mobi">.mobi ('.$mobi.')</a>
	<a href="result/'.$session_id.'/result.loc">.loc ('.$loc.')</a>
	<a href="result/'.$session_id.'/result.gpx">.gpx ('.$gpx.')</a>
	';
	
	echo '<pre>';
	echo file_get_contents('result/'.$session_id.'/log.txt');
	echo '</pre>';
}
?>
</body>
</html>