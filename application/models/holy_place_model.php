<?php
include_once(APPPATH.'models/base_model.php');
class Holy_place_model extends Base_model
{
    
    public function __construct() 
    {
        parent::__construct();
    }
    
   
    
	
	public function get_bible($where='',$i_start=null,$i_limit=null,$s_order_by='')
	{
		$limit  = (is_numeric($i_start) && is_numeric($i_limit))?" Limit ".intval($i_start).",".intval($i_limit):'';
        $s_order_by = ($s_order_by != '')?'ORDER BY '.$s_order_by :'ORDER BY b.id';
        $sql = "SELECT v.id AS verseid FROM 
                {$this->db->BIBLE_BOOK} AS b, 
				{$this->db->BIBLE_CHAPTER} AS c ,
				{$this->db->BIBLE_VERSES} AS v 
				WHERE b.id=c.i_book_id AND c.id=v.i_chapter_id {$where} {$s_order_by} {$limit}";
                /*LEFT JOIN {$this->db->USERS} u on u.id=p.i_user_id*/
		//echo $sql;exit;		
		
        $res = $this->db->query($sql)->result_array();
        return $res;
	}
	
	public function get_count_bible($where)
    {
        $sql = "SELECT COUNT(*) AS no  FROM {$this->db->BIBLE_BOOK} AS b, {$this->db->BIBLE_CHAPTER} AS c ,{$this->db->BIBLE_VERSES} AS v 
				WHERE b.id=c.i_book_id AND c.id=v.i_chapter_id {$where}";/*LEFT JOIN {$this->db->USERS} u on u.id=p.i_user_id*/
        $res = $this->db->query($sql)->result_array();
        return $res[0]['no'];
    }
	
	
	public function get_all_books($where='',$i_start=null,$i_limit=null,$s_order_by='')
	{
		$limit  = (is_numeric($i_start) && is_numeric($i_limit))?" Limit ".intval($i_start).",".intval($i_limit):'';
        $s_order_by = ($s_order_by != '')?'ORDER BY '.$s_order_by :'ORDER BY b.id';
       
        
        $sql = "SELECT id , s_book_name , s_testament FROM 
                {$this->db->BIBLE_BOOK} AS b WHERE 1 {$where} {$s_order_by} {$limit}";
                /*LEFT JOIN {$this->db->USERS} u on u.id=p.i_user_id*/
				
        $res = $this->db->query($sql)->result_array();
        return $res;
	}
	
	public function get_count_all_books($where)
    {
        $sql = "SELECT COUNT(*) AS no  FROM {$this->db->BIBLE_BOOK} AS b {$where}";/*LEFT JOIN {$this->db->USERS} u on u.id=p.i_user_id*/
        $res = $this->db->query($sql)->result_array();
        return $res[0]['no'];
    }
	
	
	public function get_books_chapter($where='',$i_start=null,$i_limit=null,$s_order_by='')
	{
		$limit  = (is_numeric($i_start) && is_numeric($i_limit))?" Limit ".intval($i_start).",".intval($i_limit):'';
        $s_order_by = ($s_order_by != '')?'ORDER BY '.$s_order_by :'ORDER BY id';
       
        
        $sql = "SELECT s_chapter FROM 
                {$this->db->BIBLE_CHAPTER} WHERE 1 {$where} {$s_order_by} {$limit}";
                /*LEFT JOIN {$this->db->USERS} u on u.id=p.i_user_id*/
				
        $res = $this->db->query($sql)->result_array();
        return $res;
	}
	
	public function get_array_of_verse_pointer($verseid='')
	{
		if($verseid	!= '')
			$wh	= " AND v.id='".$verseid."'";
		 $sql = "SELECT *,v.id AS verseid FROM {$this->db->BIBLE_CHAPTER} AS c ,{$this->db->BIBLE_VERSES} AS v 
				WHERE c.id=v.i_chapter_id".$wh;
                /*LEFT JOIN {$this->db->USERS} u on u.id=p.i_user_id*/
				
        $res = $this->db->query($sql)->result_array();
       // pr($res);
		$i='';
		foreach($res as $val)
		{
			$i								= $val['verseid'];
			$arr[$i]['verseid']				= $val['verseid'];
			$arr[$i]['i_book_id']			= $val['i_book_id'];
			$arr[$i]['s_chapter']			= $val['s_chapter'];
			$arr[$i]['i_chapter_id']		= $val['i_chapter_id'];
			$arr[$i]['i_verses']			= $val['i_verses'];
			$arr[$i]['s_text']				= $val['s_text'];
			//$arr[$i]['remain']				= $val['verseid']%25;
			$slab_for_current_id			= $val['verseid']-($val['verseid']%25);
			
			if($slab_for_current_id == $val['verseid'])
				$slab_for_current_id	= $slab_for_current_id-25;
				
			$arr[$i]['slab_for_current_id']	= $slab_for_current_id;
			//$arr[$i]['slab_for_current_id']	= $total_slab-intval((31100-$val['verseid'])/25);
			
		}
		//pr($arr);
		return $arr;
	}
	
