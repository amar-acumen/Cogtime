<?php
include_once(APPPATH.'models/base_model.php');
class Projects_model extends Base_model
{
	
	public function __construct() 
	{
		parent::__construct();
	}

	public function get() {
		$sql = sprintf('SELECT * FROM '.$this->db->PROJECT.' order by id desc');
		$query = $this->db->query($sql);
		$result_arr = $query->result_array();
//pr($result_arr,1);
		return $result_arr;
	}
	
	
	
	public function get_by_id($id, $start_limit="", $no_of_page="") {
		if("$start_limit" == "") {
			$sql = sprintf('SELECT * FROM '.$this->db->PROJECT.'  where id = %s',  $id);
		}
		else {
			$sql = sprintf('SELECT * FROM '.$this->db->PROJECT.'  where id = %s limit %s, %s',  $id, $start_limit, $no_of_page);
		}

		$query = $this->db->query($sql); #echo $this->db->last_query(); exit;
		$result_arr = $query->result_array();
		
		$result_arr[0]['skill']  =  $this->get_all_skill_by_project_id($result_arr[0]['id']);

		return $result_arr[0];
	}
	
	

	public function insert($arr=array()) {
		if(count($arr)==0) {
			return null;
		}
		$this->db->insert($this->db->PROJECT, $arr); #echo $this->db->last_query();
		return $this->db->insert_id();
	}
	
	

	public function update($arr=array(), $id) {
		if(count($arr)==0) {
			return null;
		}
		$this->db->update($this->db->PROJECT, $arr, array('id'=>$id));
	}
	

	public function delete_by_id($id) {
	    $sql = sprintf( 'DELETE FROM '.$this->db->PROJECT.' WHERE id=%s', $id );
		$this->db->query($sql);
		
		$sql = sprintf( 'DELETE FROM '.$this->db->PROJECT_SKILL_REQUIRED.' WHERE i_project_id = %s', $id );
		$this->db->query($sql);
	}
	
	
	public function get_list($where='',$i_start=null,$i_limit=null,$s_order_by='1')
    {
        
        $limit  = (is_numeric($i_start) && is_numeric($i_limit))?" Limit ".intval($i_start).",".intval($i_limit):'';
        $sql  = " SELECT P.* FROM {$this->db->PROJECT} P
						LEFT JOIN {$this->db->PROJECT_SKILL_REQUIRED} S ON P.id = S.i_project_id
						{$where} GROUP BY P.id 
						ORDER BY id DESC {$limit}";

        $query     = $this->db->query($sql); 
        $result_arr = $query->result_array(); #pr($result_arr);
		
		if(count($result_arr)){
			
			foreach($result_arr as $key=> $val){
				$result_arr[$key]['skill']  =  $this->get_all_skill_by_project_id($val['id']);
				$result_arr[$key]['total_donation']  =  $this->get_total_fund_donated_by_project_id($val['id']);
				
				$d_where = " WHERE S_REQ.i_project_id = {$val['id']} 
								   AND S_REQ.e_status = 'accepted' ";
				
				$result_arr[$key]['total_skill_donated'] = $this->get_all_skill_req_count($d_where);
			}
		}
        return $result_arr;
    }
	
    public function get_list_count($where='')
    {
        $sql    = "     SELECT count(*) as i_total FROM
							(SELECT P.id
						FROM {$this->db->PROJECT} P
						LEFT JOIN {$this->db->PROJECT_SKILL_REQUIRED} S ON P.id = S.i_project_id
					    {$where}
						GROUP BY P.id  ) as drvd_tbl";
					 
        $query     = $this->db->query($sql);
        $result_arr = $query->result_array();
        return $result_arr[0]['i_total'];
    }
	
	public function insert_skill_req($arr=array()) {
		if(count($arr)==0) {
			return null;
		}
		$this->db->insert($this->db->PROJECT_SKILL_REQUIRED, $arr); #echo $this->db->last_query();
		return $this->db->insert_id();
	}
	
