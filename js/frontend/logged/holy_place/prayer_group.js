
function check_simlar_group(){
      
       if($('#txt_group_name').val() == ''){
            showUIMsg('Please provide prayer group name!');
            return false;
       }
	   if($('#sel_denomination').val() == '-1'){
		   showUIMsg('Please provide prayer group denomination!');
           return false;
	   }
        
		showUILoader_nodialog('<img src="'+base_url+'images/loading_big.gif" width="50"/> ');
        var datatosend=$("#add_prayer_grp").serialize();
      
          $.ajax({
             type: "POST",
             url: base_url+'logged/prayer_group/search_similar_prayer_group',
             data: datatosend,
             dataType: 'json',
             success: function(data){
                     
				                 
                 if(data.success == false){
					 
					  $('#main_div').hide();
					  $('#srchContent').html('');
					  $('#confirm_bt').hide();
					  $('#err_grp_name').html('<p style="color:#FFFFFF; margin-bottom:10px;">'+data.arr_messages.grp_name+'</p>');
				 }
				 else{
					  if(data.count != 0){
						  // show confirm add
						 // alert(data.grp_html);
						  $('#err_grp_name').html('');
						  $('#main_div').show();
						  //$('#create_bt').hide();
						  $('#srchContent').html('');
						  $('#srchContent').html(data.grp_html);
						  $('#confirm_bt').show();
						  
					  }
					  else 
					  add_group();
				 }
				  
                  showUIMsg(data.msg);
                  hideUILoader_nodialog();
             }
           });     
}



function add_group(){
      
       if($('#txt_group_name').val() == ''){
            showUIMsg('Please provide prayer group name!');
            return false;
       }
	   if($('#sel_denomination').val() == '-1'){
		   showUIMsg('Please provide prayer group denomination!');
           return false;
	   }
       showUILoader_nodialog('<img src="'+base_url+'images/loading_big.gif" width="50"/> ');
        var datatosend=$("#add_prayer_grp").serialize();
      
          $.ajax({
             type: "POST",
             url: base_url+'logged/prayer_group/add_prayer_group',
             data: datatosend,
             dataType: 'json',
             success: function(data){
                                      
                  $('#txt_group_name').val('');  
				  $('#sel_denomination').val('-1'); 
				  $('#main_div').hide(); 
				  if(data.success == true){
					  $('.slid-Create-Prayer').slideToggle('slow');
					  $('#grp_list').html(data.grp_html);
					  hideUILoader_nodialog();
					  showUIMsg(data.msg);
				  }
				  else{
					  hideUILoader_nodialog();
					  showUIMsg(data.msg);
					  return false;
				  }
				  
                  
                  
             }
           });     
}


// POSTING IN PRAYER GROUP

var ajax_post = null; 
function post_prayer_grp_post(group_id) 
{
 
  showUILoader_nodialog('<img src="'+base_url+'images/loading_big.gif" width="50"/> ');
  if(ajax_post!=null) {
   ajax_post.abort();
  }
		ajax_post = $.ajax({
		 
		 url: base_url+'logged/prayer_group/post_message/'+group_id,
		 dataType: 'json',
		 data: {'message': $('#ta_post_prayer_grp').val()},
		 type: 'post',
	   
			   success: function (data, status) {
				if(data.success == 'true'){
				  $('#show_more_div').remove();
				  $('#ta_post_prayer_grp').val('');
				  
				  if(data.html != ''){
						$('#no_prayer_grp_post').hide();
						$('#grp_post_list').html(data.html);
				  }
				   if(data.view_more==true)
					{
						cur_page = data.cur_page;
						 //$('#show_more_div').hide();
						 $('.main-payer-section').append('<div class="view_more custom_show" id="show_more_div"><a href="javascript:void(0);" id="show_more_records_link" page_value="'+cur_page+'" onclick="show_more_records('+cur_page+')">[view more] </a> </div>');
						
					}
					
						 $('.form-prayer-box >.Create-list-box').hide();
				  
				}
			   hideUILoader_nodialog();
				showUIMsg(data.msg);
			   }
		});
}


function edit_prayer_grp_post(post_id){
	
   $('#message_edit_'+post_id).slideToggle('slow');
}

