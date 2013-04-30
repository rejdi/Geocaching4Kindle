<?php

//session id - kam potom pojde result
//process - array of files to process and unite
//settings - what should be in output
function createKindle($session_id, $intermediate, $codes, $settings) {
	//load template + push settings
	//process files -> create intermediate xml
	//create final HTML document with TOC and caches
	//convert document using kindlegen
	$xsl = new DOMDocument();
	$xsl->load('xsl/kindle.xsl');

	$kindleOut = new XSLTProcessor();
	$kindleOut->importStylesheet($xsl);
	$kindleOut->setParameter('', $settings);
	$htmlfile = 'result/'.$session_id.'/kindle.html';
	$mobifile = 'result.mobi';
	
	if ($settings['withImages'] && $settings['map']) {
		$sxml = simplexml_import_dom($intermediate);
		$caches = $sxml->xpath('//cache');
		foreach ($caches as $cache) {
			//google mapy
			//fetch('http://maps.google.com/maps/api/staticmap?zoom=14&size=640x480&maptype=roadmap&markers=icon:http://www.geocaching.com/images/WptTypes/pins/'.$cache->map->wptid.'.png|'.$cache->map->lat.','.$cache->map->lng.'&sensor=false', 'result/'.$session_id.'/map_'.$cache->code.'.png');
			fetch('staticmap/staticmap.php?zoom=14&size=640x480&maptype=hike&markers='.$cache->map->lat.','.$cache->map->lng.',icon:http://www.geocaching.com/images/WptTypes/pins/'.$cache->map->wptid.'.png&sensor=false', 'result/'.$session_id.'/map_'.$cache->code.'.png');
		}
	}
	
	file_put_contents($htmlfile, $kindleOut->transformToXML($intermediate));
	
	foreach ($codes as $code) {
		shell_exec('ln cache/'.$code.'/* result/'.$session_id.'/');
	}
	
	$command = 'kindlegen -c2 -o '.$mobifile.' '.$htmlfile;
	$res = exec(escapeshellcmd($command));
	if ($res != 0) {
		return false;
	}
	return true;
}

function fetch($url, $file) {
	$command = 'wget -o /dev/null -O "'.$file.'" --random-wait --timeout=5 --tries=3 '.$url;
	if (exec(escapeshellcmd($command)) != 0) {
		return null;
	}
	return $file;
}

?>