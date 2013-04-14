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

	shell_exec("php lib/generate.php " . escapeshellarg(serialize($settings)) . " >/dev/null &");
	sleep(1);
	header('Location: view.php?id=' . $session) ;
	
	function createPoint() {
		return array();
	}

	function createPointFilter() {
		return array();
	}

	function createOutputKindle() {
		return array(
			'withImages'=> $_POST['withImages'] == 'on',
			'map'=> $_POST['map'] == 'on',
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