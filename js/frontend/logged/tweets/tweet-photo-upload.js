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