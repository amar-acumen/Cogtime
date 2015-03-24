<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/*********
* Author: 
* Date  : 
* Modified By:
* Modified Date: 
* Purpose:
* Custom Controller 
* 
* 
*/

/* to implement interface */
include_once INFPATH."InfController.php";


class Admin_base_controller extends Base_Controller
{
    
    protected $language;
    //protected $data = array();

    public $translation_container = null;
    
    public function __construct () {

        try
        {
                parent::__construct();
        
                $this->_set_multilanguage_db();
                $this->_set_timezone();
        
                $this->load->helper('common_helper');
        
                if( $this->session->userdata('loggedin')!='' ) {
                    /*
                    set session variables to member variables. so one can not need to call
                    session->userdata() function whenever these common variables are needed
                    */
                    $this->_set_session_variables();
        
                    $this->data['loggedin'] = true;
					$this->data['_is_admin'] = true;
                    
                }
				if($this->session->userdata('is_admin') == 2){
        			$this->generate_subadmin_menu();        
				}
                /******************************** Layout based codes *********************** */
                    $this->js_files = array();
                    $this->css_files = array();
                    $this->header_html = array();
                    $this->title = '';
                    $this->meta_desc = '';
                    $this->layout = 'layouts/admin/admin_layout.phtml';
                /*************************************************************************** */
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        } 
    }


 	  public function _non_accessible_by_admin_logged() 
        { 
           try
             {
                  if( $this->session->userdata('loggedin')  && 
				  	$this->session->userdata('is_admin') != '')
                   {
                        header('location:'.admin_base_url().'dashboard.html');exit;
                   } 
				 
                }
            catch(Exception $err_obj)
            {
                
            } 
        }


