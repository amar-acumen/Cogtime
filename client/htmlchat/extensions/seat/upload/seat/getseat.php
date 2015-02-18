<?php
require_once "seat.php";
require_once "config.php";

$result = array();
if(!isset($_GET["roomid"]))
{
	$result["result"] = -1;
}
$roomId = $_GET["roomid"];
if ($roomId == null || true === empty($roomId) || $roomId === "")
{
	$result["result"] = -1;
}

if(!isset($_GET["credits"]))
{
	$result["result"] = -1;
}

if(!isset($_GET["un"]))
{
	$result["result"] = -1;
}

if(!isset($_GET["seatNum"]))
{
	$result["result"] = -1;
}

if (!isset ($_GET["tid"])) {
	$result["result"] = -1;
}

if (!function_exists('curl_init'))
{
	$result["result"] = -1;
}

$roomSeatsFile = "seat_data_".$roomId.".txt";
if(!file_exists($roomSeatsFile))
{
	$result["result"] = -1;
}

if (array_key_exists("result", $result) && $result["result"] == -1)
{
	$jsonencode = $_GET['callback'].'('.json_encode($result).');';
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


$bb = file_get_contents($roomSeatsFile);
$ar = unserialize($bb);
$num = count($ar);

for($i=0;$i<$num;++$i)
{
	if ($ar[$i]->getRoomId() == $roomId && $ar[$i]->getSeatNum() == $_GET["seatNum"])
	{
		$credits = $_GET["credits"];
		if ($ar[$i]->getCredits() < $credits)
		{
			//for update credits;
			$remoteURL = $updateCreditsAPI."&un=".$_GET["un"]."&app=".$_GET["un"]."&rid=" . $_GET["roomid"] ."&tsid=".$_GET["tid"]."&crds=-".$credits*100;
			$curl_handle=curl_init();
			curl_setopt($curl_handle, CURLOPT_URL,$remoteURL);
			curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 2);
			curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
			$mofifyContents = curl_exec($curl_handle);
			curl_close($curl_handle);

			if ($mofifyContents != 0) {
				$result["result"] = 2;
			} else if ($mofifyContents == 0) {

				$result["result"] = 1;
				$ar[$i]->setUsername($_GET["un"]);
				$ar[$i]->setCredits($_GET["credits"]);
				$str = serialize($ar);
				file_put_contents($roomSeatsFile, $str);

				//for brocast msg
				$custommsg = $customMessageAPI."&username=".$_GET["un"]."&roomid=".$_GET["roomid"]."&msg=topcmm_123flashchat_getseat"."&p=0&type=topcmm_123flashchat_getseat";
				$curl_handle_brocast=curl_init();
				curl_setopt($curl_handle_brocast, CURLOPT_URL,$custommsg);
				curl_setopt($curl_handle_brocast, CURLOPT_CONNECTTIMEOUT, 2);
				curl_setopt($curl_handle_brocast, CURLOPT_RETURNTRANSFER, 1);
				$fs = curl_exec($curl_handle_brocast);
				curl_close($curl_handle_brocast);
			}
		}
		else
		{
			$result["result"] = 3;
		}
		break;
	}
}
$jsonencode = $_GET['callback'].'('.json_encode($result).');';
echo $jsonencode;
?>
