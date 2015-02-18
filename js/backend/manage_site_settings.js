function displayOrderAJAX(recordId, status)
{
	var selected_language = $('#sel_lang').val();
	var URL = admin_base_url +'site_settings/hp_banners/maintain_displayorder_ajax/'+ selected_language;
	
	// loading part...
	showBusyScreen();
	
	// AJAX action started...
	if(ajax_req!=null) {
		ajax_req.abort();
	}
	ajax_req = $.ajax({
		type: 'POST',
		url: URL,
		data: {'status' : status, 'rid' : recordId},

		success: function (data, status) {

			hideBusyScreen();
			
			$('#table_content').html(data);

		}	// end of success function...
	});
	// AJAX action end...
	
}


function delete_confirm_(file_id)
	{
		//alert(file_id);
		$('#i_del_id').val( file_id );
		show_dialog('delete-page-popup');
		//return false;
	}
	function delete_()
	{
		var selected_id = parseInt( $('#i_del_id').val() );
		//alert(selected_id);
	if( selected_id!=0 )
	{
		showUIMsg("The selected banner has been deleted successfully.");
		var delURL = admin_base_url + 'site_settings/hp_banners/delete_information/'+ selected_id;
		window.location.href = delURL;
	} else {
	
		var msg = "Sorry an error has occured, Please try again";
		showUIMsg(msg);
		
		hide_dialog();
		
	}
}