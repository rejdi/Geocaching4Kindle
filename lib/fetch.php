<?php
require_once 'lib/log.php';

//gets document from uri, retrieve it with images if wanted and saves it to cache
//in format of /cache/$gccode/file.html
//             /cache/$gccode/images/*
function fetchPrint($session_id, $cookiefile, $gccode) {
	//consider skipping file, if cached dir already exists. Don't forget to remove directory upon failed download(!)
	$file = 'cache/' . $gccode . '/' . $gccode . '.html';
	unlink($file);
	mkdir('cache/' . $gccode);
	$command = 'wget -a result/'.$session_id.'/wget.log -O "'.$file.'" -E -e robots=off -H -k -P cache/ --load-cookies '.$cookiefile.' --random-wait --timeout=5 --tries=3 http://www.geocaching.com/geocache/' . $gccode;
	
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

	if (!empty($point['city'])) {
		logg($session_id, 'Getting '.$point['city'].' location ...');
		$json = file_get_contents('http://www.geocaching.com/api/geocode?q='.urlencode($point['city']));
		$data = json_decode($json);
		$point['locLat'] = $data->data->lat;
		$point['locLong'] = $data->data->lng;
		logg($session_id, $point['city'] . ' at lat: ' . $point['locLat'] . ', lng: ' . $point['locLong']);
	}

	$result = array();
	
	if (empty($point['locLat']) || empty($point['locLong'])) {
		return $result;
	}

	$postfile = 'result/'.$session_id.'/search-post.txt';
	$max = $pointFilter['limitCount'] > 0 ? min($pointFilter['limitCount'], 100) : 10;
	$conditions = buildConditions($pointFilter);
	$dist = empty($pointFilter['limitDist']) ? '' : ('&dist=' . (int)($pointFilter['limitDist'] / 1.6));
	$i = 1;
	$file = null;
	while (count($result) < $max && $i <= 10) {
		$url = 'http://www.geocaching.com/seek/nearest.aspx?lat=' . $point['locLat'] . '&lng='. $point['locLong'] . $dist;
		$res = file_put_contents($postfile, createSearchPostData($file, $i));
		$file = 'result/' . $session_id . '/search'.$i.'.html';
		unlink($file);
		if ($res === false) {
			break;
		}
		logg($session_id, 'Have '.count($result).' caches. Downloading search page ' . $i . '...');
		$command = 'wget -a result/' . $session_id . '/wget.log -O "'.$file.'" --load-cookies '.$cookiefile.' --post-file="'.$postfile.'" --random-wait --timeout=5 --tries=3 '.$url;
		if (exec(escapeshellcmd($command)) != 0) {
			break;
		}
		
		$tmpCaches = dumpCaches($file, $conditions);
		
		foreach ($tmpCaches as $key=>$item) {
			if (count($result) >= $max) {
				break;
			}
			$result[$key] = $item;
		}
		$i++;
	}
	unlink($postfile);
	return $result;
}

function createSearchPostData($file, $i) {
	if ($file == null) {
		return '';
	}
	
	$res = '__EVENTTARGET=ctl00%24ContentBody%24pgrTop%24lbGoToPage_' . $i . '&__VIEWSTATEFIELDCOUNT=2';
	$html = new DOMDocument();
	$html->loadHTMLFile($file);
	$sxml = simplexml_import_dom($html);
	$viewstate = $sxml->xpath('//input[@id="__VIEWSTATE"]/@value');
	$viewstate1 = $sxml->xpath('//input[@id="__VIEWSTATE1"]/@value');
	$res .= '&__VIEWSTATE=' . urlencode(substr($viewstate[0]->asXML(), 8, -1));
	$res .= '&__VIEWSTATE1=' . urlencode(substr($viewstate1[0]->asXML(), 8, -1));
	
	return $res;
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
		$name = trim(str_replace('&#13;', '', $name[1]));
		$link = $cache->xpath('td[@class="Merge"]/a[@class="lnk"]/@href');
		$link = $guid[0];
		//$guid = substr($guid, stripos($guid, '?guid=') + 6, -1);
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
		if (strlen($types) > 1) {
			$types .= ' or ';
		}
		$types .= 'contains(td[@class="Merge"]//img[@class="SearchResultsWptType"]/@src, "'.$type.'")';
	}
	$types .= ']';
	$res .= $types;
	
	if (!empty($pointFilter['difficultyMin'])) {
		$res .= '[number(substring-before(td[@class="AlignCenter"]/span[@class="small"], "/")) >= '.$pointFilter['difficultyMin'].']';
	}

	if (!empty($pointFilter['difficultyMax'])) {
		$res .= '[number(substring-before(td[@class="AlignCenter"]/span[@class="small"], "/")) <= '.$pointFilter['difficultyMax'].']';
	}

	if (!empty($pointFilter['terrainMin'])) {
		$res .= '[number(substring-after(td[@class="AlignCenter"]/span[@class="small"], "/")) >= '.$pointFilter['terrainMin'].']';
	}

	if (!empty($pointFilter['terrainMax'])) {
		$res .= '[number(substring-after(td[@class="AlignCenter"]/span[@class="small"], "/")) <= '.$pointFilter['terrainMax'].']';
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
