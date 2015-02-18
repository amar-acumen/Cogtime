<?php
/*********
* Author: 
* Purpose:
*  Controller For "advertisements"
* 
* @package 
* @subpackage 
* 
* @link InfController.php 
* @link Base_Controller.php
* @link model/ring_categories_model.php
* @link views/##
*/

class Word_for_today extends Admin_base_Controller
{
   
   private $word_pagination_per_page =  20 ;
    
   
    // constructor definition...
    function __construct()
    {
        try
        {
            parent::__construct();
            parent::_check_admin_login();
            
            # configuring paths...
                        
            # loading reqired model & helpers...
            // $this->load->helper('###');
           //$this->load->model("ring_categories_model");
           
           $this->logged_admin_id = $this->session->userdata('loggedin');
            
            
           $this->load->model("landing_page_cms_model");
           $this->load->helper('common_option_helper.php');
           
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }  

    }

    // "index" function definition...
    public function index() 
    {

        try
        {
            
            # adjusting header & footer sections [Start]...
            $data = $this->data;
            parent::_set_title("::: COGTIME Xtian network :::");
            parent::_set_meta_desc("::: COGTIME Xtian network :::");
            parent::_set_meta_keywords("::: COGTIME Xtian network :::");
            parent::_add_js_arr( array( 'js/lightbox.js',
                                        'js/jquery.dd.js',
                                        'js/jquery.form.js',
                                        'js/jquery/JSON/json2.js'
                                        ) );
                                        
             parent::_add_css_arr( array('css/dd.css'
                                        ) );
            # adjusting header & footer sections [End]...
            $data['top_menu_selected'] = 5;
            $data['submenu'] = 2;
            
            
            

            
            ### ajax call ###
            ob_start();
            $this->word_listing_AJAX();
            $data['content'] = ob_get_contents();
            ob_end_clean();
            ### end ajax call ###
            

            
            
            # rendering the view file...
            $VIEW_FILE = "admin/media_center/word_for_today.phtml";
            parent::_render($data, $VIEW_FILE);
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }
    }//end of index
    
    
    public function word_listing_AJAX($page=0) 
    {
         try
         {
            $data = $this->data; 
            $where    = "" ;
            
            if($_POST)
            {
                $title = $this->input->post('title');
                if($title!='')
                {
                    //$where = " WHERE w.s_title LIKE '%{$title}%'";
                    $where = "WHERE MATCH (w.s_title) AGAINST ('{$title}')";
                }
            }
            
            
            
            $result = $this->landing_page_cms_model->get_word_for_today_list($where ,$page,
                                                                $this->word_pagination_per_page);
            
            $resultCount = count($result);
            $total_rows = $this->landing_page_cms_model->get_total_word($where);
            
            if(count($result)==0 && $total_rows)
            {
                $page = $page-$this->word_pagination_per_page;
                $result = $this->landing_page_cms_model->get_word_for_today_list($where ,$page,
                                                                $this->word_pagination_per_page);
                
            }
    
    //pr($result);         
    //echo "total : ".$total_rows;exit;

    
            $this->load->library('jquery_pagination');
            $config['base_url'] = base_url()."admin/media_center/word_for_today/word_listing_AJAX";
            $config['total_rows'] = $total_rows;
            $config['per_page'] = $this->word_pagination_per_page;
            $config['uri_segment'] = 5;
            $config['num_links'] = 9;
            $config['page_query_string'] = false;
            
            $config['prev_link'] = '&laquo; Previous';
            $config['next_link'] = 'Next &raquo;';

            $config['cur_tag_open'] = '<li> <span><a href="javascript:void(0)" class="select">';
            $config['cur_tag_close'] = '</a></span></li>';

            $config['next_tag_open'] = '<li><a href="javascript:void(0)">';
            $config['next_tag_close'] = '</a></li>';

            $config['prev_tag_open'] = '<li><a href="javascript:void(0)">';
            $config['prev_tag_close'] = '</a></li>';

            $config['num_tag_open'] = '<li>';
            $config['num_tag_close'] = '</li>';
            
            

            $config['div'] = '#table_contents'; /* Here #content is the CSS selector for target DIV */
            #$config['js_bind'] = "showBusyScreen(); "; /* if you want to bind extra js code */
            #$config['js_rebind'] = "hideBusyScreen(); "; /* if you want to rebind extra js code */


            $this->jquery_pagination->initialize($config);
            $data['page_links'] = $this->jquery_pagination->create_links();

            
            $data['result_arr'] = $result;
            $data['no_of_result'] = $total_rows;
            $data['current_page'] = $page;
            $data['total_pages'] = ceil($total_rows/$this->word_pagination_per_page);
          
             $p = ($page/$this->word_pagination_per_page);
             $data['current_loaded_page_no'] =  $p + 1;
            
            # rendering the view file...
            $VIEW_FILE = "admin/media_center/word_for_today_ajax.phtml";
            echo $this->load->view($VIEW_FILE, $data,true);
            //return $html;
            
        } 
        catch(Exception $err_obj)
        {
            
        } 
    
    }
    
    
    //------------------------------------ add new word ---------------------------------------------
    function post_add_data()
    {
        
        $error = 0;
        $sys_error=0;
        $info=array();
        $key=array();
        $info['dt_posted_on'] = get_db_datetime();
        
            $info['i_posted_by'] = $this->logged_admin_id;
            $info['s_title'] = $this->input->post('txt_add_title');
            $info['s_desc'] = $this->input->post('txtarea_add_desc');
            if($info['s_title']=='')
            {
                $arr_messages['add_title']='* Required field';
                
                $error = 1;
            }
            if($info['s_desc']=='')
            {
                $arr_messages['add_desc'] = '* Required field';
                $error=1;
            }
            
           
            
            
            if($error!=1)  
            {
                $res = $this->landing_page_cms_model->add_new_word($info);
				 $host = "127.0.0.1";
				$port = 51127;
				$apiCommand = '<?xml version="1.0" encoding="UTF-8"?><Command group="default" api_pwd="3874-3459-9293-2194" type="add_room" name="'.$info['s_title'].'" desc="'.$info['s_desc'].'" max="100" audio="false" video="false" passallmessage="false" ><audio enable="0" /></Command>';
				$result = "";
				$resultDoc = "";
				$fp = @fsockopen($host, $port, $errno, $errstr, 2);
				if(!$fp)
				{
					echo json_encode( array('success'=>false,'arr_messages'=>$arr_messages,'msg'=>'Failed to excute api command,maybe host chat server is not started!') );
				}
				else
				{
					fputs($fp,$apiCommand."\0");
					while (!feof($fp)) 
					{
							$resultDoc .= fgets($fp, 1024);
							$resultDoc = rtrim($resultDoc);
					}
					$parser = xml_parser_create("UTF-8");
					xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
					xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
					if (!xml_parse_into_struct($parser, $resultDoc, $values, $tags))
					{
						printf("XML error: %s at line %d while parsing entity n",
							xml_error_string(xml_get_error_code($parser)),
							xml_get_current_line_number($parser));
						echo "xml parse error";
					}
					else
					{
						//print_r($values);
						xml_parser_free($parser);
						fclose($fp);
						$room_id = $values[0]['attributes']['result'];
						
						$wrd_arr = array();
						$wrd_arr['i_room_id'] = $room_id;
						
						$this->landing_page_cms_model->update_word_info($wrd_arr , $res);
					}
				}
				
				
                if(!$res)
                {
                    $sys_error=1;
                }
            }
            
            else
            {
                $result = 'failure';
                $msg    = 'Field(s) can not be blank!';
                echo json_encode(array('result'=>$result,'msg'=>$msg,'arr_messages'=>$arr_messages));
                exit;
            }        
            if($sys_error==1)
            {
                $result = 'failure';
                $msg    = 'Error occured! Try again.';
                echo json_encode(array('result'=>$result,'msg'=>$msg));
                exit;
            }    
            
            $result = 'success';
            $msg = 'Word added successfully.';
            
             ### ajax call ###
            ob_start();
            $this->word_listing_AJAX();
            $html = ob_get_contents();
            ob_end_clean();
            ### end ajax call ###
            //$html = $this->word_listing_AJAX();

        echo json_encode(array('result'=>$result,'msg'=>$msg,'html'=>$html));
    }
    //------------------------------------ end add new word ---------------------------------------------
    
    
    
