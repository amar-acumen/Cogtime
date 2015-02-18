function confirm_delete_(file_id)
{
	//alert(file_id);
	$('#hd_del_id').val( file_id );
	show_dialog('delete-page-popup');
	//return false;
}


function delete_()
{
	var selected_id = parseInt( $('#hd_del_id').val() );
		//alert(selected_id);
   	var URL = admin_base_url + 'trade_center/credit_history/delete/';
   $.ajax({
		type: 'POST',
		url:URL,
		dataType: 'json',
		data: ({ 'id' : selected_id
			 }),
		success: function (data, status) {
				hide_dialog();
				showUIMsg('Deleted successfully.');
				$('#PRODUCT_DIV').html(data.product_content);
				//hideBusyScreen(); 
		}
	});
}

