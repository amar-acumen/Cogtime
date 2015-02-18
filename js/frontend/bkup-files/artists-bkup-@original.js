//// script to implememnt Infinite-Scrolling effect...
$(function(){
	
	$('#most_listened').infinitescroll({
		
		//debug		 : true,
		/*currPage	 : 0,
		navSelector  : "div#page-nav:last",            
					   // selector for the paged navigation (it will be hidden)
		nextSelector : "div#page-nav:last a:first",    
					   // selector for the NEXT link (to page 2)*/
		navSelector  : "div#page-nav",            
					   // selector for the paged navigation (it will be hidden)
		nextSelector : "div#page-nav a:first",    
					   // selector for the NEXT link (to page 2)
		itemSelector : "div.post"        
					   // selector for all items you'll retrieve
		},function(arrayOfNewElems) {
			
			$(".slide_container").capslide({
				caption_color	: 'white',
				caption_bgcolor	: '#eeeeee',
				overlay_bgcolor : '#eeeeee',
				border			: '',
				showcaption	    : false
			});
			
			//// scroll-to-top...
			$().UItoTop({ easingType: 'easeOutQuart' });
			
	});
	
	//// scroll-to-top...
	$().UItoTop({ easingType: 'easeOutQuart' });
	
});