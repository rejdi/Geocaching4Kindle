<?php

function normalize_time($time) {
	return date('c', strtotime($time));
}

function createGPX($session_id, $intermediate, $settings) {
	$xsl = new DOMDocument();
	$xsl->load('xsl/gpx.xsl');

	$gpx = new XSLTProcessor();
	$gpx->registerPHPFunctions('normalize_time');
	$gpx->importStylesheet($xsl);

	file_put_contents('result/'.$session_id.'/result.gpx', $gpx->transformToXML($intermediate));
	return true;
}

?>