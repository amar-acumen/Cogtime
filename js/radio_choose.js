var dialog = null;
  function open_box(formid) {
	//alert($('#form1 input:checked').length);
	if($('#'+formid+' input:checked').length==1) {
		dialog = new ModalDialog('#'+$('#'+formid+' input:checked').attr('rel'));
		dialog.show();
	}
	else {
		showUIMsg('Please choose one option.');
	}
	//if($('#form1 input:checked').attr('rel')==)
  }
  
  function close_box() {
	if(dialog) {
		dialog.hide();
	}
  }
  
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
	
	 setTimeout($.unblockUI, 1000);
	}