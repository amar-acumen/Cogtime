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


class Holy_place extends Base_controller
{
    
    private $pagination_per_page =  25 ;
    private $search_pagination_per_page =  2 ;
    private $popular_pagination_per_page =  10 ;
    
    
    
    private $ring_members_pagination_per_page =  10 ;
    
    public function __construct()
     {
	 	
        try
        {
            parent::__construct();
            parent::check_login(TRUE, '', array('1')); // put this code on those pages which are not accessable by guest user
            # loading reqired model & helpers...
			$this->upload_path = BASEPATH.'../uploads/user_ring_logos/';
            $this->load->model('users_model');
			$this->load->model('holy_place_model');
			$this->load->model('bible_fruits_model');
			
			$this->i_profile_id = intval(decrypt($this->session->userdata('user_id')));
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }  

    }

    
    public function index($s_member_type = '') 
    {
        try
        {
                  
            $posted=array();
            $this->data["posted"]=$posted;/*don't change*/    
            $data = $this->data;      
            $this->data["MAIN_MENU_SELECTED"] = 6;
            parent::_set_title('::: COGTIME Xtian network :::');
            parent::_set_meta_desc('');
            parent::_set_meta_keywords('');
        
            
            parent::_add_js_arr( array( /*'js/ddsmoothmenu.js',
                                        'js/switch.js','js/animate-collapse.js',
                                        'js/lightbox.js','js/jquery.dd.js','js/jquery-ui-1.8.2.custom.min.js',
                                        'js/stepcarousel.js'*/
                                        ));
                                        
            parent::_add_css_arr( array(/*'css/jquery-ui-1.8.2.custom.css',
                                          'css/dd.css'*/) );
            
           
		   
    //pr($data['blogdata']);
    
            $this->session->set_userdata('where','');
            
            # view file...
			ob_start();
			
			ob_end_clean();
			//$data['listingContent'] = $content;
            $VIEW = "logged/ring/my_ring.phtml"; 
            parent::_render($data, $VIEW);
        }
        catch(Exception $err_obj)
        {
           
        } 

    }
	
	
	  

    public function prayer_wall()
	{
		try
        {
                  
            $posted=array();
            $this->data["posted"]=$posted;/*don't change*/    
            $data = $this->data;      
            $this->data["MAIN_MENU_SELECTED"] = 6;
            parent::_set_title('::: COGTIME Xtian network :::');
            parent::_set_meta_desc('');
            parent::_set_meta_keywords('');
        
            
            parent::_add_js_arr( array( /*'js/ddsmoothmenu.js',
                                        'js/switch.js','js/animate-collapse.js',
                                        'js/lightbox.js','js/jquery.dd.js','js/jquery-ui-1.8.2.custom.min.js',
                                        'js/stepcarousel.js'*/
                                        ));
                                        
            parent::_add_css_arr( array(/*'css/jquery-ui-1.8.2.custom.css',
                                          'css/dd.css'*/) );
            
           
		   
    //pr($data['blogdata']);
    
            $this->session->set_userdata('where','');
            
            # view file...
			ob_start();
			$content = $this->generate_prayer_wall_listing_AJAX();
			$content = ob_get_contents();
			$content_obj = json_decode($content);
			$data['listingContent'] = $content_obj->html; 
			$data['no_of_result'] = $content_obj->no_of_result;
			ob_end_clean();
			//$data['listingContent'] = $content;
            $VIEW = "logged/holy_place/all_prayers.phtml"; 
            parent::_render($data, $VIEW);
        }
        catch(Exception $err_obj)
        {
           
        } 

	}
	
	public function generate_prayer_wall_listing_AJAX($page=0)
    {
		$wh	= "";
		$data['prayer_wall_data']	= $this->holy_place_model->get_all_prayers($wh,$page,$this->pagination_per_page,'');
		
		
		$data['pagination_per_page'] = $this->pagination_per_page;
		//pr($result);
		$resultCount = count($data['prayer_wall_data']);
		$total_rows = $this->holy_place_model->get_count_all_prayers($wh);
		$cur_page = $page + $this->pagination_per_page;
        
        
		// getting auction-category listing...
		$data['no_of_ring'] = $total_rows;
		$data['no_of_result'] = $total_rows;
		$data['current_page_1'] = $cur_page;

		 //--- for check end of he page.
           $view_more = true;
           $rest_counter = $total_rows-$page;
		   if($rest_counter<=$this->pagination_per_page)
			  $view_more = false;
         //--------- end check
        # loading the view-part...
        $AJAX_VIEW_FILE = 'logged/holy_place/ajax_holy_place/ajax_listing_prayer.phtml';
        
        
   //pr($result);
		
		if( $total_rows>0 ) {
        	$listingContent = $this->load->view($AJAX_VIEW_FILE, $data, true);
		}
		else {
			$listingContent = '';
		}
		//echo 1; exit;
		//echo json_encode( array('html'=>$content, 'current_page'=>$page) );
        echo json_encode( array('html'=>$listingContent, 
								'current_page'=>$cur_page, 
								'no_of_result'=>$data['no_of_result'],
								'total'=>$total_rows,
								'view_more'=>$view_more ,
								'cur_page'=>$data['current_page_1']) );
    } 
	
	
	
	public function read_bible($slab='',$id='')
	{
		try
        {
			$posted=array();
			$this->data["posted"]=$posted;/*don't change*/    
			$data = $this->data;      
			$this->data["MAIN_MENU_SELECTED"] = 6;
			parent::_set_title('::: COGTIME Xtian network :::');
			parent::_set_meta_desc('');
			parent::_set_meta_keywords('');
		
			
			parent::_add_js_arr( array( /*'js/ddsmoothmenu.js',
										'js/switch.js','js/animate-collapse.js',
										'js/lightbox.js','js/jquery.dd.js','js/jquery-ui-1.8.2.custom.min.js',
										'js/stepcarousel.js',
										'js/jquery.autofill.js',
										'js/jquery/ui/jquery.ui.core.js',
										'js/jquery.ui.datepicker.js',
										'js/tab.js',
										'js/jquery.nicescroll.min.js'
										//'js/jquery.mCustomScrollbar.min.js'*/
                                        'js/search.js'

										));
										
			parent::_add_css_arr( array('css/jquery-ui-1.8.13.custom.css',
											  'css/dd.css',
											  'css/jquery.mCustomScrollbar.css') );
				
			### check if reading plan exists or not
			
			$data['category_html']	= makeOptionBibleCategory();
			$data['readingplan']	= $this->holy_place_model->get_reading_plan();
			$data['category']		= $this->holy_place_model->get_category_list();
			
			//pr($data['readingplan']);
			if(count($data['readingplan']) > 0)
			{
				$wh	= " s_testament='Old Testament'"	;
				$data['ot_book']	= makeOptionBibleBook($wh,'');
				$wh1	= " s_testament='New Testament'"	;
				$data['nt_book']	= makeOptionBibleBook($wh1,'');
				
				$i_profile_id 				= intval(decrypt($this->session->userdata('user_id')));
				$sql 						= "SELECT * FROM {$this->db->BIBLE_READING} WHERE i_user_id='".$i_profile_id."'";//exit;
				$data['read_bible_data']	= $this->db->query($sql)->result_array();
				
				 $data['read_start_date'] = $this->holy_place_model->get_reading_plan_start_date();
				 $data['read_end_date'] = $this->holy_place_model->get_reading_plan_end_date();
				 $data['last_read_date'] = $this->holy_place_model->get_reading_plan_last_read_date();
				 
				 $data['plan_type']  = $this->holy_place_model->get_reading_plan_type();
				 
				 $data['progress_bar_data'] = get_bible_progress_bar($data['plan_type']);
				 
				 $today_date = date('Y-m-d');
				 $s_today_plan  = "AND dt_date = '{$today_date}'";
				 $data['todays_plan_arr'] = $this->holy_place_model->get_reading_plan($s_today_plan);
				 
				 $tommorow =  strtotime('+1 days');
				 $tmrw_date = date('Y-m-d', $tommorow);
				 $s_tmrw_plan  = "AND dt_date = '{$tmrw_date}'";
				 $data['tmrw_plan_arr'] = $this->holy_place_model->get_reading_plan($s_tmrw_plan);
				//pr($data['todays_plan_arr']);
				
				//pr($data['tmrw_plan_arr']);
				
				//$data['verse']	= makeOptionBibleverse('','');
			}	
			### check if reading plan exists or not
			
			$search_txt_from_other_pages	= $this->input->post('searchtxt');
			
            if($search_txt_from_other_pages)     
			{
				$this->load->library('CallSp');
				#echo $read_bible[0]['dt_end_date'];exit;
				$sqlStartImmediately 	=   "CALL search('".$search_txt_from_other_pages."')";
				$sp   					=   new CallSp();
				$verses	= $sp->executeStoreProcedure( $sqlStartImmediately );
				
				$data['arr_search_result']	= $verses; //$arr_search_result;
			
				$data['searchtxt']			= $searchtxt;
				$AJAX_VIEW_FILE = 'logged/holy_place/ajax_holy_place/ajax_listing_search_result.phtml';
				$listingContent = $this->load->view($AJAX_VIEW_FILE, $data, true);
				$data['listingContent'] = $listingContent;
				$VIEW = "logged/holy_place/read_bible.phtml"; 
				parent::_render($data, $VIEW);
			}
			else
			{
				
				//pr($data['blogdata']);
		
				$this->session->set_userdata('where','');
				$data['slab']	= $slab;
				$data['anchor_id']	= $id;
				$data['category_html']	= makeOptionBibleCategory();
				$data['bible_library']	= $this->holy_place_model->get_note_bkmark_hilits('all');
				# view file...
				ob_start();
				$content = $this->generate_read_bible_AJAX();
				$content = ob_get_contents();
				$data['listingContent'] = $content;
				ob_end_clean();
				//$data['listingContent'] = $content;
				$VIEW = "logged/holy_place/read_bible.phtml"; 
				parent::_render($data, $VIEW);
			}
        }
        catch(Exception $err_obj)
        {
           
        } 
	}
	
