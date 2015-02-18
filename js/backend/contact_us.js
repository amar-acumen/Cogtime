// JS functions for backend-section "Create New Webzine-Article"...


/////////////// AJAX CREATE NEW "MAGAZINE-ARTICLE" [BEGIN] //////////////

//// ~~~~ Settings for drop-down select
$(document).ready(function(arg) {
	// tinyMCE init call
	//tinyMCE_call();
	
	
	// for AJAX page-submission...
    optionsArr = { 
        beforeSubmit:  showBusyScreen,  // pre-submit callback 
        success:       validateFrm, // post-submit callback 
		url:		base_url + "contact_us/frm_contact_ajax_submit"
    }; 
 
	
	
})

function post_frm_ajax()
{
	//tinyMCE.triggerSave();
	
	$('#frm_contact').ajaxSubmit(optionsArr);
	
	return false;
}


/*function Beforesubmit(){
	showBusyScreen();
	$('#txta_contents_en').val==tinyMCE.get('txta_contents_en').getContent();
	$('#txta_contents_fr').val==tinyMCE.get('txta_contents_fr').getContent();
	//var content = tinyMCE.get('msg').getContent(); 
}*/

// validate ajax-submission...
function validateFrm(data)
{
	 //alert(data);
	var data = JSON.parse(data);

	if(data.result=='success') {
		
		showUIMsg(data.msg);
		
		//window.location.href = result_obj.redirect;
		window.location.href = base_url + "index";
	}

	$('.error_msg').each(function(i){
		$(this).attr('style','display:none');
	});

	if(data.result=='error') {
	//console.log(data);
		for ( var id in data.arr_messages ){
				//alert(data.arr_messages[id]);
				if( $('#err_'+id) != null ) {
					$('#err_'+id).html(data.arr_messages[id]);
					$('#err_'+id).css('display', 'block');
				}
			}
		
	}
	
	// hide busy-screen...
	hideBusyScreen();
}

	
/////////////// AJAX CREATE NEW "MAGAZINE-ARTICLE" [END] //////////////