	public function update_skill_req($arr=array(), $id) {
		if(count($arr)==0) {
			return null;
		}
		$this->db->update($this->db->PROJECT_SKILL_REQUIRED, $arr, array('id'=>$id));
		#echo $this->db->last_query();
	}
	
	
	public function get_all_skill_by_project_id($i_project_id) {
		
		$sql = sprintf('SELECT * FROM '.$this->db->PROJECT_SKILL_REQUIRED.' WHERE i_project_id = %s order by id asc', $i_project_id);
		$query = $this->db->query($sql);
		$result_arr = $query->result_array();
//pr($result_arr,1);
		return $result_arr;
	}
	
	public function delete_skill_by_id($id) {
	    $sql = sprintf( 'DELETE FROM '.$this->db->PROJECT_SKILL_REQUIRED.' WHERE id=%s', $id );
		$this->db->query($sql);
	}
	
	
	public function get_all_skill_id_by_project_id($i_project_id) {
		
		$sql = sprintf('SELECT id FROM '.$this->db->PROJECT_SKILL_REQUIRED.' WHERE i_project_id = %s order by id asc', $i_project_id);
		$query = $this->db->query($sql);
		$result_arr = $query->result_array();
//pr($result_arr,1);
		return $result_arr;
	}
	
	### get Project list with all details #####
	
	
	public function get_project_details_list($where='',$i_start=null,$i_limit=null,$s_order_by='1')
    {
        ## fetching logged user's skill ##
		global $CI;
		
		$i_user_id = intval(decrypt($this->session->userdata('user_id')));
		
		$CI->load->model('users_model');
		$skill_arr = $CI->users_model->get_user_skill_detail_by_id($i_user_id);
		
		$skill = array();
		$ORDER_BY_OP = '';
		
		if(count($skill_arr)){
			
			foreach($skill_arr as $k=> $val){
				array_push($skill, $val['s_name']);
				
				if($k == 0){
					$ORDER_BY_OP .= " IF(ordr_name = '".$val['s_name']."', ordr_name,p_end_date) ";
				}
				else
				{
					$ORDER_BY_OP .= " AND IF(ordr_name = '".$val['s_name']."', ordr_name,p_end_date)";
				}
			}
		}
		
		
		$ORDER_STR = ($ORDER_BY_OP != '')?'('.$ORDER_BY_OP.'),':''; 
		
        $limit  = (is_numeric($i_start) && is_numeric($i_limit))?" Limit ".intval($i_start).",".intval($i_limit):'';
		
		$SQL = "SELECT * FROM (
								SELECT  P.id as project_id,
										P.s_title,
										P.i_country_id,
										P.s_city,
										P.s_state,
										P.s_description,
										P.dt_start_date,
										P.dt_end_date as p_end_date,
										P.f_project_cost,
										S.id as IDD,
										S.s_name as ordr_name ,
										S.dt_start_date as skill_start_dt,
										S.dt_end_date as skill_end_dt,
										S.i_total_day,
										(SELECT SUM(D.f_amount) 
															From cg_project_donation_history D 
															WHERE D.i_project_id = P.id
															AND D.i_order_status = '1') as donation_recieved 
										
							    FROM cg_project P 
										LEFT JOIN cg_project_skill_required S ON S.i_project_id = P.id 
										WHERE  P.dt_end_date >= CURDATE() 
										AND P.f_project_cost > (SELECT IFNULL(SUM(D.f_amount),0) 
															From cg_project_donation_history D 
															WHERE D.i_project_id = P.id
															AND D.i_order_status = '1')
													
										{$where}
										GROUP BY P.id

								UNION

								SELECT P.id as project_id,
			 						   P.s_title,
									   P.i_country_id,
									   P.s_city,
									   P.s_state,
									   P.s_description,
			 						   P.dt_start_date,
			 						   P.dt_end_date as p_end_date,
									   P.f_project_cost,
									   S.id as IDD,
									   S.s_name as ordr_name,
									   S.dt_start_date as skill_start_dt,
									   S.dt_end_date as skill_end_dt,
				   					   S.i_total_day,
									   (SELECT SUM(D.f_amount) 
															From cg_project_donation_history D 
															WHERE D.i_project_id = P.id
															AND D.i_order_status = '1') as donation_recieved 
								FROM cg_project P 
									LEFT JOIN cg_project_skill_required S ON S.i_project_id = P.id 
									WHERE  P.dt_end_date >= CURDATE()
									AND S.i_total_day > (SELECT COUNT(*) 
													  From cg_project_skill_request SR 
													  LEFT JOIN cg_project_skill_required S ON S.i_project_id = SR.i_project_id		
													  WHERE SR.s_skill_name = S.s_name AND SR.i_project_id = P.id
													  AND SR.e_status = 'accepted')
								   {$where}
								   GROUP BY P.id

							) as tbl
						ORDER BY {$ORDER_STR} p_end_date ASC
						{$limit}
";
#echo nl2br($SQL) ; exit;
        $query     = $this->db->query($SQL); 
        $result_arr = $query->result_array(); //pr($result_arr,1);
		
