<?php
$url = "http://hypem.com/popular";
header('Content-Type: application/json');
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; rv:11.0) Gecko/20100101 Firefox/11.0');
curl_setopt($ch, CURLOPT_HEADER  ,1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
$content = curl_exec($ch);

// get cookies
$cookies = array();
preg_match_all('/Set-Cookie:(?<cookie>\s{0,}.*)$/im', $content, $cookies);

// basic parsing of cookie strings (just an example)
$cookieParts = array();
preg_match_all('/Set-Cookie:\s{0,}(?P<name>[^=]*)=(?P<value>[^;]*).*?expires=(?P<expires>[^;]*).*?path=(?P<path>[^;]*).*?domain=(?P<domain>[^\s;]*).*?$/im', $content, $cookieParts);


$parser = new DOMDocument();
@$parser->loadHTML($content);
$tracks = json_decode(($parser->getElementById('displayList-data')->nodeValue))->tracks;
$track = $tracks[1];


$c = curl_init('http://hypem.com/serve/source/'.$track->id.'/'.$track->key);
curl_setopt($ch, CURLOPT_REFERER, $url);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; rv:11.0) Gecko/20100101 Firefox/11.0');
curl_setopt($c, CURLOPT_VERBOSE, 1);
curl_setopt($c, CURLOPT_COOKIE, $cookieParts['name'][0].'='.$cookieParts['value'][0]);
curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
$page = curl_exec($c);
curl_close($c);


$return = json_decode($page);
file_put_contents(__DIR__.'/'.$return->itemid.'.mp3', file_get_contents($return->url));