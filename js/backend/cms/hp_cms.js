$(document).ready(function(arg) {
	
	// for AJAX page-submission...
    optionsArr = { 
        beforeSubmit:  showBusyScreen,  // pre-submit callback 
        success:       validateFrm, // post-submit callback 
		url:		admin_base_url + "site_settings/hp_cms/edit_hp_cms"
    }; 
 
	
	
})

function post_frm_ajax()
{

	$('#frm_hp_cms').ajaxSubmit(optionsArr);
	
	return false;
}

// validate ajax-submission...
function validateFrm(data)
{
	
	var data = JSON.parse(data);
 //alert(data.success);
	if(data.success==false) 
	{
		

		$('.error_msg').each(function(i){
			$(this).attr('style','display:none');
		});
		
		
		for ( var id in data.arr_messages ){
			//alert(data.arr_messages[id]);
			if( $('#err_'+id) != null ) {
				$('#err_'+id).html(data.arr_messages[id]);
				$('#err_'+id).css('display', 'block');
			}
		}

		
	}
	else {
		showUIMsg(data.msg);		
		window.location.href = admin_base_url + "site_settings/hp-cms.html";
		
		
	}
	// hide busy-screen...
	hideBusyScreen();
}


