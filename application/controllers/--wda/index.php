<?php
require_once(APPPATH.'controllers/base_controller.php');

require_once(APPPATH.'libraries/multilanguage/MultilingualTMX.php');

class Index extends CI_Controller {	
	function __construct() {
		parent::__construct();

		$this->load->helper('common_helper');
	}
	
	function index() {
		$this->list_mails();
	}

	function list_mails() {
		$data['page'] = 'mail';

		$this->load->model('mail_contents_model');
		$data['mails'] = $this->mail_contents_model->get();

		$this->load->view('mail/index.phtml', $data);
	}

	function add_mail($messages='') {
		$data['page'] = 'mail';

		if($messages=="") {
			$this->session->set_userdata('mail_messages', array());
			$this->session->set_userdata('mail_values', array());
		}

		$this->load->model('mail_contents_model');
		$data['mails'] = $this->mail_contents_model->get();

		if( $this->input->post('submit_button')!='' ) {
			$this->session->set_userdata('mail_messages', array());

			$arr_post = array();
			
			$this->session->set_userdata( 'mail_values', array_merge($arr_post, $_POST) );

			if( $this->input->post('name')=='' ) {
				add_message('mail_messages', 'name', 'Required field');
			}

			if( $this->input->post('subject')=='' ) {
				add_message('mail_messages', 'subject', 'Required field');
			}

			if( $this->input->post('body')=='' ) {
				add_message('mail_messages', 'body', 'Required field');
			}

			if($this->mail_contents_model->name_exists($this->input->post('name'))) {
				add_message('mail_messages', 'name', 'Name exists!');
			}
			//echo count($this->session->userdata('mail_messages'));exit;
			
			if( count($this->session->userdata('mail_messages'))==0 ) {

				$arr['name'] = htmlspecialchars($this->input->post('name'), ENT_QUOTES, 'utf-8');
				$arr['description'] = htmlspecialchars($this->input->post('description'), ENT_QUOTES, 'utf-8');
				$arr['subject'] = htmlspecialchars($this->input->post('subject'), ENT_QUOTES, 'utf-8');
				$arr['body'] = htmlspecialchars($this->input->post('body'), ENT_QUOTES, 'utf-8');	

				$user_id = $this->mail_contents_model->insert($arr);
				
				//echo $this->db->last_query();exit;

				header("location: ".base_url().'mail/index/list_mails.html');
				exit;
				
			}
			else {
				header("location: ".base_url().'mail/index/add_mail/messages.html');
			}
			exit;
		}

		if(count($this->session->userdata('mail_messages'))!=0) {
			$data['result_page'] = $this->session->userdata('mail_values');
			$data['error_messages'] = $this->session->userdata('mail_messages');
		}

		$this->load->view('mail/add_mail.phtml', $data);
	}

	function edit_mail($id, $messages='') {
		$data['page'] = 'mail';

		if($messages=="") {
			$this->session->set_userdata('mail_messages', array());
			$this->session->set_userdata('mail_values', array());
		}

		$this->load->model('mail_contents_model');
		$data['mails'] = $this->mail_contents_model->get();

		$mail = $this->mail_contents_model->get_by_id($id);

		if($this->input->post('submit_button', true)!="") {

			$this->session->set_userdata('mail_messages', array());
			$this->session->set_userdata( 'mail_values', $_POST );

			if( $this->input->post('name')=='' ) {
				add_message('mail_messages', 'name', 'Required field');
			}

			if( $this->input->post('subject')=='' ) {
				add_message('mail_messages', 'subject', 'Required field');
			}

			if( $this->input->post('body')=='' ) {
				add_message('mail_messages', 'body', 'Required field');
			}

			if($this->mail_contents_model->name_exists($this->input->post('name'), $mail['name'])) {
				add_message('mail_messages', 'name', 'Name exists!');
			}
			
			
			if( count($this->session->userdata('mail_messages'))==0 ) {

				$arr['name'] = htmlspecialchars($this->input->post('name'), ENT_QUOTES, 'utf-8');
				$arr['description'] = htmlspecialchars($this->input->post('description'), ENT_QUOTES, 'utf-8');
				$arr['subject'] = htmlspecialchars($this->input->post('subject'), ENT_QUOTES, 'utf-8');
				$arr['body'] = htmlspecialchars($this->input->post('body'), ENT_QUOTES, 'utf-8');	

				$user_id = $this->mail_contents_model->update_by_id($id, $arr);

				header("location: ".base_url().'mail/index/list_mails.html');
				exit;
			}
			else {
				header("location: ".base_url().'modifier-profil/messages.html');
			}
			exit;
		}

		if(count($this->session->userdata('mail_messages'))!=0) {
			$data['result_page'] = $this->session->userdata('mail_values');
			$data['error_messages'] = $this->session->userdata('mail_messages');
		}
		/* to avoid notice error at select boxes, checkboxes, and setting default values */
		else {
			$mail['body'] = html_entity_decode($mail['body']);
			$data['result_page'] = $mail;
		}
		$this->load->view('mail/edit_mail.phtml', $data);
	}

