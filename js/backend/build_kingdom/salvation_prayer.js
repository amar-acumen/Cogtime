function tinyMCE_call()
{	
tinyMCE.init({
        // General options
        mode : "exact",
	    elements:"ta_mean_of_member,ta_what_next,ta_salvation_prayer,ta_to_become_member",
        theme : "advanced",
		width : "750px",
		height : "600px",
       plugins : "safari,spellchecker,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",
	   
	   entity_encoding : "raw",

       // Theme options
		theme_advanced_buttons1 : "bold,italic,underline,separator,strikethrough,justifyleft,justifycenter,justifyright, justifyfull,formatselect,fontsizeselect,forecolor,separator,media,image,link,unlink",
		theme_advanced_buttons2 : "",
		theme_advanced_buttons3 : "",
		
		theme_advanced_toolbar_location : "top",
		theme_advanced_toolbar_align : "left",
		theme_advanced_statusbar_location : "bottom",
		theme_advanced_resizing : true,
		file_browser_callback : "tinyBrowser",

        // Skin options
        skin : "o2k7",
        skin_variant : "silver",

        // Example content CSS (should be your site CSS)
        content_css : base_url+"css/tinymce/cms_pages.css",

        // Drop lists for link/image/media/template dialogs
        //template_external_list_url : "<?php echo base_url()?>js/template_list.js",
       // external_link_list_url : "<?php echo base_url()?>js/link_list.js",
        //external_image_list_url : "<?php echo base_url()?>js/image_list.js",
        //media_external_list_url : "<?php echo base_url()?>js/media_list.js",
		convert_urls : false,

        // Replace values for the template plugin
        template_replace_values : {
                username : "Some User",
                staffid : "991234"
        }
});
}
$(document).ready(function(arg) {
	//tinyMCE init call
	tinyMCE_call();
	});