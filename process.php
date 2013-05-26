<?php
srand(time());
$session = rand(0,50);
$settings = array(
	'session' => $session,
	'user' => $_POST['user'],
	'pass' => $_POST['pass'],
	'type' => $_POST['method'],
	'codes' => createCodes($_POST['cacheList']),
	'point' => createPoint(),
	'pointFilter' => createPointFilter(),
	'outputKindle' => createOutputKindle(),
	);

	//echo serialize($settings);
	shell_exec("php lib/generate.php " . escapeshellarg(serialize($settings)) . " >/dev/null &");
	sleep(1);
	header('Location: view.php?id=' . $session) ;
	//print_r($settings);
	
	function createPoint() {
		return array(
			'city' => $_POST['city'],
			'locLat' => $_POST['locLat'],
			'locLong' => $_POST['locLong'],
			);
	}

	function createPointFilter() {
		return array(
			'limitCount' => $_POST['limitCount'],
			'limitDistance' => $_POST['limitDistance'],
			'cacheType' => array_keys($_POST['cacheType']),
			'difficultyMin' => $_POST['difficultyMin'],
			'difficultyMax' => $_POST['difficultyMax'],
			'terrainMin' => $_POST['terrainMin'],
			'terrainMax' => $_POST['terrainMax'],
			'notFound' => $_POST['shortDesc'] == 'on',
			'onlyActive' => $_POST['onlyActive'] == 'on',
			'skipPremium' => $_POST['skipPremium'] == 'on',
			);
	}

	function createOutputKindle() {
		return array(
			'withImages'=> $_POST['withImages'] == 'on',
			'map'=> $_POST['map'] == 'on',
			'maptype'=> $_POST['maptype'],
			'tocName'=> $_POST['tocName'] == 'on',
			'tocDifficulty'=> $_POST['tocDifficulty'] == 'on',
			'tocTerrain'=> $_POST['tocTerrain'] == 'on',
			'meta'=> $_POST['meta'] == 'on',
			'shortDesc'=> $_POST['shortDesc'] == 'on',
			'longDesc'=> $_POST['longDesc'] == 'on',
			'additionalWaypoints'=> $_POST['additionalWaypoints'] == 'on',
			'hints'=> $_POST['hints'] == 'on',
			'solveHint'=> $_POST['solveHint'] == 'on',
			'attributes'=> $_POST['attributes'] == 'on',
			'logs' => $_POST['logs'],
			);
	}

	function createCodes($list) {
		preg_match_all('/[a-z0-9]*/imu', $list, $matches);
		$matches = array_filter($matches[0], 'strlen');
		return $matches;
	}
	
?>