	public function get_chapter($paging,$id)
	{
		if($paging=="prev")
		{
			$sql = "SELECT v.*
							FROM {$this->db->BIBLE_VERSES} AS v 
							WHERE 1 AND v.i_chapter_id < '".$id."' ORDER BY v.id DESC LIMIT 0,1";
		}
		else if($paging=="next")
		{
			$sql = "SELECT v.*
							FROM {$this->db->BIBLE_VERSES} AS v 
							WHERE 1 AND v.i_chapter_id > '".$id."' ORDER BY v.id ASC LIMIT 0,1";
		}
		#echo $sql;		
		 $res = $this->db->query($sql)->result_array();
		 #pr($res);
		 return $res[0];
	}
	
	
	public function get_book($paging,$id)
	{
		if($paging=="prev")
		{
			$sql = "SELECT v.*
							FROM {$this->db->BIBLE_VERSES} AS v, {$this->db->BIBLE_CHAPTER} AS c 
							WHERE 1 AND v.i_chapter_id=c.id  AND c.i_book_id < '".$id."' ORDER BY v.id DESC LIMIT 0,1";
		}
		else if($paging=="next")
		{
			$sql = "SELECT v.*
							FROM {$this->db->BIBLE_VERSES} AS v , {$this->db->BIBLE_CHAPTER} AS c 
							WHERE 1 AND v.i_chapter_id=c.id AND c.i_book_id > '".$id."' ORDER BY v.id ASC LIMIT 0,1";
		}
		#echo $sql;		exit;
		 $res = $this->db->query($sql)->result_array();
		 #pr($res);
		 return $res[0];
	}
	
	public function add_bible_category($arr)
	{
		if($this->db->insert($this->db->BIBLE_CAT ,$arr))
			return true;
		else
			return false;
	}
	public function edit_bible_category($arr,$wh)
	{
		if($this->db->update($this->db->BIBLE_CAT ,$arr,$wh))
			return true;
		else
			return false;
	}
	public function delete_category($wh)
	{
		if($this->db->delete($this->db->BIBLE_CAT ,$wh))
		{
			$wh1	= array('i_category'=>$wh['id']);
			$wh2	= array('s_category'=>$wh['id']);
			
			$this->delete_note($wh1);
			$this->delete_bookmark($wh2);
			$this->delete_hilits($wh2);
			
			return true;
		}
		else
			return false;
	}
	public function add_note($arr)
	{
		if($this->db->insert($this->db->BIBLE_NOTE ,$arr))
			return true;
		else
			return false;
	}
	public function edit_note($arr,$wh)
	{
		if($this->db->update($this->db->BIBLE_NOTE ,$arr,$wh))
			return true;
		else
			return false;
	}
	public function delete_note($wh)
	{
		if($this->db->delete($this->db->BIBLE_NOTE ,$wh))
			return true;
		else
			return false;
	}
	
	public function add_highlights($arr)
	{
		$this->db->insert($this->db->BIBLE_HILITS ,$arr);
		$last_id	= $this->db->insert_id();
		if($last_id)
			return $last_id;
		else
			return false;
	}
	public function add_bookmark($arr)
	{
		$i_profile_id = intval(decrypt($this->session->userdata('user_id')));
		
		$sql	= "SELECT COUNT(*) AS verse FROM {$this->db->BIBLE_BOOKMARK} WHERE  i_verses_id = '".$arr['i_verses_id']."' 
					AND i_user_id='".$i_profile_id."' AND i_page_bookmark='".$arr['i_page_bookmark']."'";
				
		$res 	= $this->db->query($sql)->result_array();
		if($res[0]['verse']>0)
		{
			return false;
		}
		else
		{
			if($this->db->insert($this->db->BIBLE_BOOKMARK ,$arr))
				return true;
			else
				return false;
		}
	}
	
	public function delete_bookmark($wh)
	{
		if($this->db->delete($this->db->BIBLE_BOOKMARK ,$wh))
			return true;
		else
			return false;
	}
	public function delete_hilits($wh)
	{
		if($this->db->delete($this->db->BIBLE_HILITS ,$wh))
			return true;
		else
			return false;
	}
	
	public function get_all_note_by_user($where='',$i_start=null,$i_limit=null,$s_order_by='')
	{
		$limit  = (is_numeric($i_start) && is_numeric($i_limit))?" Limit ".intval($i_start).",".intval($i_limit):'';
        $s_order_by = ($s_order_by != '')?'ORDER BY '.$s_order_by :'ORDER BY bookid';
       
        $i_profile_id = intval(decrypt($this->session->userdata('user_id')));
		
        $sql = "SELECT b.id AS bookid, b.s_book_name AS book_name, c.id, c.s_chapter AS chapter,v.*,v.id AS vid ,n.s_note AS s_note,
					n.id AS noteid , n.i_category AS i_category  
					FROM 
					{$this->db->BIBLE_NOTE} AS n, 
					{$this->db->BIBLE_VERSES} AS v, 
					{$this->db->BIBLE_CHAPTER} AS c, 
					{$this->db->BIBLE_BOOK} AS b
						WHERE b.id = c.i_book_id AND
							c.id=v.i_chapter_id AND 
							v.id= n.i_verses_id AND 
							n.i_user_id='".$i_profile_id."' {$where} {$s_order_by} {$limit}";
                /*LEFT JOIN {$this->db->USERS} u on u.id=p.i_user_id*/
				
        $res = $this->db->query($sql)->result_array();
		foreach($res as $val)
		{
			$arr_note[$val['book_name']][]	= array('chapter'=> $val['chapter'],
													'verses_no'=> $val['i_verses'],
													'verses_id'=> $val['vid'],
													'note_text'=> $val['s_note'],
													'note_id'=> $val['noteid'],
													'i_category'=> $val['i_category']);
		}
		#pr($arr_note);exit;
        return $arr_note;
	}
	
