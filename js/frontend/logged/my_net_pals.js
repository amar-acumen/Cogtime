
function invite_netpals_(id)
	{
		var URL = base_url +'logged/my_net_pals/invite_netpals/';
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
						
							
								$('#invite_netpal_'+result_obj.u_id).html(result_obj.html_txt);
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
	
	
	function remove_netpal_id_put_(id)
	{
		$('#i_del_id').val(id);
	}
	
	function remove_netpal_()
	{
		var id = $('#i_del_id').val();
	
		var URL = base_url +'logged/my_net_pals/remove_netpal_request';
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
							hide_dialog();
							if(result_obj.success== true ) {
								showUIMsg(result_obj.msg);
								//$('#cancel_friend_'+result_obj.u_id).html(result_obj.html_txt);
								if(result_obj.has_records=='n')
								{
									$('#netpal_record_'+result_obj.u_id).html(result_obj.html_txt);
									
								}
								else
								{
									$('#netpal_record_'+result_obj.u_id).attr('style','padding-top:5px;display:none;');
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
	
function cancel_netpal_(id)
{

	var URL = base_url +'logged/my_net_pals/cancel_net_pal_request';
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
								//$('#sent_request_'+result_obj.u_id).attr('style','');
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
	
	
	function decline_friend_(id)
	{
		var URL = base_url +'logged/my_net_pals/decline_netpal_request';
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
								showUIMsg(result_obj.msg);
								
								if(result_obj.last_record == 'Y'){
							
								$('#rec_request_'+result_obj.u_id).html(result_obj.html_txt);
							
								}else{
									$('#rec_request_'+result_obj.u_id).attr('style','padding-top:5px;display:none;');
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
	
	
	function invitation_accept_(i_requester_id)
 	{

	  showBusyScreen();
	  
	  //alert(i_requester_id);
	  $.ajax({
		 type: "POST",
		 url: base_url+"logged/my_net_pals/confirm_invitation",
		 data: {'i_requester_id':i_requester_id},
		 dataType:"json",
		 success: function(json_response){
			//alert(json_response);
			get_new_request_notification();
			hideBusyScreen();
			  showUIMsg(json_response.msg);
				if(json_response.success==true)
				{
					
					if(json_response.last_record == 'Y'){
							
							$('#rec_request_'+json_response.u_id).html(json_response.html_txt);
							
					}else{
						$('#rec_request_'+json_response.u_id).attr('style','padding-top:5px;display:none;');
					}
					
					 //$("#rec_request_"+json_response.u_id).attr('style','padding-top:5px;display:none;');
				}
			  //hide_dialog('message_div');
			 
	  
		  
		   
		 }
	   });	 
 }
 
 
 
 
 //---------------------------------------- enlist as net pal ------------------------------------------
 function enlist_net_pal_()
 {
 	hide_dialog();
	 $.ajax({
		 type: "POST",
		 url: base_url+"logged/my_net_pals/enlist_net_pal",
		 
		 dataType:"json",
		 success: function(json_response){
			
			  if(json_response.success==true)
			  {
				  	showUIMsg(json_response.msg);
				  	window.location.href="my-net-pals.html";
			  }
			  else
			  {
			  		showUIMsg(json_response.msg);
			  }
		}
	});
	
	
 }
 
 // opt out from netpal
 
 function opt_out_net_pal()
 {
 	hide_dialog();
	 $.ajax({
		 type: "POST",
		 url: base_url+"logged/my_net_pals/opt_out_net_pal",
		 
		 dataType:"json",
		 success: function(json_response){
			
			  if(json_response.success==true)
			  {
				  	showUIMsg(json_response.msg);
				  	window.location.href="my-net-pals.html";
			  }
			  else
			  {
			  		showUIMsg(json_response.msg);
			  }
		}
	});
	
	
 }
 
 
 