<?php

/* sends email
* $arr is used to set email parameters as follows:
* $arr['subject'], $arr['to'], $arr['cc'], $arr['bcc'], $arr['from_email'], $arr['from_name'], $arr['reply_to'], $arr['message']
*/
function send_mail(array $arr) {
	if( defined('ENVIRONMENT') && ENVIRONMENT !='development' ) {
		if( defined('BOOL_SEND_MAIL') && BOOL_SEND_MAIL ) {
			$ci = get_instance();
	
			$ci->load->library('email');
			$config['charset'] = 'utf-8';
			$config['protocol'] = 'mail';
			$config['useragent'] = 'Cogtime';
			$config['mailtype'] = 'html';
			$ci->email->initialize($config);
		
			if( isset($arr['subject']) ) {
				$ci->email->subject($arr['subject']);
			}
			else {
				$ci->email->subject('');
			}
	
			if( isset($arr['to']) ) {
				$ci->email->to($arr['to']);
			}
	
			if( isset($arr['cc']) ) {
				$cc = _str_to_array( $arr['cc'] );
				if(defined('GLOBAL_CC') && GLOBAL_CC!='') {
					$cc[] = GLOBAL_CC;
				}
	
				$cc = clean_email($cc);
				$ci->email->cc($cc);
			}
	
			if( isset($arr['bcc']) ) {
				$bcc = _str_to_array( $arr['bcc'] );
				if(defined('GLOBAL_BCC') && GLOBAL_BCC!='') {
					$bcc[] = GLOBAL_BCC;
				}
	
				$bcc = clean_email($bcc);
				$ci->email->bcc($bcc);
			}
	
	
			if( isset($arr['from_email']) ) {
				if( isset($arr['from_name']) ) {
					$ci->email->from($arr['from_email'], $arr['from_name']);
				}
				else {
					$ci->email->from($arr['from_email']);
				}
			}
			else {
				$ci->email->from( $ci->site_settings_model->get('no_reply_email'), $ci->site_settings_model->get('no_reply_user') );
			}
	
			if( isset($arr['reply_to']) ) {
				$ci->email->reply_to($arr['reply_to']);
			}
			else {
				$ci->email->reply_to($ci->site_settings_model->get('no_reply_email'), $ci->site_settings_model->get('no_reply_user'));
			}
	
			if( isset($arr['message']) ) {
				$ci->email->message($arr['message']);
			}
			else {
				$ci->email->message('');
			}
	
			$ci->email->send();
			//echo '1';exit;
		}
	}
	else {
		if( defined('BOOL_SEND_MAIL') && BOOL_SEND_MAIL ) {
			$ci = get_instance();
	
			$ci->load->library('email');
			$config['charset'] = 'utf-8';
			$config['protocol'] = 'send-mail';
			$config['useragent'] = 'Cogtime';
			$config['mailtype'] = 'html';

			$config['smtp_host'] = 'mail.acumensoft.info';
			//$config['smtp_user'] = 'testing@acumensofttech.com';
			//$config['smtp_pass'] = 'hello@mail';
			$config['smtp_user'] = 'aradhana.online19@gmail.com';
			$config['smtp_pass'] = '';
			$config['crlf'] = "\r\n";
			$config['newline'] = "\r\n";

			$ci->email->initialize($config);
		
			if( isset($arr['subject']) ) {
				$ci->email->subject($arr['subject']);
			}
			else {
				$ci->email->subject('');
			}
	
			if( isset($arr['to']) ) {
				$ci->email->to($arr['to']);
			}
	
			if( isset($arr['cc']) ) {
				$cc = _str_to_array( $arr['cc'] );
				if(defined('GLOBAL_CC') && GLOBAL_CC!='') {
					$cc[] = GLOBAL_CC;
				}
	
				$cc = clean_email($cc);
				$ci->email->cc($cc);
			}
	
			if( isset($arr['bcc']) ) {
				$bcc = _str_to_array( $arr['bcc'] );
				if(defined('GLOBAL_BCC') && GLOBAL_BCC!='') {
					$bcc[] = GLOBAL_BCC;
				}
	
				$bcc = clean_email($bcc);
				$ci->email->bcc($bcc);
			}
			
			if( isset($arr['from_email']) ) {
				if( isset($arr['from_name']) ) {
					$ci->email->from($arr['from_email'], $arr['from_name']);
				}
				else {
					$ci->email->from($arr['from_email']);
				}
			}
			else {
				$ci->email->from( $ci->site_settings_model->get('no_reply_email'), $ci->site_settings_model->get('no_reply_user') );
			}
	
			if( isset($arr['message']) ) {
				$ci->email->message($arr['message']);
			}
			else {
				$ci->email->message('');
			}
			
			$ci->email->send();
			//echo '2';exit;
			//echo $ci->email->print_debugger();
			//dump($ci->email);
		}
	}
}

/* functions _str_to_array() and clean_email() are borrowed from email library of CI,
* to help write the send_mail() function only. So these two function have no other
* functionality.
*/

function _str_to_array($email) {
	if ( ! is_array($email))
	{
		if (strpos($email, ',') !== FALSE)
		{
			$email = preg_split('/[\s,]/', $email, -1, PREG_SPLIT_NO_EMPTY);
		}
		else
		{
			$email = trim($email);
			settype($email, "array");
		}
	}
	return $email;
}


function clean_email($email) {
	if ( ! is_array($email))
	{
		if (preg_match('/\<(.*)\>/', $email, $match))
		{
			return $match['1'];
		}
		else
		{
			return $email;
		}
	}

	$clean_email = array();

	foreach ($email as $addy)
	{
		if (preg_match( '/\<(.*)\>/', $addy, $match))
		{
			$clean_email[] = $match['1'];
		}
		else
		{
			$clean_email[] = $addy;
		}
	}

	return $clean_email;
}
