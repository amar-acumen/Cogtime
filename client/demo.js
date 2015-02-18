/*********************some methods used for demo page start*******************/ 
var HTML_CODE_TEMPLATE='<!-- FOR 123FLASHCHAT CODE BEGIN -->\r\n<EMBED src="#appSrc#" quality=high menu=false WIDTH="#width#" HEIGHT="#height#" TYPE="application/x-shockwave-flash" PLUGINSPAGE="http://www.macromedia.com/go/getflashplayer">\r\n</EMBED>\r\n<!-- FOR 123FLASHCHAT CODE END -->';	

var JAVASCRIPT_CODE_TEMPLATE='<!-- FOR 123FLASHCHAT CODE BEGIN -->\r\n<script language="javascript" src="#javascript#"></script>\r\n<script language="javascript">\r\nopenSWF("#appSrc#","#width#","#height#");\r\n</script>\r\n<!-- FOR 123FLASHCHAT CODE END -->';	

var FLOAT_WINDOW_CODE_TEMPLATE='<!-- FOR 123FLASHCHAT CODE BEGIN -->\r\n<!-- PLEASE EMBED THE FOLLOWING CODE BETWEEN THE HTML TAG <BODY> AND </BODY> -->\r\n<script language="javascript" src="#javascript#"></script>\r\n<script language="javascript">\r\ninit_url="#appSrc#";\r\ninit_fullscreen_text="#init_fullscreen_text#";\r\ninit_fullscreen_url="#init_fullscreen_url#";\r\ninit_position=#init_position#;\r\ninit_maximize=#init_maximize#;\r\ninit_width="#width#";\r\ninit_height="#height#";\r\nshowFCWindow();\r\n</script>\r\n<!-- FOR 123FLASHCHAT CODE END -->';

var HTML_CHAT_IFRAME_CODE_TEMPLATE='<!-- FOR 123FLASHCHAT CODE BEGIN -->\r\n<iframe border="0" frameborder="0" framespacing="0" width="#width#" height="#height#" marginheight="0" marginwidth="0" name="htmlchat" noResize scrolling="no" src="#appSrc#" vspale="0"></iframe>\r\n<!-- FOR 123FLASHCHAT CODE END -->';

var HTML_CHAT_HYPERLINK_CODE_TEMPLATE='<!-- FOR 123FLASHCHAT CODE BEGIN -->\r\n<a href="#appSrc#">Html Chat</a>\r\n<!-- FOR 123FLASHCHAT CODE END -->';

var HTML_CHAT_DIRECT_LINK_CODE_TEMPLATE='<!-- FOR 123FLASHCHAT CODE BEGIN -->\r\n#appSrc#\r\n<!-- FOR 123FLASHCHAT CODE END -->'; 

var STANDARD_TYPE="0";
var ADMIN_TYPE="1";
var LITE_TYPE="2";
var AVATAR_TYPE="3";
var BANNER_CHAT_TYPE="4";
var PPC_CHAT_TYPE="5";
var HTML_CHAT_TYPE="6";

var BANNER_BLUE_468_60_STYLE="0";
var BANNER_ORANGE_468_60_STYLE="1";
var BANNER_BLUE_728_90_STYLE="2";
var BANNER_ORANGE_728_90_STYLE="3";

var LITE_BLUE_STYLE="0";
var LITE_RED_STYLE="1";
var LITE_BLACK_STYLE="2";
var LITE_GREEN_STYLE="3";
var LITE_YELLOW_STYLE="4";
var LITE_PURPLE_STYLE="5";

var HTML_CHAT_IFRAME_WITH_LOGIN_PAGE_STYLE="0";
var HTML_CHAT_HYPERLINK_WITH_LOGIN_PAGE_STYLE="1";
var HTML_CHAT_DIRECT_LINK_WITH_LOGIN_PAGE_STYLE="2";
var HTML_CHAT_IFRAME_ENTER_ROOM_DIRECTLY_STYLE="3";
var HTML_CHAT_HYPERLINK_ENTER_ROOM_DIRECTLY_STYLE="4";
var HTML_CHAT_DIRECT_LINK_ENTER_ROOM_DIRECTLY_STYLE="5";

