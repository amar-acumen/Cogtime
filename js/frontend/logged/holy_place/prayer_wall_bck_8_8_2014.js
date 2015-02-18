$(document).ready(function() {
	
	// for AJAX page-submission...
    var options = { 
        beforeSubmit:  show_loader_screen,  // pre-submit callback 
        success:       validateAddEventFrm // post-submit callback 
    }; 
 
    // bind to the form's submit event 
    $('#add_prayer_request').submit(function() {
	
        $(this).ajaxSubmit(options);
		
        return false;
		
    });
	

});


// validate ajax-submission...
function validateAddEventFrm(data)
{
	
   var data = JSON.parse(data);//alert(data.html);
	
	if(data.success==false) 
	{
		
		$('.error-message').each(function(i){
			$(this).attr('style','display:none');
		});
		
		for ( var id in data.arr_messages ){
			//alert(data.arr_messages[id]);
			if( $('#err_'+id) != null ) {
				$('#err_'+id).html(data.arr_messages[id]);
				
				
				if(id == 'date_to1' || id == 'from_time' || id == 'to_time' || id == 'date_end1'){
					
					if(data.arr_messages['from_time'] == "* Please input time in 24 hours format." 
					|| data.arr_messages['to_time'] == "* Please input time in 24 hours format."){
						if(id == 'from_time' || id == 'to_time'){
							$('#err_'+id).attr('style','display:block; margin-left: -161px; font-weight: normal;');
						}else if(id == 'date_to1' || id == 'date_end1'){
							$('#err_'+id).attr('style','display:block; margin-left: -255px; font-weight: normal;');
						}
					}
					else{
						$('#err_'+id).attr('style','display:block; margin-left: -255px; font-weight: normal;');
					}
				}
				else if(id == 'image_msg'){
					$('#err_'+id).attr('style', 'display:block;float: none;');
				}
				else{
					$('#err_'+id).css('display', 'block');
				}
			}
			
		}
		//$('.search-create-btn-holder li').removeClass('');
		
		 //$('#create_box').slideUp();
		// $('.search-create-prayer-box .minimize').fadeIn();
	}
	else {
		  // clearing form
		  $('.error-message').each(function(i){
			$(this).attr('style','display:none');
		 });
		 
		 $('#no_tweets').hide;
		 $('#uploaded_img').attr('src','');
		 $('#photo_name').html('');
		  $('#add_prayer_request')[0].reset();	
		  $('#add_prayer_request').val('Max 500 Char allowed');
		  $('#create_box').slideUp();
		   $('.search-create-prayer-box .minimize').fadeIn();
		  $('#req_content_div').html(base64_decode(data.html));
		  $('#prayer_content_div').html(base64_decode(data.home_html));
		  showUIMsg(data.msg);
		  //window.location.href=document.URL;
		  //window.location.href= base_url+'my-events.html';
	}
	 
	hideUILoader_nodialog();
				
}


function get_selected_photo(){
	
	showUILoader_nodialog('<img src="'+base_url+'images/loading_big.gif" width="50"/> ');
	var image_name = $('input[name=prayer_img_name]:checked').val();
	//alert($('input[name=prayer_img_name]:checked').val());
	$('#hd_image_name').val(image_name);
	
	// var image_name = $('input[name=prayer_img_name]:checked').val();
    var thumb = $('input[name=prayer_img_name]:checked').attr('thumb');

    //alert($('input[name=prayer_img_name]:checked').val());
    $('#hd_image_name').val(image_name);
    $('#photo_name').html(image_name);
   
    $('#uploaded_img').attr('src',thumb);
    $('#uploaded_img').attr('style','display:block;');
	
	
    hide_dialog();
	hideUILoader_nodialog();
}

function show_loader_screen(){
	showUILoader_nodialog('<img src="'+base_url+'images/loading_big.gif" width="50"/> ');
}
// edit $(document).ready(function() {



