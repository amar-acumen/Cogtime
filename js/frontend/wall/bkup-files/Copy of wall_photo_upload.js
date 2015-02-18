function create_new_webzine_ajax()
{
	tinyMCE.triggerSave();
	
	$(frm_obj).ajaxSubmit(optionsArr);
	
	return false; 
}


// validate ajax-submission...
function validateFrm(data)
{
	alert(data);
	var result_obj = JSON.parse(data);

	if(result_obj.result=='success') {
		
		showUIMsg(result_obj.msg);
		
		//window.location.href = result_obj.redirect;
		window.location.href = base_url + result_obj.redirect;
	}

	$('.error_msg').each(function(i){
		$(this).attr('style','display:none');
	});

	if(result_obj.result=='error') {
	
		for ( var id in result_obj.arr_messages ){
			
			if( $('#err_'+id) != null ) {
				$('#err_'+id).html(result_obj.arr_messages[id]);
				$('#err_'+id).show();
			}
		}
		
	}
	
	// hide busy-screen...
	hideBusyScreen();
}

/////////////// AJAX CREATE NEW "MAGAZINE-ARTICLE" [END] //////////////


// @@@@@@@@@@@ MISC FUNCTIONALITIES [BEGIN] @@@@@@@@@@@

// FUNCTION TO POPULATE MAGAZINE SUB-CATEGORIES ON
// PARENT-CATEGORY SELECTION [AJAX CALL]...
function selectMagSubCategoriesAJAX(selected_category)
{
	var selected_lang = jQuery.url.segment(3);
	var ajaxURL = admin_base_url +'magazine_utilities/populate_sub_category_list/'+ selected_category +'/'+ selected_lang;
	$('#sub_category option:selected').text("Loading...");
	
	$.ajax({
		url: ajaxURL,
		dataType: 'json',
		success: function(data) {
			
			if( data.result=='success' )
				$('#div_mag_subcategory').html(data.html_content);
			
		}
	});
}

// @@@@@@@@@@@ MISC FUNCTIONALITIES [END] @@@@@@@@@@@


$(function() {
		   
		//// WEBZINE ARTICLE PHOTOS - ADD MORE [START]
			var scntDiv = $('#div_more_photos');
			var i = $('#div_photos input[type=file]').size() + 1;
			
			$('#add_more_photos').live('click', function() {
				$('<div class="clr"></div><div class="fld01"><input name="article_photos[]" type="file" size="54" />&nbsp;<a href="javascript:void(0)" id="remove_photos"><img src="images/admin/Trash.png" /></a></div>').appendTo(scntDiv);
				i++;
				
				return false;
			});
			
			$('#remove_photos').live('click', function() {
				
				if( i > 2 ) {
					
					// deleting the div...
					$(this).parents('div.fld01').remove();
					i--;
				}
				return false;
			});
		//// WEBZINE ARTICLE PHOTOS - ADD MORE [END]
		
		
		//// WEBZINE ARTICLE VIDEOS - ADD MORE [START]
			var vidDiv = $('#div_more_videos');
			var j = $('#div_videos input[type=text]').size() + 1;
			var cloned_HTML = $('#div_video_icons').clone().html();
			
			$('#add_more_videos').live('click', function() {
														 
				
				$('<div class="clr"></div><div class="fld01"><input name="article_videos[]" type="text" class="left" /> <a href="javascript:void(0)" id="remove_videos" style="line-height:30px;"><img src="images/admin/Trash.png" class="left space" /></a>'+ cloned_HTML +'</div>').appendTo(vidDiv);
				j++;
				
				return false;
			});
			
			$('#remove_videos').live('click', function() { 
				if( j > 2 ) {
					
					// 1st, deleting the "label" div...
					$(this).parents('div.fld01').prev("div").remove();
					
					// 2nd, finally deleting the parent container div...
					$(this).parents('div.fld01').remove();
					j--;
				}
				return false;
			});
		//// WEBZINE ARTICLE VIDEOS - ADD MORE [END]
		
});



// =========================================================================
// 			"KB"-STYLE IMAGE UPLOADER [START]
// =========================================================================

