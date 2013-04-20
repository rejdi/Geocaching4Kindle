<?php

function createIntermediate($files) {
	$xml = new DOMDocument();
	$xsl = new DOMDocument();
	$xsl->load('xsl/intermediate.xsl');
	$xslt = new XSLTProcessor();
	$xslt->importStylesheet($xsl);
	$root = $xml->appendChild($xml->createElement('caches'));
	foreach ($files as $cache) {
		$file = new DOMDocument();
		$file->loadHTMLFile($cache);
		$tmp = $xml->importNode($xslt->transformToDoc($file)->documentElement, true);
		$root->appendChild($tmp);
	}
	return $xml;
}

?>