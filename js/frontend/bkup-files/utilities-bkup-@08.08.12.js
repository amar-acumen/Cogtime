//// Javascript document for "COMMON UTILITIES" page [from top header]...

var dialog = null;
var dialog_loading = null;
var ajax_req = null;


//// function to show dialog window [Start]...
var dialog = null;

function show_dialog (id)
{
	if(!dialog) dialog = null;
	dialog = new ModalDialog ("#"+id);
	dialog.show();
}

function hide_dialog ()
{
	if(!dialog) 
		dialog = null;
	else 
		dialog.hide();
}
//// function to show dialog window [End]...


//// function to show/hide busy-screen [Start]
function showBusyScreen()
{
	if(!dialog_loading) dialog_loading = null;
	dialog_loading = new ModalDialog ("#loading_dialog");
	
	dialog_loading.show();
}

function hideBusyScreen()
{
	if(dialog_loading) dialog_loading.hide();
}
//// function to show/hide busy-screen [End]


function restoreRatings()
{
	$('.star').rating({});
	if(dialog_loading) dialog_loading.hide();
}


/* ############## ELEMENT BLOCKING LOADER [BEGIN] ############## */

	// loading screen...
	function showAJAXLoader(divID)
	{
		var HTML = '<img src="'+ base_url +'images/loader_atom.gif" />';
		var element_div = 'div#'+ divID;
		
		$(element_div).block({
			message: HTML,
			css: {
					border: 'none',
					color: '#ffffff'
				 },
			overlayCSS: { backgroundColor: '#ffffff' }
		});
	}
	
	// now, hiding the loading screen...
	function hideAJAXLoader(divID)
	{
		var element_div = 'div#'+ divID;
		$(element_div).unblock();
	}

/* ############## ELEMENT BLOCKING LOADER [END] ############## */




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
var redirect_url ='';
function showUIMsgRedirect(msg,pageurl)
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
	redirect_url = pageurl;
	//setTimeout($.unblockUI, 2000);
	setTimeout('timeout_trigger()', 1000);
	
	
	
}


function timeout_trigger()
 {
	  $.unblockUI();
	  window.location = base_url+redirect_url;
 }
/////////////// BASE64 ENCODE-DECODE [START] ///////////////


/**
*
*  Base64 encode / decode
*  http://www.webtoolkit.info/
*
**/

