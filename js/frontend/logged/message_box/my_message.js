
var ajax_req = null;
function show_message(id) {
	showUILoader_nodialog('<img src="'+base_url+'images/loading_big.gif" width="50"/> ');

	if(ajax_req!=null) {
		ajax_req.abort();
	}

	ajax_req = $.ajax({
		type: 'POST',
		url:base_url+'logged/mymessages/get_message_body/',
		dataType: 'json',
		data: ({'message_id' : id}),
		success: function (data, status) {
			
			$('#mail_content'+id).html(data.content);
			hideUILoader_nodialog();
			//show_dialog('message_div');
		},
		error: function (data, status, e) {
			hideUILoader_nodialog();
		}
	});
}

/* INVITATION ACCEPT REQUEST FUNC FOR NORMAL FRIENDS REQUEST*/

function invitation_accept_contact_request_reject(i_msg_id,i_requester_id,type )
 {
	  showBusyScreen();
	  
	  
	  $.ajax({
		 type: "POST",
		 url: base_url+"logged/my_friends/confirm_invitation_opt",
		 data: {'i_message_id':i_msg_id,'i_requester_id':i_requester_id,'type':type},
		 dataType:"json",
		 success: function(json_response){
			
			 //  hide_dialog('message_div');
			   hideBusyScreen();
			  
			   showUIMsg(json_response.msg);
		   
		 }
	   });	 
 }

/* END INVITATION ACCEPT REQUEST FUNC FOR NORMAL FRIENDS REQUEST*/



/* INVITATION ACCEPT REQUEST FUNC FOR PRAYER PARTNERS REQUEST*/
function invitation_accept_prayer_partner_request_reject(i_msg_id,i_requester_id,type )
 {
	  showBusyScreen();
	  
	  
	  $.ajax({
		 type: "POST",
		 url: base_url+"logged/my_prayer_partners/confirm_invitation_opt",
		 data: {'i_message_id':i_msg_id,'i_requester_id':i_requester_id,'type':type},
		 dataType:"json",
		 success: function(json_response){
				
			   //hide_dialog('message_div');
			   
			   hideBusyScreen();
			   showUIMsg(json_response.msg);
		   
		 }
	   });	 
 }

/* END INVITATION ACCEPT REQUEST FUNC FOR PRAYER PARTNERS REQUEST*/


/* INVITATION ACCEPT REQUEST FUNC FOR NET PAL REQUEST*/

function invitation_accept_net_pal_request_reject(i_msg_id,i_requester_id,type )
 {
	  showBusyScreen();
	  
	  
	  $.ajax({
		 type: "POST",
		 url: base_url+"logged/my_net_pals/confirm_invitation_opt",
		 data: {'i_message_id':i_msg_id,'i_requester_id':i_requester_id,'type':type},
		 dataType:"json",
		 success: function(json_response){
			
			 //  hide_dialog('message_div');
			 
			   hideBusyScreen();
			   showUIMsg(json_response.msg);
		   
		 }
	   });	 
 }

/* END INVITATION ACCEPT REQUEST FUNC FOR NET PAL REQUEST*/








