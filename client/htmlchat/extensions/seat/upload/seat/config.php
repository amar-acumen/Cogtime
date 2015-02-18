<?php


/*
For example of the "http://localhost:35555/update-user-credits.api";
http://localhost:35555 is the chat server, you may be need to modify the localhost to you chat server ip.
And alse need change 35555 to the server port;

change "http://localhost:35555/custommessage.api" just like follows

To important, open the server.xml under CHAT_ROOT\server\etc\groups\default, find tag just like follows 
	<data-api enable="On"> 
		<query-allow-access-from password=""> 
			<ip>*</ip> 
		</query-allow-access-from> 
		<push-allow-access-from password=""> 
			<ip>*</ip> 
		</push-allow-access-from> 
	</data-api>

the data-api should be enabled, and the <ip> under  query-allow-access-from need add you domain without limited
<ip>*</ip> is not so scurity, you could modify like this <ip>127.0.0.1</ip>

*/



$updateCreditsAPI = "http://localhost:35555/update-user-credits.api";
$customMessageAPI = "http://localhost:35555/custommessage.api";

?>