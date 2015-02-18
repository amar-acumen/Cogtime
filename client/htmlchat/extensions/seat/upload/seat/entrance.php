<?php

require_once "seat.php";
require_once "config.php";

if (!strpos($updateCreditsAPI, "?"))
{
	$updateCreditsAPI = $updateCreditsAPI."?a=a";
}
if (!strpos($customMessageAPI, "?"))
{
	$customMessageAPI = $customMessageAPI."?a=a";
}

function clearSpecifyUserRoomSeat($seats, $username, $filename)
{
	$num = count($seats);
	for($i=0;$i<$num;++$i)
	{
		if ($seats[$i]->getUsername() == $username)
		{
			$seats[$i]->setUsername("");
			$seats[$i]->setCredits(0);
			$str = serialize($seats);
			file_put_contents($filename, $str);
		}
	}
}

if(isset($_GET["un"]) && isset($_GET["roomid"]) && isset($_GET["clear"]))
{
	$rid = $_GET["roomid"];
	$username = $_GET["un"];
	$clear = $_GET["clear"];
	if ($rid == "0")
	{
		if ($handler = opendir("."))
		{
			while( ($filename = readdir($handler)) !== false ) 
			{	
				if($filename != "." && $filename != ".." && strpos($filename, "seat_data_") !== false)
			    {
			        $bb = file_get_contents($filename);
					$ar = unserialize($bb);
					clearSpecifyUserRoomSeat($ar, $username, $filename);
			    }
			}
		}
	}
	else
	{
		$dealFile = "seat_data_".$rid.".txt";
		$bb = file_get_contents($dealFile);
		$ar = unserialize($bb);
		clearSpecifyUserRoomSeat($ar, $username, $dealFile);
	}
	
	//for brocast msg
	$custommsg = $customMessageAPI."&username=".$_GET["un"]."&msg=topcmm_123flashchat_getseat"."&p=0&type=topcmm_123flashchat_getseat";
	$curl_handle_brocast=curl_init();
	curl_setopt($curl_handle_brocast, CURLOPT_URL,$custommsg);
	curl_setopt($curl_handle_brocast, CURLOPT_CONNECTTIMEOUT, 2);
	curl_setopt($curl_handle_brocast, CURLOPT_RETURNTRANSFER, 1);
	$fs = curl_exec($curl_handle_brocast);
	curl_close($curl_handle_brocast);
	exit;
}

function getSeatsByRoomId() {
	if(!isset($_GET["roomid"]))
	{
		echo "";
		exit;
	}
	$roomId = $_GET["roomid"];
	if ($roomId == null || true === empty($roomId) || $roomId === "")
	{
		echo "";
		exit;
	}

	$result = array();
	$roomSeatsFile = "seat_data_".$roomId.".txt";
	if(!file_exists($roomSeatsFile))
	{
		$seats = array();
		$seats[0] = new Seat(0, "", $roomId, 0);
		$result[0] = $seats[0]->toArray();
		$seats[1] = new Seat(0, "", $roomId, 1);
		$result[1] = $seats[1]->toArray();
		$seats[2] = new Seat(0, "", $roomId, 2);
		$result[2] = $seats[2]->toArray();
		$seats[3] = new Seat(0, "", $roomId, 3);
		$result[3] = $seats[3]->toArray();

		$aa = serialize($seats);
		file_put_contents($roomSeatsFile, $aa);

		$jsonencode = $_GET['callback'].'('.json_encode($result).');';
		echo $jsonencode;
	}
	else
	{
		$bb = file_get_contents($roomSeatsFile);
		$ar = unserialize($bb);
		$num = count($ar);
		for($i=0;$i<$num;++$i)
		{
			$result[$i] = $ar[$i]->toArray();
		}

		$jsonencode = $_GET['callback'].'('.json_encode($result).');';
	    echo $jsonencode;
	}
}

getSeatsByRoomId();
?>
