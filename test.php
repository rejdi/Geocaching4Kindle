<?php

require_once 'lib/log.php';
require_once 'lib/login.php';

$session_id = '12345'; //inak random <0 - 50> alebo nejaky rotator

system('rm -rf result/'.$session_id);
mkdir('result/'.$session_id);
logg($session_id, 'Logging in...');
$cookiefile = login($login, $pass, $session_id);

if (!empty($cookiefile)) {
	logg($session_id, 'Logged in successfully');
} else {
	logg($session_id, 'Failed to login');
}



?>
