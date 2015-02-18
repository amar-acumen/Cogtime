var ajax_post = null; 
function post_tweet_text(posted_from) 
{
  //var id=$('#i_photo_id').val();
 showUILoader_nodialog('<img src="'+base_url+'images/loading_big.gif" width="50"/> ');
var photos = $('input[name="photo[]"]').map(function(){
   return this.value;
}).get();

//console.log(photos); commented on 17-12-2014
  if(ajax_post!=null) {
   ajax_post.abort();
  }
  ajax_post = $.ajax({
   
   url: base_url+'logged/tweet_home/post_his_tweets/'+posted_from,
   dataType: 'json',
   data: {'message': $('#ta_post_tweet').val(),'photo':photos,'txt_video_url':$('#txt_video_url').val()},
   type: 'post',
 
   success: function (data, status) {
    if(data.success == 'true'){
	  $('#show_more_div').remove();
	  $('#ta_post_tweet').val('Max 140 Characters');
	  $('#txt_video_url').val('');
	  if(data.html != ''){
	   $('[id^="myvideo_big_thumb_1_"]').trigger('click');
      		$('#no_tweets').hide();
			$('#tweet_contents').html(data.html);
			$('#count_my_tweets').html(data.total_my_tweets);
			//$("#public_wall_post_form")[0].reset(); 
		$('#all_photos').html('');
		$('#hdnflds').html('');
		
		$('.tab_content ul li').removeClass('select');
		$('.tab_content ul li').first().addClass('select');
		var index = 0;
		$('#txt_video_url').html('');
		$('[id^="myvideo_big_thumb_1_"]').bind('click');
		$('.tab_details > div').hide();
	//	$('.tab_details > div').filter(':eq(' + index + ')').show();
		$('.tab_details .sec-title').filter(':eq(' + index + ')').show();
		$('.tab_details .sec-detail').filter(':eq(' + index + ')').show();
		$('.tab_details .title-body').filter(':eq(' + index + ')').show();
		var v_id=data.i_id;
		$.ajax({
		type: 'post',
		url: base_url+'logged/tweet_home/get_video/',
		data: ({'media_id' : data.i_id ,'width':329 ,'height':212}),

		dataType: 'json',
		success: function (response, status) {
			if(response.result=='success') {
				hideLoading();
			   $('#myvideo_first_thumb_'+v_id).html(response.s_image_source.html);
			   $('#myvideo_first_thumb_'+v_id).attr('i_media_id',response.i_media_id);
			}
		},
		error: function (response, status, e) {
			hideLoading();
			showUIMsg('Some error occurred. Please try again.');
		}
		});		
			
	  }
	   if(data.view_more==true)
		{
			cur_page = data.cur_page;
			 //$('#show_more_div').hide();
			 $('#mid_container').append('<div class="view_more" id="show_more_div"><a href="javascript:void(0);" id="show_more_tweets_link" page_value="'+cur_page+'" onclick="show_more_tweets('+cur_page+')">[view more] </a> </div>');
		}
	show_video();
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


//Post reply on a feed
var ajax_reply_post = null; 
function post_reply(tweet_id,owner_name) 
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
				  $('#ta_tweet_reply'+tweet_id).val(owner_name+':');
				  var total_reply = $('#total_reply'+tweet_id).html();
				  total_reply++;
				  //alert(total_reply);
				  $('#total_reply'+tweet_id).html(total_reply);
				 
				}
				else{
					$('#ta_tweet_reply'+tweet_id).val(owner_name+':');
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
	//var dat=$('#li_fav_block'+id).children('a').text();
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
							//alert(result_obj.action_txt);
							$('#ptext'+result_obj.u_id).html(result_obj.MARK_UNMARK_TXT);
							
							if(result_obj.i_status == 0){
								$('#fav_img'+result_obj.u_id).html('<img src="images/icons/heart.png" alt="favorite" width="14" height="14" class="favorite-icon" title="Favorite">');
							}else
							{
								$('#fav_img'+result_obj.u_id).html('');
							}
							
				location.reload();			
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
 
function view_tweet_report(id)
 {
 	   if($('#tweet_report_block'+id).css('display')=="block"){
			$('#tweet_report_block'+id).slideUp();
			$('#tweet_report_block'+id).removeClass('select');
			return false;
		}
		$('.tweeterBlock .insideTweeterBlock').hide();
		$('.liquid li').removeClass('select');
		
		$('#tweet_report_block'+id).slideDown();
			$('#li_report_block'+id).addClass('select');
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
				  $('[id^="myvideo_big_thumb_1_"]').trigger('click');
				  if(data.html != ''){
						$('#no_tweets').hide();
						$('#tweet_contents').html(data.html);
						$('#count_my_tweets').html(data.total_my_tweets);
						//alert(11);
						show_video();
						//$('#new_right_tweet_contents').html(data.html);
				  }
				   if(data.view_more==true)
					{
						cur_page = data.cur_page;
						 //$('#show_more_div').hide();
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

function post_report(id)
{

if($('#ta_tweet_report'+id).val() == ''){
	  showUIMsg('Please provide reason.');
	  return false;
  }
  showUILoader_nodialog();
  $.ajax({
	  type: 'post',
	  url: base_url + 'public_profile/abuseMedia/'+id+'/tweet',
	  data: {'s_reason':$('#ta_tweet_report'+id).val(),'abuser':$('#abuser_'+id).val()},
	  dataType: 'json',
	  success: function(data, status) {
		  
		  showUIMsg(data.msg);
		  $('#ta_tweet_report'+id).val('');
		  //$('.link-report').trigger('click');
		 // $("#text-section-"+id).css("display","none");
		  hideUILoader_nodialog();
	  }	// end of success function...
  });
}




$(document).ready(function() {
/*
//  $('#myvideo_big_thumb_1').click(function(){
$('[id^="myvideo_big_thumb_1_"]').click(function(){ 
//alert($(this).attr('media_id'));
  showLoading(); 
  var media_id =  $(this).attr('media_id');
  $.ajax({
		type: 'post',
		url: base_url+'logged/tweet_home/get_video/',
		data: ({'media_id' : media_id ,'width':329 ,'height':212}),

		dataType: 'json',
		success: function (data, status) {
			if(data.result=='success') {
				hideLoading();
			   $('#myvideo_first_thumb_'+media_id).html(data.s_image_source.html);
			   $('#myvideo_first_thumb_'+media_id).attr('i_media_id',data.i_media_id);
			}
		},
		error: function (data, status, e) {
			hideLoading();
			showUIMsg('Some error occurred. Please try again.');
		}
	});
});
  $('[id^="myvideo_big_thumb_1_"]').trigger('click');*/
  show_video();
});

function show_video()
{
 
//alert($(this).attr('media_id'));
$('[id^="myvideo_big_thumb_1_"]').each(function(){
  showLoading(); 
  var media_id =  $(this).attr('media_id');
  $.ajax({
		type: 'post',
		url: base_url+'logged/tweet_home/get_video/',
		data: ({'media_id' : media_id ,'width':329 ,'height':212}),

		dataType: 'json',
		success: function (data, status) {
			if(data.result=='success') {
				hideLoading();
			   $('#myvideo_first_thumb_'+media_id).html(data.s_image_source.html);
			   $('#myvideo_first_thumb_'+media_id).attr('i_media_id',data.i_media_id);
			}
		},
		error: function (data, status, e) {
			hideLoading();
			showUIMsg('Some error occurred. Please try again.');
		}
	});
});
}