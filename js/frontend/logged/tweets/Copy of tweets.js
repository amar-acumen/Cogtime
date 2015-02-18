var ajax_post = null; 
function post_tweet_text(posted_from) 
{
  //var id=$('#i_photo_id').val();
 showUILoader_nodialog('<img src="'+base_url+'images/loading_big.gif" width="50"/> ');
  if(ajax_post!=null) {
   ajax_post.abort();
  }
  ajax_post = $.ajax({
   
   url: base_url+'logged/tweets/post_tweet/'+posted_from,
   dataType: 'json',
   data: {'message': $('#ta_post_tweet').val()},
   type: 'post',
 
   success: function (data, status) {
    if(data.success == 'true'){
	  $('#show_more_div').remove();
	  $('#ta_post_tweet').val('Max 140 Characters');
	  if(data.html != ''){
      		$('#no_tweets').hide();
			$('#tweet_contents').html(data.html);
	  }
	   if(data.view_more==true)
		{
			cur_page = data.cur_page;
			 //$('#show_more_div').hide();
			 $('#mid_container').append('<div class="view_more" id="show_more_div"><a href="javascript:void(0);" id="show_more_tweets_link" page_value="'+cur_page+'" onclick="show_more_tweets('+cur_page+')">[view more] </a> </div>');
		}
	  
    }
   hideUILoader_nodialog();
    showUIMsg(data.msg);
   },
   error: function(data, status, e) {
  //hideUILoader_nodialog();
    showUIMsg("Error!");
    
   }
  });
}


function delete_confirm_(file_id)
{
	//alert(file_id);
	$('#i_del_id').val( file_id );
	show_dialog('delete-popup');
	//return false;
}


function delete_()
{
		var selected_id = parseInt( $('#i_del_id').val() );
		//alert(selected_id);
	if( selected_id!=0 )
	{
		showUIMsg("Tweet deleted successfully.");
		var delURL = base_url + 'logged/tweets/delete_information/'+ selected_id;
		window.location.href = delURL;
	} else {
	
		var msg = "Sorry an error has occured, Please try again";
		showUIMsg(msg);
		
		hide_dialog();
		
	}
}
