//Script for rings

function show_comments(i_ring_id)
{
		 
	 showUILoader_nodialog('<img src="'+base_url+'images/loading_big.gif" width="50"/> ');
		$.ajax({
			
			url: base_url +'logged/church_ring_home/NEW_fetch_comment_ring/'+i_ring_id,
			dataType: 'json',
			type: 'post',
	
			success: function (data, status) {
				$('#ring_post_comment_div'+i_ring_id).html(data.html_data);
				hideUILoader_nodialog();
				showUIMsg(data.msg);
			},
			error: function(data, status, e) {
				hideUILoader_nodialog();
				showUIMsg("Error!");
				
			}
		});
				 
		 
  }
  
  function show_people_liked(i_ring_id)
  {
	  showUILoader_nodialog('<img src="'+base_url+'images/loading_big.gif" width="50"/> ');
		$.ajax({
			
			url: base_url +'logged/church_ring_home/new_fetch_likes_on_ring/'+i_ring_id,
			dataType: 'json',
			type: 'post',
	
			success: function (data, status) {
				$('#ring_post_like_div'+i_ring_id).html(data.html_data);
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


var ajax_comment_post = null;	
function church_post_comment(id) 
{
		
		showUILoader_nodialog('<img src="'+base_url+'images/loading_big.gif" width="50"/> ');
		if(ajax_comment_post!=null) {
			ajax_comment_post.abort();
		}
		ajax_comment_post = $.ajax({
			
			url: base_url+'logged/church_ring_home/post_comment/'+ id,
			dataType: 'json',
			data: {'message': $('#feed_comment_box_'+id).val(),'ring_id':$('#ring_id').val()},
			type: 'post',
	
			success: function (data, status) {
				$('.wal-minus').trigger('click');  
				if(data.success == 'true'){
				  $('#feed_comment_box_'+id).val('');
				  $('.comments_div'+id).html(data.html);
				  $('#total_ring_comments').html(data.total_comments);
				  
				    show_comments(id);
					if($('#comments_list_div'+id).css('display')=="none"){
						 $('#comments_list_div'+id).slideDown('slow');
					}
				}
				else{
					$('#feed_comment_box_'+id).val('');
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

// like unlike comments

function windowLike(window_id,like_val)
{
showUILoader_nodialog('<img src="'+base_url+'images/loading_big.gif" width="50"/> ');
	$.ajax({
		type: "POST",
		url: base_url+"logged/church_ring_home/like_unlike",
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


// POSTING IN RING 

var ajax_post = null;	
function post_on_church_ring(id) 
{ 
		//var id=$('#i_ring_id').val();
		
		showUILoader_nodialog('<img src="'+base_url+'images/loading_big.gif" width="50"/> ');
		if(ajax_post!=null) {
			ajax_post.abort();
		}
		ajax_comment_post = $.ajax({
			
			url: base_url+'logged/church_ring_home/post_on_ring/'+ id,
			dataType: 'json',
			data: {'message': $('#txt_ring_post').val(), 'txt_title': $('#txt_title').val()},
			type: 'post',
	
			success: function (data, status) {
			        
					$('.error-message').each(function(i){
						$(this).attr('style','display:none');
					});
    
					if(data.success=='false')
					{
						for (var i in data.arr_messages) 
						{ 
						//alert('#err_'+i);
							$('#err_'+i).html(data.arr_messages[i]); 
                            $('#err_'+i).attr('style','display:block');
						};
					}
					else if(data.success=='true')
					{
                        
						showUIMsg(data.msg);
						$('#txt_title').val('Type title here');
						$('#txt_ring_post').val('Max 500 Characters');
						$('#ring_total_posts').html(data.total_posts);
											
						
						 if(data.html != ''){
						  $('#no_ring_posts').hide();
						  $('#ring_post').html(data.html);
						 }
						 
						if(data.view_more==true)
						{
							cur_page = data.cur_page;
							 $('#show_more_div').remove();
							 $('#mid_container').append('<div id="show_more_div"><a href="javascript:void(0);" id="show_more_posts_link" page_value="'+cur_page+'" onclick="show_more_posts('+cur_page+')" class="view-more-btn">[view more] </a> </div>');
						}
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

function ringLimitText(limitField, limitCount, limitNum) {
	if (limitField.value.length > limitNum) {
		limitField.value = limitField.value.substring(0, limitNum);
	} 
}
