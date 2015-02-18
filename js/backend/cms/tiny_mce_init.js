// JS functions for backend-section "Create New Webzine-Article"...


//// for tinyMCE configurations [Start]
function tinyMCE_call()
{
	tinyMCE.init({
		mode : "textareas",
		theme : "advanced",
	
		plugins : "safari,spellchecker,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",
	
		entity_encoding : "raw",
	
		document_base_url : base_url,
		relative_urls : false,
		remove_script_host : false,
	
		theme_advanced_buttons1 : "bold,italic,underline,separator,strikethrough,justifyleft,justifycenter,justifyright, justifyfull,formatselect,fontsizeselect,forecolor,separator,link,unlink,separator,image,media,preview,advhr",
		theme_advanced_buttons2 : "",
		theme_advanced_buttons3 : "",
		theme_advanced_toolbar_location : "top",
		theme_advanced_toolbar_align : "left",
		file_browser_callback : "tinyBrowser",
		content_css : base_url +"css/tinymce.css",
		
		extended_valid_elements : "a[name|href|target|title|onclick],img[class|src|border=0|alt|title|hspace|vspace|width|height|align|onmouseover|onmouseout|name|style],hr[class|width|size|noshade],font[face|size|color|style],span[class|align|style],object[classid|codebase|width|height|align|type|data],param[name|value|movie|allowFullScreen|allowScriptAccess],embed[quality|type|pluginspage|width|height|src|align]"
	});

}
//// for tinyMCE configurations [End]