	public function generate_read_bible_AJAX($page=0)
    {
		//echo '==='.$page;
		$data['hlts']	= $this->holy_place_model->get_all_hilits_arr_by_verse_for_user('');
		$data['note']	= $this->holy_place_model->get_all_note_arr_by_verse_for_user('');
		$data['bkmark']	= $this->holy_place_model->get_all_bkmark_arr_by_verse_for_user('');
		
		$data['category_html']	= makeOptionBibleCategory();
		#pr($data['note']);exit;
		
		$wh	= "";
		$data['bible_data']	= $this->holy_place_model->get_bible($wh,$page,$this->pagination_per_page,'');
		
		
		$data['pagination_per_page'] = $this->pagination_per_page;
		//pr($result);
		$resultCount = count($data['prayer_wall_data']);
		$total_rows = $this->holy_place_model->get_count_bible($wh);
							
		//$data['versepointer']	= $this->holy_place_model->get_array_of_verse_pointer();					
		$this->load->library('jquery_pagination');
		$config['base_url'] = base_url()."logged/holy_place/generate_read_bible_AJAX";
		$config['total_rows'] = $total_rows;
		$config['per_page'] = $this->pagination_per_page;
		$config['uri_segment'] = 4;
		$config['num_links'] = 1;
		$config['page_query_string'] = false;
		$config['prev_link'] = 'PREV PAGE';
		$config['next_link'] = 'NEXT PAGE';
		
		$config['cur_tag_open'] = '<li class="num_tag_link">';
		$config['cur_tag_close'] = '</li>';
		
		$config['next_tag_open'] = '<li>';
		$config['next_tag_close'] = '</li>';
		
		$config['prev_tag_open'] = '<li>';
		$config['prev_tag_close'] = '</li><li>|</li>';
		
		$config['num_tag_open'] = '<li class="num_tag_link">';
		$config['num_tag_close'] = '</li>';
		
		$config['first_link'] = '';
		$config['last_link'] = '';
		
	
		
		$config['div'] = '#table_content'; /* Here #content is the CSS selector for target DIV */
		$config['js_bind'] = "showBusyScreen();resetAll();"; /* if you want to bind extra js code */
		$config['js_rebind'] = "hideBusyScreen();showscroll();"; /* if you want to rebind extra js code */
		
		$this->jquery_pagination->initialize($config);
		$data['page_links'] = $this->jquery_pagination->create_links();
		
		// getting   listing...
		$data['info_arr'] = $result;
		$data['no_of_result'] = $total_rows;
		$data['current_page'] = $page;
		
		$data['pagination_per_page']   =    $this->pagination_per_page;
		
		# loading the view-part...
		$AJAX_VIEW_FILE = 'logged/holy_place/ajax_holy_place/ajax_listing_bible.phtml';
		echo $this->load->view($AJAX_VIEW_FILE, $data,TRUE);
    } 
	
	public function all_books()
	{
		try
        {
                  
            $posted=array();
            $this->data["posted"]=$posted;/*don't change*/    
            $data = $this->data;      
            $this->data["MAIN_MENU_SELECTED"] = 6;
            parent::_set_title('::: COGTIME Xtian network :::');
            parent::_set_meta_desc('');
            parent::_set_meta_keywords('');
        
            
            parent::_add_js_arr( array( 'js/ddsmoothmenu.js',
                                        'js/switch.js','js/animate-collapse.js',
                                        'js/lightbox.js','js/jquery.dd.js','js/jquery-ui-1.8.2.custom.min.js',
                                        'js/stepcarousel.js',
										'js/jquery.autofill.js',
										'js/search.js'
                                        ));
                                        
            parent::_add_css_arr( array('css/jquery-ui-1.8.2.custom.css',
                                          'css/dd.css') );
            
           
		   
    //pr($data['blogdata']);
    
            $this->session->set_userdata('where','');
            
            # view file...
			ob_start();
			$content = $this->generate_all_books_listing_AJAX();
			$content = ob_get_contents();
			$content_obj = json_decode($content);
			$data['listingContent'] = $content_obj->html; 
			$data['no_of_result'] = $content_obj->no_of_result;
			ob_end_clean();
			//$data['listingContent'] = $content;
            $VIEW = "logged/holy_place/all_books.phtml"; 
            parent::_render($data, $VIEW);
        }
        catch(Exception $err_obj)
        {
           
        } 

	}
	
	public function generate_all_books_listing_AJAX($page=0)
    {
		$wh	= "";
		$data['bible_data']	= $this->holy_place_model->get_all_books($wh,'','','');
		
		
		$data['pagination_per_page'] = $this->pagination_per_page;
		//pr($result);
		$resultCount = count($data['bible_data']);
		$total_rows = $this->holy_place_model->get_count_all_books($wh);
		$cur_page = $page + $this->pagination_per_page;
        
        
		// getting auction-category listing...
		$data['no_of_ring'] = $total_rows;
		$data['no_of_result'] = $total_rows;
		$data['current_page_1'] = $cur_page;

		 //--- for check end of he page.
           $view_more = true;
           $rest_counter = $total_rows-$page;
		   if($rest_counter<=$this->pagination_per_page)
			  $view_more = false;
         //--------- end check
        # loading the view-part...
        $AJAX_VIEW_FILE = 'logged/holy_place/ajax_holy_place/ajax_listing_books.phtml';
        
        
   //pr($result);
		
		if( $total_rows>0 ) {
        	$listingContent = $this->load->view($AJAX_VIEW_FILE, $data, true);
		}
		else {
			$listingContent = '';
		}
		//echo 1; exit;
		//echo json_encode( array('html'=>$content, 'current_page'=>$page) );
        echo json_encode( array('html'=>$listingContent, 
								'current_page'=>$cur_page, 
								'no_of_result'=>$data['no_of_result'],
								'total'=>$total_rows,
								'view_more'=>$view_more ,
								'cur_page'=>$data['current_page_1']) );
    } 
	
	
	
	
	public function books_chapter($bookname)
	{
		try
        {
                  
            $posted=array();
            $this->data["posted"]=$posted;/*don't change*/    
            $data = $this->data;      
            $this->data["MAIN_MENU_SELECTED"] = 6;
            parent::_set_title('::: COGTIME Xtian network :::');
            parent::_set_meta_desc('');
            parent::_set_meta_keywords('');
        
            
            parent::_add_js_arr( array( 'js/ddsmoothmenu.js',
                                        'js/switch.js','js/animate-collapse.js',
                                        'js/lightbox.js','js/jquery.dd.js','js/jquery-ui-1.8.2.custom.min.js',
                                        'js/stepcarousel.js',
										'js/jquery.autofill.js',
										'js/search.js'
                                        ));
                                        
            parent::_add_css_arr( array('css/jquery-ui-1.8.2.custom.css',
                                          'css/dd.css') );
            
           
		   
    //pr($data['blogdata']);
	
			$bookname	= get_original_text($bookname);
			$data['bookname']	= $bookname;
    		$wh			= " AND s_book_name='".$bookname."'";
            $bookdetail	= $this->holy_place_model->get_all_books($wh,'','','');
			#pr($bookdetail);
			$wh	= " AND i_book_id='".$bookdetail[0]['id']."'";
            $data['chapter_data']	= $this->holy_place_model->get_books_chapter($wh,'','','');
				
            # view file...
			
			//$data['listingContent'] = $content;
            $VIEW = "logged/holy_place/books_chapter.phtml"; 
            parent::_render($data, $VIEW);
        }
        catch(Exception $err_obj)
        {
           
        } 

	}
	
