
function changeStatus(id,i_status ,cur_status)
{ 
	var URL = admin_base_url +'social_hub/chat_rooms/change_status/';
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




function edit_chatRoom(id)
	{
		$('#i_edit_id').val(id );
		 showUILoader('<img src="'+base_url+'images/loading_big.gif" width="50"/> ');
	  
			 $.ajax({
				 type: "get",
				 url: admin_base_url+'social_hub/chat_rooms/edit_info/'+id,
				 dataType:"json",
				 success: function(json_response){
					 
						  $('#txt_edit_title').val('');
						  $('#txt_edit_desc').val('');
						  $('#txt_edit_max_user').val('');
						  
						  $('#txt_edit_title').val(json_response.room_arr.name);
						  $('#txt_edit_desc').val(json_response.room_arr.des);
						  $('#txt_edit_max_user').val(json_response.room_arr.max_user);
						  $('#chat_cat_div').html(json_response.cat_html);
						   
                          hideUILoader('edit-chat-room');
				  },
				  error: function(){
					 hideUILoader('edit-chat-room');
					  showUIMsg('Some error occurred. Please try again.');
				  }
			  });	 
		//show_dialog('edit-scrolling-headlines');
		//return false;
	}



  function update()
    {
        
        var id = $('#i_edit_id').val();
        
        
		showUILoader('<img src="'+base_url+'images/loading_big.gif" width="50"/> ');
      
             $.ajax({
                 type: "post",
                 url: admin_base_url+'social_hub/chat_rooms/edit_info',
                 data : ({'txt_edit_title':$('#txt_edit_title').val(), 
				 	      'txt_edit_desc':$('#txt_edit_desc').val(), 
						  'txt_edit_max_user' : $('#txt_edit_max_user').val(),
						  'edit_sel_cat': $('#edit_sel_cat').val(),
						  'i_edit_id':id}),
						  
                 dataType:"json",
                 success: function(json_response){
                     
                    
        			 $('.error-message').attr('style','display:none');
                     
                      if(json_response.result == 'success') {
                       
                             hideUILoader('edit-chat-room');
                             hide_dialog();
							 showUIMsg(json_response.msg);
                             $('#table_content').html(json_response.html);
                      }
                      else {
						   
                            for ( var id in json_response.arr_messages ){
                                //alert(data.arr_messages[id]);
                                if( $('#err_'+id) != null ) {
                                    $('#err_'+id).html(json_response.arr_messages[id]);
									
									if(id == 'edit_desc'){
										$('#err_'+id).attr('style','display:block; margin-top:1px;');
									}
									else{
										$('#err_'+id).css('display', 'block');
									}
                                    
                                }
                            }
                          
                          hideUILoader('edit-chat-room');
                         //showUIMsg('Some error occurred. Please try again.');
                          
                      }
					  
                  },
                  error: function(){
                     hideUILoader('edit-chat-room');
                      showUIMsg('Some error occurred. Please try again.');
                  }
              });     
        //show_dialog('edit-scrolling-headlines');
        //return false;
    }

$(document).ready(function(arg) {
 $('#frm_search').submit(function(){ 
						
		var datatosend=$("#frm_search").serialize();
		
			showBusyScreen();
			//alert(datatosend);
			
			$.ajax({
			   type: "POST",
			   url: admin_base_url+"social_hub/chat_rooms/ajax_pagination/",
			   data: datatosend,
			   success: function(data){
				  
				  hideBusyScreen(); 
				   $('#table_content').html(data);
				 
			   }
			 });	 
			
 });

 });
 
 function clear_all_error_msg()
 {
    $('.error-message').attr('style','display:none');
 }
 
 
 
 /// edit ring post ///
 
function limitText(limitField, limitCount, limitNum) {
	if (limitField.value.length > limitNum) {
		limitField.value = limitField.value.substring(0, limitNum);
	} 
}
 
function delete_chatRoom(file_id)
{
	//alert(file_id);
	$('#i_del_id').val( file_id );
	show_dialog('delete-page-popup');
	//return false;
}

function delete_()
{

	var selected_id = parseInt( $('#i_del_id').val() );

  if( selected_id!=0 )
  {
	 $.ajax({
				 type: "post",
				 url:base_url + 'admin/social_hub/chat_rooms/delete_information',
				 dataType:"json",
				data: ({'id':selected_id}),
				 success: function(json_response){
					 hide_dialog();
					  if(json_response.result = 'success') {
						
						$('#table_content').html(json_response.html);
						showUIMsg("Chat Room has been deleted successfully.");
							
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



$(document).ready(function(arg) {
	
	// for AJAX page-submission... add
    optionsArr = { 
        beforeSubmit:  showBusyScreen,  // pre-submit callback 
        success:       validateFrm, // post-submit callback 
		url:		admin_base_url + "social_hub/chat_rooms/add_info"
    }; 
 
  
})

function post_frm_ajax()
{
	$('#frm_add').ajaxSubmit(optionsArr);
	
	return false;
}

// validate ajax-submission...
function validateFrm(data)
{
	
	var data = JSON.parse(data);

	if(data.success==false) 
	{
		
		//hideBusyScreen();
		$('.error-message').each(function(i){
			$(this).attr('style','display:none');
		});
		
		
		for ( var id in data.arr_messages ){
			//alert('#err_'+id);
			if( $('#err_'+id) != null ) {
				$('#err_'+id).html(data.arr_messages[id]);
				//$('#err_'+id).css('display', 'block');
				if(id == 'desc'){
					$('#err_'+id).attr('style','display:block; margin-top:1px;');
				}
				else{
					$('#err_'+id).css('display', 'block');
				}
			}
		}

		
	}
	else {
		$('#frm_add')[0].reset();
		hide_dialog('add-chat-room');
		showUIMsg(data.msg);		
		$('#table_content').html(base64_decode(data.html));
	}
	// hide busy-screen...
	//hideBusyScreen();
}