		if(count($result_arr)){
			
			foreach($result_arr as $key=> $val){
				$result_arr[$key]['skill']  =  $this->get_all_skill_by_project_id($val['project_id']);

				$d_where ='';
				$fund_where = '';
				
				$d_where = " WHERE S_REQ.i_project_id = {$val['project_id']} 
								   AND S_REQ.e_status = 'accepted' ";
								   
				$result_arr[$key]['skill_donor_list'] = $this->get_all_skill_req_list($d_where);
				
				$fund_where = "WHERE PF.i_project_id = {$val['project_id']} 
								    AND PF.i_order_status = 1
									AND PF.e_dnt_disclose_name = 'N'";
				$result_arr[$key]['fund_donor_list'] = $this->getFundDonationlist($fund_where);
			}
		}
		
        return $result_arr;
    }
	
    public function get_project_details_list_count($where='')
    {
        
        
        $sql    = "SELECT count(*) as count FROM (
										  SELECT  P.id 
												  FROM cg_project P 
												  LEFT JOIN cg_project_skill_required S ON S.i_project_id = P.id 
												  WHERE  P.dt_end_date >= CURDATE() 
												  AND P.f_project_cost > (SELECT IFNULL(SUM(D.f_amount),0) 
															 		From cg_project_donation_history D 
																 	WHERE D.i_project_id = P.id
																	AND D.i_order_status = '1' )
												  {$where}
												  GROUP BY P.id

											UNION
											
											SELECT P.id
													FROM cg_project P 
													LEFT JOIN cg_project_skill_required S ON S.i_project_id = P.id 
													WHERE  P.dt_end_date >= CURDATE()
													AND S.i_total_day > (SELECT COUNT(*) 
																From cg_project_skill_request SR 
																LEFT JOIN cg_project_skill_required S 
																ON S.i_project_id = SR.i_project_id		
																WHERE SR.s_skill_name = S.s_name AND SR.i_project_id = P.id
																AND SR.e_status = 'accepted')
													{$where}
													GROUP BY P.id
										) as tbl


					";
        $query     = $this->db->query($sql);
        $result_arr = $query->result_array();
        return $result_arr[0]['count'];
    }
	
	
	##############################################
	##### Getting all skill request by project id
	##############################################
	
	public function get_all_skill_req_list($where='',$i_start=null,$i_limit=null,$s_order_by='1')
    {
        
        $limit  = (is_numeric($i_start) && is_numeric($i_limit))?" Limit ".intval($i_start).",".intval($i_limit):'';
		
        $sql  = " SELECT P.*,
	   					 S_REQ.id as s_id,
						 S_REQ.i_user_id,
						 S_REQ.s_description,
						 S_REQ.d_start_date,
						 S_REQ.d_end_date,
						 S_REQ.e_status ,
						 S_REQ.s_skill_name,
						 CONCAT(U.s_first_name,' ',U.s_last_name) as s_profile_name,
						 S_REQ.i_user_id as post_owner_user_id,
						 S_REQ.dt_created_on as donation_dt
		
						FROM cg_project_skill_request S_REQ
						LEFT JOIN {$this->db->PROJECT} P ON P.id = S_REQ.i_project_id
						LEFT JOIN {$this->db->USERS} U ON S_REQ.i_user_id = U.id
						{$where} 
						ORDER BY S_REQ.id DESC {$limit}";
//echo nl2br($sql);
        $query     = $this->db->query($sql); 
        $result_arr = $query->result_array(); //pr($result_arr,1);
		
		
		
        return $result_arr;
    }
	
    public function get_all_skill_req_count($where='')
    {
        $sql    = "     SELECT count(*) as i_total FROM
							( SELECT S_REQ.id 
								FROM cg_project_skill_request S_REQ
								LEFT JOIN {$this->db->PROJECT} P ON P.id = S_REQ.i_project_id
								LEFT JOIN {$this->db->USERS} U ON S_REQ.i_user_id = U.id
					    		{$where}
								
							) as tbl  ";
					 
        $query     = $this->db->query($sql);
        $result_arr = $query->result_array();
        return $result_arr[0]['i_total'];
    }
	
	
	
	public function getSkillSufficency($skill_name, $project_id, $check_date){
		
					
	    $SQL = "SELECT 	COUNT(*) as count
						FROM cg_project_skill_request 
						WHERE s_skill_name  = '{$skill_name}' 
							  AND i_project_id = {$project_id}
							  AND e_status = 'accepted'
							  AND ('{$check_date}' BETWEEN d_start_date AND d_end_date )
							  ";
		$query     = $this->db->query($SQL);
		$result_arr = $query->result_array();
		
		return $result_arr[0]['count'];
	}
	
	
	##################################################
	##### End getting all skill request by project id
	##################################################
	
	##### insert skill request #########

	public function insert_skill_request($arr=array()) {
		if(count($arr)==0) {
			return null;
		}
		$this->db->insert('cg_project_skill_request', $arr); #echo $this->db->last_query();
		return $this->db->insert_id();
	}
	
	##### update skill request #########
	
	public function update_skill_request($arr=array(),$id) {
		  if(count($arr)==0) {
			  return null;
		  }
		  $this->db->update('cg_project_skill_request', $arr, array('id'=>$id)); //echo $this->db->last_query();
	}
	
	
	
	##### delete skill request
	
	public function deleteDonationRequest($id) {
	    
		$sql = sprintf( 'DELETE FROM cg_project_skill_request WHERE id=%s', $id );
		$this->db->query($sql);
	}
	
	
	#################################
	#### common Donation basket
	#################################
	  
	  public function insert_common_donation_details($arr=array()) {
		  if(count($arr)==0) {
			  return null;
		  }
		  $this->db->insert($this->db->COMMON_DONATION, $arr); //echo $this->db->last_query();
		  return $this->db->insert_id();
	  }
	
	
	  public function update_common_donation_details($arr=array(),$id) {
		  if(count($arr)==0) {
			  return null;
		  }
		  $this->db->update($this->db->COMMON_DONATION, $arr, array('id'=>$id)); //echo $this->db->last_query();
	  }
	
	#################################
	#### common Donation basket
	#################################
	
	
	
	#################################
	#### Project Fund Donation 
	#################################
	  
	  public function insert_project_fund_donation_details($arr=array()) {
		  if(count($arr)==0) {
			  return null;
		  }
		  $this->db->insert($this->db->PROJECT_DONATION_HISTORY, $arr); //echo $this->db->last_query();
		  return $this->db->insert_id();
	  }
	
	
	  public function update_project_fund_donation_details($arr=array(),$id) {
		  if(count($arr)==0) {
			  return null;
		  }
		  $this->db->update($this->db->PROJECT_DONATION_HISTORY, $arr, array('id'=>$id)); //echo $this->db->last_query();
	  }
	  
	  
	##############################################
	##### Getting all fund donation by project id
	##############################################
	
	public function getFundDonationlist($where='',$i_start=null,$i_limit=null,$s_order_by='1')
    {
        
        $limit  = (is_numeric($i_start) && is_numeric($i_limit))?" Limit ".intval($i_start).",".intval($i_limit):'';
		
        $sql  = " SELECT P.*,
	   					 PF.id as donation_id,
						 PF.dt_created_on as donation_dt,
						 PF.f_amount,
						 CONCAT(U.s_first_name,' ',U.s_last_name) as s_profile_name,
						 PF.i_user_id as post_owner_user_id,
						 PF.e_gift_aid_my_donation,
						 PF.e_dnt_disclose_name
		
						FROM {$this->db->PROJECT_DONATION_HISTORY} PF
						LEFT JOIN {$this->db->PROJECT} P ON P.id = PF.i_project_id
						LEFT JOIN {$this->db->USERS} U ON PF.i_user_id = U.id
						{$where} 
						ORDER BY PF.id DESC {$limit}";

        $query     = $this->db->query($sql); 
        $result_arr = $query->result_array(); //pr($result_arr,1);
		
		
		
        return $result_arr;
    }
	
	
	
	public function getFundDonationlistCount($where='')
    {
        $sql    = "     SELECT count(*) as i_total FROM
							( SELECT PF.id 
								FROM {$this->db->PROJECT_DONATION_HISTORY} PF
								LEFT JOIN {$this->db->PROJECT} P ON P.id = PF.i_project_id
								LEFT JOIN {$this->db->USERS} U ON PF.i_user_id = U.id
					    		{$where}
								
							) as tbl  ";
					 
        $query     = $this->db->query($sql);
        $result_arr = $query->result_array();
        return $result_arr[0]['i_total'];
    }
	
	
	public function get_total_fund_donated_by_project_id($p_id){
		
		$SQL = "SELECT SUM(D.f_amount) as donation_recieved 
								From cg_project_donation_history D 
								WHERE D.i_project_id = {$p_id}
								AND D.i_order_status = '1' ";
								
		$query     = $this->db->query($SQL);
        $result_arr = $query->result_array();
        return $result_arr[0]['donation_recieved'];
	}
	
	public function get_total_fund_donated(){
		
		$SQL = "SELECT SUM(D.f_amount) as donation_recieved 
								From cg_project_donation_history D 
								WHERE 
								 D.i_order_status = '1' ";
								
		$query     = $this->db->query($SQL);
        $result_arr = $query->result_array();
        return $result_arr[0]['donation_recieved'];
	}
	
	#################################
	#### Project Fund Donation 
	#################################

	#################################
	###### common donation list 
	#################################
	
	public function getCommonDonationlist($where='',$i_start=null,$i_limit=null,$s_order_by='1')
    {
        
        $limit  = (is_numeric($i_start) && is_numeric($i_limit))?" Limit ".intval($i_start).",".intval($i_limit):'';
		
        $sql  = " SELECT 
	   					 PF.id as donation_id,
						 PF.dt_created_on as donation_dt,
						 PF.f_amount,
						 CONCAT(U.s_first_name,' ',U.s_last_name) as s_profile_name,
						 PF.i_order_status
		
						FROM {$this->db->COMMON_DONATION} PF
						LEFT JOIN {$this->db->USERS} U ON PF.i_user_id = U.id
						{$where} 
						ORDER BY PF.id DESC {$limit}";

        $query     = $this->db->query($sql); 
        $result_arr = $query->result_array(); //pr($result_arr,1);
		
		
		
        return $result_arr;
    }
	
	
	
	public function getCommonDonationlistCount($where='')
    {
        $sql    = "     SELECT count(*) as i_total FROM
							( SELECT PF.id 
								FROM {$this->db->COMMON_DONATION} PF
								LEFT JOIN {$this->db->USERS} U ON PF.i_user_id = U.id
					    		{$where}
								
							) as tbl  ";
					 
        $query     = $this->db->query($sql);
        $result_arr = $query->result_array();
        return $result_arr[0]['i_total'];
    }
	
	#################################
	###### common donation list 
	#################################
	
	##################################################################
	##### view my projects (donated either skill/time/fund ) ###
	##################################################################
	
	public function get_my_project_details_list($where='',$i_start=null,$i_limit=null)
    {
        ## fetching logged user's skill ##
		
		$i_user_id = intval(decrypt($this->session->userdata('user_id')));
        $limit  = (is_numeric($i_start) && is_numeric($i_limit))?" Limit ".intval($i_start).",".intval($i_limit):'';
		
		$SQL = "SELECT *  FROM (
								  SELECT  P.id,
								  		  P.id as project_id,
										  P.s_title,
										  P.i_country_id,
										  P.s_city,
										  P.s_state,
										  P.s_description,
										  P.dt_start_date,
										  P.dt_end_date as p_end_date,
										  P.f_project_cost,
										  FD.i_order_status as 'request_status',
										  'N' as donation_skill,
										  (SELECT SUM(D.f_amount)
														  From cg_project_donation_history D
														  WHERE D.i_project_id = P.id
														  AND D.i_order_status = '1')  as donation_amt,
										  FD.dt_created_on as donation_dt
						  
										  FROM cg_project_donation_history FD
										  LEFT JOIN cg_project P ON P.id = FD.i_project_id
										  LEFT JOIN cg_project_skill_required s ON s.i_project_id = P.id 
										  WHERE FD.i_user_id = {$i_user_id}
										  AND FD.i_order_status = '1'
										  GROUP BY P.id
						  
								  UNION 
						  
								  SELECT 
										  P.id,
										  P.id as project_id,
										  P.s_title,
										  P.i_country_id,
										  P.s_city,
										  P.s_state,
										  P.s_description,
										  P.dt_start_date,
										  P.dt_end_date as p_end_date,
										  P.f_project_cost,
										  S_REQ.e_status as 'request_status',
										   S_REQ.s_skill_name as donation_skill,
										  (SELECT SUM(D.f_amount)
														  From cg_project_donation_history D
														  WHERE D.i_project_id = P.id
														  AND D.i_order_status = '1')  as donation_amt,
										 
										  S_REQ.dt_created_on as donation_dt
						  
										  FROM cg_project_skill_request S_REQ
										  LEFT JOIN cg_project P ON P.id = S_REQ.i_project_id
										  LEFT JOIN cg_project_skill_required s ON s.i_project_id = P.id 
										  WHERE S_REQ.i_user_id = {$i_user_id}
										  
										  GROUP BY P.id
						  ) as tbl GROUP BY id
						  ORDER BY donation_dt DESC
						  {$limit} ";
