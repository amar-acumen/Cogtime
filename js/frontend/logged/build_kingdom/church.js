$(document).ready(function() {

	// for AJAX page-submission...
    var options = { 
        beforeSubmit:  show_loader_screen,  // pre-submit callback 
        success:       validateAddEventFrm // post-submit callback 
    }; 
    // bind to the form's submit event 
    $('#register_church').submit(function() {
        $(this).ajaxSubmit(options);
        return false;
    });

});

function show_loader_screen(){
	showUILoader_nodialog('<img src="'+base_url+'images/loading_big.gif" width="50"/> ');
}

// validate ajax-submission...
function validateAddEventFrm(data)
{
	
   var data = JSON.parse(data);//alert(data.html);
   $('.church-form-error-message').each(function(i){
			$(this).attr('style','display:none');
	});
	
	if(data.success==false) 
	{
		for ( var id in data.arr_messages ){
			//alert(data.arr_messages[id]);
			if( $('#err_'+id) != null ) {
				$('#err_'+id).html(data.arr_messages[id]);
				$('#err_'+id).css('display', 'block');
			}
		}
	}
	else {
		  // clearing form
		  $('#register_church')[0].reset();	
		  showUIMsg(data.msg);
	}
	 
	hideUILoader_nodialog();
}