/*123 Flash Chat Live Show Version 7.4.0-0729 Build 6541*/

var rootJSPath = "js"
var rootCSSPath = "css"
if (http_root_123flashchat.length != 0) {
    var re = new RegExp("/+$");
    rootJSPath = http_root_123flashchat.replace(re, "") + "/" + rootJSPath;
    rootCSSPath = http_root_123flashchat.replace(re, "") + "/" + rootCSSPath;
}
document.write('<link rel="stylesheet" id="blue" type="text/css" href="' + rootCSSPath + '/ext-all.css" />');
includeJS(rootJSPath + "/swfobject.js");
includeJS(rootJSPath + "/RSA.js");
includeJS(rootJSPath + "/BigInt.js");
includeJS(rootJSPath + "/Barrett.js");
includeJS(rootJSPath + "/communication.js");
function includeJS(path){
    var sobj = document.createElement('script');
    sobj.type = "text/javascript";
    sobj.src = path;
    var headobj = document.getElementById('live_show_123flashchat');
    headobj.appendChild(sobj);
}

function openIntroSWF(url){
    var so = new SWFObject(url, "topcmm_flashchat", "100", "100", "8", "#FFFFFF");
    so.addParam("quality", "high");
    so.addParam("wmode", "transparent");
    so.addParam("allowScriptAccess", "always");
    so.write("div_flashchat");
}

if (!isSupportFlash()) {
    var my_domain = "";
    if (my_domain == undefined || my_domain == "") {
        my_domain = getDomain();
    }
    document.domain = my_domain;
}
function isIP(ipStr){
    var result = false;
    if (ipStr.length > 0) {
        try {
            var temp = ipStr.split(".");
            if (temp.length != 4) {
                result = false;
            }
            else {
                for (var i = 0; i < temp.length; i++) {
                    if (!isIntegerInRange(temp[i], 0, 255)) {
                        result = false;
                        break;
                    }
                }
                result = true;
            }
        } 
        catch (e) {
            result = false;
        }
    }
    else {
        result = false;
    }
    return result;
}

function isIntegerInRange(s, a, b){
    if (isEmpty(s)) 
        if (isIntegerInRange.arguments.length == 1) 
            return false;
        else 
            return (isIntegerInRange.arguments[1] == true);
    if (!isInteger(s, false)) 
        return false;
    var num = parseInt(s);
    return ((num >= a) && (num <= b));
}

function isInteger(s){
    var i;
    if (isEmpty(s)) 
        if (isInteger.arguments.length == 1) 
            return 0;
        else 
            return (isInteger.arguments[1] == true);
    for (var i = 0; i < s.length; i++) {
        var c = s.charAt(i);
        if (!isDigit(c)) 
            return false;
    }
    return true;
}

function isEmpty(s){
    return ((s == null) || (s.length == 0))
}

function isDigit(c){
    return ((c >= "0") && (c <= "9"))
}

function getDomain(){
    var COLON = ":";
    var DOT = ".";
    var domainStr = "";
    var hostStr = "";
    try {
        if (http_root_123flashchat.length != 0 && http_root_123flashchat.indexOf("http://") != -1) {
            hostStr = http_root_123flashchat.substring("http://".length, http_root_123flashchat.length);
        }
        else {
            hostStr = window.location.host;
        }
        domainStr = hostStr.indexOf(COLON) == -1 ? hostStr : hostStr.substring(0, hostStr.indexOf(COLON));
	    domainStr = domainStr.indexOf("/") == -1 ? domainStr : domainStr.substring(0, domainStr.indexOf("/"));
        if (!isIP(domainStr)) {
            var domainArray = domainStr.split(".");
            //if (domainArray.length > 2) {
            //domainStr = domainArray[domainArray.length - 2] + "." + domainArray[domainArray.length - 1];
           // }
        }
    } 
    catch (e) {
        domainStr = "";
    } 
    return domainStr;
}

function getMovie(){
    try {
        var movieName = "topcmm_flashchat";
        if (isIE()) {
            return window[movieName];
        }
        else {
            return document[movieName];
        }
    } 
    catch (e) {
        return undefined;
    }
}

function isIE(){
    return getUserAgent().indexOf("msie") != -1;
}

function isMobile(){
    var userAgentStr = getUserAgent();
    return (userAgentStr.indexOf("iphone") != -1 || userAgentStr.indexOf("android") != -1 || userAgentStr.indexOf("msiemobile") != -1);
}

function getUserAgent(){
    return navigator.userAgent.toLowerCase();
}

function isSupportFlash(){
    var result = false;
    try {
        if (navigator.plugins && navigator.plugins.length > 0) {
            var swf = navigator.plugins["Shockwave Flash"];
            if (swf) {
                result = true;
            }
        }
        if (!result) {
            var swf = new ActiveXObject('ShockwaveFlash.ShockwaveFlash');
            if (swf) {
                result = true;
            }
        }
    } 
    catch (e) {
        result = false;
    }
    return result;
}