$(function()
{
	/**---------------- Multiple Upload -------------------------*/
	var btnUpload	= $('#btn_upload');
	var status	= $('#status');
    var errMsg = $('#err_article_img');
	
	var _temp_img_path = base_url +'uploads/wall_tmp/';
	
	var ajaxURL = base_url +'newsfeed/upload_multiple_img_AJAX';
	
	new AjaxUpload(btnUpload, {	action: ajaxURL,
					name: 'uploadfile',
					onSubmit: 	function(file, ext)
							{
								if (! (ext && /^(jpg|png|jpeg|gif|bmp)$/.test(ext)))
								{
									// extension is not allowed 
									errMsg.html('* Seuls les fichiers JPG, PNG ou GIF sont acceptés.');
									return false;
								}
                                errMsg.empty();
								status.html('<div class="info_msg">Téléchargement ...</div>');
								showLoading();
							},
					onComplete: 	function(file, response)
							{
								//On completion clear the status
								var arr = Array();
								//// alert( response );
								arr = response.split('|@sep@|');
								if(arr[0]==="ok")
								{
									var nm_wo_dot = arr[2].replace('.','');
									var nm_w_sep = arr[2].replace('.', '|@SEP@|');
									status.html('<div class="ok_msg">Upload de fichier avec succès ...</div>');
                                    
									var _temp_img = _temp_img_path + arr[1];
									var radio_fld_id = arr[2].replace('.','');
									/* <input id="'+ radio_fld_id +'" name="rdo_main_pic" type="radio" value="'+ nm_w_sep +'" class="radio_primary" />*/
									
									$('#all_photos').append($('<div id="div'+ nm_wo_dot +'" ><div class="view-thumb"  style="background:url('+ _temp_img +')  no-repeat center;max-width:60; height:60;"><a href="javascript:void(0);" class="remove" onclick="javascript:delpic(\''+ nm_w_sep +'\')"></a></div></div>'));
																		
									$('#'+ radio_fld_id).attr('checked', 'checked');
									$("#clr_div").remove();
									
									$('<div id="clr_div" class="clr"></div>').appendTo('#all_photos');
                                    
									$('<input type="hidden" id="hid_'+ radio_fld_id +'" name="photo[]" />').appendTo('#hdnflds').val(arr[2]);
									
									if( $('#all_photos').css('display')=='none' )
										$('#all_photos').show();
									//appbtnUpload.hide();
								}
								else
								{
									status.html(arr[2]);
								}
								hideLoading();
							}
							
					});
					
	/*------------------- Done ------------------*/

});



//// function to delete uploaded picture...
function delpic(filename)
{
	var radioid	= filename.replace('|@SEP@|', '');
	var radiolength	= $('input[name=rdo_main_pic]').length;
	var selectedIndex	= $('input[name=rdo_main_pic]:checked').index('input[name=rdo_main_pic]') ;
	
	if(radiolength > 1)
	{
		if(selectedIndex==0)
		{
			selectedIndex	=	1;
		}
		else
			selectedIndex	= selectedIndex-1;
	}
	
	var status	= $('#status');
	var btnuplad = $('#btn_upload');
	var delAJAXurl = base_url +'newsfeed/delete_tmp_image_AJAX/'+ filename.replace('|@SEP@|', '/') + '/extraparam/';
	
	$.get(delAJAXurl, function(msg){
							   
		if(msg === 'ok')
		{
			//selectedIndex	= selectedIndex-1;
			if(selectedIndex!= -1)
			{
				$('input[name=rdo_main_pic]:eq('+selectedIndex+')').attr('checked', 'checked');
			}
			
			$("#div"+filename.replace('|@SEP@|','')).remove();	
			$("#hid_"+filename.replace('|@SEP@|','')).remove();
			
			//search_for_other();
			
			status.html('<div class="ok_msg">Image deleted successfully ...</div>');
			btnuplad.show();
		}
		else
		{
			status.attr('class', 'ok');
			status.html('<div class="err_msg">Image cannot be deleted!</div>');
		}
	});
};

/*function delpic_orig(filename,path)
{
	var radioid	= filename.replace('|@SEP@|', '');
	var radiolength	= $('input[name=rdo_main_pic]').length;
	var selectedIndex	= $('input[name=rdo_main_pic]:checked').index('input[name=rdo_main_pic]') ;
	
	if(radiolength > 1)
	{
		if(selectedIndex==0)
		{
			selectedIndex	=	1;
		}
		else
			selectedIndex	= selectedIndex-1;
	}
	else if(radiolength == 1)
	{
		$('#selectedimage_err').html('You can not delete this file, you must have atleast one image.');
		return false;
	}
	
	var status	= $('#status');
	var btnuplad = $('#btn_upload');
	var delAJAXurl = base_url + path+'/delete_tmp_image_AJAX/'+ filename.replace('|@SEP@|', '/') + '/orig/';
	//alert(delAJAXurl);return false;
	$.get(delAJAXurl, function(msg){
							   
		if(msg === 'ok')
		{
			if($('#'+radioid).attr('checked') =='checked' )
			{
				/*$('input:radio[name=rdo_main_pic]')[0].checked = true;
				var firstradioid	= $('input:radio[name=rdo_main_pic]')[0];
				alert($('#'+firstradioid).val());
				alert('hhhh');*/
				//selectedIndex	= selectedIndex-1;
			/*	if(selectedIndex!= -1)
				{
					$('input[name=rdo_main_pic]:eq('+selectedIndex+')').attr('checked', 'checked');
					var nextradio_value	=	$('input[name=rdo_main_pic]:eq('+selectedIndex+')').val().replace('|@SEP@|', '.');
					
					//$.get(base_url + path + '/set_main_image/'+nextradio_value ,function(){
					$.ajax({
					  type: 'POST',
					  url: base_url + path + '/set_main_image',
					  data: 'imagename='+nextradio_value+'&photo='+nextradio_value,
					  success: function(){}
					});
									  
				}
			}
			$("#div"+filename.replace('|@SEP@|','')).remove();	
			$("#hid_"+filename.replace('|@SEP@|','')).remove();
			
			//search_for_other();
			
			status.html('<div class="ok_msg">Image supprimé avec succès ...</div>');
			btnuplad.show();
		}
		else
		{
			status.attr('class', 'ok');
			status.html('<div class="err_msg">Image peut \'t être supprimés!</div>');
		}
	});
};*/


// =========================================================================
// 			"KB"-STYLE IMAGE UPLOADER [END]
// =========================================================================