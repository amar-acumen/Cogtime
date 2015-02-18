
function invite_friend_(id)
	{
		var URL = base_url +'logged/my_friends/invite_friend';
		showBusyScreen();
		if(ajax_req!=null) {
			ajax_req.abort();
		}
		ajax_req = $.ajax({
			type: 'POST',
			url:URL,
			dataType: 'json',
			data: ({ 'frnd_id' : id
				 }),
			success: function (data) {
							var result_obj = data;
							hideBusyScreen();
							if(result_obj.success== true ) {
								showUIMsg(result_obj.msg);
								$('#invite_friend_'+result_obj.u_id).html(result_obj.html_txt);
							}
							else if(result_obj.success==false){
							    showUIMsg(result_obj.msg);
							}
					},
			error: function (data, status, e) {
								hideBusyScreen();
								showUIMsg(data.msg);
			}
		});
	}
	
	
	function cancel_friend_(id)
	{
		var URL = base_url +'logged/my_friends/cancel_friend_request';
		showBusyScreen();
		if(ajax_req!=null) {
			ajax_req.abort();
		}
		ajax_req = $.ajax({
			type: 'POST',
			url:URL,
			dataType: 'json',
			data: ({ 'frnd_id' : id
				 }),
			success: function (data) {
							var result_obj = data;
							hideBusyScreen();
							if(result_obj.success== true ) {
								
								if(result_obj.last_record == 'Y'){
									$('#sent_request_'+result_obj.u_id).html(result_obj.html_txt);
									//$('#friend_record_'+result_obj.u_id).attr('style','padding-top:5px;display:block;');
								}else{
									$('#sent_request_'+result_obj.u_id).attr('style','padding-top:5px;display:none;');
								}
								showUIMsg(result_obj.msg);
								//showUIMsg(result_obj.msg);
								//$('#cancel_friend_'+result_obj.u_id).html(result_obj.html_txt);
								//$('#sent_request_'+result_obj.u_id).attr('style','padding-top:5px;display:none;');
							}
							else if(result_obj.success==false){
							    showUIMsg(result_obj.msg);
							}
					},
			error: function (data, status, e) {
								hideBusyScreen();
								showUIMsg(data.msg);
			}
		});
	}
	
/* Decline any friend request . */
	
	
	function decline_friend_(id)
	{
		var URL = base_url +'logged/my_friends/decline_friend_request';
		showBusyScreen();
		if(ajax_req!=null) {
			ajax_req.abort();
		}
		ajax_req = $.ajax({
			type: 'POST',
			url:URL,
			dataType: 'json',
			data: ({ 'frnd_id' : id
				 }),
			success: function (data) {
							var result_obj = data;
							get_new_request_notification();
							hideBusyScreen();
							if(result_obj.success== true ) {
								//showUIMsg(result_obj.msg);
								//$('#rec_request_'+result_obj.u_id).attr('style','padding-top:5px;display:none;');
								
								if(result_obj.last_record == 'Y'){
									$('#rec_request_'+result_obj.u_id).html(result_obj.html_txt);
									//$('#friend_record_'+result_obj.u_id).attr('style','padding-top:5px;display:block;');
								}else{
									$('#rec_request_'+result_obj.u_id).attr('style','padding-top:5px;display:none;');
								}
								showUIMsg(result_obj.msg);
							}
							else if(result_obj.success==false){
							    showUIMsg(result_obj.msg);
							}
					},
			error: function (data, status, e) {
								hideBusyScreen();
								showUIMsg(data.msg);
			}
		});
	}
	
	
	function invitation_accept_(i_requester_id)
 	{
	  showBusyScreen();
	  
	  //alert(i_requester_id);
	  $.ajax({
		 type: "POST",
		 url: base_url+"logged/my_friends/confirm_invitation",
		 data: {'i_requester_id':i_requester_id},
		 dataType:"json",
		 success: function(json_response){
			//alert(json_response);
				if(json_response.success==true)
				{
					 //$("#rec_request_"+json_response.u_id).attr('style','padding-top:5px;display:none;');
					 //$("div$id="+json_response.u_id).attr('style','padding-top:5px;display:none;');
					 /*if($("#sent_request_"+json_response.u_id).length)		// if exists
					 	alert("test passed");
					*/
					get_new_request_notification();
					if(json_response.last_record == 'Y'){
							
							$('#rec_request_'+json_response.u_id).html(json_response.html_txt);
							
					}else{
						$('#rec_request_'+json_response.u_id).attr('style','padding-top:5px;display:none;');
					}
				}
			  //hide_dialog('message_div');
			 hideBusyScreen();
			  showUIMsg(json_response.msg);
	  
		  
		   
		 }
	   });	 
 }
 
 
 