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


    public function index($generate = 1) {
		
	 	
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
		if($generate == 1){
			## verse for a day for all user will remain same/ changed.
			$rand_verse_id = rand(1, 31102);
			$s_verse_where = " AND v.id =  {$rand_verse_id}"; 
			
			$sql = "SELECT *,v.id AS verseid FROM 
					{$this->db->BIBLE_BOOK} AS b, 
					{$this->db->BIBLE_CHAPTER} AS c ,
					{$this->db->BIBLE_VERSES} AS v 
					WHERE b.id=c.i_book_id AND c.id=v.i_chapter_id {$s_verse_where} ";
			
			$result_arr = $this->db->query($sql)->result_array();
			
			$data['rand_bible_verse'] = $result_arr[0];
			$data['show_publish'] = true;
		}
		
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
	
	
	public function add(){
		
		$SQL = "TRUNCATE {$this->db->day_verse}";
		$this->db->query($SQL);
		
		$arr = array();
		$arr['s_verse']   = $_POST['s_text'];
		$arr['s_book_name'] = $_POST['s_book_name'];
		$arr['bible_chapter_no'] = $_POST['s_chapter'];
		$arr['bible_verse_no'] = $_POST['i_verses'];
		$arr['dt_created_on'] = get_db_datetime();
				
		$this->db->insert($this->db->day_verse, $arr); 
		echo json_encode(array('success'=> true));
	}
    
  
}