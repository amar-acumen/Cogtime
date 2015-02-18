$(document).ready(function() {
		var options = {
        beforeSubmit:  showBusyScreen,  // pre-submit callback
        success:       validateFrm  // post-submit callback
		};
		
		// bind to the form's submit event
		$('#frm_settings').submit(function() {
			// inside event callbacks 'this' is the DOM element so we first
			// wrap it in a jQuery object and then invoke ajaxSubmit
			$(this).ajaxSubmit(options);
	
			// !!! Important !!!
			// always return false to prevent standard browser submit and page navigation
			return false;
		});
		
	});
	
	
// validate ajax-submission...
function validateFrm(data)
{
	//alert(data);
	var result_obj = JSON.parse(data);

	if(result_obj.result=='success') {
		
		hideBusyScreen();
		showUIMsg(result_obj.msg);
		//$('#table_content').html(result_obj.content);
		
		//// redirecting to products listing window...
		//var URL = admin_base_url + "site_settings/settings.html";
			//window.location.href = URL;
	}

	if( result_obj.arr_messages=='' && result_obj.result=='error' )
	{
		hideBusyScreen();
		showUIMsg(result_obj.msg);
	} else {
	
		$('.error_msg').each(function(i){
			$(this).attr('style','display:none');
		});
	
		if(result_obj.result=='error') {
		
			for ( var id in result_obj.arr_messages ){
				
				if( $('#err_'+id) != null ) {
					$('#err_'+id).html(result_obj.arr_messages[id]);
					$('#err_'+id).show();
				}
			}
			
		}
		
	}
	
	// hide busy-screen...
	//hideBusyScreen();
}