function delete_confirm_(file_id)
{
	//alert(file_id);
	$('#i_del_id').val( file_id );
	show_dialog('delete-prayer');
	//return false;
}


function delete_()
{
		var selected_id = parseInt( $('#i_del_id').val() );
		//alert(selected_id);
	if( selected_id!=0 )
	{
		showUIMsg("Prayer Request deleted successfully.");
		var delURL = base_url + 'logged/prayer_wall/delete_information/'+ selected_id;
		window.location.href = delURL;
	} else {
	
		var msg = "Sorry an error has occured, Please try again";
		showUIMsg(msg);
		
		hide_dialog();
		
	}
}

function limitText(limitField, limitCount, limitNum, id) {
	
	if(typeof(id)==='undefined') id = '';
	
	if (limitField.value.length > limitNum) {
		limitField.value = limitField.value.substring(0, limitNum);
	} 
	
	$('#text_count'+id).html(limitField.value.length);
}


function show_reply(p_request_id){
	
	$('#commit_form'+p_request_id).slideToggle('slow');
}
//Post reply on a feed
var ajax_reply_post = null; 
function post_reply(p_request_id) 
{
		
		var message  = $('#ta_commit_text_'+p_request_id).val();
		var start_time  = $('#txt_commit_from_time_'+p_request_id).val();
		var start_date  = $('#commit_date_to_'+p_request_id).val();
		var end_time  = $('#txt_commit_to_time_'+p_request_id).val();
		var end_date  = $('#commit_date_end_'+p_request_id).val();
		

		
		var csv_weekdays = ''; 
		$('#weekdays_tr'+p_request_id+' :checkbox:checked').each(function(i){
				csv_weekdays += $(this).val()+', ';
		});
		var chk_day  = csv_weekdays;
		
		
		var csv_time = ''; 
		$('#time_tr'+p_request_id+' :checkbox:checked').each(function(i){
				csv_time += $(this).val()+', ';
		});
		var chk_time  = csv_time; 
		
		showUILoader_nodialog('<img src="'+base_url+'images/loading_big.gif" width="50"/> ');
		if(ajax_reply_post!=null) {
			ajax_reply_post.abort();
		}
		ajax_reply_post = $.ajax({
			
			url: base_url+'logged/prayer_wall/post_commitments/'+ p_request_id,
			dataType: 'json',
			data: {'message':message,
				   'start_time':start_time,
				    'start_date':start_date ,
					'end_time': end_time,	
					'end_date':end_date,
					'chk_day':chk_day,
					'chk_time': chk_time},
			type: 'post',
	
			success: function (data, status) {
				
				if(data.success == 'false'){
					
					  $('.error-message').each(function(i){
						  $(this).attr('style','display:none');
					  });
					  
					  for ( var id in data.arr_messages ){
						  //alert(data.arr_messages[id]);
						  if( $('#err_'+id) != null ) {
							  $('#err_'+id).html(data.arr_messages[id]);
								  $('#err_'+id).css('display', 'block');
						  }
						  
					  }
					  showUIMsg(data.msg);

				}
				else{
					
					$('.error-message').each(function(i){
						$(this).attr('style','display:none');
					});
					$('#total_commits'+p_request_id).html('('+data.total_commits+')');
					$('#add_commit'+p_request_id)[0].reset();	
					
					$('#commit_li'+p_request_id).next().addClass('first');
					$('#commit_li'+p_request_id).hide();
					
		  			$('#ta_commit_text_'+p_request_id).val('Max 140 Char allowed');
					// added for detail page 
					
					$('#commit_content').html(data.html);
					$('#commitment-container'+p_request_id).html(data.html);
					
										
					showUIMsg(data.msg);
					$('#commit_form'+p_request_id).slideToggle('slow');
				}
				
				
				hideUILoader_nodialog();
				//
			},
			error: function(data, status, e) {
				hideUILoader_nodialog();
				showUIMsg("Error!");
				
			}
		});
}



