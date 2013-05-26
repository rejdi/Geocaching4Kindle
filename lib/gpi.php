<?php

function createGPI($session_id, $settings) {
	$infile = 'result/' . $session_id . '/result.gpx';
	$outfile = 'result/' . $session_id . '/result.gpi';
	$command = 'gpsbabel -i gpx -f '.$infile.' -o garmin_gpi,notes -F ' . $outfile;
	$res = exec(escapeshellcmd($command));
	if ($res != 0) {
		return false;
	}
	return true;
}

?>