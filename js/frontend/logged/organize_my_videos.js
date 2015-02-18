//----------------------------------------- upload video --------------------------------------------
function upload_video_organize_()
{
	// for AJAX page-submission...
    optionsArr = { 
        beforeSubmit:  showUILoader_nodialog('<img src="'+base_url+'images/loading_big.gif" width="50"/> '),
		//showBusyScreen,  // pre-submit callback 
        success:       validateUploadFrm // post-submit callback 
    }; 
 
	frm_obj = $('#uploadVideoFrm');
	$(frm_obj).ajaxSubmit(optionsArr);
	
	return false; 
	
}
// validate ajax-submission...
function validateUploadFrm(data)
{

	$('.error-message').each(function(i){
		$(this).attr('style','display:none');
	});
	var result_obj = JSON.parse(data);
	
	
	
	if(result_obj.success==true) {
		
		showUIMsg(result_obj.msg);
		$('#uploadVideoFrm')[0].reset();
		
		hide_dialog();
			// hide busy-screen...
	//hideBusyScreen();
	$('#video_albums').html(result_obj.html);
	//window.location.href=base_url+"my-videos.html";
	
	}

	
   // $('div[id^=err_]').html(''); 
	
	if(result_obj.success==false) {
	
		//alert(result_obj.success);
		$('.error-message').each(function(i){
			$(this).attr('style','display:none');
		});
		
		
		for ( var id in result_obj.arr_messages ){
			//alert(data.arr_messages[id]);
			if( $('#err_'+id) != null ) {
				$('#err_'+id).html(result_obj.arr_messages[id]);
				$('#err_'+id).css('display', 'block');
			}
		}
	
		 if(result_obj.maxlimit == true){
		  $('#uploadVideoFrm')[0].reset();
		  $('.error-message').hide();
		  hide_dialog();
		  showUIMsg(result_obj.msg);
		}
	
	}
	
hideUILoader_nodialog();
}
//----------------------------------------- end of upload video --------------------------------------------


//----------------------------------------- edit video popup -------------------------------------------
function organize_edit_video_(video_id)
{
	showUILoader_nodialog('<img src="'+base_url+'images/loading_big.gif" width="50"/> ');
	$.ajax({
		url : base_url+"logged/organize_my_videos/edit_video_fetch_data",
		data : ({'video_id' : video_id}),
		type : "post",
		dataType : 'json',
		success : function(data)
		{
			hideUILoader_nodialog();
			for ( var id in data.fetched_data ){
				
				$('#'+id).val('');
				$('#'+id).val(data.fetched_data[id]);
				//$('#'+id).attr('value',data.fetched_data[id]);
				
				
			}
		
			show_dialog('edit_video');
		}
	
	});
}

//----------------------------------------- edit video --------------------------------------------
//------------------------ fetch ---------------------------
function upload_video_organize_edit_()
{
	// for AJAX page-submission...
    optionsArr = { 
        beforeSubmit:  showUILoader_nodialog('<img src="'+base_url+'images/loading_big.gif" width="50"/> '),
		//showBusyScreen,  // pre-submit callback 
        success:       validateUploadFrmEdit // post-submit callback 
    }; 
 
	frm_obj = $('#uploadVideoFrmEdit');
	$(frm_obj).ajaxSubmit(optionsArr);
	
	return false; 
	
}
//------------------------ end of fetch ---------------------------



// validate ajax-submission...
function validateUploadFrmEdit(data)
{
	
	$('.error-message').each(function(i){
		$(this).attr('style','display:none');
	});
	var result_obj = JSON.parse(data);
	
	
	
	if(result_obj.success==true) {
		
		showUIMsg(result_obj.msg);
		$('#uploadVideoFrmEdit')[0].reset();
		hide_dialog();
			// hide busy-screen...
	//hideBusyScreen();
	$('#video_albums').html(result_obj.html);
	//window.location.href=base_url+"my-videos.html";
	
	}

	
   // $('div[id^=err_]').html(''); 
	
	if(result_obj.success==false) {
	
		
		$('.error-message').each(function(i){
			$(this).attr('style','display:none');
		});
		
		
		for ( var id in result_obj.arr_messages ){
			//alert(data.arr_messages[id]);
			if( $('#err_'+id) != null ) {
				$('#err_'+id).html(result_obj.arr_messages[id]);
				$('#err_'+id).css('display', 'block');
			}
		}
	
	
	
	}
	
hideUILoader_nodialog();
}
//----------------------------------------- end of edit video --------------------------------------------


//------------------------------------------- delete video -------------------------------------------
function delete_video_id_set_(video_id)
{
	$('#i_del_id').val(video_id);
	show_dialog('delete-page-popup');
}



function delete_video_()
{
	//alert(video_id);
	var video_id = $('#i_del_id').val();

	showUILoader_nodialog('<img src="'+base_url+'images/loading_big.gif" width="50"/> ');
	$.ajax({
		url : base_url+"logged/organize_my_videos/delete_video",
		data : ({'video_id' : video_id}),
		type : "post",
		dataType : 'json',
		success : function(data)
		{
			hide_dialog();
			showUIMsg(data.msg);
			//hideUILoader_nodialog();
			$('#video_albums').html(data.html);
			if(data.html=='')
            {
                $('#video_albums').append('<div class="view_more" style="text-align: center;"><p class="blue_bold12" style="font-size:14px;">No Video.</p></div>');
            }
		
			
		}
	
	});
	
}
//------------------------------------------- end of delete video -------------------------------------------