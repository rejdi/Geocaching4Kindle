<?php
//gets document from uri, retrieve it with images if wanted and saves it to cache
//in format of /cache/$gccode/file.html
//             /cache/$gccode/images/*
function fetchPrint($cookiefile, $gccode) {
	//consider skipping file, if cached dir already exists. Don't forget to remove directory upon failed download(!)
	global $session_id;
	$file = 'cache/' . $gccode . '/' . $gccode . '.html';
	unlink($file);
	mkdir('cache/' . $gccode);
	$command = 'wget -a result/'.$session_id.'/wget.log -O "'.$file.'" -E -H -k -P cache/ --load-cookies '.$cookiefile.' --random-wait --timeout=5 --tries=3 http://www.geocaching.com/seek/cache_details.aspx?wp=' . $gccode;
	
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
	
	$remoteFile = 'cdpf.aspx?guid='.$res.'&lc=10';
	$file = 'cache/'.$gccode . '/' . $remoteFile . '.html';
	
	//skip download, if file already exists
	if (file_exists($file)) {
		return $file;
	}
	
	$command = 'wget -a result/'.$session_id.'/wget.log -nd -E -H -k -p -P cache/'.$gccode.' --load-cookies '.$cookiefile." --random-wait --timeout=5 --tries=3 'http://www.geocaching.com/seek/".$remoteFile."'";
	
	//logg($session_id, $command);
	if (exec($command) != 0) {
		return null;
	}
	
	file_put_contents($file, str_replace("\r", '', file_get_contents($file)));
	
	return $file;
}
/*

GC38X9V
GC1KFDH

*/
?>