	public function verses($bookname,$chapter)
	{
		try
        {
                  
            $posted=array();
            $this->data["posted"]=$posted;/*don't change*/    
            $data = $this->data;      
            $this->data["MAIN_MENU_SELECTED"] = 6;
            parent::_set_title('::: COGTIME Xtian network :::');
            parent::_set_meta_desc('');
            parent::_set_meta_keywords('');
        
            
            parent::_add_js_arr( array( 'js/ddsmoothmenu.js',
                                        'js/switch.js','js/animate-collapse.js',
                                        'js/lightbox.js','js/jquery.dd.js','js/jquery-ui-1.8.2.custom.min.js',
                                        'js/stepcarousel.js',
										'js/jquery.autofill.js',
										'js/search.js'
                                        ));
                                        
            parent::_add_css_arr( array('css/jquery-ui-1.8.2.custom.css',
                                          'css/dd.css') );
										  
           $bookname			= get_original_text($bookname);
           $data['bookname']	= $bookname;
		   $data['chapter']		= $chapter;
		   
    //pr($data['blogdata']);
    		$wh			= " AND s_book_name='".$bookname."'";
            $bookdetail	= $this->holy_place_model->get_all_books($wh,'','','');
			#pr($bookdetail);
			$wh	= " AND b.s_book_name='".$bookname."' AND c.s_chapter='".$chapter."'";
            $data['verse_data']	= $this->holy_place_model->get_bible($wh,'','','');
			
			$data['versepointer']	= $this->holy_place_model->get_array_of_verse_pointer();
			#pr($data['versepointer']);
            # view file...
			
			//$data['listingContent'] = $content;
            $VIEW = "logged/holy_place/chapter_verse.phtml"; 
            parent::_render($data, $VIEW);
        }
        catch(Exception $err_obj)
        {
           
        } 

	}
	
	
	public function get_chapter()
	{
		$verseid		= $this->input->post('verseid');
		$paging			= $this->input->post('paging');
		$chapter		= $this->input->post('chapter');
		if($chapter==1 && $paging	=='prev')
		{
			echo json_encode(array("slab"=>0,"dest_verse"=>1));
			exit;
		}
		else
		{
			$verses			= $this->holy_place_model->get_chapter($paging,$chapter);
			$versepointer	= $this->holy_place_model->get_array_of_verse_pointer($verses['id']);
			echo json_encode(array("slab"=>$versepointer[$verses['id']]['slab_for_current_id'],"dest_verse"=>$verses['id']));
			exit;
		}
	}
	public function get_book()
	{
		$verseid		= $this->input->post('verseid');
		$paging			= $this->input->post('paging');
		$book			= $this->input->post('book');
		if($book==1 && $paging	=='prev')
		{
			echo json_encode(array("slab"=>0,"dest_verse"=>1));
			exit;
		}
		else
		{
			$verses			= $this->holy_place_model->get_book($paging,$book);
			$versepointer	= $this->holy_place_model->get_array_of_verse_pointer($verses['id']);
			echo json_encode(array("slab"=>$versepointer[$verses['id']]['slab_for_current_id'],"dest_verse"=>$verses['id']));
			exit;
		}
	}
	
	public function search()
	{
		
		$searchtxt		= $this->input->post('searchtxt');
		
		if(preg_match("/^[\w\s]+[\s]?[0-9]+[:][0-9]+/", $searchtxt ))
		{
			$arr	= explode(':',$searchtxt);
			$verse	= $arr[1];
			$arr1	= explode(' ',$arr[0]);
			$book_name	= $arr1[0];
			$chapter	= $arr1[1];
			$wh			= " AND b.s_book_name='".$book_name."' AND c.s_chapter='".$chapter."' AND v.i_verses='".$verse."'";
			$verses			= $this->holy_place_model->get_bible($wh,'','','');
			$versepointer	= $this->holy_place_model->get_array_of_verse_pointer($verses[0]['verseid']);
			
			echo json_encode(array( "searchtype"=>'by_verse',
									"slab"=>$versepointer[$verses[0]['verseid']]['slab_for_current_id'],
									"dest_verse"=>$verses[0]['verseid'],
									"success"=>true));
			exit;
		}
		else
		{
			// $wh			= " AND v.s_text LIKE '%".$searchtxt."%'";
			//$verses			= $this->holy_place_model->get_bible_search($searchtxt);
			$this->load->library('CallSp');
			#echo $read_bible[0]['dt_end_date'];exit;
			$sqlStartImmediately 	=   "CALL search('".$searchtxt."')";
			$sp   					=   new CallSp();
			$verses	= $sp->executeStoreProcedure( $sqlStartImmediately );	
			//pr($row,1);
			$data['arr_search_result']	= $verses; //$arr_search_result;
			
			$data['searchtxt']					= $searchtxt;
			$data['category_html']	= makeOptionBibleCategory();
			$AJAX_VIEW_FILE = 'logged/holy_place/ajax_holy_place/ajax_listing_search_result.phtml';
        	$listingContent = $this->load->view($AJAX_VIEW_FILE, $data, true);
			echo json_encode(array( "searchtype"=>'by_book',
									"content"=>$listingContent,
									"success"=>true));
			exit;
		}
		
		echo json_encode(array(
									"success"=>false));
		exit;
	}
	public function search_more()
	{
		$stext	= $this->input->post('stext');
		$bid	= $this->input->post('bid');
		$verse_id	= $this->input->post('verse_ids');
		$verse_id	= substr($verse_id,0,-1);
		$wh	= " AND v.id NOT IN (".$verse_id.") AND b.id='".$bid."' AND v.s_text LIKE'%".$stext."%'";
		$data['bible_data']	= $this->holy_place_model->get_bible($wh,'','','');
		$data['category_html']	= makeOptionBibleCategory();
		#pr($data['bible_data'],1);
		$AJAX_VIEW_FILE = 'logged/holy_place/ajax_holy_place/ajax_listing_more_search_result.phtml';
		$listingContent = $this->load->view($AJAX_VIEW_FILE, $data, true);
		echo json_encode(array( "content"=>$listingContent,
								"success"=>true));
	}
	
	public function add_note()
	{
		$err	= array();
		if($this->input->post('category') == '' && $this->input->post('add_cat_n') == '')
		{
			$err['err_cat']	= "Required field";
		}
		if($this->input->post('note') == '')
		{
			$err['err_note']	= "Required field";
		}
		if(count($err)>0)
		{
			echo json_encode(array( "data"=>$err,
									"success"=>false));
			exit;
		}
		else
		{
			$insert_arr['i_verses_id']	= $this->input->post('verIdForNote');
			$insert_arr['i_user_id']	= $this->i_profile_id;
			
			if($this->input->post('category') != '' && $this->input->post('add_cat_n') != '')
			{
				$insert_arr['i_category']	= decrypt($this->input->post('category'));
			}
			else if($this->input->post('category') == '' && $this->input->post('add_cat_n') != '')
			{
				$insert_arr1['i_user_id']	= $this->i_profile_id;
				$insert_arr1['s_category']	= $this->input->post('add_cat_n');
				
				$wh	= " AND s_category='".$insert_arr1['s_category']."'";
				$check_cat_name	= $this->holy_place_model->get_category_list($wh);
				if(count($check_cat_name)>0)
				{
					$insert_arr['i_category']	= $check_cat_name[0]['id'];
				}
				else
				{
					$this->holy_place_model->add_bible_category($insert_arr1);
					$insert_arr['i_category']	= $this->db->insert_id();
				}
				
			}
			else
				$insert_arr['i_category']	= decrypt($this->input->post('category'));
				
			$insert_arr['s_note']				= $this->input->post('note');
			$insert_arr['dt_created_date']		= get_db_datetime();
			$this->holy_place_model->add_note($insert_arr);
			$versepointer	= $this->holy_place_model->get_array_of_verse_pointer($insert_arr['i_verses_id']);
			
			
			$category_html ='';
			$category_html .= '<option value="">View all</option>';
			$category_html .= makeOptionBibleCategory();
			$category_html1 = '<option value="">Select category</option>';
			$category_html1 .= makeOptionBibleCategory();
			
			$data['bible_library']	= $this->holy_place_model->get_note_bkmark_hilits('all');
			$AJAX_VIEW_FILE = 'logged/holy_place/ajax_holy_place/ajax_listing_library.phtml';
        	$listingContent = $this->load->view($AJAX_VIEW_FILE, $data, true);
			
			echo json_encode(array( "data"=>"Note has been successfully added",
									"slab"=>$versepointer[$insert_arr['i_verses_id']]['slab_for_current_id'],
									"library"=>$listingContent,
									"category_html"=>$category_html,
									"category_html1"=>$category_html1,
									"success"=>true));
			exit;
		}
	}
	