	public function get_all_bookmark_by_user($where='',$i_start=null,$i_limit=null,$s_order_by='')
	{
		$limit  = (is_numeric($i_start) && is_numeric($i_limit))?" Limit ".intval($i_start).",".intval($i_limit):'';
        $s_order_by = ($s_order_by != '')?'ORDER BY '.$s_order_by :'ORDER BY bookid';
       
        $i_profile_id = intval(decrypt($this->session->userdata('user_id')));
		
        $sql = "SELECT b.id AS bookid, b.s_book_name AS book_name, c.id, c.s_chapter AS chapter,v.*,v.id AS vid ,bk.id AS bk_id,
					bk.* FROM 
					{$this->db->BIBLE_BOOKMARK} AS bk, 
					{$this->db->BIBLE_VERSES} AS v, 
					{$this->db->BIBLE_CHAPTER} AS c, 
					{$this->db->BIBLE_BOOK} AS b
						WHERE b.id = c.i_book_id AND
							c.id=v.i_chapter_id AND 
							v.id= bk.i_verses_id AND 
							bk.i_user_id='".$i_profile_id."' {$where} {$s_order_by} {$limit}";
                /*LEFT JOIN {$this->db->USERS} u on u.id=p.i_user_id*/
				
        $res = $this->db->query($sql)->result_array();
		foreach($res as $val)
		{
			$arr_note[$val['book_name']][]	= array('chapter'=> $val['chapter'],
													'verses_no'=> $val['i_verses'],
													'verses_id'=> $val['vid'],
													's_text'=> $val['s_text'],
													'bk_id'=> $val['bk_id']);
		}
		#pr($arr_note);exit;
        return $arr_note;
	}
	
	public function get_all_highilights_by_user($where='',$i_start=null,$i_limit=null,$s_order_by='')
	{
		$limit  = (is_numeric($i_start) && is_numeric($i_limit))?" Limit ".intval($i_start).",".intval($i_limit):'';
        $s_order_by = ($s_order_by != '')?'ORDER BY '.$s_order_by :'ORDER BY bookid';
       
        $i_profile_id = intval(decrypt($this->session->userdata('user_id')));
		
       $sql = "SELECT b.id AS bookid, b.s_book_name AS book_name, c.id, c.s_chapter AS chapter,v.*,v.id AS vid ,n.* ,
					n.id AS hid
					FROM 
					{$this->db->BIBLE_HILITS} AS n, 
					{$this->db->BIBLE_VERSES} AS v, 
					{$this->db->BIBLE_CHAPTER} AS c, 
					{$this->db->BIBLE_BOOK} AS b
						WHERE b.id = c.i_book_id AND
							c.id=v.i_chapter_id AND 
							v.id= n.i_verses_id AND 
							n.i_user_id='".$i_profile_id."' {$where} {$s_order_by} {$limit}";
                /*LEFT JOIN {$this->db->USERS} u on u.id=p.i_user_id*/
				
        $res = $this->db->query($sql)->result_array();
		foreach($res as $val)
		{
			$arr_note[$val['book_name']][]	= array('chapter'=> $val['chapter'],
													'verses_no'=> $val['i_verses'],
													'verses_id'=> $val['vid'],
													'note_text'=> trim($val['s_text']),
													'hltsid'=> $val['hid'],
													'i_pos1'=> $val['i_pos1'],
													'i_pos2'=> $val['i_pos2']);
		}
		#pr($arr_note);exit;
        return $arr_note;
	}
	
	public function get_all_note_arr_by_verse_for_user($wh='')
	{
		$i_profile_id = intval(decrypt($this->session->userdata('user_id')));
		
		$sql	= "SELECT * FROM {$this->db->BIBLE_NOTE} WHERE i_user_id='".$i_profile_id."' {$wh}";
				
		$res 	= $this->db->query($sql)->result_array();
		
		foreach($res AS $val)
		{
			$arr[$val['i_verses_id']][]	= $val['s_note'];
		}
		return $arr;
	}
	public function get_all_bkmark_arr_by_verse_for_user($wh='')
	{
		$i_profile_id = intval(decrypt($this->session->userdata('user_id')));
		
		$sql	= "SELECT * FROM {$this->db->BIBLE_BOOKMARK} WHERE i_user_id='".$i_profile_id."' {$wh}";
				
		$res 	= $this->db->query($sql)->result_array();
		
		foreach($res AS $val)
		{
			$arr[$val['i_verses_id']]	= $val['i_verses_id'];
		}
		return $arr;
	}
	public function get_all_pagebkmark_arr_for_user($wh='')
	{
		$i_profile_id = intval(decrypt($this->session->userdata('user_id')));
		
		$sql	= "SELECT * FROM {$this->db->BIBLE_BOOKMARK} WHERE i_user_id='".$i_profile_id."' {$wh} ORDER BY i_page_bookmark ASC";
				
		$res 	= $this->db->query($sql)->result_array();
		return $res;
	}
	public function get_all_hilits_arr_by_verse_for_user($wh='')
	{
		$i_profile_id = intval(decrypt($this->session->userdata('user_id')));
		
		$sql	= "SELECT * FROM {$this->db->BIBLE_HILITS} WHERE i_user_id='".$i_profile_id."' {$wh} ORDER BY i_verses_id, i_pos1";
				
		$res 	= $this->db->query($sql)->result_array();
		$i	=	0;
		foreach($res AS $val)
		{
			$wh	= " AND id='".$val['i_colorhidden']."'";
			$color	= $this->get_color_list($wh);
			$arr[$val['i_verses_id']][$i]['txt']		= $val['s_text'];
			$arr[$val['i_verses_id']][$i]['colocode']	= $color[0]['s_color_code'];
			$arr[$val['i_verses_id']][$i]['pos1']	= $val['i_pos1'];
			$arr[$val['i_verses_id']][$i]['pos2']	= $val['i_pos2'];
			$i++;
		}
		return $arr;
	}
	public function get_hilits_for_pos($wh='')
	{
		$i_profile_id = intval(decrypt($this->session->userdata('user_id')));
		
		$sql	= "SELECT * FROM {$this->db->BIBLE_HILITS} WHERE i_user_id='".$i_profile_id."' {$wh}";
				
		$res 	= $this->db->query($sql)->result_array();
		
		
		return $res;
	}
	
