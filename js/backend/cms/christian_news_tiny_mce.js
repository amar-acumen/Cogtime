function tinyMCE_call()
{
    
/*tinyMCE.init({
    
        // General options
        mode : "exact",
        elements:"elm1",
        theme : "advanced",
        //width : "750px",
        //height : "600px",
        plugins : "safari,spellchecker,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",

        // Theme options
        theme_advanced_buttons1 : "bold,italic,underline,separator,strikethrough,justifyleft,justifycenter,justifyright, justifyfull,formatselect,fontsizeselect,forecolor,separator,media,image,link,unlink",
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
        
        // Style formats
        /*style_formats : [
            {title : 'Bold text', inline : 'b'},
            {title : 'Red text', inline : 'span', styles : {color : '#ff0000'}},
            {title : 'Red header', block : 'h1', styles : {color : '#ff0000'}},
            {title : 'Table styles'},
            {title : 'Table row 1', selector : 'tr', classes : 'tablerow1'}
        ],*/

        // Replace values for the template plugin
        /*template_replace_values : {
            username : "Some User",
            staffid : "991234"
        }
    
    
});*/

tinyMCE.init({
        // General options
        mode : "exact",
        elements:"elm1",
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
        },
        oninit: pastePlainText
});
}
//// for tinyMCE configurations [End]

$(document).ready(function(){
    tinyMCE_call();
});
var pastePlainText = function() {
    // No need to pass in an ID, instead fetch the first tinyMCE instance
    var ed = tinyMCE.get(0); 
    ed.pasteAsPlainText = true;  

    //adding handlers crossbrowser
    if (tinymce.isOpera || /Firefox\/2/.test(navigator.userAgent)) {
        ed.onKeyDown.add(function (ed, e) {
            if (((tinymce.isMac ? e.metaKey : e.ctrlKey) && e.keyCode == 86) || (e.shiftKey && e.keyCode == 45))
                ed.pasteAsPlainText = true;
        });
    } else {            
        ed.onPaste.addToTop(function (ed, e) {
            ed.pasteAsPlainText = true;
        });
    }
};