//echo nl2br($SQL) ; exit;
        $query     = $this->db->query($SQL); 
        $result_arr = $query->result_array(); //pr($result_arr,1);
		
		if(count($result_arr)){
			
			foreach($result_arr as $key=> $val){
				$result_arr[$key]['skill']  =  $this->get_all_skill_by_project_id($val['id']);

				$d_where ='';
				$fund_where = '';
				
				$d_where = " WHERE S_REQ.i_project_id = {$val['id']} 
								   AND S_REQ.e_status = 'accepted' ";
								   
				$result_arr[$key]['skill_donor_list'] = $this->get_all_skill_req_list($d_where);
				
				$fund_where = "WHERE PF.i_project_id = {$val['id']} 
								    AND PF.i_order_status = 1
									AND PF.e_dnt_disclose_name = 'N'";
				$result_arr[$key]['fund_donor_list'] = $this->getFundDonationlist($fund_where);
			}
		}
		
        return $result_arr;
    }
	
    public function get_my_project_details_list_count($where='')
    {
        
        $i_user_id = intval(decrypt($this->session->userdata('user_id')));
        $sql    = "SELECT count(*) as count FROM (
								  SELECT  P.id
										  FROM cg_project_donation_history FD
										  LEFT JOIN cg_project P ON P.id = FD.i_project_id
										  LEFT JOIN cg_project_skill_required s ON s.i_project_id = P.id 
										  WHERE FD.i_user_id = {$i_user_id}
										  AND FD.i_order_status = '1'
										  GROUP BY P.id
								  UNION
								  SELECT 
										  P.id
										  FROM cg_project_skill_request S_REQ
										  LEFT JOIN cg_project P ON P.id = S_REQ.i_project_id
										  LEFT JOIN cg_project_skill_required s ON s.i_project_id = P.id 
										  WHERE S_REQ.i_user_id = {$i_user_id}
										  GROUP BY P.id
						  ) as tbl";
        $query     = $this->db->query($sql);
        $result_arr = $query->result_array();
        return $result_arr[0]['count'];
    }
	
	##################################################################
	##### end view my projects (donated either skill/time/fund ) ###
	##################################################################
	
	
	### check skill request @ strt_date and end_date
	
	public function checkSkillRequest_RequestedDate($skill_name, $project_id, $start_dt, $end_dt){
		
		$i_user_id = intval(decrypt($this->session->userdata('user_id')));
		$SQL = "SELECT count(*) as count
							FROM  cg_project_skill_request SR
							WHERE SR.s_skill_name = '{$skill_name}'
							AND SR.i_project_id = {$project_id}
						    AND ( SR.d_start_date = '{$start_dt}' OR SR.d_end_date = '{$end_dt}'
							 OR '{$start_dt}' BETWEEN SR.d_start_date AND SR.d_end_date
							 OR '{$end_dt}' BETWEEN SR.d_start_date AND SR.d_end_date)
							AND SR.i_user_id = {$i_user_id}
							";
							
							#{$start_dt} BETWEEN SR.d_start_date AND SR.d_end_date
		$query     = $this->db->query($SQL);
        $result_arr = $query->result_array();
		
		if($result_arr[0]['count']  >= 1)
			return true;
		else
			return false;
	}
	
	public function get_skill_request_detail_by_id($id) {
			
		$sql = sprintf('SELECT * FROM cg_project_skill_request where id = %s',  $id);

		$query = $this->db->query($sql); #echo $this->db->last_query(); exit;
		$result_arr = $query->result_array();
		return $result_arr[0];
	}
	
	public function insert_request_info($arr=array()) {
		if(count($arr)==0) {
			return null;
		}
		$this->db->insert('cg_project_information', $arr); #echo $this->db->last_query();
		return $this->db->insert_id();
	}
	
	
	public function get_info_list($where='',$i_start=null,$i_limit=null,$s_order_by='1')
    {
        
        $limit  = (is_numeric($i_start) && is_numeric($i_limit))?" Limit ".intval($i_start).",".intval($i_limit):'';
        $sql  =  " SELECT PR.* 
						FROM cg_project_information PR
						LEFT JOIN cg_users U ON U.id = PR.i_user_id
						{$where} GROUP BY PR.id 
						ORDER BY id DESC {$limit}";

        $query     = $this->db->query($sql); 
        $result_arr = $query->result_array(); //pr($result_arr,1);
		
        return $result_arr;
    }
	
    public function get_info_list_count($where='')
    {
        $sql    = "     SELECT count(*) as i_total FROM
							(SELECT PR.id
							FROM cg_project_information PR
							LEFT JOIN cg_users U ON U.id = PR.i_user_id
							{$where}
							GROUP BY PR.id  ) as drvd_tbl";
					 
        $query     = $this->db->query($sql);
        $result_arr = $query->result_array();
        return $result_arr[0]['i_total'];
    }
	
	
	#### new methods to check if skill == required
	