function send_message() {
	showBusyScreen();

	var csv_recipient_ids = '';
	i = 0;
	csv_recipient_ids = $('#chkmember').val();
	csv_recipient_ids = csv_recipient_ids.substring(0, csv_recipient_ids.length-1);
	//alert(csv_recipient_ids);
	if(ajax_req!=null) {
		ajax_req.abort();
	}

	
	 $('#err_send_recepients').attr('style','margin-left: 195px;display:none'); 
	 $('#err_send_message').attr('style','display:none');
	 
	ajax_req = $.ajax({
		type: 'POST',
		url:base_url+'logged/compose-msg/send_message/',
	
		dataType: 'json',
		data: ({'recipients': csv_recipient_ids, 'txt_send_subject': $('#subject').val() , 'txt_send_message': $('#message').val()}),
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
												
											$('#subject').val('');
											$('#message').val('');
											$('.my-compose-minus').trigger('click');
												
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
												$('#err_'+id).show();
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

function checkAll_messages() 
{
	if($('#result_div :checkbox').attr('checked')) {
		$('#result_div :checkbox').attr('checked', false);
	}
	else {
		$('#result_div :checkbox').attr('checked', true);
	}
}


function delete_checked_messages() {
	var j = 0;
	$(':checkbox:checked').each(function(i){
		if($(this).attr('id').substr(0, 8) == 'chk_mail') {
			j++;
		}
	});
	if(j!=0) {
		show_dialog('delete-inboxmsg-popup');
	}
}


function delete_messages() 
{
  	hide_dialog();
 	showBusyScreen();

	var csv_mail_ids = '';
	$(':checkbox:checked').each(function(i){
		if($(this).attr('id').substr(0, 8) == 'chk_mail') {
			csv_mail_ids += $(this).val()+',';
		}
	});
	csv_mail_ids = csv_mail_ids.substring(0, csv_mail_ids.length-1);


	if(ajax_req!=null) {
		ajax_req.abort();
	}

	ajax_req = $.ajax({
		type: 'POST',
		url:base_url+'logged/mymessages/delete_messages/',
		dataType: 'json',
		data: ({'csv_mail_ids': csv_mail_ids ,'current_page' : $('#current_page').val()}),
		success: function (data, status) {
			 hideBusyScreen();
			 showUIMsg(data.msg);
			$('#result_div').html(data.content);
                        /**********17-12-2014******************/
			location.reload();
                        /*****************************/
		},
		error: function (data, status, e) {
			hideBusyScreen();
		}
	});
}

function redirect_reply_message(id, msg_id) {
	
	hide_dialog('message_div');
	window.location.href = base_url+"message/"+id+'/'+msg_id+"/reply-msg.html";
	
}

function reply_message(id) {
	showBusyScreen();

	var csv_recipient_ids = $('#reply_user'+id).val(); 
		
	$('#err_send_message').attr('style','display:none');
	 
	ajax_req = $.ajax({
		type: 'POST',
		url:base_url+'logged/compose-msg/send_message/',
	
		dataType: 'json',
		data: ({'recipients': csv_recipient_ids, 'txt_send_subject': $('#subject'+id).val() , 'txt_send_message': $('#message'+id).val()}),
		success: function (json_response)  {
			
			
									if(json_response.success==true)
									  {
									       showUIMsg(json_response.msg);										
											$('#subject').val('');
											$('#message').val('');
											//window.location.href = "my-msg-inbox.html";
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
												$('#err_'+id).show();
											   }
											  }
											 }
									  }
			
			
			 hideBusyScreen();
			 $('.rlp-btn').trigger('click');	
		},
		error: function (data, status, e) {
			hideBusyScreen();
		}
	});
}

