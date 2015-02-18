<?php

 
 
 //WALL FUNCTIONS



//LIKE UNLIKE

 function like_display($window_id)
    {
        global $CI;
		#echo $window_id;
        //$user_session_data =$CI->user_session_data;  //GETTING STORED SESSION DATA WHILE LOGGED IN
        $logged_user_id = intval(decrypt($CI->session->userdata('user_id')));


        $sql_window_like_all = "SELECT l.id,l.i_liked_user_id, CONCAT(u.s_first_name,' ',u.s_last_name) as s_fullname ,u.i_user_type
        						from {$CI->db->dbprefix}user_newsfeed_like l, {$CI->db->dbprefix}users u
        						where l.i_newsfeed_id = '$window_id'
       							and u.id=l.i_liked_user_id"; 
								
        $result_window_like_all = $CI->db->query($sql_window_like_all); #echo  $CI->db->last_query(); exit;
        $row_window_like_all = $result_window_like_all->num_rows();


	if($row_window_like_all >0){
		if($row_window_like_all > 2){
		//$all_user_liked = '<a href="view_like_users.php?height=250&width=450&window_id='.$window_id.'" class="thickbox" title="People who like this">'.$row_window_like_all." people</a> liked.";

		
		 
		$have_i_liked = false; 
	    if($row_window_like_all >0){
		
		     	foreach ($result_window_like_all->result_array() as $get_all_users )
				 {
						if($get_all_users['i_liked_user_id'] == $logged_user_id)
						{
						    $have_i_liked =TRUE;
						}
				 }
			
			
			  }
		  if($have_i_liked)	
		   $all_user_liked = 'you'.' '.'and'.' '.($row_window_like_all-1)." "."Others"." ".'like';  
		  else
		  $all_user_liked = ''.$row_window_like_all." ".'people'." ".'like';
		 
		 
                 //onclick="light_like('.$window_id.')"
		}
		elseif($row_window_like_all == 1){
			 //$get_all_users = $result_window_like_all->result_array();
                        $get_all_users = $result_window_like_all->row_array();
                        if($get_all_users['liked_user_id'] == $logged_user_id){
			$all_user_liked = 'You liked this';
			}
			else{
			//$all_user_liked = '<a href="'.public_profile_url($get_all_users['username']).'">'.$get_all_users['disp_name'].'</a> liked this.';
			$profile_url = get_profile_url($get_all_users['i_liked_user_id']);
			$all_user_liked = '<a class="black_link" href="'.$profile_url.'">'.$get_all_users['s_fullname'].'</a> '.t('aime');
			}
		}
		elseif($row_window_like_all == 2){
		$all_user_liked ='';
			$cnt=1;

			foreach ($result_window_like_all->result_array() as $get_all_users ){
				if($get_all_users['i_liked_user_id'] == $logged_user_id){
				$display_user_name = 'you';
				}
				else{
				//$display_user_name = '<a href="'.public_profile_url($get_all_users['username']).'">'.$get_all_users['disp_name'].'</a>';
				
				$profile_url = get_profile_url($get_all_users['i_liked_user_id']);
			    $display_user_name = '<a class="black_link" href="'.$profile_url.'">'.$get_all_users['s_fullname'].'</a> ';
				
				}

				if($cnt == 1){
				$all_user_liked = $all_user_liked.$display_user_name.' '.'and'.' ';
				}
				else{
				$all_user_liked = $all_user_liked.$display_user_name.'';
				}
			$cnt++;
			}
			$all_user_liked = $all_user_liked.' '.'like';
		}
	$display_style = 'block';
	}
	else{
	$all_user_liked = '';
	$display_style = 'none';
	}

return array($all_user_liked,$display_style);
}