function save_post(post_id, group_id, is_grp_owner, post_owner_id){
	
	showUILoader_nodialog('<img src="'+base_url+'images/loading_big.gif" width="50"/> ');
	$.ajax({
		 
		 url: base_url+'logged/prayer_group/update_post/'+post_id+'/'+group_id,
		 dataType: 'json',
		 data: {'message': $('#ta_edit_post_'+post_id).val()
		 		,'is_grp_owner' : is_grp_owner,
				'post_owner_id': post_owner_id},
		 type: 'post',
	   
			   success: function (data, status) {
				if(data.success == 'true'){
				  $('#show_more_div').remove();
				  
				  if(data.html != ''){
						$('#no_prayer_grp_post').hide();
						$('#grp_post_list').html(data.html);
				  }
				   if(data.view_more==true)
					{
						 cur_page = data.cur_page;
						 $('.main-payer-section').after('<div class="view_more custom_show" id="show_more_div"><a href="javascript:void(0);" id="show_more_records_link" page_value="'+cur_page+'" onclick="show_more_records('+cur_page+')">[view more] </a> </div>');
					}
					
				  
				}
				//$('#message_edit_'+post_id).slideToggle('slow');
			    hideUILoader_nodialog();
				showUIMsg(data.msg);
			   }
		});
}



function invite_prayer_group(user_id){
	var grp_id = $('#current_group_id').val();
	
	var URL = base_url +'logged/prayer_group/invite_to_group';
		showBusyScreen();
		if(ajax_req!=null) {
			ajax_req.abort();
		}
		ajax_req = $.ajax({
			type: 'POST',
			url:URL,
			dataType: 'json',
			data: ({ 'user_id' : user_id,
					 'grp_id' : grp_id
				 }),
			success: function (data) {
							
							hideBusyScreen();
							if(data.success== true ) {
								showUIMsg(data.msg);
								$('#invite_text_'+data.uid).html(data.html);
							}
							else if(data.success==false){
							    showUIMsg(data.msg);
							}
					},
			error: function (data, status, e) {
								hideBusyScreen();
								showUIMsg(data.msg);
			}
		});
	
}



function show_confirm_popup(file_id)
{
	$('#i_del_id').val( file_id );
	show_dialog('remove-post');
}


function delete_prayer_grp_post()
{
	var selected_id = parseInt( $('#i_del_id').val() );
	var grp_id = $('#current_group_id').val();
		//alert(grp_id);
	if( selected_id!=0 )
	{
		hide_dialog('remove-post');
		showUIMsg("Post deleted successfully.");
		var delURL = base_url + 'logged/prayer_group/delete_post/'+ selected_id+'/'+grp_id;
		window.location.href = delURL;
	} else {
	
		var msg = "Sorry an error has occured, Please try again";
		showUIMsg(msg);
		
		hide_dialog();
		
	}
}



// INVITE MEMBER TO CHAT ROOM IN A PRAYER GROUP.

function invite_chat_member() {
	showBusyScreen();

	var csv_recipient_ids = '';
	i = 0;
	$(function() {
		$(':checkbox:checked').each(function(i){
			if($(this).attr('id').substr(0, 9) == 'chkmember') {
				csv_recipient_ids += $(this).val()+',';
			}
		});
	});
	csv_recipient_ids = csv_recipient_ids.substring(0, csv_recipient_ids.length-1);

	if(ajax_req!=null) {
		ajax_req.abort();
	}

	
	 $('#err_send_recepients').attr('style','margin-left:10px;display:none'); 
	 $('.error-message').attr('style','display:none');
	 
	 ajax_req = $.ajax({
		type: 'POST',
		url:base_url+'logged/prayer_group/add_private_chat_room_invite/',
	
		dataType: 'json',
		data: ({'recipients': csv_recipient_ids, 
				'txt_room_password': $('#txt_room_password').val() , 
				'txt_room_name': $('#txt_room_name').val(),
				'date_to1': $('#date_to1').val(),
				'date_end1': $('#date_end1').val(),
				'todo_end_frm': $('#todo_end_frm').val(),
				'todo_strt_frm': $('#todo_strt_frm').val(),
				'group_id': $('#group_id').val()}),
		success: function (json_response)  {
			
			
									if(json_response.success==true)
									  {
									       showUIMsg(json_response.msg);
										   $(function() {
													$(':checkbox:checked').each(function(i){
														if($(this).attr('id').substr(0, 9) == 'chkmember') {
															$(this).attr('checked', false);
														}
													});
												});
												
											$('#txt_room_password').val('');
											$('#txt_room_name').val('');
											$('#date_to1').val('');
											$('#date_end1').val('');
											
											window.location.href = json_response.redirect_loc;
												
									  }
									  else
									  {
									       if(json_response.success==false) 
										   {
									  
												  for ( var id in json_response.arr_messages )
												  {
												   	 if( $('#err_'+id) != null ) 
													   {
														   
															   $('#err_'+id).html(json_response.arr_messages[id]);
															   $('#err_'+id).attr('style','display:block; margin-left:131px;');
														  
												  	}
												  }
											  
											}
									 

									  }
			
			
			 hideBusyScreen();
		},
		error: function (data, status, e) {
			hideBusyScreen();
		}
	});
}



