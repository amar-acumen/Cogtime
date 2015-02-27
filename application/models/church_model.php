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
	
	
 public function insert_information($logged_id,$_ret,$time,$content,$email) {
             
              
		$arr = array(
                    'dt_created_on' => $time,
                    'i_user_id' => $logged_id,
                    'id'=>$_ret,
                    'request_type'=>'church' ,
                     's_information'=> $content,
                    'user_email' => $email
                    
                );
		$this->db->insert($this->db->church_request, $arr); #echo $this->db->last_query();
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
                  //
                   if($status == 2)
                  {
                      //deny
                       $data = array(
               's_status' => 2,
               
            );
        $this->db->where('id', $id);
$this->db->update('cg_church_request', $data);
                  }
                  else if($status == 1)
                  {
                      //accept
                       $data = array(
               's_status' => 1,
               
            );
        $this->db->where('id', $id);
$this->db->update('cg_church_request', $data);
                  }
                  //
		  return true;
	}

       
        
        
        public function get_info_list($where='',$i_start=null,$i_limit=null,$s_order_by='1')
    {
       
        $limit  = (is_numeric($i_start) && is_numeric($i_limit))?" Limit ".intval($i_start).",".intval($i_limit):'';
        $sql  =  " SELECT CH.* , CONCAT(U.s_first_name,' ',U.s_last_name) AS s_profile_name
						FROM cg_church_request CH
						LEFT JOIN cg_users U ON U.id = CH.i_user_id
						{$where} GROUP BY CH.id 
						ORDER BY id DESC {$limit}";
    
		 $query     = $this->db->query($sql); 
        $result_arr = $query->result_array();
        return $result_arr;
    }
    
     public function get_info_list_count($where='')
    {
        
        $sql    = "     SELECT count(*) as i_total FROM
							(SELECT CH.id
							FROM cg_church_request CH
							LEFT JOIN cg_users U ON U.id = CH.i_user_id
							{$where}
							GROUP BY CH.id  ) as drvd_tbl";
					 
        $query     = $this->db->query($sql);
        $result_arr = $query->result_array();
        return $result_arr[0]['i_total'];
    }
    public function changestatus($id){
        $data = array(
               's_status' => 1,
               
            );
        $this->db->where('id', $id);
$this->db->update('cg_church_request', $data);

$data1 = array('i_disabled' => 1);
 $this->db->where('id', $id);
$this->db->update('cg_church', $data1);


$query = $this->db->get_where('cg_church_request', array('id' => $id));
$result = $query->result();
foreach ($query->result() as $row)
{
    $this->load->helper('html');
    $logo="http://cogtime.com/images/logo.png";
    $body = '<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#e9f3f5" style="font-family:Arial, Helvetica, sans-serif; font-size:13px; line-height:19px;">
  <tr>
    <td align="left" style="background:#013D62; border-bottom:5px solid #62C3BC; padding:15px 0;"><img src="'.$logo.'" alt= ""></td>
  </tr>
  <tr style="border-top:1px solid #ffffff;">
    <td style="padding-top:10px; padding-bottom:10px;">&nbsp;</td>
  </tr>
  <tr>
  	<td style="background:#62C3BC; color:#fff; font-family:Arial, Helvetica, sans-serif; font-size:22px; font-weight:bold; width:300px; margin:0px auto; display:block; padding:15px; text-align:center;">
    Church Request
    </td>
  </tr>
  <tr>
  	<td style="padding:15px;"><p>Dear User, </p><p>your church request is accepted</p><p>Thanks</p><p>admin@cogtime.com</p></tr>
  <tr>
    <td align="center" valign="middle" style="background:#A8A7A7; padding:15px;"><table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td align="left" valign="middle" style="color:#d3edfd; font-family:Arial, Helvetica, sans-serif; font-size:12px;"> <a href="http://acumencs.com/drandpt-arabic/contact-us/" style="color:#d3edfd; text-decoration:none;"></a></td>
        
        <td align="right" style="color:#525252; font-family:Arial, Helvetica, sans-serif; font-size:12px; direction:rtl;">© All Rights Reserved<span style="color:#525252;"><strong> COGTIME 2014  </strong></span></td>
      </tr>
    </table></td>
  </tr>
</table>'; 
   // $body = " ";
    
	$this->load->library('email');
       $email_setting  = array('mailtype'=>'html','charset'  => 'utf-8',
                  'priority' => '1');
$this->email->initialize($email_setting);

$this->email->from('admin@cogtime.com', 'Team Cogtime');
$this->email->to("$row->user_email");
$this->email->cc('arif.zisu@gmail.com');
//$this->email->bcc('them@their-example.com');

$this->email->subject('New Chuch accept message from Cogtime');
$this->email->message("$body");

$this->email->send();	
//echo $this->email->print_debugger();
			
//				$body = "<p>Dear $row->user_email, </p><p>your church request is accepted</p><p>Thanks</p><p>$row->user_email</p> ";
//								   
//				 $to = $row->user_email;
//            $subject = "New Chuch accept message from Cogtime";
//          $msg = $body;
//            $from ='admin@cogtime.com';
//            
//			$headers  = 'MIME-Version: 1.0' . "\r\n";
//			$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
//			
//			// Additional headers
//			$headers .= 'To: Cogtim <'.$to.'>' . "\r\n";
//			$headers .= 'From: ' . 'Cogtime' . '<' . $from . '>' . "\r\n";
//			
//			
//
//         $mail =   mail($to,$subject,$msg,$headers);
        
//var_dump($mail);
// echo $row->user_email;
}


    }
     public function deny_status($id){
        $data = array(
               's_status' => 2,
               
            );
        $this->db->where('id', $id);
$this->db->update('cg_church_request', $data);
$query = $this->db->get_where('cg_church_request', array('id' => $id));
foreach ($query->result() as $row)
{

    
    $this->load->library('email');
    $this->load->helper('html');
        $email_setting  = array('mailtype'=>'html','charset'  => 'utf-8',
                  'priority' => '1');
$this->email->initialize($email_setting);
//$body = "<p>Dear User, </p><p>your church request is denied</p><p>Thanks</p><p>admin@cogtime.com</p> ";
  $logo="http://cogtime.com/images/logo.png";
    $body = '<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#e9f3f5" style="font-family:Arial, Helvetica, sans-serif; font-size:13px; line-height:19px;">
  <tr>
    <td align="left" style="background:#013D62; border-bottom:5px solid #62C3BC; padding:15px 0;"><img src="'.$logo.'" alt= ""></td>
  </tr>
  <tr style="border-top:1px solid #ffffff;">
    <td style="padding-top:10px; padding-bottom:10px;">&nbsp;</td>
  </tr>
  <tr>
  	<td style="background:#62C3BC; color:#fff; font-family:Arial, Helvetica, sans-serif; font-size:22px; font-weight:bold; width:300px; margin:0px auto; display:block; padding:15px; text-align:center;">
    Church Request
    </td>
  </tr>
  <tr>
  	<td style="padding:15px;"><p>Dear User, </p><p>your church request is denied</p><p>Thanks</p><p>admin@cogtime.com</p></tr>
  <tr>
    <td align="center" valign="middle" style="background:#A8A7A7; padding:15px;"><table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td align="left" valign="middle" style="color:#d3edfd; font-family:Arial, Helvetica, sans-serif; font-size:12px;"> <a href="http://acumencs.com/drandpt-arabic/contact-us/" style="color:#d3edfd; text-decoration:none;"></a></td>
        
        <td align="right" style="color:#525252; font-family:Arial, Helvetica, sans-serif; font-size:12px; direction:rtl;">© All Rights Reserved<span style="color:#525252;"><strong> COGTIME 2014  </strong></span></td>
      </tr>
    </table></td>
  </tr>
</table>'; 
$this->email->from('admin@cogtime.com', 'Team Cogtime');
$this->email->to("$row->user_email");
$this->email->cc('arif.zisu@gmail.com');
//$this->email->bcc('them@their-example.com');

$this->email->subject('New Chuch deny message from Cogtime');
$this->email->message("$body");

$this->email->send();	
				
//			
//				$body = "<p>Dear $row->user_email, </p><p>your church request is denied</p><p>Thanks</p><p>$row->user_email</p> ";
//								   
//				$to = $row->user_email;
//            $subject = "New Chuch denied message from Cogtime";
//            $msg = $body;
//            $from ='admin@cogtime.com';
//            
//			$headers  = 'MIME-Version: 1.0' . "\r\n";
//			$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
//			
//			// Additional headers
//			$headers .= 'To: Cogtim <'.$to.'>' . "\r\n";
//			$headers .= 'From: ' . 'Cogtime' . '<' . $from . '>' . "\r\n";
//			
//			
//
//            mail($to,$subject,$msg,$headers);

// echo $row->user_email;
}
//$data1 = array('i_disabled' => 1);
// $this->db->where('id', $id);
//$this->db->update('cg_church', $data1);



    }
    
    
}
