var url = require('url');
var http = require('http');
var mysql      = require('mysql');

http.createServer(function (req, res) {


	var url_parts = url.parse(req.url, true);
	console.log(url_parts);
	var tmp = function(cnt,req1, res1){
				var connection = mysql.createConnection({
				  host     : '103.227.62.106',
				  user     : 'acumen',
				  password : 'eWvo456&',
				  database : 'admin_cogtime'
				});
				connection.connect();
					
				connection.query('(SELECT COUNT(*) AS countrow,"notification" as item_type from cg_notifications WHERE i_accepter_id ="'+url_parts.query.user+'" AND i_notification_shown=1) UNION (SELECT COUNT(*) AS countrow,"prayer_partner" as item_type FROM cg_prayer_partner WHERE i_accepter_id="'+url_parts.query.user+'" AND s_status = "pending")UNION (SELECT COUNT(*) AS countrow,"net_pal" as item_type FROM cg_users_net_pal_contacts WHERE i_accepter_id="'+url_parts.query.user+'" AND `s_status` = "pending")UNION (SELECT COUNT(*) AS countrow,"friends" as item_type FROM cg_user_contacts WHERE i_accepter_id="'+url_parts.query.user+'"  AND `s_status` = "pending") UNION (SELECT COUNT(*) AS countrow,"events" as item_type FROM cg_events  WHERE i_host_id = "'+url_parts.query.user+'" AND i_status = 1 AND i_user_type = 1 ) UNION (SELECT COUNT(*) AS countrow,"organizer" as item_type FROM cg_organizer_to_do_list  WHERE i_user_id ="'+url_parts.query.user+'") UNION (select COUNT(*) AS countrow ,"chat" as item_type from cg_im_chat where (cg_im_chat.to = "'+url_parts.query.displayuser+'" AND cg_im_chat.to_id = "'+url_parts.query.user+'" AND recd = 0))', 
					function(err, rows, fields) {
					if (!err)
					{
						
						//console.log(typeof(rows)+'sdsds');
						//var statusList = {"totalRecords": 4 };
						//res.write(JSON.stringify(statusList, 0, 4));

						var nonZeroFound = false;
						for(var i=0;i<rows.length;i++)
						{
							if(rows[i].countrow*1>0 
								&& rows[i].item_type!='events' && rows[i].item_type!='organizer' 
							)
								nonZeroFound = true;
						}
						/*if(nonZeroFound || cnt<=0)
						{
							
						}
						else 
						{
							setTimeout(function(){
								cnt--;
								tmp(cnt,req1,res1);
								},1000);
						}*/
						res.writeHead(200, {'Content-Type': 'text/plain'});
						res.end("setUpdateStatus('"+JSON.stringify(rows)+"')");
					}
					else 
					{
							res.writeHead(200, {'Content-Type': 'text/plain'});
							res.end('setUpdateStatus("error")');
					}
				});

				connection.end();
   }
   tmp(1,req,res);
  
}).listen(1337, 'web.acumensofttech.com',200);
console.log('Server running at http://127.0.0.1:1337/');
