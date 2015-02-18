
function changeStatus(id,i_status ,cur_status)
{
	var URL = admin_base_url +'social_hub/rings/change_status/';
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




function edit_ring(id)
	{
		$('#i_edit_id').val(id );
		 showUILoader('<img src="'+base_url+'images/loading_big.gif" width="50"/> ');
	  
			 $.ajax({
				 type: "get",
				 url: admin_base_url+'social_hub/rings/edit_info/'+id,
				 dataType:"json",
				 success: function(json_response){
					 
						  $('#txt_max_member').val('');
						  $('#txt_max_member').val(json_response.i_member);
                          hideUILoader('edit-max-members');
				  },
				  error: function(){
					 hideUILoader('edit-max-members');
					  showUIMsg('Some error occurred. Please try again.');
				  }
			  });	 
		//show_dialog('edit-scrolling-headlines');
		//return false;
	}



  function update()
    {
        
        var id = $('#i_edit_id').val();
        var txt_max_member = $('#txt_max_member').val();
        
		showUILoader('<img src="'+base_url+'images/loading_big.gif" width="50"/> ');
      
             $.ajax({
                 type: "post",
                 url: admin_base_url+'social_hub/rings/edit_info',
                 data : ({'txt_max_member':txt_max_member,'i_edit_id':id}),
                 dataType:"json",
                 success: function(json_response){
                     
                    
        			 $('.error-message').attr('style','display:none');
                     
                      if(json_response.result == 'success') {
                       
                             hideUILoader('edit-max-members');
                             hide_dialog();
							 showUIMsg(json_response.msg);
                             $('#td_member_'+json_response.id).html(json_response.updated_max_members);
                      }
                      else {
						   
                            for ( var id in json_response.arr_messages ){
                                //alert(data.arr_messages[id]);
                                if( $('#err_'+id) != null ) {
                                    $('#err_'+id).html(json_response.arr_messages[id]);
                                    $('#err_'+id).css('display', 'block');
                                }
                            }
                          
                          hideUILoader('edit-max-members');
                         //showUIMsg('Some error occurred. Please try again.');
                          
                      }
					  
                  },
                  error: function(){
                     hideUILoader('edit-max-members');
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
			   url: admin_base_url+"social_hub/rings/ajax_pagination/",
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
 
 function edit_ring_post(id)
	{
		$('#i_post_edit_id').val(id );
		 showUILoader('<img src="'+base_url+'images/loading_big.gif" width="50"/> ');
	  
			 $.ajax({
				 type: "get",
				 url: admin_base_url+'social_hub/rings/edit_post_info/'+id,
				 dataType:"json",
				 success: function(json_response){
					 
						  $('#txt_edit_title').val('');
						  $('#txt_edit_post').val('');
						  $('#txt_edit_title').val(json_response.post_info_title);
						  $('#txt_edit_post').val(json_response.post_info_desc);
						  
						  
                          hideUILoader('post-edit-popup');
				  },
				  error: function(){
					 hideUILoader('post-edit-popup');
					  showUIMsg('Some error occurred. Please try again.');
				  }
			  });	 
	
	}



  function update_post()
    {
        
        var id = $('#i_post_edit_id').val();
             
		showUILoader('<img src="'+base_url+'images/loading_big.gif" width="50"/> ');
      
             $.ajax({
                 type: "post",
                 url: admin_base_url+'social_hub/rings/edit_post_info',
                 data : ({'txt_edit_title':$('#txt_edit_title').val(), 'txt_edit_post':$('#txt_edit_post').val(),'i_edit_id':id}),
                 dataType:"json",
                 success: function(json_response){
                     
                    
        			  $('.error-message').each(function(i){
						  $(this).attr('style','display:none');
					  });
                     
                      if(json_response.success == 'true') {
                       
                             hideUILoader('post-edit-popup');
                             hide_dialog();
							 showUIMsg(json_response.msg);
                             $('#table_content').html(json_response.html);
                      }
                      else {
						   
                            for ( var id in json_response.arr_messages ){
                                //alert(data.arr_messages[id]);
                                if( $('#err_'+id) != null ) {
                                    $('#err_'+id).html(json_response.arr_messages[id]);
                                    $('#err_'+id).css('display', 'block');
                                }
                            }
                          
                          hideUILoader('post-edit-popup');
                         //showUIMsg('Some error occurred. Please try again.');
                          
                      }
					  
                  },
                  error: function(){
                     hideUILoader('post-edit-popup');
                      showUIMsg('Some error occurred. Please try again.');
                  }
              });     
        //show_dialog('edit-scrolling-headlines');
        //return false;
    }

 
 function clrErrorMsgs()
 {
    $('.error-message').each(function(i){
		$(this).attr('style','display:none');
	});
 }
 
function delete_ring_confirm(file_id)
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
				 url:base_url + 'admin/social_hub/rings/delete_ring_post',
				 dataType:"json",
				data: ({'id':selected_id}),
				 success: function(json_response){
					 hide_dialog();
					  if(json_response.result = 'success') {
						
						$('#table_content').html(json_response.html)
						showUIMsg("Ring post has been deleted successfully.");
							
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