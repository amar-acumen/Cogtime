<?php

require_once "config.php";

/*
 * Created on Nov 13, 2012
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

$result = array ();
if (!isset ($_GET["un"])) {
	$result["result"] = -1;
}

if (!isset ($_GET["msg"])) {
	$result["result"] = -1;
}
if (!isset ($_GET["tid"])) {
	$result["result"] = -1;
}
if (!function_exists('curl_init'))
{
	$result["result"] = -1;
}

if (array_key_exists("result", $result) && $result["result"] == -1) {
	$jsonencode = $_GET['callback'] . '(' . json_encode($result) . ');';
	echo $jsonencode;
	exit;
}

if (!strpos($updateCreditsAPI, "?"))
{
	$updateCreditsAPI = $updateCreditsAPI."?a=a";
}
if (!strpos($customMessageAPI, "?"))
{
	$customMessageAPI = $customMessageAPI."?a=a";
}

//for update credits;
$remoteURL = $updateCreditsAPI."&un=" . $_GET["un"]."&app=" . $_GET["un"]. "&rid=" . $_GET["roomid"]. "&crds=-1000" . "&tsid=" .$_GET["tid"];

$curl_handle = curl_init();
curl_setopt($curl_handle, CURLOPT_URL, $remoteURL);
curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 2);
curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
$mofifyContents = curl_exec($curl_handle);
curl_close($curl_handle);

if ($mofifyContents != 0) {
	$result["result"] = 2;
} else if ($mofifyContents == 0) {
	//for brocast msg
	$custommsg = $customMessageAPI."&username=" . $_GET["un"] . "&roomid=" . $_GET["roomid"] . "&msg=". $_GET["msg"] . "&p=0&type=topcmm_123flashchat_flashscreen";
	//echo $custommsg;
	$curl_handle_brocast = curl_init();
	curl_setopt($curl_handle_brocast, CURLOPT_URL, $custommsg);
	curl_setopt($curl_handle_brocast, CURLOPT_CONNECTTIMEOUT, 2);
	curl_setopt($curl_handle_brocast, CURLOPT_RETURNTRANSFER, 1);
	$fs = curl_exec($curl_handle_brocast);
	curl_close($curl_handle_brocast);
	$result["result"] = 1;
}
$jsonencode = $_GET['callback'] . '(' . json_encode($result) . ');';
echo $jsonencode;
?>