var JAVA_SCRIPT="123flashchat.js";
var STANDARD_SWF="123flashchat.swf";
var ADMIN_SWF="admin_123flashchat.swf";
var LITE_SWF="lite.swf";
var AVATAR_SWF="avatarchat.swf";
var BANNER_BLUE_468_60_SWF="banner/lite_blue_468_60.swf";
var BANNER_ORANGE_468_60_SWF="banner/lite_orange_468_60.swf";
var BANNER_BLUE_728_90_SWF="banner/lite_blue_728_90.swf";
var BANNER_ORANGE_728_90_SWF="banner/lite_orange_728_90.swf";
var PPC_176_208_SWF="banner/lite_ppc_176_208.swf";

var HTML_CHAT_LOGIN_PAGE="html-chat-login.html";
var HTML_CHAT_APPLICATION_PAGE="htmlchat/ChatApplication.html";

var STANDARD_WIN_WIDTH=634;
var STANDARD_WIN_HEIGHT=476;
var ADMIN_WIN_WIDTH=825;
var ADMIN_WIN_HEIGHT=600;
var LITE_WIN_WIDTH=202;
var LITE_WIN_HEIGHT=318;
var AVATAR_WIN_WIDTH=800;
var AVATAR_WIN_HEIGHT=600;
var BANNER_BLUE_468_60_WIN_WIDTH=468;
var BANNER_BLUE_468_60_WIN_HEIGHT=60;
var BANNER_ORANGE_468_60_WIN_WIDTH=468;
var BANNER_ORANGE_468_60_WIN_HEIGHT=60;
var BANNER_BLUE_728_90_WIN_WIDTH=728;
var BANNER_BLUE_728_90_WIN_HEIGHT=90;
var BANNER_ORANGE_728_90_WIN_WIDTH=728;
var BANNER_ORANGE_728_90_WIN_HEIGHT=90;
var PPC_176_208_WIN_WIDTH=176;
var PPC_176_208_WIN_HEIGHT=208;
var HTML_CHAT_WIN_WIDTH=634;
var HTML_CHAT_WIN_HEIGHT=476;
var LITE_WINDOW_FLOAT_TYPE=0;
var LITE_WINDOW_FIXED_TYPE=1;
var JAVASCRIPT_CODE_TYPE=0;
var HTML_CODE_TYPE=1;
var FLOAT_WINDOW_TYPE=0;
var FIXED_WINDOW_TYPE=1;

var POSITION_OPTION_ARRAY=new Array("POSITION_TOP_LEFT","POSITION_TOP_RIGHT","POSITION_BOTTOM_LEFT","POSITION_BOTTOM_RIGHT");
var MAXIMIZE_OPTION_ARRAY=new Array("MINIMIZE","MAXIMIZE");

function openFullScreenChat(url)
{
	if (document.all)
	{
		var w = screen.width - window.screenLeft - 10;
		var h = screen.height - window.screenTop;
		var win = window.open(url, "_123FullscreenDemo", "resizable=1, width="+w+",height="+h+",status=1");
		win.moveTo(0,0);
	}
	else
	{
		window.open(url, "_123FullscreenDemo", "resizable=1, fullscreen=1");
	}
}

function openPrivateWin(winId,w,h)
{
	window.open ("privatewin.html?"+winId, "privateMsg_"+winId, "height="+h+", width="+w+", top=0, left=0, toolbar=no, menubar=no, scrollbars=no, resizable=yes,location=no, status=no");
}

