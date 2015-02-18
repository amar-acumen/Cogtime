if (typeof(topcmm_facebook_like_account) == 'undefined' || topcmm_facebook_like_account.length == 0)
{
    var topcmm_facebook_like_account = "http://www.123flashchat.com";
}

topcmm.extensions.like = {
    tip: "Facebook Like",
    position: "top",
    display: {
        type: "html",
        value: "<div style=\"float:left;\"><iframe scrolling=\"no\" frameborder=\"0\" allowtransparency=\"true\" style=\"border:0;width:90px;height:21px;overflow:hidden; \" src=\"//www.facebook.com/plugins/like.php?href=" + encodeURIComponent(topcmm_facebook_like_account) + "&amp;send=false&amp;layout=button_count&amp;width=90&amp;show_faces=false&amp;action=like&amp;colorscheme=light&amp;font=arial&amp;height=21\"></iframe></div>"
    }
}
