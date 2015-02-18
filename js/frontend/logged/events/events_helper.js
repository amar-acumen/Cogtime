//Script for comments

function show_comments(i_newsfeed_id)
  {
	 showUILoader('<img src="'+base_url+'images/loading_big.gif" width="50"/> ');
		$.ajax({
			
			url: base_url +'logged/my_events/NEW_fetch_comment_events/'+i_newsfeed_id,
			dataType: 'json',
			type: 'post',
	
			success: function (data, status) {
				$('#post_comment_div'+i_newsfeed_id).html(data.html_data);
				showUIMsg(data.msg);
			},
			error: function(data, status, e) {
				hideUILoader('view_comments_div');
				//show_dialog('view_comments_div');
				showUIMsg("Error!");
				
			}
		});
				 
		 
  }
  



function post_comment(id) 
{
	
		showUILoader_nodialog('<img src="'+base_url+'images/loading_big.gif" width="50"/> ');
		$.ajax({
			
			url: base_url+'logged/my_events/post_comment/'+ id,
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


function send_rsvp(i_event_id)
  {
		 
	 showUILoader_nodialog('<img src="'+base_url+'images/loading_big.gif" width="50"/> ');
		$.ajax({
			
			url: base_url +'logged/my_events/send_rsvp/'+i_event_id,
			dataType: 'json',
			type: 'post',
	
			success: function (data, status) {
				$('#send_rsvp_'+i_event_id).hide();
				hideUILoader_nodialog();
				showUIMsg(data.msg);
			},
			error: function(data, status, e) {
				hideUILoader_nodialog();
				showUIMsg("Error!");
				
			}
		});
				 
		 
  }


function send_feedback(i_event_id)
{
	   
	  showUILoader_nodialog('<img src="'+base_url+'images/loading_big.gif" width="50"/> ');
	  $.ajax({
		  
		  url: base_url +'logged/my_events/send_feedback/'+i_event_id,
		  dataType: 'json',
		  type: 'post',
		  data: {'message': $('#feedback_box'+i_event_id).val()},
  
		  success: function (data, status) {
			  
			  $('#feedback_box'+i_event_id).val('');
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
function delete_event(file_id)
{
	$('#i_del_id').val( file_id );
	show_dialog('delete-event');
}


function delete_()
{
	var selected_id = parseInt( $('#i_del_id').val() );
	//alert(selected_id);
	if( selected_id!=0 )
	{
		showUIMsg("Event deleted successfully.");
		var delURL = base_url + 'logged/my_events/delete_information/'+ selected_id;
		window.location.href = delURL;
	} else {
	
		var msg = "Error.";
		showUIMsg(msg);
		hide_dialog();
		
	}
}


var ajax_comment_post = null;	
function post_events_comment_ajax(id) 
{
		//var id=$('#i_event_id').val();

		showUILoader_nodialog('<img src="'+base_url+'images/loading_big.gif" width="50"/> ');
		if(ajax_comment_post!=null) {
			ajax_comment_post.abort();
		}
		ajax_comment_post = $.ajax({
			
			url: base_url+'logged/events/post_comment_ajax/'+ id,
			dataType: 'json',
			data: {'message': $('#feed_comment_box_'+id).val()},
			type: 'post',
	
			success: function (data, status) {
				//hideUILoader();
				if(data.success == 'true'){
				  $('#feed_comment_box_'+id).val('');
				 
				  $('#comment_div').html(data.html);
				  //$('#comments_div'+id).val(data.total_comments);
				 
				}
				else{
					$('#feed_comment_box_'+id).val('');
					
				}
				 if(data.view_more==true)
				  {
					  cur_page = data.cur_page;
					   $('#show_more_feeds_div').remove();
					   $('#mid_container').append('<div class="view_more" id="show_more_feeds_div"><a href="javascript:void(0);" id="show_more_feeds_link" page_value="'+cur_page+'" onclick="show_more_feeds('+cur_page+')">[view more]ggg </a> </div>');
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