	public function edit_note()
	{
		$err	= array();
		if($this->input->post('category') == '')
		{
			$err['err_cat']	= "Required field";
		}
		if($this->input->post('note') == '')
		{
			$err['err_note']	= "Required field";
		}
		if(count($err)>0)
		{
			echo json_encode(array( "data"=>$err,
									"success"=>false));
			exit;
		}
		else
		{
			
			$insert_arr['i_category']	= decrypt($this->input->post('category'));
			$insert_arr['s_note']		= $this->input->post('note');
			$wh 	= array('id'=>decrypt($this->input->post('noteid')));
			$this->holy_place_model->edit_note($insert_arr,$wh);
			$this->get_all_note(2);
			
		}
	}
	
	public function show_edit_note($nid)
	{
		$wh	= "";
		if($nid!='')
		{
			$id	= decrypt($nid);
			$wh	= " AND n.id = '".$id."'";
		}	
		$note_arr	= $this->holy_place_model->get_all_note_by_user($wh,'','','');
		foreach($note_arr as $val)
		{
			$cat	= encrypt($val[0]['i_category']);
			$txt	= $val[0]['note_text'];
		}
		
        echo json_encode( array('category'=>$cat,'text'=>$txt) );
	}
	
	public function remove_note($id)
	{
		$wh	= array('id'=>decrypt($this->input->post('deletenoteid')));
		$this->holy_place_model->delete_note($wh);
		//echo $this->db->last_query();exit;
		$this->get_all_note(1,decrypt($this->input->post('deletenoteid')));
	}
	
	public function remove_bookmark()
	{
		$wh	= array('id'=>decrypt($this->input->post('deleteBkmarkId')));
		$this->holy_place_model->delete_bookmark($wh);
		#echo $this->db->last_query();exit;
		$this->get_all_bookmark(1,decrypt($this->input->post('deleteBkmarkId')));
	}
	
	public function remove_hilits()
	{
		$wh	= array('id'=>decrypt($this->input->post('deleteHilitsId')));
		$this->holy_place_model->delete_hilits($wh);
		//echo $this->db->last_query();exit;
		$this->get_all_highilights(1, decrypt($this->input->post('deleteHilitsId')));
	}
	
