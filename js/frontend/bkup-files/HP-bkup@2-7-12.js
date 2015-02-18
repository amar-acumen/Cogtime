$(document).ready(function(){
	
	
	/////////// FOR INFINITE SCROLL [START] ///////////
	
		//// setting up the infinite-scroll part...
		var page_nav_marker = $('#tab_type').val();
		/*var displayed_div = 'div#div_tab_content_'+ page_nav_marker;
		var nav_selector  = 'div#page-nav-'+ page_nav_marker;*/
		
		var displayed_div = 'div#articles_div';
		var nav_selector  = 'div#page-nav';
		
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
				
				//// for addthis...
				load_content_after_ajax_call();
				
				//// scroll-to-top...
				$().UItoTop({ easingType: 'easeOutQuart' });
				
		});
		
		
		//// scroll-to-top...
		//$().UItoTop({ easingType: 'easeOutQuart' });
	
	/////////// FOR INFINITE SCROLL [END] ///////////
	
});


// few adjustments for "Addthis"...
function load_content_after_ajax_call()
{
	// addthis configurations...
	addthis.init();
	addthis.ready();
	addthis.toolbox(".social_sharing_class");
	
	hideBusyScreen();	
}