function openCustomStandardClient()
{
	var win_width = window.screen.width - 5;
	var win_height = window.screen.height - 10;
	if(document.customForm.customsize[0].checked==true)
	{
			win_width=document.customForm.win_width.value;
			win_height=document.customForm.win_height.value;
	}
	if (win_width < 468)
	{
		alert("width can't be less than 468");
		return;
	}
	if (win_height < 360)
	{
		alert("height can't be less than 360");
		return;	
	}
	var urlStr="123flashchat.html";
	var init_user;
	var init_birth;
	var init_gender;
	var init_location;
	var init_password="";
	var init_room;
	var init_skin=document.customForm.init_skin.value;
	if(init_skin=="Light Blue")
	{
		init_skin="lightblue";
	}
	var init_lang=document.customForm.init_lang[document.customForm.init_lang.selectedIndex].value;
	if(document.customForm.autologin[1].checked==true)
	{
			init_user=document.customForm.init_user.value;
			init_birth=document.customForm.init_birth.value;
			init_gender=document.customForm.init_gender.value;
			init_location=document.customForm.init_location.value;
	}
	if(document.customForm.roomlist[document.customForm.roomlist.selectedIndex].value=="true")
	{
			init_room="1";
	}
	var init_ad=document.customForm.adbanner[document.customForm.adbanner.selectedIndex].value;
	if(init_user!=undefined)
	{
		if(init_user=="")
		{
			init_user="guest";
		}
		urlStr=(urlStr.indexOf("?")==-1)?urlStr+"?init_user="+init_user:urlStr+"&init_user="+init_user;	
		urlStr+="&init_password="+init_password;
	}
	if(init_birth!=undefined)
	{
		urlStr=(urlStr.indexOf("?")==-1)?urlStr+"?init_birth="+init_birth:urlStr+"&init_birth="+init_birth;	
	}
	if(init_gender!=undefined)
	{
		urlStr=(urlStr.indexOf("?")==-1)?urlStr+"?init_gender="+init_gender:urlStr+"&init_gender="+init_gender;	
	}
	if(init_location!=undefined)
	{
		urlStr=(urlStr.indexOf("?")==-1)?urlStr+"?init_location="+init_location:urlStr+"&init_location="+init_location;	
	}
	if(init_room!=undefined)
	{
		urlStr=(urlStr.indexOf("?")==-1)?urlStr+"?init_room="+init_room:urlStr+"&init_room="+init_room;
		if(init_user==undefined)
		{
			init_user="guest";
			urlStr+="&init_user="+init_user;
			urlStr+="&init_password="+init_password;
		}
	}
	if(init_lang!="*")
	{
		urlStr=(urlStr.indexOf("?")==-1)?urlStr+"?init_lang="+init_lang:urlStr+"&init_lang="+init_lang;
	}
	urlStr=(urlStr.indexOf("?")==-1)?urlStr+"?init_skin="+init_skin:urlStr+"&init_skin="+init_skin;
	urlStr+="&init_ad="+init_ad;
	var win = window.open(urlStr, "123flashchat_client"+Math.round(Math.random()*1000), "width=" + win_width + ",height=" + win_height + ",toolbar=no,menubar=no,alwaysRaised=yes,scrollbars=no,resizable=yes,location=no,status=no,alwaysRaised=yes,directories=no,titlebar=no");
}

function getCheckedRadioNumber(radioGroup)
{
	var number=0;
	var radioGroupLength=radioGroup.length;
	for(var i=0;i<radioGroupLength;i++)
	{
		if(radioGroup[i].checked)
		{
			number=i;
			break;
		}
	}
	return number;
}

function changeCheckedRadioNumber(radioGroup,checkedNumber)
{
	var currentCheckedNumber=getCheckedRadioNumber(radioGroup);
	radioGroup[currentCheckedNumber].checked=false;
	radioGroup[checkedNumber].checked=true;
}

function generateEmbedChatCodeFromForm()
{
	var chatType=document.customForm.client_type.value;
	var chatStyle="";
    var embedType=FLOAT_WINDOW_TYPE;
	var position=POSITION_BOTTOM_RIGHT;
	var maximize=MAXIMIZE;
	var init_fullscreen_text="";
	var init_fullscreen_url="";
	if(chatType==BANNER_CHAT_TYPE)
	{
		chatStyle=getCheckedRadioNumber(document.customForm.banner_style);
	}
	else if(chatType==HTML_CHAT_TYPE)
	{
		chatStyle=getCheckedRadioNumber(document.customForm.html_chat_style);
	}
	else if(chatType==LITE_TYPE)
	{
		chatStyle=getCheckedRadioNumber(document.customForm.lite_style);
		embedType=document.customForm.embed_type.value;
		position=POSITION_OPTION_ARRAY[parseInt(document.customForm.float_window_position.value)];
		maximize=MAXIMIZE_OPTION_ARRAY[document.customForm.float_window_checkbox.checked?0:1];
		init_fullscreen_text=document.customForm.full_screen_text.value;
		init_fullscreen_url=document.customForm.full_screen_url.value;
	}
	var init_host=document.customForm.init_host.value;
	if(init_host=="")
	{
		init_host="*";
	}
	var init_port=document.customForm.init_port.value;
	if(init_port=="")
	{
		init_port=51127;
	}
	var win_width = "100%";
	var win_height = "100%";
	if(document.customForm.customsize[0].checked==true)
	{
		win_width=document.customForm.win_width.value;
		win_height=document.customForm.win_height.value;
	}
	var rootUrl=document.customForm.root_url.value;
	var codeType=document.customForm.code_type.value;
	var embedChatCode=generateEmbedChatCode(rootUrl,chatType,chatStyle,init_host,init_port,win_width,win_height,codeType,embedType,position,maximize,init_fullscreen_text,init_fullscreen_url);
	if(embedChatCode!=undefined)
	{
		document.customForm.embedChatCode.value=embedChatCode;
	}
}

