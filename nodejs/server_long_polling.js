var url = require('url');
var http = require('http');
var mysql      = require('mysql');
var pool = mysql.createPool({
				  host     : '103.227.62.106',
				  user     : 'acumen',
				  password : 'eWvo456&',
				  database : 'admin_cogtime'
});


function processRequest(url_parts,res,cnt)
{
	
	pool.getConnection(function (err, connection) {
		if(err)
		{
			console.log(err);
			res.writeHead(200, {'Content-Type': 'text/plain'});
			res.end("setUpdateStatus('error');");
		}
		else
		{
			connection.query(	'(SELECT COUNT(*) AS countrow,"notification" as item_type from cg_notifications WHERE i_accepter_id ="'+url_parts.query.user+'" AND i_notification_shown=1) UNION (SELECT COUNT(*) AS countrow,"prayer_partner" as item_type FROM cg_prayer_partner WHERE i_accepter_id="'+url_parts.query.user+'" AND s_status = "pending")UNION (SELECT COUNT(*) AS countrow,"net_pal" as item_type FROM cg_users_net_pal_contacts WHERE i_accepter_id="'+url_parts.query.user+'" AND `s_status` = "pending")UNION (SELECT COUNT(*) AS countrow,"friends" as item_type FROM cg_user_contacts WHERE i_accepter_id="'+url_parts.query.user+'"  AND `s_status` = "pending") UNION (SELECT COUNT(*) AS countrow,"events" as item_type FROM cg_events  WHERE i_host_id = "'+url_parts.query.user+'" AND i_status = 1 AND i_user_type = 1 ) UNION (SELECT COUNT(*) AS countrow,"organizer" as item_type FROM cg_organizer_to_do_list  WHERE i_user_id ="'+url_parts.query.user+'") UNION (select COUNT(*) AS countrow ,"chat" as item_type from cg_im_chat where (cg_im_chat.to = "'+url_parts.query.displayuser+'" AND cg_im_chat.to_id = "'+url_parts.query.user+'" AND recd = 0)) UNION (SELECT COUNT(*) prayer_group FROM ((SELECT n.id FROM cg_prayer_group_notifications n LEFT JOIN cg_prayer_group pg ON pg.id = n.i_prayer_group_id LEFT JOIN cg_users u ON pg.i_owner_id = u.id LEFT JOIN cg_prayer_group_members pg_mem ON pg_mem.i_user_id = n.i_requester_user_id WHERE u.i_status="1" AND u.i_isdeleted ="1" AND pg.i_isenabled = 1 AND pg.i_owner_id = "'+url_parts.query.user+'" AND pg.i_owner_id = u.id AND n.s_type != "invited" AND n.s_type = "joining_req" AND pg_mem.s_status = "pending" AND pg_mem.i_prayer_group_id = pg.id) UNION (SELECT n.id FROM cg_prayer_group_notifications n LEFT JOIN cg_prayer_group pg ON pg.id = n.i_prayer_group_id LEFT JOIN cg_users u ON n.i_user_id = u.id LEFT JOIN cg_prayer_group_members pg_mem ON pg_mem.i_user_id = u.id WHERE u.i_status="1" AND u.i_isdeleted ="1" AND pg.i_isenabled = 1 AND n.i_user_id = "'+url_parts.query.user+'" AND n.s_type = "invited" AND n.s_type != "joining_req" AND pg_mem.s_status = "pending"AND pg_mem.i_prayer_group_id = pg.id)) derived_tbl) UNION (SELECT COUNT(*) AS ring_notification FROM cg_users u , cg_ring_invited_user AS r LEFT JOIN cg_user_ring rg ON r.i_ring_id = rg.id WHERE r.i_invited_id=u.id AND r.i_request=1 AND r.i_joined=0 AND rg.i_user_id = "'+url_parts.query.user+'") UNION (SELECT COUNT(*) AS ring_inv_notification FROM cg_ring_invited_user AS r LEFT JOIN cg_user_ring rg ON r.i_ring_id = rg.id LEFT JOIN cg_users u ON rg.i_user_id=u.id WHERE r.i_request=0 AND r.i_joined=0 AND r.i_invited_id="'+url_parts.query.user+'")', 
								function(err, rows, fields) {
									connection.release();
									if (err) 
									{
										console.log(err);
										res.writeHead(200, {'Content-Type': 'text/plain'});
										res.end("setUpdateStatus('error');");
									}
									else
									{
										var nonZeroFound = false;
										for(var i=0;i<rows.length;i++)
										{
											if(rows[i].countrow*1>0 
												&& rows[i].item_type!='events' && rows[i].item_type!='organizer' 
											)
											{
												nonZeroFound = true;
											}
										}
										var cnt_new = (new Date()).getTime();
										if(nonZeroFound)
										{
											console.log('1');
											res.writeHead(200, {'Content-Type': 'text/plain'});
											res.end("setUpdateStatus('"+JSON.stringify(rows)+"');");
										}
										else if(cnt_new>=cnt)
										{
											res.writeHead(200, {'Content-Type': 'text/plain'});
											res.end("setUpdateStatus('no change');");
										}
										else
										{
											setTimeout(function(){
												processRequest(url_parts,res,cnt);
											},1000);
										}
									}
								});
		}
	});

	
}
http.createServer(function(req, res) {
	
	var url_parts = url.parse(req.url, true);
	var cnt = (new Date()).getTime()+10000;
	processRequest(url_parts,res,cnt);
	
	
}).listen(2337,'web.acumensofttech.com');


