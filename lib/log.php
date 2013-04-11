<?php

function logg($session_id, $text) {
	echo $text . "\n";
	file_put_contents('result/'.$session_id.'/log.txt', $text . "\n", FILE_APPEND);
}

?>