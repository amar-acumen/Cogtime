var version = "9.9";
var desktopHtml = '<div id="topcmm-123flashchat-common-main-div" class="topcmm-123flashchat-common-main-div topcmm-123flashchat-common-global-font-div"><div class="topcmm-123flashchat-common-main-div-outline"><div class="topcmm-123flashchat-common-main-div-interior" id="topcmm-123flashchat-common-main-div-interior"><a id="topcmm-123flashchat-loading-background-href" href="http://www.123flashchat.com" target="_blank" alt="Chat Room - Powered by 123 Flash Chat"><div class="topcmm-123flashchat-loading-container" id="topcmm-123flashchat-loading-container" style="display:none;"><div class="topcmm-123flashchat-loading-center-block" align="center"><div class="topcmm-123flashchat-loading-loading-logo"><img id="topcmm-123flashchat-loading-loading-img-url"></div><div class="topcmm-123flashchat-loading-background"><div class="topcmm-123flashchat-loading-loading-img-text">Version '
        + version
        + '</div><div class="topcmm-123flashchat-loading-loading-img" id="topcmm-123flashchat-loading-loading-img"><img src="img/common/topcmm-123flashchat-loading-img.gif" border="0"/></div><div class="topcmm-123flashchat-loading-loading-text"><span class="topcmm-123flashchat-loading-loading-text-loading" id="topcmm-123flashchat-loading-loading-text-loading">Loading ...</span></div></div></div></div></a></div></div></div><div id="div_flashchat" style="position: absolute; right: 0px; bottom: 0px;"></div>';
var mobileHtml = '<div id="topcmm-123flashchat-common-main-div-body" style="position: absolute;"><div id="topcmm-123flashchat-common-main-div-interior" class="topcmm-123flashchat-common-main-div-container"><div id="topcmm-123flashchat-loading-container" class="topcmm-123flashchat-welcome-page topcmm-123flashchat-min-height"><div class="topcmm-123flashchat-welcome-logo-area"><div class="topcmm-123flashchat-welcome-logo-area-interior"><div class="topcmm-123flashchat-welcome-logo-area-interior-table"><div class="topcmm-123flashchat-welcome-logo"></div></div></div></div><div class="topcmm-123flashchat-welcome-area" id="topcmm-123flashchat-welcome-area"><div class="topcmm-123flashchat-welcome-text-area"><span></span></div><div class="topcmm-123flashchat-welcome-powered-by"><span>Powered by TOPCMM</span></div></div><div class="topcmm-123flashchat-welcome-light-background"></div></div></div></div><div id="topcmm-123flashchat-mask-double-loading" class="ui-loader ui-corner-all ui-body-a ui-loader-default"><span class="ui-icon ui-icon-loading"></span></div><div style="position: absolute; left: 0px; top: 0px; right: 0px; bottom: -100px; opacity: 0.6; background-color: rgb(0, 0, 0); z-index: 340; display: none;" id="topcmm-123flashchat-mask-model"></div><div style="position: absolute; left: 0px; top: 42px; right: 0px; opacity: 0.8; background-color: rgb(0, 0, 0); z-index: 3400; height: 40px; display: none;" id="topcmm-123flashchat-private-tip-model"><span id="topcmm-123flashchat-private-chat-tip-message" style="color: #FFFFFF; " class="topcmm-123flashchat-private-chat-tip"></span></div>';
var loadHtml;
var isForMobile = false;

/**
 * The extension core. Extend topcmm to add more functionalities.
 */
(function(undefined)
{
    var topcmm = {
        loadComplete: false,
        extensions: {},
        listeners: {},
        init: function()
        {
            var _this = this;
            this.addLoadCallback(function()
            {
                _this.loadComplete = true;
            });
            return this;
        },
        addListener: function(type, listener)
        {
            var lowerType = type.toLowerCase();
            if (this.listeners[lowerType] === undefined)
            {
                this.listeners[lowerType] = [listener];
            }
            else
            {
                this.listeners[lowerType].push(listener);
            }
        },
        removeListeners: function(type)
        {
            if (this.listeners[type] !== undefined)
            {
                delete this.listeners[type];
            }
        },
        fireEvent: function(type, arg)
        {
            var lowerType = type.toLowerCase();
            if (this.listeners[lowerType] !== undefined)
            {
                for (var i = 0; i < this.listeners[lowerType].length; ++i)
                {
                    this.listeners[lowerType][i](arg);
                }
            }
        },
        addLoadCallback: function(callback)
        {
            if (this.loadComplete)
            {
                callback();
            }
            else
            {
                this.addListener("loadComplete", callback);
            }
        },
        trackEvent: function(category, action)
        {
            if (typeof enable_google_analytics === "undefined" || enable_google_analytics == true)
            {
                window._gaq = window._gaq || [];
                window._gaq.push(['_trackEvent', category, action]);
            }
        }
    };

    window.topcmm = topcmm.init();
})();

