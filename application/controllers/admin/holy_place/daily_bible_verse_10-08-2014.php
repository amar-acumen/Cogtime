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

class Daily_bible_verse extends Admin_base_Controller
{
 private $pagination_per_page=10;
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
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }  

    }


    public function index($generate =1,$page_no=0) {
		
	 	
		$data = $this->data;
		parent::_set_title("::: COGTIME Xtian network :::");
		parent::_set_meta_desc("::: COGTIME Xtian network :::");
		parent::_set_meta_keywords("::: COGTIME Xtian network :::");
		parent::_add_js_arr( array( 'js/lightbox.js',
									'js/jquery.dd.js',
									'js/jquery.form.js',
									'js/jquery/JSON/json2.js') );
									
		 parent::_add_css_arr( array('css/dd.css'
									) );
		# adjusting header & footer sections [End]...
		$data['top_menu_selected'] = 7;
		$data['submenu'] = 9;
		$data['show_publish'] =  false;
		$page=0;
		 //---------------------- for pagination back ---------------------
            if($page_no!=0)
                $page=($page_no-1)*2;
        //---------------------- end pagination back ---------------------
		
		 ob_start();
		$this->ajax_pagination($page);
		$data['result_content'] = ob_get_contents();
		ob_end_clean();
		
			$this->load->model('holy_place_model');
			$rand_bible_verse = $this->holy_place_model->getDayverse();
			
			$rand_bible_verse1 = array();
			$rand_bible_verse1['s_book_name'] = $rand_bible_verse['s_book_name'];
			$rand_bible_verse1['s_chapter'] = $rand_bible_verse['bible_chapter_no'];
			$rand_bible_verse1['i_verses']	= $rand_bible_verse['bible_verse_no'];
			$rand_bible_verse1['s_text']	= $rand_bible_verse['s_verse'];
			
			
			$data['published_rand_bible_verse'] = $rand_bible_verse1;
		
		
		//pr($data['rand_bible_verse']); 
		$VIEW_FILE = "admin/holy_place/verse.phtml";
        parent::_render($data, $VIEW_FILE);
		
	}
	
	    public function ajax_pagination($page=0)
    {
	
            $this->load->model('holy_place_model');
            $order_by = " id ASC";
            $result = $this->holy_place_model->daily_verse_list($page,$this->pagination_per_page,$order_by);
			//pr($result,1);
            $resultCount = count($result);
            
            $total_rows = '30';
         
            //echo $total_rows;exit;
            //echo $this->db->last_query(); exit;
            //echo "total : ".$total_rows;exit;
            ## end seacrh conditions : filter ############
            
            //pr($result,1);
            #Jquery Pagination Starts
            $this->load->library('jquery_pagination');
            $config['base_url'] = base_url()."admin/holy_place/daily_bible_verse/ajax_pagination";
            $config['total_rows'] = $total_rows;
            $config['per_page'] = $this->pagination_per_page;
            $config['uri_segment'] = 5;
            $config['num_links'] = 9;
            $config['page_query_string'] = false;
            $config['prev_link'] = 'PREV';
            $config['next_link'] = 'NEXT';

            $config['cur_tag_open'] = '<li> <span><a href="javascript:void(0)" class="select">';
            $config['cur_tag_close'] = '</a></span></li>';

            $config['next_tag_open'] = '<li><a href="javascript:void(0)">';
            $config['next_tag_close'] = '</a></li>';

            $config['prev_tag_open'] = '<li><a href="javascript:void(0)">';
            $config['prev_tag_close'] = '</a></li>';

            $config['num_tag_open'] = '<li>';
            $config['num_tag_close'] = '</li>';

            $config['div'] = '#table_content'; /* Here #content is the CSS selector for target DIV */
            $config['js_bind'] = "showBusyScreen(); "; /* if you want to bind extra js code */
            $config['js_rebind'] = "hideBusyScreen(); "; /* if you want to rebind extra js code */

            $this->jquery_pagination->initialize($config);
            $data['page_links'] = $this->jquery_pagination->create_links();
            $this->jquery_pagination->create_links();

            // getting   listing...
            $data['info_arr'] = $result;
            $data['no_of_result'] = $total_rows;
            $data['current_page'] = $page;
           $data['pagination_per_page']   =    $this->pagination_per_page;
            # loading the view-part...
          echo  $this->load->view('admin/holy_place/verse_ajax.phtml', $data,TRUE);
        
        
    }
	public function add(){
		
			## verse for a day for all user will remain same/ changed.
			//$rand_verse_id = rand(1, 31102);
			//$s_verse_where = " AND v.id =  {$rand_verse_id}"; 
			
			$sql = "SELECT *,v.id AS verseid FROM 
					{$this->db->BIBLE_BOOK} AS b, 
					{$this->db->BIBLE_CHAPTER} AS c ,
					{$this->db->BIBLE_VERSES} AS v 
					WHERE b.id=c.i_book_id AND c.id=v.i_chapter_id order by RAND() Limit 30";
					//echo $sql;
			
			$result_arr = $this->db->query($sql)->result_array();
			//pr($result_arr,1);
			$SQL = "TRUNCATE {$this->db->day_verse}";
			$this->db->query($SQL);
			foreach($result_arr as $val)
			{
			$arr = array();
			$arr['s_verse']   = $val['s_text'];
			$arr['s_book_name'] = $val['s_book_name'];
			$arr['bible_chapter_no'] = $val['s_chapter'];
			$arr['bible_verse_no'] = $val['i_verses'];
			$this->db->insert($this->db->day_verse, $arr); 
			}	
		$sql=$this->db->query("update {$this->db->day_verse} set publish_status=true where id=1");
		echo json_encode(array('success'=> true));
	}
    
  
}