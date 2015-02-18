function show_tweetpopup(id, align){
	
	$('#hd_tweet_id').val(id); 
	$('body').append('<div class="ghost-layer" onclick="tweet_ghost_layer_click($(this))" ></div>');
	$('[id^="floating_tweet_popup"]').slideUp();
	/*if(align == 'ryt_align')
		$('#floating_tweet_popup'+id).css('top','36px').css('left','-160px');
		
	else*/ if(align == 'detail_align')
		$('#floating_tweet_popup'+id).css('top','123px').css('left','285px');
	else
		$('#floating_tweet_popup'+id).css('top','36px').css('left','-11px');
		
	$('#floating_tweet_popup'+id).slideDown();
	//show_dialog('tweet_div');
}
function tweet_ghost_layer_click(obj)
{
	$('.floating-popup ').each(function(){
			if($(this).css('display')=='block'){
				$(this).hide();	
			}
		});
	obj.remove();
}

var ajax_tweet_post = null; 
function tweet_prayer_request(id) 
{
  //var id  = $('#hd_tweet_id').val();
  var posted_from= 'all_tweet';
 //alert($('#ta_each_post_tweet_'+id).val()); //return false;
 	if($('#ta_each_post_tweet_'+id).val() != 'Max 140 Characters'){
  		var message = $('#ta_each_post_tweet_'+id).val() + ' '+ base64_decode($('#ta_post_tweet_'+id).val()); 
	}
	else{
		var message = base64_decode($('#ta_post_tweet_'+id).val()); 
	}
  
  //alert(message);
  showUILoader_nodialog('<img src="'+base_url+'images/loading_big.gif" width="50"/> ');
  if(ajax_tweet_post!=null) {
   ajax_tweet_post.abort();
  }
  ajax_tweet_post = $.ajax({
   
   url: base_url+'logged/tweet_home/post_prayer_req_tweets/'+posted_from,
   dataType: 'json',
   data: {'message': message},
   type: 'post',
 
   success: function (data, status) {
  
  	//$('#ta_each_post_tweet_'+id).val('');
	//$('#ta_each_post_tweet_'+id).val('Max 140 Characters');
	//hide_dialog('tweet_div');
	close_floating_tweet_popup(id);
   	hideUILoader_nodialog();
    showUIMsg(data.msg);
   },
   error: function(data, status, e) {
  //hideUILoader_nodialog();
    showUIMsg("Error!");
    
   }
  });
}

function close_floating_tweet_popup(id){
		$('#ta_each_post_tweet_'+id).val('');
		$('#ta_each_post_tweet_'+id).val('Max 140 Characters');
		$('#floating_tweet_popup'+id).slideUp();
		$('.ghost-layer').remove();
}
function TweetlimitText(limitField, limitCount, limitNum) {
	if (limitField.value.length > limitNum) {
		limitField.value = limitField.value.substring(0, limitNum);
	} 
}
