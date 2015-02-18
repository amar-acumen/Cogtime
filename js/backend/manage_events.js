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
		  showUIMsg("Deleted successfully.");
		  var delURL = admin_base_url + 'social_hub/events/delete_information/'+ selected_id;
		  window.location.href = delURL;
	  } else {
	  
		  var msg = "Sorry an error has occured, Please try again";
		  showUIMsg(msg);
		  
		  hide_dialog();
		  
	  }
}