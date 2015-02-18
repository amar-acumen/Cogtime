function displayOrderAJAX(recordId, i_album_id, status)
{
	
	var URL = base_url +'logged/organize_photo/maintain_displayorder_ajax/';
	
	// loading part...
	showBusyScreen();
	
	// AJAX action started...
	if(ajax_req!=null) {
		ajax_req.abort();
	}
	ajax_req = $.ajax({
		type: 'POST',
		url: URL,
		data: {'status' : status, 'rid' : recordId , 'i_album_id' : i_album_id },

		success: function (data, status) {

			hideBusyScreen();
			
			$('#album_div').html(data);

		}	// end of success function...
	});
	// AJAX action end...
	
}

function delete_confirm_(file_id)
	{
		//alert(file_id);
		$('#i_del_id').val( file_id );
		show_dialog('vraiment-supprime');
		//return false;
	}
	function delete_()
	{
		var selected_id = parseInt( $('#i_del_id').val() );
		//alert(selected_id);
	if( selected_id!=0 )
	{
		showUIMsg("supprimé avec succès.");
		var delURL = base_url + 'admin/banner/delete_information/'+ selected_id;
		window.location.href = delURL;
	} else {
	
		var msg = " erreur s'est produite ";
		showUIMsg(msg);
		
		hide_dialog();
		
	}
}