function dislike_display($window_id){

    global $CI;
  //$user_session_data =$CI->user_session_data;  //GETTING STORED SESSION DATA WHILE LOGGED IN
  
   $logged_user_id = intval(decrypt($CI->session->userdata('user_id')));
   //$is_disliked_by_user = false;




 $sql_window_unlike_all = "SELECT n.id, n.i_unliked_user_id, CONCAT(u.s_first_name,' ',u.s_last_name) as s_fullname  
 							,u.i_user_type
							from {$CI->db->dbprefix}user_newsfeed_unlike n, {$CI->db->dbprefix}users u
							where n.i_newsfeed_id = '$window_id'
							and u.id=n.i_unliked_user_id";

$result_window_unlike_all = $CI->db->query($sql_window_unlike_all);
$row_window_unlike_all = $result_window_unlike_all->num_rows();


	if($row_window_unlike_all > 0){
		if($row_window_unlike_all > 2){
		//$all_user_unliked = '<a href="view_unlike_users.php?height=250&width=450&window_id='.$window_id.'" class="thickbox" title="People who dislike this" >'.$row_window_unlike_all." people</a> disliked.";
		$all_user_unliked = '<a class="black_link" href="javascript:void(0);"  title="">'.$row_window_unlike_all." ".t('les gens')."</a> ".t("n'a pas aimé");
                //onclick="light_unlike('.$window_id.')"
		}
		elseif($row_window_unlike_all == 1){
			//$get_all_users = mysql_fetch_array($result_window_unlike_all);
                         $get_all_users = $result_window_unlike_all->row_array();
			if($get_all_users['i_unliked_user_id'] == $logged_user_id){
			$all_user_unliked = "You do not like this";
			}
			else{
			//$all_user_unliked = '<a href="'.public_profile_url($get_all_users['username']).'">'.$get_all_users['disp_name'].'</a> disliked this.';
			//$all_user_unliked = '<a href="#">'.$get_all_users['s_fullname'].'</a> '.t("n'aimait pas cela");
			$profile_url = get_profile_url($get_all_users['i_unliked_user_id']);
			$all_user_unliked = '<a class="black_link" href="'.$profile_url.'">'.$get_all_users['s_fullname'].'</a> '.t("n'aimait pas cela");
			
			}
		}
		elseif($row_window_unlike_all == 2){
		$all_user_unliked ='';
			$cnt=1;
			foreach($result_window_unlike_all->result_array() as $get_all_users){
				if($get_all_users['i_unliked_user_id'] == $logged_user_id){
				$display_user_name = 'You';
				}
				else{
				//$display_user_name = '<a href="'.public_profile_url($get_all_users['username']).'">'.$get_all_users['disp_name'].'</a>';
				
				//$display_user_name = '<a href="#">'.$get_all_users['s_fullname'].'</a>';
				
				$profile_url = get_profile_url($get_all_users['i_unliked_user_id']);
			$display_user_name = '<a class="black_link" href="'.$profile_url.'">'.$get_all_users['s_fullname'].'</a>';
				
				}

				if($cnt == 1){
				$all_user_unliked = $all_user_unliked.$display_user_name.' '.'and'.' ';
				}
				else{
				$all_user_unliked = $all_user_unliked.$display_user_name.'';
				}
			$cnt++;
			}
			$all_user_unliked = $all_user_unliked.' '."did not like it";
		}
	$display_style_un = 'block';
	}
	else{
	$all_user_unliked = '';
	$display_style_un = 'none';
	}

return array($all_user_unliked,$display_style_un);
}


function show_like_link($window_id)
 {
         $show_link =TRUE;
		 global $CI;
        //$user_session_data =$CI->user_session_data;  //GETTING STORED SESSION DATA WHILE LOGGED IN
        $logged_user_id = intval(decrypt($CI->session->userdata('user_id')));


        $sql_window_like_all = "SELECT l.id,l.i_liked_user_id, 
								CONCAT(u.s_first_name,' ',u.s_last_name) as s_fullname  ,u.i_user_type
       							from {$CI->db->dbprefix}user_newsfeed_like l, {$CI->db->dbprefix}users u
        						where l.i_newsfeed_id = '$window_id'
        						and u.id=l.i_liked_user_id";
        $result_window_like_all = $CI->db->query($sql_window_like_all);
        $row_window_like_all = $result_window_like_all->num_rows();


	    if($row_window_like_all >0){
		
		     	foreach ($result_window_like_all->result_array() as $get_all_users )
				 {
						if($get_all_users['i_liked_user_id'] == $logged_user_id)
						{
						  $show_link =FALSE;
						}
				 }
			
			
			  }
	  return 	$show_link;	  
 }
 
 
