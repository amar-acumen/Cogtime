if (typeof(topcmm_facebook_fan_account) == 'undefined' || topcmm_facebook_fan_account.length == 0)
{
    var topcmm_facebook_fan_account = "flashchat";
}

topcmm.extensions.fan = {
    tip: "Facebook Fan Page",
    position: "top",
    width: "480",
    height: "500",
    display: {
        type: "html",
        value: "<div class=\"topcmm-123flashchat-common-header-toolbar-btn-over-block\"><div onmouseout=\"this.className='topcmm-123flashchat-common-header-btn'\" onmouseover=\"this.className='topcmm-123flashchat-common-header-btn-over-color'\" class=\"topcmm-123flashchat-common-header-btn\"><span class=\"topcmm-123flashchat-common-header-facebook-btn-img\"></span><span>Page</span></div></div>"
    },
    content: "<div style=\"float:left;background-color: white;\"><iframe src=\"//www.facebook.com/plugins/likebox.php?href=http%3A%2F%2Fwww.facebook.com%2F" + encodeURIComponent(topcmm_facebook_fan_account) + "&amp;width=480&amp;height=500&amp;colorscheme=light&amp;show_faces=false&amp;border_color&amp;stream=true&amp;header=true\" scrolling=\"no\" frameborder=\"0\" style=\"border:none; overflow:hidden; width:480px; height:500px;\" allowTransparency=\"true\"></iframe></div>"
}
