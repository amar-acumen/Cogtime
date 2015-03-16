/* ==== show blockUI message ==== */
//function showUIMsgRsvp(msg)
//{ 
//	$.blockUI({
//		message: msg,
//		css: {
//				border: 'none',
//				padding: '15px',
//				fontSize: '12px',
//				backgroundColor: '#000000',
//				'-webkit-border-radius': '10px',
//				'-moz-border-radius': '10px',
//				opacity: '1',
//				color: '#ffffff'
//		},
//		overlayCSS: { backgroundColor: '#000000' }
//	});
//
//	setTimeout($.unblockUI, 4000);
//}

function showUIMsg(msg)
{ 
	$.blockUI({
		message: msg,
		css: {
				border: 'none',
				padding: '15px',
				fontSize: '12px',
				backgroundColor: '#000000',
				'-webkit-border-radius': '10px',
				'-moz-border-radius': '10px',
				opacity: '1',
				color: '#ffffff'
		},
		overlayCSS: { backgroundColor: '#000000' }
	});

	setTimeout($.unblockUI, 5000);
}

function showUIMsgFriend(msg)
{ 
	$.blockUI({
		message: msg,
		css: {
				border: 'none',
				padding: '15px',
				fontSize: '12px',
				backgroundColor: '#000000',
				'-webkit-border-radius': '10px',
				'-moz-border-radius': '10px',
				opacity: '1',
				color: '#ffffff'
		},
		overlayCSS: { backgroundColor: '#000000' }
	});

	setTimeout($.unblockUI, 5000);
}

/// global;
NO_ZINDEX = false;


function showSub(obj)
{//console.log($(obj).next().next());
	$(obj).next().next().slideToggle();
}

function checkAllInPrivacy(flag)
{
	if(flag=='frnd')
	{
		$( "input[name^='frnds']" ).attr('checked','checked');
	}
	else if(flag=='netpal')
	{
		$( "input[name^='netpal']" ).attr('checked','checked');
	}
	else if(flag=='pp')
	{
		$( "input[name^='pp']" ).attr('checked','checked');
	}
	else if(flag=='ring')
	{
		$( "input[name^='ringid']" ).attr('checked','checked');
	}
	else if(flag=='pg')
	{
		$( "input[name^='pgid']" ).attr('checked','checked');
	}
	
}