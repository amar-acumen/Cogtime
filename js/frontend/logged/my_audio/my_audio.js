function delete_checked_messages() {
	var j = 0;
	
	$("input:checkbox").each(function(){
		var $this = $(this);
	
		if($this.is(":checked")){
			//alert($this.attr("id"));
			if($(this).attr('id').substr(0, 9) == 'chk_photo') {
				j++;
			}
	  	}
	});
	if(j!=0) {
		show_dialog('delete-msg-popup');
	}
}


function delete_messages() 
{
  	hide_dialog();
 	showBusyScreen();

	var csv_mail_ids = '';
	$(':checkbox:checked').each(function(i){
		//alert($(this).attr('id'));
		if($(this).attr('id').substr(0, 9) == 'chk_photo') {
			csv_mail_ids += $(this).val()+',';
		}
	});
	csv_mail_ids = csv_mail_ids.substring(0, csv_mail_ids.length-1);

	//alert(csv_mail_ids);

	if(ajax_req!=null) {
		ajax_req.abort();
	}

	ajax_req = $.ajax({
		type: 'POST',
		url:base_url+'logged/my_photos/delete_sel_photos',
		dataType: 'json',
		data: ({'csv_mail_ids': csv_mail_ids ,'current_page' : $('#current_page').val()}),
		success: function (data, status) {
			 hideBusyScreen();
			 showUIMsg(data.msg);
			$('#result_div').html(data.content);
			
			
		},
		error: function (data, status, e) {
			hideBusyScreen();
			//closeDiv_WriteMsg('writeMsg');
		}
	});
}