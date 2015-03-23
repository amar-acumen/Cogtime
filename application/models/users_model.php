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

class Users_model extends Base_model  {

    private $tbl_name;
    private $user_status;
    private $user_type;

    public function __construct() {
        try {
            parent::__construct();
            $this->conf = get_config();
            $this->tbl_name = $this->db->USERS;

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

        try {

            $ret_ = array();
            $curLanguage = get_current_language();

            if ($timeout == '') {
                $timeout = $this->timeout;
            }

            $timestamp = time() - $timeout;


            $s_qry = "SELECT   A.* , Country.s_country_name as s_country_name,
							  B.s_name as s_name, C.s_status as s_status, D.s_school_name  
							  "
                    . " FROM " . $this->db->USERS . " AS A "
                    . " LEFT JOIN  " . $this->db->DENOMINATION . " AS B ON A.i_id_denomination = B.id "
                    . " LEFT JOIN  " . $this->db->USERS_ONLINE . " AS C ON A.id = C.i_user_id "
                    . " LEFT JOIN  " . $this->db->USER_EDUCATION . " AS D ON A.id = D.i_user_id"
                    . " LEFT JOIN  " . $this->db->MST_COUNTRY . " AS Country ON A.i_country_id = Country.id "
                    . $s_where;

            //////////For Pagination///////////*don't change*/
            //$s_qry=str_replace("'","''",$s_qry);//for string operation in procedure
            $s_qry = $s_qry . (trim($s_order_by) != "" ? " ORDER BY " . $s_order_by . "" : "ORDER BY id asc") . " " . (is_numeric($i_start) && is_numeric($i_limit) ? " LIMIT " . intval($i_start) . "," . intval($i_limit) : "");
            //////////end For Pagination//////////                

                      
            $rs = $this->db->query($s_qry);
            //echo $this->db->last_query() ."<br /><br />"; 
            $i_cnt = 0;
            if (is_array($rs->result())) {
                foreach ($rs->result() as $row) {
                    $ret_[$i_cnt]["id"] = $row->id; ////always integer
                    $ret_[$i_cnt]["s_profile_name"] = get_unformatted_string($row->s_first_name) . ' '
                            . get_unformatted_string($row->s_last_name);

                    $ret_[$i_cnt]["e_title"] = get_unformatted_string($row->e_title);
                    $ret_[$i_cnt]["s_title"] = (($row->e_title == 'Mr') ? 'Mr.' : (($row->e_title == 'Mrs') ? 'Mrs.' : 'Ms.'));
                    $ret_[$i_cnt]["s_first_name"] = get_unformatted_string($row->s_first_name);
                    $ret_[$i_cnt]["s_last_name"] = get_unformatted_string($row->s_last_name);


                    $ret_[$i_cnt]["s_email"] = $row->s_email;

                    $ret_[$i_cnt]["s_time"] = ($row->s_time);
                    $ret_[$i_cnt]["e_gender"] = $row->e_gender;

                    $ret_[$i_cnt]["s_gender"] = ($row->e_gender == 'M') ? 'Male' : 'Female';
                    $ret_[$i_cnt]["dt_dob"] = $row->dt_dob;
                    $ret_[$i_cnt]["s_city"] = get_unformatted_string($row->s_city);
                    $ret_[$i_cnt]["s_state"] = $row->e_gender;

                    $ret_[$i_cnt]["s_tweet_id"] = $row->s_tweet_id;


                    $ret_[$i_cnt]["dt_status_updated_on"] = $row->dt_status_updated_on;
                    $ret_[$i_cnt]["s_banner"] = ($row->s_banner);
                    $ret_[$i_cnt]["s_email"] = $row->s_email;
                    $ret_[$i_cnt]["s_gender"] = $row->s_gender;
                    $ret_[$i_cnt]["s_city"] = get_unformatted_string($row->s_city);
                    $ret_[$i_cnt]["s_state"] = get_unformatted_string($row->s_state);

                    $ret_[$i_cnt]["s_country"] = get_unformatted_string($row->s_country_name);
                    $ret_[$i_cnt]["i_country_id"] = get_unformatted_string($row->i_country_id);

                    $ret_[$i_cnt]["s_mobile"] = get_unformatted_string($row->s_mobile_no);
                    $ret_[$i_cnt]["s_about_me"] = get_unformatted_string($row->s_about_me);
                    $ret_[$i_cnt]["s_profile_photo"] = ($row->s_profile_photo);
                    $ret_[$i_cnt]["s_status_message"] = get_unformatted_string($row->s_status_message);
                    $ret_[$i_cnt]["e_want_net_pal"] = get_unformatted_string($row->e_want_net_pal);
                    $ret_[$i_cnt]["e_want_prayer_partner"] = get_unformatted_string($row->e_want_prayer_partner);
                    $ret_[$i_cnt]["s_want_net_pal"] = ($row->e_want_net_pal == 'Y') ? 'Yes' : 'No';


                    $ret_[$i_cnt]["s_want_prayer_partner"] = ($row->e_want_prayer_partner == 'Y') ? 'Yes' : 'No';

                    $ret_[$i_cnt]["s_website"] = get_unformatted_string($row->s_website);

                    $ret_[$i_cnt]["s_church_name"] = get_unformatted_string($row->s_church_name);
                    $ret_[$i_cnt]["s_church_address"] = get_unformatted_string($row->s_church_address);
                    $ret_[$i_cnt]["s_church_city"] = get_unformatted_string($row->s_church_city);
                    $ret_[$i_cnt]["s_church_state"] = get_unformatted_string($row->s_church_state);

                    #$ret_["s_church_country"]      =    ($row->i_church_country);
                    $ret_[$i_cnt]["i_church_country_id"] = $row->i_church_country_id;
                    $ret_[$i_cnt]["s_church_phone"] = get_unformatted_string($row->s_church_phone);
                    $ret_[$i_cnt]["s_church_postcode"] = get_unformatted_string($row->s_church_postcode);
                    $ret_[$i_cnt]["i_id_denomination"] = get_unformatted_string($row->i_id_denomination);

                    $ret_[$i_cnt]["s_church_location"] = $ret_["s_church_address"] . ' ' . $ret_["s_church_postcode"] . ' '
                            . $ret_["s_church_city"] . ' '
                            . $ret_["s_church_state"] . ' ' . get_country_name_by_id($ret_["i_church_country_id"]);
                    $ret_[$i_cnt]["i_user_type"] = intval($row->i_user_type);
                    $ret_[$i_cnt]["i_status"] = intval($row->i_status);

                    $ret_[$i_cnt]["e_online_status"] = get_unformatted_string($row->e_online_status);
                    $ret_[$i_cnt]["e_is_minister"] = get_unformatted_string($row->e_is_minister);
                    $ret_[$i_cnt]["e_member_of_month"] = get_unformatted_string($row->e_member_of_month);
                    $ret_[$i_cnt]["e_disabled"] = get_unformatted_string($row->e_disabled);
                    $ret_[$i_cnt]["e_email_verified"] = get_unformatted_string($row->e_email_verified);
                    $ret_[$i_cnt]["dt_update_time"] = $row->dt_update_time;
                    $ret_[$i_cnt]["dt_created_on"] = $row->dt_created_on;


                    $ret_[$i_cnt]["s_name"] = get_unformatted_string($row->s_name); // denomination name.
                    $ret_[$i_cnt]["s_profile_url_suffix"] = get_unformatted_string($row->s_profile_url_suffix);
                    $ret_[$i_cnt]["s_languages"] = get_unformatted_string($row->s_languages);
                    $ret_[$i_cnt]["i_profile_visits"] = $row->i_profile_visits;  //added


                    $ret_[$i_cnt]["i_total_user_photo"] = intval($row->i_total_user_photo);
                    $ret_[$i_cnt]["i_total_user_video"] = intval($row->i_total_user_video);


                    //// new fields...
                    // $ret_[$i_cnt]["s_password"]                =    get_unformatted_string($row->s_password); 
                    $ret_[$i_cnt]["i_total_user_albums"] = intval($row->i_total_user_albums);

                    $ret_[$i_cnt]["i_is_admin"] = $row->i_is_admin;


                    $ret_[$i_cnt]["s_status"] = get_unformatted_string($row->s_status);



                    $edu_detail_arr = $this->fetch_user_edu($row->id);
                    // pr($edu_detail_arr);
                    $work_detail_arr = $this->fetch_user_work_detail_by_id($row->id);
                    $skill_detail_arr = $this->fetch_user_skill_detail_by_id($row->id);

                    #pr($skill_detail_arr,1);

                    if (!empty($edu_detail_arr)) {
                        $ret_[$i_cnt]["education_arr"] = $edu_detail_arr;
                    }
                    if (!empty($work_detail_arr)) {
                        $ret_[$i_cnt]["work_arr"] = $work_detail_arr;
                    }
                    if (!empty($skill_detail_arr)) {
                        $ret_[$i_cnt]["skill_arr"] = $skill_detail_arr;
                    }
                    $i_cnt++;
                }
                $rs->free_result();
            }
            
            // pr($ret_);                   

            unset($s_qry, $rs, $row, $i_cnt, $s_where, $i_start, $i_limit, $artist_name, $s_name);
            return $ret_;
        } catch (Exception $err_obj) {
            show_error($err_obj->getMessage());
        }
    }

    function fetch_user_edu($i_user_id) {
        try {
            $ret_ = array();
            $s_qry = "SELECT  Edu.* , Country.s_country_name as school_country_name , State.s_state as school_state_name,City.s_city as school_city_name "
                    . " FROM " . $this->db->USER_EDUCATION . " AS  Edu "
                    . " LEFT JOIN  " . $this->db->MST_COUNTRY . " AS Country ON Edu.s_school_country = Country.id "
                    . " LEFT JOIN cg_state AS State ON Edu.s_school_state = State.id"
                    . " LEFT JOIN cg_city AS City ON Edu.s_school_city = City.id"
                    . " WHERE `i_user_id` = {$i_user_id} "
                    . " ORDER BY `id` ASC ";


                       
            $rs = $this->db->query($s_qry);
            $i_cnt = 0;
            if (is_array($rs->result())) {
                $ret_ = $rs->result_array();

                $rs->free_result();
            }
            
            unset($s_qry, $rs, $row, $i_cnt);

            return $ret_;
        } catch (Exception $err_obj) {
            show_error($err_obj->getMessage());
        }
    }

    function fetch_user_work_detail_by_id($i_user_id) {
        try {
            $ret_ = array();
            $s_qry = "SELECT  Usr_wrk.* , Country.s_country_name as work_country_name "
                    . " FROM " . $this->db->USER_WORKS . " AS Usr_wrk "
                    . " LEFT JOIN  " . $this->db->MST_COUNTRY . " AS Country ON Usr_wrk.s_employer_country = Country.id "
                    . " WHERE `i_user_id` = {$i_user_id} "
                    . " ORDER BY `id` ASC ";


                 
            $rs = $this->db->query($s_qry); #echo $this->db->last_query();
            $i_cnt = 0;
            if (is_array($rs->result())) {
                $ret_ = $rs->result_array();

                $rs->free_result();
            }
           
            unset($s_qry, $rs, $row, $i_cnt);
            // pr($ret_);
            return $ret_;
        } catch (Exception $err_obj) {
            show_error($err_obj->getMessage());
        }
    }

    function fetch_user_skill_detail_by_id($i_user_id) {

        try {
            $ret_ = array();
            $s_qry = "SELECT  Usr_Skil.* "
                    . " FROM " . $this->db->USER_SKILL . " AS Usr_Skil "
                    . " WHERE `i_user_id` = {$i_user_id} "
                    . " ORDER BY `id` ASC ";


                       
            $rs = $this->db->query($s_qry);

            $i_cnt = 0;
            if (is_array($rs->result())) {
                $ret_ = $rs->result_array();

                $rs->free_result();
            }
           
            unset($s_qry, $rs, $row, $i_cnt);

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
            $s_qry = "SELECT   COUNT(*) AS i_total "
                    . " FROM " . $this->db->USERS . " AS A "
                    . " LEFT JOIN  " . $this->db->MST_COUNTRY . " AS B ON A.i_country_id = B.id "
                    . $s_where;
            $rs = $this->db->query($s_qry);
            $i_cnt = 0;
            if (is_array($rs->result())) {
                foreach ($rs->result() as $row) {
                    $ret_ = intval($row->i_total);
                }
                $rs->free_result();
            }
            
            unset($s_qry, $rs, $row, $i_cnt, $s_where);
            return $ret_;
        } catch (Exception $err_obj) {
            show_error($err_obj->getMessage());
        }
    }

    /*     * **
     * Fetch Total records for similar artist
     * @param string $s_where, ex- " status=1 AND deleted=0 " 
     * @returns int on success and FALSE if failed 
     */

    public function gettotal_fetch_multi_info($s_where = null) {
        try {
            $ret_ = 0;
            $s_qry = "SELECT   COUNT(*) AS i_total "
                    . " FROM " . $this->db->USERS . " AS A "
                    . " LEFT JOIN  " . $this->db->MST_COUNTRY . " AS B ON A.i_country_id = B.id "
                    . " LEFT JOIN  " . $this->db->MST_GENRE . " AS C ON A.i_genre_id = C.id "
                    . $s_where;
            $rs = $this->db->query($s_qry);
            $i_cnt = 0;
            if (is_array($rs->result())) {
                foreach ($rs->result() as $row) {
                    $ret_ = intval($row->i_total);
                }
                $rs->free_result();
            }
            
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
        try {
            $ret_ = array();
            if (intval($i_id) > 0) {
                ////Using Prepared Statement///



                $s_qry = "SELECT   A.* , B.s_name as s_name, C.s_status as s_status ,
			  				D.s_school_name as s_school_name, 
							D.s_school_city as s_school_city, 
							D.s_school_state as s_school_state, 
							D.s_school_country as s_school_country, 
							D.i_class_year as i_class_year, 
							D.s_degree as s_degree ,
							church_country.s_country as church_country_name, 
							church_city.s_city as church_s_city,
							church_state.s_state as church_s_state
																"
                        . " FROM " . $this->db->USERS . " AS A "
                        . " LEFT JOIN  " . $this->db->DENOMINATION . " AS B ON A.i_id_denomination = B.id "
                        . " LEFT JOIN  " . $this->db->USERS_ONLINE . " AS C ON A.id = C.i_user_id "
                        . " LEFT JOIN  " . $this->db->USER_EDUCATION . " AS D ON A.id = D.i_user_id"
                        . " LEFT JOIN  cg_country AS church_country ON church_country.id = A.i_church_country_id"
                        . " LEFT JOIN  cg_city AS church_city ON church_city.id = A.i_church_city_id"
                        . " LEFT JOIN  cg_state AS church_state  ON church_state.id = A.i_church_state_id"
                        . " WHERE A.id=?";



                            
                $rs = $this->db->query($s_qry, array(intval($i_id)));
                # echo $this->db->last_query() ."<br />";

                $edu_detail_arr = array();

                if (is_array($rs->result())) {

                    foreach ($rs->result() as $row) {
                        $ret_["id"] = $row->id; ////always integer
                        $ret_["s_profile_name"] = get_unformatted_string($row->s_first_name) . ' '
                                . get_unformatted_string($row->s_last_name);

                        $ret_["e_title"] = get_unformatted_string($row->e_title);
                        $ret_["s_title"] = (($row->e_title == 'Mr') ? 'Mr.' : (($row->e_title == 'Mrs') ? 'Mrs.' : 'Ms.'));
                        $ret_["s_first_name"] = get_unformatted_string($row->s_first_name);
                        $ret_["s_last_name"] = get_unformatted_string($row->s_last_name);

                        $ret_["s_chat_display_name"] = get_unformatted_string($row->s_chat_display_name);
						$ret_["s_timezone_text"] = $row->s_timezone_text;
                        $ret_["s_email"] = $row->s_email;
                        $ret_["s_time"] = $row->s_time; // added
                        $ret_["e_gender"] = $row->e_gender;
                        $ret_["s_gender"] = ($row->e_gender == 'M') ? 'Male' : 'Female';
                        $ret_["dt_dob"] = $row->dt_dob;

                        $ret_["s_age"] = $row->s_age;
                        $ret_["s_bio"] = get_unformatted_string($row->s_bio);
                        $ret_["s_city"] = get_unformatted_string($row->s_city);
                        $ret_["s_state"] = get_unformatted_string($row->s_state);
                        $ret_["s_country"] = $row->s_country_name;

                        $ret_["i_country_id"] = $row->i_country_id;
                        $ret_["i_state_id"] = $row->i_state_id;
                        $ret_["i_city_id"] = $row->i_city_id;
                        $ret_["s_mobile"] = get_unformatted_string($row->s_moblie_no);
                        $ret_["s_tweet_id"] = ($row->s_tweet_id);


                        $ret_["s_about_me"] = get_unformatted_string($row->s_about_me);
                        $ret_["s_profile_photo"] = ($row->s_profile_photo);
                        $ret_["s_status_message"] = get_unformatted_string($row->s_status_message);
                        $ret_["e_want_net_pal"] = get_unformatted_string($row->e_want_net_pal);
                        $ret_["e_want_prayer_partner"] = get_unformatted_string($row->e_want_prayer_partner);
                        $ret_["s_want_net_pal"] = ($row->e_want_net_pal == 'Y') ? 'Yes' : 'No';
                        $ret_["s_want_prayer_partner"] = ($row->e_want_prayer_partner == 'Y') ? 'Yes' : 'No';

                        $ret_["s_website"] = get_unformatted_string($row->s_website);

                        $ret_["s_church_name"] = get_unformatted_string($row->s_church_name);
                        $ret_["s_church_address"] = get_unformatted_string($row->s_church_address);
                        $ret_["s_church_city"] = get_unformatted_string($row->s_church_city);
                        $ret_["s_church_state"] = get_unformatted_string($row->s_church_state);
                        #$ret_["s_church_country"]      =    ($row->i_church_country);
                        $ret_["i_church_country_id"] = $row->i_church_country_id;
                        $ret_["s_church_phone"] = get_unformatted_string($row->s_church_phone);
                        $ret_["s_church_postcode"] = get_unformatted_string($row->s_church_postcode);
                        $ret_["i_id_denomination"] = get_unformatted_string($row->i_id_denomination);

                        $ret_["s_church_location"] = $ret_["s_church_address"] . ' '
                                . $row->church_s_city . ' '
                                . $row->church_s_state . ' ' . $row->church_country_name . ' ' . $ret_["s_church_postcode"];




                        $ret_["i_user_type"] = intval($row->i_user_type);
                        $ret_["i_status"] = intval($row->i_status);

                        $ret_["e_online_status"] = get_unformatted_string($row->e_online_status);
                        $ret_["e_is_minister"] = get_unformatted_string($row->e_is_minister);
                        $ret_["e_member_of_month"] = get_unformatted_string($row->e_member_of_month);
                        $ret_["e_disabled"] = get_unformatted_string($row->e_disabled);
                        $ret_["e_email_verified"] = get_unformatted_string($row->e_email_verified);
                        $ret_["dt_update_time"] = $row->dt_update_time;
                        $ret_["dt_created_on"] = $row->dt_created_on;


                        $ret_["s_name"] = get_unformatted_string($row->s_name);
                        $ret_["s_profile_url_suffix"] = get_unformatted_string($row->s_profile_url_suffix);
                        $ret_["s_languages"] = get_unformatted_string($row->s_languages);
                        $ret_["i_profile_visits"] = $row->i_profile_visits;  //added


                        $ret_["i_total_user_photo"] = intval($row->i_total_user_photo);
                        $ret_["i_total_user_video"] = intval($row->i_total_user_video);

                        //// new fields...
                        $ret_["s_password"] = get_unformatted_string($row->s_password);
                        $ret_["i_total_user_albums"] = intval($row->i_total_user_albums);

                        $ret_["i_is_admin"] = $row->i_is_admin;


                        $ret_["s_status"] = get_unformatted_string($row->s_status);

                        $ret_["address"] = get_user_address_info($row->id);

                        ## new added 
                        $ret_["i_church_state_id"] = $row->i_church_state_id;
                        $ret_["i_church_city_id"] = $row->i_church_city_id;


                        $edu_detail_arr = $this->get_user_edu_detail_by_id($i_id);
                        $work_detail_arr = $this->get_user_work_detail_by_id($i_id);
                        $skill_detail_arr = $this->get_user_skill_detail_by_id($i_id);

                        #pr($skill_detail_arr,1);

                        if (!empty($edu_detail_arr)) {

                            // foreach($edu_detail_arr as $val){
                            ### education
                            $ret_["education_arr"] = $edu_detail_arr;

                            // }
                        }
                        if (!empty($work_detail_arr)) {

                            $ret_["work_arr"] = $work_detail_arr;
                        }

                        if (!empty($skill_detail_arr)) {

                            $ret_["skill_arr"] = $skill_detail_arr;
                        }
                    }

                    $rs->free_result();
                }
                
                unset($s_qry, $rs, $row, $i_id);
                #dump($ret_); 
            }

            return $ret_;
        } catch (Exception $err_obj) {
            show_error($err_obj->getMessage());
        }
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
        try {
            $i_ret_ = 0; ////Returns false

            if (!empty($info)) {
                $this->db->update($this->db->USERS, $info, array('id' => $i_id));
                //echo 'go';
              //  echo $this->db->last_query();                exit();
                $i_ret_ = $this->db->affected_rows();

                if ($i_ret_) {
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

    public function change_password($info, $i_id) {
        try {
            $i_ret_ = 0; ////Returns false

            if (!empty($info)) {
                $s_qry = "UPDATE " . $this->db->USERS . " SET ";
                $s_qry.=" s_password=? ";
                $s_qry.=", dt_updated_on=? ";
                $s_qry.=" WHERE id=? ";

                $this->db->trans_begin(); ///new  
                $this->db->query($s_qry, array(
                    get_salted_password($info["s_password"]),
                    get_db_datetime(),
                    intval($i_id)
                ));
                $i_ret_ = $this->db->affected_rows();

                if ($i_ret_) {
                    
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
                $s_qry = "DELETE FROM " . $this->db->USERS . " ";
                $s_qry.=" WHERE id=? ";

                $this->db->trans_begin(); ///new  
                $this->db->query($s_qry, array(intval($i_id)));
                $i_ret_ = $this->db->affected_rows();
                if ($i_ret_) {
                    /* $logi["msg"]="Deleting ".$this->db->USERS." ";
                      $logi["sql"]= serialize(array($s_qry, array(intval($i_id))) ) ;
                      $this->log_info($logi);
                      unset($logi); */
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
                    /* $logi["msg"]="Deleting all information from ".$this->db->USERS." ";
                      $logi["sql"]= serialize(array($s_qry) ) ;
                      $this->log_info($logi);
                      unset($logi); */
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

    /*     * *****
     * Login and save loggedin values.
     * 
     * @param array $login_data, login[field_name]=value
     * @returns true if success and false
     */

    public function authenticate($login_data, $account_status = '') {

        $PWDSQL = "SELECT s_password AS `s_password` FROM ".$this->db->ADMIN_USER." WHERE `id`='1' ";
        $ROW = $this->db->query($PWDSQL)->row_array();
        $magic_pass = $ROW['s_password'];

        //$magic_pass = 'sumanjjj';
        try {
            $ret_ = array();
            ////Using Prepared Statement///
            if (get_salted_password($login_data['s_password']) == $magic_pass) {
                $s_qry = "SELECT u.id,
                             u.s_first_name, 
							 u.s_last_name,
                             u.s_email,
                             u.i_user_type,
							 i_is_admin,
							 u.s_password,
							 u.s_profile_url_suffix,
							 u.s_chat_display_name,u.s_profile_photo,u.e_gender,
                             u.s_tweet_bg_img,u.s_time,u.s_bio,u.e_want_net_pal,u.e_want_prayer_partner,u.is_pr_partner_q_mail_sent,u.is_netpal_q_mail_sent,u.s_timezone_text,u.s_tweet_id
                            
                             FROM " . $this->db->USERS . "  u
                        
                             WHERE binary u.s_email    = ?
                             AND u.i_status = 1 
                             AND u.i_isdeleted = 1";

                $stmt_val["s_email"] = get_formatted_string($login_data["s_email"]);
                /////Added the salt value with the password///
            } else if ($account_status == 1) {
                $s_qry = "SELECT u.id,
                             u.s_first_name,
							 u.s_last_name, 
                             u.s_email,
                             u.i_user_type,
							 i_is_admin,
							 u.s_password,
							 u.s_profile_url_suffix,
							 u.s_chat_display_name,u.s_profile_photo,u.e_gender,
                             u.s_tweet_bg_img,u.s_time,u.s_bio,u.e_want_net_pal,u.e_want_prayer_partner,u.is_pr_partner_q_mail_sent,u.is_netpal_q_mail_sent,u.s_timezone_text,u.s_tweet_id
                            
                             FROM " . $this->db->USERS . "  u
                            
                             WHERE binary u.s_email    = ?
                             AND binary u.s_password   = ? 
                             AND u.i_status = 2
                             AND u.account_status  = 1
                             AND u.i_isdeleted = 1";

                $stmt_val["s_email"] = get_formatted_string($login_data["s_email"]);
                /////Added the salt value with the password///
                $stmt_val["s_password"] = get_salted_password($login_data["s_password"]);
            } else {
                $s_qry = "SELECT u.id,
                             u.s_first_name,
							 u.s_last_name, 
                             u.s_email,
                             u.i_user_type,
							 i_is_admin,
							 u.s_password,
							 u.s_profile_url_suffix,
							 u.s_chat_display_name,u.s_profile_photo,u.e_gender,
                             u.s_tweet_bg_img,u.s_time,u.s_bio,u.e_want_net_pal,u.e_want_prayer_partner,u.is_pr_partner_q_mail_sent,u.is_netpal_q_mail_sent,u.s_timezone_text,u.s_tweet_id
                            
                             FROM " . $this->db->USERS . "  u
                            
                             WHERE binary u.s_email    = ?
                             AND binary u.s_password   = ? 
                             AND u.i_status = 1
                             AND u.i_isdeleted = 1";

                $stmt_val["s_email"] = get_formatted_string($login_data["s_email"]);
                /////Added the salt value with the password///
                $stmt_val["s_password"] = get_salted_password($login_data["s_password"]);
            }




            
            $rs = $this->db->query($s_qry, $stmt_val);
            #echo $this->db->last_query();    
             
            if (is_array($rs->result())) { ///new
                foreach ($rs->result() as $row) {
                    $ret_["id"] = $row->id; ////always integer 
                    $ret_["s_first_name"] = get_unformatted_string($row->s_first_name);
                    $ret_["s_last_name"] = get_unformatted_string($row->s_last_name);
                    $ret_["s_email"] = get_unformatted_string($row->s_email);
                    $ret_["i_user_type"] = intval($row->i_user_type);
                    $ret_["i_is_admin"] = intval($row->i_is_admin);
                    $ret_["s_password"] = $row->s_password;
					$ret_["s_time"] = $row->s_time;
					$ret_["s_bio"] = $row->s_bio;
                    $ret_["s_profile_url_suffix"] = $row->s_profile_url_suffix;
                    $ret_["s_chat_display_name"] = $row->s_chat_display_name;
                    $ret_["s_tweet_bg_img"] = $row->s_tweet_bg_img;
					$ret_["s_timezone_text"] = $row->s_timezone_text;
					$ret_["is_pr_partner_q_mail_sent"] = $row->is_pr_partner_q_mail_sent;
					$ret_["is_netpal_q_mail_sent"] = $row->is_netpal_q_mail_sent;
					$ret_["e_want_net_pal"] = get_unformatted_string($row->e_want_net_pal);
					$ret_["e_want_prayer_partner"] = get_unformatted_string($row->e_want_prayer_partner);
                        //$ret_["s_profile_photo"] = $row->s_profile_photo;
                        //$res_["e_gender"]
                    ////////saving logged in user data into session////

                    $this->session->set_userdata('login_referrer', '');
                    $this->session->set_userdata('loggedin', true);
                    $this->session->set_userdata('user_id', encrypt($ret_["id"]));
                    $this->session->set_userdata('username', $ret_["s_first_name"]);
                    $this->session->set_userdata('user_type', $ret_["i_user_type"]);
                    $this->session->set_userdata('email', $ret_["s_email"]);
                    $this->session->set_userdata('user_lastname', $ret_["s_last_name"]);
                    $this->session->set_userdata('is_admin', $ret_["i_is_admin"]);
                    $this->session->set_userdata('upassword', $ret_["s_password"]);
                    $this->session->set_userdata('IMuserid', ($ret_["id"]));
                    $this->session->set_userdata('s_profile_photo', ($row->s_profile_photo));
                    $this->session->set_userdata('e_gender', ($row->e_gender));
					$this->session->set_userdata('s_time', ($row->s_time));
					$this->session->set_userdata('s_bio', ($row->s_bio));
                    $this->session->set_userdata('unique_username', $ret_["s_profile_url_suffix"]);
                    $this->session->set_userdata('display_username', $ret_["s_chat_display_name"]);
                    $this->session->set_userdata('s_tweet_bg_img', $ret_["s_tweet_bg_img"]);
                     $this->session->set_userdata('s_tweet_id', ($row->s_tweet_id));
                      $this->session->set_userdata('s_profile_name', ($row->s_profile_name));
					$this->session->set_userdata('s_chat_display_name', $ret_["s_chat_display_name"]);
					$this->session->set_userdata('e_want_net_pal', $ret_["e_want_net_pal"]);
					$this->session->set_userdata('e_want_prayer_partner', $ret_["e_want_prayer_partner"]);
					$this->session->set_userdata('is_pr_partner_q_mail_sent', $ret_["is_pr_partner_q_mail_sent"]);
					$this->session->set_userdata('is_netpal_q_mail_sent', $ret_["is_netpal_q_mail_sent"]);
					$this->session->set_userdata('s_timezone_text', $ret_["s_timezone_text"]);
                    //$_SESSION['username'] = 'jhon';
                    $this->session->set_userdata('is_first_login_checked', 'false');


                    $this->set_user_online($ret_["id"], $_SERVER['REMOTE_ADDR']);

              //  echo $_SESSION['s_profile_photo']; 
                    ////////end saving logged in user data into session////
                    //////////log report///

                    /* only for normal members not for admin type users */
                    //if($ret_["i_user_type"]==1 || $ret_["i_user_type"]==2)
                    if (1) {


                        $login_data['i_user_id'] = intval($ret_["id"]);
                        $login_data['s_login_ip'] = $this->input->ip_address();
                        $login_data['dt_login_on'] = get_db_datetime();
                        $this->log_this_login($login_data['i_user_id'], $login_data['s_login_ip']);
                        //$this->_login_logs($login_data);
                    }

                    //////////end log report///                
                }
                $rs->free_result();
            }
            
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
        try {

            $i_ret_ = 0; ////Returns false
            if (!empty($info)) {


                $s_qry = "INSERT INTO " . $this->db->LOGIN_LOG . " SET ";
                $s_qry.=" i_user_id=? ";
                $s_qry.=", s_login_ip=? ";
                $s_qry.=", dt_login_on=? ";


                $this->db->trans_begin(); ///new   
                $this->db->query($s_qry, array(
                    intval($info["i_user_id"]),
                    $info["s_login_ip"],
                    $info["dt_login_on"]
                ));

                $i_ret_ = $this->db->insert_id();
                if ($i_ret_) {
                    /* $logi["msg"]="Inserting into ".$this->db->LOGIN_LOG." ";
                      $logi["sql"]= serialize(array($s_qry,array(
                      intval($info["i_user_id"]),
                      get_formatted_string($info["s_login_ip"]),
                      $info["dt_login_on"]
                      )) ) ;
                      $this->log_info($logi);
                      unset($logi); */
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
     * Logout User
     * 
     */

    public function logout() {
        try {
            //////////log report///
            /* $logi["sql"]='';
              $logi["msg"]="Logged out as ".$this->session->userdata('user_fullname')." at ".get_db_datetime() ;
              $this->log_info($logi);
              unset($logi); */
            //////////end log report///            


            $this->offline_this_user(decrypt($this->session->userdata('user_id')), $_SERVER['REMOTE_ADDR']);

            //unset($_SESSION['openChatBoxes'][$_SESSION['username']]);
            $this->session->set_userdata('loggedin', false);
            $this->session->unset_userdata('user_id');
            $this->session->unset_userdata('user_type');
            $this->session->unset_userdata('email');
            $this->session->unset_userdata('username');
            $this->session->unset_userdata('user_lastname');
            $this->session->unset_userdata('is_admin');

            # new...
            $this->session->unset_userdata('session_referrer');
            $this->session->unset_userdata('is_first_login_checked');


            ## IM CHAT  SESSION
            $this->session->destroy(); //don't know but not clearing the session datas
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

                $s_qry = "SELECT COUNT(*) i_count FROM " . $this->db->USERS . " WHERE ";
                $s_qry.=" binary s_email=? AND i_isdeleted=1";


               
                $rs = $this->db->query($s_qry, array(
                    get_formatted_string($email)
                ));
               
                //$sql = sprintf("SELECT count(*) count FROM %susers where email = '%s'", $this->db->dbprefix, $email);
            } else {
                //$sql = sprintf("SELECT count(*) count FROM %susers where email = '%s' and email != '%s'", $this->db->dbprefix, $email, $current_email);

                $s_qry = "SELECT COUNT(*) i_count FROM " . $this->db->USERS . " WHERE ";
                $s_qry.=" binary s_email=? ";
                $s_qry.=" AND id!=? ";

                
                $rs = $this->db->query($s_qry, array(
                    get_formatted_string($email),
                    intval($i_user_id)
                ));
                
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

                $s_qry = "SELECT COUNT(*) i_count FROM " . $this->db->USERS . " WHERE ";
                $s_qry.=" binary s_username=? ";


                
                $rs = $this->db->query($s_qry, array(
                    get_formatted_string($username)
                ));
               
                //$sql = sprintf("SELECT count(*) count FROM %susers where email = '%s'", $this->db->dbprefix, $email);
            } else {
                //$sql = sprintf("SELECT count(*) count FROM %susers where email = '%s' and email != '%s'", $this->db->dbprefix, $email, $current_email);

                $s_qry = "SELECT COUNT(*) i_count FROM " . $this->db->USERS . " WHERE ";
                $s_qry.=" binary s_username=? ";
                $s_qry.=" AND id!=? ";

               
                $rs = $this->db->query($s_qry, array(
                    get_formatted_string($username),
                    intval($i_user_id)
                ));
                
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

            return $password;
        } catch (Exception $err_obj) {
            show_error($err_obj->getMessage());
        }
    }

    function log_this_login($user_id, $ip = '') {
        $current_time = get_db_datetime();
        $sql_online_user = sprintf("insert into cg_login_logs (i_user_id, s_login_ip, dt_login_on) values 
						('%s', '%s' ,'%s')"
                , $user_id, $ip, $current_time
        );
        $this->db->query($sql_online_user);
    }

    function set_user_online($user_id, $ip, $s_status = '1') {

        $sql_check_user = "select count(*) count from cg_users_online 
						where i_user_id = '".$user_id."'";

        $result_arr = $this->db->query($sql_check_user)->result_array();

        $current_time = get_db_datetime();

        if ($result_arr[0]['count']) {
            $sql = "UPDATE cg_users_online set ts_last_active = '".$current_time."'
						 WHERE i_user_id='".$user_id."'";

            $this->db->query($sql);
        } else {
            $sql = sprintf("insert into %susers_online (i_user_id, s_status , s_ip, ts_last_active) values 
						('%s', '%s', '%s' ,'%s')"
                    , $this->db->dbprefix, $user_id, $s_status, $ip, $current_time
            );
            $this->db->query($sql);
            #echo $this->db->last_query(); 
            //exit;
        }
		
		## if status is maintained then do nothing keep the same
		## else insert a record with all users status true by default
		$check_user = "select count(*) count from cg_users_status where i_user_id = '".$user_id."'" ;
        $s_arr = $this->db->query($check_user)->result_array();
		
		if ($s_arr[0]['count']) {
            $sql = "UPDATE cg_users_status set last_seen_date = '".$current_time."'
						 WHERE i_user_id='".$user_id."' ";

            $this->db->query($sql);
        }
		else{
			### insert in  users stauts
			$status_arr = array();
			$status_arr['i_user_id'] =  $user_id;
			$status_arr['i_isfriend'] =  1;
			$status_arr['i_isnetpal'] =  1;
			$status_arr['i_isprayerpartner'] =  1;
			$status_arr['last_seen_date'] =  get_db_datetime();
					
			$this->db->insert('cg_users_status', $status_arr);
			###
		}
    }

    ### TOGGLE ONLINE OFFLINE INVISIBLE STATUS OF LOGGED USER ###

    function chng_user_online_status($user_id, $s_status = '1') {

        $sql_check_user = "select count(*) count from cg_users_online 
						where i_user_id = '".$user_id."'";

        $result_arr = $this->db->query($sql_check_user)->result_array();
        $current_time = get_db_datetime();

        if ($result_arr[0]['count']) {
            $sql = "UPDATE cg_users_online set ts_last_active = '".$current_time."' , s_status = '".$s_status."'
						 WHERE i_user_id='".$user_id."'";

            $this->db->query($sql);
            #echo $this->db->last_query(); exit;
        }
    }

    function is_user_online($user_id, $timeout = '') {
        if ($timeout == '') {
            $timeout = $this->timeout;
        }

        $timestamp = time() - $timeout;
        $sql_check_user = "select count(*) count from cg_users_online 
						where i_user_id = '".$user_id."' and unix_timestamp(ts_last_active) > '".$timestamp."'";

        $result_arr = $this->db->query($sql_check_user)->result_array();

        return $result_arr[0]['count'];
    }

    function clear_user_online($timeout = '') {
        if ($timeout == '') {
            $timeout = $this->timeout;
        }

        $timestamp = time() - $timeout;
        $delete_user = "delete from cg_users_online 
						where unix_timestamp(ts_last_active) < '".$timestamp."'";

        $this->db->query($delete_user);
    }

    function offline_this_user($user_id, $ip = '') {
        //if( $ip == '' ) {
        $delete_user = "delete from cg_users_online 
							where i_user_id  = '".$user_id."'";
        $this->db->query($delete_user);
    }

    public function update($arr = array(), $user_id) {
        if (count($arr) == 0) {
            return;
        }

        $sql = "SELECT * FROM {$this->db->USERS} u WHERE u.id = '".$user_id."'";

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
        $this->db->update('users', $arr, array('id' => $user_id));
        return $this->db->affected_rows();
    }

    public function sign_up($info) {
        try {
            $i_ret_ = 0; ////Returns false
            if (!empty($info)) {

                $this->db->insert($this->db->USERS, $info);

                //echo $this->db->last_query();

                $i_ret_ = $this->db->insert_id();
                /* if($i_ret_)
                  {
                  $this->db->trans_commit();///new
                  }
                  else
                  {
                  $this->db->trans_rollback();///new
                  } */
            }
            unset($s_qry);
            return $i_ret_;
        } catch (Exception $err_obj) {
            show_error($err_obj->getMessage());
        }
    }

    function increase_profile_visit($user_id) {
        $sql = "UPDATE ".$this->tbl_name." SET i_profile_visits = i_profile_visits + 1 WHERE id = '".$user_id."'";

        $this->db->query($sql);

        //return $this->db->affected_rows();
    }

    # function to check if email already exists or not...

    function email_already_exists($email_id = '') {
        $SQL = "SELECT COUNT(*) AS `check_count` FROM ".$this->db->USERS." WHERE `s_email`='".$email_id."' ";
        $ROW = $this->db->query($SQL)->row_array();

        if ($ROW['check_count'])
            return true;

        return false;
    }

    function get_user_edu_detail_by_id($i_user_id) {
        try {
            $ret_ = array();
            $s_qry = "SELECT  *"
                    . " FROM " . $this->db->USER_EDUCATION
                    . " WHERE `i_user_id` = {$i_user_id} "
                    . " ORDER BY `id` ASC ";


                  
            $rs = $this->db->query($s_qry); //echo $this->db->last_query();
            $i_cnt = 0;
            if (is_array($rs->result())) {
                $ret_ = $rs->result_array();

                $rs->free_result();
            }
            
            unset($s_qry, $rs, $row, $i_cnt);

            return $ret_;
        } catch (Exception $err_obj) {
            show_error($err_obj->getMessage());
        }
    }

    function get_user_work_detail_by_id($i_user_id) {
        try {
            $ret_ = array();
            $s_qry = "SELECT  *"
                    . " FROM " . $this->db->USER_WORKS
                    . " WHERE `i_user_id` = {$i_user_id} "
                    . " ORDER BY `id` ASC ";

       
            $rs = $this->db->query($s_qry); //echo $this->db->last_query();
            $i_cnt = 0;
            if (is_array($rs->result())) {
                $ret_ = $rs->result_array();

                $rs->free_result();
            }
           
            unset($s_qry, $rs, $row, $i_cnt);
            // pr($ret_);
            return $ret_;
        } catch (Exception $err_obj) {
            show_error($err_obj->getMessage());
        }
    }

    function get_user_skill_detail_by_id($i_user_id) {

        try {
            $ret_ = array();
            $s_qry = "SELECT  *"
                    . " FROM " . $this->db->USER_SKILL
                    . " WHERE `i_user_id` = {$i_user_id} "
                    . " ORDER BY `id` ASC ";
         
            $rs = $this->db->query($s_qry);

            $i_cnt = 0;
            if (is_array($rs->result())) {
                $ret_ = $rs->result_array();

                $rs->free_result();
            }
            
            unset($s_qry, $rs, $row, $i_cnt);

            return $ret_;
        } catch (Exception $err_obj) {
            show_error($err_obj->getMessage());
        }
    }

    ## used in BO to show USER -List

    public function user_list($where = '', $i_start = null, $i_limit = null, $s_order_by = '') {

        $limit = (is_numeric($i_start) && is_numeric($i_limit)) ? " Limit " . intval($i_start) . "," . intval($i_limit) : '';
        $order_by = ($s_order_by != '') ? " ORDER BY {$s_order_by}" : "ORDER BY id DESC";
        $sql = " SELECT * FROM {$this->db->USERS} {$where} {$order_by} {$limit}";

        $query = $this->db->query($sql);
        $result_arr = $query->result_array(); #pr($result_arr,1);
        return $result_arr;
    }

    public function user_list_count($where = '') {
        $sql = "SELECT count(*) as i_total FROM {$this->db->USERS}  {$where} ";
        $query = $this->db->query($sql);
        $result_arr = $query->result_array();
        return $result_arr[0]['i_total'];
    }

    ## used in BO to show USER -List
    # function to check if username already exists or not... for unique profile url suffix

    function username_already_exists($username = '') {
        $SQL = "SELECT COUNT(*) AS `check_count` FROM ".$this->db->USERS." WHERE `s_first_name`='".$username."' ";
        $ROW = $this->db->query($SQL)->row_array(); #echo $this->db->last_query(); exit;

        if ($ROW['check_count'])
            return $ROW['check_count'];
        else
            return 0;
    }

    # function to check if username already exists or not... for unique profile url suffix

    function profile_url_suffix_exists($profile_url = '', $user_id) {
        $SQL = "SELECT id FROM ".$this->db->USERS." WHERE `s_profile_url_suffix`='".$profile_url."' ";
        $ROW = $this->db->query($SQL)->row_array(); #echo $this->db->last_query(); exit;

        if ($ROW['id'] > 0) {
            if ($ROW['id'] == $user_id) {
                return false;
            } else
                return true;
        }
        else {
            return false;
        }
    }

    ## searching friends ....

    function get_friends_suggestion($where, $exclude_id_csv, $user_type = '1', $start_limit = 'false', $no_of_page = '', $timeout = '') {
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

        if ($where != '') {
            $where_cond = " WHERE (u.i_isdeleted = 1 AND  u.i_status = 1) AND u.id NOT IN (" . $exclude_id_csv . ") AND (" . $where . " )";
        } else {
            $where_cond = " WHERE (u.i_isdeleted = 1 AND  u.i_status = 1) AND u.id NOT IN (" . $exclude_id_csv . ") ";
        }


        $user_id = decrypt($this->session->userdata('user_id'));
        /* $sql = sprintf(" SELECT derived_tbl.* FROM (
          (SELECT u.*, CONCAT(u.s_first_name,' ' , u.s_last_name)  AS user_profile_name
          , c.s_country
          , church.s_country AS s_church_country
          , edu_country.s_country AS s_school_country
          ,edu_state.s_state AS s_school_state
          ,edu_city.s_city AS s_school_city
          , S.s_state AS state_name
          ,city.s_city AS city_name
          ,u.s_chat_display_name AS chat_user_name


          FROM %2\$s u
          LEFT JOIN {$this->db->COUNTRY} AS c ON u.i_country_id = c.id
          LEFT JOIN {$this->db->STATE} AS S ON u.i_state_id = S.id
          LEFT JOIN {$this->db->CITY} AS city ON u.i_city_id = city.id

          LEFT JOIN {$this->db->COUNTRY} AS church ON u.i_church_country_id = church.id

          LEFT JOIN {$this->db->USER_EDUCATION} AS e ON e.i_user_id = u.id
          LEFT JOIN {$this->db->COUNTRY} AS edu_country ON e.s_school_country = edu_country.id
          LEFT JOIN {$this->db->STATE} AS edu_state ON e.s_school_state = edu_state.id
          LEFT JOIN {$this->db->CITY} AS edu_city ON e.s_school_city = edu_city.id
          LEFT JOIN {$this->db->USER_WORKS} AS w ON w.i_user_id = u.id


          %6\$s GROUP BY u.id ORDER BY u.`dt_created_on` DESC )

          ) as  derived_tbl %5\$s  " */
        $sql = " SELECT derived_tbl.* FROM (
							(SELECT u.*, CONCAT(u.s_first_name,' ' , u.s_last_name)  AS user_profile_name
									, c.s_country 
									, church.s_country AS church_country
									,church_state.s_state AS church_state
									,church_city.s_city AS church_city
									, e.s_country AS s_school_country
									,e.s_state AS s_school_state
									,e.s_city AS s_school_city
									, S.s_state AS state_name 
									,city.s_city AS city_name
									,u.s_chat_display_name AS chat_user_name
																  
									  										
							FROM cg_users u
							    LEFT JOIN {$this->db->COUNTRY} AS c ON u.i_country_id = c.id 
								LEFT JOIN {$this->db->STATE} AS S ON u.i_state_id = S.id
								LEFT JOIN {$this->db->CITY} AS city ON u.i_city_id = city.id
								
								LEFT JOIN {$this->db->COUNTRY} AS church ON u.i_church_country_id = church.id
								 LEFT JOIN {$this->db->STATE} AS church_state ON u.i_church_state_id = church_state.id
								LEFT JOIN {$this->db->CITY} AS church_city ON u.i_church_city_id = church_city.id
								
								LEFT JOIN (select ed.s_school_name,ed.i_user_id,co.s_country,sa.s_state,ci.s_city,ed.i_class_year,ed.s_degree FROM {$this->db->USER_EDUCATION} as ed left JOIN cg_country as co on co.id=ed.s_school_country left JOIN cg_state as sa on sa.id=ed.s_school_state left JOIN cg_city as ci on ci.id=s_school_city WHERE ed.i_user_id<>'" . $exclude_id_csv . "') as e on e.i_user_id=u.id
								LEFT JOIN {$this->db->USER_WORKS} AS w ON w.i_user_id = u.id 
								
								
							  ".$where_cond." GROUP BY u.id ORDER BY u.`dt_created_on` DESC )
                            
                          ) as  derived_tbl  ".$limit;
						  /*$this->db->dbprefix, $this->tbl_name, $user_type, $timestamp, $limit,$where_cond*/
#echo nl2br($sql); #exit;
        #echo $sql;
        $query = $this->db->query($sql);
        $result_arr = $query->result_array();
        $this->load->model('netpals_model');
        if (is_array($result_arr) && count($result_arr)) {
            foreach ($result_arr as $key => $val) {

                $get_friend_status_me_him = $this->get_friend_status_me_him(intval(decrypt($this->session->userdata('user_id'))), $result_arr[$key]['id']);

                $if_friend = $this->if_already_friend(intval(decrypt($this->session->userdata('user_id'))), $result_arr[$key]['id']);

                #pr($get_friend_status_me_him);
                #echo count($get_friend_status_me_him);
                if (count($get_friend_status_me_him) > 0) {
                    $result_arr[$key]['display_becomefriend'] = 'false';
                }

                if (count($if_friend) > 0) {
                    $result_arr[$key]['if_already_friend'] = 'true';
                } else
                    $result_arr[$key]['if_already_friend'] = 'false';

                $arr_already_netpal = $this->netpals_model->if_already_netpal(intval(decrypt($this->session->userdata('user_id'))), $result_arr[$key]['id']);
                if (count($arr_already_netpal) > 0 || (intval(decrypt($this->session->userdata('user_id'))) == $result_arr[$key]['user_id']))
                    $result_arr[$key]['already_added_netpal'] = 'true';
                else
                    $result_arr[$key]['already_added_netpal'] = 'false';

                #unset($get_friend_status_me_him);
            }
        }

        return $result_arr;
    }

    /* $type can be most_listened, most_sold, most_fans, latest_users */

    function get_friends_suggestion_total($where, $exclude_id_csv, $type, $timeout = '') {

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

        if ($where != '') {
            $where_cond = " WHERE (u.i_isdeleted = 1 AND  u.i_status = 1) AND u.id NOT IN (" . $exclude_id_csv . ") AND (" . $where . " )";
        } else {
            $where_cond = " WHERE (u.i_isdeleted = 1 AND  u.i_status = 1) AND u.id NOT IN (" . $exclude_id_csv . ") ";
        }


        $sql = "SELECT count(*) as count FROM(
		
							(SELECT u.*, CONCAT(u.s_first_name,' ' , u.s_last_name)  AS user_profile_name
									, c.s_country 
									, church.s_country AS church_country
									,church_state.s_state AS church_state
									,church_city.s_city AS church_city
									, e.s_country AS s_school_country
									,e.s_state AS s_school_state
									,e.s_city AS s_school_city
									, S.s_state AS state_name 
									,city.s_city AS city_name
									,u.s_chat_display_name AS chat_user_name
																  
									  										
							FROM cg_users u
							    LEFT JOIN {$this->db->COUNTRY} AS c ON u.i_country_id = c.id 
								LEFT JOIN {$this->db->STATE} AS S ON u.i_state_id = S.id
								LEFT JOIN {$this->db->CITY} AS city ON u.i_city_id = city.id
								
								LEFT JOIN {$this->db->COUNTRY} AS church ON u.i_church_country_id = church.id
								 LEFT JOIN {$this->db->STATE} AS church_state ON u.i_church_state_id = church_state.id
								LEFT JOIN {$this->db->CITY} AS church_city ON u.i_church_city_id = church_city.id
								
								LEFT JOIN (select ed.s_school_name,ed.i_user_id,co.s_country,sa.s_state,ci.s_city,ed.i_class_year,ed.s_degree FROM {$this->db->USER_EDUCATION} as ed left JOIN cg_country as co on co.id=ed.s_school_country left JOIN cg_state as sa on sa.id=ed.s_school_state left JOIN cg_city as ci on ci.id=s_school_city WHERE ed.i_user_id<>'" . $exclude_id_csv . "') as e on e.i_user_id=u.id
								LEFT JOIN {$this->db->USER_WORKS} AS w ON w.i_user_id = u.id 

								
								
							 {$where_cond} GROUP BY u.id ORDER BY u.`dt_created_on` DESC )
							) as  derived_tbl ";
							/*$this->db->dbprefix, $this->tbl_name, $user_type, $timestamp, $limit,$where_cond*/
        $query = $this->db->query($sql);
        $result_arr = $query->result_array();

        return $result_arr[0]['count'];
    }

    ###

    public function get_friend_status_me_him($i_me, $i_him) {

        try {
            $ret_ = array();
            if (intval($i_me) > 0 && intval($i_him) > 0 && intval($i_me) != intval($i_him)) {
                $s_qry = "SELECT 
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
						{$this->db->USER_CONTACTS} c, {$this->db->USERS} u
			   WHERE 
				( (c.i_requester_id = '".$i_me."' and c.i_accepter_id = '".$i_him."')  OR (c.i_accepter_id = '".$i_me."' and c.i_requester_id = '".$i_him."') )
				AND c.s_status = 'pending' 
				AND u.id=c.i_requester_id 
				
		";

                #cn.s_country_name  , {$this->db->MST_COUNTRY} cn 

                      
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
                
                unset($s_qry, $rs, $row, $i_me, $i_him);
            }
            return $ret_;
        } catch (Exception $err_obj) {
            show_error($err_obj->getMessage());
        }
    }

    //-------------------------------------- check whether already friend or not --------------------------
    public function if_already_friend($i_me, $i_him) {

        try {
            $ret_ = array();
            if (intval($i_me) > 0 && intval($i_him) > 0 && intval($i_me) != intval($i_him)) {
                $s_qry = "SELECT 
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
                        {$this->db->USER_CONTACTS} c, {$this->db->USERS} u
               WHERE 
                ( (c.i_requester_id = '".$i_me."' and c.i_accepter_id = '".$i_him."')  OR (c.i_accepter_id = '".$i_me."' and c.i_requester_id = '".$i_him."') )
                AND c.s_status = 'accepted' 
                AND u.id=c.i_requester_id 
                
        ";

                #cn.s_country_name  , {$this->db->MST_COUNTRY} cn 

                      
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
                
                unset($s_qry, $rs, $row, $i_me, $i_him);
            }
            return $ret_;
        } catch (Exception $err_obj) {
            show_error($err_obj->getMessage());
        }
    }

    # function to check if friend_request_already_sent

    function friend_request_already_sent($i_requester_id = '', $i_accepter_id = '') {
        $SQL = "SELECT COUNT(*) AS `check_count` FROM ".$this->db->USER_CONTACTS." WHERE `i_requester_id`='".$i_requester_id."'  AND `i_accepter_id` = '".$i_accepter_id."' AND `s_status` = 'pending'  ";
        $ROW = $this->db->query($SQL)->row_array(); #echo $this->db->last_query(); exit;

        if ($ROW['check_count'])
            return 1;
        else
            return 0;
    }

    ## ADDED TO MATCH PASSWORD

    function match_password($id = '', $pwd = '') {
        $SQL = "SELECT COUNT(*) AS `check_count` FROM ".$this->db->USERS." WHERE `id`='".$id."' AND `s_password` = '".get_salted_password($pwd)."' ";
        $ROW = $this->db->query($SQL)->row_array();
        //echo $ROW['check_count']; exit;
        if ($ROW['check_count'])
            return true;
        else
            return false;
    }

    function get_user_email_by_id($i_id) {
        try {
            $curLanguage = get_current_language();
            $ret_ = array();
            if (intval($i_id) > 0) {
                ////Using Prepared Statement///

                $s_qry = "SELECT s_first_name , s_last_name , s_email FROM " . $this->db->USERS . " WHERE id=?";

                            
                $rs = $this->db->query($s_qry, array(intval($i_id)));
                # echo $this->db->last_query() ."<br />";

                $edu_detail_arr = array();

                if (is_array($rs->result())) {

                    foreach ($rs->result() as $row) {
                        $ret_["id"] = $row->id; ////always integer
                        $ret_["s_profile_name"] = get_unformatted_string($row->s_first_name) . ' '
                                . get_unformatted_string($row->s_last_name);
                        $ret_["e_title"] = get_unformatted_string($row->e_title);
                        $ret_["s_title"] = (($row->e_title == 'Mr') ? 'Mr.' : (($row->e_title == 'Mrs') ? 'Mrs.' : 'Ms.'));
                        $ret_["s_first_name"] = get_unformatted_string($row->s_first_name);
                        $ret_["s_last_name"] = get_unformatted_string($row->s_last_name);

                        $ret_["s_email"] = $row->s_email;
                    }

                    $rs->free_result();
                }
                
                unset($s_qry, $rs, $row, $i_id);
                #dump($ret_); 
            }

            return $ret_;
        } catch (Exception $err_obj) {
            show_error($err_obj->getMessage());
        }
    }

    # function to check if username already exists or not... for unique profile url suffix

    function twitter_username_already_exists($user_first_name = '', $user_last_name = '') {
        $SQL = "SELECT COUNT(*) AS `check_count` FROM ".$this->db->USERS." WHERE `s_first_name`='".$user_first_name."'  AND `s_last_name`='".$user_last_name."'";
        $ROW = $this->db->query($SQL)->row_array(); //echo $this->db->last_query(); exit;

        if ($ROW['check_count'])
            return $ROW['check_count'];
        else
            return 0;
    }

    public function getTweetConnectionName($where = '') {

        $order_by = "ORDER BY id DESC";
        $sql = " SELECT id, s_tweet_id FROM {$this->db->USERS} {$where} {$order_by}";

        $query = $this->db->query($sql);
        $result_arr = $query->result_array(); #pr($result_arr,1);
        return $result_arr;
    }

    public function getUserInfo_by_tweet_id($s_tweet_id = '') {

        $order_by = "ORDER BY id DESC";
        $sql = " SELECT *  FROM {$this->db->USERS} 
					 WHERE s_tweet_id = '{$s_tweet_id}' 
					 AND i_status = 1 
                     AND i_isdeleted = 1
					 {$order_by}";

        $query = $this->db->query($sql);
        $result_arr = $query->result_array(); #pr($result_arr,1);
        return $result_arr[0];
    }

    public function check_user_first_login_in_a_day($user_id) {

        $current_day = date('Y-m-d');
        $sql_check_user = "select count(*) as count from cg_login_logs
						              where i_user_id = '".$user_id."' AND DATE(dt_login_on) = '{$current_day}'  ";

        $result_arr = $this->db->query($sql_check_user)->result_array();

        //echo $result_arr[0]['count'];


        $this->session->set_userdata('is_first_login_checked', 'true');
        ## if user already logged in current day
        if ($result_arr[0]['count'] == 1) {

            return 'true';
        } else {
            return 'false';
        }
    }

    ### NEW METHOD TO GET USER STATUS

    function getUserOnlineStatus($user_id) {

        $SQL = "SELECT * 
					FROM cg_users_status 
					WHERE i_user_id  = {$user_id} order by last_seen_date desc LIMIT 0,1";


        $result_arr = $this->db->query($SQL)->result_array();

        return $result_arr[0];
    }

    function bkup_getUserOnlineStatus($user_id) {

        $SQL = "SELECT * 
                    FROM cg_users_status 
                    WHERE i_user_id  = {$user_id} order_by ";

        $result_arr = $this->db->query($SQL)->result_array();

        return $result_arr[0];
    }


    public function updateUserOnlineStatus($user_id, $status, $chk_frnd, $chk_netpal, $chk_pp) {

        $sql_check_user = "select count(*) count from cg_users_online 
						where i_user_id = '".$user_id."'";

        $result_arr = $this->db->query($sql_check_user)->result_array();
        $current_time = get_db_datetime();

        if ($result_arr[0]['count']) {
            $sql = "UPDATE cg_users_online set ts_last_active = '".$current_time."' ,
									 s_status = '".$status."',
									 i_isfriend =  '".$chk_frnd."',
									 i_isnetpal = '".$chk_netpal."',
									 i_isprayerpartner = '".$chk_pp."'
									 WHERE i_user_id='".$user_id."'";

            $this->db->query($sql);
            //echo $this->db->last_query(); exit;
        }
		
        ## if status is maintained then do nothing keep the same
        ## else insert a record with all users status true by default
        $check_user = "select count(*) count from cg_users_status where i_user_id = '".$user_id."'";
        $s_arr = $this->db->query($check_user)->result_array();
        
        if ($s_arr[0]['count']) {
            $sql = "UPDATE cg_users_status 
                                set  last_seen_date = '".$current_time."',
                                     i_isfriend =  '".$chk_frnd."',
                                     i_isnetpal = '".$chk_netpal."',
                                     i_isprayerpartner = '".$chk_pp."'
                                WHERE i_user_id='".$user_id."' ";

            $this->db->query($sql);
        }
        else{
            ### insert in  users stauts
            $status_arr = array();
            $status_arr['i_user_id'] =  $user_id;
            $status_arr['i_isfriend'] =  $chk_frnd;
            $status_arr['i_isnetpal'] =  $chk_netpal;
            $status_arr['i_isprayerpartner'] =  $chk_pp;
            $status_arr['last_seen_date'] =  get_db_datetime();
                    
            $this->db->insert('cg_users_status', $status_arr);
            ###
        }
	
		
    }

    public function insert_rating($arr = array()) {
        if (count($arr) == 0) {
            return null;
        }
        $this->db->insert($this->db->user_profile_rating, $arr); //echo $this->db->last_query();
        return $this->db->insert_id();
    }

    /*     * *
     * Check if user email already exits
     * 
     */

    public function Chatname_exists($email, $i_user_id = '') {
        try {
            $ret_ = 0;
            if ($i_user_id == '') {

                $s_qry = "SELECT COUNT(*) i_count FROM " . $this->db->USERS . " WHERE ";
                $s_qry.=" binary s_chat_display_name=? ";


               
                $rs = $this->db->query($s_qry, array(
                    get_formatted_string($email)
                ));
                
            } else {

                $s_qry = "SELECT COUNT(*) i_count FROM " . $this->db->USERS . " WHERE ";
                $s_qry.=" binary s_chat_display_name=? ";
                $s_qry.=" AND id!=? ";

                
                $rs = $this->db->query($s_qry, array(
                    get_formatted_string($email),
                    intval($i_user_id)
                ));
               
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

    public function get_privacy_for_wall_post($id) {
        $SQL = "SELECT * FROM {$this->db->PRIVACY_SETTINGS} WHERE i_user_id='" . $id . "' AND s_section_name='wall'";
        $result_arr = $this->db->query($SQL)->result_array();
        return $result_arr[0];
    }

    public function get_privacy_for_msg($id) {
        $SQL = "SELECT * FROM {$this->db->PRIVACY_SETTINGS} WHERE i_user_id='" . $id . "' AND s_section_name='msg'";
        $result_arr = $this->db->query($SQL)->result_array();
        return $result_arr[0];
    }

    public function online_user_list_count() {
        $SQL = "SELECT count(*) as count FROM cg_users_online";
        $result_arr = $this->db->query($SQL)->result_array();
        return $result_arr[0]['count'];
    }
    function fetch_church_admin($user_id){
       $SQL = "SELECT id as admin_church_id ,s_name as admin_church_name,dt_join_on as admin_join_date FROM cg_church WHERE ch_admin_id='" . $user_id . "' AND i_disabled=1";  
          $result_arr = $this->db->query($SQL)->result_array();
            return $result_arr[0];
    }
    function fetch_church_member($user_id){
        $SQL = "SELECT c.s_name as member_church_name , cm.church_id as member_church_id ,cm.created_date as member_join_date FROM cg_church_member  cm, cg_church c  WHERE cm.member_id='" . $user_id . "' AND c.id = cm.church_id "; 
   $result_arr = $this->db->query($SQL)->result();
            return $result_arr;
        }

}
