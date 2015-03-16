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

class My_blog_post_model extends Base_model {
    # constructor definition...

    public function __construct() {
        try {
            parent::__construct();
            $this->conf = get_config();
            $this->load->model("users_model");
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

    public function fetch_multi($s_where = null, $i_start = null, $i_limit = null, $s_order_by = null) {     // used in both user end and admin
        try {

            $ret_ = array();

            $language = get_current_language();

            $s_qry = "SELECT p.*, COUNT(pc.id) AS no_of_comments, CONCAT(U.s_first_name,' ',U.s_last_name) as user_name FROM {$this->db->USER_BLOG_POST} AS p LEFT JOIN {$this->db->USER_BLOG_POST_COMMENTS} AS pc ON p.id=pc.i_blog_post_id LEFT JOIN {$this->db->USERS} AS U on U.id=p.i_user_id"
                    . $s_where . " GROUP BY p.id ";

            /* cn.s_country_name {$this->db->MST_COUNTRY} cn */

            //////////For Pagination///////////*don't change*/
            //$s_qry=str_replace("'","''",$s_qry);//for string operation in procedure
            $s_qry = $s_qry . (trim($s_order_by) != "" ? " ORDER BY " . $s_order_by . "" : "ORDER BY id DESC") . " " . (is_numeric($i_start) && is_numeric($i_limit) ? " LIMIT " . intval($i_start) . "," . intval($i_limit) : "");

            //echo "where : ".$s_where;
            //echo '***********************'.$s_qry;
            //////////end For Pagination//////////                

                
            $rs = $this->db->query($s_qry);
            $rst_data = $rs->result();
            //print_r($rst_data);
            
            unset($s_qry, $rs, $row, $i_cnt, $s_where, $i_start, $i_limit);
            return $rst_data;
        } catch (Exception $err_obj) {
            show_error($err_obj->getMessage());
        }
    }

    public function gettotal_info_admin($s_where = null) {
        try {
            $ret_ = 0;

            $s_qry = "SELECT p.*, COUNT(pc.id) AS no_of_comments FROM {$this->db->USER_BLOG_POST} AS p LEFT JOIN {$this->db->USER_BLOG_POST_COMMENTS} AS pc ON p.id=pc.i_blog_post_id LEFT JOIN {$this->db->USERS} AS U on U.id=p.i_user_id"
                    . $s_where . " GROUP BY p.id ";


            $s_qry = "SELECT COUNT(*) AS i_total 
                    FROM 
                        {$this->db->USER_BLOG_POST} AS p LEFT JOIN {$this->db->USER_BLOG_POST_COMMENTS} AS pc ON p.id=pc.i_blog_post_id LEFT JOIN {$this->db->USERS} AS U on U.id=p.i_user_id" . $s_where;
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
     * Fetch Total records
     * @param string $s_where, ex- " status=1 AND deleted=0 " 
     * @returns int on success and FALSE if failed 
     */

    public function gettotal_info($s_where = null) {
        try {
            $ret_ = 0;


            $s_qry = "SELECT COUNT(*) AS i_total 
					FROM 
						{$this->db->USER_BLOG_POST} AS p
					" . $s_where;
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
        
    }

    public function get_by_id($id) {

        $sql = 'SELECT * FROM ' . $this->db->USER_BLOG_POST . '  where id = "'.$id.'"';
        $query = $this->db->query($sql); #echo $this->db->last_query(); exit;
        $result_arr = $query->result_array();
        //pr($result_arr[0]);
        return $result_arr[0];
    }

    public function get_detail_by_id($id) {

        $sql = "SELECT p.*, CONCAT(u.s_first_name,' ',u.s_last_name) as user_name, u.s_profile_photo,u.e_gender FROM " . $this->db->USER_BLOG_POST . " as p LEFT JOIN " . $this->db->USERS . " as u on p.i_user_id=u.id  where p.id = '".$id."'";
        $query = $this->db->query($sql); #echo $this->db->last_query(); exit;

        $result_arr = $query->result_array();
        //pr($result_arr);
        return $result_arr[0];
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
            if ($this->db->insert($this->db->USER_BLOG_POST, $info))
                return true;
            else
                return false;
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
        try {

            $this->db->where('id', $i_id);
            if ($this->db->update($this->db->USER_BLOG_POST, $info))
                return true;
            else
                return false;
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

    public function add_bog($add_arr, $title) {
        if ($title == '')
            $return_str = $this->db->insert($this->db->USER_BLOGS, $add_arr);
        else {
            $this->db->where('i_user_id', intval(decrypt($this->session->userdata('user_id'))));
            $return_str = $this->db->update($this->db->USER_BLOGS, $add_arr);
        }
        if ($return_str)
            return true;
        else
            return false;
    }

    public function show_all_comments($s_where, $i_start = null, $i_limit = null, $s_order_by = null) {
        try {
            $s_qry = "SELECT c.* , CONCAT(u.s_first_name,' ', u.s_last_name) AS s_profile_name, u.s_profile_photo,u.e_gender 
						FROM {$this->db->USER_BLOG_POST_COMMENTS} AS c,{$this->db->USERS} u "
                    . $s_where . " AND c.i_user_id=u.id ";

            /* cn.s_country_name {$this->db->MST_COUNTRY} cn */

            //////////For Pagination///////////*don't change*/
            //$s_qry=str_replace("'","''",$s_qry);//for string operation in procedure
            $s_qry = $s_qry . (trim($s_order_by) != "" ? " ORDER BY " . $s_order_by . "" : "ORDER BY id DESC") . " " . (is_numeric($i_start) && is_numeric($i_limit) ? " LIMIT " . intval($i_start) . "," . intval($i_limit) : "");

            #echo '***********************'.$s_qry;
            //////////end For Pagination//////////                

                 
            $rs = $this->db->query($s_qry);
            $rst_data = $rs->result();
            //print_r($rst_data);
            
            return $rst_data;
        } catch (Exception $err_obj) {
            show_error($err_obj->getMessage());
        }
    }

    public function gettotal_comments($s_where = null) {
        try {
            $ret_ = 0;


            $s_qry = "SELECT COUNT(*) AS i_total 
						FROM {$this->db->USER_BLOG_POST_COMMENTS} AS c,{$this->db->USERS} u "
                    . $s_where . " AND c.i_user_id=u.id ";
            #echo '***********************'.$s_qry;
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

    public function add_post_cmnts($add_arr) {
        $return_str = $this->db->insert($this->db->USER_BLOG_POST_COMMENTS, $add_arr);
        //echo $this->db->last_query();
        $last_id = $this->db->insert_id();
        if ($return_str)
            return $last_id;
        else
            return $last_id;
    }

    public function delete_by_id($id) {

        $sql = 'DELETE FROM ' . $this->db->USER_BLOG_POST_COMMENTS . ' WHERE i_blog_post_id="'.$id.'"';
        $this->db->query($sql);

        $sql = 'DELETE FROM ' . $this->db->USER_BLOG_POST . ' WHERE id="'.$id.'"';
        $this->db->query($sql);


        #echo $this->db->last_query(); exit;
    }

    public function delete_blog_data_by_id($blog_id) {
        $sql = 'DELETE FROM ' . $this->db->USER_BLOG_POST_COMMENTS . ' WHERE i_blog_id="'.$blog_id.'"';
        $this->db->query($sql);

        $sql = 'DELETE FROM ' . $this->db->USER_BLOG_POST . ' WHERE i_blog_id="'.$blog_id.'"';
        $this->db->query($sql);

        $sql = 'DELETE FROM ' . $this->db->USER_BLOGS . ' WHERE id="'.$blog_id.'"';
        $this->db->query($sql);
    }

    public function change_status($status, $id) {

        if ($status != '' && $id != '') {
            $sql = sprintf("UPDATE {$this->db->USER_BLOG_POST} SET `i_disable` = '%s'
                           WHERE `id` ='%s'"
                    , $status, $id);
            $this->db->query($sql); # echo $this->db->last_query();exit;
            return true;
        }
    }

    public function getCommentsbyArticleId($s_where, $i_start = null, $i_limit = null, $s_order_by = null) {
        $s_qry = "SELECT c.* , CONCAT(u.s_first_name,' ', u.s_last_name) AS s_profile_name,u.s_profile_photo,u.e_gender 
						FROM {$this->db->USER_BLOG_POST_COMMENTS} AS c,{$this->db->USERS} u "
                . $s_where . " AND c.i_user_id=u.id ";

        $s_qry = $s_qry . (trim($s_order_by) != "" ? " ORDER BY " . $s_order_by . "" : "ORDER BY id DESC") . " " . (is_numeric($i_start) && is_numeric($i_limit) ? " LIMIT " . intval($i_start) . "," . intval($i_limit) : "");
        $query = $this->db->query($s_qry); // echo $this->db->last_query(); exit;
        $result_arr = $query->result_array();

        return $result_arr;
    }

    public function get_minister_shouts_list($where = '', $i_start = null, $i_limit = null, $s_order_by = '') {
        $limit = (is_numeric($i_start) && is_numeric($i_limit)) ? " Limit " . intval($i_start) . "," . intval($i_limit) : '';
        $s_order_by = ($s_order_by != '') ? 'ORDER BY ' . $s_order_by : 'ORDER BY id DESC';

        $sql = " SELECT BP.*, 
					CONCAT(U.s_first_name,' ', U.s_last_name) AS s_profile_name,
					U.s_profile_photo,U.e_gender
					FROM {$this->db->USER_BLOG_POST} BP
					LEFT JOIN {$this->db->USER_BLOGS} B ON B.id = BP.i_blog_id
					LEFT JOIN  {$this->db->USERS} U ON U.id = BP.i_user_id 
					
					{$where}  {$s_order_by} {$limit}";

        $query = $this->db->query($sql); // echo $this->db->last_query(); exit;
        $result_arr = $query->result_array(); //pr($result_arr,1);

        if (count($result_arr)) {
            foreach ($result_arr as $key => $val) {

                $wh = " WHERE c.i_blog_post_id  = {$val['id']} ";
                $result_arr[$key]['total_comments'] = $this->gettotal_comments($wh);

                $result_arr[$key]['comment_arr'] = $this->getCommentsbyArticleId($wh);
            }
        }


        return $result_arr;
    }

    public function get_minister_shouts_count($where = '') {


        $sql = "SELECT count(*) as i_total 
					FROM {$this->db->USER_BLOG_POST} BP
					LEFT JOIN {$this->db->USER_BLOGS} B ON B.id = BP.i_blog_id
					LEFT JOIN  {$this->db->USERS} U ON U.id = BP.i_user_id 
				  	{$where} ";
        $query = $this->db->query($sql); //echo $this->db->last_query();
        $result_arr = $query->result_array(); //pr($result_arr);
        return $result_arr[0]['i_total'];
    }

    public function get_minister_list($where = '', $i_start = null, $i_limit = null, $s_order_by = '') {
        $limit = (is_numeric($i_start) && is_numeric($i_limit)) ? " Limit " . intval($i_start) . "," . intval($i_limit) : '';
        $s_order_by = ($s_order_by != '') ? 'ORDER BY ' . $s_order_by : 'ORDER BY id DESC';

        $sql = " SELECT B.*, 
					U.id as user_id,
					CONCAT(U.s_first_name,' ', U.s_last_name) AS s_profile_name,
					U.s_profile_photo,U.e_gender
					FROM 
					{$this->db->USERS} U 
					LEFT JOIN {$this->db->USER_BLOGS} B ON B.i_user_id = U.id					
					{$where}  {$s_order_by} {$limit}";

        $query = $this->db->query($sql); //echo $this->db->last_query(); exit;
        $result_arr = $query->result_array(); //pr($result_arr,1);



        return $result_arr;
    }

    public function get_minister_count($where = '') {


        $sql = "SELECT count(*) as i_total 
					FROM(
					  SELECT U.id
					  FROM
					 {$this->db->USERS} U 
					LEFT JOIN {$this->db->USER_BLOGS} B ON B.i_user_id = U.id
				  	{$where}) as drvd_tbl ";
        $query = $this->db->query($sql); //echo $this->db->last_query();
        $result_arr = $query->result_array(); //pr($result_arr);
        return $result_arr[0]['i_total'];
    }
public function get_blog_post_by_user_id($i_user_id, $s_where, $i_start_limit='', $i_no_of_page='')
	{
	if("$i_start_limit" == "") {
			$sql = "select * from cg_user_blog_post ".$s_where." and i_user_id=".$i_user_id." order by id desc";
		}
		else {
		
		
		
			 $sql = "select * from cg_user_blog_post ".$s_where." and i_user_id=".$i_user_id." order by id desc
			 limit ".$i_start_limit.",".$i_no_of_page."";
		}

#AND t.i_user_id != '%2\$s'
		$query = $this->db->query($sql); #echo "sql ==>". ($sql);exit; 
		$result_arr = $query->result_array();
        
     //pr($result_arr);
	 	//$result_arr = check_friend_netpal_status($result_arr);
     
		return $result_arr;
	}
	public function get_total_blog_post_by_user_id($i_user_id,$s_where)
	{
	$sql="select count(*) as count from cg_user_blog_post ".$s_where." and i_user_id=".$i_user_id;
	$query = $this->db->query($sql); #echo "sql ==>". ($sql);exit; 
		$result_arr = $query->result_array();
		return $result_arr['0']['count'];
	}
    public function __destruct() {
        
    }
      public function get_all_blog_comment_blogid($i_blog_id){
            {$sql="select distinct c.* ,CONCAT(u.s_first_name,' ', u.s_last_name) s_profile_name, u.s_profile_photo,u.e_gender from cg_users u ,cg_user_blog_post_comments c where u.id = c.i_user_id AND c.i_blog_post_id='$i_blog_id' ";}
//echo $sql; exit;
$query=$this->db->query($sql);
$result_arr=$query->result_array();
//echo $sql;exit;
//echo $i_start_limit;
//pr($result_arr,1);
return $result_arr;
            
        }
        public function get_blog_all_comments($s_where,$i_start_limit='', $i_no_of_page=''){
            $sql = "select c.*,p.s_post_title,p.s_post_description,CONCAT(u.s_first_name,' ',u.s_last_name) AS s_profile_name,u.s_profile_photo,u.e_gender from cg_user_blog_post_comments c,cg_user_blog_post p ,cg_users u $s_where and c.i_blog_post_id = p.id and u.id = c.i_user_id ORDER BY c.id DESC limit ".$i_start_limit.",".$i_no_of_page;
            //echo $sql;
            //exit;
            $query=$this->db->query($sql);
$result_arr=$query->result_array();
//echo $sql;exit;
//echo $i_start_limit;
//pr($result_arr,1);
return $result_arr;
        }
        public function get_blog_all_comments_total($s_where){
            $sql = "select count(*) as count from cg_user_blog_post_comments c,cg_users u $s_where and u.id = c.i_user_id ORDER BY c.id DESC";
            //echo $sql;
            //exit;
            $query = $this->db->query($sql); #echo "sql ==>". ($sql);exit; 
		$result_arr = $query->result_array();
		return $result_arr['0']['count'];
        }

}

// end of class definition...
