<?php
//gets document from uri, retrieve it with images if wanted and saves it to cache
//in format of /cache/$gccode/file.html
//             /cache/$gccode/images/*
function fetchPrint($session_id, $cookiefile, $gccode) {
	//consider skipping file, if cached dir already exists. Don't forget to remove directory upon failed download(!)
	$file = 'cache/' . $gccode . '/' . $gccode . '.html';
	unlink($file);
	mkdir('cache/' . $gccode);
	$command = 'wget -a result/'.$session_id.'/wget.log -O "'.$file.'" -E -e robots=off -H -k -P cache/ --load-cookies '.$cookiefile.' --random-wait --timeout=5 --tries=3 http://www.geocaching.com/seek/cache_details.aspx?wp=' . $gccode;
	
	//logg($session_id, $command);
	
	if (exec(escapeshellcmd($command)) != 0) {
		return null;
	}
	
	$data = file_get_contents($file);
	$prefix = 'cdpf.aspx?guid=';
	$pos = stripos($data, $prefix);
	$res = substr($data, $pos + strlen($prefix), 36);
	if (strlen($res) != 36) {
		return null;
	}
	
	return fetchPrintByGuid($session_id, $cookiefile, $gccode, $res);
}

//download search list, filter it and return list of caches to download
//return array, where keys are cache codes, values are paths to downloaded files
function fetchList($session_id, $cookiefile, $point, $pointFilter) {
	//http://www.geocaching.com/seek/nearest.aspx?lat=48.149245&lng=17.107005&dist=62.5
	//TODO: search by city is not supported

	$postfile = 'result/'.$session_id.'/search-post.txt';
	$result = array();
	$max = $pointFilter['limitCount'] > 0 ? min($pointFilter['limitCount'], 100) : 100;
	$conditions = buildConditions($pointFilter);
	$dist = empty($pointFilter['limitDist']) ? '' : ('&dist=' . (int)($pointFilter['limitDist'] / 1.6));
	while (count($result) <= $max) {
		$url = 'http://www.geocaching.com/seek/nearest.aspx?lat=' . $point['locLat'] . '&lng='. $point['locLong'] . $dist;
		$file = 'result/' . $session_id . '/search.html';
		unlink($file);
		$res = file_put_contents($postfile, '__EVENTTARGET=&ctl00%24ContentBody%24pgrTop%24lbGoToPage_' . $i);
		if (!$res) {
			break;
		}
		
		$command = 'wget -a result/' . $session_id . '/wget.log -O "'.$file.'" --load-cookies '.$cookiefile.' --random-wait --timeout=5 --tries=3 '.$url;
		if (exec(escapeshellcmd($command)) != 0) {
			break;
		}
		
		$tmpCaches = dumpCaches($file, $conditions);
		
		for ($tmpCaches as $key=>$item) {
			if (count($result) <= $max) {
				break;
			}
			$result[$key] = $item;
		}
	}
}

function dumpCaches($file, $conditions) {
	$html = new DOMDocument();
	$html->loadHTMLFile($file);
	$sxml = simplexml_import_dom($html);
	$caches = $sxml->xpath('//table[@class="SearchResultsTable Table"]//tr[contains(@class, "Data")]' . $conditions);
	$res = array();
	foreach ($caches as $cache) {
		$name = $cache->xpath('td[@class="Merge"]/span[@class="small"]');
		$name = $name[0]->asXML();
		$name = explode('|', $name);
		$name = trim($name[1]);
		$guid = $cache->xpath('td[@class="Merge"]/a[@class="lnk"]/@href');
		$guid = $guid[0]->asXML();
		$guid = substr($guid, stripos($guid, '?guid=') + 6);
		$res[$name] = $guid;
	}
	return $res;
}

//vrati string s xpath podmienkami pre vyber konkretneho riadku na zaklade zvolenych filtrov
function buildConditions($pointFilter) {
	/*
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
	 */
	
	$res = '';
	$types = '[';
	foreach ($pointFilter['cacheType'] as $type) {
		if (count($types) > 1) {
			$types .= ' or ';
		}
		$types .= 'contains(td[@class="Merge"]/img[@class="SearchResultsWptType"]/@src, "'.$type.'")';
	}
	$types .= ']';
	$res .= $types;
	
	if (!empty($pointFilter['difficultyMin'])) {
		$res .= '[number(substring-before(td[@class="AlignCenter"]/span[@class="small"], '/')) &gt= '.$pointFilter['difficultyMin']).']';
	}

	if (!empty($pointFilter['difficultyMax'])) {
		$res .= '[number(substring-before(td[@class="AlignCenter"]/span[@class="small"], '/')) &lt= '.$pointFilter['difficultyMax']).']';
	}

	if (!empty($pointFilter['terrainMin'])) {
		$res .= '[number(substring-after(td[@class="AlignCenter"]/span[@class="small"], '/')) &gt= '.$pointFilter['terrainMin']).']';
	}

	if (!empty($pointFilter['terrainMax'])) {
		$res .= '[number(substring-after(td[@class="AlignCenter"]/span[@class="small"], '/')) &lt= '.$pointFilter['terrainMax']).']';
	}
	
	if (!empty($pointFilter['notFound'])) {
		$res .= '[not(contains(td/img/@src, "found.png"))]';
	}

	if (!empty($pointFilter['onlyActive'])) {
		$res .= '[not(contains(td[@class="Merge"]/a/@class, "Strike"))]';
	}

	if (!empty($pointFilter['skipPremium'])) {
		$res .= '[not(contains(td/img/@src, "premium_only.png"))]';
	}

	return $res;
}

//stiahne kes s datami
function fetchPrintByGuid($session_id, $cookiefile, $gccode, $guid) {
	$remoteFile = 'cdpf.aspx?guid='.$guid.'&lc=10';
	$file = 'cache/'.$gccode . '/' . $remoteFile . '.html';
	
	//skip download, if file already exists
	//TODO: ak je file starsi ako n-dni, zmazat a nanovo
	if (file_exists($file)) {
		return $file;
	}
	
	$command = 'wget -a result/'.$session_id.'/wget.log -nd -E -e robots=off -H -k -p -P cache/'.$gccode.' --load-cookies '.$cookiefile." --random-wait --timeout=5 --tries=3 'http://www.geocaching.com/seek/".$remoteFile."'";
	
	//logg($session_id, $command);
	if (exec($command) != 0) {
		return null;
	}
	
	//dos2unix :)
	file_put_contents($file, str_replace("\r", '', file_get_contents($file)));
	
	return $file;
}

?>