var Base64 = {

	// private property
	_keyStr : "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",

	// public method for encoding
	encode : function (input) {
		var output = "";
		var chr1, chr2, chr3, enc1, enc2, enc3, enc4;
		var i = 0;

		input = Base64._utf8_encode(input);

		while (i < input.length) {

			chr1 = input.charCodeAt(i++);
			chr2 = input.charCodeAt(i++);
			chr3 = input.charCodeAt(i++);

			enc1 = chr1 >> 2;
			enc2 = ((chr1 & 3) << 4) | (chr2 >> 4);
			enc3 = ((chr2 & 15) << 2) | (chr3 >> 6);
			enc4 = chr3 & 63;

			if (isNaN(chr2)) {
				enc3 = enc4 = 64;
			} else if (isNaN(chr3)) {
				enc4 = 64;
			}

			output = output +
			this._keyStr.charAt(enc1) + this._keyStr.charAt(enc2) +
			this._keyStr.charAt(enc3) + this._keyStr.charAt(enc4);

		}

		return output;
	},

	// public method for decoding
	decode : function (input) {
		var output = "";
		var chr1, chr2, chr3;
		var enc1, enc2, enc3, enc4;
		var i = 0;

		input = input.replace(/[^A-Za-z0-9\+\/\=]/g, "");

		while (i < input.length) {

			enc1 = this._keyStr.indexOf(input.charAt(i++));
			enc2 = this._keyStr.indexOf(input.charAt(i++));
			enc3 = this._keyStr.indexOf(input.charAt(i++));
			enc4 = this._keyStr.indexOf(input.charAt(i++));

			chr1 = (enc1 << 2) | (enc2 >> 4);
			chr2 = ((enc2 & 15) << 4) | (enc3 >> 2);
			chr3 = ((enc3 & 3) << 6) | enc4;

			output = output + String.fromCharCode(chr1);

			if (enc3 != 64) {
				output = output + String.fromCharCode(chr2);
			}
			if (enc4 != 64) {
				output = output + String.fromCharCode(chr3);
			}

		}

		output = Base64._utf8_decode(output);

		return output;

	},

	// private method for UTF-8 encoding
	_utf8_encode : function (string) {
		string = string.replace(/\r\n/g,"\n");
		var utftext = "";

		for (var n = 0; n < string.length; n++) {

			var c = string.charCodeAt(n);

			if (c < 128) {
				utftext += String.fromCharCode(c);
			}
			else if((c > 127) && (c < 2048)) {
				utftext += String.fromCharCode((c >> 6) | 192);
				utftext += String.fromCharCode((c & 63) | 128);
			}
			else {
				utftext += String.fromCharCode((c >> 12) | 224);
				utftext += String.fromCharCode(((c >> 6) & 63) | 128);
				utftext += String.fromCharCode((c & 63) | 128);
			}

		}

		return utftext;
	},

	// private method for UTF-8 decoding
	_utf8_decode : function (utftext) {
		var string = "";
		var i = 0;
		var c = c1 = c2 = 0;

		while ( i < utftext.length ) {

			c = utftext.charCodeAt(i);

			if (c < 128) {
				string += String.fromCharCode(c);
				i++;
			}
			else if((c > 191) && (c < 224)) {
				c2 = utftext.charCodeAt(i+1);
				string += String.fromCharCode(((c & 31) << 6) | (c2 & 63));
				i += 2;
			}
			else {
				c2 = utftext.charCodeAt(i+1);
				c3 = utftext.charCodeAt(i+2);
				string += String.fromCharCode(((c & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));
				i += 3;
			}

		}

		return string;
	}

}

/////////////// BASE64 ENCODE-DECODE [END] ///////////////
var dialog_loading = null;
function showLoading() {
	
	if(!dialog_loading) dialog_loading = null;
	dialog_loading = new ModalDialog ("#loading_dialog");
	dialog_loading.show();
}

function hideLoading() {
	//alert("hide txt");
	if(dialog_loading) dialog_loading.hide();
}


//// to show "ajax message [jquery blockUI]"...
function showBlockUI(msg)
{
	$.blockUI({
		message: msg,
		css: {
				border: 'none',
				width: '250px',
				padding: '15px',
				backgroundColor: '#000000',
				'-webkit-border-radius': '10px',
				'-moz-border-radius': '10px',
				opacity: '1',
				color: '#ffffff'
		},
		overlayCSS: { backgroundColor: '#FFFFFF' }
	});
	
	setTimeout($.unblockUI, 2000);
}


var ajax_req = null;
/// function for login-popup...
/*function popLoginAJAX()
{
	showSmallLoader();
	
	if(ajax_req!=null) {
		ajax_req.abort();
	}
	

	ajax_req = $.ajax({
		type: 'POST',
		url:base_url+'session/authenticate_popup_ajax',
		dataType: 'json',

		data: ({'hash' : window.location.hash
			, 'referrer' : $('#login_referrer').val()
			, 'current_referrer' : $('#current_referrer').val()
			, 'email' : email
			, 'password' : password
			, 'chkRem' : $('#chkRem').attr('checked')}),

		success: function (data, status) {
			
			if(data.success && typeof(data.redirect_url)!='undefined' ) {
				window.location = data.redirect_url;
			}
			else {
				
							
				if(data.arr_messages['email']!='')
				 {
					  $('#error_msg_email_login').html(data.arr_messages['email']);
					  $('#error_msg_email_login').show();
				 }
				 if(data.arr_messages['passwd']!='')
				 {
					  $('#error_msg_password_login').html(data.arr_messages['passwd']);
					  $('#error_msg_password_login').show();
				 }
				hideLoading();
				showBlockUI(data.msg);
				
			}
		},
		error: function (data, status, e) {
			
			hideLoading();
			showBlockUI(data.msg);
			
			
		}
	});

	return false;
}*/


// for AJAX page-submission...
var popup_options = { 
	beforeSubmit:  showSmallLoader,  // pre-submit callback 
	success:       validateLoginFrm // post-submit callback 
}; 

/// function for login-popup...
function popLoginAJAX() {
	
	$('#frmpopUPLogin').ajaxSubmit(popup_options);
	
	return false; 
}

// validate ajax-submission...
function validateLoginFrm(data)
{
	//alert(data);
	var result_obj = JSON.parse(data);

	if(result_obj.result=='success') {
		
		if( result_obj.redirect_url!='' )
			window.location.href = result_obj.redirect_url;
		/*else {
			showUIMsg(result_obj.msg);
			$('#div_login_logout_placeholder').html(result_obj.content);
			hideSmallLoader();
			hide_dialog();
		}*/
		
	}

	if(result_obj.result=='error') {
	
		showUIMsg(result_obj.msg);
		hideSmallLoader();
		
	}
	
	// hide busy-screen...
	hideBusyScreen();
}




function showSmallLoader()
{
	var loading_img = base_url +"images/loading_small.gif";
	var HTML = '<img src="'+ loading_img +'" />';
	
	$('#div_login_loader').empty().html(HTML);
}


function hideSmallLoader()
{
	$('#div_login_loader').empty();
}



	var dialog = null;
	
	function show_dialog (id)
	{
		if(!dialog) dialog = null;
		dialog = new ModalDialog ("#"+id);
		dialog.show();
	}
	
	function hide_dialog ()
	{
		dialog.hide();
		if(!dialog) dialog = null;
	}
	
	
	
function base64_decode (data) {
    var b64 = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=";
    var o1, o2, o3, h1, h2, h3, h4, bits, i = 0, ac = 0, dec = "", tmp_arr = [];

    if (!data) {
        return data;
    }

    data += '';

    do {  // unpack four hexets into three octets using index points in b64
        h1 = b64.indexOf(data.charAt(i++));
        h2 = b64.indexOf(data.charAt(i++));
        h3 = b64.indexOf(data.charAt(i++));
        h4 = b64.indexOf(data.charAt(i++));

        bits = h1<<18 | h2<<12 | h3<<6 | h4;

        o1 = bits>>16 & 0xff;
        o2 = bits>>8 & 0xff;
        o3 = bits & 0xff;

        if (h3 == 64) {
            tmp_arr[ac++] = String.fromCharCode(o1);
        } else if (h4 == 64) {
            tmp_arr[ac++] = String.fromCharCode(o1, o2);
        } else {
            tmp_arr[ac++] = String.fromCharCode(o1, o2, o3);
        }
    } while (i < data.length);

    dec = tmp_arr.join('');
    dec = this.utf8_decode(dec);

    return dec;
}


function utf8_decode ( str_data ) {
    var tmp_arr = [], i = 0, ac = 0, c1 = 0, c2 = 0, c3 = 0;
    
    str_data += '';
    
    while ( i < str_data.length ) {
        c1 = str_data.charCodeAt(i);
        if (c1 < 128) {
            tmp_arr[ac++] = String.fromCharCode(c1);
            i++;
        } else if ((c1 > 191) && (c1 < 224)) {
            c2 = str_data.charCodeAt(i+1);
            tmp_arr[ac++] = String.fromCharCode(((c1 & 31) << 6) | (c2 & 63));
            i += 2;
        } else {
            c2 = str_data.charCodeAt(i+1);
            c3 = str_data.charCodeAt(i+2);
            tmp_arr[ac++] = String.fromCharCode(((c1 & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));
            i += 3;
        }
    }

    return tmp_arr.join('');
}



function redirectWithHash(id)
 {
    var url_parts = document.URL.split('#');
  	var main_url = url_parts[0];
    window.location= main_url+"#"+id;
 }
 
 
function open_login_popup(){
  // showUIMsg('need to login first');
  $.blockUI({
		message: $('#need_to_login'),
		css: {
				'width': '275px',
				'border': 'none',
				'padding': '15px',
				'font-size': '12px',
				'line-height': '17px',
				'font-weight': 'bold',
				'backgroundColor': '#000000',
				'-webkit-border-radius': '10px',
				'-moz-border-radius': '10px',
				'opacity': '1',
				'color': '#ffffff'/*,
				'font-size': '12px'*/
		},
		overlayCSS: { backgroundColor: '#000000' }
	});

	setTimeout($.unblockUI, 10000);
}


//// function related to "Band Equity Graph"...
function generate_band_equity_graph_ajax(artist_id, chart_type) {
	
	var ajax_url = base_url + 'profile_utilities/generate_band_equity_chart_AJAX/'+ artist_id +'/'+ chart_type;
	
	showAJAXLoader('div_band_equity_chart');
	
	$.get(ajax_url, function(data) {
		$('#div_band_equity_chart').empty().html(data);
		//alert('Load was performed.');
	});
	
	hideAJAXLoader('div_band_equity_chart');
}





function show_block_msg(msg, timeout){
  
  timeout = parseInt(timeout);
  $.blockUI({
		message: msg,
		css: {
				'width': '275px',
				'border': 'none',
				'padding': '15px',
				'font-size': '12px',
				'line-height': '17px',
				'font-weight': 'bold',
				'backgroundColor': '#000000',
				'-webkit-border-radius': '10px',
				'-moz-border-radius': '10px',
				'opacity': '1',
				'color': '#ffffff'
		},
		overlayCSS: { backgroundColor: '#000000' }
	});

	setTimeout($.unblockUI, timeout);
}


// function to show/hide notification-tool accordingly...
function show_notification_tool()
{
	var notify_div = '#div_notification_tool';
	if( $(notify_div).is(":visible") )
		$(notify_div).slideUp('slow');
	else
		$(notify_div).slideDown('slow');
}
