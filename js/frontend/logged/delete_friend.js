function delete_confirm_(file_id)
  {
	  //alert(file_id);
	  $('#i_del_id').val( file_id );
	  
	  show_dialog('delete-page-popup');
	  //return false;
  }



function delete_friend_()
	{
		hide_dialog();
		var id = parseInt( $('#i_del_id').val() );
		var URL = base_url +'logged/my_friends/delete_friend';
		showBusyScreen();
		if(ajax_req!=null) {
			ajax_req.abort();
		}
		ajax_req = $.ajax({
			type: 'POST',
			url:URL,
			dataType: 'json',
			data: ({ 'frnd_id' : id
				 }),
			success: function (data) {
							var result_obj = data;
							hideBusyScreen();
							//alert(result_obj.last_record);
							if(result_obj.success== true ) {
								showUIMsg(result_obj.msg);
								//$('#cancel_friend_'+result_obj.u_id).html(result_obj.html_txt);
								if(result_obj.last_record == 'Y'){
									$('#friend_record_'+result_obj.u_id).html(result_obj.html_txt);
									//$('#friend_record_'+result_obj.u_id).attr('style','padding-top:5px;display:block;');
								}else{
									$('#friend_record_'+result_obj.u_id).attr('style','padding-top:5px;display:none;');
								}
								
							}
							else if(result_obj.success==false){
							    showUIMsg(result_obj.msg);
							}
					},
			error: function (data, status, e) {
								hideBusyScreen();
								showUIMsg(data.msg);
			}
		});
	}