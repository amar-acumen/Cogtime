$(document).ready(function(arg) {
	
	// for AJAX page-submission... add
    optionsArr = { 
        beforeSubmit:  showBusyScreen,  // pre-submit callback 
        success:       validateFrm, // post-submit callback 
		url:		admin_base_url + "site_settings/abuse_dictionary/add_abuse"
    }; 
 
 
 	// for AJAX page-submission... edit
    optionsArr_edit = { 
        beforeSubmit:  showBusyScreen,  // pre-submit callback 
        success:       validateEditFrm, // post-submit callback 
		url:		admin_base_url + "site_settings/abuse_dictionary/edit_abuse"
    }; 
 
 
 
})

function post_frm_ajax()
{
	$('#frm_abuse_wrd').ajaxSubmit(optionsArr);
	
	return false;
}

// validate ajax-submission...
function validateFrm(data)
{
	
	var data = JSON.parse(data);

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
		showUIMsg(data.msg);		
		window.location.href = admin_base_url + "site_settings/abuse-dictionary.html";
		
		
	}
	// hide busy-screen...
	//hideBusyScreen();
}




function post_frm_edit_ajax()
{
	$('#frm_edit_abuse_wrd').ajaxSubmit(optionsArr_edit);
	
	return false;
}

// validate ajax-submission...
function validateEditFrm(data)
{
	
	var data = JSON.parse(data);

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
		showUIMsg(data.msg);		
		window.location.href = admin_base_url + "site_settings/abuse-dictionary.html";
		
		
	}
	// hide busy-screen...
	//hideBusyScreen();
}

 





function delete_confirm_(file_id)
	{
		//alert(file_id);
		$('#i_del_id').val( file_id );
		//showUILoader('<img src="'+base_url+'images/loading_big.gif" width="50"/> ');
	    //setTimeout(function(){hideUILoader('delete-page-popup')},1000);
		show_dialog('delete-page-popup');
		//return false;
	}

function delete_()
  {
	  var selected_id = parseInt( $('#i_del_id').val() );
	  //alert(selected_id);
  if( selected_id!=0 )
  {
	  showUIMsg("The selected abuse word has been deleted successfully.");
	  var delURL = admin_base_url + 'site_settings/abuse_dictionary/delete_information/'+ selected_id;
	  window.location.href = delURL;
  } else {
  
	  var msg = "Sorry an error has occured, Please try again";
	  showUIMsg(msg);
	  
	  hide_dialog();
	  
  }
}