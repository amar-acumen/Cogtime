function showState(country)
{
	showBusyScreen();
	$.ajax({
			"type" : "post",
			"url"  : base_url+'logged/country_state_city/getstate/'+country,
			"success" : function(response)
			{
				var optiontxt	= '<option value="-1">---</option>'+response;
				$('#txt_state').html(optiontxt);
				hideBusyScreen();
			}
		});
	
}
function showCity(state)
{
	showBusyScreen();
	$.ajax({
			"type" : "post",
			"url"  : base_url+'logged/country_state_city/getcity/'+state,
			"success" : function(response)
			{
				var optiontxt	= '<option value="-1">---</option>'+response;
				$('#txt_city').html(optiontxt);
				hideBusyScreen();
			}
		});
	
}


function showStatePopup(country)
{
 	showUILoader_nodialog('<img src="'+base_url+'images/loading_big.gif" width="50"/> ');
	$.ajax({
			"type" : "post",
			"url"  : base_url+'logged/country_state_city/getstate/'+country,
			"success" : function(response)
			{
				var optiontxt	= '<option value="-1">---</option>'+response;
				$('#txt_state').html(optiontxt);
				hideUILoader_nodialog();
			}
		});
	
}
function showCityPopup(state)
{
 	showUILoader_nodialog('<img src="'+base_url+'images/loading_big.gif" width="50"/> ');
	$.ajax({
			"type" : "post",
			"url"  : base_url+'logged/country_state_city/getcity/'+state,
			"success" : function(response)
			{
				var optiontxt	= '<option value="-1">---</option>'+response;
				$('#txt_city').html(optiontxt);
				hideUILoader_nodialog();
			}
		});
	
}