//---------------------------------- count for like-unlike ------------------------------
 function count_like_link($window_id)
 {
        
		 global $CI;
		 $i_total = 0;
        //$user_session_data =$CI->user_session_data;  //GETTING STORED SESSION DATA WHILE LOGGED IN
        $logged_user_id = intval(decrypt($CI->session->userdata('user_id')));


        $sql_window_like_all = "SELECT COUNT(*) as i_total
        from {$CI->db->dbprefix}user_newsfeed_like l, {$CI->db->dbprefix}users u
        where l.i_newsfeed_id = '$window_id'
        and u.id=l.i_liked_user_id";
       
        $query = $CI->db->query($sql_window_like_all);
		$result_arr = $query->result_array();

		$i_total = intval($result_arr[0]['i_total']);

	   
	    return 	$i_total;	  
 }
 // --------------------------- end of count for like-unlike -------------------------------
 
 ###------------------------- count for comments ---------------------------###
 
  function count_comment_link($window_id)
 {
        
         global $CI;
         $i_total = 0;
        //$user_session_data =$CI->user_session_data;  //GETTING STORED SESSION DATA WHILE LOGGED IN
        $logged_user_id = intval(decrypt($CI->session->userdata('user_id')));


        $sql_window_comment_all = "SELECT COUNT(*) as i_total
        from {$CI->db->dbprefix}user_newsfeed_comments c, {$CI->db->dbprefix}users u
        where c.i_newsfeed_id = '$window_id'
        and u.id=c.i_user_id";
       
        $query = $CI->db->query($sql_window_comment_all);


        $result_arr = $query->result_array();
        
        $i_total = intval($result_arr[0]['i_total']);

       
        return     $i_total;      
 }
 
 
 ###------------------------- end count for cpmments ---------------------------###
 #NEW ADDED TO SHOW DELETE POST
 
 function show_delete_link($i_newsfeed_id)
 {
        $show_link =TRUE;
		global $CI;
        //$user_session_data =$CI->user_session_data;  //GETTING STORED SESSION DATA WHILE LOGGED IN
        $logged_user_id = intval(decrypt($CI->session->userdata('user_id')));


        $sql		 		= "SELECT l.id,l.i_owner_id, 
								CONCAT(u.s_first_name,' ',u.s_last_name) as s_fullname  ,u.i_user_type
       							from {$CI->db->dbprefix}user_newsfeeds l, {$CI->db->dbprefix}users u
        						where l.id = '$i_newsfeed_id'
        						and u.id=l.i_owner_id";
        $result = $CI->db->query($sql);
        $row_newsfeed = $result->num_rows();

		$result_arr = $result->result_array();
		$res_arr = $result_arr[0];
		//return $result_arr[0];
		
	    if(count($res_arr) >0){
				//echo $res_arr['i_owner_id'];
				if($res_arr['i_owner_id'] == $logged_user_id)
				{
				   $show_link = 'yes';
				}
				else
				{
					$show_link = 'no';
				}
			
	 	}
	  return 	$show_link;	  
 }
 
 
 
 
 
 
 

 ### ---------------------------------- ###
 ### new added for photo- wall section  ###
 ### ---------------------------------- ###

 
 function show_photo_like_link($window_id,$type)
 {
         $show_link =TRUE;
		 global $CI;
        //$user_session_data =$CI->user_session_data;  //GETTING STORED SESSION DATA WHILE LOGGED IN
        $logged_user_id = intval(decrypt($CI->session->userdata('user_id')));


        $sql_window_like_all = "SELECT l.id,l.i_liked_user_id, 
								CONCAT(u.s_first_name,' ',u.s_last_name) as s_fullname  ,u.i_user_type
       							from {$CI->db->dbprefix}user_media_like l, {$CI->db->dbprefix}users u
        						where l.i_media_id = '$window_id'
                                AND l.s_media_type = '$type'
        						and u.id=l.i_liked_user_id";
        $result_window_like_all = $CI->db->query($sql_window_like_all);
        $row_window_like_all = $result_window_like_all->num_rows();


	    if($row_window_like_all >0){
		
		     	foreach ($result_window_like_all->result_array() as $get_all_users )
				 {
						if($get_all_users['i_liked_user_id'] == $logged_user_id)
						{
						  $show_link =FALSE;
						}
				 }
			
			
			  }
	  return 	$show_link;	  
 }
 
 
