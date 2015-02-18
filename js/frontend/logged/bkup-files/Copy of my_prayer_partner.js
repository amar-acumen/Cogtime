
function invite_prayer_partner_(id,display_becomeprayer_partner)
	{
		var URL = base_url +'logged/my_prayer_partners/invite_prayer_partner';
		showBusyScreen();
		if(ajax_req!=null) {
			ajax_req.abort();
		}
		ajax_req = $.ajax({
			type: 'POST',
			url:URL,
			dataType: 'json',
			data: ({ 'frnd_id' : id, 'display_becomeprayer_partner' : display_becomeprayer_partner
				 }),
			success: function (data) {
							var result_obj = data;
							hideBusyScreen();
							if(result_obj.success== true ) {
								showUIMsg(result_obj.msg);
								$('#invite_prayer_partner_'+result_obj.u_id).html(result_obj.html_txt);
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
		var URL = base_url +'logged/my_prayer_partners/cancel_prayer_partner_request';
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
								
								//$('#cancel_friend_'+result_obj.u_id).html(result_obj.html_txt);
								//$('#sent_request_'+result_obj.u_id).attr('style','padding-top:5px;display:none;');
								if(result_obj.last_record == 'Y'){
									$('#sent_request_'+result_obj.u_id).html(result_obj.html_txt);
									//$('#friend_record_'+result_obj.u_id).attr('style','padding-top:5px;display:block;');
								}else{
									$('#sent_request_'+result_obj.u_id).attr('style','padding-top:5px;display:none;');
								}
								showUIMsg(result_obj.msg);
								//showUIMsg(result_obj.msg);
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
	
	
	function decline_prayer_partner_(id)
	{
		var URL = base_url +'logged/my_prayer_partners/decline_prayer_partner_request';
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
		 url: base_url+"logged/my_prayer_partners/confirm_invitation",
		 data: {'i_requester_id':i_requester_id},
		 dataType:"json",
		 success: function(json_response){
			//alert(json_response);
				if(json_response.success==true)
				{	
					/*if(json_response.updated_flag == 'Y'){
						$("#rec_request_"+json_response.u_id).attr('style','padding-top:5px;display:none;');
					}else{
						$("#rec_request_"+json_response.u_id).attr('style','padding-top:5px;');
					}
					*/
					
					if(json_response.updated_flag == 'Y'){
								
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

	function delete_prayer_partners_(id)
	{
		var URL = base_url +'logged/my_prayer_partners/delete_prayer_partners';
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
								//$('#cancel_friend_'+result_obj.u_id).html(result_obj.html_txt);
								//$('#friend_record_'+result_obj.u_id).attr('style','padding-top:5px;display:none;');
								if(result_obj.last_record == 'Y'){
									$('#friend_record_'+result_obj.u_id).html(result_obj.html_txt);
									//$('#friend_record_'+result_obj.u_id).attr('style','padding-top:5px;display:block;');
								}else{
									$('#friend_record_'+result_obj.u_id).attr('style','padding-top:5px;display:none;');
								}
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
 
 // ENLISTING PRAYER PARTNER //
 
 function enlist_()
 {
 	hide_dialog();
	 $.ajax({
		 type: "POST",
		 url: base_url+"logged/my_prayer_partners/enlist_prayer_partner",
		 
		 dataType:"json",
		 success: function(json_response){
			
			  if(json_response.success==true)
			  {
				  	showUIMsg(json_response.msg);
				  	window.location.href="my-prayer-partners.html";
			  }
			  else
			  {
			  		showUIMsg(json_response.msg);
			  }
		}
	});
	
 }
 
 