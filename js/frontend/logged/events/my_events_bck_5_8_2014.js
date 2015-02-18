$(document).ready(function() {
	
	// for AJAX page-submission...
    var options = { 
        beforeSubmit:  showBusyScreen,  // pre-submit callback 
        success:       validateAddEventFrm // post-submit callback 
    }; 
 
    // bind to the form's submit event 
    $('#frmCreateEvent').submit(function() {
	
        $(this).ajaxSubmit(options);
		
        return false;
		
    });

});


// validate ajax-submission...
function validateAddEventFrm(data)
{
	
   var data = JSON.parse(data);//alert(data.html);
	
	if(data.success==false) 
	{
		
		$('.error-message').each(function(i){
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
		  // clearing form
		  $('.error-message').each(function(i){
			$(this).attr('style','display:none');
		 });
		 
		  $("#contact_list :checkbox").attr('checked', false);
		  $("#h_friend_id").val();
		  $('#frmCreateEvent')[0].reset();	
		  showUIMsg(data.msg);
		  //window.location.href=document.URL;
		  window.location.href= base_url+'my-events.html';
	}
	 
	hideBusyScreen();
				
}

function get_selected_contacts(){

	var str_id="";
	var str_name="";
	var arr_temp="";
	var total_count = $("#contact_list :checkbox").filter(":checked").size();
	var i= 0;
	//alert(total_count);
	$('#contact_list :checkbox:checked').each(function() {
		//alert($(this).attr('name'));
		//alert($(this).val());
		 i= i+1;
		 
		  arr_temp=$(this).val().split('_');
		  str_id+=arr_temp[0]+'##';
		  if(i == total_count){
		  	str_name+=arr_temp[1];	
		  }
		  else
		  {
			  str_name+=arr_temp[1]+', ';	
		  }
		
	});
	
	$('#invite_frnd').val(str_name.replace(/,+$/,' '));
	$('#h_friend_id').val(str_id.replace(/##+$/,''));
  ///  hide_dialog();
  $('.wal-minus').trigger('click');
	
}
