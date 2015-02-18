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
		var delURL = admin_base_url + 'social_hub/tweets/delete_information/'+ selected_id;
		window.location.href = delURL;
	} else {
	
		var msg = "Sorry an error has occured, Please try again";
		showUIMsg(msg);
		
		hide_dialog();
		
	}
}

function changeStatus(id,i_status ,cur_status)
{
	var URL = admin_base_url +'social_hub/tweets/change_status/';
	showBusyScreen();
	if(ajax_req!=null) {
		ajax_req.abort();
	}
	ajax_req = $.ajax({
		type: 'POST',
		url:URL,
		dataType: 'json',
		data: ({ 'record_id' : id
				 ,'i_status' : i_status
				 ,'cur_status' : cur_status
			 }),
		success: function (data, status) {
						var result_obj = data;
						hideBusyScreen();
						if(result_obj.result=='success' && result_obj.redirect == false ) {
							showUIMsg(result_obj.msg);
							$('#'+result_obj.u_id+'_status').html(result_obj.action_txt);
						}
						else if(result_obj.result==false && result_obj.redirect == true){
							showUIMsg(result_obj.msg);
						}
				},
		error: function (data, status, e) {
							hideBusyScreen();
							showUIMsg(data.msg);
		}
	});
}


$(document).ready(function(arg) {
 $('#frm_search').submit(function(){ 
						
						var datatosend=$("#frm_search").serialize();
						
					     	showBusyScreen();
							//alert(datatosend);
							
							$.ajax({
							   type: "POST",
							   url: admin_base_url+"social_hub/tweets/ajax_pagination/",
							   data: datatosend,
							   success: function(data){
								  
								  hideBusyScreen(); 
								   $('#table_content').html(data);
							       
								
								 
							   }
							 });	 
					   
						
				});
 
 });