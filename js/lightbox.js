// JavaScript Document
var dialog = null;
function show_dialog (id)
{ //alert(id);
	if(!dialog) dialog = null;
	dialog = new ModalDialog ("."+id);
	dialog.show();
	return false;
}

function hide_dialog ()
{
	dialog.hide();
	if(!dialog) dialog = null;
}


function show_another_dialog (id)
{
	dialog.hide();
	if(!dialog) dialog = null;
	dialog = new ModalDialog ("."+id);
	dialog.show();
	return false;
}