	public function get_verse_text_by_verse_id($id='')
	{
		$sql = "SELECT * FROM
				{$this->db->BIBLE_VERSES} 
				WHERE id='".$id."' ";
                /*LEFT JOIN {$this->db->USERS} u on u.id=p.i_user_id*/
		#echo $sql;exit;		
        $res = $this->db->query($sql)->result_array();
        return $res[0];
	}
	
	
	public function get_verse_txt_with_hilits($id='')
	{
		$i_profile_id = intval(decrypt($this->session->userdata('user_id')));
		$sql	= "SELECT v.id AS vid,v.s_text AS vtxt,h.s_text AS htxt, h.i_pos1 AS i_pos1, h.i_pos2 AS i_pos2 ,h.i_colorhidden,c.*
					FROM {$this->db->BIBLE_VERSES} AS v,{$this->db->BIBLE_HILITS} AS h, {$this->db->BIBLE_HILITS_COLOR} AS c
					 WHERE c.id=h.i_colorhidden AND  v.id=h.i_verses_id AND h.i_user_id='".$i_profile_id."' AND v.id='".$id."' {$wh} ORDER BY h.i_verses_id, h.i_pos1";
				
		$res 	= $this->db->query($sql)->result_array();

		$replacetxtlength	= 0;
		$i	= 0;
		foreach($res as $val)
		{
			if($i==0)
			{
				$vtxt	= 	$val['vtxt'];
			}
			$pos	= $replacetxtlength+$val['i_pos1'];
			$leng	= $val['i_pos2']-$val['i_pos1'];
			$replacetxt	= '<span style="background-color: '.$val['s_color_code'].'" id="'.$val['vid'].'">'.$val['htxt'].'</span>'; 
			$replacetxtlength	+=	strlen($replacetxt)-strlen($val['htxt']);
			$vtxt	= substr_replace($vtxt,$replacetxt,$pos,$leng);
			$i++;
		}

		return $vtxt;
		
	}
	
	
	
	public function total_old_testament_verse(){
		
		$SQL = " SELECT COUNT(*) as total_count FROM(
				  SELECT bv.id
				  
				  FROM cg_bible_book as bb
				  LEFT JOIN cg_bible_chapter bc ON bc.i_book_id = bb.id
				  LEFT JOIN cg_bible_verses bv ON bv.i_chapter_id = bc.id
				  WHERE bb.s_testament = 'Old Testament') as derived_tbl 
				";
		$result_array 	= $this->db->query($sql)->result_array();
		