public function get_project()
    {
		$s_qry="SELECT pro.s_title AS title,pro.s_description AS description,city.s_city AS city,state.s_state AS state,country.s_country AS country,pro.dt_start_date AS start_date,pro.dt_end_date AS end_date FROM ".$this->db->PROJECT
                    ." pro LEFT JOIN ".$this->db->CITY." city on city.id=pro.s_city 
						   LEFT JOIN ".$this->db->STATE." state on state.id=pro.s_state
							LEFT JOIN ".$this->db->COUNTRY." country on country.id=pro.i_country_id ORDER BY pro.id DESC limit 0,2";
                
				#echo $s_qry;
                 $rs=$this->db->query($s_qry);
				 
       
		  $result_arr=$rs->result_array();#pr($result_arr);;
		  return $result_arr;
				 
	}   
	
public function get_my_project($id)
{
	$query="SELECT project.s_title as project ,project.s_description as description FROM ".$this->db->PROJECT." project left JOIN ".$this->db->PROJECT_SKILL_REQUEST." skill_request on skill_request.i_project_id=project.id LEFT JOIN ".$this->db->PROJECT_SKILL_REQUIRED." skill_required on skill_required.i_project_id=project.id LEFT JOIN ".$this->db->PROJECT_DONATION_HISTORY." don on don.i_project_id=project.id WHERE (skill_request.i_user_id='".$id."' AND skill_request.e_status='accepted') or don.i_user_id='".$id."' GROUP BY project.id DESC limit 0,2";

	$result=$this->db->query($query);
	$result_arr=$result->result_array();
	return $result_arr;
}
public function get_my_project_count($id)
{
	$query="SELECT project.s_title as project ,project.s_description as description FROM ".$this->db->PROJECT." project left JOIN ".$this->db->PROJECT_SKILL_REQUEST." skill_request on skill_request.i_project_id=project.id LEFT JOIN ".$this->db->PROJECT_SKILL_REQUIRED." skill_required on skill_required.i_project_id=project.id LEFT JOIN ".$this->db->PROJECT_DONATION_HISTORY." don on don.i_project_id=project.id WHERE (skill_request.i_user_id='".$id."' AND skill_request.e_status='accepted') or don.i_user_id='".$id."' GROUP BY project.id DESC";
	$result=$this->db->query($query);
	$result_arr=$result->num_rows();
	return $result_arr;
}
}
