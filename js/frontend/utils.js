$(document).ready(function() {
						   
	if ( $.browser.msie ) {
		var originalTitle = document.title.split("#")[0];    
		document.attachEvent('onpropertychange', function (evt) {
			if(evt.propertyName === 'title' && document.title !== originalTitle) {
				setTimeout(function () {
				   document.title = originalTitle;
				}, 1);
			}
		});
	}
	
	
});



