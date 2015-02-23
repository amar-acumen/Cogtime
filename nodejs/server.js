var url = require('url');
var http = require('http');
var mysql      = require('mysql');

http.createServer(function (req, res) {

	var connection = mysql.createConnection({
	  host     : 'localhost',
	  user     : 'root',
	  password : '',
	  database : 'cogtime'
	});

	var url_parts = url.parse(req.url, true);
	console.log(url_parts);

	connection.connect();
	
		
	connection.query('(SELECT COUNT(*) AS countrow,"notification" as item_type from cg_notifications WHERE i_accepter_id ="'+url_parts.query.user+'" AND i_notification_shown=1) UNION (SELECT COUNT(*) AS countrow,"prayer_partner" as item_type FROM cg_prayer_partner WHERE i_accepter_id="'+url_parts.query.user+'" AND s_status = "pending")UNION (SELECT COUNT(*) AS countrow,"net_pal" as item_type FROM cg_users_net_pal_contacts WHERE i_accepter_id="'+url_parts.query.user+'" AND `s_status` = "pending")UNION (SELECT COUNT(*) AS countrow,"friends" as item_type FROM cg_user_contacts WHERE i_accepter_id="'+url_parts.query.user+'"  AND `s_status` = "pending") UNION (SELECT COUNT(*) AS countrow,"events" as item_type FROM cg_events  WHERE i_host_id = "'+url_parts.query.user+'" AND i_status = 1 AND i_user_type = 1 ) UNION (SELECT COUNT(*) AS countrow,"organizer" as item_type FROM cg_organizer_to_do_list  WHERE i_user_id ="'+url_parts.query.user+'") UNION (select COUNT(*) AS countrow ,"chat" as item_type from cg_im_chat where (cg_im_chat.to = "'+url_parts.query.displayuser+'" AND cg_im_chat.to_id = "'+url_parts.query.user+'" AND recd = 0))', 
		function(err, rows, fields) {
		if (!err)
		{
			res.writeHead(200, {'Content-Type': 'application/json'});
			console.log(typeof(rows)+'sdsds');
			//var statusList = {"totalRecords": 4 };
			//res.write(JSON.stringify(statusList, 0, 4));
			res.end("setUpdateStatus('"+JSON.stringify(rows)+"')");
			//res.end('setUpdateStatus('+JSON.stringify(statusList)+')');
		}
		else 
		{
				res.end('setUpdateStatus("error","'+err+'")');
		}
	});

	connection.end();
   
   
  
}).listen(3000, '192.168.1.34');
console.log('Server running at http://127.0.0.1:1337/');