// Post reply on a feed //

function view_commits(id)
 {
 	   	//showUILoader_nodialog('<img src="'+base_url+'images/loading_big.gif" width="50"/> ');
		
	
	$('#commitment-container'+id).slideToggle('slow');
	$('#commitment-container'+id).show();
	
	
	$.ajax({
		type : "post",
		url : base_url+"logged/prayer_wall/viewCommits",
		dataType : "json",
		data : ({'p_request_id' : id }),
		success : function(data)
		{
			  
			  if(data.des != ''){
				$('#commitment_content_'+id).html(base64_decode(data.des));
			  }else{
				  $('#commitment_content_'+id).html("<div class ='view_more' style=' text-align: center; font-size: 15px; color:#013D62;'>No Commitments.</div>");
				 
			  }
			  
			  
			 // hideUILoader_nodialog();
		}
	});
 }
 
 

function show_testimony(p_request_id){
	
	$('#add-testimony'+p_request_id).slideToggle('slow');
}

var ajax_req = null; 
function add_testimony(p_request_id) 
{
		
		var message  = $('#ta_testimony'+p_request_id).val();
		
		showUILoader_nodialog('<img src="'+base_url+'images/loading_big.gif" width="50"/> ');
		if(ajax_req!=null) {
			ajax_req.abort();
		}
		ajax_req = $.ajax({
			
			url: base_url+'logged/prayer_wall/post_testimony/'+ p_request_id,
			dataType: 'json',
			data: {'message':message},
			type: 'post',
	
			success: function (data, status) {
				
				if(data.success == 'false'){
					showUIMsg(data.msg);
				}
				else{
					
					
					$('#add_testimony_frm'+p_request_id)[0].reset();	
		  			$('#ta_commit_text_'+p_request_id).val('Max 500 Char allowed');
					showUIMsg(data.msg);
					$('#add-testimony'+p_request_id).slideToggle('slow');
                    
                    
                    $('#add_testimony_'+p_request_id).remove();
                    $('#edit_prayer_req_'+p_request_id).addClass('first');
                    
				}
				
				
				hideUILoader_nodialog();
				//
			},
			error: function(data, status, e) {
				hideUILoader_nodialog();
				showUIMsg("Error!");
				
			}
		});
}
 
function view_testimony(id)
 {
	
	$('#testimony-container'+id).slideToggle();
	
	$.ajax({
		type : "post",
		url : base_url+"logged/prayer_wall/viewTestimony",
		dataType : "json",
		data : ({'p_request_id' : id }),
		success : function(data)
		{
			  
			  if(data.des != ''){
				$('#testimony_content_'+id).html(base64_decode(data.des));
			  }else{
				  $('#testimony_content_'+id).html("<div class ='view_more' style=' text-align: center; font-size: 15px; color:#013D62;'>No Testimony.</div>");
			  }
			  
			  
			 // hideUILoader_nodialog();
		}
	});
 }
 
 
 
// edit 


