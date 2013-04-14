<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>Result</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" href="style.css" type="text/css" media="all" />
</head>
<body>
<?php
$session_id = $_GET['id'];
if (!is_numeric($session_id)) {
	echo 'ERROR: Invalid session';
} else {
	echo '
	<a href="result/'.$session_id.'/result.mobi">.mobi</a>
	<a href="result/'.$session_id.'/result.loc">.loc</a>
	<a href="result/'.$session_id.'/result.gpx">.gpx</a>
	';
	
	echo '<pre>';
	echo file_get_contents('result/'.$session_id.'/log.txt');
	echo '</pre>';
}
?>
</body>
</html>