function initGenerateEmbedChatCode()
{
		var chatType=document.customForm.client_type.value;
		var codeType=document.customForm.code_type.value;
		document.customForm.root_url.value=ROOT_URL;
	    var embedCode="";
		var winWidth="";
		var winHeight="";
		var displayClientSizeSetting=true;
		var displayLiteChatStyle=false;
		var displayBannerChatStyle=false;
		var displayHtmlChatStyle=false;
		if(chatType==ADMIN_TYPE)
		{
			winWidth=ADMIN_WIN_WIDTH;
			winHeight=ADMIN_WIN_HEIGHT;
			displayClientSizeSetting=false;
			embedCode=generateEmbedChatCode(ROOT_URL,chatType,"","*",51127,ADMIN_WIN_WIDTH,ADMIN_WIN_HEIGHT,codeType);
		}
		else if(chatType==LITE_TYPE)
		{
			var fullScreenText="FullScreen";
			var fullScreenUrl=ROOT_URL+"/"+"standard.html";
			document.customForm.full_screen_text.value=fullScreenText;
			document.customForm.full_screen_url.value=fullScreenUrl;
			displayLiteChatStyle=true;
			winWidth=LITE_WIN_WIDTH;
			winHeight=LITE_WIN_HEIGHT;
			displayClientSizeSetting=false;
			embedCode=generateEmbedChatCode(ROOT_URL,chatType,LITE_BLUE_STYLE,"*",51127,LITE_WIN_WIDTH,LITE_WIN_HEIGHT,codeType,FLOAT_WINDOW_TYPE,POSITION_OPTION_ARRAY[POSITION_BOTTOM_RIGHT-1],MAXIMIZE_OPTION_ARRAY[MAXIMIZE],fullScreenText,fullScreenUrl);
		}
		else if(chatType==AVATAR_TYPE)
		{
			winWidth=AVATAR_WIN_WIDTH;
			winHeight=AVATAR_WIN_HEIGHT;
			embedCode=generateEmbedChatCode(ROOT_URL,chatType,"","*",51127,AVATAR_WIN_WIDTH,AVATAR_WIN_HEIGHT,codeType);
		}
		else if(chatType==BANNER_CHAT_TYPE)
		{
			displayBannerChatStyle=true;
			embedCode=generateEmbedChatCode(ROOT_URL,chatType,BANNER_BLUE_468_60_STYLE,"*",51127,BANNER_BLUE_468_60_WIN_WIDTH,BANNER_BLUE_468_60_WIN_HEIGHT,codeType);
			winWidth=BANNER_BLUE_468_60_WIN_WIDTH;
			winHeight=BANNER_BLUE_468_60_WIN_HEIGHT;
		}
		else if(chatType==PPC_CHAT_TYPE)
		{
			embedCode=generateEmbedChatCode(ROOT_URL,chatType,"","*",51127,PPC_176_208_WIN_WIDTH,PPC_176_208_WIN_HEIGHT,codeType);
			winWidth= PPC_176_208_WIN_WIDTH;
			winHeight= PPC_176_208_WIN_HEIGHT;
		}
		else if(chatType==HTML_CHAT_TYPE)
		{
			displayHtmlChatStyle=true;
			embedCode=generateEmbedChatCode(ROOT_URL,chatType,HTML_CHAT_IFRAME_WITH_LOGIN_PAGE_STYLE,"*",51127,HTML_CHAT_WIN_WIDTH,HTML_CHAT_WIN_HEIGHT,codeType);
			winWidth=HTML_CHAT_WIN_WIDTH;
			winHeight=HTML_CHAT_WIN_HEIGHT;
			displayClientSizeSetting=false;
		}
		else
		{	
			winWidth=STANDARD_WIN_WIDTH;
			winHeight=STANDARD_WIN_HEIGHT;
			displayClientSizeSetting=false;
			embedCode=generateEmbedChatCode(ROOT_URL,chatType,"","*",51127,STANDARD_WIN_WIDTH,STANDARD_WIN_HEIGHT,codeType);
		}
		document.customForm.win_width.value=winWidth;
		document.customForm.win_height.value=winHeight;
		document.customForm.customsize[0].checked=true;
		document.customForm.customsize[1].checked=false;
		document.customForm.client_type[chatType].selected=true;
		document.customForm.embedChatCode.value=embedCode;
		if(displayClientSizeSetting)
		{
			document.getElementById("lwidth").innerHTML="";
			document.getElementById("lheight").innerHTML="";
			document.getElementById("client_size_tr").style.display="none";
		}
		else
		{
			document.getElementById("lwidth").innerHTML="(Minimum: "+winWidth+" pixels)";
			document.getElementById("lheight").innerHTML="(Minimum: "+winHeight+" pixels)";
		   	document.getElementById("client_size_tr").style.display="";
		}
		if(displayLiteChatStyle)
		{
			initLite();
		}
		else
		{
			document.getElementById("lite_style_tr").style.display="none";	
			document.getElementById("full_screen_button_tr").style.display="none";
			document.getElementById("embed_type_tr").style.display="none";
			document.getElementById("float_window_position_tr").style.display="none";
			document.getElementById("float_window_checkbox_tr").style.display="none";
			document.customForm.customsize[1].disabled=""
		}
		if(displayBannerChatStyle)
		{
		   	document.getElementById("banner_style_tr").style.display="";
			changeCheckedRadioNumber(document.customForm.banner_style,BANNER_BLUE_468_60_STYLE);
		}
		else
		{
			document.getElementById("banner_style_tr").style.display="none";
		}
		if(displayHtmlChatStyle)
		{
		   	document.getElementById("html_chat_style_tr").style.display="";
			changeCheckedRadioNumber(document.customForm.html_chat_style,HTML_CHAT_IFRAME_WITH_LOGIN_PAGE_STYLE);
		}
		else
		{
			document.getElementById("html_chat_style_tr").style.display="none";
		}
}

