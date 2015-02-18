<?php
/*********
* Author: 
* Date  : 
* Modified By: 
* Modified Date:
* 
* Purpose:
* Controller For WALL
* 
* 
*/
include(APPPATH.'controllers/base_controller.php');


class Church_wall_about extends Base_controller
{
    
//    private $pagination_per_page =  10;
//    private $comments_pagination_per_page =  2 ;
//    private $people_liked_pagination_per_page =  4 ;
//   
    
    public function __construct()
     {
	 	
        try
        {
            parent::__construct();
            parent::check_login(TRUE, '', array('1')); // put this code on those pages which are not accessable by guest user
            parent::_add_church_css_arr (array('css/church.css'));
            $this->load->model('cms_model');
			$this->load->model('landing_page_cms_model');
            $this->load->model('church_new_model');
			$this->load->model('users_model');
			////////////Managing Validators/////////
			$this->load->library('form_validation');
			$this->form_validation->set_error_delimiters('<div id="err_msg" class="error_massage">', '</div>');
			$this->form_validation->set_message('required', 'Please provide %s.');
			////////////end Managing Validators/////////
			$this->i_profile_id = intval(decrypt($this->session->userdata('user_id')));
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }  

    }

    
    public function index($c_id) 
    {
       //echo $c_id;
        //die('comming soon.........');
        try
        {
        	$user_id = intval(decrypt($this->session->userdata('user_id')));
            $posted=array();
            $this->data["posted"]=$posted;/*don't change*/    
            $data = $this->data;      
            parent::_set_title('::: COGTIME Xtian network :::');
            parent::_set_meta_desc('');
            parent::_set_meta_keywords('');
            $data['church_arr'] =$this->church_new_model->get_church_info($c_id);
			
			$VIEW = "logged/church/church_wall_about.phtml";
			parent::_render($data, $VIEW);
 
            
        }
        
        catch(Exception $err_obj)
        {
           
        } 

    } // end of index   
	
	 public function edit() 
    {
        //die('comming soon.........');
        try
        {
			parent::_add_js_arr( array( 
										'js/jquery.dd.js',
										'js/lightbox.js',
										'js/jquery.form.js',
										 'js/jquery/JSON/json2.js',
										//editor/tiny_mce.js',
										///'editor/tiny_mce_editor.js',
										// TINYMCE CALL...
										  'tiny_mce/plugins/tinybrowser/tb_tinymce.js.php',
										  'tiny_mce/tiny_mce.js') );
                                        
            parent::_add_css_arr( array('css/dd.css') );   
            //$data = $this->data;      
            parent::_set_title('::: COGTIME Xtian network :::');
            parent::_set_meta_desc('');
            parent::_set_meta_keywords('');
            
			$c_id = $this->uri->rsegment(3);
			//$user_id = intval(decrypt($this->session->userdata('user_id')));
			$info=$this->church_new_model->get_church_info($c_id);
			//pr($info[0]);
				
			$posted=array();
			$posted["id"]= trim($info[0]->id);
			$posted["s_title"]= trim($info[0]->s_title);
			$posted["s_contents"]= trim($info[0]->s_contents);
            
            $data["posted"]=$posted;/*don't change*/ 
			//pr($data);
			
			$VIEW = "logged/church/edit_church_about_info.phtml";
			parent::_render($data, $VIEW);
		}
	catch(Exception $err_obj)
        {
           
        } 

    } 	
	
	 public function save_church_about_info() 
    {
        //die('comming soon.........');
        try
        {
		
		//Submitted Form
            if(trim($this->input->post("is_submitted",true))=='Y')
            {
				$posted=array();
				$c_id = $this->input->post("c_id");
				$posted["c_id"] = $c_id;
                $posted["s_title"]= get_formatted_string($this->input->post("txt_title"));
				$posted["s_contents"]= get_formatted_string($this->input->post("elm1"));
				
				$arr_messages = array();
			  
				if(trim($this->input->post('txt_title'))=='') 
				{		
						$arr_messages['title'] = "* Required Field";
				}	
				
				if($posted["s_contents"]=='') 
				{		
						$arr_messages['elm1'] = "* Required Field";
				}
				if( count($arr_messages)==0 ) 
				 {
						
						//adding to database
						$info=array();
						$info["s_title"]= trim($posted["s_title"]);
						$info["s_contents"]= trim($posted["s_contents"]);
						
						//pr($info,1);
						$i_newid = $this->church_new_model->update_church_about_info($info, $c_id);
						//$this->db->update('cg_church', $info, array('id'=>$c_id));
						//echo $this->db->last_query(); exit;
						$re_page = base_url()."".$c_id."/church_wall_about";
						echo json_encode( array('success'=>true,'redirect'=>$re_page,'arr_messages'=>$arr_messages,'msg'=>'Church info has been updated successfully.') );
						exit;
				 }
				  else
				  {
					  echo json_encode( array('success'=>false, 'arr_messages'=>$arr_messages,'msg'=>'Error!') );
				  }
				
			}// End Submitted Form
				
		}
        
        catch(Exception $err_obj)
        {
           
        } 

    } // end of edit
    
	
}   // end of controller...

