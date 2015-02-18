function displayOrderAJAX(recordId, i_album_id, status)
{
	
	var URL = base_url +'logged/organize_photo/maintain_displayorder_ajax/';
	
	// loading part...
	showBusyScreen();
	//alert(recordId+' -- '+i_album_id+'  -- '+status);
	// AJAX action started...
	if(ajax_req!=null) {
		ajax_req.abort();
	}
	ajax_req = $.ajax({
		type: 'POST',
		url: URL,
		data: {'status' : status, 'rid' : recordId , 'i_album_id' : i_album_id },
		dataType: 'json',
		success: function (data, status) {

			hideBusyScreen();
			
			$('#album_div').html('');
			$('#show_more_feeds_div').hide();
			
			 if(data.html != ''){
				$('#no_tweets').hide();
				$('#album_div').html(data.html);
	 		 }
			if(data.view_more==true)
			{
				 $('#album_div').append('<div class="view_more" id="show_more_feeds_div"><a href="javascript:void(0);"id="show_more_feeds_link" page_value="'+data.current_page+'" onclick="show_more_feeds('+data.current_page+')">[view more] </a></div>');
			}

		}	// end of success function...
	});
	// AJAX action end...
	
}

function delete_confirm_(file_id, album_id)
{
	$('#i_del_id').val( file_id );
	$('#i_album_id').val(album_id);
	show_dialog('delete-photo');
}

function delete_()
{
	var selected_id = parseInt( $('#i_del_id').val() );
	var album_id = $('#i_album_id').val();
	//alert(selected_id);
	if( selected_id!=0 )
	{
		showUIMsg("Photo deleted successfully.");
		var delURL = base_url + 'logged/my_photos/delete/'+ selected_id+'/'+album_id;
		window.location.href = delURL;
	} else {
	
		var msg = "Error.";
		showUIMsg(msg);
		
		hide_dialog();
		
	}
}

function change_album(id, curr_album_id)
{
	var album_id = $('#album_'+id).val();
	
	var current_page = $('#current_page').val();
	var URL = base_url +'logged/organize_photo/change_photo_album';
	showBusyScreen();

	if(ajax_req!=null) {
		ajax_req.abort();
	}
	ajax_req = $.ajax({
		type: 'POST',
		url:URL,
		dataType: 'json',

		data: ({ 'record_id' : id
				 ,'album_id' : album_id
				 ,'curr_album_id' : curr_album_id
				 ,'current_page': current_page
			 }),

		success: function (data, status) {
						
						var data = data;
						hideBusyScreen();
						if(data.sucess== true) {
							$('#album_'+id).val('move');
							//$('#album_div').html(data.content);
							showUIMsg(data.msg);
						}
						
							$('#album_div').html('');
							$('#show_more_feeds_div').hide();
							
							if(data.html != ''){
								$('#no_tweets').hide();
								$('#album_div').html(data.html);
							}
							else{
								$('#no_tweets').remove();
								$('#album_div').append('<div id="no_tweets" style="padding-top:5px;" class="section01"><div style="width:260px;" class="shade_norecords"><p class="blue_bold12">No Photos.</p></div></div>')
							}
							if(data.view_more==true)
							{
								 $('#album_div').append('<div class="view_more" id="show_more_feeds_div"><a href="javascript:void(0);"id="show_more_feeds_link" page_value="'+data.current_page+'" onclick="show_more_feeds('+data.current_page+')">[view more] </a></div>');
							}
							
				},
		error: function (data, status, e) {
			
							hideBusyScreen();
							showUIMsg(data.msg);
			
		}
	});
	
}