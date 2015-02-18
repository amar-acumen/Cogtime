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


// function to reload songs dropdown, based on genre selection [AJAX CALL]...
function change_radio_song_list_AJAX(selected_genre)
{
	var ajaxURL = base_url +'utility/populate_radio_songs_list_AJAX/'+ selected_genre;
	$('#radio_song_list option:selected').text("Loading...");
	
	//// 1st, generate songs list [AJAX CALL]...
	$.ajax({
		url: ajaxURL,
		dataType: 'json',
		success: function(data) {
			
			if( data.result=='success' )
				$('select#radio_song_list').html(data.html_content);
			
		}
	});
	
	//// 2nd, re-generate web-radio playlist [AJAX CALL]...
	radio_playlist.remove(); // Removes all items
	
	// AJAX url to get json playlist data...
	var playlist_ajax_url = base_url +'utility/get_playlist_data_AJAX/'+ selected_genre;
	$.getJSON(playlist_ajax_url, function(json) {
		radio_playlist.setPlaylist( json );	// refreshing & reloading the whole playlist...
		
		// and, select the 1st track...
		radio_playlist.select(0);
		radio_playlist.play();
	});
	
	
	
	//alert( swf_executable_path );
	
	//radio_playlist.add(playlist);
}


//// function to play the selected song (from drop-down) &
//// plays it from the playlist...
function play_track_from_playlist()
{
	var selected_track_index = $('#radio_song_list').attr("selectedIndex");
	
	// play the selected track from playlist...
	radio_playlist.play(selected_track_index);
}


//// function to re-instantiate infinite-scrolling function...
function initialize_infinite_scroller(selected_div)
{
	var nav_selector = 'div#page-nav-'+ selected_div;
	var selected_div_id = '#'+ selected_div;
	
	//alert( nav_selector +'=='+ selected_div_id );
	
	//// 2nd, setting up the infinite-scroll part...
	$(selected_div_id).infinitescroll({
		
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
			$(selected_div).UItoTop({ easingType: 'easeOutQuart' });
			
	});
	
}