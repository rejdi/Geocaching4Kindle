<?php
//preg_match_all('!cdpf\.aspx\?guid=[a-zA-Z0-9\-]*!imu', file_get_contents('cache/GC1KFDH.html'), $matches);
$data = file_get_contents('cache/GC1KFDH.html');
$prefix = 'cdpf.aspx?guid=';
$pos = stripos($data, $prefix);
var_dump($pos);
$res = substr($data, $pos + strlen($prefix), 36);
var_dump($res);

echo escapeshellarg("wget -o cache/log.txt -nd -E -H -k -P cache/GC38X9V --load-cookies result/21/cookie.txt --random-wait --timeout=5 --tries=3 http://www.geocaching.com/seek/cdpf.aspx?guid=0feef387-e063-4f63-bfbe-873bec53ca7b&lc=0");
?>
