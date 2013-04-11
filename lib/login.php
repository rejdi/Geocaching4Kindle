<?php

function login($username, $password, $session_id) {
	//TODO: skontrolovat username a password na ine znaky ako a-Z0-1
	$cookiefile = 'result/' . $session_id . '/cookie.txt';
	$postfile = 'result/' . $session_id . '/post.txt';
	
	/*html*/
	$res = file_put_contents($postfile, "__EVENTTARGET=&ctl00%24ContentBody%24btnSignIn=Sign%20In&ctl00%24ContentBody%24cbRememberMe=on&ctl00%24ContentBody%24tbPassword=".$password."&ctl00%24ContentBody%24tbUsername=".$username);
	if (!$res) {
		return false;
	}
	
	$command = "wget --post-file='".$postfile."' --keep-session-cookies --save-cookies ".$cookiefile." --referer='https://www.geocaching.com/login/default.aspx' 'https://www.geocaching.com/login/default.aspx' -O /dev/null -o /dev/null";
	exec(escapeshellcmd($command));
	
	$cookies = file_get_contents($cookiefile);
	$userid = stristr($cookies, 'userid');
	if (empty($userid)) {
		return false;
	}
	
	/* WAP */
	/*
	$res = file_put_contents($postfile, "__EVENTTARGET=&txtPassword=".$password."&txtUsername=".$username);
	if (!$res) {
		return false;
	}
	$command = "wget --post-file='".$postfile."' --keep-session-cookies --load-cookies ".$cookiefile." --save-cookies ".$cookiefile." --referer='http://wap.geocaching.com/login.aspx' 'http://wap.geocaching.com/login.aspx' -O /dev/null";
	exec(escapeshellcmd($command));
	
	$cookies = file_get_contents($cookiefile);
	$userid = stristr($cookies, 'userid');
	if (empty($userid)) {
		return false;
	}*/
	
	return $cookiefile;
}

?>