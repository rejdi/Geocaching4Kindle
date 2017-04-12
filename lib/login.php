<?php

function login($username, $password, $session_id) {
	//TODO: skontrolovat username a password na ine znaky ako a-Z0-1
	$cookiefile = 'result/' . $session_id . '/cookie.txt';
	$postfile = 'result/' . $session_id . '/post.txt';
	$tmp = 'result/' . $session_id . '/index.html';
	
	//token
	$command = "wget --keep-session-cookies -e robots=off --save-cookies ".$cookiefile." --referer='https://www.geocaching.com/account/login?returnUrl=%2fplay' 'https://www.geocaching.com/account/login?returnUrl=%2fplay' -O " . $tmp . " -o /dev/null";
	exec(escapeshellcmd($command));
	
	$data = file_get_contents($tmp);
	$needle = '__RequestVerificationToken" type="hidden" value="';
	$token_start=stripos($data, $needle);
	$token = substr($data, $token_start+strlen($needle), 92);
	
	$data = http_build_query(
		array(
			'__RequestVerificationToken' => $token,
			'Username' => $username,
			'Password' => $password
			)
		);
	
	/*html*/
	$res = file_put_contents($postfile, $data);
	if (!$res) {
		return false;
	}
	
	$command = "wget --post-file='".$postfile."' --keep-session-cookies -e robots=off --load-cookies ".$cookiefile." --save-cookies ".$cookiefile." --referer='https://www.geocaching.com/account/login?returnUrl=%2fplay' 'https://www.geocaching.com/account/login?returnUrl=%2fplay' -O " . $tmp . " -o /dev/null";
	exec(escapeshellcmd($command));
	
	$cookies = file_get_contents($cookiefile);
	$userid = stristr($cookies, 'gspkauth');
	unlink($postfile);
	unlink($tmp);
	if (empty($userid)) {
		unlink($cookiefile);
		return false;
	}
	
	/* WAP */
	/*
	$res = file_put_contents($postfile, "__EVENTTARGET=&txtPassword=".$password."&txtUsername=".$username);
	if (!$res) {
		return false;
	}
	$command = "wget --post-file='".$postfile."' --keep-session-cookies -e robots=off --load-cookies ".$cookiefile." --save-cookies ".$cookiefile." --referer='http://wap.geocaching.com/login.aspx' 'http://wap.geocaching.com/login.aspx' -O /dev/null";
	exec(escapeshellcmd($command));
	
	$cookies = file_get_contents($cookiefile);
	$userid = stristr($cookies, 'userid');
	if (empty($userid)) {
		return false;
	}*/
	
	return $cookiefile;
}

?>