var Topcmm =
{
    version: "9.9",
    desktopHtml: '<div class="topcmm-123flashchat-common-main-div topcmm-123flashchat-common-global-font-div"><div class="topcmm-123flashchat-common-main-div-outline"><div class="topcmm-123flashchat-common-main-div-interior" id="topcmm-123flashchat-common-main-div-interior"><a id="topcmm-123flashchat-loading-background-href" href="http://www.123flashchat.com" target="_blank" alt="Chat Room - Powered by 123 Flash Chat"><div class="topcmm-123flashchat-loading-container" id="topcmm-123flashchat-loading-container" style="display:none;"><div class="topcmm-123flashchat-loading-center-block" align="center"><div class="topcmm-123flashchat-loading-loading-logo"><img id="topcmm-123flashchat-loading-loading-img-url"></div><div class="topcmm-123flashchat-loading-background"><div class="topcmm-123flashchat-loading-loading-img-text">Version '
        + version
        + '</div><div class="topcmm-123flashchat-loading-loading-img" id="topcmm-123flashchat-loading-loading-img"><img src="img/common/topcmm-123flashchat-loading-img.gif" border="0"/></div><div class="topcmm-123flashchat-loading-loading-text"><span class="topcmm-123flashchat-loading-loading-text-loading" id="topcmm-123flashchat-loading-loading-text-loading">Loading ...</span></div></div></div></div></a></div></div></div><div id="div_flashchat" style="position: absolute; right: 0px; bottom: 0px;"></div>',
    mobileHtml: '<div id="topcmm-123flashchat-common-main-div-body" style="position: absolute;"><div id="topcmm-123flashchat-common-main-div-interior" class="topcmm-123flashchat-common-main-div-container"><div id="topcmm-123flashchat-loading-container" class="topcmm-123flashchat-welcome-page topcmm-123flashchat-min-height"><div class="topcmm-123flashchat-welcome-logo-area"><div class="topcmm-123flashchat-welcome-logo-area-interior"><div class="topcmm-123flashchat-welcome-logo-area-interior-table"><div class="topcmm-123flashchat-welcome-logo"></div></div></div></div><div class="topcmm-123flashchat-welcome-area" id="topcmm-123flashchat-welcome-area"><div class="topcmm-123flashchat-welcome-text-area"><span></span></div><div class="topcmm-123flashchat-welcome-powered-by"><span>Powered by TOPCMM</span></div></div><div class="topcmm-123flashchat-welcome-light-background"></div></div></div></div><div id="topcmm-123flashchat-mask-double-loading" class="ui-loader ui-corner-all ui-body-a ui-loader-default"><span class="ui-icon ui-icon-loading"></span></div><div style="position: absolute; left: 0px; top: 0px; right: 0px; bottom: -100px; opacity: 0.6; background-color: rgb(0, 0, 0); z-index: 340; display: none;" id="topcmm-123flashchat-mask-model"></div><div style="position: absolute; left: 0px; top: 42px; right: 0px; opacity: 0.8; background-color: rgb(0, 0, 0); z-index: 3400; height: 40px; display: none;" id="topcmm-123flashchat-private-tip-model"><span id="topcmm-123flashchat-private-chat-tip-message" style="color: #FFFFFF; " class="topcmm-123flashchat-private-chat-tip"></span></div>',
    loadHtml: "",
    isForMobile: false,

    loadForMobile: function()
    {
        window.onload = function()
        {
            Topcmm.onLoadForMobile();
        }
        loadHtml = mobileHtml;
        document.title = "123FlashChat";
        TopcmmUtil.includeCss("mobile/css/common/topcmm-123flashchat-common.css");
        TopcmmUtil.includeCss("mobile/css/layout/topcmm-123flashchat-layout.css");
        TopcmmUtil.includeAppleTouchIcon("mobile/img/57.png", "");
        TopcmmUtil.includeAppleTouchIcon("mobile/img/114.png", "114x114");
        TopcmmUtil.includeAppleTouchIcon("mobile/img/72.png", "72x72");
        TopcmmUtil.includeMobileMetaTag("viewport", "width=device-width, initial-scale=1.0; maximum-scale=1.0; user-scalable=0;");
        TopcmmUtil.includeMobileMetaTag("apple-mobile-web-app-capable", "yes");
        TopcmmIncludeJs.includeJs([ "mobile/js/iscroll.js" ]);
    },

    loadForDesktop: function()
    {
        loadHtml = desktopHtml;
        TopcmmUtil.includeCss("css/common/topcmm-123flashchat-common.css");
        TopcmmUtil.includeCss("css/layout/topcmm-123flashchat-layout.css");
        TopcmmUtil.includeCssWithId("css/skin/default/topcmm-123flashchat-skin-default.css", "layout-color");
        TopcmmIncludeJs.setFunctionName("Topcmm.loadForDesktopAfterJsInvoked");
        TopcmmIncludeJs.includeJs([ "js/swfobject.js" ]);
    },

   onLoadForMobile: function()
    {
        setTimeout(function()
        {
            window.scrollTo(0, 1)
        }, 1000);
    },

    loadForDesktopAfterJsInvoked: function()
    {
        var flashvars = {};
        var params = {};
        var attributes =
        {
            id : "topcmm_flashchat",
            name : "topcmm_flashchat",
            allowScriptAccess : "always",
            bgColor : "ffcc00",
            align : "middle"
        };

        swfobject.embedSWF("interop.swf", "div_flashchat", "1", "1", "9.0.0",
                "expressInstall.swf", flashvars, params, attributes);
    },

    init: function()
    {
        document.getElementById("topcmm-123flashchat-preloading-background").parentNode.removeChild(document.getElementById("topcmm-123flashchat-preloading-background"));

        var userAgent = navigator.userAgent.toLowerCase();
        var url = location.href;
        var paraString = "";

        if (url.indexOf("?") != -1)
        {
            paraString = url.substring(url.indexOf("?"), url.length);
        }
        if (null != navigator.userAgent
                .match(/(iPad)|(iPhone)|(iPod)|(android)|(BlackBerry)|(BB)|(webOS)/i))
        {
            if (top !== self)
            {
                top.location.href = location.href;
            }
            else
            {
                isForMobile = true;
                Topcmm.loadForMobile();
            }
        }
        else
        {
            Topcmm.loadForDesktop();
        }

        document.write(loadHtml);
        if (isForMobile)
        {
            document.body.className = "topcmm-123flashchat-viewport topcmm-123flashchat-common-global-font-div topcmm-123flashchat-page";
        }

        if ("undefined" == typeof enable_google_analytics || true == enable_google_analytics)
        {
            var _gaq = _gaq || [];
            _gaq.push([ '_setAccount', 'UA-1907790-18' ]);
            _gaq.push([ '_trackPageview' ]);
            (function()
            {
                var ga = document.createElement('script');
                ga.type = 'text/javascript';
                ga.async = true;
                ga.src = ('https:' == document.location.protocol ? 'https://ssl'
                        : 'http://www')
                        + '.google-analytics.com/ga.js';
                var s = document.getElementsByTagName('script')[0];
                s.parentNode.insertBefore(ga, s);
            })();
        }
    }
}

