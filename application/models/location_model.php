<?php
include_once(APPPATH.'models/base_model.php');
class Location_model extends Base_model
{
	
	public function __construct() 
	{
		parent::__construct();
	}

	 public function get_city_detail($letter)
	 {
		$sql	= "SELECT * FROM cg_city WHERE  s_city like '{$letter}%'";
		$rst	= $this->db->query($sql);
		$result	= $rst->result();
		//pr($result);exit;
		return $result;
	}
	
	
	public function get_search_detail($letter)
	 {
		$sql	= "(SELECT con.s_country, 
						   s.s_state, 
						   ct.s_city, 
						   'country_res' as result_type 
						    FROM cg_country con
							LEFT JOIN cg_city ct ON  ct.i_country_id = con.id
							LEFT JOIN cg_state s ON  s.id =  ct.i_state_id
							WHERE  con.s_country like '{$letter}%'
							GROUP BY con.id)
				   	UNION
					(SELECT con.s_country, 
							s.s_state, 
							ct.s_city,
							'state_res' as result_type  
							FROM cg_state s
							LEFT JOIN cg_city ct ON s.id =  ct.i_state_id
							LEFT JOIN cg_country con ON  s.i_country_id = con.id
							WHERE  s.s_state like '{$letter}%' 
							GROUP BY s.id)
					UNION
					(SELECT  con.s_country,
							 s.s_state,
							 ct.s_city,
							 'city_res' as result_type  
							 FROM cg_city ct
						LEFT JOIN cg_state s ON s.id =  ct.i_state_id
						LEFT JOIN cg_country con ON  ct.i_country_id = con.id
						WHERE  ct.s_city like '{$letter}%'
						GROUP BY ct.id)
					
				  ";
		
		$rst	= $this->db->query($sql);
		$result	= $rst->result_array();
		//pr($result);exit;
		return $result;
	}
	
	
	 public function get_friend_edu_search_detail($letter)
	 {
		$sql	= "(SELECT con.s_country, 
						   s.s_state, 
						   ct.s_city, 
							edu.s_school_name,
						   'country_res' as result_type 
						  FROM 
							cg_country con
							LEFT JOIN cg_city ct ON  ct.i_country_id = con.id
							LEFT JOIN cg_state s ON  s.id =  ct.i_state_id
						  LEFT JOIN cg_user_education edu ON edu.s_school_country = con.id

							WHERE  con.s_country like '{$letter}%'
							GROUP BY con.id)
				   	UNION
					(SELECT con.s_country, 
							s.s_state, 
							ct.s_city,
							edu.s_school_name,
							'state_res' as result_type  
							FROM cg_state s
							LEFT JOIN cg_city ct ON s.id =  ct.i_state_id
							LEFT JOIN cg_country con ON  s.i_country_id = con.id
						  LEFT JOIN cg_user_education edu ON edu.s_school_state = s.id
							WHERE  s.s_state like '{$letter}%' 
							GROUP BY s.id)
					UNION
					(SELECT  con.s_country,
							 s.s_state,
							 ct.s_city,
							 edu.s_school_name,
							 'city_res' as result_type  
							 FROM cg_city ct
						LEFT JOIN cg_state s ON s.id =  ct.i_state_id
						LEFT JOIN cg_country con ON  ct.i_country_id = con.id
						LEFT JOIN cg_user_education edu ON edu.s_school_city = ct.id
						WHERE  ct.s_city like '{$letter}%'
						GROUP BY ct.id)
				
				UNION
					(SELECT  con.s_country,
							 s.s_state,
							 ct.s_city,
							edu.s_school_name,
							 'school_res' as result_type  
							 FROM 
						cg_user_education edu 
						LEFT JOIN cg_city ct ON ct.id = edu.s_school_city
						LEFT JOIN cg_state s ON s.id =  ct.i_state_id
						LEFT JOIN cg_country con ON  ct.i_country_id = con.id
						WHERE  edu.s_school_name like '{$letter}%'
						GROUP BY ct.id)
			 
					
				  ";
		
		$rst	= $this->db->query($sql);
		$result	= $rst->result_array();
		//pr($result);exit;
		return $result;
	}
	
	
	
}
