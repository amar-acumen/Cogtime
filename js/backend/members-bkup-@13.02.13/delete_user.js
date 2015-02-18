function delete_confirm_(file_id, i_user_type)
	{
		//alert(admin_base_url + 'members/delete_member/'+ file_id +'/'+ i_user_type);
		$('#i_del_id').val( file_id );
		$('#i_user_typ').val( i_user_type );
		show_dialog('delete-page-popup');
		//return false;
	}
	function delete_()
	{
		var selected_id = parseInt( $('#i_del_id').val() );
		var user_type = parseInt( $('#i_user_typ').val() );
		//alert(selected_id);
		if( selected_id!=0 )
		{
		
			showUIMsg("supprimé avec succès.");
			var delURL = admin_base_url + 'members/members/delete_member/'+ selected_id +'/'+ user_type ;
			window.location.href = delURL;
		} else {
		
			var msg = " erreur s'est produite ";
			showUIMsg(msg);
			
			hide_dialog();
			
		}
	}
	
	function status_change(id)
	{
	
	var status=$('#status'+id).val();
//alert(status);
	showBusyScreen();
	if(status=="Disable")
	{
		var now_status="2"; // 1: active , 2: inactive
		var now_status_name='Enable';
	}
	else
	{
		var now_status="1"; 
		var now_status_name='Disable';
	}
	//alert(now_status_name);
	$.ajax({
			type: "get",
			url: admin_base_url+"members/members/change_status/"+now_status+'/'+id,
			dataType:"json",
			success: function(json_response){
				
						if(json_response.result=='success')
						{
							$('#status'+id).val(now_status_name);	
							hideBusyScreen();
						}
						else
						{
							showUIMsg('Sorry. Can not be modified.');
						}
				}
		   
		   });
	}	// end of function status_change
	
	
	function change_is_minister(id)
	{
	
	var status=$('#is_minister'+id).val();
	//alert(status);
	showBusyScreen();
	if(status=="SET AS MINISTER")
	{
		var now_status="1"; // 1: active , 2: inactive
		var now_status_name='UNSET MINISTER';
	}
	else
	{
		var now_status="0"; 
		var now_status_name='SET AS MINISTER';
	}
	//alert(now_status_name);
	$.ajax({
			type: "get",
			url: admin_base_url+"members/members/change_status_is_minister/"+now_status+'/'+id,
			dataType:"json",
			success: function(json_response){
				
						if(json_response.result=='success')
						{
							$('#is_minister'+id).val(now_status_name);	
							hideBusyScreen();
						}
						else
						{
							showUIMsg('Sorry. Can not be modified.');
						}
				}
		   
		   });
	}	// end of function change_is_minister