	public function add_highlights()
	{
		if($this->input->post('bk_mark_category') == '' && $this->input->post('add_cat_h') == '')
		{
			$err['err_cat']	= "Required field";
		}
		if(count($err)>0)
		{
			echo json_encode(array( "data"=>$err,
									"success"=>false));
			exit;
		}
		else
		{
			$insert_arr['i_verses_id']	= $this->input->post('versesid');
			$insert_arr['s_text']		= trim($this->input->post('text'));
			$insert_arr['i_colorhidden']		= trim($this->input->post('colorhidden'));
			if($insert_arr['i_colorhidden']=='')
				$insert_arr['i_colorhidden']	= 1;
			
			if($this->input->post('bk_mark_category') != '' && $this->input->post('add_cat_h') != '')
			{
				$insert_arr['s_category']	= decrypt($this->input->post('bk_mark_category'));
			}
			else if($this->input->post('bk_mark_category') == '' && $this->input->post('add_cat_h') != '')
			{
				$insert_arr1['i_user_id']	= $this->i_profile_id;
				$insert_arr1['s_category']	= $this->input->post('add_cat_h');
				
				$wh	= " AND s_category='".$insert_arr1['s_category']."'";
				$check_cat_name	= $this->holy_place_model->get_category_list($wh);
				if(count($check_cat_name)>0)
				{
					$insert_arr['s_category']	= $check_cat_name[0]['id'];
				}
				else
				{
					$this->holy_place_model->add_bible_category($insert_arr1);
					$insert_arr['s_category']	= $this->db->insert_id();
				}
			}
			else
				$insert_arr['s_category']	= decrypt($this->input->post('bk_mark_category'));
			
			$insert_arr['i_user_id']	= $this->i_profile_id;
			
			$versetext	= $this->holy_place_model->get_verse_text_by_verse_id($insert_arr['i_verses_id']);
			
			$pos1	= strpos($versetext['s_text'],trim($insert_arr['s_text']));
			$pos2	= strlen($insert_arr['s_text'])+$pos1;
			$insert_arr['i_pos1']	= $pos1;
			$insert_arr['i_pos2']	= $pos2;
			$wh	= " AND i_verses_id='".$insert_arr['i_verses_id']."' AND (('".$insert_arr['i_pos1']."'>= i_pos1 AND '".$insert_arr['i_pos1']."'<= i_pos2)
					OR ('".$insert_arr['i_pos2']."'>= i_pos1 AND '".$insert_arr['i_pos2']."'<= i_pos2)) ";
					
			$all_hilits_by_vid	= $this->holy_place_model->get_hilits_for_pos($wh);
			
			$flag	= 0;
			$hidarr	= array();
			foreach($all_hilits_by_vid as $val)
			{
				if($val['i_pos1']<=$insert_arr['i_pos1'])
				{
					$flag	= 1;
					$insert_arr['i_pos1']	= $val['i_pos1'];
					$vid	= $val['id'].',';
				}
				if($val['i_pos2']>=$insert_arr['i_pos2'])
				{
					$flag	= 1;
					$insert_arr['i_pos2']	= $val['i_pos2'];
					$vid	.= $val['id'].',';
				}
			}
			
			if($flag	== 1)
			{
				$vid	= substr($vid,0,strlen($vid)-1);
				$charlength	= ($insert_arr['i_pos2']-$insert_arr['i_pos1'])+1;
				$insert_arr['s_text']	= substr($versetext['s_text'],$insert_arr['i_pos1'],$charlength);
				$deletesql	= "DELETE FROM {$this->db->BIBLE_HILITS} WHERE id IN($vid)";
				$this->db->query($deletesql);
			}
			$insert_arr['dt_created_date']		= get_db_datetime();
			$last_id	= $this->holy_place_model->add_highlights($insert_arr);
			if($last_id)
			{
				$txt	= $this->holy_place_model->get_verse_txt_with_hilits($insert_arr['i_verses_id']);
				
				$data['bible_library']	= $this->holy_place_model->get_note_bkmark_hilits('all');
				$AJAX_VIEW_FILE = 'logged/holy_place/ajax_holy_place/ajax_listing_library.phtml';
				$listingContent = $this->load->view($AJAX_VIEW_FILE, $data, true);				
				
				$category_html ='';
				$category_html .= '<option value="">View all</option>';
				$category_html .= makeOptionBibleCategory();
				$category_html1 = '<option value="">Select category</option>';
				$category_html1 .= makeOptionBibleCategory();
				
						
				echo json_encode(array( "data"=>"Highlights has been successfully added",
										"txt"=>$txt,
										"library"=>$listingContent,
										"category_html"=>$category_html,
										"category_html1"=>$category_html1,
										"success"=>true));
				exit;
			}
			else
			{
				echo json_encode(array( "data"=>"An error occured",
										"txt"=>'',
										"success"=>false));
				exit;
			}	
		}
	}
	
	public function add_bookmark()
	{
		
		if($this->input->post('bk_mark_category') == '' && $this->input->post('add_cat_b') == '')
		{
			$err['err_cat']	= "Required field";
		}
		if(count($err)>0)
		{
			echo json_encode(array( "data"=>$err,
									"success"=>false));
			exit;
		}
		else
		{
			$insert_arr['i_verses_id']	= $this->input->post('versesid');
			if($this->input->post('bk_mark_category') != '' && $this->input->post('add_cat_b') != '')
			{
				$insert_arr['s_category']	= decrypt($this->input->post('bk_mark_category'));
			}
			else if($this->input->post('bk_mark_category') == '' && $this->input->post('add_cat_b') != '')
			{
				$insert_arr1['i_user_id']	= $this->i_profile_id;
				$insert_arr1['s_category']	= $this->input->post('add_cat_b');
				
				$wh	= " AND s_category='".$insert_arr1['s_category']."'";
				$check_cat_name	= $this->holy_place_model->get_category_list($wh);
				if(count($check_cat_name)>0)
				{
					$insert_arr['s_category']	= $check_cat_name[0]['id'];
				}
				else
				{
					$this->holy_place_model->add_bible_category($insert_arr1);
					$insert_arr['s_category']	= $this->db->insert_id();
				}
			}
			else
				$insert_arr['s_category']	= decrypt($this->input->post('bk_mark_category'));
			$insert_arr['i_user_id']	= $this->i_profile_id;
			$insert_arr['dt_created_date']		= get_db_datetime();
			if($this->holy_place_model->add_bookmark($insert_arr))
			{
				$versepointer	= $this->holy_place_model->get_array_of_verse_pointer($insert_arr['i_verses_id']);
			
				$data['bible_library']	= $this->holy_place_model->get_note_bkmark_hilits('all');
				$AJAX_VIEW_FILE = 'logged/holy_place/ajax_holy_place/ajax_listing_library.phtml';
				$listingContent = $this->load->view($AJAX_VIEW_FILE, $data, true);
				
				$category_html ='';
				$category_html .= '<option value="">View all</option>';
				$category_html .= makeOptionBibleCategory();
				$category_html1 = '<option value="">Select category</option>';
				$category_html1 .= makeOptionBibleCategory();
				
				echo json_encode(array( "data"=>"Bookmark has been successfully added",
										"slab"=>$versepointer[$insert_arr['i_verses_id']]['slab_for_current_id'],
										"library"=>$listingContent,
										"category_html"=>$category_html,
										"category_html1"=>$category_html1,
										"success"=>true));
				exit;
			}
			else
			{
				echo json_encode(array( "data"=>"This bookmark has already been added",
										"success"=>false));
				exit;
			}	
		}
	}
	
	public function get_all_note($showeditdelete='',$id='')
	{
		$wh	= "";
		$cat	= $this->input->post('cat');
		if($cat!='')
		{
			$cat	= decrypt($cat);
			$wh	= " AND n.i_category = '".$cat."'";
		}	
		
		$data['showeditdelete']	= $showeditdelete;
		//$data['category_html']	= makeOptionBibleCategory();
		
		$data['type']	= 'note';
		$data['note_arr']	= $this->holy_place_model->get_note_bkmark_hilits('note',$wh);
		$AJAX_VIEW_FILE = 'logged/holy_place/ajax_holy_place/ajax_listing_note_short.phtml';
        
		$listingContent = $this->load->view($AJAX_VIEW_FILE, $data, true);
		if($showeditdelete==1)
		{
			$versepointer	= $this->holy_place_model->get_array_of_verse_pointer($id);
			$data['bible_library']	= $this->holy_place_model->get_note_bkmark_hilits('all');
			$AJAX_VIEW_FILE = 'logged/holy_place/ajax_holy_place/ajax_listing_library.phtml';
        	$library_content = $this->load->view($AJAX_VIEW_FILE, $data, true);
		}
		if($showeditdelete==2)
		{
			$data['bible_library']	= $this->holy_place_model->get_note_bkmark_hilits('all');
			$AJAX_VIEW_FILE = 'logged/holy_place/ajax_holy_place/ajax_listing_library.phtml';
        	$library_content = $this->load->view($AJAX_VIEW_FILE, $data, true);
		}
		
		//$category_html = makeOptionBibleCategory();
		//echo 1; exit;
		//echo json_encode( array('html'=>$content, 'current_page'=>$page) );
        echo json_encode( array('html'=>$listingContent,
								"slab"=>$versepointer[$id]['slab_for_current_id'],
								"dest_verse"=>$id,
								"library"=>$library_content,
								'filtercat'=>$data['category_html'],'success'=>true) );
		exit;
	}

	public function get_all_bookmark($showeditdelete='',$id='')
	{
		$wh	= "";
		$cat	= $this->input->post('cat');
		if($cat!='')
		{
			$cat	= decrypt($cat);
			$wh	= " AND n.s_category = '".$cat."'";
		}	
		
		
		$data['showeditdelete']	= $showeditdelete;
		$data['category_html']	= makeOptionBibleCategory();
		
		
		$data['type']	= 'bkmark';
		$data['note_arr']	= $this->holy_place_model->get_note_bkmark_hilits('bookmark',$wh);
		$AJAX_VIEW_FILE = 'logged/holy_place/ajax_holy_place/ajax_listing_note_short.phtml';
		
        $listingContent = $this->load->view($AJAX_VIEW_FILE, $data, true);
		
		if($showeditdelete==1)
		{	
			$versepointer	= $this->holy_place_model->get_array_of_verse_pointer($id);
			$data['bible_library']	= $this->holy_place_model->get_note_bkmark_hilits('all');
			$AJAX_VIEW_FILE = 'logged/holy_place/ajax_holy_place/ajax_listing_library.phtml';
        	$library_content = $this->load->view($AJAX_VIEW_FILE, $data, true);
		}
		
		//echo 1; exit;
		//echo json_encode( array('html'=>$content, 'current_page'=>$page) );
        echo json_encode( array('html'=>$listingContent,
								"slab"=>$versepointer[$id]['slab_for_current_id'],
								"dest_verse"=>$id,
								"library"=>$library_content,
								'filtercat'=>$data['category_html']) );
	}
	
	public function get_all_highilights($showeditdelete='', $id='')
	{#echo $id;exit;
		$wh	= "";
		$cat	= $this->input->post('cat');
		if($cat!='')
		{
			$cat	= decrypt($cat);
			$wh	= " AND n.s_category = '".$cat."'";
		}	
		
		$data['showeditdelete']	= $showeditdelete;
		$data['category_html']	= makeOptionBibleCategory();
		$data['type']	= 'hilits';
		$data['note_arr']	= $this->holy_place_model->get_note_bkmark_hilits('hilits',$wh);
		$AJAX_VIEW_FILE = 'logged/holy_place/ajax_holy_place/ajax_listing_note_short.phtml';
        $listingContent = $this->load->view($AJAX_VIEW_FILE, $data, true);
		if($showeditdelete==1)
		{	
			$versepointer	= $this->holy_place_model->get_array_of_verse_pointer($id);
			$data['bible_library']	= $this->holy_place_model->get_note_bkmark_hilits('all');
			$AJAX_VIEW_FILE = 'logged/holy_place/ajax_holy_place/ajax_listing_library.phtml';
        	$library_content = $this->load->view($AJAX_VIEW_FILE, $data, true);
		}
		//echo 1; exit;
		//echo json_encode( array('html'=>$content, 'current_page'=>$page) );
        echo json_encode( array('html'=>$listingContent,
								"slab"=>$versepointer[$id]['slab_for_current_id'],
								"dest_verse"=>$id,
								"library"=>$library_content,
								'filtercat'=>$data['category_html']) );
	}
	
	public function get_all_note_bookmark_highilights($showeditdelete='',$id='')
	{
		$wh	= "";
		$cat	= $this->input->post('cat');
		if($cat!='')
		{
			$cat	= decrypt($cat);
			$wh	= " AND n.i_category = '".$cat."'";
			$wh1	= " AND n.s_category = '".$cat."'";
		}	
		
		$data['showeditdelete']	= $showeditdelete;
		//$data['category_html']	= makeOptionBibleCategory();
		
		$data['bible_library']	= $this->holy_place_model->get_note_bkmark_hilits('all',$wh,$wh1);
		#pr($data['note_arr'],1);
		$AJAX_VIEW_FILE = 'logged/holy_place/ajax_holy_place/ajax_listing_library.phtml';
        
		$listingContent = $this->load->view($AJAX_VIEW_FILE, $data, true);
        echo json_encode( array('html'=>$listingContent,
								"slab"=>$versepointer[$id]['slab_for_current_id'],
								"dest_verse"=>$id,
								"library"=>$library_content,
								'filtercat'=>$data['category_html'],'success'=>true) );
		exit;
	}
	
	public function getting_slab_from_verse($id='')
	{
		
		$verse_id	= $this->input->post('verseid');
		$versepointer	= $this->holy_place_model->get_array_of_verse_pointer($verse_id);
		
		echo json_encode(array( "slab"=>$versepointer[$verse_id]['slab_for_current_id'],
								"dest_verse"=>$verses[0]['verseid'],
								"success"=>true));
		exit;
		
		
	}
	

	
	### five a day 
	
	public function five_a_day() 
    {
        try
        {
                  
            $posted=array();
            $this->data["posted"]=$posted;/*don't change*/    
            $data = $this->data;      
            $this->data["MAIN_MENU_SELECTED"] = 6;
            parent::_set_title('::: COGTIME Xtian network :::');
            parent::_set_meta_desc('');
            parent::_set_meta_keywords('');
        
            
            parent::_add_js_arr( array( 'js/ddsmoothmenu.js',
                                        'js/switch.js','js/animate-collapse.js',
                                        'js/lightbox.js','js/jquery.dd.js','js/jquery-ui-1.8.2.custom.min.js',
                                        'js/stepcarousel.js'
                                        ));
                                        
            parent::_add_css_arr( array('css/jquery-ui-1.8.2.custom.css',
                                          'css/dd.css') );
            
			$i_profile_id = intval(decrypt($this->session->userdata('user_id')));
			$data['is_first_login_in_a_day'] = '';
			
			if($this->session->userdata('is_first_login_checked') == 'false'){
				$data['is_first_login_in_a_day'] = $this->users_model->check_user_first_login_in_a_day($i_profile_id);
				//echo $data['is_first_login_in_a_day'];
			}
			
			
			if($data['is_first_login_in_a_day'] == 'true'){
				$this->bible_fruits_model->generate_fruit_list_per_user_id_date();
				$data['five_fruits_arr'] = $this->bible_fruits_model->get_fruit_list($i_profile_id);
			}
			else{
				$data['five_fruits_arr'] = $this->bible_fruits_model->get_fruit_list($i_profile_id);
			}
           
		   
   
            $VIEW = "logged/holy_place/five_a_day.phtml"; 
            parent::_render($data, $VIEW);
        }
        catch(Exception $err_obj)
        {
           
        } 

    }
	
	
	
	public function bible_library()
	{
		try
        {
            $posted=array();
            $this->data["posted"]=$posted;/*don't change*/    
            $data = $this->data;      
            $this->data["MAIN_MENU_SELECTED"] = 6;
            parent::_set_title('::: COGTIME Xtian network :::');
            parent::_set_meta_desc('');
            parent::_set_meta_keywords('');
        
            
            parent::_add_js_arr( array( 'js/ddsmoothmenu.js',
                                        'js/switch.js','js/animate-collapse.js',
                                        'js/lightbox.js','js/jquery.dd.js','js/jquery-ui-1.8.2.custom.min.js',
                                        'js/stepcarousel.js'
                                        ));
                                        
            parent::_add_css_arr( array('css/jquery-ui-1.8.2.custom.css',
                                          'css/dd.css') );
            
			
			$data['category_html']	= makeOptionBibleCategory();
			$data['readingplan']	= $this->holy_place_model->get_reading_plan();
			$data['category']		= $this->holy_place_model->get_category_list();
			
			//pr($data['readingplan']);
			if(count($data['readingplan']) > 0)
			{
				$wh	= " s_testament='Old Testament'"	;
				$data['ot_book']	= makeOptionBibleBook($wh,'');
				$wh1	= " s_testament='New Testament'"	;
				$data['nt_book']	= makeOptionBibleBook($wh1,'');
				
				$i_profile_id 				= intval(decrypt($this->session->userdata('user_id')));
				$sql 						= "SELECT * FROM {$this->db->BIBLE_READING} WHERE i_user_id='".$i_profile_id."'";//exit;
				$data['read_bible_data']	= $this->db->query($sql)->result_array();
				
				//$data['verse']	= makeOptionBibleverse('','');
			}
			$VIEW = "logged/holy_place/library.phtml"; 
            parent::_render($data, $VIEW);
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }  
	}
	public function get_chapter_option($bookid)
	{
		$wh	= " i_book_id='".decrypt($bookid)."'";
		$chapter	= makeOptionBiblechapter($wh,'');
		$chapter	= "<option value=''>Select chapter</option>".$chapter;
		echo json_encode( array('success'=>true,'html'=>$chapter));exit;
	}
	
	public function get_verses_option($cid)
	{
		$wh	= " i_chapter_id='".decrypt($cid)."'";
		$verse	= makeOptionBibleverse($wh,'');
		echo json_encode( array('success'=>true,'html'=>$verse));exit;
	}
	
	public function generateReadingPlan()
	{
		try
		{
				$err		= '';
				#$plan_type = trim($this->input->post('plan_type'));
				$plan_type = trim($this->input->post('hd_plan_type'));
				
				$per_day_reading_old_testament = '';
				$per_day_reading_new_testament = '';
				
				 if(intval($plan_type) == 3)
				 {
				 	$d1	=	$this->input->post('start_to3');
					$d2	=	$this->input->post('start_to4');
					if($d1=='')
						$err	= "Please select date";
					if($d2=='')
						$err	= "Please select date";
						
					$date_to = get_db_dateformat(trim($d1),'/');
					$date_end = get_db_dateformat(trim($d2),'/');	
					
					$d1= strtotime($date_to);
					$date_to	= date('Y-m-d',mktime(0, 0, 0, date('m',$d1), date('d',$d1)-1,   date('y',$d1)));
					$d11 = strtotime($date_to);
					$d2= strtotime($date_end);
					$all = round(($d2 - $d11) / 60);
					$d = floor ($all / 1440);
				 }
				 else
				 {
					if($plan_type == 1)
					{
						if($this->input->post('start_to1')=='')
							$err	= "Please select date";
				 		$date_to = get_db_dateformat(trim($this->input->post('start_to1')),'/');
						
						$d1 = strtotime($date_to);
						$date_to	= date('Y-m-d',mktime(0, 0, 0, date('m',$d1), date('d',$d1)-1,   date('y',$d1)));
						
						//$d2= strtotime($date_to);
						$date_end = date('Y-m-d',mktime(0, 0, 0, date('m',$d1)+6, date('d',$d1),   date('y',$d1)));
					}
					else if($plan_type == 2)
				 	{
						if($this->input->post('start_to2')=='')
							$err	= "Please select date";
						 	
						$date_to = get_db_dateformat(trim($this->input->post('start_to2')),'/');
						
						$d1 = strtotime($date_to);
						$date_to	= date('Y-m-d',mktime(0, 0, 0, date('m',$d1), date('d',$d1)-1,   date('y',$d1)));
						
						//$d2= strtotime($date_to);
						$date_end = date('Y-m-d',mktime(0, 0, 0, date('m',$d1)+12, date('d',$d1),   date('y',$d1)));
					}
					else if($plan_type == 4)
					{
						
				 		$date_to = date('Y-m-d');//et_db_dateformat(trim($this->input->post('start_to1')),'/');
						
						$d1 = strtotime($date_to);
						$date_to	= date('Y-m-d',mktime(0, 0, 0, date('m',$d1), date('d',$d1),   date('y',$d1)));
						
						//$d2= strtotime($date_to);
						$date_end = date('Y-m-d',mktime(0, 0, 0, date('m',$d1), date('d',$d1)+365,   date('y',$d1)));
					}
					
					$t	= get_date_diff($date_to,$date_end); 
					
					$d	= $t[0]['difference'];
					## reading from  testament
					
				 }
				 if($err!='')
				 {
				 	echo json_encode( array('success'=>false,'sel_html'=>'','error'=>$err));exit;
				 }
				 else
				 {
					if($plan_type == 4){
						//echo $d; exit;
						$per_day_reading =  get_bible_reading_plan($d , 31102);
						$t_verse_arr1		= explode("###",$per_day_reading[0]); /// verse
						$t_verse_arr2		= explode("###",$per_day_reading[1]); /// days
						
						//pr($t_verse_arr1);
						//pr($t_verse_arr2);
						
						$return_nxt_start_data = $this->holy_place_model->insert_fixed_reading_plan(1,1,$t_verse_arr1[0] ,$t_verse_arr1[1] ); 
						$next_slot_data = explode('###', $return_nxt_start_data);
						
						#pr($next_slot_data[0]);
						//pr($next_slot_data[1]);
						//echo $next_slot_data[1]+$t_verse_arr1[0];
						//exit;
						$this->holy_place_model->insert_fixed_reading_plan($next_slot_data[0], $next_slot_data[1]+$t_verse_arr1[0],$t_verse_arr2[0] ,$t_verse_arr2[1],'Y' );
						//pr($t_verse_arr1);
						
					//pr($t_verse_arr2,1);
										
						$insert_arr['i_day_for_slab1_oldtestament']	= $t_verse_arr1[1];
						$insert_arr['i_slab1_verse_oldtestament']	= $t_verse_arr1[0];
						
						$insert_arr['i_user_id']					= intval(decrypt($this->session->userdata('user_id')));
						$insert_arr['dt_created_date']				= get_db_datetime();
						$insert_arr['dt_start_date']				= $date_to;
						$insert_arr['dt_end_date']					= $date_end;
						$insert_arr['i_plan_type '] 				= $plan_type;
						
						//pr($insert_arr,1);
										
					}
					else{
						$per_day_reading_old_testament =  get_bible_reading_plan($d , 23145);
						#pr($per_day_reading_old_testament);
						$old_t_verse_arr1		= explode("###",$per_day_reading_old_testament[0]);
						$old_t_verse_arr2		= explode("###",$per_day_reading_old_testament[1]);
						#echo $old_t_verse_arr1[1].'******'.$old_t_verse_arr1[2].'*****';
						
						
						
						
						$per_day_reading_new_testament =  get_bible_reading_plan($d , 7958);
						#pr($per_day_reading_new_testament,1);
						$new_t_verse_arr1		= explode("###",$per_day_reading_new_testament[0]);
						$new_t_verse_arr2		= explode("###",$per_day_reading_new_testament[1]);
						#echo '******'.$new_t_verse_arr1[1].'******'.$new_t_verse_arr2[2];
						
						$insert_arr['i_day_for_slab1_oldtestament']	= $old_t_verse_arr1[1];
						$insert_arr['i_slab1_verse_oldtestament']	= $old_t_verse_arr1[0];
						$insert_arr['i_day_for_slab2_oldtestament']	= $old_t_verse_arr2[1];
						$insert_arr['i_slab2_verse_oldtestament']	= $old_t_verse_arr2[0];
						$insert_arr['i_day_for_slab1_newtestament']	= $new_t_verse_arr1[1];
						$insert_arr['i_slab1_verse_newtestament']	= $new_t_verse_arr1[0];
						$insert_arr['i_day_for_slab2_newtestament']	= $new_t_verse_arr2[1];
						$insert_arr['i_slab2_verse_newtestament']	= $new_t_verse_arr2[0];
						$insert_arr['i_user_id']					= intval(decrypt($this->session->userdata('user_id')));
						$insert_arr['dt_created_date']				= get_db_datetime();
						$insert_arr['dt_start_date']				= $date_to;
						$insert_arr['dt_end_date']					= $date_end;
						$insert_arr['i_plan_type '] 					= $plan_type;
						#pr($insert_arr,1);
					}
					$this->holy_place_model->insert_reading_plan($insert_arr);
					
					echo json_encode( array('success'=>true,'sel_html'=>$sel_html,'error'=>''));exit;
				}
		}
		catch(Exception $err_obj)
        {
            
        } 
	}
	
	public function update_reading_plan()
	{
		$ot_book_id		= decrypt($this->input->post('ot_book'));
		$ot_chapter_id	= decrypt($this->input->post('ot_chappter'));
		$ot_verse_id	= decrypt($this->input->post('ot_verse'));
		
		$nt_book_id		= decrypt($this->input->post('nt_book'));
		$nt_chapter_id	= decrypt($this->input->post('nt_chappter'));
		$nt_verse_id	= decrypt($this->input->post('nt_verse'));
		
		if($ot_verse_id=='')
		{
			echo json_encode(array( "data"=>'Please select a verse for old testament',
									"success"=>false));
			exit;
		}
		if($nt_verse_id=='')
		{
			echo json_encode(array( "data"=>'Please select a verse for new testament',
									"success"=>false));
			exit;
		}
		
		$today	= date('Y-m-d');//'2013-05-29'
		$i_profile_id = intval(decrypt($this->session->userdata('user_id')));
		
		$sql = "SELECT v.id AS verseid , v.i_verses AS i_verses, v.i_chapter_id AS i_chapter_id,
						c.s_chapter AS s_chapter, c.i_book_id AS i_book_id, b.s_book_name AS s_book_name FROM 
                {$this->db->BIBLE_BOOK} AS b, 
				{$this->db->BIBLE_CHAPTER} AS c ,
				{$this->db->BIBLE_VERSES} AS v 
				WHERE b.id=c.i_book_id AND c.id=v.i_chapter_id AND v.id='".$ot_verse_id."'";
                /*LEFT JOIN {$this->db->USERS} u on u.id=p.i_user_id*/
		#echo $sql;exit;		
        $res = $this->db->query($sql)->result_array();
		$sql = "SELECT v.id AS verseid , v.i_verses AS i_verses, v.i_chapter_id AS i_chapter_id,
						c.s_chapter AS s_chapter, c.i_book_id AS i_book_id, b.s_book_name AS s_book_name FROM 
                {$this->db->BIBLE_BOOK} AS b, 
				{$this->db->BIBLE_CHAPTER} AS c ,
				{$this->db->BIBLE_VERSES} AS v 
				WHERE b.id=c.i_book_id AND c.id=v.i_chapter_id AND v.id='".$nt_verse_id."'";
                /*LEFT JOIN {$this->db->USERS} u on u.id=p.i_user_id*/
		#echo $sql;exit;		
        $res_n = $this->db->query($sql)->result_array();
		$sql_update	= "UPDATE {$this->db->BIBLE_READING_PLAN} SET 	i_verse_no_till='".$res[0]['verseid']."',
												i_verse_till='".$res[0]['i_verses']."',
												i_chapter_no_till='".$res[0]['i_chapter_id']."',
												s_chapter_till='".$res[0]['s_chapter']."',
												i_book_id_till='".$res[0]['i_book_id']."',
												s_book_name_till='".$res[0]['s_book_name']."',
												s_type_testament='Old Testament',
												s_chapter_till_n='".$res_n[0]['s_chapter']."',
												i_chapter_no_till_n='".$res_n[0]['i_chapter_id']."',
												i_book_id_till_n='".$res_n[0]['i_book_id']."',
												s_book_name_till_n='".$res_n[0]['s_book_name']."',
												i_verse_no_till_n='".$res_n[0]['verseid']."',
												i_verse_till_n='".$res_n[0]['i_verses']."'
												WHERE dt_date='".$today."' AND i_user_id='".$i_profile_id."'";
												
		//$this->db->query($sql_update);
		
		
		$sql = "SELECT * FROM {$this->db->BIBLE_READING} WHERE i_user_id='".$i_profile_id."'";//exit;
		$read_bible	= $this->db->query($sql)->result_array();
		
		$d1			= strtotime($today);
		$dtstart	= date('Y-m-d',mktime(0, 0, 0, date('m',$d1), date('d',$d1)+1,   date('y',$d1)));
		
		$t	= get_date_diff($dtstart,$read_bible[0]['dt_end_date']); 
		$d	= $t[0]['difference'];
		
		
		/*$sql = "SELECT COUNT(*) AS verse_no FROM {$this->db->BIBLE_VERSES} AS v, {$this->db->BIBLE_CHAPTER} AS c, {$this->db->BIBLE_BOOK} AS b 
				WHERE b.s_testament='Old Testament' AND b.id=c.i_book_id AND c.id=v.i_chapter_id AND v.id <= 23145 AND v.id> '".$nt_verse_id."'";
		$ot_verse_no	= $this->db->query($sql)->result_array();*/
		$ot_verse_id	= $ot_verse_id+1;
		$ot_verse_no[0]['verse_no']	= 23145-$ot_verse_id;
		$per_day_reading_old_testament =  get_bible_reading_plan($d , $ot_verse_no[0]['verse_no']);
		#pr($per_day_reading_old_testament);
		$old_t_verse_arr1		= explode("###",$per_day_reading_old_testament[0]);
		$old_t_verse_arr2		= explode("###",$per_day_reading_old_testament[1]);
		
		$nt_verse_id	= $nt_verse_id+1;
		/*$sql = "SELECT COUNT(*) AS verse_no FROM {$this->db->BIBLE_VERSES} AS v, {$this->db->BIBLE_CHAPTER} AS c, {$this->db->BIBLE_BOOK} AS b 
				WHERE b.s_testament='New Testament' AND b.id=c.i_book_id AND c.id=v.i_chapter_id AND v.id <= 31102 AND v.id> '".$nt_verse_id."'";
		
		$nt_verse_no	= $this->db->query($sql)->result_array();*/
		$nt_verse_no[0]['verse_no']	= 31102-$nt_verse_id;
		$per_day_reading_old_testament =  get_bible_reading_plan($d , $nt_verse_no[0]['verse_no']);
		#pr($per_day_reading_old_testament,1);
		$new_t_verse_arr1		= explode("###",$per_day_reading_old_testament[0]);
		$new_t_verse_arr2		= explode("###",$per_day_reading_old_testament[1]);
		
		
		
		$update_arr['i_day_for_slab1_oldtestament']	= $old_t_verse_arr1[1];
		$update_arr['i_slab1_verse_oldtestament']	= $old_t_verse_arr1[0];
		$update_arr['i_day_for_slab2_oldtestament']	= $old_t_verse_arr2[1];
		$update_arr['i_slab2_verse_oldtestament']	= $old_t_verse_arr2[0];
		$update_arr['i_day_for_slab1_newtestament']	= $new_t_verse_arr1[1];
		$update_arr['i_slab1_verse_newtestament']	= $new_t_verse_arr1[0];
		$update_arr['i_day_for_slab2_newtestament']	= $new_t_verse_arr2[1];
		$update_arr['i_slab2_verse_newtestament']	= $new_t_verse_arr2[0];
		$update_arr['dt_updated_date']				= $dtstart;//date('Y-m-d');
		
		$update_arr['dt_start_date']				= $dtstart;
		$update_arr['dt_end_date']					= $read_bible[0]['dt_end_date'];
		
		$wh	=	array("i_user_id"=> $i_profile_id);
		//$this->holy_place_model->update_reading_plan($update_arr,$wh);
		
		
 		$sql_del	= "DELETE FROM cg_bible_reading_plan WHERE i_user_id= '".$i_profile_id."' AND dt_date>='".$update_arr['dt_updated_date']."'";
		$this->db->query($sql_del);
		//exit;
		$st_old_vid	= $ot_verse_id;
		$st_new_vid	= $nt_verse_id;
		
		$this->load->library('CallSp');
		#echo $read_bible[0]['dt_end_date'];exit;
		$sqlStartImmediately 	=   "CALL test('".$old_t_verse_arr1[1]."','".$old_t_verse_arr1[0]."','".$old_t_verse_arr2[1]."','".$old_t_verse_arr2[0]."','".$new_t_verse_arr1[1]."','".$new_t_verse_arr1[0]."', '".$new_t_verse_arr2[1]."' ,'".$new_t_verse_arr2[0]."', '".$i_profile_id."', '".$today."', '".$read_bible[0]['dt_end_date']."','".$st_old_vid."','".$st_new_vid."')";
		$sp   					=   new CallSp();
		$sp->executeStoreProcedure( $sqlStartImmediately );	
		
		echo json_encode(array( "data"=>'Bible reading plan has been successfully updated',
									"success"=>true));
		exit;
	}
	
	public function delete_reading_plan()
	{
		$this->holy_place_model->delete_reading_plan();
		header('Location:'.base_url().'holy-place/read-bible.html');
	}
	
	
	
	public function add_category()
	{
		$err	= array();
		if($this->input->post('add_category') == '')
		{
			$err['err_addcat']	= "Required field";
		}
		
		if(count($err)>0)
		{
			echo json_encode(array( "data"=>$err,
									"success"=>false));
			exit;
		}
		else
		{
			
			$insert_arr['i_user_id']	= $this->i_profile_id;
			$insert_arr['s_category']	= $this->input->post('add_category');
			$this->holy_place_model->add_bible_category($insert_arr);
			$data['category']		= $this->holy_place_model->get_category_list();
			
			
			$AJAX_VIEW_FILE = 'logged/holy_place/ajax_holy_place/ajax_listing_category.phtml';
        	$listingContent = $this->load->view($AJAX_VIEW_FILE, $data, true);
			echo json_encode(array( "data"=>"Category has been successfully added",
									"html"=>$listingContent,
									"success"=>true));
			exit;
		}
	}
	
	public function show_edit_cat($nid)
	{
		$wh	= "";
		if($nid!='')
		{
			$id	= decrypt($nid);
			$wh	= " AND id = '".$id."'";
		}	
		$note_arr	= $this->holy_place_model->get_category_list($wh);
		
		foreach($note_arr as $val)
		{
			$cat	= $val['s_category'];
		}
		
        echo json_encode( array('category'=>$cat) );
	}
	
	public function edit_category()
	{
		$err	= array();
		if($this->input->post('edit_category') == '')
		{
			$err['err_editcat']	= "Required field";
		}
		
		if(count($err)>0)
		{
			echo json_encode(array( "data"=>$err,
									"success"=>false));
			exit;
		}
		else
		{
			
			$id	= decrypt($this->input->post('catid'));
			$insert_arr['s_category']	= $this->input->post('edit_category');
			$wh	= array("id"=>$id);
			$this->holy_place_model->edit_bible_category($insert_arr,$wh);
			
			$data['category']		= $this->holy_place_model->get_category_list();
			
			$AJAX_VIEW_FILE = 'logged/holy_place/ajax_holy_place/ajax_listing_short_category.phtml';
        	$listingContent = $this->load->view($AJAX_VIEW_FILE, $data, true);
			echo json_encode(array( "data"=>"Category has been successfully added",
									"html"=>$listingContent,
									"success"=>true));
			exit;
		}
	}
	
	public function remove_category($id)
	{
		$wh	= array('id'=>decrypt($this->input->post('deleteCatId')));
		$this->holy_place_model->delete_category($wh);
		//echo $this->db->last_query();exit;
		$data['category']		= $this->holy_place_model->get_category_list();
		$AJAX_VIEW_FILE = 'logged/holy_place/ajax_holy_place/ajax_listing_short_category.phtml';
		$listingContent = $this->load->view($AJAX_VIEW_FILE, $data, true);
		echo json_encode(array( "data"=>"Category has been successfully added",
								"html"=>$listingContent,
								"success"=>true));
		exit;
	}
	
	public function get_all_category(){
		
		    $data['category']		= $this->holy_place_model->get_category_list();
			
			
			$AJAX_VIEW_FILE = 'logged/holy_place/ajax_holy_place/ajax_listing_short_category.phtml';
        	$listingContent = $this->load->view($AJAX_VIEW_FILE, $data, true);
			echo json_encode(array( "data"=>"Category has been successfully listed.",
									"html"=>$listingContent,
									"success"=>true));
			exit;
	}
	
	### new added for new design
	public function update_reading_plan_by_day()
	{
		$dt_date		= ($this->input->post('dt_to_be_updated'));
		$is_read_updated = $this->input->post('is_read_updated');
		
		
		$updated_flag = ($is_read_updated == 1)?0:1;
		
		/*if($dt_date > date('Y-m-d')){
			
			echo json_encode(array( "data"=>'You cannot update future bible reading plan.',
									"success"=>false));
		}
		else*/{
		//$today	= date('Y-m-d');//'2013-05-29'
		$i_profile_id = intval(decrypt($this->session->userdata('user_id')));
		$sql_update	= "UPDATE {$this->db->BIBLE_READING_PLAN} SET 	is_read_updated = {$updated_flag}
												WHERE dt_date='".$dt_date."' AND i_user_id='".$i_profile_id."'";
												
		$this->db->query($sql_update);
		echo json_encode(array( "data"=>'Bible reading plan has been successfully updated',
								"updated_flag"=>$updated_flag,
								"is_read_updated"=>$is_read_updated,
									"success"=>true));
		}
		exit;
	}
	
}   // end of controller...

