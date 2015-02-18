<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*********
* Author:
* 
* Purpose:
*  Controller For HOME Page 
* 
* @package 
* @subpackage 
* 
* @link InfController.php 
* @link Base_Controller.php
*/

include(APPPATH.'controllers/base_controller.php');

class All extends Base_controller
{
	
    public function __construct()
     {
        try
        {
            parent::__construct();
        
		}
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }  

    }

    public function index() 
    {
	}
	
	
	public function accept_invitation($uid,$ringid,$msgbox='')
	 {
	 	$uid	= intval(decrypt($uid));
		$ringid	= decrypt($ringid);
		$where = array('i_ring_id' => $ringid,
    					'i_invited_id' => $uid
						);
		$arr['i_joined']	= 1;
		$arr['dt_joined_date']	= get_db_datetime();
		$this->load->model('my_ring_model');
		 $member_arr = getMembers_ring_bYID($ringid);
                $membar = count($member_arr);
                  $MAX_RING_MEMBER  =  $this->data['site_settings_arr']['i_max_ring_member'];
                // die('ok');
                if($membar >= $MAX_RING_MEMBER && $MAX_RING_MEMBER != 0){
                  echo json_encode(array('msg'=>"error"));
			exit;
                }
                else{
                    
                   // die('uff');
		$msgarr	= array('s_type'=>'ring_join_request', 'i_referred_media_id'=>$ringid,'i_receiver_id'=>$uid);
		
		$this->my_ring_model->accept_invitation($where,$arr,1,$msgarr);
		
		if($msgbox==1)
		{
			echo json_encode(array('msg'=>"You have successfully joined"));
			exit;
		}
		else
		{
			if($this->session->userdata('user_id')=='')
				header('Location:'.base_url());
			else
				header('Location:'.base_url().'my-ring.html');
			exit;
		}
                }
	 }  
	 
	 
	 public function decline_ring_invitation($uid,$ringid)
	 {
	 	$uid	= intval(decrypt($uid));
		$ringid	= decrypt($ringid);
		
        $res = $this->my_ring_model->leave_ring($ringid);
		$msgarr	= array('s_type'=>'ring_join_request', 'i_referred_media_id'=>$ringid,'i_receiver_id'=>$uid);
		
		$this->my_ring_model->accept_invitation($where,$arr,1,$msgarr);
		echo json_encode(array('msg'=>"You have successfully declined"));
		exit;
	 }  
	 public function resend_invitation($u_id,$i_id,$id,$mode)
	 {
		 if($mode=='ring')
		 {
			 $msg_id=$this->social_notifications_message($u_id, $i_id,'ring_join_request',$id);
		 }
		 
			echo json_encode(array('msg'=>"Invitation sent successfully")); 
		 
	 }
	
}