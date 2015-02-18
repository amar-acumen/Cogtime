//// Javascript document for "ADMIN COMMON UTILITIES" page [from top header]...

var dialog = null;
var dialog_loading = null;
var ajax_req = null;

/// global;
NO_ZINDEX = false;
SHOW_ALL_DIALOG = false;


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
	dialog.hide();
	if(!dialog) dialog = null;
}

////// NEW FUNCTION(S) - BEGIN
	function show_delete_dialog (id)
	{
		if(!dialog) dialog = null;
		dialog = new ModalDialog ("."+id);
		
		dialog.show();
	}
	
	function hide_delete_dialog ()
	{
		if( SHOW_ALL_DIALOG )
			dialog.hideOnly();
		else
			dialog.hide();
		if(!dialog) dialog = null;
		
		NO_ZINDEX = false;
	}


//////////////////////////// popup over popup /////////////////////////
var dialog_over_popup = null;


function show_dialog_over_popup (id)
{
    if(!dialog_over_popup) dialog_over_popup = null;
	
    dialog_over_popup = new ModalDialog ("."+id);
    dialog_over_popup.show();
	
	NO_ZINDEX = true;
	SHOW_ALL_DIALOG = true;
}

function hide_dialog_over_popup ()
{
	dialog_over_popup.hide();
	if(!dialog_over_popup) dialog_over_popup = null;
	
	NO_ZINDEX = false;
	SHOW_ALL_DIALOG = false;
}

//////////////////////////// end popup over popup /////////////////////////
////// NEW FUNCTION(S) - END

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




// function to show div...
function showDiv(id) {

	divID = '#jason'+ id;
	linkID = '#'+ id;
	
	if(id == 1) {
		if($(divID).css('display') == 'block') {
			closeDiv(id);
			return;
		}
	}else if(id == 2 ) {
		$(linkID).hide('slow');
	}
	
	$(divID).slideDown("slow");
	
	if(g_LAST_OPEN_DIV > 0) closeDiv(g_LAST_OPEN_DIV);
	g_LAST_OPEN_DIV = id;
}

// function to hide div...
function closeDiv(id)
{
	divID = '#jason'+ id;
	linkID = '#'+ id;
	
	$(divID).slideUp("slow");
	$(linkID).show('slow');
	
	g_LAST_OPEN_DIV = -1;
}
//// ========== ANIMATED COLLAPSE [END] ========== ////



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

/* ############## ELEMENT BLOCKING LOADER [BEGIN] ############## */

var dialog_loading = null;
function showLoading() {
	
	if(!dialog_loading) dialog_loading = null;
	dialog_loading = new ModalDialog ("#loading_dialog");
	dialog_loading.show();
	
	//$("#loading_dialog").animate({},1500)
}

function hideLoading() {
	//alert("hide txt");
	if(dialog_loading) dialog_loading.hide();
}

function hideSlowLoading() {
	//alert("hide txt");
	setTimeout(function(){hideLoading()},3000);
}

/* ############## ELEMENT BLOCKING LOADER [END] ############## */




/* ############## ELEMENT BLOCKING LOADER [BEGIN] ############## */

	// loading screen...
	function showAJAXLoader(divID)
	{
		var HTML = '<img src="'+ base_url +'images/loading.gif" />';
		var element_div = 'div#'+ divID;
		//alert(divID)
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


function showUILoader(msg)
{
	$.blockUI({
		message: msg,
		css: {
				border: 'none',
				padding: '15px',
				fontSize: '12px',
				background: 'none',
				backgroundColor: '#000',
				opacity: '1',
				color: '#ffffff'
		},
		overlayCSS: { backgroundColor: '#000'
		 }
	});

	//setTimeout($.unblockUI, 2000);
}


function hideUILoader(dialog_name)
{
	setTimeout($.unblockUI, 100);
	show_dialog(dialog_name);
}

// new added 

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




function showUILoader_nodialog(msg)
{
	$.blockUI({
		message: msg,
		css: {
				border: 'none',
				padding: '15px',
				fontSize: '12px',
				background: 'none',
				backgroundColor: '#000',
				opacity: '1',
				color: '#ffffff'
		},
		overlayCSS: { backgroundColor: '#000'
		 }
	});

	//setTimeout($.unblockUI, 2000);
}


function hideUILoader_nodialog()
{
	setTimeout($.unblockUI, 100);
	//show_dialog(dialog_name);
}


