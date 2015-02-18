//// script to implememnt Infinite-Scrolling effect...
//// 1st, fetching the selected div...

$(function(){
	
	var displayed_div = 'div#gen';
	var nav_selector = 'div#page-nav';
  
	//// 2nd, setting up the infinite-scroll part...
	$(displayed_div).infinitescroll({
		
		navSelector  : nav_selector,            
					   // selector for the paged navigation (it will be hidden)
		nextSelector : nav_selector +" a:first",    
					   // selector for the NEXT link (to page 2)
		itemSelector : "div.post",
					   // selector for all items you'll retrieve
		loading      : {
							finishedMsg: "No more records...",
							img: base_url +'images/scrolling_content_loader.gif',
							msgText: "Loading the next page"
					   }
		},function(arrayOfNewElems) {
			
			//// re-instantiating content slide part...
			callSlideContainer();
			
			//// scroll-to-top...
			$().UItoTop({ easingType: 'easeOutQuart' });
			
	});
	
	
	//// scroll-to-top...
	$().UItoTop({ easingType: 'easeOutQuart' });
	
	
	
	
});



function callSlideContainer()
{
	$(".slide_container").capslide({
		caption_color	: 'white',
		caption_bgcolor	: '#eeeeee',
		overlay_bgcolor : '#eeeeee',
		border			: '',
		showcaption	    : false
	});
}
