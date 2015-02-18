//Script for wall and newsfeeds

function show_photo_details(id)
  {
		 
	 showUILoader('<img src="'+base_url+'images/loading_big.gif" width="50"/> ');
		$.ajax({
			
			url: base_url +'logged/my_photo_album_details/fetch_photo_details/'+id,
			dataType: 'json',
			type: 'post',
	
			success: function (data, status) {
				//hideUILoader();
				$('#photo_html').html(data.html_data);
				hideUILoader('image-details');
				//hideBusyScreen();
				showUIMsg(data.msg);
			},
			error: function(data, status, e) {
				hideUILoader('image-details');
				//show_dialog('view_comments_div');
				showUIMsg("Error!");
				
			}
		});
				 
		 
 }
 
 
 var ajax_comment_post = null;	
function post_comment() 
{
		var id=$('#i_photo_id').val();
		//showUILoader('<img src="'+base_url+'images/loading_big.gif" width="50"/> ');
		//showBusyScreen();
		showUILoader_nodialog('<img src="'+base_url+'images/loading_big.gif" width="50"/> ');
		if(ajax_comment_post!=null) {
			ajax_comment_post.abort();
		}
		ajax_comment_post = $.ajax({
			
			url: base_url+'logged/my_photo_album_details/post_comment_ajax/'+ id,
			dataType: 'json',
			data: {'message': $('#feed_comment_box_'+id).val()},
			type: 'post',
	
			success: function (data, status) {
				//hideUILoader();
				if(data.success == 'true'){
				  $('#feed_comment_box_'+id).val('');
				  $('#no_comments_div').hide();
				  $('#comment_result_div').html(data.html);
				 /* $('#current_media_id').val(data.i_media_id);
				  if(data.no_of_result > 2){
				  		$('#show_more_feeds_div_ajax').attr('page_value',data.cur_page);
				  		$('#show_more_feeds_div_ajax').attr('style','display:block;');
				  }*/
				  //alert(data.no_of_result); alert(data.html);
				 // alert(data.cur_page);
				  
				  
				 // hide_dialog('comment_div');
				}
				else{
					$('#feed_comment_box_'+id).val('');
					// hide_dialog('comment_div');
				}
				//hideBusyScreen();
				hideUILoader_nodialog();
				showUIMsg(data.msg);
			},
			error: function(data, status, e) {
				//hideLoading();
				hideUILoader_nodialog();
				//hideUILoader('');
				showUIMsg("Error!");
				
			}
		});
}
// Post comments on a feed //
  
  
  
  
  
  
  function show_people_liked(i_newsfeed_id)
  {
		 //alert(i_newsfeed_id);
	 showUILoader('<img src="'+base_url+'images/loading_big.gif" width="50"/> ');
		$.ajax({
			
			url: base_url +'logged/my_photos/fetch_people_liked_post/'+i_newsfeed_id,
			dataType: 'json',
			type: 'post',
	
			success: function (data, status) {
				//hideUILoader();
				$('#liked_html').html(data.html_data);
				hideUILoader('liked_by_div');
				//hideBusyScreen();
				showUIMsg(data.msg);
			},
			error: function(data, status, e) {
				hideUILoader('liked_by_div');
				//show_dialog('view_comments_div');
				showUIMsg("Error!");
				
			}
		});
				 
		 
  }




// like unlike comments

function windowLike(window_id,like_val)
{


	//$("#like_load"+window_id).css('display','block');
	//$('#like_load'+window_id).html(LOADING_IMAGE);
//showLoading();
showUILoader_nodialog('<img src="'+base_url+'images/loading_big.gif" width="50"/> ');
	$.ajax({
		type: "POST",
		url: base_url+"logged/my_photos/like_unlike",
		data: "window_id="+window_id+"&like_val="+like_val,
		dataType:"json",
		success: function(json_response){
				if(json_response.status)
				{
					//$('#like_count_'+window_id).html(json_response.response_html);
					$('#liked_by_'+window_id).html(json_response.response_html);
					//$('#window_like'+window_id).remove();

				}
       //showBlockUI(json_response.response_message);
		//$("#like_load"+window_id).html('');
		//$("#like_load"+window_id).css('display','none');
		hideUILoader_nodialog();
		}
	});

}


