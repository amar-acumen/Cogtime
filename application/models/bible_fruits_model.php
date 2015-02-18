<?php
include_once(APPPATH.'models/base_model.php');
class Bible_fruits_model extends Base_model
{
	
	public function __construct() 
	{
		parent::__construct();
	}

	
	public function get_fruit_list($id)
    {
        $curr_date = date('Y-m-d');
		
        $sql    = " SELECT *
					FROM {$this->db->FIVE_FRUITS_PER_USER} 
					WHERE  DATE(dt_created_on) = '{$curr_date}' AND  i_user_id = {$id} 
					ORDER BY dt_created_on";

        $query     = $this->db->query($sql);  #echo $this->db->last_query();exit;
        $result_arr = $query->result_array(); //pr($result_arr,1);  i_user_id = {$id}
		
		
        return $result_arr;
    }
	
	
	/*public function generate_fruit_list_per_user_id_date()
    {
        $cur_date = get_db_datetime();
		$user_id = intval(decrypt($this->session->userdata('user_id')));
      
	  
	    $sql    = " SELECT 
							BV.s_verse as s_verse,
							BV.s_book_name as s_book_name,
							BV.bible_chapter_no as bible_chapter_no,
							BV.bible_verse_no as bible_verse_no,
							BF.s_image_name as s_image_name
					FROM {$this->db->BIBLE_FRUIT_VERSE} BV
					LEFT JOIN {$this->db->BIBLE_FRUIT} BF ON BF.id = BV.i_fruit_id
					GROUP BY BF.s_fruit_name
					ORDER BY rand()  LIMIT 0,5";

        $query     = $this->db->query($sql); #echo $this->db->last_query();
        $result_arr = $query->result_array(); //pr($result_arr,1);
		
	
		//pr($result_arr,1);
		
		if(count($result_arr)){
			foreach($result_arr as $key=>$val){
		     	
				$arr = array();
				
				$arr['i_user_id'] = $user_id;
				$arr['s_verse']   = $val['s_verse'];
				$arr['s_book_name'] = $val['s_book_name'];
				$arr['bible_chapter_no'] = $val['bible_chapter_no'];
				$arr['bible_verse_no'] = $val['bible_verse_no'];
				$arr['s_image_name'] = $val['s_image_name'];
				$arr['dt_created_on'] = $cur_date;
				
				$this->db->insert($this->db->FIVE_FRUITS_PER_USER, $arr);# echo $this->db->last_query();
			}
		}
		return $this->db->insert_id();
        //return $result_arr;
    }*/
public function generate_fruit_list_per_user_id_date()
    {
        $cur_date = get_db_datetime();
		$user_id = intval(decrypt($this->session->userdata('user_id')));
      
	  
	   /* $sql    = " SELECT 
							BV.s_verse as s_verse,
							BV.s_book_name as s_book_name,
							BV.bible_chapter_no as bible_chapter_no,
							BV.bible_verse_no as bible_verse_no,
							BF.s_image_name as s_image_name
					FROM {$this->db->BIBLE_FRUIT_VERSE} BV
					LEFT JOIN {$this->db->BIBLE_FRUIT} BF ON BF.id = BV.i_fruit_id
					GROUP BY BF.s_fruit_name
					ORDER BY rand()  LIMIT 0,5";*/
					
					//$sql="select id,s_image_name from {$this->db->BIBLE_FRUIT} order by rand() limit 0,5";
$sql="select verse.i_fruit_id as id,s_image_name from cg_bible_concordance verse left join cg_bible_fruit fruit on fruit.id=verse.i_fruit_id group by verse.i_fruit_id order by rand() limit 0,5";
        $query     = $this->db->query($sql); //echo $this->db->last_query();
        $result_arr = $query->result_array(); //pr($result_arr,1);
		
	
		//pr($result_arr,1);
		
		if(count($result_arr)){
			foreach($result_arr as $key=>$val){
		     $sql1="select * from cg_bible_concordance where i_fruit_id=".$val['id']." order by rand() limit 0,1";	
			   $query     = $this->db->query($sql1);
			   $res = $query->result_array();
				$arr = array();
				
				$arr['i_user_id'] = $user_id;
				$arr['s_verse']   = $res['0']['s_verse'];
				$arr['s_book_name'] = $res['0']['s_book_name'];
				$arr['bible_chapter_no'] = $res['0']['bible_chapter_no'];
				$arr['bible_verse_no'] = $res['0']['bible_verse_no'];
				$arr['s_image_name'] = $val['s_image_name'];
				$arr['dt_created_on'] = $cur_date;
				
				$this->db->insert($this->db->FIVE_FRUITS_PER_USER, $arr); //echo $this->db->last_query();exit;
				//pr($arr);
			}
		}
		//exit;
		return $this->db->insert_id();
        //return $result_arr;
    }
####concordence_list

/*public function get_concordence_list()
{
	$sql=   "SELECT  group_concat(concat(concat('<li onclick=edit_concordence(' ,verse.id ,')>'),'',concat(verse.s_book_name ,' - ',
					CONCAT(verse.bible_verse_no, ':',  verse.bible_chapter_no ))) separator '</li>') as verse_info ,
	
				 fruit.s_fruit_name AS fruit_name
				 FROM ".$this->db->BIBLE_FRUIT_VERSE." verse 
				 LEFT JOIN ".$this->db->BIBLE_FRUIT." fruit on fruit.id=verse.i_fruit_id 
				
				group by verse.i_fruit_id 
				ORDER BY fruit.id ";
	$query=$this->db->query($sql); #echo $this->db->last_query();
	$result_arr=$query->result_array();
	return $result_arr;	
}*/


public function get_count_concordence()
{
	$sql='SELECT count(*) FROM '.$this->db->BIBLE_FRUIT_VERSE.' verse LEFT JOIN '.$this->db->BIBLE_FRUIT.' fruit on 			fruit.id=verse.i_fruit_id ORDER BY fruit.id';
	$query=$this->db->query($sql);#echo $this->db->last_query();
	$result_arr=$query->result_array(); #pr($result_arr);
	return $result_arr[0]['count(*)'];	
}

public function insert_concordence($info)
    {	
        $this->db->insert($this->db->BIBLE_FRUIT_VERSE,$info); #echo $this->db->last_query(); exit;
        $last_id = $this->db->insert_id();
		
        return $last_id;
    }	
public function get_verse($chap,$ver)
{
	$sql='SELECT s_text as verse FROM '.$this->db->BIBLE_VERSES.' WHERE i_chapter_id='.$chap.' AND i_verses='.$ver;
	$query=$this->db->query($sql);#echo $this->db->last_query();
	$result_arr=$query->result_array(); #pr($result_arr);
	return $result_arr[0]['verse'];	
}

public function get_verse_by_id($id)
{
	$sql='SELECT * FROM '.$this->db->BIBLE_FRUIT_VERSE.' WHERE id='.$id;
	$query=$this->db->query($sql);#echo $this->db->last_query();
	$result_arr=$query->result_array(); #pr($result_arr);
	return $result_arr;
}
public function edit_concordence($info,$id)
    {	//echo '1' ; pr($info); echo $id;
        $this->db->update($this->db->BIBLE_FRUIT_VERSE,$info,array('id'=>$id));
        //echo "last q: ".$this->db->last_query(); exit;
        
    }
public function get_concordence_list()
{
$sql="Select * from ".$this->db->BIBLE_FRUIT;
$query=$this->db->query($sql);#echo $this->db->last_query();
$result_arr=$query->result_array();
$com=array();
foreach($result_arr as $val)
{
	$sql1="select id as verse_id,s_book_name,bible_chapter_no,bible_verse_no from ".$this->db->BIBLE_FRUIT_VERSE." where i_fruit_id=".$val['id'];
	$query1=$this->db->query($sql1);#echo $this->db->last_query();
	$result_arr1=$query1->result_array();
	$com[$val['s_fruit_name']]=$result_arr1;

}
return $com;
}

public function get_selected_verse()
{
$sql="Select verse_id from cg_bible_concordance";
$query=$this->db->query($sql);#echo $this->db->last_query();
$result_arr=$query->result_array();
$ids=array();
foreach($result_arr as $val)
{
	$ids[]=$val['verse_id'];
}
return $ids;
}
}
