<?php
include_once(APPPATH.'models/base_model.php');
class Church_model extends Base_model
{
	
	public function __construct() 
	{
		parent::__construct();
	}

	
	public function get_by_id($id, $start_limit="", $no_of_page="") {
		
		$sql = sprintf('SELECT * FROM '.$this->db->CHURCH.'  where id = %s',  $id);
		
		$query = $this->db->query($sql); #echo $this->db->last_query(); exit;
		$result_arr = $query->result_array();
		return $result_arr[0];
	}
	
	

	public function insert($arr=array()) {
		if(count($arr)==0) {
			return null;
		}
		$this->db->insert($this->db->CHURCH, $arr); #echo $this->db->last_query();
		return $this->db->insert_id();
	}
	
	

	public function update($arr=array(), $id) {
		if(count($arr)==0) {
			return null;
		}
		$this->db->update($this->db->CHURCH, $arr, array('id'=>$id));
		#echo $this->db->last_query();
	}
	

	public function delete_by_id($id) {
	    $sql = sprintf( 'DELETE FROM '.$this->db->CHURCH.' WHERE id=%s', $id );
		$this->db->query($sql);
		
	}
	
	
	public function get_list($where='',$i_start=null,$i_limit=null,$s_order_by='1')
    {
        
        $limit  = (is_numeric($i_start) && is_numeric($i_limit))?" Limit ".intval($i_start).",".intval($i_limit):'';
         $sql  = " SELECT C.* 
				  		FROM {$this->db->CHURCH} C
						LEFT JOIN {$this->db->COUNTRY} mst_c on mst_c.id=C.i_country_id
						LEFT JOIN {$this->db->STATE} mst_s on mst_s.id=C.i_state_id
						LEFT JOIN {$this->db->CITY} mst_city on mst_city.id=C.i_city_id
						{$where} GROUP BY C.id 
						ORDER BY C.id DESC {$limit}"; 
						/*LEFT JOIN cg_city ct ON  ct.i_country_id = mst_c.id
						LEFT JOIN cg_state s ON  s.id =  ct.i_state_id*/
						
		//$sql       = 		"call sp_find_church('".$where."','".$limit."');";

        $query     = $this->db->query($sql); //echo $this->db->last_query();
        $result_arr = $query->result_array(); //pr($result_arr,1);
        return $result_arr;
    }
	
    public function get_list_count($where='')
    {
        $sql    = "     SELECT count(*) as i_total FROM (
									SELECT C.id
									FROM {$this->db->CHURCH} C
									LEFT JOIN {$this->db->COUNTRY} mst_c on mst_c.id=C.i_country_id
									LEFT JOIN {$this->db->STATE} mst_s on mst_s.id=C.i_state_id
									LEFT JOIN {$this->db->CITY} mst_city on mst_city.id=C.i_city_id
									{$where}
									GROUP BY C.id 
									) AS TBL ";
					/* LEFT JOIN cg_city ct ON  ct.i_country_id = mst_c.id
									LEFT JOIN cg_state s ON  s.id =  ct.i_state_idLEFT JOIN {$this->db->CITY} mst_city on mst_city.id=C.i_city_id*/
        $query     = $this->db->query($sql);// echo $this->db->last_query();
        $result_arr = $query->result_array();
        return $result_arr[0]['i_total'];
    }
	
	public function change_status($status ,$id) {
		
		  $sql = sprintf( "UPDATE {$this->db->CHURCH} SET `i_disabled` = '%s'
						   WHERE `id` ='%s'"
					  , $status, $id );
		  $this->db->query($sql); //echo $this->db->last_query();exit;
		  return true;
	}

}