function show_edit_prayer(p_request_id){
	
	
	$('#commit-content'+p_request_id).slideToggle();
	$('#comment-for-pray-form'+p_request_id).slideToggle();
}
//Post reply on a feed
function edit_prayer(p_request_id) 
{
		
		var message  = $('#ta_edit_desc'+p_request_id).val();
		var request_type = $('#request_type'+p_request_id).val();
		var start_time  = $('#txt_from_time'+p_request_id).val();
		var start_date  = $('#date_to1'+p_request_id).val();
		var end_time  = $('#txt_to_time'+p_request_id).val();
		var end_date  = $('#date_end1'+p_request_id).val();
		
		var s_subject  = $('#s_subject'+p_request_id).val();
		
		var image_name  = $('#hd_edit_image_name'+p_request_id).val();
		
		showUILoader_nodialog('<img src="'+base_url+'images/loading_big.gif" width="50"/> ');
		$.ajax({
			
			url: base_url+'logged/prayer_wall/edit_prayer_request_ajax/'+ p_request_id,
			dataType: 'json',
			data: {'message':message,
				   'start_time':start_time,
				    'start_date':start_date ,
					'end_time': end_time,	
					'end_date':end_date
					,'request_type':request_type
					,'image_name':image_name,
					's_subject': s_subject},
			type: 'post',
	
			success: function (data, status) {
				
				if(data.success == false){
					
					  $('.error-message').each(function(i){
						  $(this).attr('style','display:none');
					  });
					  
					  for ( var id in data.arr_messages ){
						  //alert(data.arr_messages[id]);
						  if( $('#err_'+id) != null ) {
							  $('#err_'+id).html(data.arr_messages[id]);
								  $('#err_'+id).css('display', 'block');
						  }
						  
					  }

				}
				else{
					
					$('.error-message').each(function(i){
						$(this).attr('style','display:none');
					});
					
					$('#editprayer_'+p_request_id)[0].reset();
					
					
					$('#commit-content'+p_request_id).hide();
					$('#comment-for-pray-form'+p_request_id).slideUp();
					$('#commit-content'+p_request_id).slideDown();
					
					$('#req_content_div').html(data.html);
					showUIMsg(data.msg);
					
				}
				
				
				hideUILoader_nodialog();
				//
			},
			error: function(data, status, e) {
				hideUILoader_nodialog();
				showUIMsg("Error!");
				
			}
		});
}

function show_photo_popup(id){
	
	$('.record_id').val(id);
	show_dialog('edit_photo_div');

}
function get_edit_selected_photo(){

	
	showUILoader_nodialog('<img src="'+base_url+'images/loading_big.gif" width="50"/> ');
	var image_name = $('input[name=edit_prayer_img_name]:checked').val();
	
	var record_id = $('.record_id').val(); //alert(record_id);
	$('#hd_edit_image_name'+record_id).val(image_name);
	//alert($('input[name=prayer_img_name]:checked').val());
	//$('#hd_image_name').val(image_name);
	
	// var image_name = $('input[name=prayer_img_name]:checked').val();
    var thumb = $('input[name=edit_prayer_img_name]:checked').attr('thumb');

    //alert($('input[name=prayer_img_name]:checked').val());
    $('#hd_edit_image_name'+record_id).val(image_name);
    $('#photo_edit_name'+record_id).html(image_name);
   
    $('#uploaded_edit_img'+record_id).attr('src',thumb);
    $('#uploaded_edit_img'+record_id).attr('style','display:block;');
	
	
    hide_dialog();
	hideUILoader_nodialog();
	
}


/// show edit commitments

