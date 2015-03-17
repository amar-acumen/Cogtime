<?php
include_once(APPPATH.'models/base_model.php');
class Church_newsfeed_model extends Base_model
{
	
	public function __construct() 
	{
		parent::__construct();
	}

        function get_church_info($c_id){
             $query = $this->db->get_where('cg_church', array('id' => $c_id));
             $result = $query->result();
			 
             return $result;
            
        }
        function get_service_info($c_id){
           
              $query = $this->db->query('select * from cg_church_schedul where c_id = '.$c_id.' ORDER BY c_update ASC LIMIT 0, 5');
             $result = $query->result();
             return $result;
            
        }
		
		function get_church_admin_data($c_id)
		{
			$query = $this->db->get_where('cg_church',array('ch'=>$c_id));
			$result = $query->result();
			
			return $result[0];
		}
		public function insert($arr) {
			#dump($arr);
			$this->db->insert('church_newsfeed', $arr);
			return $this->db->insert_id();
		}
		public function get_newsfeeds_by_church_id($c_id,$user_id,$page,$offset)
		{
			//$query = $this->db->order_by('id', 'DESC')->get_where('cg_church_newsfeed',array('i_owner_id'=>$c_id),$offset,$page);
			$sql='select cn.*,cp.privacy from cg_church_newsfeed cn left join cg_church_post_privacy_settings cp on cn.id=cp.feed_id where cn.i_owner_id = "'.$_SESSION['logged_church_id'].'" AND (privacy =1 and posted_by="'.$user_id.'") OR ( privacy=2) OR (privacy=3 and (viewed_by="'.$user_id.'" or posted_by="'.$user_id.'")) group by id order by id desc  limit '.$page.','.$offset.'';
			$query=$this->db->query($sql);
			$result = $query->result();
			//echo $this->db->last_query();exit;
			return $result;
			
		}
		public function get_total_newsfeeds_by_church_id($i_church_id,$user_id)
		{
			$sql='select cn.* from cg_church_newsfeed cn left join cg_church_post_privacy_settings cp on cn.id=cp.feed_id where cn.i_owner_id = "'.$_SESSION['logged_church_id'].'" AND (privacy =1 and posted_by="'.$user_id.'") OR ( privacy=2) OR (privacy=3 and (viewed_by="'.$user_id.'" or posted_by="'.$user_id.'")) group by id ';
			$query=$this->db->query($sql);
			//$query = $this->db->get_where('cg_church_newsfeed',array('i_owner_id'=>$i_church_id));
			$result = $query->result();
			
			return count($result);
		}
		
		public function get_by_id($i_newsfeed_id) {
	
	    $_ret = array();
		if(intval($i_newsfeed_id)>0)
		{
			$sql = "SELECT * FROM cg_church_newsfeed WHERE id = '".intval($i_newsfeed_id)."'";
			$query = $this->db->query($sql);
			$result_arr = $query->result_array();
			 $_ret = $result_arr[0];
		}

		return  $_ret;
	}
	public function insert_privacy($arr)
	{
		$ids=$this->db->insert('cg_church_post_privacy_settings',$arr);
		return $ids;
	}
	 public function postLikeUnlike($like_unlike_information_array,$like_or_unlike="like")
  {
        
         $response['value'] = false ;
         $response['message'] = "Success" ;
         $response['last_inserted_id'] = '';
		 
		  $sql_present="SELECT COUNT(*) as total FROM {$this->db->dbprefix}church_newsfeed_{$like_or_unlike} 
		  					WHERE i_newsfeed_id='{$like_unlike_information_array['i_newsfeed_id']}' 
			 				AND i_{$like_or_unlike}d_user_id='{$like_unlike_information_array["i_{$like_or_unlike}d_user_id"]}'
			 			"; 
            $res = $this->db->query($sql_present);
            $result = $res->row_array();
            $is_present = $result['total'];


       if($like_or_unlike=="like"){

         
      
           if( $is_present == 0){
			//$this->db->insert($this->db->dbprefix."user_ring_post_like",$like_unlike_information_array);
			//echo $this->db->last_query();exit;   
			  
			   
            if($this->db->insert($this->db->dbprefix."church_newsfeed_like",$like_unlike_information_array))
            {
				
             //  $sql_del ="DELETE FROM {$this->db->dbprefix}user_ring_post_unlike WHERE i_ring_post_id='{$like_unlike_information_array['i_ring_post_id']}' AND i_unliked_user_id='{$like_unlike_information_array['i_liked_user_id']}'";
              //  $this->db->query($sql_del);
                $response['last_inserted_id'] = $this->db->insert_id();
                $response['value'] = true ;
                $response['message'] = "Succes" ;
            }
           }
     
        
       }
	   /*else if($like_or_unlike=="unlike")
        {

        if($is_present==0)
            {
            if($this->db->insert($this->db->dbprefix."user_ring_post_unlike",$like_unlike_information_array))
                {

                    $sql_del ="DELETE FROM {$this->db->dbprefix}user_ring_post_like WHERE i_ring_post_id='{$like_unlike_information_array['i_ring_post_id']}' AND i_liked_user_id='{$like_unlike_information_array['i_unliked_user_id']}'";
                    $this->db->query($sql_del);
                    $response['last_inserted_id'] = $this->db->insert_id();
                    $response['value'] = true ;
                    $response['message'] = "Success" ;
                }
            }

        }*/
//pr($response);
        return $response;
  }
  
  function insert_comment($arr)
  {
	$this->db->insert('cg_church_newsfeed_comments',$arr);
	return $this->db->insert_id();
  }


  public function update_by_id($id, $arr) {
		$this->db->update('cg_church_newsfeed', $arr, array('id' => $id));
}


public function delete_by_id($id) {
	$sql = sprintf( "DELETE FROM cg_church_newsfeed WHERE id=%s",$id );

	$this->db->query($sql);
	
	$sql = sprintf( "DELETE FROM cg_church_newsfeed_comments WHERE i_feed_id=%s", $id );

	$this->db->query($sql);
	
	# delete from like table #
	$sql = sprintf( "DELETE FROM cg_church_newsfeed_like WHERE i_newsfeed_id=%s", $id );

	$this->db->query($sql);
}
public function del_privacy($post)
{
	$query=$this->db->query(" delete from cg_church_post_privacy_settings where feed_id='".$post."'");
	return true;
}	
}
