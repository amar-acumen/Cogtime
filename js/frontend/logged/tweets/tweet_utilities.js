
//Post reply on a feed
var ajax_reply_post = null; 
function post_reply(tweet_id) 
{
		
		showUILoader_nodialog('<img src="'+base_url+'images/loading_big.gif" width="50"/> ');
		if(ajax_reply_post!=null) {
			ajax_reply_post.abort();
		}
		ajax_reply_post = $.ajax({
			
			url: base_url+'logged/tweet_home/post_reply/'+ tweet_id,
			dataType: 'json',
			data: {'message': $('#ta_tweet_reply'+tweet_id).val()},
			type: 'post',
	
			success: function (data, status) {
				if(data.success == 'true'){
				  $('#ta_tweet_reply'+tweet_id).val('');
				  var total_reply = $('#total_reply'+tweet_id).html();
				  total_reply++;
				  //alert(total_reply);
				  $('#total_reply'+tweet_id).html(total_reply);
				 
				}
				else{
					$('#ta_tweet_reply'+tweet_id).val('');
			    }
				$('.insideTweeterBlock').slideUp();
				hideUILoader_nodialog();
				showUIMsg(data.msg);
			},
			error: function(data, status, e) {
				hideUILoader_nodialog();
				showUIMsg("Error!");
				
			}
		});
}
// Post reply on a feed //

function open_mark_fav(id){
	
	   if($('#tweet_fav_block'+id).css('display')=="block"){
			$('#tweet_fav_block'+id).slideUp();
			$('#tweet_fav_block'+id).removeClass('select');
			return false;
		}
		
		$('.tweeterBlock .insideTweeterBlock').hide();
		$('.liquid li').removeClass('select');
		
		$('#tweet_fav_block'+id).slideDown();
		$('#li_fav_block'+id).addClass('select');
}



function mark_fav_tweet(tweet_id,i_status ,cur_status)
{
		
	var URL = base_url +'logged/tweet_home/markFavTweet/';
	showUILoader_nodialog('<img src="'+base_url+'images/loading_big.gif" width="50"/> ');
	if(ajax_req!=null) {
		ajax_req.abort();
	}
	ajax_req = $.ajax({
		type: 'POST',
		url:URL,
		dataType: 'json',
		data: ({ 'record_id' : tweet_id
				 ,'i_status' : i_status
				 ,'cur_status' : cur_status
			 }),
		success: function (data, status) {
						var result_obj = data;
						hideUILoader_nodialog();
						if(result_obj.result=='success' && result_obj.redirect == false ) {
							showUIMsg(result_obj.msg);
							$('#'+result_obj.u_id+'_status').html(result_obj.action_txt);
							$('#ptext'+result_obj.u_id).html(result_obj.MARK_UNMARK_TXT);
							
							if(result_obj.i_status == 0){
								$('#fav_img'+result_obj.u_id).html('<img src="images/icons/heart.png" alt="favorite" width="14" height="14" class="favorite-icon" title="Favorite">');
							}else
							{
								$('#fav_img'+result_obj.u_id).html('');
							}
							
							
						}
						else if(result_obj.result==false && result_obj.redirect == true){
							showUIMsg(result_obj.msg);
						}
						$('.insideTweeterBlock').slideUp();
				},
		error: function (data, status, e) {
							hideUILoader_nodialog();
							showUIMsg(data.msg);
		}
	});
}

function view_tweet_reply(id)
 {
 	   if($('#tweet_reply_block'+id).css('display')=="block"){
			$('#tweet_reply_block'+id).slideUp();
			$('#tweet_reply_block'+id).removeClass('select');
			return false;
		}
		$('.tweeterBlock .insideTweeterBlock').hide();
		$('.liquid li').removeClass('select');
		
		$('#tweet_reply_block'+id).slideDown();
			$('#li_reply_block'+id).addClass('select');
	
	
	//showUILoader_nodialog('<img src="'+base_url+'images/loading_big.gif" width="50"/> ');
	
	$.ajax({
		type : "post",
		url : base_url+"logged/tweet_home/viewTweetReply",
		dataType : "json",
		data : ({'tweet_id' : id }),
		success : function(data)
		{
			  $('#tweet_reply'+id).html(base64_decode(data.des));
			 // hideUILoader_nodialog();
		}
	});
 }
 

 
 function slideDownRetweet(id){
	 if($('#retweet_block'+id).css('display')=="block"){
			$('#retweet_block'+id).slideUp();
			$('#retweet_block'+id).removeClass('select');
			return false;
		}
		$('.tweeterBlock .insideTweeterBlock').hide();
		$('.liquid li').removeClass('select');
		
		$('#retweet_block'+id).slideDown();
		$('#li_retweet_block'+id).addClass('select');
	
 }
 
 function retweet(posted_from,tweet_id)
 {
 	   
	showUILoader_nodialog('<img src="'+base_url+'images/loading_big.gif" width="50"/> ');
	$.ajax({
			
			url: base_url+'logged/tweet_home/retweet/'+posted_from +'/'+ tweet_id,
			dataType: 'json',
			type: 'post',
	
			success: function (data, status) {
				 
				 if(data.success == 'true' && posted_from != 'right_bar'){
				  $('#show_more_div').remove();
				  $('#ta_post_tweet').val('Max 140 Characters');
				  if(data.html != ''){
						$('#no_tweets').hide();
						$('#tweet_contents').html(data.html);
						$('#count_my_tweets').html(data.total_my_tweets);
						alert(11);
						//$('#new_right_tweet_contents').html(data.html);
				  }
				   if(data.view_more==true)
					{
						cur_page = data.cur_page;
						 //$('#show_more_div').hide();
						 alert(22);
						 $('#mid_container').append('<div class="view_more" id="show_more_div"><a href="javascript:void(0);" id="show_more_tweets_link" page_value="'+cur_page+'" onclick="show_more_tweets('+cur_page+')">[view more] </a> </div>');
					}
				  
				}	
				else if(data.success == 'true' && posted_from == 'right_bar'){
					//alert(1);
					$('#new_right_tweet_contents').html(data.html);
				}
				$('.insideTweeterBlock').slideUp();
				hideUILoader_nodialog();
			},
			error: function(data, status, e) {
				hideUILoader_nodialog();
				showUIMsg("Error!");
				
			}
		});
 }
 
 
/// addded for new feature on prayer wall tweet option
