   $(document).ready(function() {
		$(".signin").click(function(e) {          
			e.preventDefault();
			$("div#signin_menu").toggle();
			$(".signin").toggleClass("menu-open");
			
			$('#screen').css({ opacity: 0.5, 'width':$(document).width(),'height':$(document).height()});
		});
		
		$("div#signin_menu").mouseup(function() {
			return false
		});
		$(document).mouseup(function(e) {
			if($(e.target).parent("a.signin").length==0) {
				$(".signin").removeClass("menu-open");
				$("div#signin_menu").hide();
				
				$('#screen').css({ opacity: 0, 'width':0,'height':0});
			}
			
		});			
		
	});