//---------------------------------- count for like-unlike ------------------------------
 function count_photo_like_link($window_id,$type)
 {
        
		 global $CI;
		 $i_total = 0;
        //$user_session_data =$CI->user_session_data;  //GETTING STORED SESSION DATA WHILE LOGGED IN
        $logged_user_id = intval(decrypt($CI->session->userdata('user_id')));


        $sql_window_like_all = "SELECT COUNT(*) as i_total
        from {$CI->db->dbprefix}user_media_like l, {$CI->db->dbprefix}users u
        where l.i_media_id = '$window_id'
        and u.id=l.i_liked_user_id
        AND l.s_media_type = '$type'";
       
        $query = $CI->db->query($sql_window_like_all);
		$result_arr = $query->result_array();

		$i_total = intval($result_arr[0]['i_total']);

	   
	    return 	$i_total;	  
 }
 // --------------------------- end of count for like-unlike -------------------------------
 
 ###------------------------- count for comments ---------------------------###
 
 function count_photo_comment_link($window_id,$type)
 {
        
         global $CI;
         $i_total = 0;
        //$user_session_data =$CI->user_session_data;  //GETTING STORED SESSION DATA WHILE LOGGED IN
        $logged_user_id = intval(decrypt($CI->session->userdata('user_id')));


        $sql_window_comment_all = "SELECT COUNT(*) as i_total
        from {$CI->db->dbprefix}user_media_comments c, {$CI->db->dbprefix}users u
        where c.i_media_id = '$window_id'
        and u.id=c.i_user_id
        AND c.s_media_type = '$type'";
       
        $query = $CI->db->query($sql_window_comment_all);


        $result_arr = $query->result_array();
        
        $i_total = intval($result_arr[0]['i_total']);

       
        return $i_total;      
 }
 

 ### end ###
 

 ###------------------------- count for comments ---------------------------###
 
 function count_event_comment_link($event_id)
 {
        
         global $CI;
         $i_total = 0;
        //$user_session_data =$CI->user_session_data;  //GETTING STORED SESSION DATA WHILE LOGGED IN
        $logged_user_id = intval(decrypt($CI->session->userdata('user_id')));


        $sql_window_comment_all = "SELECT COUNT(*) as i_total
        from {$CI->db->dbprefix}event_comments c, {$CI->db->dbprefix}users u
        where c.i_event_id = '{$event_id}'
        and u.id=c.i_user_id
         ";
       
        $query = $CI->db->query($sql_window_comment_all);
        $result_arr = $query->result_array();
        
        $i_total = intval($result_arr[0]['i_total']);

       
        return $i_total;      
 }
 
 function show_event_rsvp($event_id)
 {
         $show_link =TRUE;
		 global $CI;
        //$user_session_data =$CI->user_session_data;  //GETTING STORED SESSION DATA WHILE LOGGED IN
        $logged_user_id = intval(decrypt($CI->session->userdata('user_id')));


       $sql					 = "SELECT r.id,
									   r.i_user_id, 
									   CONCAT(u.s_first_name,' ',u.s_last_name) as s_fullname  ,
									   u.i_user_type
       								   from {$CI->db->dbprefix}event_rsvp r, 
									  {$CI->db->dbprefix}users u
        							   where r.i_event_id = '$event_id'
        						       and u.id=r.i_user_id";
									   
        $result = $CI->db->query($sql);
        $result_count = $result->num_rows();


	    if($result_count >0){
		
		     	foreach ($result->result_array() as $get_all_users )
				 {
						if($get_all_users['i_user_id'] == $logged_user_id)
						{
						  $show_link =FALSE;
						}
				 }
			
			
			  }
	  return 	$show_link;	  
 }
 

 ### end ###
 
 ### new methods for rings section ###
 
 
 function show_ring_like_link($window_id)
 {
         $show_link =TRUE;
		 global $CI;
        //$user_session_data =$CI->user_session_data;  //GETTING STORED SESSION DATA WHILE LOGGED IN
        $logged_user_id = intval(decrypt($CI->session->userdata('user_id')));


        $sql_window_like_all = "SELECT l.id,l.i_liked_user_id, 
								CONCAT(u.s_first_name,' ',u.s_last_name) as s_fullname  ,u.i_user_type
       							from {$CI->db->dbprefix}user_ring_post_like l, {$CI->db->dbprefix}users u
        						where l.i_ring_post_id = '$window_id'
        						and u.id=l.i_liked_user_id";
								
        $result_window_like_all = $CI->db->query($sql_window_like_all);
        $row_window_like_all = $result_window_like_all->num_rows();


	    if($row_window_like_all >0){
		
		     	foreach ($result_window_like_all->result_array() as $get_all_users )
				 {
						if($get_all_users['i_liked_user_id'] == $logged_user_id)
						{
						  $show_link =FALSE;
						}
				 }
			
			
			  }
	  return 	$show_link;	  
 }
 
 function count_ring_comment_link($window_id)
 {
        
         global $CI;
         $i_total = 0;
        //$user_session_data =$CI->user_session_data;  //GETTING STORED SESSION DATA WHILE LOGGED IN
        $logged_user_id = intval(decrypt($CI->session->userdata('user_id')));


        $sql_window_comment_all = "SELECT COUNT(*) as i_total
        from {$CI->db->dbprefix}user_ring_post_comments c, {$CI->db->dbprefix}users u
        where c.i_ring_post_id = '$window_id'
        and u.id=c.i_user_id";
       
        $query = $CI->db->query($sql_window_comment_all);


        $result_arr = $query->result_array();
        
        $i_total = intval($result_arr[0]['i_total']);

       
        return $i_total;      
} 