function initLite()
{
	var chatType=document.customForm.client_type.value;
	if(chatType==LITE_TYPE)
	{
		document.getElementById("lite_style_tr").style.display="";
		document.getElementById("full_screen_button_tr").style.display="";
		changeCheckedRadioNumber(document.customForm.lite_style,LITE_BLUE_STYLE);
		var codeType=document.customForm.code_type.value;
		if(codeType==HTML_CODE_TYPE)
		{
			document.getElementById("embed_type_tr").style.display="none";
			document.getElementById("float_window_position_tr").style.display="none";
			document.getElementById("float_window_checkbox_tr").style.display="none";
			document.customForm.customsize[1].disabled=""
		}
		else
		{
			document.getElementById("embed_type_tr").style.display="";
			var liteWindowType=document.customForm.embed_type.value;
			if(liteWindowType==LITE_WINDOW_FLOAT_TYPE)
			{
				document.getElementById("float_window_position_tr").style.display="";
				document.getElementById("float_window_checkbox_tr").style.display="";
				document.customForm.customsize[1].disabled="disabled"
			}
			else
			{
				document.getElementById("float_window_position_tr").style.display="none";
				document.getElementById("float_window_checkbox_tr").style.display="none";
				document.customForm.customsize[1].disabled=""
			}
		}
	}
}

function changeBannerChatSize()
{			
		var chatStyle=getCheckedRadioNumber(document.customForm.banner_style);
		var winWidth="";
		var winHeight="";
		if(chatStyle==BANNER_BLUE_468_60_STYLE)
		{
			winWidth=BANNER_BLUE_468_60_WIN_WIDTH;
			winHeight=BANNER_BLUE_468_60_WIN_HEIGHT;
		}
		else if(chatStyle==BANNER_ORANGE_468_60_STYLE)
		{
			winWidth=BANNER_ORANGE_468_60_WIN_WIDTH;
			winHeight=BANNER_ORANGE_468_60_WIN_HEIGHT;
		}
		else if(chatStyle==BANNER_BLUE_728_90_STYLE)
		{
			winWidth=BANNER_BLUE_728_90_WIN_WIDTH;
			winHeight=BANNER_BLUE_728_90_WIN_HEIGHT;
		}
		else if(chatStyle==BANNER_ORANGE_728_90_STYLE)
		{
			winWidth=BANNER_ORANGE_728_90_WIN_WIDTH;
			winHeight=BANNER_ORANGE_728_90_WIN_HEIGHT;
		}
		document.customForm.win_width.value=winWidth;
		document.customForm.win_height.value=winHeight;
}

