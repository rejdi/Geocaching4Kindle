<?php
require_once 'lib/log.php';
require_once 'lib/login.php';
require_once 'lib/fetch.php';
require_once 'lib/intermediate.php';
require_once 'lib/kindle.php';
require_once 'lib/gpx.php';
require_once 'lib/loc.php';

if ($argc > 0) {
	$settings = unserialize($argv[1]);
	if ($settings == false) {
		exit(1);
	}
} else {
	exit(1);
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
	$cacheType = array of string <zoznam typov kesi> (v tomto pripade imagov)
	$difficultyMin = double
	$difficultyMax = double
	$terrainMin = double
	$terrainMax = double
	$notFound = boolean
	$onlyActive = boolean
	$skipPremium = boolean

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
// logg($session_id, print_r($settings, true));
// logg($session_id, $argv[1]);
logg($session_id, 'Logging in...');
$cookiefile = login($settings['user'], $settings['pass'], $session_id);

if (!empty($cookiefile)) {
	logg($session_id, 'Logged in successfully');
} else {
	logg($session_id, 'Failed to login');
	exit(1);
}

$process = array();
if ($settings['type'] == 'list') {
	$codes = $settings['codes'];
	if (empty($codes)) {
		logg($session_id, 'Empty list, nothing to do.');
		exit(0);
	}
	
	$i = 1;
	foreach ($codes as $code) {
		logg($session_id, 'Downloading... (' . $i . '/' . count($codes) . ') ' . $code);
		$i++;
		$result = fetchPrint($session_id, $cookiefile, $code);
		if (empty($result)) {
			logg($session_id, 'Failed to download ' . $code . ', skipping ...');
			continue;
		}
		$process[] = $result;
	}
	
} else if ($settings['type'] == 'point') {
	$point = $settings['point'];
	$pointFilter = $settings['pointFilter'];
	
	if (empty($point['city']) && empty($point['locLat']) && empty($point['locLong'])) {
		logg($session_id, 'Empty search params, nothing to do.');
		exit(0);
	}
	
	$guids = fetchList($session_id, $cookiefile, $point, $pointFilter);

	$i = 1;
	foreach ($guids as $key=>$guid) {
		logg($session_id, 'Downloading... (' . $i . '/' . count($guids) . ') ' . $key);
		$i++;
		$result = fetchPrintByGuid($session_id, $cookiefile, $key, $guid);
		if (empty($result)) {
			logg($session_id, 'Failed to download ' . $key . ', skipping ...');
			continue;
		}
		$process[] = $result;
	}

}

logg($session_id, 'Creating intermediate format...');
$intermediate = createIntermediate($process);
$intermediate->save('result/'.$session_id.'/intermediate.xml');

logg($session_id, 'Creating gpx output...');
$res = createGPX($session_id, $intermediate, $settings['outputGPX']);
if (!$res) {
	logg($session_id, 'Failed to create gpx output!');
}

logg($session_id, 'Creating kindle output...');
$res = createKindle($session_id, $intermediate, $process, $settings['outputKindle']);
if (!$res) {
	logg($session_id, 'Failed to create kindle output!');
}
//createLOC($session_id, $process, $settings['outputLOC']);
logg($session_id, 'Done.');

?>