    //------------------------------------ edit new word ---------------------------------------------
    function fetch_word_info()
    {
        $word_id = $this->input->post('id');
        $res = $this->landing_page_cms_model->get_word_info_by_word_id($word_id);
        $title = $res['s_title'];
        $desc = $res['s_desc'];
        echo json_encode(array('title'=>$title,'desc'=>$desc));
    }    
        
        
    function post_edit_data()
    {
        
        $error = 0;
        $sys_error=0;
        $info=array();
        $key=array();
        $info['dt_updated_on'] = get_db_datetime();
        
        $word_id = $this->input->post('i_edit_word_id');
        $current_page = $this->input->post('i_current_page');
        
            //$info['i_posted_by'] = $this->logged_admin_id;
            $info['s_title'] = $this->input->post('txt_edit_title');
            $info['s_desc'] = $this->input->post('txtarea_edit_desc');
            if($info['s_title']=='')
            {
                $arr_messages['edit_title']='* Required field';
                
                $error = 1;
            }
            if($info['s_desc']=='')
            {
                $arr_messages['edit_desc'] = '* Required field';
                $error=1;
            }
            
           
            
            
            if($error!=1)  
            {
                $this->landing_page_cms_model->update_word_info($info,$word_id);
               
            }
            
            else
            {
                $result = 'failure';
                $msg    = 'Field(s) can not be blank!';
                echo json_encode(array('result'=>$result,'msg'=>$msg,'arr_messages'=>$arr_messages));
                exit;
            }        
           
            
            $result = 'success';
            $msg = 'Word updated successfully.';
            
            ### ajax call ###
            ob_start();
            $this->word_listing_AJAX($current_page);
            $html = ob_get_contents();
            ob_end_clean();
            ### end ajax call ###
            //$html = $this->word_listing_AJAX();

        echo json_encode(array('result'=>$result,
                                'msg'=>$msg,
                                'html'=>$html,
                                'word_id'=>$word_id));
    }
    //------------------------------------ end add new word ---------------------------------------------
    
    
    //--------------------------------------- delete word ----------------------------------------------
    function delete_word()
    {
        $id = $this->input->post('word_id');
        $current_page = $this->input->post('current_page');
        $this->landing_page_cms_model->delete_word_by_word_id($id);
        
        ### ajax call ###
        ob_start();
        $this->word_listing_AJAX($current_page);
        $html = ob_get_contents();
        ob_end_clean();
        ### end ajax call ###
        
        
        echo json_encode(array('success'=>true,
                                'msg'=>'The word deleted successfully.',
                                'html'=>$html
                                ));
    }
    //--------------------------------------- end of delete word ----------------------------------------------
    
    
}// end of controller