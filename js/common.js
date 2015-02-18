// JavaScript Document

$(function() {
	$(".slide_container").capslide({
		caption_color	: 'white',
		caption_bgcolor	: '#eeeeee',
		overlay_bgcolor : '#eeeeee',
		border			: '',
		showcaption	    : false
	});
        
       
});


$(window).load(function() {
	$('#slider').nivoSlider({
		directionNavHide:false,
		effect:'fade',
		animSpeed:'1000',
		pauseTime:'8000'
	});
	
});


