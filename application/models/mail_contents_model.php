<?php

class Mail_contents_model extends CI_Model {
	
	public function __construct() {
            parent::__construct();
	}

	function get() {
            $sql = "SELECT * FROM cg_mail_contents ORDER BY name" ;
            $query = $this->db->query($sql);
            $result_arr = $query->result_array();

            return $result_arr;
	}

	
	function get_by_id($id) {
            $sql = "SELECT * FROM cg_mail_contents WHERE id = '".$id."' ORDER BY name";
            $query = $this->db->query($sql);
            $result_arr = $query->result_array();

            return $result_arr[0];
	}

	function get_by_name($name) {
            $sql = "SELECT * FROM cg_mail_contents
                            WHERE `name` = '".$name."' ORDER BY name";
            $query = $this->db->query($sql);
            $result_arr = $query->result_array();
           // echo $sql; die();

            if( is_array($result_arr) && count($result_arr) ) {
                return $result_arr[0];
            }
            else {
                return array('subject'=>'', 'body'=>'');
            }
	}


	function name_exists($name, $current = '') {
            if($current=='') {
                $sql = "SELECT count(*) count FROM cg_mail_contents WHERE name = '".$name."'";
            }
            else {
                $sql = "SELECT count(*) count FROM cg_mail_contents WHERE name = '".$name."' and name != '".$current."'";
            }

            $query = $this->db->query($sql);
            $result_arr = $query->result_array();

            if($result_arr[0]['count'] == 0) {
                return false;
            }
            else {
                return true;
            }
	}

	function insert($arr) {
            //dump($arr);
            $return = $this->db->insert('mail_contents', $arr);
            return $return;
	}

	function update_by_id($id, $arr) {
            $this->db->update('mail_contents', $arr, array('id' => $id));
	}


	function delete_by_id($id) {
            $sql = "DELETE FROM cg_mail_contents WHERE id='".$id."'";

            $this->db->query($sql);
	}
}
