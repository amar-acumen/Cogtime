// define extension information start
//define extension information end


// define extension function start
function onAppleClicked()
{
    playEgg();
}

function playEgg()
{
    var le = document.getElementById("topcmm-123flashchat-warp-egg-id");
    if (le != null)
    {
        le.parentNode.removeChild(le);
        return;
    }
    var div = document.createElement('div');
    div.style.position = "relative";
    div.id = "topcmm-123flashchat-warp-egg-id";
    div.style.zIndex= "101";
    div.style.margin= "50px auto auto";
    div.style.width = 520+"px";
    div.style.height = 550+"px";

    document.body.appendChild(div);


    var iframe = document.createElement('iframe');
    iframe.style.width = 520+"px";
    iframe.style.height = 550+"px";
    iframe.style.position = "relative";
    iframe.style.zIndex= "101";
    iframe.style.border= "none";
    iframe.style.overflow= "hidden";
    iframe.src = "plugin/egg/egg.html";/* your URL here */
    div.appendChild(iframe);
}

function clearMessages()
{
    var parent = document.getElementById('topcmm-123flashchat-main-messageview');
    while (parent.firstChild)
    {
        parent.removeChild(parent.firstChild);
    }
}

//define extension function end

var extensions = [];