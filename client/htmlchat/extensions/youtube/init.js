if (typeof(topcmm_youtube_account) == 'undefined' || topcmm_youtube_account.length == 0)
{
    var topcmm_youtube_account = "123flashchat";
}

function getYoutubeHtml()
{
    var code = "<object width=\"400\" height=\"300\" classid=\"clsid:D27CDB6E-AE6D-11cf-96B8-444553540000\">";
    code += "<param value=\"http://apps.cooliris.com/embed/cooliris.swf\" name=\"movie\">";
    code += "<param value=\"true\" name=\"allowFullScreen\">";
    code += "<param value=\"#121212\" name=\"bgColor\">";
    code += "<param value=\"feed=api%3A%2F%2Fwww.youtube.com%2F%3Fuser%3D" + topcmm_youtube_account + "&amp;backgroundcolor=%23000000\" name=\"flashvars\">";
    code += "<param value=\"opaque\" name=\"wmode\">";
    code += "<embed width=\"400\" height=\"300\" wmode=\"opaque\" flashvars=\"feed=api%3A%2F%2Fwww.youtube.com%2F%3Fuser%3D" + topcmm_youtube_account + "&amp;backgroundcolor=%23000000\" bgcolor=\"#121212\" allowscriptaccess=\"always\" allowfullscreen=\"true\" src=\"http://apps.cooliris.com/embed/cooliris.swf\" type=\"application/x-shockwave-flash\" allownetworking=\"all\">";
    code += "<param value=\"always\" name=\"allowScriptAccess\">";
    code += "<param name=\"AllowNetworking\" value=\"all\">";
    code += "</object>";
    return code;
}

topcmm.extensions.youtube = {
    tip: "Youtube",
    position: "top",
    width: "400",
    height: "300",
    display: {
        type: "html",
        value: "<div class=\"topcmm-123flashchat-common-header-toolbar-btn-over-block\"><div onmouseout=\"this.className='topcmm-123flashchat-common-header-btn'\" onmouseover=\"this.className='topcmm-123flashchat-common-header-btn-over-color'\" class=\"topcmm-123flashchat-common-header-btn\"><span class=\"topcmm-123flashchat-common-header-youtube-btn-img\"></span><span>Videos</span></div></div>"
    },
    content: getYoutubeHtml()
}
