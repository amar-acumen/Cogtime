/*
 * For php:
 * Just as the example var getSeatURL = "http://localhost/seat/entrance.php";
 * You maybe need to change the localhost to you php server and the port like php_server:php_port
 * And you need to change seat/server/entrance.php to you entrance.php file path under php server.
 * If you php server root fold is seat, and tht entrance.php is under that fold, you should change it to 
 * "http://php_server:php_port/seat/entrance.php"
 * 
 * And alse need to change var updateSeatURL = "http://localhost/seat/getseat.php"; That is just like bellow.
 * 
 * For tomcat:
 * You just need change localhost:8080 to your (ip<you tomcat ip>:port<your tomcat port>).
 * */

var hostName=window.location.hostname;
//for jsp
var getSeatURL = "http://"+hostName+":28080/123flashchat_plugins/servlet/Entrance";
var updateSeatURL = "http://"+hostName+":28080/123flashchat_plugins/servlet/GetSeat";

/**for php*/
//var getSeatURL = "http://"+hostName+"/seat/entrance.php";
//var updateSeatURL = "http://"+hostName+"/seat/getseat.php";
