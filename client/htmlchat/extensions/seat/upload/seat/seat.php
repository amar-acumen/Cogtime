<?php
/*
 * Created on Nov 5, 2012
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

class Seat
{
	private $credits;
	private $username;
	private $roomId;
	private $seatNum;

	function Seat($credits, $username, $roomId, $seatNum)
	{
		$this->credits = $credits;
		$this->username = $username;
		$this->roomId = $roomId;
		$this->seatNum = $seatNum;
	}

	public function getCredits()
	{
		return $this->credits;
	}

	public function getUsername()
	{
		return $this->username;
	}

	public function setUsername($un)
	{
		$this->username = $un;
	}

	public function setCredits($cre)
	{
		$this->credits = $cre;
	}


	public function getRoomId()
	{
		return $this->roomId;
	}

	public function getSeatNum()
	{
		return $this->seatNum;
	}

	public function toArray()
	{
		return array("credits" => $this->credits, "username" => $this->username
					, "roomId" => $this->roomId, "seatNum" => $this->seatNum);
	}

}
?>
