if (typeof(topcmm_twitter_account) == 'undefined' || topcmm_twitter_account.length == 0)
{
    var topcmm_twitter_account = "123flashchat";
}

function sendTwitterRequest()
{
    $.ajax({
        url: 'http://api.twitter.com/1/followers/ids.json',
        data: {screen_name: topcmm_twitter_account},
        dataType: 'jsonp',
        success: function(data)
        {
            var ids = data.ids;
            var idList = '';
            for (var i=0;i<48;i++)
            {
                if (ids[i])
                {
                    idList += ids[i]+',';
                }
            }
            $.ajax({
                url: 'http://api.twitter.com/1/users/lookup.json',
                data: {user_id: idList},
                dataType: 'jsonp',
                success: function(data)
                {
                    followers = '';
                    for (follower in data)
                    {
                        if(typeof(data[follower]['profile_image_url']) != 'undefined')
                        {
                            followers += "<a target=\"_blank\" href=\"http://www.twitter.com/" + data[follower]["screen_name"] + "\"><img width=24 height=24 src=\"" + data[follower]["profile_image_url"].replace("normal", "mini") + "\" alt=\"" + data[follower]["name"] + "\" title=\"" + data[follower]["name"] + "\"></a>";
                        }
                    }
                    $("#twitter_follow").html(followers);
                }
            });
        }
    });

    $.ajax({
        url: 'http://api.twitter.com/1/statuses/user_timeline.json',
        data: {screen_name: topcmm_twitter_account, exclude_replies: 'true', trim_user: 'true'},
        dataType: 'jsonp',
        success: function(data)
        {
            var tweets = '';
            for(var i=0;i<data.length;i++)
            {
                tweets += "<div class=\"tweet\"><a class=\"tweet_user\" href=\"https://twitter.com/123flashchat\"></a><p>" + data[i].text.replace(/http:\/\/t.co\/(\S+)/g, "<a href=\"http://t.co/$1\" target=\"_blank\">http://t.co/$1</a>") + "</p><a target=\"_blank\" href=\"https://twitter.com/123flashchat/status/" + data[i].id_str + "\"><small>" + data[i].created_at.replace(/(\S+) (\S+) (\S+)(.*)/g, "$1 $2 $3") + "</small></a></div>";
            }
            $("#tweets").html(tweets);
        }
    });
}

topcmm.extensions.twitter = {
    tip: "Twitter",
    position: "top",
    width: "500",
    height: "350",
    display: {
        type: "html",
        value: "<div class=\"topcmm-123flashchat-common-header-toolbar-btn-over-block\"><div onmouseout=\"this.className='topcmm-123flashchat-common-header-btn'\" onmouseover=\"this.className='topcmm-123flashchat-common-header-btn-over-color'\" class=\"topcmm-123flashchat-common-header-btn\"><span class=\"topcmm-123flashchat-common-header-twitter-btn-img\"></span><span>Stream</span></div></div>"
    },
    content: "<div id=\"app_btn_twitter\" style=\"border:none; overflow-y:scroll;width:500px;height:350px;\"><div id=\"twitterleft\"><a href=\"https://twitter.com/123flashchat\" title=\"Follow\" target=\"_blank\"><img src=\"http://www.123flashchat.com/123webmessenger/images/twitter_follow.png\" /></a><div id=\"twitter_follow\"></div></div><div id=\"tweets\"></div><div style=\"clear:both;\"></div></div>",
    action: "sendTwitterRequest()"
}
