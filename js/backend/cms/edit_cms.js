function tinyMCE_call()
{
	
tinyMCE.init({
	
		// General options
		mode : "exact",
		elements:"elm1",
        theme : "advanced",
		//width : "750px",
		//height : "600px",
			plugins : "safari,spellchecker,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",

		// Theme options
		theme_advanced_buttons1 : "bold,italic,underline,separator,strikethrough,justifyleft,justifycenter,justifyright, justifyfull,formatselect,fontsizeselect,forecolor,separator,link,unlink,image,media,code",
		theme_advanced_buttons2 : "",
		theme_advanced_buttons3 : "",
		theme_advanced_toolbar_location : "top",
		theme_advanced_toolbar_align : "left",
		theme_advanced_statusbar_location : "bottom",
		theme_advanced_resizing : true,
		file_browser_callback : "tinyBrowser",
		
		skin : "o2k7",
        skin_variant : "silver",
		content_css : base_url+"css/tinymce/cms_pages.css",
		convert_urls : false,
		// Style formats
		/*style_formats : [
			{title : 'Bold text', inline : 'b'},
			{title : 'Red text', inline : 'span', styles : {color : '#ff0000'}},
			{title : 'Red header', block : 'h1', styles : {color : '#ff0000'}},
			{title : 'Table styles'},
			{title : 'Table row 1', selector : 'tr', classes : 'tablerow1'}
		],*/

		// Replace values for the template plugin
		template_replace_values : {
			username : "Some User",
			staffid : "991234"
		}
	
	
});

}
//// for tinyMCE configurations [End]


$(document).ready(function(arg) {
	//tinyMCE init call
	tinyMCE_call();
	
	var id = $('#i_edit_id').val();
	 
	// for AJAX page-submission... edit
    optionsArr_edit = { 
        beforeSubmit:  showBusyScreen,  // pre-submit callback 
        success:       validateEditFrm, // post-submit callback 
		url:		admin_base_url + "site_settings/cms-pages/edit-cms-pages/" +id
    }; 
	
});

// edit form submission

function post_frm_ajax_edit()
{
	 tinyMCE.triggerSave(true,true);
	//alert(1);
	$('#frm_cms_edit').ajaxSubmit(optionsArr_edit);
	
	return false;
}



// validate ajax-submission...
function validateEditFrm(data)
{
	
	var data = JSON.parse(data);

	//alert(data.success);
	if(data.success==false) 
	{
		

		$('.error-message').each(function(i){
			$(this).attr('style','display:none');
		});
		
		
		for ( var id in data.arr_messages ){
			//alert('#err_'+id);
			if( $('#err_'+id) != null ) {
				$('#err_'+id).html(data.arr_messages[id]);
				$('#err_'+id).css('display', 'block');
			}
		}

		
	}
	else {
		showUIMsg(data.msg);		
		window.location.href = admin_base_url + "site_settings/cms-pages.html";
		
		
	}
	
	// hide busy-screen...
	hideBusyScreen();
}
// end

