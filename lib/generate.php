<?php
require_once 'lib/log.php';
require_once 'lib/login.php';
require_once 'lib/fetch.php';
require_once 'lib/intermediate.php';
require_once 'lib/kindle.php';
require_once 'lib/gpx.php';
require_once 'lib/gpi.php';

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
	
$outputGPX = array of
	$shortDesc
	$longDesc
	$hints
	$logs = integer <0,10>
	$additionalWaypoints

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
deleteDirectory('result/'.$session_id);
mkdir('result/'.$session_id);
// logg($session_id, json_encode($settings));
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
	
} else if ($settings['type'] == 'location') {
	$point = $settings['point'];
	$pointFilter = $settings['pointFilter'];
	
	if (empty($point['city']) && empty($point['locLat']) && empty($point['locLong'])) {
		logg($session_id, 'Empty search params, nothing to do.');
		exit(0);
	}
	
	$links = fetchList($session_id, $cookiefile, $point, $pointFilter);

	$i = 1;
	foreach ($links as $key=>$link) {
		logg($session_id, 'Downloading... (' . $i . '/' . count($links) . ') ' . $key);
		$i++;
		//$result = fetchPrintByLink($session_id, $cookiefile, $key, $link);
		$result = fetchPrint($session_id, $cookiefile, $key);
		if (empty($result)) {
			logg($session_id, 'Failed to download ' . $key . ', skipping ...');
			continue;
		}
		$process[] = $result;
	}

}
unlink($cookiefile);

if (empty($process)) {
	logg($session_id, 'Empty list, nothing to do.');
	exit(0);
}

logg($session_id, 'Creating intermediate format...');
$intermediate = createIntermediate($process);
$intermediate->save('result/'.$session_id.'/intermediate.xml');

logg($session_id, 'Creating gpx output...');
$res = createGPX($session_id, $intermediate, $settings['outputGPX']);
if (!$res) {
	logg($session_id, 'Failed to create gpx output!');
}


logg($session_id, 'Creating garmin gpi output...');
$res = createGPI($session_id, $settings['outputGPI']);
if (!$res) {
	logg($session_id, 'Failed to create gpi output!');
}

logg($session_id, 'Creating html and kindle output...');
$res = createKindle($session_id, $intermediate, $process, $settings['outputKindle']);
if (!$res) {
	logg($session_id, 'Failed to create kindle output!');
}

logg($session_id, 'Done.');


function deleteDirectory($dir) {
    if (!file_exists($dir)) return true;
    if (!is_dir($dir) || is_link($dir)) return unlink($dir);
	foreach (scandir($dir) as $item) {
		if ($item == '.' || $item == '..') continue;
		if (!deleteDirectory($dir . "/" . $item)) {
			chmod($dir . "/" . $item, 0777);
			if (!deleteDirectory($dir . "/" . $item)) return false;
		};
	}
	return rmdir($dir);
} 
?>
