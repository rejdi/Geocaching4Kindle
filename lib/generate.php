<?php
require_once 'lib/log.php';
require_once 'lib/login.php';
require_once 'lib/fetch.php';
require_once 'lib/kindle.php';
require_once 'lib/gpx.php';
require_once 'lib/loc.php';

if ($argc > 0) {
	$settings = unserialize($argv[1]);
	if ($settings == false) {
		exit(1);
	}
}

/*
 
$settings = array of
 $session = integer
 $user = string
 $pass = string
 $type = list | point
 $codes = array of string
 
 $point = array of
	$type = town | location
	$city = string
	$locLong = double
	$locLat = double
 
 $pointFilter = array of
	$limitCount = integer
	$limitDistance = double
	$filterType = <zoznam typov kesi>
	$difficultyMin = double
	$difficultyMax = double
	$terrainMin = double
	$terrainMax = double
	$notFound = boolean

 $outputKindle = array of
	$withImages
	$tocName
	$tocDifficulty
	$tocTerrain
	$meta
	$shortDesc
	$longDesc
	$hints
	$solveHint
	$attributes
	$logs = integer <0,10>
	$additionalWaypoints
	$map
*/
$session_id = $settings['session'];

system('rm -rf result/'.$session_id);
mkdir('result/'.$session_id);
//logg($session_id, print_r($settings, true));
logg($session_id, 'Logging in...');
$cookiefile = login($settings['user'], $settings['pass'], $session_id);

if (!empty($cookiefile)) {
	logg($session_id, 'Logged in successfully');
} else {
	logg($session_id, 'Failed to login');
	exit(1);
}

if ($settings['type'] == 'list') {
	$codes = $settings['codes'];
	if (empty($codes)) {
		logg($session_id, 'Empty list, nothing to do.');
		exit(0);
	}
	
	$process = array();
	foreach ($codes as $code) {
		logg($session_id, 'Downloading... ' . $code);
		$result = fetchPrint($cookiefile, $code, (boolean)$settings['outputKindle']['withImages'], 10);
		if (empty($result)) {
			logg($session_id, 'Failed to download ' . $code . ', skipping ...');
			continue;
		}
		$process[] = $result;
	}
	//TODO: processing faza
	createKindle($session_id, $process, $settings['outputKindle']);
	createGPX($session_id, $process, $settings['outputGPX']);
	createLOC($session_id, $process, $settings['outputLOC']);
} else if ($settings['type'] == 'point') {
	//TODO
}

?>
