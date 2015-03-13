<?php

class Mail_contents_model extends CI_Model {
	
	public function __construct() {
            parent::__construct();
	}

	function get() {
            $sql = "SELECT * FROM %smail_contents ORDER BY name", $this->db->dbprefix;
            $query = $this->db->query($sql);
            $result_arr = $query->result_array();

            return $result_arr;
	}

	
	function get_by_id($id) {
            $sql = sprintf("SELECT * FROM %smail_contents WHERE id = %s ORDER BY name", $this->db->dbprefix, $id);
            $query = $this->db->query($sql);
            $result_arr = $query->result_array();

            return $result_arr[0];
	}

	function get_by_name($name) {
            $sql = sprintf("SELECT * FROM %smail_contents
                            WHERE `name` = '%s' ORDER BY name", $this->db->dbprefix, $name);
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
                $sql = sprintf("SELECT count(*) count FROM %smail_contents WHERE name = '%s'", $this->db->dbprefix, $name);
            }
            else {
                $sql = sprintf("SELECT count(*) count FROM %smail_contents WHERE name = '%s' and name != '%s'", $this->db->dbprefix, $name, $current);
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
            $sql = sprintf( "DELETE FROM %smail_contents WHERE id=%s", $this->db->dbprefix, $id );

            $this->db->query($sql);
	}
}
