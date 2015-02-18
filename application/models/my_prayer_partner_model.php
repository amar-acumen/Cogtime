<?php

/* * *******
 * Author: 
 * Date  : 
 * Modified By: 
 * Modified Date:
 * 
 * Purpose:
 *  Model For  
 * 
 * @package 
 * @subpackage 
 * 
 * @link InfModel.php 
 * @link Base_model.php
 * @link controllers/
 * @link views/
 */
require_once(APPPATH . 'models/base_model.php');

class My_prayer_partner_model extends Base_model implements InfModel {
    # constructor definition...

    public function __construct() {
        try {
            parent::__construct();
            $this->conf = get_config();
            $this->load->model("users_model");
            $this->load->model("netpals_model");
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

        try {
            $ret_ = array();

            $language = get_current_language();

            $s_qry = "SELECT   c.id, 
						   c.i_requester_id, 
						   c.i_accepter_id,
						   c.i_deleted_by,
						   c.dt_created_on, 
						   c.dt_accepted_on, 
						   u.id user_id, 
						   u.s_email,
						  
						   u.s_last_name,
						   u.s_first_name ,
						  
						   u.s_profile_photo,
						   u.e_gender,
						   u.i_country_id, 
						   u.i_user_type,
						   u.s_city,
						   u.s_state,
						   u.i_status,
						   u.dt_created_on
						   
					FROM 
						{$this->db->USER_PRAYER_PARTNER} c ,{$this->db->USERS} u "
                    . $s_where;

            /* cn.s_country_name {$this->db->MST_COUNTRY} cn */

            //////////For Pagination///////////*don't change*/
            //$s_qry=str_replace("'","''",$s_qry);//for string operation in procedure
            $s_qry = $s_qry . (trim($s_order_by) != "" ? " ORDER BY " . $s_order_by . "" : "ORDER BY id asc") . " " . (is_numeric($i_start) && is_numeric($i_limit) ? " LIMIT " . intval($i_start) . "," . intval($i_limit) : "");

            #echo ($s_qry);
            //////////end For Pagination//////////                

            $this->db->trans_begin(); ///new                
            $rs = $this->db->query($s_qry);
            $i_cnt = 0;
            if (is_array($rs->result())) {
                foreach ($rs->result() as $row) {
                    $ret_[$i_cnt]["id"] = $row->id; ////always integer
                    $ret_[$i_cnt]["i_requester_id"] = intval($row->i_requester_id);
                    $ret_[$i_cnt]["i_accepter_id"] = intval($row->i_accepter_id);
                    $ret_[$i_cnt]["user_id"] = intval($row->user_id);
                    $ret_[$i_cnt]["s_first_name"] = get_unformatted_string($row->s_first_name);
                    $ret_[$i_cnt]["s_last_name"] = get_unformatted_string($row->s_last_name);

                    $ret_[$i_cnt]["s_displayname"] = $ret_[$i_cnt]["s_first_name"] . ' ' . $ret_[$i_cnt]["s_last_name"];
                    $ret_[$i_cnt]["s_email"] = get_unformatted_string($row->s_email);

                    $ret_[$i_cnt]["s_profile_photo"] = get_unformatted_string($row->s_profile_photo);
                    $ret_[$i_cnt]["s_gender"] = ($row->e_gender == 'M') ? 'Male' : 'Female';
                    $ret_[$i_cnt]["s_city"] = get_unformatted_string($row->s_city);
                    $ret_[$i_cnt]["s_state"] = get_unformatted_string($row->s_state);
                    #causing prob 		#$ret_[$i_cnt]["s_country_name"]  	    =	get_unformatted_string($row->s_country_name); 
                    $ret_[$i_cnt]["i_user_type"] = intval($row->i_user_type);
                    $ret_[$i_cnt]["i_country_id"] = intval($row->i_country_id);
                    $ret_[$i_cnt]["dt_created_on"] = intval($row->dt_created_on);

                    $get_friend_req_sent_status_me_him = $this->get_prayer_partner_status_me_him(
                            intval(decrypt($this->session->userdata('user_id'))), $ret_[$i_cnt]['user_id']);

                    if (count($get_friend_req_sent_status_me_him) > 0) {
                        $ret_[$i_cnt]['display_becomeprayer_partner'] = 'false';
                    }

                    $get_friend_status_me_him = $this->get_prayer_partner_accepted_me_him(
                            intval(decrypt($this->session->userdata('user_id'))), $ret_[$i_cnt]['user_id']);
                    if (count($get_friend_status_me_him) > 0) {
                        $ret_[$i_cnt]['display_alreadyprayer_partner'] = 'true';
                    }

                    $total_PP_arr = $this->get_prayerPartnerId_by_user_id($item['user_id']);
                    $total_PP = count($total_PP_arr);
                    $total_pending_PP_req = $this->total_pending_prayer_partner_recieved($ret_[$i_cnt]['user_id']);

                    ## CHECKING TOTAL PRAYER PARTNERS ##

                    if ($total_PP == 3 || $total_pending_PP_req >= 5) {
                        $ret_[$i_cnt]['is_available'] = 'false';
                    } else {
                        $ret_[$i_cnt]['is_available'] = 'true';
                    }
                    ## CHECKING TOTAL PRAYER PARTNERS ##


                    $if_friend = $this->users_model->if_already_friend(intval(decrypt($this->session->userdata('user_id'))), $ret_[$i_cnt]["user_id"]);

                    if (count($if_friend) > 0) {
                        $ret_[$i_cnt]['if_already_friend'] = 'true';
                    } else {
                        $ret_[$i_cnt]['if_already_friend'] = 'false';
                    }

                    $if_netpal = $this->netpals_model->if_already_netpal(intval(decrypt($this->session->userdata('user_id'))), $ret_[$i_cnt]['user_id']);
                    if (count($arr_already_netpal) > 0) {
                        $ret_[$i_cnt]['already_added_netpal'] = 'true';
                    } else {
                        $ret_[$i_cnt]['already_added_netpal'] = 'false';
                    }



                    $i_cnt++;
                }
                $rs->free_result();
            }
            $this->db->trans_commit(); ///new



            unset($s_qry, $rs, $row, $i_cnt, $s_where, $i_start, $i_limit);
            return $ret_;
        } catch (Exception $err_obj) {
            show_error($err_obj->getMessage());
        }
    }

    /*     * **
     * Fetch Total records
     * @param string $s_where, ex- " status=1 AND deleted=0 " 
     * @returns int on success and FALSE if failed 
     */

    public function gettotal_info($s_where = null) {
        try {
            $ret_ = 0;


            $s_qry = "SELECT COUNT(*) AS i_total 
					FROM 
						{$this->db->USER_PRAYER_PARTNER} c, {$this->db->USERS} u
					" . $s_where;
            $rs = $this->db->query($s_qry);
            $i_cnt = 0;
            if (is_array($rs->result())) {
                foreach ($rs->result() as $row) {
                    $ret_ = intval($row->i_total);
                }
                $rs->free_result();
            }
            $this->db->trans_commit(); ///new
            unset($s_qry, $rs, $row, $i_cnt, $s_where);
            return $ret_;
        } catch (Exception $err_obj) {
            show_error($err_obj->getMessage());
        }
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
        try {
            $i_ret_ = 0; ////Returns false
            if (!empty($info)) {


                $s_qry = "INSERT INTO " . $this->db->USER_PRAYER_PARTNER . " SET ";
                $s_qry.=" i_requester_id=? ";
                $s_qry.=", i_accepter_id=? ";
                $s_qry.=", s_status=? ";
                $s_qry.=", dt_created_on=? ";

                $this->db->trans_begin(); ///new   
                $this->db->query($s_qry, array(
                    intval($info["i_requester_id"]),
                    intval($info["i_accepter_id"]),
                    ($info["s_status"]),
                    get_db_datetime()
                ));
                $i_ret_ = $this->db->insert_id();
                if ($i_ret_) {
                    $logi["msg"] = "Inserting into " . $this->db->USER_PRAYER_PARTNER . " ";
                    $logi["sql"] = serialize(array($s_qry, array(
                            intval($info["i_requester_id"]),
                            intval($info["i_accepter_id"]),
                            ($info["s_status"]),
                            get_db_datetime()
                        )));
                    $this->log_info($logi);
                    unset($logi);
                    $this->db->trans_commit(); ///new   
                } else {
                    $this->db->trans_rollback(); ///new
                }
            }
            unset($s_qry);
            return $i_ret_;
        } catch (Exception $err_obj) {
            show_error($err_obj->getMessage());
        }
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

    /*     * ****
     * Deletes all or single record from db. 
     * For Master entries deletion only change the flag i_is_deleted. 
     *
     * @param int $i_id, id value to be deleted used in where clause 
     * @returns $i_rows_affected  on success and FALSE if failed 
     * 
     */

    public function delete_info($i_id) {
        try {
            $i_ret_ = 0; ////Returns false

            if (intval($i_id) > 0) {
                $s_qry = "UPDATE  " . $this->db->USER_PRAYER_PARTNER . " ";
                $s_qry.="SET s_status='deleted' WHERE id=? ";

                $this->db->trans_begin(); ///new  
                $this->db->query($s_qry, array(intval($i_id)));
                $i_ret_ = $this->db->affected_rows();
                if ($i_ret_) {
                    $logi["msg"] = "UPDATE " . $this->db->USER_PRAYER_PARTNER . " ";
                    $logi["sql"] = serialize(array($s_qry, array(intval($i_id))));
                    $this->log_info($logi);
                    unset($logi);
                    $this->db->trans_commit(); ///new   
                } else {
                    $this->db->trans_rollback(); ///new
                }
            } elseif (intval($i_id) == -1) {////Deleting All
                $s_qry = "DELETE FROM " . $this->db->USERS . " ";
                $this->db->trans_begin(); ///new
                $this->db->query($s_qry);
                $i_ret_ = $this->db->affected_rows();
                if ($i_ret_) {
                    $logi["msg"] = "Deleting all information from " . $this->db->USERS . " ";
                    $logi["sql"] = serialize(array($s_qry));
                    $this->log_info($logi);
                    unset($logi);
                    $this->db->trans_commit(); ///new   
                } else {
                    $this->db->trans_rollback(); ///new
                }
            }
            unset($s_qry, $i_id);
            return $i_ret_;
        } catch (Exception $err_obj) {
            show_error($err_obj->getMessage());
        }
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

    public function get_status_me_him($i_me, $i_him) {

        try {
            $ret_ = array();
            $language = get_current_language();
            if (intval($i_me) > 0 && intval($i_him) > 0 && intval($i_me) != intval($i_him)) {
                $s_qry = sprintf("SELECT 
									c.id, 
									c.i_requester_id, 
									c.i_accepter_id,
									c.s_status,
									c.dt_created_on, 
									c.dt_accepted_on, 
									u.id user_id, 
									u.s_email,
									u.s_name, 
									u.s_first_name, 
									u.s_profile_photo, 
									u.i_user_type,
									u.i_status, 
									cn.s_country_name 
					FROM 
							{$this->db->USER_PRAYER_PARTNER} c, {$this->db->USERS} u, {$this->db->MST_COUNTRY} cn 
				   WHERE 
					( (c.i_requester_id = %s and c.i_accepter_id = %s)  OR (c.i_accepter_id = %s and c.i_requester_id = %s) )
					AND u.id=c.i_requester_id 
					
			", $i_me, $i_him, $i_me, $i_him);



                $this->db->trans_begin(); ///new                       
                $rs = $this->db->query($s_qry);
                if (is_array($rs->result())) {
                    foreach ($rs->result() as $row) {

                        $ret_["id"] = $row->id; ////always integer
                        $ret_["i_requester_id"] = intval($row->i_requester_id);
                        $ret_["i_accepter_id"] = intval($row->i_accepter_id);
                        $ret_["user_id"] = intval($row->user_id);
                        $ret_["s_status"] = $row->s_status;
                        $ret_["s_name"] = get_unformatted_string($row->s_name);
                        $ret_["s_email"] = get_unformatted_string($row->s_email);
                        $ret_["s_profile_photo"] = get_unformatted_string($row->s_profile_photo);
                        $ret_["s_gender"] = $row->s_gender;
                        $ret_["s_country_name"] = get_unformatted_string($row->s_country_name);
                        $ret_["i_user_type"] = intval($row->i_user_type);




                        // $ret_[$i_cnt]["dt_created_on"]	=   getShortDate($row->dt_created_on); 
                    }
                    $rs->free_result();
                }
                $this->db->trans_commit(); ///new
                unset($s_qry, $rs, $row, $i_me, $i_him);
            }
            return $ret_;
        } catch (Exception $err_obj) {
            show_error($err_obj->getMessage());
        }
    }

    public function update_by_requester_accepter($arr = array(), $i_requester_id, $i_accepter_id) {
        if (count($arr) == 0) {
            return null;
        }
        $arr['dt_accepted_on'] = get_db_datetime();
        $_ret = $this->db->update("{$this->db->USER_PRAYER_PARTNER}", $arr, array('i_requester_id' => $i_requester_id, 'i_accepter_id' => $i_accepter_id));
        #echo $this->db->last_query();

        $q_count = "(SELECT count(*) AS count FROM cg_prayer_partner WHERE `i_requester_id`=" . $i_accepter_id . " AND `i_accepter_id`=" . $i_requester_id . " AND `i_deleted_by`=1)";
        $res = $this->db->query($q_count)->row_array();


        if ($res['count']) {

            $_ret = $this->db->update('prayer_partner', $arr, array('i_requester_id' => $i_accepter_id, 'i_accepter_id' => $i_requester_id));
        }



        return $_ret;
    }

    /* add info for sending invitation mail */

    public function add_invitation_info($info) {
        try {
            $i_ret_ = 0; ////Returns false
            if (!empty($info)) {


                $s_qry = "INSERT INTO " . $this->db->INVITATIONS . " SET ";
                $s_qry.="  i_user_id=? ";
                $s_qry.=", s_firstname=? ";
                $s_qry.=", s_lastname=? ";
                $s_qry.=", s_email=? ";
                $s_qry.=", i_country_id=? ";
                $s_qry.=", i_entity_id=? ";
                $s_qry.=", s_message=? ";
                $s_qry.=", dt_created_on=? ";

                $this->db->trans_begin(); ///new   
                $this->db->query($s_qry, array(
                    intval($info["i_user_id"]),
                    get_formatted_string($info["s_firstname"]),
                    get_formatted_string($info["s_lastname"]),
                    get_formatted_string($info["s_email"]),
                    intval($info["i_country_id"]),
                    intval($info["i_entity_id"]),
                    get_formatted_string($info["s_message"]),
                    get_db_datetime()
                ));
                $i_ret_ = $this->db->insert_id();
                if ($i_ret_) {
                    $logi["msg"] = "Inserting into " . $this->db->INVITATIONS . " ";
                    $logi["sql"] = serialize(array($s_qry, array(
                            intval($info["i_user_id"]),
                            get_formatted_string($info["s_firstname"]),
                            get_formatted_string($info["s_lastname"]),
                            get_formatted_string($info["s_email"]),
                            intval($info["i_country_id"]),
                            intval($info["i_entity_id"]),
                            get_formatted_string($info["s_message"]),
                            get_db_datetime()
                        )));
                    $this->log_info($logi);
                    unset($logi);
                    $this->db->trans_commit(); ///new   
                } else {
                    $this->db->trans_rollback(); ///new
                }
            }
            unset($s_qry);
            return $i_ret_;
        } catch (Exception $err_obj) {
            show_error($err_obj->getMessage());
        }
    }

    public function cancel_friend_request_sent($arr = array()) {
        if (count($arr) == 0) {
            return null;
        }
        #$arr['dt_accepted_on'] = get_db_datetime();
        $_ret = $this->db->update("{$this->db->USER_PRAYER_PARTNER}", array('i_deleted_by' => 2, 's_status' => 'deleted'), $arr);

        #echo $this->db->last_query();
        return $_ret;
    }

    public function decline_friend_request_recieved($arr = array()) {
        if (count($arr) == 0) {
            return null;
        }
        #$arr['dt_accepted_on'] = get_db_datetime();
        $_ret = $this->db->update("{$this->db->USER_PRAYER_PARTNER}", array('i_deleted_by' => 2, 's_status' => 'rejected'), $arr);

        #echo $this->db->last_query();
        return $_ret;
    }

    public function get_prayerPartnerId_by_user_id($i_user_id) {

        try {
            $ret_ = array();

            $language = get_current_language();
            $s_where = "WHERE 
						1
						AND c.s_status = 'accepted' 
						AND u.i_status=1 
						AND i_deleted_by = 1
						AND
						((c.i_requester_id = '" . $i_user_id . "' AND u.id=c.i_accepter_id ) 
						OR (c.i_accepter_id = '" . $i_user_id . "' AND u.id=c.i_requester_id ))";

            $s_qry = "SELECT   c.id, c.i_requester_id, c.i_accepter_id
					 FROM 
						{$this->db->USER_PRAYER_PARTNER} c ,{$this->db->USERS} u "
                    . $s_where;

            #echo nl2br($s_qry);

            $this->db->trans_begin(); ///new                
            $rs = $this->db->query($s_qry);
            $i_cnt = 0;
            if (is_array($rs->result())) {
                foreach ($rs->result() as $row) {
                    if ($i_user_id == $row->i_requester_id) {
                        $ret_[$i_cnt] = $row->i_accepter_id; ////always integer
                    } else if ($i_user_id == $row->i_accepter_id) {
                        $ret_[$i_cnt] = $row->i_requester_id;
                    }
                    $i_cnt++;
                }
                $rs->free_result();
            }
            $this->db->trans_commit(); ///new
            // pr($ret_);
            //pr(array_unique($ret_));				

            unset($s_qry, $rs, $row, $i_cnt, $s_where, $i_start, $i_limit);
            return array_unique($ret_);
        } catch (Exception $err_obj) {
            show_error($err_obj->getMessage());
        }
    }

    # function to check if friend_request_already_sent

    function friend_request_already_sent($i_requester_id = '', $i_accepter_id = '') {
        $SQL = sprintf("SELECT COUNT(*) AS `check_count` FROM %s WHERE `i_requester_id`='%s'  AND `i_accepter_id` = '%s' AND `s_status` = 'pending'  ", $this->db->USER_PRAYER_PARTNER, $i_requester_id, $i_accepter_id);
        $ROW = $this->db->query($SQL)->row_array(); #echo $this->db->last_query(); exit;

        if ($ROW['check_count'])
            return 1;
        else
            return 0;
    }

    # delete friend .

    public function delete_friend($arr = array()) {
        if (count($arr) == 0) {
            return null;
        }
        #$arr['dt_accepted_on'] = get_db_datetime();
        $SQL = sprintf("DELETE FROM %s WHERE (`i_requester_id`='{$arr['i_requester_id']}'  AND `i_accepter_id` = '{$arr['i_accepter_id']}' ) 
						OR (`i_requester_id`='{$arr['i_accepter_id']}'  AND `i_accepter_id` = '{$arr['i_requester_id']}' AND `s_status`='{$arr['s_status']}' ) ", $this->db->USER_PRAYER_PARTNER);

        $this->db->query($SQL);
        $ret_ = $this->db->affected_rows();
        #echo $this->db->last_query(); 
        return $ret_;
    }

### friends -> wna b prayer partner (Y)-> show them

    public function get_prayer_partner_suggestion($s_where = null, $i_start = null, $i_limit = null, $s_order_by = null) {/*

      try
      {
      $ret_=array();
      $where_cond = '';
      echo	$s_qry = "SELECT
      u.id as user_id,
      u.s_email,

      u.s_last_name,
      u.s_first_name ,

      u.s_profile_photo,
      u.e_gender,
      u.i_country_id,
      u.i_user_type,
      u.s_city,
      u.s_state,
      u.i_status,
      u.dt_created_on,

      u.e_want_prayer_partner,
      d.s_name ,
      uc.s_country_name

      FROM
      {$this->db->USERS} u
      LEFT JOIN {$this->db->MST_COUNTRY} AS uc  ON uc.id = u.i_country_id
      LEFT JOIN {$this->db->DENOMINATION} AS d ON u.i_id_denomination = d.id "
      .$s_where;

      /*cn.s_country_name {$this->db->MST_COUNTRY} cn

      //////////For Pagination///////////*don't change
      //$s_qry=str_replace("'","''",$s_qry);//for string operation in procedure
      $s_qry= $s_qry.(trim($s_order_by)!=""?" ORDER BY ".$s_order_by."":"ORDER BY id asc")." ".(is_numeric($i_start) && is_numeric($i_limit)?" LIMIT ".intval($i_start).",".intval($i_limit):"");

      //echo nl2br($s_qry);
      //////////end For Pagination//////////

      $this->db->trans_begin();///new
      $rs=$this->db->query($s_qry);
      //pr($rs->result(),1);
      $i_cnt=0;
      if(is_array($rs->result()))
      {
      foreach($rs->result() as $row)
      {
      $ret_[$i_cnt]["id"]				=	$row->id;////always integer
      //$ret_[$i_cnt]["i_requester_id"]		=	intval($row->i_requester_id);
      //$ret_[$i_cnt]["i_accepter_id"]		=	intval($row->i_accepter_id);
      $ret_[$i_cnt]["user_id"]		=	intval($row->user_id);
      $ret_[$i_cnt]["s_first_name"]		=	get_unformatted_string($row->s_first_name);
      $ret_[$i_cnt]["s_last_name"]				=	get_unformatted_string($row->s_last_name);

      $ret_[$i_cnt]["s_displayname"]		=	$ret_[$i_cnt]["s_first_name"].' '.$ret_[$i_cnt]["s_last_name"]	;
      $ret_[$i_cnt]["s_email"]  	   			 =	get_unformatted_string($row->s_email);

      $ret_[$i_cnt]["s_profile_photo"]  	    =	get_unformatted_string($row->s_profile_photo);
      $ret_[$i_cnt]["s_gender"]				=	($row->e_gender == 'M')?'Male':'Female';
      $ret_[$i_cnt]["s_city"]  	   			 =	get_unformatted_string($row->s_city);
      $ret_[$i_cnt]["s_state"]  	   			 =	get_unformatted_string($row->s_state);
      $ret_[$i_cnt]["s_country_name"]  	    =	get_unformatted_string($row->s_country_name);
      $ret_[$i_cnt]["s_denomination"]          =    get_unformatted_string($row->s_name);
      $ret_[$i_cnt]["i_user_type"]			=	intval($row->i_user_type);
      $ret_[$i_cnt]["i_country_id"]			=	intval($row->i_country_id);
      $ret_[$i_cnt]["dt_created_on"]			=	intval($row->dt_created_on);
      $ret_[$i_cnt]["e_want_prayer_partner"]	=	($row->e_want_prayer_partner);
      $get_friend_req_sent_status_me_him = $this->get_prayer_partner_status_me_him(
      intval(decrypt($this->session->userdata('user_id'))) , $ret_[$i_cnt]['user_id']);

      if(count($get_friend_req_sent_status_me_him) > 0  ) {
      $ret_[$i_cnt]['display_becomeprayer_partner']     ='false';
      }

      $get_friend_status_me_him = $this->get_prayer_partner_accepted_me_him(
      intval(decrypt($this->session->userdata('user_id'))) , $ret_[$i_cnt]['user_id']);
      if(count($get_friend_status_me_him) > 0  ) {
      $ret_[$i_cnt]['display_alreadyprayer_partner']     ='true';
      }

      $total_PP_arr = $this->get_prayerPartnerId_by_user_id($ret_[$i_cnt]['user_id']);

      $total_PP = count($total_PP_arr);

      $total_pending_PP_req = $this->total_pending_prayer_partner_recieved($ret_[$i_cnt]['user_id']);

      ## CHECKING TOTAL PRAYER PARTNERS ##

      if($total_PP == 3 || $total_pending_PP_req >= 5){
      $ret_[$i_cnt]['is_available']     ='false';

      }else{
      $ret_[$i_cnt]['is_available']     ='true';
      }
      ## CHECKING TOTAL PRAYER PARTNERS ##

      $i_cnt++;
      }
      $rs->free_result();
      }
      $this->db->trans_commit();///new
      unset($s_qry,$rs,$row,$i_cnt,$s_where,$i_start,$i_limit);


      return $ret_;
      }
      catch(Exception $err_obj)
      {
      show_error($err_obj->getMessage());
      }

     */
    }

    /*     * **
     * Fetch Total records
     * @param string $s_where, ex- " status=1 AND deleted=0 " 
     * @returns int on success and FALSE if failed 
     */

    public function gettotal_prayer_partner_suggestion($s_where = null) {/*
      try
      {
      $ret_=0;


      $s_qry = "SELECT COUNT(*) AS i_total  FROM( SELECT COUNT(*)
      FROM
      {$this->db->USERS} u
      LEFT JOIN {$this->db->MST_COUNTRY} AS uc  ON uc.id = u.i_country_id
      LEFT JOIN {$this->db->DENOMINATION} AS d ON u.i_id_denomination = d.id ".$s_where.") AS derived_tbl";
      $rs=$this->db->query($s_qry);

      #echo nl2br($s_qry);  #exit;
      $i_cnt=0;
      if(is_array($rs->result()))
      {
      foreach($rs->result() as $row)
      {
      $ret_=intval($row->i_total);
      }
      $rs->free_result();
      }
      $this->db->trans_commit();///new
      unset($s_qry,$rs,$row,$i_cnt,$s_where);
      return $ret_;
      }
      catch(Exception $err_obj)
      {
      show_error($err_obj->getMessage());
      }
     */
    }

    public function get_prayer_partner_status_me_him($i_me, $i_him) {
        try {
            $ret_ = array();
            if (intval($i_me) > 0 && intval($i_him) > 0 && intval($i_me) != intval($i_him)) {
                $s_qry = sprintf("SELECT 
								c.id, 
								c.i_requester_id, 
								c.i_accepter_id,
								c.s_status,
								c.dt_created_on, 
								c.dt_accepted_on, 
								u.id user_id, 
								u.s_email,
								u.s_first_name, 
								
								u.s_profile_photo, 
								
								u.i_status 
								
				FROM 
						{$this->db->USER_PRAYER_PARTNER} c, {$this->db->USERS} u
			   WHERE 
				( (c.i_requester_id = %s and c.i_accepter_id = %s)  OR (c.i_accepter_id = %s and c.i_requester_id = %s) )
				AND c.s_status = 'pending' 
				AND u.id=c.i_requester_id 
				
		", $i_me, $i_him, $i_me, $i_him);

                #cn.s_country_name  , {$this->db->MST_COUNTRY} cn 

                $this->db->trans_begin(); ///new                       
                $rs = $this->db->query($s_qry); #echo $this->db->last_query(); exit;
                if (is_array($rs->result())) {
                    foreach ($rs->result() as $row) {

                        $ret_["id"] = $row->id; ////always integer
                        $ret_["i_requester_id"] = intval($row->i_requester_id);
                        $ret_["i_accepter_id"] = intval($row->i_accepter_id);
                        $ret_["s_first_name"] = get_unformatted_string($row->s_first_name);
                        $ret_["s_email"] = get_unformatted_string($row->s_email);
                        $ret_["s_profile_photo"] = get_unformatted_string($row->s_profile_photo);
                        $ret_["s_gender"] = ($row->e_gender == 'M') ? 'Male' : 'Female';
                        $ret_["s_country_name"] = get_unformatted_string($row->s_country_name);
                    }
                    $rs->free_result();
                }
                $this->db->trans_commit(); ///new
                unset($s_qry, $rs, $row, $i_me, $i_him);
            }
            #pr($ret_);
            return $ret_;
        } catch (Exception $err_obj) {
            show_error($err_obj->getMessage());
        }
    }

    public function fetch_multi_online_friends($s_where = null, $i_start = null, $i_limit = null, $s_order_by = null) {

        try {
            $ret_ = array();
            $this->load->model("users_model");
            $this->load->model("netpals_model");

            $s_qry = "SELECT   c.id, 
						   c.i_requester_id, 
						   c.i_accepter_id,
						   c.i_deleted_by,
						   c.dt_created_on, 
						   c.dt_accepted_on, 
						   u.id user_id, 
						   u.s_email,
						  
						   u.s_last_name,
						   u.s_first_name ,
						  
						   u.s_profile_photo,
						   u.e_gender,
						   u.i_country_id, 
						   u.i_user_type,
						   u.s_city,
						   u.s_state,
						   u.s_about_me,
						   u.i_status,
						   u.dt_created_on,
						   uon.s_status 
						   
					FROM 
						{$this->db->USER_PRAYER_PARTNER} c ,{$this->db->USERS} u 
						LEFT JOIN cg_users_online AS uon ON u.id = uon.i_user_id "
                    . $s_where;

            /* cn.s_country_name {$this->db->MST_COUNTRY} cn */

            //////////For Pagination///////////*don't change*/
            //$s_qry=str_replace("'","''",$s_qry);//for string operation in procedure
           $s_qry = $s_qry . (trim($s_order_by) != "" ? " ORDER BY " . $s_order_by . "" : "ORDER BY id asc") . " " . (is_numeric($i_start) && is_numeric($i_limit) ? " LIMIT " . intval($i_start) . "," . intval($i_limit) : "");

            #echo nl2br($s_qry);
            //////////end For Pagination//////////                

            $this->db->trans_begin(); ///new                
            $rs = $this->db->query($s_qry);
            $i_cnt = 0;
            if (is_array($rs->result())) {
                foreach ($rs->result() as $row) {
                    $ret_[$i_cnt]["id"] = $row->id; ////always integer
                    $ret_[$i_cnt]["i_requester_id"] = intval($row->i_requester_id);
                    $ret_[$i_cnt]["i_accepter_id"] = intval($row->i_accepter_id);
                    $ret_[$i_cnt]["user_id"] = intval($row->user_id);
                    $ret_[$i_cnt]["s_first_name"] = get_unformatted_string($row->s_first_name);
                    $ret_[$i_cnt]["s_last_name"] = get_unformatted_string($row->s_last_name);

                    $ret_[$i_cnt]["s_displayname"] = $ret_[$i_cnt]["s_first_name"] . ' ' . $ret_[$i_cnt]["s_last_name"];
                    $ret_[$i_cnt]["s_email"] = get_unformatted_string($row->s_email);

                    $ret_[$i_cnt]["s_profile_photo"] = get_unformatted_string($row->s_profile_photo);
                    $ret_[$i_cnt]["s_gender"] = ($row->e_gender == 'M') ? 'Male' : 'Female';
                    $ret_[$i_cnt]["s_city"] = get_unformatted_string($row->s_city);
                    $ret_[$i_cnt]["s_state"] = get_unformatted_string($row->s_state);
                    #causing prob 		#$ret_[$i_cnt]["s_country_name"]  	    =	get_unformatted_string($row->s_country_name); 
                    $ret_[$i_cnt]["i_user_type"] = intval($row->i_user_type);
                    $ret_[$i_cnt]["i_country_id"] = intval($row->i_country_id);
                    $ret_[$i_cnt]["dt_created_on"] = intval($row->dt_created_on);
                    $ret_[$i_cnt]["s_about_me"] = get_unformatted_string($row->s_about_me);

                    $if_friend = $this->users_model->if_already_friend(intval(decrypt($this->session->userdata('user_id'))), $ret_[$i_cnt]["user_id"]);

                    if (count($if_friend) > 0) {
                        $ret_[$i_cnt]['if_already_friend'] = 'true';
                    } else {
                        $ret_[$i_cnt]['if_already_friend'] = 'false';
                    }

                    $if_netpal = $this->netpals_model->if_already_netpal(intval(decrypt($this->session->userdata('user_id'))), $ret_[$i_cnt]['user_id']);
                    if (count($if_netpal) > 0) {
                        $ret_[$i_cnt]['already_added_netpal'] = 'true';
                    } else {
                        $ret_[$i_cnt]['already_added_netpal'] = 'false';
                    }


                    $i_cnt++;
                }
                $rs->free_result();
            }
            $this->db->trans_commit(); ///new



            unset($s_qry, $rs, $row, $i_cnt, $s_where, $i_start, $i_limit);
            return $ret_;
        } catch (Exception $err_obj) {
            show_error($err_obj->getMessage());
        }
    }

    /*     * **
     * Fetch Total records
     * @param string $s_where, ex- " status=1 AND deleted=0 " 
     * @returns int on success and FALSE if failed 
     */

    public function gettotal_online_friends($s_where = null) {
        try {
            $ret_ = 0;


            $s_qry = "SELECT COUNT(*) AS i_total  FROM(SELECT COUNT(*)  
					     FROM 
						{$this->db->USER_PRAYER_PARTNER} c, {$this->db->USERS} u
						LEFT JOIN cg_users_online AS uon ON u.id = uon.i_user_id 
					" . $s_where . ") AS derived_tbl";
            $rs = $this->db->query($s_qry);
            $i_cnt = 0;
            if (is_array($rs->result())) {
                foreach ($rs->result() as $row) {
                    $ret_ = intval($row->i_total);
                }
                $rs->free_result();
            }
            $this->db->trans_commit(); ///new
            unset($s_qry, $rs, $row, $i_cnt, $s_where);
            return $ret_;
        } catch (Exception $err_obj) {
            show_error($err_obj->getMessage());
        }
    }

    # function to check total total_pending_prayer partner_request

    function total_pending_prayer_partner_request($i_requester_id = '', $i_accepter_id = '') {
        $SQL = sprintf("SELECT COUNT(*) AS `check_count` FROM %s WHERE  (`i_requester_id`='%s'  OR `i_accepter_id` = '%s')  AND `s_status` = 'pending'", $this->db->USER_PRAYER_PARTNER, $i_requester_id, $i_requester_id);
        $ROW = $this->db->query($SQL)->row_array(); #echo $this->db->last_query(); exit;

        if ($ROW['check_count'])
            return $ROW['check_count'];
        else
            return 0;
    }

    # function to check total pending prayer_partner sent

    function total_pending_prayer_partner_sent($i_requester_id = '') {
        $SQL = sprintf("SELECT COUNT(*) AS `check_count` FROM %s WHERE (`i_requester_id`='%s' ) AND `s_status` = 'pending' ", $this->db->USER_PRAYER_PARTNER, $i_requester_id);
        $ROW = $this->db->query($SQL)->row_array(); #echo $this->db->last_query(); exit;

        if ($ROW['check_count'])
            return $ROW['check_count'];
        else
            return 0;
    }

    # function to check total pending prayer_partner recieved

    function total_pending_prayer_partner_recieved($i_requester_id = '') {
        $SQL = sprintf("SELECT COUNT(*) AS `check_count` FROM %s WHERE (`i_accepter_id`='%s' ) AND `s_status` = 'pending' ", $this->db->USER_PRAYER_PARTNER, $i_requester_id);
        $ROW = $this->db->query($SQL)->row_array(); //echo $this->db->last_query(); //exit;
        //echo  $ROW['check_count'];

        if ($ROW['check_count'])
            return $ROW['check_count'];
        else
            return 0;
    }

    public function get_prayer_partner_accepted_me_him($i_me, $i_him) {
        try {
            $ret_ = array();
            if (intval($i_me) > 0 && intval($i_him) > 0 && intval($i_me) != intval($i_him)) {
                $s_qry = sprintf("SELECT 
								c.id, 
								c.i_requester_id, 
								c.i_accepter_id,
								c.s_status,
								c.dt_created_on, 
								c.dt_accepted_on, 
								u.id user_id, 
								u.s_email,
								u.s_first_name, 
								
								u.s_profile_photo, 
								
								u.i_status 
								
				FROM 
						{$this->db->USER_PRAYER_PARTNER} c, {$this->db->USERS} u
			   WHERE 
				( (c.i_requester_id = %s and c.i_accepter_id = %s)  OR (c.i_accepter_id = %s and c.i_requester_id = %s) )
				AND c.s_status = 'accepted' 
				AND u.id=c.i_requester_id 
				
		", $i_me, $i_him, $i_me, $i_him);

                #cn.s_country_name  , {$this->db->MST_COUNTRY} cn 

                $this->db->trans_begin(); ///new                       
                $rs = $this->db->query($s_qry); //echo $this->db->last_query(); //exit;
                if (is_array($rs->result())) {
                    foreach ($rs->result() as $row) {

                        $ret_["id"] = $row->id; ////always integer
                        $ret_["i_requester_id"] = intval($row->i_requester_id);
                        $ret_["i_accepter_id"] = intval($row->i_accepter_id);
                        $ret_["s_first_name"] = get_unformatted_string($row->s_first_name);
                        $ret_["s_email"] = get_unformatted_string($row->s_email);
                        $ret_["s_profile_photo"] = get_unformatted_string($row->s_profile_photo);
                        $ret_["s_gender"] = ($row->e_gender == 'M') ? 'Male' : 'Female';
                        $ret_["s_country_name"] = get_unformatted_string($row->s_country_name);
                    }
                    $rs->free_result();
                }
                $this->db->trans_commit(); ///new
                unset($s_qry, $rs, $row, $i_me, $i_him);
            }
            #pr($ret_);
            return $ret_;
        } catch (Exception $err_obj) {
            show_error($err_obj->getMessage());
        }
    }

    ##-------------- read-write prayer partner -----------------------

    public function fetch_prayer_partner_points($arr = array()) {
        //pr($arr);
        $qty = sprintf("SELECT 
						p.*,
						u.s_first_name 
						FROM %s AS p 
						LEFT JOIN {$this->db->USERS} AS u ON u.id = p.i_giving_user_id 
						WHERE (`i_rec_user_id` = %s AND `i_giving_user_id` = %s )
						OR (`i_rec_user_id` = %s AND `i_giving_user_id` = %s)
						
						ORDER BY p.id DESC", $this->db->PRAYER_PARTNER_POINTS, $arr['i_rec_user_id'], $arr['i_giving_user_id'], $arr['i_giving_user_id'], $arr['i_rec_user_id']);
        $res = $this->db->query($qty)->result_array();

        return $res;
    }

    public function update_prayer_partner_point($arr = array()) {
        $qty = $this->db->update($this->db->PRAYER_PARTNER_POINTS, $arr, array('i_giving_user_id' => $arr['i_giving_user_id'], 'i_rec_user_id' => $arr['i_rec_user_id']));
        $res = $this->db->query($qty);

        $rows = $this->db->affected_rows();


        //echo "res : ".$rows;
        return $rows;
    }

    public function insert_prayer_partner_point($arr = array()) {
        $qty = $this->db->insert($this->db->PRAYER_PARTNER_POINTS, $arr);
        //echo $res = $this->db->query($qty); //echo $this->db->last_query();
        //$result = $this->db->insert_id();
        return $qty;
    }

    public function edit_prayer_partner_point($arr = array(), $row_id = '') {
        $qty = $this->db->where('id', $row_id)->update($this->db->PRAYER_PARTNER_POINTS, $arr);
        $res = $this->db->query($qty);
        $rows = $this->db->affected_rows();
        return $rows;
    }

    ##--------------- end of read-write prayer partner -------------------------

    function get_prayer_partner_sugg($s_where = null, $s_like_where, $s_order_by = null, $start_limit = null, $no_of_page = null, $timeout = '') {
        if ($timeout == '') {
            $timeout = $this->timeout;
        }

        $timestamp = time() - $timeout;

        if ("$start_limit" == '') {
            $limit = '';
        } else {
            $start_limit = (int) $start_limit;
            $no_of_page = (int) $no_of_page;
            $limit = ' limit ' . $start_limit . ', ' . $no_of_page;
        }

        $ORDERBY = trim($s_order_by) != "" ? " ORDER BY " . $s_order_by . "" : "ORDER BY id asc";
        $user_id = decrypt($this->session->userdata('user_id'));
        $sql = sprintf(" SELECT derived_tbl.* FROM (
							(SELECT 
								  
								  u.id as user_id,
								  u.s_email,
								  
								  u.s_last_name,
								  u.s_first_name ,
								  CONCAT(u.s_first_name,' ',u.s_last_name) as s_displayname, 
								  u.s_profile_photo,
								  u.e_gender,
								  u.i_country_id,
								  u.i_user_type,
								 
								  u.i_status,
								  u.dt_created_on,
								  
								  u.e_want_prayer_partner,
								  d.s_name ,
								 con.s_country,
								 s.s_state,
						    	 c.s_city	
								 
								 FROM  {$this->db->USERS} u  
								 LEFT JOIN cg_country con ON con.id = u.i_country_id
								 LEFT JOIN cg_state s ON s.id = u.i_state_id
								 LEFT JOIN cg_city c ON u.i_city_id = c.id
								 LEFT JOIN {$this->db->DENOMINATION} AS d ON u.i_id_denomination = d.id 
								 WHERE 1  
								 %1\$s   
							     ORDER BY u.`dt_created_on` DESC )
                            
                            UNION
                            
                         (  SELECT
						 	
						 	u.id as user_id,
							u.s_email,
							
							u.s_last_name,
							u.s_first_name ,
							CONCAT(u.s_first_name,' ',u.s_last_name) as s_displayname,
							u.s_profile_photo,
							u.e_gender,
							u.i_country_id,
							u.i_user_type,
							
							u.i_status,
							u.dt_created_on,
							
							u.e_want_prayer_partner,
							d.s_name ,
							con.s_country,
							s.s_state,
						    c.s_city
							
							FROM  {$this->db->USERS} u  
 						    LEFT JOIN cg_country con ON con.id = u.i_country_id
							LEFT JOIN cg_state s ON s.id = u.i_state_id
							LEFT JOIN cg_city c ON u.i_city_id = c.id
							LEFT JOIN {$this->db->DENOMINATION} AS d ON u.i_id_denomination = d.id  WHERE 1  
							 %2\$s                                      
                            ORDER BY u.`dt_created_on` DESC )) as  derived_tbl %4\$s  "
                , $s_where, $s_like_where, $timestamp, $limit);
//echo $sql;
        $query = $this->db->query($sql);
        $result_arr = $query->result_array();
		//pr($result_arr,1);



        if (is_array($result_arr) && count($result_arr)) {
            foreach ($result_arr as $key => $item) {

                ## CHECKING 100% MATCH OR LIKELY MATCH  ##
                $SQL_EXACT_COUNT = sprintf(" SELECT derived_tbl.* FROM (
							(SELECT 
								  'Y' as flag_fld
								 
								 FROM  {$this->db->USERS} u  
 						          LEFT JOIN cg_country con ON con.id = u.i_country_id
								 LEFT JOIN cg_state s ON s.id = u.i_state_id
								 LEFT JOIN cg_city c ON u.i_city_id = c.id
								 LEFT JOIN {$this->db->DENOMINATION} AS d ON u.i_id_denomination = d.id 
								 WHERE 1  AND u.id = '%3\$s'
								 %1\$s    
							     ORDER BY u.`dt_created_on` DESC )
                            
                             UNION
                            
                            (SELECT
						 	  	'N' as flag_fld
						 
								FROM  {$this->db->USERS} u  
 						    	 LEFT JOIN cg_country con ON con.id = u.i_country_id
								 LEFT JOIN cg_state s ON s.id = u.i_state_id
								 LEFT JOIN cg_city c ON u.i_city_id = c.id
								LEFT JOIN {$this->db->DENOMINATION} AS d ON u.i_id_denomination = d.id 
								WHERE 1  AND u.id = '%3\$s'
								 %2\$s                                      
                            	ORDER BY u.`dt_created_on` DESC )) as  derived_tbl %5\$s  "
                        , $s_where, $s_like_where, $item['user_id'], $timestamp, $limit);
                //echo $SQL_EXACT_COUNT; exit;
                ## CHECKING 100% MATCH OR LIKELY MATCH  ##

                $exact_query = $this->db->query($SQL_EXACT_COUNT);
                $exact_flag_ = $exact_query->row_array();
                $result_arr[$key]['IS_EXACT_RESULT'] = $exact_flag_['flag_fld'];

                $get_friend_req_sent_status_me_him = $this->get_prayer_partner_status_me_him(
                        intval(decrypt($this->session->userdata('user_id'))), $item['user_id']);

                if (count($get_friend_req_sent_status_me_him) > 0) {
                    $result_arr[$key]['display_becomeprayer_partner'] = 'false';
                }


                $get_friend_status_me_him = $this->get_prayer_partner_accepted_me_him(
                        intval(decrypt($this->session->userdata('user_id'))), $item['user_id']);
                if (count($get_friend_status_me_him) > 0) {
                    $result_arr[$key]['display_alreadyprayer_partner'] = 'true';
                }

                $total_PP_arr = $this->get_prayerPartnerId_by_user_id($item['user_id']);
                $total_PP = count($total_PP_arr);
                $total_pending_PP_req = $this->total_pending_prayer_partner_recieved($item['user_id']);
                ## CHECKING TOTAL PRAYER PARTNERS ##
                if ($total_PP == 3 || $total_pending_PP_req >= 5) {
                    $result_arr[$key]['is_available'] = 'false';
                } else {
                    $result_arr[$key]['is_available'] = 'true';
                }
                ## CHECKING TOTAL PRAYER PARTNERS ##

                $if_friend = $this->users_model->if_already_friend(intval(decrypt($this->session->userdata('user_id'))), $item['user_id']);

                if (count($if_friend) > 0) {
                    $result_arr[$key]['if_already_friend'] = 'true';
                } else {
                    $result_arr[$key]['if_already_friend'] = 'false';
                }

                $if_netpal = $this->netpals_model->if_already_netpal(intval(decrypt($this->session->userdata('user_id'))), $item['user_id']);
                if (count($if_netpal) > 0) {
                    $result_arr[$key]['already_added_netpal'] = 'true';
                } else {
                    $result_arr[$key]['already_added_netpal'] = 'false';
                }
            }
        }
        //pr($result_arr);
        return $result_arr;
    }

    /* $type can be most_listened, most_sold, most_fans, latest_users */

    function get_prayer_partner_sug_total($s_where = null, $s_like_where = null) {


        $sql = sprintf(" SELECT count(*) as count FROM (
							(SELECT 
								  
								 u.id as user_id
								  
								 FROM  {$this->db->USERS} u  
 						          LEFT JOIN cg_country con ON con.id = u.i_country_id
								 LEFT JOIN cg_state s ON s.id = u.i_state_id
								 LEFT JOIN cg_city c ON u.i_city_id = c.id
								 LEFT JOIN {$this->db->DENOMINATION} AS d ON u.i_id_denomination = d.id 
								 WHERE 1  
								 %1\$s   
							     ORDER BY u.`dt_created_on` DESC )
                            
                            UNION
                            
                          (  SELECT
						 	
						 	 u.id as user_id
								  
							FROM  {$this->db->USERS} u  
 						    LEFT JOIN cg_country con ON con.id = u.i_country_id
							LEFT JOIN cg_state s ON s.id = u.i_state_id
							LEFT JOIN cg_city c ON u.i_city_id = c.id
							LEFT JOIN {$this->db->DENOMINATION} AS d ON u.i_id_denomination = d.id  WHERE 1  
							 %2\$s                                      
                            ORDER BY u.`dt_created_on` DESC )) as  derived_tbl  "
                , $s_where, $s_like_where, $timestamp);

        $query = $this->db->query($sql);
        $result_arr = $query->result_array();

        return $result_arr[0]['count'];
    }

    #DELETE PRAYER POINTS

    public function delete_prayer_points($id) {

        $SQL = sprintf("DELETE FROM %s WHERE id = {$id}  ", $this->db->PRAYER_PARTNER_POINTS);

        $this->db->query($SQL);
        $ret_ = $this->db->affected_rows();
        #echo $this->db->last_query(); 
        return $ret_;
    }

    # function to check prayer_partner

    function total_prayer_partner($i_requester_id = '') {


        $SQL = sprintf("SELECT COUNT(*) AS `check_count` FROM {$this->db->USER_PRAYER_PARTNER} c ,{$this->db->USERS} u  WHERE 
									 
									 c.s_status = 'accepted' 
									 AND u.i_status = 1
									AND
									((c.i_requester_id = '" . $i_requester_id . "' AND u.id=c.i_accepter_id  ) 
									OR (c.i_accepter_id = '" . $i_requester_id . "' AND u.id=c.i_requester_id ))");

        $ROW = $this->db->query($SQL)->row_array(); //echo $this->db->last_query(); 

        if ($ROW['check_count'])
            return $ROW['check_count'];
        else
            return 0;
    }

    public function get_pending_prayer_partner_recieved_notification($s_where = null, $i_start = null, $i_limit = null, $s_order_by = null) {

        try {
            $ret_ = array();

            $s_qry = "SELECT   c.id, 
						   c.i_requester_id, 
						   c.i_accepter_id,
						   c.i_deleted_by,
						   c.s_status,
						   c.dt_created_on, 
						   c.dt_accepted_on,
						   c.i_notification_shown
						  					   
						   
					FROM 
						{$this->db->USER_PRAYER_PARTNER} c 
						"
                    . $s_where;

            /* cn.s_country_name {$this->db->MST_COUNTRY} cn */

            //////////For Pagination///////////*don't change*/
            //$s_qry=str_replace("'","''",$s_qry);//for string operation in procedure
            $s_qry = $s_qry . (trim($s_order_by) != "" ? " ORDER BY " . $s_order_by . "" : "ORDER BY id asc") . " " . (is_numeric($i_start) && is_numeric($i_limit) ? " LIMIT " . intval($i_start) . "," . intval($i_limit) : "");

            #echo nl2br($s_qry);
            //////////end For Pagination//////////                

            $this->db->trans_begin(); ///new                
            $rs = $this->db->query($s_qry);
            $i_cnt = 0;
            if (is_array($rs->result())) {
                foreach ($rs->result() as $row) {
                    $ret_[$i_cnt]["id"] = $row->id; ////always integer
                    $ret_[$i_cnt]["i_requester_id"] = intval($row->i_requester_id);
                    $ret_[$i_cnt]["i_accepter_id"] = intval($row->i_accepter_id);
                    $ret_[$i_cnt]["s_status"] = $row->s_status;
                    $ret_[$i_cnt]["dt_created_on"] = ($row->dt_created_on);
                    $ret_[$i_cnt]["i_notification_shown"] = ($row->i_notification_shown);


                    $i_cnt++;
                }
                $rs->free_result();
            }
            $this->db->trans_commit(); ///new



            unset($s_qry, $rs, $row, $i_cnt, $s_where, $i_start, $i_limit);
            return $ret_;
        } catch (Exception $err_obj) {
            show_error($err_obj->getMessage());
        }
    }

    public function __destruct() {
        
    }

}

// end of class definition...
