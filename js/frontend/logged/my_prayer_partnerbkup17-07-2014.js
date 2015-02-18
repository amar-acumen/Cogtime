
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
							get_new_request_notification();
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
				get_new_request_notification();
				if(json_response.success==true)
				{	
					/*if(json_response.updated_flag == 'Y'){
						$("#rec_request_"+json_response.u_id).attr('style','padding-top:5px;display:none;');
					}else{
						$("#rec_request_"+json_response.u_id).attr('style','padding-top:5px;');
					}
					*/
					if(json_response.limit_exceed != 'yes'){
						if(json_response.updated_flag == 'Y' && json_response.html_txt != ''){
									
									
									$('#rec_request_'+json_response.u_id).html(json_response.html_txt);
									
						}
						else{
								$('#rec_request_'+json_response.u_id).attr('style','padding-top:5px;display:none;');
						}
					}
				}
			  //hide_dialog('message_div');
			 hideBusyScreen();
			  showUIMsg(json_response.msg);
	  
		  
		   
		 }
	   });	 
 	}

	 
 function delete_partner(id)
 {
 	
	$('#i_del_partner_id').val(id);
	show_dialog('delete-prayer-partner');
 }
	function delete_prayer_partners_()
	{
		var id = $('#i_del_partner_id').val();
		var URL = base_url +'logged/my_prayer_partners/delete_prayer_partners';
		//showBusyScreen();
		showUILoader_nodialog('<img src="'+base_url+'images/loading_big.gif" width="50"/> ');
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
							hide_dialog('delete-prayer-partner');
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
							hideUILoader_nodialog();
					},
			error: function (data, status, e) {
								hideUILoader_nodialog();
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
 
 
 // optout PRAYER PARTNER //
 
 function optout_()
 {
 	hide_dialog();
	 $.ajax({
		 type: "POST",
		 url: base_url+"logged/my_prayer_partners/optout",
		 
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
 
 
 //------------------------------------------- write prayer partner  -----------------------------

 function if_any_exists_(id)
 {
 	$('#dbId').val(id);
	alert(id);
	//show_dialog('write_prayer_point');
 	
 }
 
 function write_points(user_id){
	 
	//  $('.text_area_prayer_point').attr('id' ,'text_area_prayer_point'+user_id);
	 //$('.dbId').attr('id','dbId'+user_id);
	// $('.dbId').val(user_id);
	// id="text_area_prayer_point<?=$val['user_id']?>" 
 }
 
 function write_prayer_partner_(user_id)
 {
 	
	showUILoader_nodialog('<img src="'+base_url+'images/loading_big.gif" width="50"/> ');
	
	var id = user_id;//$('#dbId'+id).val();
	//alert($('#text_area_prayer_point'+id).val());
	var des = $('#text_area_prayer_point'+id).val();
	//alert(des +'  '+id);
	$.ajax({
		type : "post",
		url : base_url+"logged/my_prayer_partners/write_prayer_partner",
		dataType : "json",
		data : ({'frnd_id' : id , 'des' : des}),
		success : function(data)
		{
			hideUILoader_nodialog();
			//hide_dialog();
			showUIMsg(data.msg);
			$('#text_area_prayer_point'+id).val('');
			
		}
	});
	
 }
 
 function read_prayer_partner_(id)
 {
 	//showUILoader_nodialog('<img src="'+base_url+'images/loading_big.gif" width="50"/> ');
	
	$.ajax({
		type : "post",
		url : base_url+"logged/my_prayer_partners/read_prayer_partner",
		dataType : "json",
		data : ({'frnd_id' : id }),
		success : function(data)
		{
			//alert(data.des);
			  $('#prayer_points'+id).html(base64_decode(data.des));
			//show_dialog('read_prayer_pt');
			// hideUILoader_nodialog();
			
		}
	});
 }
 
 function delete_(id)
 {
 	
	$('#i_del_id').val(id);
	show_dialog('delete-page-popup');
 }
 
 function delete_prayer_points()
	{
		var id=$('#i_del_id').val();
		var URL = base_url +'logged/my_prayer_partners/delete_prayer_points';
		showUILoader_nodialog('<img src="'+base_url+'images/loading_big.gif" width="50"/> ');
		if(ajax_req!=null) {
			ajax_req.abort();
		}
		ajax_req = $.ajax({
			type: 'POST',
			url:URL,
			dataType: 'json',
			data: ({ 'rec_id' : id
				 }),
			success: function (data) {
							var result_obj = data;
							hide_dialog('delete-page-popup');
							if(result_obj.success== true ) {
								    showUIMsg(result_obj.msg);
									$('#pp_'+result_obj.rec_id).attr('style','display:none;');
							}
							else if(result_obj.success==false){
							    showUIMsg(result_obj.msg);
							}
							hideUILoader_nodialog();
					},
			error: function (data, status, e) {
								hideUILoader_nodialog();
								showUIMsg(data.msg);
			}
		});
	}