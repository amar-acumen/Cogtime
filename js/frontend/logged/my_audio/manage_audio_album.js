function delete_confirm_(file_id)
{
	$('#i_del_id').val( file_id );
	show_dialog('delete-photo-album');
}
function delete_()
{
	var selected_id = parseInt( $('#i_del_id').val() );
	//alert(selected_id);
	if( selected_id!=0 )
	{
		showUIMsg("Audio album deleted successfully.");
		var delURL = base_url + 'logged/manage_my_audio/delete/'+ selected_id;
		window.location.href = delURL;
	} else {
	
		var msg = "Error.";
		showUIMsg(msg);
		hide_dialog();
		
	}
}
