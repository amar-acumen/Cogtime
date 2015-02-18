function confirm_delete_(file_id)
{
	//alert(file_id);
	$('#hd_del_product_id').val( file_id );
	show_dialog('delete-product');
	//return false;
}


function delete_()
{
		var selected_id = parseInt( $('#hd_del_product_id').val() );
		//alert(selected_id);
   	var URL = admin_base_url + 'trade_center/eswap/delete/';
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
				$('#REQUEST_DIV').html(data.request_content);
				//hideBusyScreen(); 
		}
	});
}

function change_status(id,i_status)
{
	var URL = admin_base_url +'trade_center/eswap/change_status/';
	showBusyScreen();
	if(ajax_req!=null) {
		ajax_req.abort();
	}
	ajax_req = $.ajax({
		type: 'POST',
		url:URL,
		dataType: 'json',
		data: ({ 'id' : id
				 ,'status' : i_status
			 }),
		success: function (data, status) {
			
				showUIMsg('Status changed successfully.');
				$('#prod_status_'+id).html(data.action_html);
				hideBusyScreen(); 
		}
	});
}



/*REQUEST METHODS
*/
function confirm_delete_req(file_id)
{
	//alert(file_id);
	$('#hd_del_request_id').val( file_id );
	show_dialog('delete-request');
	//return false;
}


function delete_req()
{
		var selected_id = parseInt( $('#hd_del_request_id').val() );
		//alert(selected_id);
		var URL = admin_base_url + 'trade_center/eswap/delete_request/';
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
						$('#REQUEST_DIV').html(data.request_content);
						//hideBusyScreen(); 
				}
		});
}

function change_req_status(id,i_status)
{
	var URL = admin_base_url +'trade_center/eswap/change_request_status/';
	showBusyScreen();
	if(ajax_req!=null) {
		ajax_req.abort();
	}
	ajax_req = $.ajax({
		type: 'POST',
		url:URL,
		dataType: 'json',
		data: ({ 'id' : id
				 ,'status' : i_status
			 }),
		success: function (data, status) {
			
				showUIMsg('Status changed successfully.');
				$('#req_status_'+id).html(data.action_html);
				hideBusyScreen(); 
		}
	});
}



function show_subcategory(p_id){
	  $.ajax({
		type: 'POST',
		url:admin_base_url +'trade_center/eswap/generate_sub_categroies/',
		dataType: 'json',
		data: ({ 'id' : p_id
			 }),
		success: function (data, status) {
				if(data.s_option != ""){
					$('#subCategory').html(data.s_option);
					$('#subCategorySpan').show();
				}
				else
					$('#subCategorySpan').hide();
				     hideBusyScreen(); 
		}
	});
 }


 
function get_details(pid){
	 showUILoader('<img src="<?= base_url() ?>images/loading_big.gif" width="50"/> ');
	 $.ajax({
		type: 'POST',
		url:admin_base_url +'trade_center/eswap/get_details/',
		dataType: 'json',
		data: ({ 'id' : pid
			 }),
		success: function (data, status) {
					$('#detail_box').html(data.html);
					 hideUILoader('product-details');
		}
	}); 
 	
}
 





$(document).ready(function(arg) {
 $('#frm_search').submit(function(){ 
 	var URL;
 		if($('#frm_typ').val() == 0)
		 URL = admin_base_url+"trade_center/eswap/product_ajax_pagination/";
		else
		 URL = admin_base_url+"trade_center/eswap/request_ajax_pagination/";
		 
		var datatosend=$("#frm_search").serialize();
			showBusyScreen();
			$.ajax({
			   type: "POST",
			   url: URL,
			   data: datatosend,
			   success: function(data){
				  
				  hideBusyScreen(); 
				   if($('#frm_typ').val() == 0)
				     $('#PRODUCT_DIV').html(data);
				   else
				   	 $('#REQUEST_DIV').html(data);
			   }
			 });	 
});
 
 });