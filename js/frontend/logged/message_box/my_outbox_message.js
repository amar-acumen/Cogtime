
var ajax_req = null;
function show_message(id) {
	showBusyScreen();

	if(ajax_req!=null) {
		ajax_req.abort();
	}

   // alert(id);

	ajax_req = $.ajax({
		type: 'POST',
		url:base_url+'logged/my-msg-outbox/get_message_body/',
		dataType: 'json',
		data: ({'message_id' : id}),
		success: function (data, status) {
			
			$('#mail_content').html(data.content);
			hideBusyScreen()
			show_dialog('message_div');
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
		show_dialog('delete-msg-popup');
	}
}


function delete_messages() 
{
  	hide_dialog();
 	showBusyScreen();

	var csv_mail_ids = '';
	$(':checkbox:checked').each(function(i){
		//alert($(this).attr('id'));
		if($(this).attr('id').substr(0, 8) == 'chk_mail') {
			csv_mail_ids += $(this).val()+',';
		}
	});
	csv_mail_ids = csv_mail_ids.substring(0, csv_mail_ids.length-1);

	//alert(csv_mail_ids);

	if(ajax_req!=null) {
		ajax_req.abort();
	}

	ajax_req = $.ajax({
		type: 'POST',
		url:base_url+'logged/my_msg_outbox/delete_messages/',
		dataType: 'json',
		data: ({'csv_mail_ids': csv_mail_ids ,'current_page' : $('#current_page').val()}),
		success: function (data, status) {
			 hideBusyScreen();
			 showUIMsg(data.msg);
			$('#result_div').html(data.content);
			
		},
		error: function (data, status, e) {
			hideBusyScreen();
			//closeDiv_WriteMsg('writeMsg');
		}
	});
}

function reply_message(id) {
	
	hide_dialog('message_div');
	window.location.href = base_url+"message/"+id+"/reply-msg.html";
	
}







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
