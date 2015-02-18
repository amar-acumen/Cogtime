//-------------------------------------- create video album ----------------------------------
function create_video_album_()
{

	// for AJAX page-submission...
    optionsArr = { 
        beforeSubmit:  showBusyScreen,  // pre-submit callback 
        success:       validateFrm // post-submit callback 
    }; 
 
	frm_obj = $('#createVideoAlbumFrm');
	$(frm_obj).ajaxSubmit(optionsArr);
	
	return false; 
}

// validate ajax-submission...
function validateFrm(data)
{
    
	var result_obj = JSON.parse(data);
	   
	if(result_obj.result==true) {

		showUIMsg(result_obj.msg);
		$('#createVideoAlbumFrm')[0].reset();
		
		
	//window.location.href="create-video-album.html";
window.location.href=result_obj.http_referer;
		//location.reload();
		
	
	}

	$('.error-message').each(function(i){
		$(this).attr('style','display:none');
	});
    $('div[id^=err_]').html(''); 
	
	if(result_obj.result==false) {
	
		
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
	
	// hide busy-screen...
	hideBusyScreen();
}
//-------------------------------------- end of create video album ----------------------------------


//-------------------------------------- edit video album ----------------------------------
function edit_video_album_()
{

	// for AJAX page-submission...
    optionsArr = { 
        beforeSubmit:  showBusyScreen,  // pre-submit callback 
        success:       validateEditFrm // post-submit callback 
    }; 
 
	frm_obj = $('#editVideoAlbumFrm');
	$(frm_obj).ajaxSubmit(optionsArr);
	
	return false; 
}

// validate ajax-submission...
function validateEditFrm(data)
{
	var result_obj = JSON.parse(data);
	   
	if(result_obj.result==true) {
		var album_id = $('#album_id').val();
		var album_name = $('#txt_video_album_name').val();
		showUIMsg(result_obj.msg);
		//$('#editVideoAlbumFrm')[0].reset();
		window.location.href=base_url+"manage-video-album.html";
		//location.reload();
	
	}

	$('.error-message').each(function(i){
		$(this).attr('style','display:none');
	});
    $('div[id^=err_]').html(''); 
	
	if(result_obj.result==false) {
	
		
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
	
	// hide busy-screen...
	hideBusyScreen();
}
//-------------------------------------- end of edit video album ----------------------------------




//----------------------------------------- upload video --------------------------------------------
function upload_video_()
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
 	//window.location.href=base_url+"my-videos.html";
	window.location.href=result_obj.http_referer;
	
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

//====================================== necessary tiny functions =======================================
function form_reset_(id)
{
	$('#'+id)[0].reset();
}


function clr_all_err_()
{
	$('.error-message').each(function(i){
			$(this).attr('style','display:none');
		});
}



//==================================== end of necessary tiny functions ====================================




//--------------------------------------- search video --------------------------------------------
$(document).ready(function(arg){
 $('#search_form').submit(function(){ 
      
    var datatosend=$("#search_form").serialize();
    //alert(datatosend);
     showUILoader_nodialog('<img src="'+base_url+'images/loading_big.gif" width="50"/> ');
     $.ajax({
	     type: "POST",
	     url: base_url+"logged/my_videos/my_videos_ajax_pagination",
	     data: datatosend,
		 dataType : 'json',
	     success: function(data){
	     $('#search_form')[0].reset();
		if(data.html=='')
		{
			var no_record = '<div class="section01" style="padding-top:5px;"><div  class="shade_norecords" style="width:260px;"><p class="blue_bold12">No Videos.</p></div></div>';
			$('#all_videos').html(no_record);
		}
		else{
			$('#all_videos').html(data.html);	
		}
	     
	        hide_dialog('search_audio');
	     hideUILoader_nodialog();
	     }
      });  
 });
});




//-------------------------------------- delete video album --------------------------------
function set_del_album_id(album_id)
{
	$('#i_del_id').val(album_id);
}
function delete_video_album_()
{
	var album_id=$('#i_del_id').val();
	$.ajax({
		url : base_url+"logged/my_videos/delete_video_album",
		type : "post",
		dataType : "json",
		data :({"album_id" : album_id}),
		success : function(data){
			//alert(data.response);
			showUIMsg(data.msg);
			if(data.response==true)
			{
				
				window.location.href="manage-video-album.html";
				
			}
			else
			{
				hide_dialog('delete-page-popup');
			}
		}
	});
}


//----------------------------- organize section ------------------------------------------
//------------------------------ display order ----------------------------
function displayOrderAJAX(recordId, i_album_id, status)
{
	
	var URL = base_url +'logged/organize_my_videos/maintain_displayorder_ajax/';
	
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
//alert(data);
			hideBusyScreen();
			
			
			$('#video_albums').html('');
			$('#show_more_feeds_div').hide();
			
			 if(data.html != ''){
				$('#no_audio_box').hide();
				$('#video_albums').html(data.html);
	 		 }
			if(data.view_more==true)
			{
				 $('#video_albums').append('<div class="view_more" id="show_more_feeds_div"><a href="javascript:void(0);"id="show_more_feeds_link" page_value="'+data.current_page+'" onclick="show_more_feeds('+data.current_page+')">[view more] </a></div>');
			}

		}	// end of success function...
	});
	// AJAX action end...
	
}
//----------------------------- end organize section ------------------------------------------