function changeHtmlChat()
{
	var chatStyle=getCheckedRadioNumber(document.customForm.html_chat_style);
	var winWidth="";
	var winHeight="";
	if(chatStyle==HTML_CHAT_IFRAME_WITH_LOGIN_PAGE_STYLE||chatStyle==HTML_CHAT_IFRAME_ENTER_ROOM_DIRECTLY_STYLE)
	{
		 document.getElementById("client_size_tr").style.display="";
	}
	else
	{
		document.getElementById("client_size_tr").style.display="none";
	}
}

function isValidPortValue(init_port)
{
		if(!(init_port>0&&init_port<=65535))
		{
			alert("Invilid chat server port value, port value must great than 0 and less than 65536");
			return false;	
		}
		else
		{
			return true;
		}
}

function generateEmbedChatCode(rootUrl,chatType,chatStyle,init_host,init_port,win_width,win_height,codeType,embedType,position,maximize,init_fullscreen_text,init_fullscreen_url)
{
	var embedChatCode;
	if(codeType==HTML_CODE_TYPE)
	{
		 embedChatCode=HTML_CODE_TEMPLATE;
	}
	else
	{
		embedChatCode=JAVASCRIPT_CODE_TEMPLATE;
	}
	var rootUrlLength=rootUrl.length;
	var webServerUrl="";
	var init_skin="";
	if(rootUrlLength>0)
	{
			webServerUrl=rootUrl;
			var lastIndexOfSlash=rootUrl.lastIndexOf('/');
			if((rootUrlLength-1)!=lastIndexOfSlash)
			{
				webServerUrl=	webServerUrl+'/';
			}
	}
	var javascriptUrl=webServerUrl+JAVA_SCRIPT;
	var appSrc=STANDARD_SWF;
	if(chatType==ADMIN_TYPE)
	{
		appSrc=ADMIN_SWF;
		if (win_width < 825)
		{
				alert("width can't be less than 825");
				return;
		}
		if (win_height < 600)
		{
				alert("height can't be less than 600");
				return;
		}
		if(!isValidPortValue(init_port))
		{
			return;
		}
	}
	else if(chatType==LITE_TYPE)
	{
		appSrc=LITE_SWF;
		if (win_width < 202)
		{
				alert("width can't be less than 202");
				return;
		}
		if (win_height < 318)
		{
				alert("height can't be less than 318");
				return;
		}
		if(!isValidPortValue(init_port))
		{
			return;
		}
		if(chatStyle==LITE_BLUE_STYLE)
		{
			init_skin="blue";
		}
		else if(chatStyle==LITE_RED_STYLE)
		{
			init_skin="red"
		}
		else if(chatStyle==LITE_BLACK_STYLE)
		{
			init_skin="black";
		}
		else if(chatStyle==LITE_GREEN_STYLE)
		{
			init_skin="green";
		}
		else if(chatStyle==LITE_YELLOW_STYLE)
		{
			init_skin="yellow";
		}
		else if(chatStyle==LITE_PURPLE_STYLE)
		{
			init_skin="purple";
		}
		if(codeType==JAVASCRIPT_CODE_TYPE&&embedType==FLOAT_WINDOW_TYPE)
		{
			embedChatCode=FLOAT_WINDOW_CODE_TEMPLATE;
		}
	}
	else if(chatType==AVATAR_TYPE)
	{
		appSrc=AVATAR_SWF;
		if (win_width != 800)
		{
				alert("width must be equals to 800");
				return;
		}
		if (win_height != 600)
		{
				alert("width must be equals to 600");
				return;
		}
		if(!isValidPortValue(init_port))
		{
			return;
		}
	}
	else if(chatType==BANNER_CHAT_TYPE)
	{
		if(chatStyle==BANNER_BLUE_468_60_STYLE)
		{
			appSrc=BANNER_BLUE_468_60_SWF;
			if (win_width != 468)
			{
					alert("width must be equals to 468");
					return;
			}
			if (win_height != 60)
			{
					alert("height must be equals to 60");
					return;	
			}
			if(!isValidPortValue(init_port))
			{
				return;	
			}
		}
		else if(chatStyle==BANNER_ORANGE_468_60_STYLE)
		{
			appSrc=BANNER_ORANGE_468_60_SWF;
			if (win_width != 468)
			{
					alert("width must be equals to 468");
					return;
			}
			if (win_height != 60)
			{
					alert("height must be equals to 60");
					return;	
			}
			if(!isValidPortValue(init_port))
			{
				return;	
			}
		}
		else if(chatStyle==BANNER_BLUE_728_90_STYLE)
		{
			appSrc=BANNER_BLUE_728_90_SWF;
			if (win_width != 728)
			{
					alert("width must be equals to 728");
					return;
			}
			if (win_height != 90)
			{
					alert("height must be equals to 90");
					return;	
			}
			if(!isValidPortValue(init_port))
			{
				return;	
			}
		}
		else if(chatStyle==BANNER_ORANGE_728_90_STYLE)
		{
			appSrc=BANNER_ORANGE_728_90_SWF;
			if (win_width != 728)
			{
					alert("width must be equals to 728");
					return;
			}
			if (win_height != 90)
			{
					alert("height must be equals to 90");
					return;	
			}
			if(!isValidPortValue(init_port))
			{
				return;	
			}
		}
	}
	else if(chatType==PPC_CHAT_TYPE)
	{
			appSrc=PPC_176_208_SWF;		
			if (win_width != 176)
			{
					alert("width must be equals to 176");
					return;
			}
			if (win_height != 208)
			{
					alert("height must be equals to 208");
					return;
			}
			if(!isValidPortValue(init_port))
			{
				return;
			}
	}
	else if(chatType==HTML_CHAT_TYPE)
	{
		if (win_width < 1)
		{
			alert("width can't be less than 1");
			return;
		}
		else if (win_height < 1)
		{
			alert("height can't be less than 1");
			return;
		}
		else if(!isValidPortValue(init_port))
		{
			return;
		}
		if(chatStyle==HTML_CHAT_IFRAME_WITH_LOGIN_PAGE_STYLE)
		{
			embedChatCode=HTML_CHAT_IFRAME_CODE_TEMPLATE;
			appSrc=HTML_CHAT_LOGIN_PAGE;
		}
		else if(chatStyle==HTML_CHAT_HYPERLINK_WITH_LOGIN_PAGE_STYLE)
		{
			embedChatCode=HTML_CHAT_HYPERLINK_CODE_TEMPLATE;
			appSrc=HTML_CHAT_LOGIN_PAGE;
		}
		else if(chatStyle==HTML_CHAT_DIRECT_LINK_WITH_LOGIN_PAGE_STYLE)
		{
			embedChatCode=HTML_CHAT_DIRECT_LINK_CODE_TEMPLATE;
			appSrc=HTML_CHAT_LOGIN_PAGE;
		}
		else if(chatStyle==HTML_CHAT_IFRAME_ENTER_ROOM_DIRECTLY_STYLE)
		{
			embedChatCode=HTML_CHAT_IFRAME_CODE_TEMPLATE;
			appSrc=HTML_CHAT_APPLICATION_PAGE;
		}
		else if(chatStyle==HTML_CHAT_HYPERLINK_ENTER_ROOM_DIRECTLY_STYLE)
		{
			embedChatCode=HTML_CHAT_HYPERLINK_CODE_TEMPLATE;
			appSrc=HTML_CHAT_APPLICATION_PAGE;
		}
		else if(chatStyle==HTML_CHAT_DIRECT_LINK_ENTER_ROOM_DIRECTLY_STYLE)
		{
			embedChatCode=HTML_CHAT_DIRECT_LINK_CODE_TEMPLATE;
			appSrc=HTML_CHAT_APPLICATION_PAGE;
		}
	}
	else
	{
		appSrc=STANDARD_SWF;
		if (win_width < 468)
		{
				alert("width can't be less than 468");
				return;
		}
		if (win_height < 360)
		{
				alert("height can't be less than 360");
				return;
		}
		if(!isValidPortValue(init_port))
		{
			return;
		}
	}
	if(init_host!="*")
	{
		appSrc=(appSrc.indexOf("?")==-1)?appSrc+"?init_host="+init_host:appSrc+"&init_host="+init_host;
	}
	if(init_port!=51127)
	{
		appSrc=(appSrc.indexOf("?")==-1)?appSrc+"?init_port="+init_port:appSrc+"&init_port="+init_port;
	}
	if(init_skin!="")
	{
		appSrc=(appSrc.indexOf("?")==-1)?appSrc+"?init_skin="+init_skin:appSrc+"&init_skin="+init_skin;
	}
	if(chatType==LITE_TYPE)
	{
		if(codeType==JAVASCRIPT_CODE_TYPE&&embedType==FLOAT_WINDOW_TYPE)
		{
			appSrc=(appSrc.indexOf("?")==-1)?appSrc+"?show_quit=1":appSrc+"&show_quit=1";
			if(maximize==MAXIMIZE_OPTION_ARRAY[MAXIMIZE])
			{
				appSrc=(appSrc.indexOf("?")==-1)?appSrc+"?isbig=1":appSrc+"&isbig=1";
			}
			else
			{
				appSrc=(appSrc.indexOf("?")==-1)?appSrc+"?isbig=0":appSrc+"&isbig=0";
			}
		}
		else
		{
			if(init_fullscreen_text!="")
			{
				appSrc=(appSrc.indexOf("?")==-1)?appSrc+"?init_fullscreen_text="+init_fullscreen_text:appSrc+"&init_fullscreen_text="+init_fullscreen_text;
			}
			if(init_fullscreen_url!="")
			{
				appSrc=(appSrc.indexOf("?")==-1)?appSrc+"?init_fullscreen_url="+init_fullscreen_url:appSrc+"&init_fullscreen_url="+init_fullscreen_url;
			}
		}
	}
	appSrc=webServerUrl+appSrc;
	while(embedChatCode.indexOf("#javascript#")!=-1)
	{
		embedChatCode=embedChatCode.replace("#javascript#",javascriptUrl);
	}
	while(embedChatCode.indexOf("#appSrc#")!=-1)
	{
		embedChatCode=embedChatCode.replace("#appSrc#",appSrc);
	}
	while(embedChatCode.indexOf("#width#")!=-1)
	{
		embedChatCode=embedChatCode.replace("#width#",win_width);
	}
	while(embedChatCode.indexOf("#height#")!=-1)
	{
		embedChatCode=embedChatCode.replace("#height#",win_height);
	}
	while(embedChatCode.indexOf("#init_position#")!=-1)
	{
		embedChatCode=embedChatCode.replace("#init_position#",position);
	}
	while(embedChatCode.indexOf("#init_maximize#")!=-1)
	{
		embedChatCode=embedChatCode.replace("#init_maximize#",maximize);
	}
	while(embedChatCode.indexOf("#init_fullscreen_text#")!=-1)
	{
		embedChatCode=embedChatCode.replace("#init_fullscreen_text#",init_fullscreen_text);
	}
	while(embedChatCode.indexOf("#init_fullscreen_url#")!=-1)
	{
		embedChatCode=embedChatCode.replace("#init_fullscreen_url#",init_fullscreen_url);
	}
	return embedChatCode;
}
var windowHTML= "";
function preview()
{		
	windowHTML= document.customForm.embedChatCode.value;
	//var wh="width="+document.customForm.win_width.value+",height="+document.customForm.win_height.value;
	//var newWindow = window.open('preview.html','Preview',wh);
	var newWindow=window.open("preview.html","Preview");
	if (newWindow != null)
	{
		newWindow.focus();
	}
}

jQuery(document).ready(function(){
	jQuery(".border").click(function(){
		  var skinName=$(this).next().text().toLowerCase();
		  if(skinName=="light blue")
		  {
		  	skinName="lightblue";
		  }
			var img_url = 'skinimg/'+skinName + '.jpg';
			$("#init_skin").val($(this).next().text());	
			$("#show_screenshot").attr('src',img_url);
	});
});

	
/****************************some methods used for demo page end*************************************/