 function changeStatus_(id,i_status ,cur_status)
	{
	
		var URL = base_url+'admin/site_settings/denomination/change_status/';
		showBusyScreen();
		if(ajax_req!=null) {
			//ajax_req.abort();
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
			
							if(result_obj.result==true ) {
								showUIMsg(result_obj.msg);
								$('#'+id+'_status').html('<input id="input_'+id+'" class="btn-01" type="button" value="'+result_obj.now_status+'" onclick="changeStatus_('+id+','+cur_status+','+i_status+')" title="Show">');
								
							}
							else if(result_obj.result==false ){
							    showUIMsg(result_obj.msg);
							}
					},
			error: function (data, status, e) {
								hideBusyScreen();
								showUIMsg(data.msg);
			}
		});
	}

  
  function clear_box(mode){
	  if(mode == 'add'){
		  $('#frm_add_denomination')[0].reset();
		  $('.error-message').hide();
	  }else{
		  $('#frm_edit_denomination')[0].reset();
		   $('.error-message').hide();
		}
  }
  
  function edit_denomination_(id)
	{
		$('#i_edit_id').val(id );
		 showUILoader('<img src="'+base_url+'images/loading_big.gif" width="50"/> ');
	  
			 $.ajax({
				 type: "post",
				 url: base_url+'admin/site_settings/denomination/edit_info/'+id,
				 dataType:"json",
				 
				 success: function(json_response){
				
					  if(json_response.result = 'success') {
						  
						  $('#txt_edit_denomination').val('');
						 
						  
						  $('#txt_edit_denomination').val(json_response.s_denomination);
						 
						 	hideUILoader('edit-denomination');
						  
					  }
					  else {
						   hideUILoader('edit-denomination');
						  showUIMsg('Some error occurred. Please try again.');
					  }
				  },
				  error: function(){
					 hideUILoader('edit-denomination');
					  showUIMsg('Some error occurred. Please try again.');
				  }
			  });	 
	
	}



//============================= delete data =================================
function delete_confirm_(record_id)
{
 
		
		$('#i_del_id').val( record_id );
		show_dialog('delete-page-popup');
		
	}
	
function delete_()
  {

	  var selected_id = parseInt( $('#i_del_id').val() );

  if( selected_id!=0 )
  {
  	 $.ajax({
				 type: "post",
				 url:base_url + 'admin/site_settings/denomination/delete_information',
				 dataType:"json",
				data: ({'id':selected_id}),
				 success: function(json_response){
					 hide_dialog();
					  if(json_response.result = 'success') {
						$('#row_'+selected_id).hide();
						showUIMsg("Denomination has been deleted successfully.");
						 	
					  }
					  else {
					 
						   
						  showUIMsg('Some error occurred. Please try again.');
					  }
				  },
				  error: function(){
					  showUIMsg('Some error occurred. Please try again.');
				  }
			  });
			  
			  
	}		  
			  
			  
	
  
 
}
//========================== end of delete =======================================


//============================== ajax form submit for edit =================================
$(document).ready(function(arg) {
	
	// for AJAX page-submission... add
    optionsArr = { 
        beforeSubmit:  showBusyScreen,  // pre-submit callback 
        success:       validateFrm, // post-submit callback 
		url:		admin_base_url + "site_settings/denomination/add_info"
    }; 
 
 
 	// for AJAX page-submission... edit
    optionsArr_edit = { 
        beforeSubmit:  showBusyScreen,  // pre-submit callback 
        success:       validateEditFrm, // post-submit callback 
		url:		admin_base_url + "site_settings/denomination/edit_info"
    }; 
 
 
 
})

function post_frm_ajax()
{
	
	$('#frm_add_denomination').ajaxSubmit(optionsArr);
	
	return false;
}

// validate ajax-submission...
function validateFrm(data)
{
	
	var data = JSON.parse(data);

	if(data.result=='failure') 
	{
		
	
		$('.error-message').each(function(i){
			$(this).attr('style','display:none');
		});
		
		
		for ( var id in data.arr_messages ){
			
			if( $('#err_'+id) != null ) {
				$('#err_'+id).html(data.arr_messages[id]);
				$('#err_'+id).css('display', 'block');
			}
		}

		
	}
	else {
		showUIMsg(data.msg);	
		hide_dialog();	
		$('#table_content').html(base64_decode(data.response));
		clear_box('add');
	
		
		
	}

}




function post_frm_edit_ajax()
{
	$('#frm_edit_denomination').ajaxSubmit(optionsArr_edit);
	
	return false;
}

// validate ajax-submission...
function validateEditFrm(data)
{
	
	var data = JSON.parse(data);

	if(data.result=='failure') 
	{
		

		$('.error-message').each(function(i){
			$(this).attr('style','display:none');
		});
		
		
		for ( var id in data.arr_messages ){
		
		
			if( $('#err_'+id) != null ) {
				$('#err_'+id).html(data.arr_messages[id]);
				$('#err_'+id).css('display', 'block');
			}
		}

		
	}
	else {
		showUIMsg(data.msg);		
		hide_dialog();
		
		$('#td_name_'+data.id).html(data.updated_d_name);
	
		
		
	}

}

 