//---------------------------------- count for like-unlike ------------------------------
 function count_ring_post_like_link($window_id)
 {
        
		 global $CI;
		 $i_total = 0;
        //$user_session_data =$CI->user_session_data;  //GETTING STORED SESSION DATA WHILE LOGGED IN
        $logged_user_id = intval(decrypt($CI->session->userdata('user_id')));


        $sql_window_like_all = "SELECT COUNT(*) as i_total
        from {$CI->db->dbprefix}user_ring_post_like l, {$CI->db->dbprefix}users u
        where l.i_ring_post_id = '$window_id'
        and u.id=l.i_liked_user_id
        ";
       
        $query = $CI->db->query($sql_window_like_all);
		$result_arr = $query->result_array();

		$i_total = intval($result_arr[0]['i_total']);

	   
	    return 	$i_total;	  
 }
 // --------------------------- end of count for like-unlike -------------------------------
 //------------------------------------------count for word like----------------------
 
 function count_word_like_link($window_id)
 {
	 global $CI;
	 $i_total=0;
	  $logged_user_id = intval(decrypt($CI->session->userdata('user_id')));


        $sql_window_like_all = "SELECT COUNT(*) as i_total
        from {$CI->db->dbprefix}user_word_like l, {$CI->db->dbprefix}users u
        where l.i_word_id = '$window_id'
        and u.id=l.i_liked_user_id
        ";
		//echo $sql_window_like_all;
		 $query = $CI->db->query($sql_window_like_all);
		$result_arr = $query->result_array();

		$i_total = intval($result_arr[0]['i_total']);

	   
	    return 	$i_total;
 }
 function show_word_like_link($window_id)
 {
         $show_link =TRUE;
		 global $CI;
        //$user_session_data =$CI->user_session_data;  //GETTING STORED SESSION DATA WHILE LOGGED IN
        $logged_user_id = intval(decrypt($CI->session->userdata('user_id')));


        $sql_window_like_all = "SELECT l.id,l.i_liked_user_id, 
								CONCAT(u.s_first_name,' ',u.s_last_name) as s_fullname  ,u.i_user_type
       							from {$CI->db->dbprefix}user_word_like l, {$CI->db->dbprefix}users u
        						where l.i_word_id = '$window_id'
        						and u.id=l.i_liked_user_id";
        $result_window_like_all = $CI->db->query($sql_window_like_all);
        $row_window_like_all = $result_window_like_all->num_rows();


	    if($row_window_like_all >0){
		
		     	foreach ($result_window_like_all->result_array() as $get_all_users )
				 {
						if($get_all_users['i_liked_user_id'] == $logged_user_id)
						{
						  $show_link =FALSE;
						}
				 }
			
			
			  }
	  return 	$show_link;	  
 }
 
 
 //------------------------count for comments word
 function count_word_comment_link($window_id)
 {
        
         global $CI;
         $i_total = 0;
        //$user_session_data =$CI->user_session_data;  //GETTING STORED SESSION DATA WHILE LOGGED IN
        $logged_user_id = intval(decrypt($CI->session->userdata('user_id')));


        $sql_window_comment_all = "SELECT COUNT(*) as i_total
        from {$CI->db->dbprefix}user_word_comments c, {$CI->db->dbprefix}users u
        where c.i_word_id = '$window_id'
        and u.id=c.i_user_id";
       
        $query = $CI->db->query($sql_window_comment_all);


        $result_arr = $query->result_array();
        
        $i_total = intval($result_arr[0]['i_total']);

       
        return $i_total;      
 }
 
 function count_church_ring_comment_link($window_id)
 {
        
         global $CI;
         $i_total = 0;
        //$user_session_data =$CI->user_session_data;  //GETTING STORED SESSION DATA WHILE LOGGED IN
        $logged_user_id = intval(decrypt($CI->session->userdata('user_id')));


        $sql_window_comment_all = "SELECT COUNT(*) as i_total
        from {$CI->db->dbprefix}church_ring_post_comments c, {$CI->db->dbprefix}users u
        where c.i_ring_post_id = '$window_id'
        and u.id=c.i_user_id";
       
        $query = $CI->db->query($sql_window_comment_all);


        $result_arr = $query->result_array();
        
        $i_total = intval($result_arr[0]['i_total']);

       
        return $i_total;      
} 

function count_church_post_like_link($window_id)
 {
        
		 global $CI;
		 $i_total = 0;
        //$user_session_data =$CI->user_session_data;  //GETTING STORED SESSION DATA WHILE LOGGED IN
        $logged_user_id = intval(decrypt($CI->session->userdata('user_id')));


        $sql_window_like_all = "SELECT COUNT(*) as i_total
        from {$CI->db->dbprefix}church_ring_post_like l, {$CI->db->dbprefix}users u
        where l.i_ring_post_id = '$window_id'
        and u.id=l.i_liked_user_id
        ";
       
        $query = $CI->db->query($sql_window_like_all);
		$result_arr = $query->result_array();

		$i_total = intval($result_arr[0]['i_total']);

	   
	    return 	$i_total;	  
 }