function send_message_from_public_profile() {
    
	showUILoader_nodialog('<img src="'+base_url+'images/loading_big.gif" width="50"/> ');
	
	if(ajax_req!=null) {
		ajax_req.abort();
	}

	 $('#err_send_message').attr('style','display:none;margin-left:138px;');
	 
	ajax_req = $.ajax({
		type: 'POST',
		url:base_url+'logged/compose-msg/send_message/',
	
		dataType: 'json',
		data: ({'recipients': $('#recipent_id').val(), 'txt_send_subject': $('#subject').val() , 'txt_send_message': $('#message').val()}),
		success: function (json_response)  {
			
			
									if(json_response.success==true)
									  {
									       showUIMsg(json_response.msg);
										   	$('#subject').val('');
											$('#message').val('');
											//hide_dialog('send_message_div');
											$('.wal-minus').trigger('click');
												
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
												$('#err_'+id).show();
											   }
											  }
											  
											 }
									 

									  }
			
			
			hideUILoader_nodialog();
		},
		error: function (data, status, e) {
			hideUILoader_nodialog();
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
				//hide_dialog();
				hideUILoader_nodialog();
				showUIMsg(data.msg);
			},
			error: function(data, status, e) {
				hideUILoader_nodialog();
				showUIMsg("Error!");
			}
		});
				 
		 
}
  
  
  
  function acceptRingInvitation(i_u_id,i_ring_id)
  {
		 
	 showUILoader_nodialog('<img src="'+base_url+'images/loading_big.gif" width="50"/> ');
		$.ajax({
			
			url: base_url +'all/accept_invitation/'+i_u_id+'/'+i_ring_id+'/1',
			dataType: 'json',
			type: 'post',
	
			success: function (data) {
				//hide_dialog();
				hideUILoader_nodialog();
				showUIMsg(data.msg);
			},
			error: function(data, status, e) {
				hideUILoader_nodialog();
				showUIMsg("Error!");
				
			}
		});
				 
		 
  }
  
  
  function acceptDeclineInvitation(ringid, uid, id, type)
  {
	  if(type=='accept')
	  {
		  $.ajax({
			  type: 'get',
			  url: base_url+'logged/ring_home/accept_req/'+ringid+'/'+uid+'/'+id+'/1',
			  dataType: 'json',
	  
			  success: function (data) {
				  showUIMsg(data.msg);
				 //// hide_dialog();
			  }	// end of success function...
		  });
	  }
	  else if(type=='decline')
	  {
		  $.ajax({
			  type: 'get',
			  url: base_url+'logged/ring_home/decline_req/'+ringid+'/'+uid+'/'+id+'/1',
			  dataType: 'json',
	  
			  success: function (data) {
				  showUIMsg(data.msg);
				 // hide_dialog();
			  }	// end of success function...
		  });
	  }
  }
  
  
  
  
  function acceptDecline_PrayerGroup_Invitation(grp_id, uid, id, type)
  {
	  if(type=='accept')
	  {
		  $.ajax({
			  type: 'get',
			  url: base_url+'logged/prayer_group/accept_req/'+grp_id+'/'+uid+'/'+id+'/1',
			  dataType: 'json',
	  
			  success: function (data) {
				  showUIMsg(data.msg);
				 // hide_dialog();
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
				  showUIMsg(data.msg);
				 // hide_dialog();
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
				  showUIMsg(data.msg);
				 // hide_dialog();
			  }	// end of success function...
		  });
	  }
	  
  }
  
  function join_PrayerGroup_Chat_Invitation(room_id, uid, id)
  {
		  $.ajax({
			  type: 'get',
			  url: base_url+'logged/prayer_group/join_chat_room/'+room_id+'/'+uid+'/'+id+'/1',
			  dataType: 'json',
	  
			  success: function (data) {
				  showUIMsg(data.msg);
				 // hide_dialog();
			  }	// end of success function...
		  });
  }


function tradeAcceptDeny(id,type,prodid)
{
	$.ajax({
			"type":'post',
			"data":'id='+id+'&type='+type+'&prodid='+prodid,
			"dataType":'json',
			"url":'<?php base_url()?>logged/e_trade/accept_deny',
			"success": function(data)
						{
							if(data.success==false)
							{
								showUIMsg(data.msg);
							}
							else
							{
								if(data.html!='')
								{
									$('#rating_div_'+id).html(data.html);
									$('#stock_'+data.stockid).html(data.stockval);
								}
								showUIMsg(data.msg);
							}
						}
		
		});
}


function forwardMessage(msgid) {
	showBusyScreen();
	var csv_recipient_ids = '';
	i = 0;
	csv_recipient_ids = $('#chkfmember').val();
	csv_recipient_ids = csv_recipient_ids.substring(0, csv_recipient_ids.length-1);
	//alert(csv_recipient_ids);
	if(ajax_req!=null) {
		ajax_req.abort();
	}

	
	 $('#err_frwd_recepients').attr('style','margin-left: 195px;display:none'); 
	 $('#err_send_message').attr('style','display:none');
	 
	ajax_req = $.ajax({
		type: 'POST',
		url:base_url+'logged/compose-msg/forward_message/',
	
		dataType: 'json',
		data: ({'recipients': csv_recipient_ids, 'txt_send_subject': $('#frwd_subject'+msgid).val() , 'txt_send_message': $('#frwd_message'+msgid).val()}),
		success: function (json_response)  {
			
			
									if(json_response.success==true)
									  {
									       showUIMsg(json_response.msg);
										   $(function() {
													$(':checkbox:checked').each(function(i){
														if($(this).attr('id').substr(0, 9) == 'chkfmember') {
															$(this).attr('checked', false);
														}
													});
												});
												
											$('#frwd_subject'+msgid).val('');
											$('#frwd_message'+msgid).val('');
											$('#forward-msg-div'+msgid).slideToggle();
												
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
												$('#err_'+id).show();
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