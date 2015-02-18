//Script for wall and newsfeeds

function show_comments(i_newsfeed_id)
  {
		 
	 showUILoader_nodialog('<img src="'+base_url+'images/loading_big.gif" width="50"/> ');
		$.ajax({
			
			url: base_url +'logged/my_audios/NEW_fetch_comment_audios/'+i_newsfeed_id,
			dataType: 'json',
			type: 'post',
	
			success: function (data, status) {
				$('#audio_comment_div'+i_newsfeed_id).html(data.html_data);
				hideUILoader_nodialog();
				showUIMsg(data.msg);
			},
			error: function(data, status, e) {
				hideUILoader_nodialog();
				showUIMsg("Error!");
				
			}
		});
				 
		 
  }
  
  function show_people_liked(i_newsfeed_id)
  {
	 showUILoader_nodialog('<img src="'+base_url+'images/loading_big.gif" width="50"/> ');
		$.ajax({
			
			url: base_url +'logged/my_audios/new_fetch_likes_on_audios/'+i_newsfeed_id,
			dataType: 'json',
			type: 'post',
	
			success: function (data, status) {
				$('#audio_like_div'+i_newsfeed_id).html(data.html_data);
				hideUILoader_nodialog();
				showUIMsg(data.msg);
			},
			error: function(data, status, e) {
				hideUILoader_nodialog();//('liked_by_div');
				showUIMsg("Error!");
				
			}
		});
				 
		 
  }


// Post comments on a feed //

var ajax_comment_post = null;	
function post_comment(id) 
{
		showUILoader_nodialog('<img src="'+base_url+'images/loading_big.gif" width="50"/> ');
		if(ajax_comment_post!=null) {
			ajax_comment_post.abort();
		}
		ajax_comment_post = $.ajax({
			
			url: base_url+'logged/my_audios/post_comment/'+ id,
			dataType: 'json',
			data: {'message': $('#feed_comment_box_'+id).val()},
			type: 'post',
	
			success: function (data, status) {
				if(data.success == 'true'){
				  $('#feed_comment_box_'+id).val('');
				  $('.comments_div'+id).html(data.html);
				  $('#comment_result_div').html(data.comments_list_html);
				}
				else{
					$('#feed_comment_box_'+id).val('');
				}
				$('.wal-minus').trigger('click');
				hideUILoader_nodialog();
				showUIMsg(data.msg);
			},
			error: function(data, status, e) {
				hideUILoader_nodialog();
				showUIMsg("Error!");
				
			}
		});
}
// Post comments on a feed //

// like unlike comments

function windowLike(window_id,like_val)
{

   showUILoader_nodialog('<img src="'+base_url+'images/loading_big.gif" width="50"/> ');
	$.ajax({
		type: "POST",
		url: base_url+"logged/my_audios/like_unlike",
		data: "window_id="+window_id+"&like_val="+like_val,
		dataType:"json",
		success: function(json_response){
				if(json_response.status)
				{
					$('.liked_by_'+window_id).html(json_response.response_html);
				}
      
		hideUILoader_nodialog();
		}
	});

}

/// audio album wall section //

/// audio album wall section //

function album_windowLike(window_id,like_val)
{

showUILoader_nodialog('<img src="'+base_url+'images/loading_big.gif" width="50"/> ');
	$.ajax({
		type: "POST",
		url: base_url+"logged/my_audio_album_details/like_unlike",
		data: "window_id="+window_id+"&like_val="+like_val,
		dataType:"json",
		success: function(json_response){
				if(json_response.status)
				{
					$('.album_liked_by_'+window_id).html(json_response.response_html);
				}
				hideUILoader_nodialog();
		}
	});

}
 var ajax_comment_post = null;	

function post_album_comment() 
{
		var id=$('#i_audio_album_id').val();
		showUILoader_nodialog('<img src="'+base_url+'images/loading_big.gif" width="50"/> ');
		if(ajax_comment_post!=null) {
			ajax_comment_post.abort();
		}
		ajax_comment_post = $.ajax({
			
			url: base_url+'logged/my_audio_album_details/post_comment_ajax/'+ id,
			dataType: 'json',
			data: {'message': $('#feed_album_comment_box'+id).val()},
			type: 'post',
	
			success: function (data, status) {
				if(data.success == 'true'){
				  $('#feed_album_comment_box'+id).val('');
				  $('#no_comments_div').hide();
				  $('#comment_result_div').html(data.html);
				}
				else{
					$('#feed_album_comment_box'+id).val('');
				}
				hideUILoader_nodialog();
				showUIMsg(data.msg);
			},
			error: function(data, status, e) {
				hideUILoader_nodialog();
				showUIMsg("Error!");
				
			}
		});
}
// Post comments on a feed //


  
  function show_album_people_liked(i_newsfeed_id)
  {
	 showUILoader_nodialog('<img src="'+base_url+'images/loading_big.gif" width="50"/> ');
		$.ajax({
			
			url: base_url +'logged/my_audio_album_details/new_fetch_likes_on_audio_album/'+i_newsfeed_id,
			dataType: 'json',
			type: 'post',
	
			success: function (data, status) {
				$('#audio_album_like_div'+i_newsfeed_id).html(data.html_data);
				hideUILoader_nodialog();
				showUIMsg(data.msg);
			},
			error: function(data, status, e) {
				hideUILoader_nodialog();
				showUIMsg("Error!");
				
			}
		});
				 
		 
  }

