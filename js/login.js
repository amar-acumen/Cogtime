var ajax_req = null;

function userLogin(email, password) 
{   
	$('#error_msg_email_login').html('');
	$('#error_msg_password_login').html('');
	
	$('#error_msg_email_login').hide();
	$('#error_msg_password_login').hide();

	showLoading();
   
	if(ajax_req!=null) {
		ajax_req.abort();
	}
	

	ajax_req = $.ajax({
		type: 'POST',
		url:base_url+'session/authenticate',
		dataType: 'json',

		data: ({'hash' : window.location.hash
			, 'referrer' : $('#login_referrer').val()
			, 'current_referrer' : $('#current_referrer').val()
			, 'email' : email
			, 'password' : password
			, 'chkRem' : $('#chkRem').attr('checked')}),

		success: function (data, status) {
			
		//alert(data.redirect_url);
			
			if(data.success && typeof(data.redirect_url)!='undefined' ) {
				window.location = data.redirect_url;
			}
			else {
				hideLoading();
					///alert(data.arr_messages['email']);		
				if(data.arr_messages['email']!='' && data.arr_messages['passwd']!='')
				 {  
					  showUIMsg("Invalid Email Id and Password");
					  //$('#error_msg_email_login').html(data.arr_messages['email']);
					 // $('#error_msg_email_login').show();
				 }
				else if(data.arr_messages['passwd']!='')
				 {
					  showUIMsg(data.arr_messages['passwd']);
					  //$('#error_msg_password_login').html(data.arr_messages['passwd']);
					  //$('#error_msg_password_login').show();
				 }
				 else if(data.arr_messages['email']!=''){
					 showUIMsg(data.arr_messages['email']);
				 }
				else{
				//alert(data.msg);
				//showUIMsg(data.arr_messages['email']);
				showUIMsg(data.msg);
				}
				
			}
		},
		error: function (data, status, e) {
			
			hideLoading();
			showUIMsg(data.msg);
			
			
		}
	});

	return false;
}


// ====== New Functions [Begin] ======





function show_login_popup(current_url)
{	
	//alert('in login..js '+current_url);
	
	$('input#current_referrer').val(current_url);
	 show_dialog('login-box');
}



var ajax_loginreq = null;

function userAjaxLogin(email, password) 
{   
	$('#error_msg_email_login').html('');
	$('#error_msg_password_login').html('');
	
	$('#error_msg_email_login').hide();
	$('#error_msg_password_login').hide();

	showUILoader_nodialog();
   
	if(ajax_loginreq!=null) {
		ajax_loginreq.abort();
	}
	

	ajax_loginreq = $.ajax({
		type: 'POST',
		url:base_url+'session/ajax_authenticate',
		dataType: 'json',

		data: ({'hash' : window.location.hash
			, 'referrer' : $('#login_referrer').val()
			, 'current_referrer' : $('#current_referrer').val()
			, 'email' : email
			, 'password' : password
			, 'chkRem' : $('#chkRem').attr('checked')}),

		success: function (data, status) {
			
		//alert(data.redirect_url);
			
			if(data.success && typeof(data.redirect_url)!='undefined' ) {
				window.location = data.redirect_url;
			}
			else {
				hideUILoader_nodialog();
				hide_dialog();
					///alert(data.arr_messages['email']);		
				if(data.arr_messages['email']!='' && data.arr_messages['passwd']!='')
				 {  
					  showUIMsg("Invalid Email Id and Password");
					  //$('#error_msg_email_login').html(data.arr_messages['email']);
					  //$('#error_msg_email_login').show();
				 }
				else if(data.arr_messages['passwd']!='')
				 {
					  showUIMsg(data.arr_messages['passwd']);
					 // $('#error_msg_password_login').html(data.arr_messages['passwd']);
					  //$('#error_msg_password_login').show();
				 }
				 else if(data.arr_messages['email']!=''){
					 showUIMsg(data.arr_messages['email']);
				 }
				else{
				//alert(data.msg);
				//showUIMsg(data.arr_messages['email']);
				showUIMsg(data.msg);
				}
				
			}
		},
		error: function (data, status, e) {
			
			hideUILoader_nodialog();
			showUIMsg(data.msg);
			
			
		}
	});

	return false;
}


