<?php

function url_exists($url) {
	//if (fopen ( "http://". $url ."/", "r"))  {
// 	if (@fopen ( $url , "r"))  {
// 		return true;
// 	}
// 	else {
// 		return false;
// 	}

	if( get_response_code($url) == '404' )  {
		return false;
	}
	else {
		return true;
	}
	
// 	return preg_match('/(^.+intenzcity.+)\.([^\.]+)$/', $url);

	//return true;
}


function get_response_code($url) {
	$headers = @get_headers($url);
	
	$pattern = '/^http\/[^\s]*[\s]+([\d]+)/i';
	if( is_array($headers) && count($headers) ) {
		foreach($headers as $header) {
			$matches = array();
			if(preg_match($pattern, $header, $matches)) {
				if( isset($matches[1]) ) {
					return $matches[1];
				}
			}
		}
	}

	return '404';
}


function my_current_url() {

	$protocol = ( isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 'https' : 'http';
	$url = $protocol.'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	return $url;
}


function my_url($str) {
	$url = html_entity_decode($str, ENT_QUOTES, 'utf-8');
	$url = utf8_accents_to_ascii($url);

	return trim(preg_replace('/[^\da-zA-Z]+/', '-', strip_tags($url)), '-');
}


function get_ip() {
	$ip = $_SERVER['REMOTE_ADDR'];
	return $ip;
}

function get_subnet_ip() {
/*
This function will try to find out if user is coming behind proxy server. Why is this important?
If you have high traffic web site, it might happen that you receive lot of traffic
from the same proxy server (like AOL). In that case, the script would count them all as 1 user.
This function tryes to get real IP address.
Note that getenv() function doesn't work when PHP is running as ISAPI module
*/
	if (getenv('HTTP_CLIENT_IP')) {
		$ip = getenv('HTTP_CLIENT_IP');
	}
	elseif (getenv('HTTP_X_FORWARDED_FOR')) {
		$ip = getenv('HTTP_X_FORWARDED_FOR');
	}
	elseif (getenv('HTTP_X_FORWARDED')) {
		$ip = getenv('HTTP_X_FORWARDED');
	}
	elseif (getenv('HTTP_FORWARDED_FOR')) {
		$ip = getenv('HTTP_FORWARDED_FOR');
	}
	elseif (getenv('HTTP_FORWARDED')) {
		$ip = getenv('HTTP_FORWARDED');
	}
	else {
		$ip = $_SERVER['REMOTE_ADDR'];
	}
	return $ip;
}

function get_302_Location($url) {
	$headers = get_headers($url);
	$array = $headers;
	$count = count($array);
	
	for ($i=0; $i < $count; $i++) {
		if (strpos($array[$i], "ocation:")) {
			$url = substr($array[$i], 10);
		}
	}
	if ($url) {
		return $url;
	}
	else {
		return 0;
	}
}



//// valid url format checker
function isValidURL($url)
{
    $pattern = "/^((http(s?):\/\/|ftp:\/\/{1}))?((\S+\.){1,})\w{2,4}(\/[^\:]+)*\/?$/i";

    return preg_match($pattern, $url);
}

function get_original_text($txt)
{
	return str_replace("-"," ",$txt);
}


function get_href_content($html){
	
	//$html = '<a href="http://www.mydomain.com/page.html">URL</a>';

	$url = preg_match('/<a href="(.+)">/', $html, $match);
	//$url = preg_match('/href="(http?://[^/]*)/', $html, $match);
	

	$info = parse_url($match[1]);
	//pr($info);
    return $info['scheme'].'://'.$info['host'].''.$info['path'];
}



