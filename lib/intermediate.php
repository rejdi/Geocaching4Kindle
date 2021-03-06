<?php

require_once 'lib/xsltfunc.php';

function createIntermediate($files) {
	$xml = new DOMDocument();
	$xsl = new DOMDocument();
	$xsl->load('xsl/intermediate.xsl');
	$xslt = new XSLTProcessor();
	$xslt->registerPHPFunctions();
	$xslt->importStylesheet($xsl);
	$root = $xml->appendChild($xml->createElement('caches'));
	foreach ($files as $cache) {
		$file = new DOMDocument();
		$file->loadHTMLFile($cache);
		$inter = new DOMDocument();
		$inter->loadXML($xslt->transformToXML($file));
		$tmp = $xml->importNode($inter->documentElement, true);
		$root->appendChild($tmp);
	}
	return $xml;
}

?>
