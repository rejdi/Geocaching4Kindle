<?php

function normalizeTime($time) {
	return date('c', strtotime($time));
}

function wpextract($data, $i) {
	$pos = stripos($data, 'cmapAdditionalWaypoints = ');
	if ($pos === false) {
		return "";
	}
	$pos2 = stripos($data, '}];', $pos + 26);
	if ($pos2 === false) {
		return "";
	}
	$data = substr($data, $pos + 26, $pos2 - $pos - 26 + 2);
	$array = json_decode($data);
	if (!is_array($array)) {
		return "";
	}

	//file_put_contents('aaa.txt', print_r(json_decode($data), true));
	return '<lat>'.$array[$i - 1]->lat.'</lat><lon>'.$array[$i - 1]->lon.'</lon>';
}

function DMStoDec2($deg, $minsec) {
	//file_put_contents('aaa.txt', $deg . ' ' . $minsec . "\n", FILE_APPEND);
	return $deg+($minsec)/60;
}

function ascii_to_dec($str) {
	//TODO: implement proper function to convert GC code to number or figure out, how to get cacheID
	$result = "";
	for ($i = 0, $j = strlen($str); $i < $j; $i++) {
		$result .= ord($str{$i});
	}
	$result = floor(fmod($result, 2147483648));
	return $result;
}

function DMStoDec($value) {
//	'N 48° 10.161 E 017° 03.042'
	$pos = stripos($value, 'E');
	if ($pos === false) {
		$pos = stripos($value, 'W');
	}
	if ($pos === false) {
		return "";
	}
	$lat = trim(substr($value, 0, $pos));
	$lon = trim(substr($value, $pos));

	$a = 1;
	$b = 1;

	if ($lat[0] == 'S') $a = -1;
	if ($lon[0] == 'W') $b = -1;

	$lat = DMStoDec2($a*($lat[2] . $lat[3]), $a*($lat[7].$lat[8].$lat[10].$lat[11].$lat[12])/1000);
	$lon = DMStoDec2($b*($lon[2] . $lon[3] . $lon[4]), $b*($lon[8].$lon[9].$lon[11].$lon[12].$lon[13])/1000);
	
	return '<lat>'.$lat.'</lat><lon>'.$lon.'</lon>';
}

?>
