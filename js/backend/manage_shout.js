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
		
		 showUILoader_nodialog('<img src="'+base_url+'images/loading_big.gif" width="50"/> ');
	
		
		$.ajax({
				type: 'get',
				url: admin_base_url + 'media_center/minister_shouts/delete_shout/'+ selected_id,
				success: function (data) {
							
							if(data.success == true){
								showUIMsg("Shout deleted successfully.");
								hide_dialog();
								hideUILoader_nodialog();
							}
							
					}	// end of success function...
			});

	} else {
	
		var msg = "Sorry an error has occured, Please try again";
		showUIMsg(msg);
		hide_dialog();
		
	}
}

function changeStatus(id,i_status ,cur_status)
{
	var URL = admin_base_url +'media_center/minister_shouts/change_status/';
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
				   url: admin_base_url+"media_center/minister_shouts/minister_shout_listing_ajax/",
				   data: datatosend,
				   success: function(data){
					  
					  hideBusyScreen(); 
					   $('#table_content').html(data);
					   
					
					 
				   }
				 });	 
				
	 });
});


function show_article(article_id,user_id)
{
	//alert(article_id+','+user_id);
	showUILoader('<img src="'+base_url+'images/loading_big.gif" width="50"/> ');
	
	
	
	$.ajax({
		url : admin_base_url+'social_hub/blog_detail/article_detail',
		type : 'post',
		dataType : 'json',
		data : ({'id':article_id,'user_id':user_id}),
		success : function (data)
		{
			hideUILoader('article-popup');
			if(data.success==true)
			{
				$('#img_article_post_by').attr('src',data.img);
				$('#article_title').html(data.title);
				$('#article_user_name').html(data.user_name);
				$('#article_posted_on').html(data.created_dt);
				$('#article_desc').html(data.desc);
				
			}
			else
			{
				showUIMsg('Sorry. Error occured.');
			}
		}
	});
}
