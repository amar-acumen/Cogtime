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
            $order_by = " `i_order` DESC";
            $result = $this->holy_place_model->daily_verse_list($page,$this->pagination_per_page,$order_by);
			//pr($result,1);
            $resultCount = count($result);
            
            $total_rows =  $this->holy_place_model->total_day_verse();
         
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
	function maintain_displayorder_ajax($selected_language, $page=0)
    {
        //sleep(2);
		$this->load->model('holy_place_model');
			$actionID = $this->input->post('rid');
			$status = $this->input->post('status');
			
			
			# retrieving  info...
			$info_arr = $this->holy_place_model->get_by_id($actionID);
	
			$this->load->model("utility_model");
			$tbl=$this->db->day_verse;
	
		  //  $WHERE_COND_BEGIN = " `i_parent_id` = {$PARENT_CATEGORY_ID} AND `i_is_active` = 1 ";
			$this->utility_model->Ranking($status, $actionID, $WHERE_COND_BEGIN,$tbl);
		
            $order_by = " `i_order` DESC ";
			$result = $this->holy_place_model->daily_verse_list($page,$this->pagination_per_page, $order_by);
            $resultCount = count($result);
            //$total_rows = $this->mail_domains_model->gettotal_info($WHERE_COND);
			$total_rows = $this->holy_place_model->total_day_verse();
			
		/*	$order_by = " `id` ASC ";
		   	$result = $this->cms_model->cms_list($s_where,$page,$this->pagination_per_page,$order_by);
            $resultCount = count($result);
			#echo $this->db->last_query(); exit;
			$total_rows = $this->cms_model->cms_list_count($s_where);*/
			
			if( ( !is_array($result) || !count($result) ) && $total_rows ) {
                $page = $page - $this->pagination_per_page;
                
                $result = $this->holy_place_model->daily_verse_list($page, $this->pagination_per_page,$ORDER_BY);
            }
			
			#Jquery Pagination Starts
           	$this->load->library('jquery_pagination');
            $config['base_url'] = base_url()."admin/holy_place/daily_bible_verse/ajax_pagination";
            $config['total_rows'] = $total_rows;
            $config['per_page'] = $this->pagination_per_page;
            $config['uri_segment'] = 6;
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

            // getting   listing...
			$data['info_arr'] = $result;
			$data['no_of_result'] = $total_rows;
			$data['current_page'] = $page;
          
			# loading the view-part...
          echo  $this->load->view('admin/holy_place/verse_ajax.phtml', $data,TRUE);
		
	}
