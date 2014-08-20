<?php

require_once 'lib/xsltfunc.php';

function createGPX($session_id, $intermediate, $settings) {
	$xsl = new DOMDocument();
	$xsl->load('xsl/gpx.xsl');

	$gpx = new XSLTProcessor();
	$gpx->registerPHPFunctions();
	$gpx->importStylesheet($xsl);
	$gpx->setParameter('', $settings);

	file_put_contents('result/'.$session_id.'/result.gpx', $gpx->transformToXML($intermediate));
	return true;
}

?>