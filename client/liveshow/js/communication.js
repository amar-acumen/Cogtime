
var HOST="127.0.0.1";var PORT=35555;var MY_PUBKEY_STR;var MY_HASH_KEY;var MY_PUBKEY;function openIntroSWF(url){var so=new SWFObject(url,"topcmm_flashchat","100%","100%","8","#FFFFFF");so.addParam("quality","high");so.addParam("wmode","transparent");so.addParam("allowScriptAccess","always");so.write("div_flashchat");}
if(!isSupportFlash()){var my_domain="";if(my_domain==undefined||my_domain==""){my_domain=getDomain();}
document.domain=my_domain;}
function isIP(ipStr){var result=false;if(ipStr.length>0){try{var temp=ipStr.split(".");if(temp.length!=4){result=false;}
else{for(var i=0;i<temp.length;i++){if(!isIntegerInRange(temp[i],0,255)){result=false;break;}}
result=true;}}
catch(e){result=false;}}
else{result=false;}
return result;}
function isIntegerInRange(s,a,b){if(isEmpty(s))
if(isIntegerInRange.arguments.length==1)
return false;else
return(isIntegerInRange.arguments[1]==true);if(!isInteger(s,false))
return false;var num=parseInt(s);return((num>=a)&&(num<=b));}
function isInteger(s){var i;if(isEmpty(s))
if(isInteger.arguments.length==1)
return 0;else
return(isInteger.arguments[1]==true);for(var i=0;i<s.length;i++){var c=s.charAt(i);if(!isDigit(c))
return false;}
return true;}
function isEmpty(s){return((s==null)||(s.length==0))}
function isDigit(c){return((c>="0")&&(c<="9"))}
function getDomain(){var COLON=":";var DOT=".";var domainStr="";var hostStr="";try{if(http_root_123flashchat.length!=0&&http_root_123flashchat.indexOf("http://")!=-1){hostStr=http_root_123flashchat.substring("http://".length,http_root_123flashchat.length);}
else{hostStr=window.location.host;}
domainStr=hostStr.indexOf(COLON)==-1?hostStr:hostStr.substring(0,hostStr.indexOf(COLON));domainStr=domainStr.indexOf("/")==-1?domainStr:domainStr.substring(0,domainStr.indexOf("/"));if(!isIP(domainStr)){var domainArray=domainStr.split(".");}}
catch(e){domainStr="";}
return domainStr;}
function getMovie(){try{var movieName="topcmm_flashchat";if(isIE()){return window[movieName];}
else{return document[movieName];}}
catch(e){return undefined;}}
function isIE(){return getUserAgent().indexOf("msie")!=-1;}
function getLanguage(){if(isIE()){return navigator.browserLanguage;}
else{return navigator.language;}}
function isMobile(){var userAgentStr=getUserAgent();return(userAgentStr.indexOf("iphone")!=-1||userAgentStr.indexOf("android")!=-1||userAgentStr.indexOf("msiemobile")!=-1);}
function getUserAgent(){return navigator.userAgent.toLowerCase();}
function isSupportFlash(){var result=false;try{if(navigator.plugins&&navigator.plugins.length>0){var swf=navigator.plugins["Shockwave Flash"];if(swf){result=true;}}
if(!result){var swf=new ActiveXObject('ShockwaveFlash.ShockwaveFlash');if(swf){result=true;}}}
catch(e){result=false;}
return result;}
function flashConnect(host,port){var swfObj=getMovie();if(swfObj==undefined||swfObj.connect==undefined){return false;}
return swfObj.connect(host,port);}
function flashSendMessage(msg){var swfObj=getMovie();swfObj.sendMessage(msg);}
function httpConnect(host,port){HOST=host;PORT=port;onConnected();return true;}
function httpSendMessage(msg){message=msg+"0";message=encodeURIComponent(encodeURIComponent(message));var randomNumber=Math.floor(Math.random()*999999999999);var url="http://"+HOST+":"+PORT+"/123flashchat_"+randomNumber;var parameter="protocol="+message;if(message.indexOf("%253CInit")==0){J(url,parameter,"l");}
else{J(url,parameter,"s"+randomNumber);}}
function c(z){var v=null;var o=z;var j=z+'_div';var w='border-width:0;height:0;width:0;visibility:hidden;';var k='position:absolute;top:0;left:0;width:0;height:0;overflow:hidden;';if(document.getElementById(o)){return(document.getElementById(o));}
try{var C=document.createElement("iframe");C.setAttribute("id",o);C.setAttribute("name",o);C.setAttribute("style",w);var B=document.createElement("div");B.setAttribute("id",j);B.setAttribute("style",k);B.appendChild(C);document.body.appendChild(B);if(typeof document.frames!="undefined"){v=document.frames[o];}
if(!v||typeof v.nodeType=="undefined"){v=document.getElementById(o);}}
catch(e){var n='<iframe id="'+o+'" name="'+o+'" style="'+w+'"><\/iframe>';if(!document.getElementById(j)){B=document.createElement("div");B.setAttribute('id',j);B.setAttribute('style',k);B.innerHTML=n;document.getElementsByTagName('DIV')[0].appendChild(B);}
else{document.getElementById(j).innerHTML=n;}
v=document.getElementById(o);}
return v;}
function J(url,f,r){try{var g=url+"?"+f;var v=c(r);var contentDocument=d(v);}
catch(l){}
try{if(!contentDocument.location){contentDocument.location=g;}
else{contentDocument.location.replace(g);}}
catch(l){v.src=g;}}
function d(A){if(A.contentDocument){return(A.contentDocument);}
else
if(A.contentWindow){return(A.contentWindow.document);}
else
if(A.document){return(A.document);}
else{return(undefined);}}
function I(i){receiveMessage(i);}
function setPublicKey(keyCode,seqid,sid){MY_PUBKEY_STR=keyCode;MY_HASH_KEY=genFCKey();MY_PUBKEY=new RSAKeyPair("11",MY_PUBKEY_STR);var sk=encryptedString(MY_PUBKEY,MY_HASH_KEY);var skXML='<Suhk k="'+sk+'"';if(seqid!=null&&seqid!=undefined&&seqid!=""){skXML+=' msqid="'+seqid+'"'}
skXML+=' sid="'+sid+'"'
skXML+='></Suhk>';if(isMobile()){httpSendMessage(skXML);}
else{flashSendMessage(skXML);}}
function encryptPassword(pwd){return encryptedString(MY_PUBKEY,pwd);}
function encryptPasswordByPublicKey(pwd,publicKeyStr){var publicKey=new RSAKeyPair("11",publicKeyStr)
return encryptedString(publicKey,pwd);}
function encodeMessage(msg,key){if((key=="")||(key==undefined)){return fcEncode(MY_HASH_KEY,msg);}
else{return fcEncode(key,msg);}}
function decodeMessage(msg,roomKeyCode){var res="";if((roomKeyCode=="")||(roomKeyCode==undefined)){res=fcDecode(MY_HASH_KEY,msg);}
else{res=fcDecode(roomKeyCode,msg);}
return res.split("\\").join("\\\\");}
function encryptWithRsaKeyPair(exponent,module,str){return getEncryptedMsgWithRsaKeyPair(exponent,module,str);}
function getEncryptedMsgWithRsaKeyPair(exponent,module,str){var key=new RSAKeyPair(exponent,module);return encryptedString(key,str);}