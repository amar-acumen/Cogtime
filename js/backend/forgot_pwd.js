/* ==== show blockUI message ==== */
function showUIMsg(msg)
{ 
	$.blockUI({
		message: msg,
		css: {
				border: 'none',
				padding: '15px',
				fontSize: '12px',
				backgroundColor: '#000000',
				'-webkit-border-radius': '10px',
				'-moz-border-radius': '10px',
				opacity: '1',
				color: '#ffffff'
		},
		overlayCSS: { backgroundColor: '#000000' }
	});

	setTimeout($.unblockUI, 2000);
}


function forgotPasswordAJAX()
{
	
	var HTML = '<img src="'+base_url+'images/loading_small.gif" />';
	
	$('#loader_forgot_passwd').html(HTML);
	
	if(ajax_req!=null) {
		ajax_req.abort();
		
	}
	ajax_req = $.ajax({
		type: 'POST',
		url: admin_base_url +'index/forgot_password_ajax',
		data: ({txt_forgot_email : $('#txt_forgot_email').val()}),

		success: function (data, status) {
			
			$('#loader_forgot_passwd').html('');
		
			var data = JSON.parse(data);
			
			if( data.result=='failure' )
			{
				$('#error_msg_forgot_passw').show().html(data.msg);
				
				
				if( $('#txt_forgot_email').val()!='' )
					$('#txt_forgot_email').select();
				else
					$('#txt_forgot_email').focus();
			}
			else
			{
				hide_dialog('send_email');
				//showUIMsg(data.msg);
				show_dialog('sent-to-mail');
			}

		}	// end of success function...
	});
	
	

}
