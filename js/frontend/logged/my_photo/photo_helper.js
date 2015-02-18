//Script for wall and newsfeeds

function show_comments(i_newsfeed_id)
  {
		 
	 showUILoader_nodialog('<img src="'+base_url+'images/loading_big.gif" width="50"/> ');
		$.ajax({
			
			url: base_url +'logged/my_photos/NEW_fetch_comment_photos/'+i_newsfeed_id,
			dataType: 'json',
			type: 'post',
	
			success: function (data, status) {
				$('#photo_comment_div'+i_newsfeed_id).html(data.html_data);
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
			
			url: base_url +'logged/my_photos/new_fetch_likes_on_photos/'+i_newsfeed_id,
			dataType: 'json',
			type: 'post',
	
			success: function (data, status) {
				$('#photo_like_div'+i_newsfeed_id).html(data.html_data);
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
/*function show_post_comment_box(i_photo_id)
{
	$('#i_photo_id').val(i_photo_id);
	$('.feed_comment_box').attr('id','feed_comment_box_'+i_photo_id); 
	//show_dialog('comment_div');
}
*/
var ajax_comment_post = null;	
function post_comment(id) 
{
		showUILoader_nodialog('<img src="'+base_url+'images/loading_big.gif" width="50"/> ');
		if(ajax_comment_post!=null) {
			ajax_comment_post.abort();
		}
		ajax_comment_post = $.ajax({
			
			url: base_url+'logged/my_photos/post_comment/'+ id,
			dataType: 'json',
			data: {'message': $('#feed_comment_box_'+id).val()},
			type: 'post',
	
			success: function (data, status) {
				if(data.success == 'true'){
				  $('#feed_comment_box_'+id).val('');
				  $('.comments_div'+id).html(data.html);
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
		url: base_url+"logged/my_photos/like_unlike",
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