function sendGrpJoinReq(grp_id,  owner_id){
	
	var URL = base_url +'logged/prayer_group/join_group';
		showBusyScreen();
		if(ajax_req!=null) {
			ajax_req.abort();
		}
		ajax_req = $.ajax({
			type: 'POST',
			url:URL,
			dataType: 'json',
			data: ({ 
					 'grp_id' : grp_id,
					 'owner_id':owner_id
				 }),
			success: function (data) {
							
							hideBusyScreen();
							if(data.success== true ) {
								showUIMsg(data.msg);
								$('#invite_text_'+grp_id).html(data.html);
							}
							else if(data.success==false){
							    showUIMsg(data.msg);
							}
					},
			error: function (data, status, e) {
								hideBusyScreen();
								showUIMsg(data.msg);
			}
		});
	
}


function closePrayerChatRoom(roomid, grpid){
	
	
	$.ajax({
				 
				 type: "post",
				 url:base_url + 'logged/prayer_group/deletePrayerChatroom',
				 dataType:"json",
				 data: ({'id':roomid}),
				 success: function(data){
					 
					  if(data.result = 'success') {
						$('#room_link').html('');
						$('#room_link').html('<a href="'+base_url+'prayer-group/'+grpid+'/create-prayer-room.html" class="create-paryer-room">Create Prayer Room</a>');
						showUIMsg("Chat Room has been deleted successfully.");
					  }
					  else {
						  showUIMsg('Some error occurred. Please try again.');
					  }
				  },
				  error: function(){
					  showUIMsg('Some error occurred. Please try again.');
				  }
		});
}

function acceptDeclinePrayerGroupInvitation(grp_id, uid, id, type, rec_id)
  {
	  if(type=='accept')
	  {
		  $.ajax({
			  type: 'get',
			  url: base_url+'logged/prayer_group/accept_req/'+grp_id+'/'+uid+'/'+id+'/1',
			  dataType: 'json',
	  
			  success: function (data) {
				  $('#join_action_content'+rec_id).html('');
				  $('#pending_img'+rec_id).hide();
				  showUIMsg(data.msg);
			  }	// end of success function...
		  });
	  }
	  else if(type=='decline')
	  {
		  $.ajax({
			  type: 'get',
			  url: base_url+'logged/prayer_group/decline_req/'+grp_id+'/'+uid+'/'+id+'/1',
			  dataType: 'json',
	  
			  success: function (data) {
				  $('#join_action_content'+rec_id).html('');
				  $('#pending_img'+rec_id).hide();
				  showUIMsg(data.msg);
			  }	// end of success function...
		  });
	  }
	  else if(type=='joining_req_accept_by_owner')
	  {
		  $.ajax({
			  type: 'get',
			  url: base_url+'logged/prayer_group/accept_join_req/'+grp_id+'/'+uid+'/'+id+'/1',
			  dataType: 'json',
	  
			  success: function (data) {
				  $('#join_action_content'+rec_id).html('');
				  $('#pending_img'+rec_id).hide();
				  showUIMsg(data.msg);
			  }	// end of success function...
		  });
	  }
	  
  }