function show_edit_commitments(s_type ,p_request_id){
	
	$('#comment-for-pray-form_'+s_type+'_'+p_request_id).slideToggle('slow');
	$('#commit-content_'+s_type+'_'+p_request_id).slideToggle('slow');
}
//Post reply on a feed
function edit_commitments(s_type  , commit_id) 
{
		
		var message  = $('#ta_commit_text_'+s_type+'_'+commit_id).val();
		var start_time  = $('#txt_commit_from_time_'+s_type+'_'+commit_id).val();
		var start_date  = $('#commit_date_to_'+s_type+'_'+commit_id).val();
		var end_time  = $('#txt_commit_to_time_'+s_type+'_'+commit_id).val();
		var end_date  = $('#commit_date_end_'+s_type+'_'+commit_id).val();
		
		var prayer_id = $('#prayer_id_'+s_type+'_'+commit_id).val();
		
		var csv_weekdays = ''; 
		$('#weekdays_tr'+s_type+'_'+commit_id+' :checkbox:checked').each(function(i){
				csv_weekdays += $(this).val()+', ';
		});
		var chk_day  = csv_weekdays;
		
		var csv_time = ''; 
		$('#time_tr'+s_type+'_'+commit_id+' :checkbox:checked').each(function(i){
				csv_time += $(this).val()+', ';
		});
		var chk_time  = csv_time; 
		
		
		showUILoader_nodialog('<img src="'+base_url+'images/loading_big.gif" width="50"/> ');
		if(ajax_reply_post!=null) {
			ajax_reply_post.abort();
		}
		ajax_reply_post = $.ajax({
			
			url: base_url+'logged/prayer_wall/edit_commitments/'+ commit_id+'/'+s_type,
			dataType: 'json',
			data: {'message':message,
				   'start_time':start_time,
				    'start_date':start_date ,
					'end_time': end_time,	
					'end_date':end_date,
					'prayer_id':prayer_id,
					'chk_day':chk_day,
					'chk_time': chk_time},
			type: 'post',
	
			success: function (data, status) {
				
				if(data.success == 'false'){
					
					  $('.error-message').each(function(i){
						  $(this).attr('style','display:none');
					  });
					  
					  for ( var id in data.arr_messages ){
						  //alert(data.arr_messages[id]);
						  if( $('#err_'+id) != null ) {
							  $('#err_'+id).html(data.arr_messages[id]);
								  $('#err_'+id).css('display', 'block');
						  }
						  
					  }

				}
				else{
					
					$('.error-message').each(function(i){
						$(this).attr('style','display:none');
					});
					
					$('#commit_content_div').html(data.html);
					$('#commit-content_'+s_type+'_'+commit_id).show();
					$('#comment-for-pray-form_'+s_type+'_'+commit_id).hide();
					
					showUIMsg(data.msg);
					
				}
				
				
				hideUILoader_nodialog();
				//
			},
			error: function(data, status, e) {
				hideUILoader_nodialog();
				showUIMsg("Error!");
				
			}
		});
}


function commitments_delete_confirm_(file_id,s_type)
{
	//alert(file_id);
	$('#i_del_id').val( file_id );
	$('#s_type').val(s_type);
	show_dialog('delete-prayer');
	//return false;
}


function delete_commitments()
{
		var selected_id = parseInt( $('#i_del_id').val() );
		var s_type  = $('#s_type').val();
		//alert(selected_id);
	if( selected_id!=0 )
	{
		showUIMsg("Commitment deleted successfully.");
		var delURL = base_url + 'logged/prayer_wall/delete_commitments/'+ selected_id+'/'+s_type;
		window.location.href = delURL;
	} else {
	
		var msg = "Sorry an error has occured, Please try again";
		showUIMsg(msg);
		
		hide_dialog();
		
	}
}

/// edit testimony 

function minimize_edit_testimony(p_request_id){
	
	
	$('#edit-testimony'+p_request_id).slideToggle('slow');
	$('#view_commit_'+p_request_id).show();
	
}

function show_edit_testimony(p_request_id){
	
	$('#view_commit_'+p_request_id).hide();
	$('#edit-testimony'+p_request_id).slideToggle('slow');
	
}

var ajax_req = null; 
function edit_testimony(p_request_id) 
{
		
		var message  = $('#ta_edit_testimony'+p_request_id).val();
		
		showUILoader_nodialog('<img src="'+base_url+'images/loading_big.gif" width="50"/> ');
		if(ajax_req!=null) {
			ajax_req.abort();
		}
		ajax_req = $.ajax({
			
			url: base_url+'logged/prayer_wall/update_testimony/'+ p_request_id,
			dataType: 'json',
			data: {'message':message},
			type: 'post',
	
			success: function (data, status) {
				
				if(data.success == 'false'){
					showUIMsg(data.msg);
				}
				else{
					
					showUIMsg(data.msg);
					$('#testimony-container'+p_request_id).slideToggle();
					$('#edit-testimony'+p_request_id).slideToggle('slow');
                    
				}
				
				
				hideUILoader_nodialog();
				//
			},
			error: function(data, status, e) {
				hideUILoader_nodialog();
				showUIMsg("Error!");
				
			}
		});
}
 
 
//// new added for weekdays in commits