/*	public function add(){
		
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
		//$sql=$this->db->query("update {$this->db->day_verse} set publish_status=true where id=1");
		echo json_encode(array('success'=> true));
	}
    */
	
	public function ajax_suggest_verse()
	{
	$sql = "SELECT *,v.id AS verseid FROM 
					{$this->db->BIBLE_BOOK} AS b, 
					{$this->db->BIBLE_CHAPTER} AS c ,
					{$this->db->BIBLE_VERSES} AS v 
					WHERE b.id=c.i_book_id AND c.id=v.i_chapter_id order by RAND() Limit 30";
					$data['result_arr'] = $this->db->query($sql)->result_array();
					
        $view_file = $this->load->view("admin/holy_place/suggested_verse_ajax.phtml", $data, true);

        echo json_encode(array('response' => $view_file));
	}
  
  public function add_verse()
  {
  $this->load->model('holy_place_model');
	if($this->input->post('verse_id')!= '')
	{
		$posted['verse_id']=$this->input->post('verse_id');
	//pr($posted['verse_id'],1);
	$count = 0;
		foreach($posted['verse_id'] as $verse_id)
		{
			$result=get_verse_by_id($verse_id);
			$arr['s_verse']   = $result['s_text'];
			$arr['s_book_name'] = $result['s_book_name'];
			$arr['bible_chapter_no'] = $result['s_chapter'];
			$arr['bible_verse_no'] = $result['i_verses'];
			 
			if($count == 0)
			{
			$vid=$this->holy_place_model->add_day_verse($arr);
			}
			else{
			$this->db->insert($this->db->day_verse, $arr);
			}
			
			$count ++;
			
			
		}
		
	$sql=$this->db->query("select count(*) as count from cg_day_verse where publish_status='1'");
	$res=$sql->result_array();
	if($res['0']['count'] == 0)
	{
		$sql1=$this->db->query("update cg_day_verse set publish_status='1' where id=".$vid);
	}
	
	
	echo json_encode(array('success'=>true,'msg'=>'verse has been added successfully.'));
	exit;
	}
	else
	{
	echo json_encode(array('success'=>false,'msg'=>'Please select verse.'));
	exit;
	}
  }
  
  public function delete_verse($page)
  {
  $this->load->model('holy_place_model');
  $id=$this->input->post('id');
  $status=$this->input->post('status');
        //$current_page = $this->input->post('current_page');
        
        /*if($this->session->userdata('current_page_intercession'))
            echo "set session";
        else
            echo "not set";
        */
        
        //$this->session->set_userdata('current_page_intercession',$current_page);
        if($status != 1)
		{
        $this->holy_place_model->delete_by_id($id);
        
        ob_start();
        $this->ajax_pagination($page);
        $html = ob_get_contents();
        ob_end_clean();
        
        
        //$this->session->set_userdata('current_page_intercession','');
        
        $result='success';
        echo json_encode(array('result'=>$result,'response'=>$html));
		}
		else{
		 echo json_encode(array('result'=>'Failure','msg'=>"You can't delete this verse.This is the currently published verse."));
		}
		
  }
  public function ajax_get_chapter()
  {
  
	$book=$this->input->post('book');
	$where="i_book_id=".decrypt($book);
	$options="<option value='-1'>---</option>";
	if($book != '-1')
	{
	$options.=makeOptionBiblechapter($where);
	}
	echo json_encode(array('response'=>$options));
  }
  
   public function ajax_get_start_verse()
  {
	$chap=$this->input->post('chapter');
	
	$where="i_chapter_id=".decrypt($chap);
	$options="<option value='-1'>---</option>";
	if($chap != '-1')
	{
	$options.=makeOptionBibleStartVerse($where);
	}
	echo json_encode(array('response'=>$options));
  }
  public function ajax_get_end_verse()
  {
	$chap=$this->input->post('chap');
	$verse=$this->input->post('verse');
	$book=$this->input->post('book');
	$where="i_chapter_id=".decrypt($chap)." and id>".decrypt($verse)." limit 3";
	$options="<option value='-1'>---</option>";
	if($verse !="-1")
	{
	$options.=makeOptionBibleEndVerse($where);
	$sql="SELECT *,v.id AS verseid FROM 
					{$this->db->BIBLE_BOOK} AS b, 
					{$this->db->BIBLE_CHAPTER} AS c ,
					{$this->db->BIBLE_VERSES} AS v 
					WHERE b.id=c.i_book_id AND c.id=v.i_chapter_id AND b.id='".decrypt($book)."' AND c.id='".decrypt($chap)."' AND v.id='".decrypt($verse)."'";
	$query=$this->db->query($sql);
	//echo $this->db->last_query();
	$res=$query->result_array();
	//pr($res,1);
	$html=" <p class='green-title'>Bible Verse : [ <a  href='read-bible-chapter-view.php'>".$res['0']['s_book_name']." ".$res['0']['s_chapter'].":".$res['0']['i_verses']."</a> ]</p><p>".$res['0']['s_text']."</p>";
	echo json_encode(array('response'=>$options,'html'=>$html));
	}
	else
	{
	echo json_encode(array('response'=>$options));
	}
  }
  
  public function ajax_change_verse_preview()
  {
	$chap=decrypt($this->input->post('chap'));
	$verse_end=decrypt($this->input->post('verse'));
	$book=decrypt($this->input->post('book'));
	$start_verse=decrypt($this->input->post('start_verse'));
	if(($this->input->post('verse'))!='-1')
	{
	
	$limit=$verse_end-$start_verse;
	$sql="SELECT *,v.id AS verseid FROM 
					{$this->db->BIBLE_BOOK} AS b, 
					{$this->db->BIBLE_CHAPTER} AS c ,
					{$this->db->BIBLE_VERSES} AS v 
					WHERE b.id=c.i_book_id AND c.id=v.i_chapter_id AND b.id='".$book."' AND c.id='".$chap."' AND v.id Between ".$start_verse." AND ".$verse_end;
					$query=$this->db->query($sql);
	//echo $this->db->last_query();
	$res=$query->result_array();
	//pr($res,1);
	$html=" <p class='green-title'>Bible Verse : [ <a  href='read-bible-chapter-view.php'>".$res['0']['s_book_name']." ".$res['0']['s_chapter'].":".$res['0']['i_verses']."-".$res[$limit]['i_verses']."</a> ]</p>";
	foreach($res as $val)
	{
		$html.="<p>".$val['s_text']."</p>";
	}
	}
	
	else{
	$sql="SELECT *,v.id AS verseid FROM 
					{$this->db->BIBLE_BOOK} AS b, 
					{$this->db->BIBLE_CHAPTER} AS c ,
					{$this->db->BIBLE_VERSES} AS v 
					WHERE b.id=c.i_book_id AND c.id=v.i_chapter_id AND b.id='".$book."' AND c.id='".$chap."' AND v.id='".$start_verse."'";
	$query=$this->db->query($sql);
	//echo $this->db->last_query();
	$res=$query->result_array();
	//pr($res,1);
	$html=" <p class='green-title'>Bible Verse : [ <a  href='read-bible-chapter-view.php'>".$res['0']['s_book_name']." ".$res['0']['s_chapter'].":".$res['0']['i_verses']."</a> ]</p><p>".$res['0']['s_text']."</p>";
	}
	echo json_encode(array('html'=>$html));
  }
  
  public function add_manual_verse()
  {
  $chap=decrypt($this->input->post('s_chapter'));
	$end_verse=decrypt($this->input->post('end_verse'));
	$book=$this->input->post('s_book_name');
	$start_verse=decrypt($this->input->post('start_verse'));
	$s_i_verse=$this->input->post('s_verse');
	$e_i_verse=$this->input->post('e_verse');
	$i_chapter=$this->input->post('i_chapter');
	$this->load->model('holy_place_model');
	$arr['s_book_name']=$book;
	$arr['bible_chapter_no']=$i_chapter;
	if($this->input->post('end_verse')!='-1')
	{
	$arr['bible_verse_no']=$s_i_verse.'-'.$e_i_verse;
	$arr['s_verse']=$this->holy_place_model->get_verse_texts($start_verse,$end_verse);
	}
	else
	{
	$arr['bible_verse_no']=$s_i_verse;
	$arr['s_verse']=$this->holy_place_model->get_verse_texts($start_verse);
	}
	$this->db->insert('cg_day_verse',$arr);
	//echo $this->db->last_query();exit;
	echo json_encode(array('success'=>true,'msg'=>'verse added successfully.'));
  }
}