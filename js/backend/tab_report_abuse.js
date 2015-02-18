// JavaScript Document for content tab
$(document).ready(function() {
	$(".tab_content ul li a").click(function() {
		$('.tab_content ul li').removeClass('select');
		$(this).parent('li').addClass('select');
		var index = jQuery('.tab_content ul li a').index(jQuery(this)); //console.log($('.tab_details > div').filter(':eq(' + index + ')'));
		$('.tab_details > div').hide();
		$('.tab_details > div').filter(':eq(' + index + ')').show();
		
  	});
	
 });