var TopcmmUtil =
{
    includeCss : function(sCSSFileName)
    {
        this.includeCssWithId(sCSSFileName, "");
    },

    includeCssWithId : function(sCSSFileName, id)
    {
        var sobj = document.createElement('link');
        sobj.rel = "stylesheet";
        sobj.type = "text/css";
        if ("" != id)
        {
            sobj.id = id;
        }
        sobj.href = sCSSFileName;
        var headobj = document.getElementsByTagName('head')[0];
        headobj.appendChild(sobj);
    },

    includeAppleTouchIcon : function(iconFileName, size)
    {
        var sobj = document.createElement('link');
        sobj.rel = "apple-touch-icon";

        if ("" != size)
        {
            sobj.setAttribute("sizes", size)
        }
        sobj.href = iconFileName;
        var headobj = document.getElementsByTagName('head')[0];
        headobj.appendChild(sobj);
    },

    includeMobileMetaTag : function(name, content)
    {
        var sobj = document.createElement("meta");
        sobj.name = name;
        sobj.content = content;
        var headobj = document.getElementsByTagName('head')[0];
        headobj.appendChild(sobj);
    }
};

var TopcmmIncludeJs =
{
    jslist : "",
    sobj : "",
    functioname : null,
    isCallBack : false,

    includeJs : function(jslist)
    {
        if (jslist.length > 0)
        {
            TopcmmIncludeJs.setJsList(jslist);
            TopcmmIncludeJs.includeWriteJs();
        }
    },

    includeWriteJs : function()
    {
        TopcmmIncludeJs.sobj = document.createElement('script');
        TopcmmIncludeJs.sobj.type = "text/javascript";
        TopcmmIncludeJs.sobj.src = TopcmmIncludeJs.jslist[TopcmmIncludeJs.jslist.length - 1];
        var headobj = document.getElementsByTagName('head')[0];
        headobj.appendChild(TopcmmIncludeJs.sobj);
        TopcmmIncludeJs.sobj.onreadystatechange = TopcmmIncludeJs.ready;
        TopcmmIncludeJs.sobj.onerror = TopcmmIncludeJs.sobj.onload = TopcmmIncludeJs.callback;
    },

    ready : function()
    {
        if (TopcmmIncludeJs.sobj.readyState == 'loaded'
                || TopcmmIncludeJs.sobj.readyState == 'complete')
        {
            TopcmmIncludeJs.callback();
        }
    },

    callback : function()
    {
        TopcmmIncludeJs.jslist.pop();

        if (TopcmmIncludeJs.jslist.length > 0)
        {
            TopcmmIncludeJs.includeWriteJs(TopcmmIncludeJs.jslist);
        }
        else
        {
            if (TopcmmIncludeJs.functioname != null
                    && TopcmmIncludeJs.isCallBack == false)
            {
                TopcmmIncludeJs.isCallBack = true;
                eval(TopcmmIncludeJs.functioname + "()");
            }
        }
    },

    setJsList : function(jslist)
    {
        TopcmmIncludeJs.jslist = jslist.reverse();
    },

    setFunctionName : function(functioname)
    {
        TopcmmIncludeJs.functioname = functioname;
    }
};
