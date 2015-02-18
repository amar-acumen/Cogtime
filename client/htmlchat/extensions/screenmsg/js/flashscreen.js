(function(undefined)
{
    var showOnSelf;
    var fsDoc;
    function FlashMessage(sender, message){
        this.sender=sender;
        this.message=message;
    }
    try
    {
        if (window.top == window)
        {
            showOnSelf = true;
            fsDoc = window;
        }
        else
        {
            window.top.topcmm = topcmm;
            showOnSelf = false;
            fsDoc = window.top;
        }
    }
    catch(e)
    {
        showOnSelf = true;
        fsDoc = window;
    }

    var transactionId;
    //var flashscreen = "http://192.168.0.222:80/screenmsg/server/flashscreen.php";        //for php
    //var flashscreen = "http://localhost:8080/ChatPlugins/servlet/ScreenMsg";
    var messages = new Array()
    function showAdvanceMsg(sender, msg)
    {
        var tagMsg = fsDoc.document.getElementById("topcmm_advance_msg");
        if (tagMsg != null && tagMsg != undefined)
        {
            var fm = new FlashMessage(sender, msg);
            messages.push(fm);
            return;
        }
        showOnScreen(sender, msg);
    }

    function showOnScreen(sender, msg)
    {
            var div = fsDoc.document.createElement('div');
            div.style.position = "fixed";
            div.id = "topcmm_advance_msg";
            div.style.zIndex= "1012";
            div.style.margin= "-30px auto auto";
            div.style.left = "0px";
            div.style.right = "0px";
            div.style.top = "50%";
            div.style.height = "65px";
            div.style.overflow = "hidden";

            fsDoc.document.body.appendChild(div);

            var iframe = fsDoc.document.createElement('iframe');
            iframe.style.width = "100%";
            iframe.id="topcmm-flashchat-flashscreen";
            iframe.name="topcmm-flashchat-flashscreen";
            iframe.allowTransparency = "true";
            iframe.style.height = "65px";
            iframe.style.position = "relative";
            iframe.style.zIndex= "101";
            iframe.style.border= "none";
            iframe.style.overflow= "hidden";
            if (showOnSelf)
            {
                iframe.src = "extensions/screenmsg/move-text.html?sender=" + sender +"&msg=" + msg;/* your URL here */
            }
            else
            {
                iframe.src = "htmlchat/extensions/screenmsg/move-text.html?sender=" + sender +"&msg=" + msg;/* your URL here */
            }

            div.appendChild(iframe);
    }

    topcmm.addLoadCallback(function()
    {
        var popConfirmId;
        function init()
        {
            topcmm.setNewLineText(replace_newline_with);
            topcmm.setNewLineCallback(function(){
                topcmm.trackEvent("FlashScreenExtension", "ClickFlashScreenBtn");
                if ("1" == topcmm.myselfIsGuest())
                {
                    topcmm.showLoginAndRegisterPanel();
                    return;
                }
                if (popConfirmId == null || (popConfirmId != null && $("#" + popConfirmId).length == 0))
                {
                    popConfirmId = topcmm.confirm(pop_up_tip_title, send_fly_message_tip, "1", function(){
                        goFlashscreen();
                        topcmm.trackEvent("FlashScreenExtension", "FlashScreenPopupConfirmViewClickOKBtn");
                    }, function(){
                        topcmm.trackEvent("FlashScreenExtension", "FlashScreenPopupConfirmViewClickCancelBtn");
                        return;
                    });
                }
            });
        }

        var lange = window.navigator.language;
        var langurl = "extensions/screenmsg/lang/lang-" + lange + ".js";
        $.getScript(langurl)
        .done(function(script, textStatus) {
            init();
        })
        .fail(function(jqxhr, settings, exception) {
            $.getScript("extensions/screenmsg/lang/lang-en-US.js")
            .done(function(script, textStatus) {
                init();
            });
        });

        topcmm.addListener("CustomMessage", function(msg)
        {
            if (msg.type == null
                    || msg.type != 'topcmm_123flashchat_flashscreen'
                    || msg.roomid != topcmm.getCurrentRoomId()) { return; }
            if (msg.owner_uid != topcmm.getMyUsername())
            {
                showAdvanceMsg(topcmm.getNicknameByUsername(msg.owner_uid), msg.msg);
            }
        });

        topcmm.addListener("MarqueeEnd", function(msg)
        {
            var le = fsDoc.document.getElementById("topcmm_advance_msg");
            if (le != null)
            {
                le.parentNode.removeChild(le);
            }
            var item = messages.shift();
            if (item != null && item != undefined)
            {
                showOnScreen(item.sender, item.message);
            }
        });

        function goFlashscreen()
        {
            var message = topcmm.getCurrentRoomInputMessage();
            if ("" == message)
            {
                topcmm.alert(tip_for_user_to_input_message);
                return;
            }
            if (topcmm.getCredits() < 1000)
            {
                if ("1" == topcmm.myselfIsGuest())
                {
                    topcmm.showLoginAndRegisterPanel();
                }
                else
                {
                    topcmm.showBuyCredits();
                }
                return;
            }
            topcmm.preUpdateCreditsForSercurity(topcmm.getCurrentRoomId(), "-1000", "flashscreen", function(transactionId){
                message = encodeURIComponent(topcmm.getCurrentRoomInputMessage());
                $.ajax({
                    url: flashscreen,
                    data: {roomid: topcmm.getCurrentRoomId(), un: topcmm.getMyUsername(), msg: message, tid: transactionId, group:topcmm.getGroup()},
                    dataType: 'jsonp',
                    success: function(data)
                    {
                        if ("1" == data.result)
                        {
                            topcmm.trackEvent("FlashScreenExtension", "FlashScreenSuccess");
                            topcmm.clearCurrentRoomInputMessage();
                            showAdvanceMsg(topcmm.getMyNickname(), message);
                        }
                        else
                        {
                            topcmm.alert(pay_credits_error);
                            return;
                        }
                    }
                });
            });
        }
    });
})();
