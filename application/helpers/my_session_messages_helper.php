<?php
function add_message($session_variable, $variable_name, $message) {
	$message_arr[$variable_name] = $message;

	//$stored_messages = $this->session->userdata($session_variable);
	$stored_messages = $_SESSION[$session_variable];
	if($stored_messages!="" && count($stored_messages)!=0) {
		$_SESSION[$session_variable] = array_merge($stored_messages, $message_arr);
		//$this->session->set_userdata($session_variable, array_merge($stored_messages, $message_arr));
	}
	else {
		$_SESSION[$session_variable] = $message_arr;
		//$this->session->set_userdata($session_variable, $message_arr);
	}
} 
