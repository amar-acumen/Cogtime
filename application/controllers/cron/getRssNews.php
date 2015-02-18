<?php
/*********
* Author:
* Date  : 
* Modified By: 
* Modified Date:
* 
* Purpose:
* 
* 
*/

include(APPPATH.'controllers/base_controller.php');

class GetRssNews extends Base_controller {

	public function __construct() {
		try
		{
			parent::__construct();
			$this->load->model('landing_page_cms_model');
		}
		catch(Exception $err_obj)
		{
			show_error($err_obj->getMessage());
		}
	}


    public function index() {
		
	 	//clear cg_five_fruits_per_user table
		
		
				$sql=$this->db->query("select i_category,s_feed_url,i_posted_by from cg_christian_news where s_feed_url is NOT NULL GROUP BY i_category ;
");
				$result=$sql->result_array();
				//pr($result);
				
				foreach($result as $val)
				{
					$feeds=$this->landing_page_cms_model->get_rss_feed($val['s_feed_url']);
					foreach($feeds as $feed)
					{
					$sql3=$this->db->query('select count(feed_news_link) as f from cg_christian_news where feed_news_link="'.$feed['feed_news_link'].'" and i_category='.$val['i_category']);
					$res=$sql3->result_array();
					if($res['0']['f'] == 0)
					{
					$feed['i_is_feature_home']=0;
					$feed['i_posted_by']=($val['i_posted_by']==0)?'1':$val['i_posted_by'];
					$feed['i_category']=$val['i_category'];
					$this->db->insert('cg_christian_news',$feed);
					}
					}
					
				}
				/*foreach($result as $val)
				{
					$feeds=$this->landing_page_cms_model->get_rss_feed($val['s_feed_url']);
					$sql1=$this->db->query("select i_category,feed_news_link from cg_christian_news where s_feed_url='".$val['s_feed_url']."'");
					//echo $this->db->last_query();
					$res1=$sql1->result_array();
					//pr($res1);
					foreach($res1 as  $k=>$value)
					{
					$res2[$k]=$value['i_category'];
					//$res2[$k]=$value['feed_news_link'];
					}
					//pr($res2);
					$f=0;
				/*	foreach($res2 as $vals)
					{
					foreach($feeds as $feed)
					{
						//$var=array_search($feed['feed_news_link'],$vals);
						//$var1=array_search($feed['i_category'],$vals);
						//pr($vals);
						$sql3=$this->db->query('select count(feed_news_link) as f from cg_christian_news where feed_news_link="'.$feed['feed_news_link'].'" and i_category='.$vals);
						$as=$sql3->result_array();
					$f++;
					}
				
					}
						echo $f;
				}*/
				
	}
    
  
}

