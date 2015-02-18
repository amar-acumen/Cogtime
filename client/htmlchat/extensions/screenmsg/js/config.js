/*
 * For php:
 * Just as the example var flashscreen = "http://192.168.0.222:80/screenmsg/server/flashscreen.php"; 
 * You maybe need to change the ip 192.168.0.222:80 to you php server and the port like php_server/php_port
 * And you need to change screenmsg/server/flashscreen.php to you flashscreen.php file path under php server.
 * If you php server root fold is screenmsg, and tht flasscreen.php is under that fold, you should change it to 
 * "http://ip:port/screenmsg/flashscreen.php"
 * 
 * For tomcat:
 * You just need change localhost:8080 to your (ip<you tomcat ip>:port<your tomcat port>).
 * */
var hostName=window.location.hostname;
var flashscreen = "http://"+hostName+":28080/123flashchat_plugins/servlet/ScreenMsg";            //for jsp
//var flashscreen = "http://"+hostName+"/flashscreen/flashscreen.php";        //for php