    /****
    * put your comment there...
    * 
    * @param array $files
    */
    
    
    // When _render() is used it releases the output as there is flush() call in the layout fle views/layout.php
    // so to get a string return one has to change layout file using _set_layout(). 
    // flush() call makes js and css parallel download possible so page loads faster.
    protected function _render($data = array(), $view_script='', $ajax_call = false, $bool_string = false)
    {
            try{
                # $data = array_merge($data, $this->data);  //// commented...
                
                if($view_script == '') {
                    if($this->router->directory=='') {
                       $view_script = $this->router->class.'.phtml';
                    }
                    else {
                        $view_script = $this->router->directory.'/'.$this->router->class.'.phtml';
                    }
                }

                if( !$ajax_call )   {

                    $matches_view = array();
                    preg_match('/^(.*?)(\.[^\.]+)?$/', $view_script, $matches_view);
                    /*$js_script =  base64_encode("backend/".$matches_view[1].'.js');
                    $css_script = base64_encode("backend/".$matches_view[1].'.css');*/
                    $js_script =  base64_encode($matches_view[1].'.js');
                    $css_script = base64_encode($matches_view[1].'.css');
                    
                    $this->header_html = array_unique($this->header_html);

                    $data['header']['header_html'] = '';
                    $data['header']['footer_html'] = '';

                    $data['header']['header_html'] .= '<title>'.$this->title.'</title>'."\n";

                    if( $this->meta_desc != '' ) {
                            $data['header']['header_html'] .= '<meta name="description" content="'.$this->meta_desc.'" />'."\n";
                    }

                    if( is_array($this->js_files) && count($this->js_files) ) {
                            foreach($this->js_files as $js_file=>$position) {
                                if($position == 'footer') {
                                    $data['header']['footer_html'] .= '<script type="text/javascript" src="'.base_url().$js_file.'"></script>'."\n";
                                }
                                else {
                                    $data['header']['header_html'] .= '<script type="text/javascript" src="'.base_url().$js_file.'"></script>'."\n";
                                }
                            }
                    }

                    // Inline js will be written in a file with the same name as view file
                    // and will be parsed by controller parse, action js
                    $data['header']['header_html'] .= '<script type="text/javascript" src="'.base_url().'parse/js/'.$js_script.'"></script>'."\n";

                    if( is_array($this->css_files) && count($this->css_files) ) {
                        foreach($this->css_files as $key_css=>$item_css) {
                            if( is_array($item_css) && count($item_css) ) {
                                $attr = '';
                                foreach($item_css as $key_attr=>$attr_value) {
                                    $attr .= ' '.$key_attr.'="'.$attr_value.'"';
                                }
                                $data['header']['header_html'] .= '<link href="'.base_url().$key_css.'" rel="stylesheet" type="text/css"'.$attr.' />'."\n";
                            }
                            else {
                                $data['header']['header_html'] .= '<link href="'.base_url().$key_css.'" rel="stylesheet" type="text/css" />'."\n";
                            }
                        }
                    }
                    
                    // Inline css will be written in a file with the same name as view file
                    // and will be parsed by controller parse, action css.
                    $data['header']['header_html'] .= '<link href="'.base_url().'parse/css/'.$css_script.'" rel="stylesheet" type="text/css" />'."\n";


                    if( is_array($this->header_html) && count($this->header_html) ) {
                            foreach($this->header_html as $header_html) {
                                    $data['header']['header_html'] .= $header_html."\n";
                            }
                    }
                    
                    

                }   // end of AJAX call check...

        
        
        # LOADING THE ADMIN HEADER & FOOTER HTML PART [START]...
            $header_html_script = "layouts/admin/admin_header.phtml";
			$WHERE = " WHERE i_isdeleted = 1 ";
				$data['total_user_main']  = $this->users_model->user_list_count($WHERE);
				//pr($data['total_user'],1);
				$data['total_online_user_main']  = $this->users_model->online_user_list_count();
            $data['header']['header_part'] = $this->load->view($header_html_script, $data, true);
            
            $footer_html_script = "layouts/admin/admin_footer.phtml";
            $data['header']['footer_part'] = $this->load->view($footer_html_script, $data, true);
        # LOADING THE ADMIN HEADER & FOOTER HTML PART [END]...
        
        $data['content'] = $this->load->view($view_script, $data, true);

        if(!$bool_string) {

                    if( !$ajax_call )
                        $this->load->view($this->layout, $data);
                    else
                        $this->load->view($view_script, $data);

        }
        else {

                    if( !$ajax_call )
                        return $this->load->view($this->layout, $data);
                    else
                        return $this->load->view($view_script, $data);
                    
            #return $this->load->view($this->layout, $data, true);
        }
    }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        } 
    }
    
	
	
	####CHECK ADMIN LOGIN 
    
    public function _check_admin_login() 
    {
		 
        
        try{
           		
				if($this->session->userdata('loggedin')=='' || $this->session->userdata('loggedin')==false || ($this->session->userdata('is_admin')!=1 && $this->session->userdata('is_admin')!=2)) 
				{
					
					$url = my_current_url();
		
					$this->session->set_userdata('session_admin_referrer', $url);
					 echo "<script>window.location='".base_url()."admin/index.html'+window.location.hash</script>";
                     exit;
				}		
				elseif( $this->session->userdata('is_admin') == 2){
					
					//echo 111;
					$this->check_SubAdmin_login();
				}
				else 
				{
					$this->session->unset_userdata('session_referrer');
				}
			 
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        } 
    }
    
   
   ### added to divert admin logged user to dashboard 
   
   
   ### check sub admin access
   
    public function check_SubAdmin_login() 
    {

        try{
			
			### fetch access available for loggedd user
			$this->load->model('admin_groups_model');
			$this->load->model('admins_user_model');
			
			$adminID = intval(decrypt($this->session->userdata('user_id')));
			$admin_detail = $this->admins_user_model->get_admin_by_id($adminID);
			
			$priviledge_arr  = $this->admin_groups_model->getPriviledges_by_Groupid($admin_detail[0]['i_id_admin_user_group']);
			
			## get total priviledges available
			
			$all_priviledges_arr  = $this->admin_groups_model->getAllPriviledges();
			
			//pr($all_priviledges_arr);
			
			//echo $admin_detail[0]['i_id_admin_user_group'];
			//pr($priviledge_arr);
            
			#### get current url
			$full_name = basename($_SERVER['REQUEST_URI']);
			$full_name = explode('.',$full_name);
            $page_name = $full_name[0]; 
			
			if($page_name == 'dashboard'){
				header('location:'.admin_base_url().'dashboard.html');
                exit;
			}
			else if($this->session->userdata('loggedin') && $this->session->userdata('is_admin')== 2  
						&& !in_array($page_name, $all_priviledges_arr)){
						 $this->session->unset_userdata('session_referrer');	
			}
			else if($this->session->userdata('loggedin') && $this->session->userdata('is_admin')== 2  
						&& !in_array($page_name , $priviledge_arr) )
            {
                $url = my_current_url();
    
                $this->session->set_userdata('session_admin_referrer', $url);
				header('location:'.admin_base_url().'dashboard.html');exit;
                exit;
            }
            else 
            {
                    $this->session->unset_userdata('session_referrer');
            }
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        } 
    }
    
	
	#### check login status 
	public function checkGlobalAdminLogin() 
    {
		 
        try{
           
            if($this->session->userdata('loggedin')=='' || $this->session->userdata('loggedin')==false || $this->session->userdata('is_admin') == ''  )
            {
                
                $url = my_current_url();
    
                $this->session->set_userdata('session_admin_referrer', $url);
                echo "<script>window.location='".base_url()."admin/index.html'+window.location.hash</script>";
                exit;
            }
            else 
               {
                    $this->session->unset_userdata('session_referrer');
               }
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        } 
    }
   
   ###GENERATE SUBMENU ACCORD TO PRIVILEDGES ASSIGNED
   
   public function generate_subadmin_menu(){
	   
	    $this->load->model('admin_groups_model');
		$this->load->model('admins_user_model');
		
		
	    $adminID = intval(decrypt($this->session->userdata('user_id')));
		$admin_detail = $this->admins_user_model->get_admin_by_id($adminID);
			
		$priviledge_arr  = $this->admin_groups_model->getAllPriviledgesGroupid($admin_detail[0]['i_id_admin_user_group']);
		#echo $admin_detail[0]['i_id_admin_user_group'];
		#pr($priviledge_arr,1);
		
		$priv_str = implode(', ',$priviledge_arr);
		
		#### get submenu arr by priviledge id ####
		$where = " AND M.i_prviledge_id in ( '".$priv_str."')";
		$menu_arr = $this->admin_groups_model->getMenuDetail($where);
		
		//pr($menu_arr,1);
		
		$menu_html = '<ul>';
		if(count($menu_arr)){
			
			$i=0;
			foreach($menu_arr as $k=>$val)
			{
			
			 ### forming main menu part 
			 $full_name = basename($_SERVER['REQUEST_URI']);
			 $full_name = explode('.',$full_name);
			 $page_name = $full_name[0]; 
			  
			 $CSS =  getSelectedPageCSS($page_name,$k);
			 
			 #['s_keyword'])?'class="select"':''
			 $fix_css = ($k == 7)?'noMargin':'';
			 $menu_html .= '<li class="'.$fix_css.' '.$CSS.'" ><a href="javascript:void(0);">'.$val['s_parent_menu_name'].'</a>';
					
			 $sub_menu_arr = array();
			    if(count($val)){
				  $menu_html .= '<ul>';	
				   foreach( $val as $index=> $value){
			    		
						if(is_numeric($index) && $value['i_sec_sub_menu_id'] == 0){
							
							 $full_name = basename($_SERVER['REQUEST_URI']);
							 $full_name = explode('.',$full_name);
							 $page_name = $full_name[0]; 
							  
							 $CSS_sub =  ($page_name == $value['s_keyword'])?'class="select"':'';
							 //$sub_menu_arr[$index] = $value;		
					   		 $menu_html .= '<li '.$CSS_sub.'><a href="'.admin_base_url().$value['s_keyword'].'.html">'.$value['s_sub_menu_name'].'</a></li>';
						 }
                  
				   }
				   $menu_html .= '</ul>';
				 }
				  
				
				//pr($sub_menu_arr);
				 $menu_html .= '</li>';
			}
			 
		}
	   
	   $menu_html .= '</ul>'; 
	   $this->data['sub_admin_menu_html'] =  $menu_html;
	 
   }
   function afterautolog(){
       if($this->session->userdata('is_admin') == 0 && $this->session->userdata('user_id') != ''){
           echo "<script>window.location='".base_url()."'+window.location.hash</script>";
                exit; 
       }
       
   }
   
    
}

