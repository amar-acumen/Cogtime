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
			connection.query('(SELECT COUNT(id) AS countrow,"notification" as item_type from cg_notifications WHERE i_accepter_id ="'+url_parts.query.user+'" AND i_notification_shown=1) ', 
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


