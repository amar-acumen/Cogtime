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
		var delURL = admin_base_url + 'social_hub/blogs/delete_information/'+ selected_id;
		window.location.href = delURL;
	} else {
	
		var msg = "Sorry an error has occured, Please try again";
		showUIMsg(msg);
		
		hide_dialog();
		
	}
}

function changeStatus(id,i_status ,cur_status)
{
	var URL = admin_base_url +'social_hub/blogs/change_status/';
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




function edit_blog(id)
	{
		$('#i_edit_id').val(id );
		 showUILoader('<img src="'+base_url+'images/loading_big.gif" width="50"/> ');
	  
			 $.ajax({
				 type: "get",
				 url: admin_base_url+'social_hub/blogs/edit_info/'+id,
				 dataType:"json",
				 success: function(json_response){
					 
					  if(json_response.result == 'success') {
						  $('#txt_title').val('');
						  $('#txt_title').val(json_response.s_title);
                          
                          $('#limitedtextarea').val('');
                          $('#limitedtextarea').val(json_response.s_description);
                          
						 	hideUILoader('blog-edit-popup');
					  }
					  else {
						   hideUILoader('blog-edit-popup');
						  showUIMsg('Some error occurred. Please try again.');
					  }
				  },
				  error: function(){
					 hideUILoader('blog-edit-popup');
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
			   url: admin_base_url+"social_hub/blogs/ajax_pagination/",
			   data: datatosend,
			   success: function(data){
				  
				  hideBusyScreen(); 
				   $('#table_content').html(data);
				   
				
				 
			   }
			 });	 
			
 });
 
  $('#frm_article_search').submit(function(){ 
                        
        var datatosend=$("#frm_article_search").serialize();
        
            showBusyScreen();
            //alert(datatosend);
            
            $.ajax({
               type: "POST",
               url: admin_base_url+"social_hub/blog_detail/ajax_pagination/",
               data: datatosend,
               success: function(data){
                  
                  hideBusyScreen(); 
                   $('#table_content').html(data);
                   
                
                 
               }
             });     
            
 });
 
 
 $('#edit_blog_frm').submit(function(){ 
						
		var datatosend=$("#edit_blog_frm").serialize();
		
			showBusyScreen();
			//alert(datatosend);
			$.ajax({
			   type: "POST",
			   url: admin_base_url+"social_hub/blogs/edit_info/",
			   data: datatosend,
			   dataType:'json',
			   success: function(data){
                   
                   showUIMsg(data.msg);
                   clear_all_error_msg();
                   
                   if(data.success==true)
                   {
                       
                       hide_dialog();
                       hideBusyScreen();
                       $('#table_content').html(data.html);
                   }
                   else
                   {
                               
                        
                        
                        for ( var id in data.arr_messages ){
                            //alert(data.arr_messages[id]);
                            if( $('#err_'+id) != null ) {
                                $('#err_'+id).html(data.arr_messages[id]);
                                $('#err_'+id).css('display', 'block');
                            }
                        }
                   }
				   
				   
				   
			   }
			 });	 
					   
						
 });
 
 });
 
 function clear_all_error_msg()
 {
     $('.error-message').each(function(i){
        $(this).attr('style','display:none');
    });
 }