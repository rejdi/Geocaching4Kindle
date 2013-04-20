<?php

function logg($session_id, $text) {
	$date = new DateTime();
	$result = '[' . ($date->format('d.m.Y H:i:s.u')) . '] '. $text . "\n";
	echo $result;
	file_put_contents('result/'.$session_id.'/log.txt', $result, FILE_APPEND);
}

?>