		return $result_array[0]['total_count'];
		
	}
	
	public function total_new_testament_verse(){
		
		$SQL = " SELECT COUNT(*) as total_count FROM(
				  
				  SELECT bv.id
				  
				  FROM cg_bible_book as bb
				  LEFT JOIN cg_bible_chapter bc ON bc.i_book_id = bb.id
				  LEFT JOIN cg_bible_verses bv ON bv.i_chapter_id = bc.id
				  WHERE bb.s_testament = 'New Testament') as derived_tbl
				";
		$result_array 	= $this->db->query($sql)->result_array();
		
		return $result_array[0]['total_count'];
	}
	
	public function insert_reading_plan($insertarr){
	
		$this->db->insert('cg_bible_reading', $insertarr); //echo $this->db->last_query();
		
	}
	public function update_reading_plan($insertarr,$wh){
	
		$this->db->update($this->db->BIBLE_READING, $insertarr,$wh); //echo $this->db->last_query();
		
	}
	public function get_reading_plan($where='',$i_start=null,$i_limit=null,$s_order_by='')
	{
		$limit  = (is_numeric($i_start) && is_numeric($i_limit))?" Limit ".intval($i_start).",".intval($i_limit):'';
        $s_order_by = ($s_order_by != '')?'ORDER BY '.$s_order_by :'ORDER BY id';
       
        $i_profile_id = intval(decrypt($this->session->userdata('user_id')));
        $sql = "SELECT * FROM 
                {$this->db->BIBLE_READING_PLAN} WHERE 1 AND i_user_id='".$i_profile_id."' {$where} {$s_order_by} {$limit}";
                /*LEFT JOIN {$this->db->USERS} u on u.id=p.i_user_id*/
				
        $res = $this->db->query($sql)->result_array();
        return $res;
	}
	
	function delete_reading_plan()
	{
		$i_profile_id = intval(decrypt($this->session->userdata('user_id')));
        $sql = "DELETE FROM {$this->db->BIBLE_READING_PLAN} WHERE i_user_id='".$i_profile_id."'";//exit;
		$this->db->query($sql);
		$sql1 = "DELETE FROM {$this->db->BIBLE_READING} WHERE i_user_id='".$i_profile_id."'";//exit;
		$this->db->query($sql1);
	}
	
	function get_category_list($wh='')
	{
		 $i_profile_id = intval(decrypt($this->session->userdata('user_id')));
		 $sql = "SELECT * FROM 
                {$this->db->BIBLE_CAT} WHERE 1 AND i_user_id='".$i_profile_id."' {$wh}";
                /*LEFT JOIN {$this->db->USERS} u on u.id=p.i_user_id*/
				
        $res = $this->db->query($sql)->result_array();
        return $res;
	}

	function get_color_list($wh='')
	{
		 $i_profile_id = intval(decrypt($this->session->userdata('user_id')));
		 $sql = "SELECT * FROM 
                {$this->db->BIBLE_HILITS_COLOR} WHERE 1 {$wh}";
                /*LEFT JOIN {$this->db->USERS} u on u.id=p.i_user_id*/
				
        $res = $this->db->query($sql)->result_array();
        return $res;
	}
	
	function get_note_bkmark_hilits($type,$wh='',$wh1='')
	{
		$i_profile_id = intval(decrypt($this->session->userdata('user_id')));
		if($type=='all')
		{
			$sql = "(SELECT b.id AS bookid, b.s_book_name AS book_name, c.id, c.s_chapter AS chapter,v.*,v.id AS vid ,n.s_note AS s_note,
						n.id AS noteid , n.dt_created_date AS created_date, 'note' AS rowtype, '' AS images, v.s_text AS versetxt, 
						'' AS i_page_bookmark
						FROM 
						{$this->db->BIBLE_NOTE} AS n, 
						{$this->db->BIBLE_VERSES} AS v, 
						{$this->db->BIBLE_CHAPTER} AS c, 
						{$this->db->BIBLE_BOOK} AS b
							WHERE b.id = c.i_book_id AND
								c.id=v.i_chapter_id AND 
								v.id= n.i_verses_id AND 
								n.i_user_id='".$i_profile_id."' {$wh})
						UNION
						(SELECT b.id AS bookid, b.s_book_name AS book_name, c.id, c.s_chapter AS chapter,v.*,v.id AS vid ,v.s_text AS s_note,
						n.id AS noteid , n.dt_created_date AS created_date, 'bkmark' AS rowtype, '' AS images, v.s_text AS versetxt, 
						n.i_page_bookmark AS i_page_bookmark
						FROM 
						{$this->db->BIBLE_BOOKMARK} AS n, 
						{$this->db->BIBLE_VERSES} AS v, 
						{$this->db->BIBLE_CHAPTER} AS c, 
						{$this->db->BIBLE_BOOK} AS b
							WHERE b.id = c.i_book_id AND
								c.id=v.i_chapter_id AND 
								v.id= n.i_verses_id AND 
								n.i_user_id='".$i_profile_id."' {$wh1})
						UNION
						(SELECT b.id AS bookid, b.s_book_name AS book_name, c.id, c.s_chapter AS chapter,v.*,v.id AS vid ,n.s_text AS s_note,
						n.id AS noteid , n.dt_created_date AS created_date, 'hilits' AS rowtype, color.s_images AS imagess, v.s_text AS versetxt, 
						'' AS i_page_bookmark
						FROM 
						{$this->db->BIBLE_HILITS} AS n, 
						{$this->db->BIBLE_HILITS_COLOR} AS color,
						{$this->db->BIBLE_VERSES} AS v, 
						{$this->db->BIBLE_CHAPTER} AS c, 
						{$this->db->BIBLE_BOOK} AS b
							WHERE b.id = c.i_book_id AND
								c.id=v.i_chapter_id AND 
								n.i_colorhidden=color.id AND
								v.id= n.i_verses_id AND 
								n.i_user_id='".$i_profile_id."' {$wh1})
						ORDER BY created_date DESC";
		}
		else if($type=='note')
		{
			$sql = "SELECT b.id AS bookid, b.s_book_name AS book_name, c.id, c.s_chapter AS chapter,v.*,v.id AS vid ,n.s_note AS s_note,
						n.id AS noteid , n.dt_created_date AS created_date
						FROM 
						{$this->db->BIBLE_NOTE} AS n, 
						{$this->db->BIBLE_VERSES} AS v, 
						{$this->db->BIBLE_CHAPTER} AS c, 
						{$this->db->BIBLE_BOOK} AS b
							WHERE b.id = c.i_book_id AND
								c.id=v.i_chapter_id AND 
								v.id= n.i_verses_id AND 
								n.i_user_id='".$i_profile_id."'  {$wh} ORDER BY created_date DESC";
		}
		else if($type=='bookmark')
		{
			$sql = "SELECT b.id AS bookid, b.s_book_name AS book_name, c.id, c.s_chapter AS chapter,v.*,v.id AS vid ,v.s_text AS s_note,
						n.id AS noteid , n.dt_created_date AS created_date, 
						n.i_page_bookmark AS i_page_bookmark
						FROM 
						{$this->db->BIBLE_BOOKMARK} AS n, 
						{$this->db->BIBLE_VERSES} AS v, 
						{$this->db->BIBLE_CHAPTER} AS c, 
						{$this->db->BIBLE_BOOK} AS b
							WHERE b.id = c.i_book_id AND
								c.id=v.i_chapter_id AND 
								v.id= n.i_verses_id AND 
								n.i_user_id='".$i_profile_id."' {$wh} ORDER BY created_date DESC";
		}
		else if($type=='hilits')
		{
			$sql = "SELECT b.id AS bookid, b.s_book_name AS book_name, c.id, c.s_chapter AS chapter,v.*,v.id AS vid ,n.s_text AS s_note,
						n.id AS noteid , n.dt_created_date AS created_date, 'hilits' AS rowtype, color.s_images AS images
						FROM 
						{$this->db->BIBLE_HILITS} AS n, 
						{$this->db->BIBLE_HILITS_COLOR} AS color,
						{$this->db->BIBLE_VERSES} AS v, 
						{$this->db->BIBLE_CHAPTER} AS c, 
						{$this->db->BIBLE_BOOK} AS b
							WHERE b.id = c.i_book_id AND
								c.id=v.i_chapter_id AND 
								n.i_colorhidden=color.id AND
								v.id= n.i_verses_id AND 
								n.i_user_id='".$i_profile_id."' {$wh} ORDER BY created_date DESC";
		}
		#echo nl2br($sql);		
        $res = $this->db->query($sql)->result_array();
		//pr($res);
		return $res;
	}
	
	
	###new added  to get reading plan srat dae and end date.
	public function get_reading_plan_start_date($where='')
	{
		
        $i_profile_id = intval(decrypt($this->session->userdata('user_id')));
        $sql = "SELECT min(dt_date) as start_date FROM 
                {$this->db->BIBLE_READING_PLAN} WHERE 1 AND i_user_id='".$i_profile_id."' {$where} ";
                /*LEFT JOIN {$this->db->USERS} u on u.id=p.i_user_id*/
				
        $res = $this->db->query($sql)->result_array();
		
        return $res[0]['start_date'];
	}
	
	public function get_reading_plan_end_date($where='')
	{
		
        $i_profile_id = intval(decrypt($this->session->userdata('user_id')));
        $sql = "SELECT max(dt_date) as end_date FROM 
                {$this->db->BIBLE_READING_PLAN} WHERE 1 AND i_user_id='".$i_profile_id."' {$where} ";
                /*LEFT JOIN {$this->db->USERS} u on u.id=p.i_user_id*/
				
        $res = $this->db->query($sql)->result_array();
        return $res[0]['end_date'];
	}
	
	public function get_reading_plan_last_read_date($where='')
	{
		
        $i_profile_id = intval(decrypt($this->session->userdata('user_id')));
        $sql = "SELECT max(dt_date) as end_date FROM 
                {$this->db->BIBLE_READING_PLAN} WHERE 1 AND i_user_id='".$i_profile_id."' AND is_read_updated = 1 {$where} ";
                /*LEFT JOIN {$this->db->USERS} u on u.id=p.i_user_id*/
				
        $res = $this->db->query($sql)->result_array();
		//echo $res[0]['end_date'];
        return $res[0]['end_date'];
	}
	
	public function get_reading_plan_type(){
		$i_profile_id = intval(decrypt($this->session->userdata('user_id')));
        $sql = "SELECT i_plan_type as i_plan_type FROM 
                {$this->db->BIBLE_READING} WHERE 1 AND i_user_id='".$i_profile_id."' {$where} ";
                /*LEFT JOIN {$this->db->USERS} u on u.id=p.i_user_id*/
				
        $res = $this->db->query($sql)->result_array();
		//echo $res[0]['end_date'];
		$plan_type = '';
		
		if($res[0]['i_plan_type'] == 1){
			
			$plan_type = '6 Months Plan';
		}
		else if($res[0]['i_plan_type'] == 2){
			
			$plan_type ='1 Year Plan';
		}
		else if($res[0]['i_plan_type'] == 3){
			$plan_type = 'Custom Plan';
		}
		else  if($res[0]['i_plan_type'] == 4)
		{ 
		 	$plan_type = 'Beginning To End';
		}
        return $plan_type;
	}
	
	
	public function insert_fixed_reading_plan($start_day =1,$VERSE_ID = 1, $total_verse ,$total_days, $is_second_slot = 'N'){
		
		if($is_second_slot == 'Y')
			 $VERSE_UP_LIMIT = $VERSE_ID +$total_verse  ;
		else
			$VERSE_UP_LIMIT = $total_verse;
		
		$DAY_NO = 1;
		$day = date('Y-m-d');
		
		for($i=1; $i <=$total_days ; $i++){
			  
			 $SQL_1 = "SELECT  bv.id as L_i_verse_no_from, 
			 				bc.id as L_i_chapter_no_from,	
							bc.s_chapter as L_s_chapter_from,	
							bb.id as L_i_book_id_from,
							bb.s_book_name as L_s_book_name_from,
							bv.i_verses as L_i_verse_from
							FROM  cg_bible_book as bb
				    		LEFT JOIN cg_bible_chapter bc ON bc.i_book_id = bb.id
				   			 LEFT JOIN cg_bible_verses bv ON bv.i_chapter_id = bc.id
							WHERE  bv.id = {$VERSE_ID}";
							
			$result_1 = $this->db->query($SQL_1)->result_array();

			if($i == $total_days && $is_second_slot == 'Y')
				$VERSE_UP_LIMIT	= 31102;
					
			$SQL_2 = "SELECT  bv.id as L_i_verse_no_till , 
								bc.id as L_i_chapter_no_till, 
								bc.s_chapter as L_s_chapter_till, 
								bb.id as L_i_book_id_till, 
								bb.s_book_name as L_s_book_name_till ,
								bv.i_verses as L_i_verse_till
							
						FROM  cg_bible_book as bb
				    	LEFT JOIN cg_bible_chapter bc ON bc.i_book_id = bb.id
				    	LEFT JOIN cg_bible_verses bv ON bv.i_chapter_id = bc.id
						WHERE  bv.id = {$VERSE_UP_LIMIT}";
						
						
			$result_2 = $this->db->query($SQL_2)->result_array();
			
		 $next_verse_ID  =  $VERSE_ID	;
		 if($is_second_slot == 'Y'){
			 $VERSE_ID = $VERSE_ID +$total_verse  ;
		 }
		 else{
				$VERSE_ID = $VERSE_UP_LIMIT +1;
		 }
		 
		 
		 //echo $VERSE_UP_LIMIT.'====='.$total_verse.'<br />';
		 $VERSE_UP_LIMIT =  $VERSE_UP_LIMIT + $total_verse ;
			
			if($i==1){ 
		 		//$SQL_DATE = "SELECT DATE_ADD({$day}, INTERVAL 0 DAY) as curr_day";
				$day_arr[0]['curr_day'] =date('Y-m-d');
			}
			else{
				 $SQL_DATE = "SELECT DATE_ADD('{$day}', INTERVAL 1 DAY) as curr_day";
				$day_arr = $this->db->query($SQL_DATE)->result_array();
			}
		 
			 
			
			 
			  $reading_plan_arr = array();
			  $reading_plan_arr['i_user_id']    =  intval(decrypt($this->session->userdata('user_id')));
			  $reading_plan_arr['i_day']		= $start_day;
			  $reading_plan_arr['dt_date']		= $day_arr[0]['curr_day'];
			  $reading_plan_arr['i_verse_no_from'] = $result_1[0]['L_i_verse_no_from'];
			  $reading_plan_arr['i_verse_from']		= $result_1[0]['L_i_verse_from'];
			  $reading_plan_arr['i_chapter_no_from'] = $result_1[0]['L_i_chapter_no_from'];
			  $reading_plan_arr['s_chapter_from']	 = $result_1[0]['L_s_chapter_from'];
			  $reading_plan_arr['i_book_id_from']	 = $result_1[0]['L_i_book_id_from'];
			  $reading_plan_arr['s_book_name_from']  = $result_1[0]['L_s_book_name_from'];
			  $reading_plan_arr['i_verse_no_till'] = $result_2[0]['L_i_verse_no_till'];
			  $reading_plan_arr['i_verse_till']		= $result_2[0]['L_i_verse_till'];
			  $reading_plan_arr['i_chapter_no_till']	= $result_2[0]['L_i_chapter_no_till'];
			  $reading_plan_arr['s_chapter_till']	= $result_2[0]['L_s_chapter_till'];
			  $reading_plan_arr['i_book_id_till']	= $result_2[0]['L_i_book_id_till'];
			  $reading_plan_arr['s_book_name_till']		= $result_2[0]['L_s_book_name_till'];
			  $reading_plan_arr['dt_created_on']	= get_db_datetime();
			 
			  
			  $this->db->insert($this->db->BIBLE_READING_PLAN, $reading_plan_arr);
			  $day = $day_arr[0]['curr_day'];
			  $start_day++; 
			  
			  if($is_second_slot == 'Y' && $i==1)
			  	 $VERSE_ID++;
			  
		  }
		 
		 //echo $this->db->last_query();
		return $i.'###'.$next_verse_ID;
	}
    
	
	
	public function get_reading_plan_last_read_info($where='')
	{
		
        $i_profile_id = intval(decrypt($this->session->userdata('user_id')));
        $sql = "SELECT * FROM 
                {$this->db->BIBLE_READING_PLAN} WHERE 1 AND i_user_id='".$i_profile_id."' AND is_read_updated = 1 
				{$where} ORDER BY dt_date DESC LIMIT 0,1";
                /*LEFT JOIN {$this->db->USERS} u on u.id=p.i_user_id*/
				
        $res = $this->db->query($sql)->result_array();
		//echo $res[0]['end_date'];
        return $res[0];
	}
	
	public function verse_text_with_highlight_note_bookmark($id)
	{
		$wh	= " AND n.i_verses_id='".$id."'";
		$wh1	= " AND n.i_verses_id='".$id."'";
		$sql	= $this->get_note_bkmark_hilits('all',$wh='',$wh1='');
		$res = $this->db->query($sql)->result_array();
		$versetxt_row	= '';
		$notespan		= '';
		$bookmarkspan	= '';
		$hilits			= '';
		foreach($res as $val)
		{	
			if($val['rowtype']=='note')
				$notespan	.= '&nbsp;<span title="Note" class="note-notify"></span>';
			else if($val['rowtype']=='bkmark')
				$bookmarkspan	= '&nbsp;<span title="Bookmark" class="bookmark-notify"></span>';
			else if($val['rowtype']=='hilits')
			{
				$hilits		= '';
			}
		}
		
	}
	
	
	
	public function get_totaldays_read($where='')
	{
		
        $i_profile_id = intval(decrypt($this->session->userdata('user_id')));
        $sql = "SELECT count(*) as total_read_day FROM 
                {$this->db->BIBLE_READING_PLAN} WHERE 1 AND i_user_id='".$i_profile_id."' AND is_read_updated = 1 
				{$where} ";
                /*LEFT JOIN {$this->db->USERS} u on u.id=p.i_user_id*/
				
        $res = $this->db->query($sql)->result_array();
		//echo $res[0]['end_date'];
        return $res[0]['total_read_day'];
	}
	
	
	### get mst oldest unread day plan
	public function get_tdays_reading_plan_info($where='')
	{
		
        $i_profile_id = intval(decrypt($this->session->userdata('user_id')));
        $sql = "SELECT * FROM 
                {$this->db->BIBLE_READING_PLAN} WHERE 1 AND i_user_id='".$i_profile_id."' AND is_read_updated = 0 
				{$where} ORDER BY dt_date ASC LIMIT 0,1";
                /*LEFT JOIN {$this->db->USERS} u on u.id=p.i_user_id*/
				
        $res = $this->db->query($sql)->result_array();
		//echo $res[0]['end_date'];
        return $res;
	}
	
	
	### get verse of the day
	
	public function getDayverse()
	{
		$sql1=$this->db->query("select count(*) as count from {$this->db->day_verse} where publish_status=2");
		$q=$sql1->result_array();
		if($q['0']['count'] == 30)
		{
		 $sql2 = "SELECT *,v.id AS verseid FROM 
					{$this->db->BIBLE_BOOK} AS b, 
					{$this->db->BIBLE_CHAPTER} AS c ,
					{$this->db->BIBLE_VERSES} AS v 
					WHERE b.id=c.i_book_id AND c.id=v.i_chapter_id order by RAND() Limit 30";
					//echo $sql;
			
			$result_arr = $this->db->query($sql2)->result_array();
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
		$sql3=$this->db->query("update {$this->db->day_verse} set publish_status=true where id=1");	
		}
        $sql = "SELECT s_book_name,bible_chapter_no,bible_verse_no,s_verse FROM {$this->db->day_verse} where publish_status=1";
              
        $res = $this->db->query($sql)->result_array();
        return $res[0];
	}
	public function get_bible_search($wh)
	{
	$q2=array();
		$sql1=$this->db->query("select * from cg_bible_book");
		$q1=$sql1->result_array();
		foreach ($q1 as $val)
		{
		if(strcasecmp($val['s_book_name'],$wh) == 0)
		{
		$sql="select v.id as id,v.s_text as verse_txt,c.i_book_id,v.i_verses as verse,b.s_book_name as bookname,c.s_chapter as chapter from cg_bible_verses v JOIN cg_bible_chapter c ON v.i_chapter_id=c.id JOIN cg_bible_book b ON b.id=c.i_book_id WHERE b.s_book_name Like '%".$wh."%' limit 5";
		//echo $sql;exit;
		$query=$this->db->query($sql);
		$q=$query->result_array();
		return $q;
		}
		else
		{
		$sql="select v.id as id,v.s_text as verse_txt,c.i_book_id,v.i_verses as verse,b.s_book_name as bookname,c.s_chapter as chapter from cg_bible_verses v JOIN cg_bible_chapter c ON v.i_chapter_id=c.id JOIN cg_bible_book b ON b.id=c.i_book_id WHERE b.s_book_name Like '%".$val['s_book_name']."%' AND v.s_text like '%".$wh."%' LIMIT 5";
		//echo $sql;exit;
		$query=$this->db->query($sql);
		$q=$query->result_array();
		}
		$q2=array_merge($q2,$q);
		}
		return $q2;
		//pr($q1,1);
		//exit;
		
	}
	
	
	public function daily_verse_list($i_start=null,$i_limit=null,$s_order_by='')
    {
        
        $limit  = (is_numeric($i_start) && is_numeric($i_limit))?" Limit ".intval($i_start).",".intval($i_limit):'';
		$order_by = ($s_order_by != '')?" ORDER BY {$s_order_by}":"ORDER BY id ASC";
        $sql    = " SELECT * FROM {$this->db->day_verse} {$order_by} {$limit}";
//echo $sql;
        $query     = $this->db->query($sql); 
        $result_arr = $query->result_array(); #pr($result_arr,1);
		
		$this->load->model("admin_utility_model");
		if(count($result_arr)){
		 foreach($result_arr as $key=>$val)
		  {
			 $result_arr[$key]['image_rank'] = $this->admin_utility_model->RankingRowCreate($val['i_order'],
																			 $val['id'],
																			 $this->db->day_verse,
																			 $s_where); 
		  }
		}
        return $result_arr;
    }
	
		
	public function total_day_verse()
	{
		$sql=$this->db->query("select count(*) as count from cg_day_verse");
		$res=$sql->result_array();
		return $res['0']['count'];
		
	}
	public function add_day_verse($arr)
	{
	if($arr != '')
	{
		$this->db->insert($this->db->day_verse, $arr);
		$vid=$this->db->insert_id();
		return $vid;
	}	
	}
	public function delete_by_id($id)
	{
		$this->db->where('id',$id);
		$this->db->delete("cg_day_verse",$wh);
		//echo $this->db->last_query();exit;
		return true;
	}
	
	public function get_by_id($id, $start_limit="", $no_of_page="") {
		
		if("$start_limit" == "") {
			$sql = sprintf('SELECT * FROM '.$this->db->day_verse.'  where id = %s',  $id);
		}
		else {
			$sql = sprintf('SELECT * FROM '.$this->db->day_verse.'  where id = %s limit %s, %s',  $id, $start_limit, $no_of_page);
		}

		$query = $this->db->query($sql); //echo $this->db->last_query(); //exit;
		$result_arr = $query->result_array();
	
		return $result_arr[0];
	}	
	
	public function get_verse_texts($first,$last='')
	{
	if($last!= '')
	{
		$sql=$this->db->query("select GROUP_CONCAT((s_text) SEPARATOR '<br/>') as verse from cg_bible_verses where id between ".$first." and ".$last);
		//echo $this->db->last_query();
		$result=$sql->result_array();
		//pr($result);
		return $result['0']['verse'];
	}
	else
	{
	$sql=$this->db->query("select s_text from cg_bible_verses where id=".$first);
		//echo $this->db->last_query();
		$result=$sql->result_array();
		
		return $result['0']['s_text'];
	}
	}
}// end of model