	function delete_mail($id) {
		$this->load->model('mail_contents_model');

		$this->mail_contents_model->delete_by_id($id);
		
		header('location:'.base_url().'mail');
	}

	function list_messages() {
		$data['page'] = 'message';

		$this->load->model('message_contents_model');
		$data['messages'] = $this->message_contents_model->get();

		$this->load->view('mail/list_messages.phtml', $data);
	}

	function add_message($messages='') {
		$data['page'] = 'message';

		if($messages=="") {
			$this->session->set_userdata('message_messages', array());
			$this->session->set_userdata('message_values', array());
		}

		$this->load->model('message_contents_model');
		$data['messages'] = $this->message_contents_model->get();

		if( $this->input->post('submit_button')!='' ) {
			$this->session->set_userdata('message_messages', array());

			$arr_post = array();
			
			$this->session->set_userdata( 'message_values', array_merge($arr_post, $_POST) );

			if( $this->input->post('name')=='' ) {
				add_message('message_messages', 'name', 'Required field');
			}

			if( $this->input->post('subject')=='' ) {
				add_message('message_messages', 'subject', 'Required field');
			}

			if( $this->input->post('body')=='' ) {
				add_message('message_messages', 'body', 'Required field');
			}

			if($this->message_contents_model->name_exists($this->input->post('name'))) {
				add_message('message_messages', 'name', 'Name exists!');
			}
			
			
			if( count($this->session->userdata('message_messages'))==0 ) {

				$arr['name'] = htmlspecialchars($this->input->post('name'), ENT_QUOTES, 'utf-8');
				$arr['description'] = htmlspecialchars($this->input->post('description'), ENT_QUOTES, 'utf-8');
				$arr['subject'] = htmlspecialchars($this->input->post('subject'), ENT_QUOTES, 'utf-8');
				$arr['body'] = htmlspecialchars($this->input->post('body'), ENT_QUOTES, 'utf-8');	

				$user_id = $this->message_contents_model->insert($arr);

				header("location: ".base_url().'mail/index/list_messages.html');
				exit;
				
			}
			else {
				header("location: ".base_url().'mail/index/add_message/messages.html');
			}
			exit;
		}

		if(count($this->session->userdata('message_messages'))!=0) {
			$data['result_page'] = $this->session->userdata('message_values');
			$data['error_messages'] = $this->session->userdata('message_messages');
		}

		$this->load->view('mail/add_message.phtml', $data);
	}

	function edit_message($id, $messages='') {
		$data['page'] = 'message';

		if($messages=="") {
			$this->session->set_userdata('message_messages', array());
			$this->session->set_userdata('message_values', array());
		}

		$this->load->model('message_contents_model');
		$data['messages'] = $this->message_contents_model->get();

		$message = $this->message_contents_model->get_by_id($id);

		if($this->input->post('submit_button', true)!="") {

			$this->session->set_userdata('message_messages', array());
			$this->session->set_userdata( 'message_values', $_POST );

			if( $this->input->post('name')=='' ) {
				add_message('message_messages', 'name', 'Required field');
			}

			if( $this->input->post('subject')=='' ) {
				add_message('message_messages', 'subject', 'Required field');
			}

			if( $this->input->post('body')=='' ) {
				add_message('message_messages', 'body', 'Required field');
			}

			if($this->message_contents_model->name_exists($this->input->post('name'), $message['name'])) {
				add_message('message_messages', 'name', 'Name exists!');
			}
			
			
			if( count($this->session->userdata('message_messages'))==0 ) {

				$arr['name'] = htmlspecialchars($this->input->post('name'), ENT_QUOTES, 'utf-8');
				$arr['description'] = htmlspecialchars($this->input->post('description'), ENT_QUOTES, 'utf-8');
				$arr['subject'] = htmlspecialchars($this->input->post('subject'), ENT_QUOTES, 'utf-8');
				$arr['body'] = htmlspecialchars($this->input->post('body'), ENT_QUOTES, 'utf-8');	

				$user_id = $this->message_contents_model->update_by_id($id, $arr);

				header("location: ".base_url().'mail/index/list_messages.html');
				exit;
			}
			else {
				header("location: ".base_url().'modifier-profil/messages.html');
			}
			exit;
		}

		if(count($this->session->userdata('message_messages'))!=0) {
			$data['result_page'] = $this->session->userdata('message_values');
			$data['error_messages'] = $this->session->userdata('message_messages');
		}
		/* to avoid notice error at select boxes, checkboxes, and setting default values */
		else {
			$message['body'] = html_entity_decode($message['body']);
			$data['result_page'] = $message;
		}
		$this->load->view('mail/edit_message.phtml', $data);
	}

	function delete_message($id) {
		$this->load->model('message_contents_model');

		$this->message_contents_model->delete_by_id($id);
		
		header('location:'.base_url().'mail/index/list_messages');
	}
}



