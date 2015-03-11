//<!--Script for wall and newsfeeds-->
// Post comments on a feed //


function show_post_comment_box(i_newsfeed_id)
{
    console.log(i_newsfeed_id);
    if(i_newsfeed_id != undefined){
        $('#i_newsfeed_id').val(i_newsfeed_id);
        $('.feed_comment_box').attr('id','feed_comment_box_'+i_newsfeed_id);

        $('.new-wl > .comments-number').css('display','none');
        $('.new-wl-right > .comments-number').css('display','none');
        $('#post-comment-box'+i_newsfeed_id).slideDown('slow');
        //show_dialog('comment_div');
    }
}


var ajax_comment_post = null;	
function post_comment(id) 
{
		//var id=$('#i_newsfeed_id').val();
		showUILoader_nodialog('<img src="'+base_url+'images/loading_big.gif" width="50"/> ');
		if(ajax_comment_post!=null) {
			ajax_comment_post.abort();
		}
		ajax_comment_post = $.ajax({
			
			url: base_url+'newsfeed/post_comment/'+ id,
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
					 //hide_dialog('comment_div');
				}
				//$('#post-comment-box'+id).slideUp('slow');
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
		url: base_url+"newsfeed/like_unlike",
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


	 
function delete_(id)
{
  alert('delete');
  $('#i_del_post_id').val(id);
  show_dialog('delete-post');
}

var ajax_delete_post = null;	
function delete_post() 
{
	alert('delete_post');
		var id = $('#i_del_post_id').val();
		showUILoader_nodialog('<img src="'+base_url+'images/loading_big.gif" width="50"/> ');
		if(ajax_delete_post!=null) {
			ajax_delete_post.abort();
		}
		ajax_delete_post = $.ajax({
			
			url: base_url+'logged/my_wall/delete_post/'+ id,
			dataType: 'json',
			data: {'newsfeed_id':id},
			type: 'post',
	
			success: function (data, status) {
				
				if(data.result == 'success'){
					
					hide_dialog('delete-post');
					hideUILoader_nodialog();
				    showUIMsg(data.msg);
					
					window.location.href = base_url+'my-wall.html';
				}
				
			},
			error: function(data, status, e) {
				hideUILoader_nodialog();
				showUIMsg("Error!");
				
			}
		});
}


function limitTextarea(limitField, limitCount, limitNum) {
	if (limitField.value.length > limitNum) {
		limitField.value = limitField.value.substring(0, limitNum);
	} 
}

// Post comments on a feed //