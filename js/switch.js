$(function() {
	$('.exp-col01')
		.unbind('click')
		.click(function () {
			var index = $('.exp-col01').index($(this));
			$('#switchdiv01').slideToggle('slow');
			$('.exp-col01').not(':eq(' + index + ')').show();
			$(this).hide();
                         setTimeout(function(){
                        if($('#switchdiv01').css('display') == 'block'){
                           $.cookie("Chitter", '1');  
                        }
                      else  if($('#switchdiv01').css('display') == 'none'){
                          $.cookie("Chitter", '0');    
                        }
                        }, 1000);
		});
              var value = $.cookie('Chitter');
              // console.log( $.cookie('Chitter'));
                    //alert(value)
                    if(value == 1){
                      $('#switchdiv01').css('display','block');
                       //$.removeCookie('Personal')
                      $('.minus-right:eq(0)').show();
                       $('.plus-right:eq(0)').hide();
                    }
                     if(value == 0){
                      $('#switchdiv01').css('display','none');
                      $('.minus-right:eq(0)').hide();
                       $('.plus-right:eq(0)').show();
                    }
});

$(function() {
	$('.exp-col02')
		.unbind('click')
		.click(function () {
			var index = $('.exp-col02').index($(this));
			$('#switchdiv02').slideToggle('slow');
			$('.exp-col02').not(':eq(' + index + ')').show();
			$(this).hide();
                        
                        setTimeout(function(){
                        if($('#switchdiv02').css('display') == 'block'){
                           $.cookie("CoGTime-Events", '1');  
                        }
                      else  if($('#switchdiv02').css('display') == 'none'){
                          $.cookie("CoGTime-Events", '0');    
                        }
                        }, 1000);
                        
		});
                var value = $.cookie('CoGTime-Events');
                    //alert(value)
                    if(value == 1){
                      $('#switchdiv02').css('display','block');
                       //$.removeCookie('Personal')
                      $('.minus-right:eq(1)').show();
                       $('.plus-right:eq(1)').hide();
                    }
                     if(value == 0){
                      $('#switchdiv02').css('display','none');
                      $('.minus-right:eq(1)').hide();
                       $('.plus-right:eq(1)').show();
                    }
});

$(function() {
	$('.exp-col03')
		.unbind('click')
		.click(function () {
			var index = $('.exp-col03').index($(this));
               // alert(index)
			$('#switchdiv03').slideToggle('slow');
			$('.exp-col03').not(':eq(' + index + ')').show();
			$(this).hide();
                             setTimeout(function(){
                        if($('#switchdiv03').css('display') == 'block'){
                           $.cookie("Connections", '1');  
                        }
                      else  if($('#switchdiv03').css('display') == 'none'){
                          $.cookie("Connections", '0');    
                        }
                        }, 1000);
                        
		});
                var value = $.cookie('Connections');
                    //alert(value)
                    if(value == 1){
                      $('#switchdiv03').css('display','block');
                       //$.removeCookie('Personal')
                      $('.minus:eq(2)').show();
                       $('.plus:eq(2)').hide();
                    }
                     if(value == 0){
                      $('#switchdiv03').css('display','none');
                      $('.minus:eq(2)').hide();
                       $('.plus:eq(2)').show();
                    }
});

$(function() {
	$('.exp-col04')
		.unbind('click')
		.click(function () {
			var index = $('.exp-col04').index($(this));
			$('#switchdiv04').slideToggle('slow');
			$('.exp-col04').not(':eq(' + index + ')').show();
			$(this).hide();
		});
});



$(function() {
	$('.action_icon')
		.unbind('click')
		.click(function () {
			var index = $('.action_icon').index($(this));
			$('#action_div').slideToggle('slow');
			$('.action_icon').not(':eq(' + index + ')').show();
			$(this).hide();
		});
});
$(function() {
	$('.action_icon01')
		.unbind('click')
		.click(function () {
			var index = $('.action_icon01').index($(this));
			$('#action_div01').slideToggle('slow');
			$('.action_icon01').not(':eq(' + index + ')').show();
			$(this).hide();
		});
});


/*$(document).ready(function(){
	$('.action_show').click(function(){
		$(".action_div").slideToggle();
		var class_selected = $(this).attr('class');
		
		if( class_selected=='action_show action_selected' )
			$(".action_show").removeClass("action_selected");
		else
			$(".action_show").addClass("action_selected");
			
	});
});*/


$(function() {
	$('.exp-col011')
		.unbind('click')
		.click(function () {
			var index = $('.exp-col011').index($(this));
			$('#switchdiv011').slideToggle('slow');
			$('.exp-col011').not(':eq(' + index + ')').show();
			$(this).hide();
		});
});


$(function() {
	$('.exp-col012')
		.unbind('click')
		.click(function () {
			var index = $('.exp-col012').index($(this));
			$('#switchdiv012').slideToggle('slow');
			$('.exp-col012').not(':eq(' + index + ')').show();
			$(this).hide();
		});
});


$(function() {
	$('.exp-col013')
		.unbind('click')
		.click(function () {
			var index = $('.exp-col013').index($(this));
			$('#switchdiv013').slideToggle('slow');
			$('.exp-col013').not(':eq(' + index + ')').show();
			$(this).hide();
		});
});

$(function() {
	$('.exp-col014')
		.unbind('click')
		.click(function () {
			var index = $('.exp-col014').index($(this));
			$('#switchdiv014').slideToggle('slow');
			$('.exp-col014').not(':eq(' + index + ')').show();
			$(this).hide();
		});
});

$(function() {
	$('.exp-col015')
		.unbind('click')
		.click(function () {
			var index = $('.exp-col015').index($(this));
			$('#switchdiv015').slideToggle('slow');
			$('.exp-col015').not(':eq(' + index + ')').show();
			$(this).hide();
		});
});

$(function() {
	$('.exp-col016')
		.unbind('click')
		.click(function () {
			var index = $('.exp-col016').index($(this));
			$('#switchdiv016').slideToggle('slow');
			$('.exp-col016').not(':eq(' + index + ')').show();
			$(this).hide();
		});
});

$(function() {
	$('.exp-col017')
		.unbind('click')
		.click(function () {
			var index = $('.exp-col017').index($(this));
			$('#switchdiv017').slideToggle('slow');
			$('.exp-col017').not(':eq(' + index + ')').show();
			$(this).hide();
		});
});

$(function() {
	$('.exp-col018')
		.unbind('click')
		.click(function () {
			var index = $('.exp-col018').index($(this));
			$('#switchdiv018').slideToggle('slow');
			$('.exp-col018').not(':eq(' + index + ')').show();
			$(this).hide();
		});
});

$(function() {
	$('.exp-col019')
		.unbind('click')
		.click(function () {
			var index = $('.exp-col019').index($(this));
			$('#switchdiv019').slideToggle('slow');
			$('.exp-col019').not(':eq(' + index + ')').show();
			$(this).hide();
                        
                        
                         setTimeout(function(){
                        if($('#switchdiv019').css('display') == 'block'){
                           $.cookie("Media-Library", '1');  
                        }
                      else  if($('#switchdiv019').css('display') == 'none'){
                          $.cookie("Media-Library", '0');    
                        }
                        }, 1000);
		});
                
                
                var value = $.cookie('Media-Library');
                    //alert(value)
                    if(value == 1){
                      $('#switchdiv019').css('display','block');
                       //$.removeCookie('Personal')
                      $('.minus:eq(3)').show();
                       $('.plus:eq(3)').hide();
                    }
                     if(value == 0){
                      $('#switchdiv019').css('display','none');
                      $('.minus:eq(3)').hide();
                       $('.plus:eq(3)').show();
                    }
});

$(function() {
	$('.exp-col020')
		.unbind('click')
		.click(function () {
			var index = $('.exp-col020').index($(this));
			$('#switchdiv020').slideToggle('slow');
			$('.exp-col020').not(':eq(' + index + ')').show();
			$(this).hide();
                        
                         setTimeout(function(){
                        if($('#switchdiv020').css('display') == 'block'){
                           $.cookie("Social", '1');  
                        }
                      else  if($('#switchdiv020').css('display') == 'none'){
                          $.cookie("Social", '0');    
                        }
                        }, 1000);
		});
                
                 var value = $.cookie('Social');
                    //alert(value)
                    if(value == 1){
                      $('#switchdiv020').css('display','block');
                       //$.removeCookie('Personal')
                      $('.minus:eq(4)').show();
                       $('.plus:eq(4)').hide();
                    }
                     if(value == 0){
                      $('#switchdiv020').css('display','none');
                      $('.minus:eq(4)').hide();
                       $('.plus:eq(4)').show();
                    }
});

$(function() {
    // alert('dd')
	$('.exp-col021')
		.unbind('click')
		.click(function () {
                   // alert('dd')
			var index = $('.exp-col021').index($(this));
			$('#switchdiv021').slideToggle('slow');
			$('.exp-col021').not(':eq(' + index + ')').show();
			$(this).hide();
                        setTimeout(function(){
                        if($('#switchdiv021').css('display') == 'block'){
                           $.cookie("Personal", '1');  
                        }
                      else  if($('#switchdiv021').css('display') == 'none'){
                          $.cookie("Personal", '0');    
                        }
                        }, 1000);
                      /***********************/
                    
                      
		});
                
                    
//                   $('.exp-col021').live("click",function(){
//                       if($('#switchdiv021').is(':visible')){
//                      $.cookie("Personal", '1');
//                  } 
//                  if($('#switchdiv021').is(':hidden')){
//                       $.cookie("Personal", '0');  
//                    }
//                  
//                   });
//                     
                   
                    
               
                
                    var value = $.cookie('Personal');
                    //alert(value)
                    if(value == 1){
                      $('#switchdiv021').css('display','block');
                       //$.removeCookie('Personal')
                      $('.minus:eq(0)').show();
                       $('.plus:eq(0)').hide();
                    }
                     if(value == 0){
                      $('#switchdiv021').css('display','none');
                      $('.minus:eq(0)').hide();
                       $('.plus:eq(0)').show();
                    }
                    
                    
});

$(function() {
	$('.exp-col022')
		.unbind('click')
		.click(function () {
			var index = $('.exp-col022').index($(this));
			$('#switchdiv022').slideToggle('slow');
			$('.exp-col022').not(':eq(' + index + ')').show();
			$(this).hide();
                        
                         setTimeout(function(){
                        if($('#switchdiv022').css('display') == 'block'){
                           $.cookie("People", '1');  
                        }
                      else  if($('#switchdiv022').css('display') == 'none'){
                          $.cookie("People", '0');    
                        }
                        }, 1000);
		});
                var value = $.cookie('People');
                    //alert(value)
                    if(value == 1){
                      $('#switchdiv022').css('display','block');
                       //$.removeCookie('Personal')
                      
                        $('.minus-right:eq(2)').show();
                       $('.plus-right:eq(2)').hide();
                     
                    }
                     if(value == 0){
                      $('#switchdiv022').css('display','none');
                       $('.minus-right:eq(2)').hide();
                       $('.plus-right:eq(2)').show();
                    }
});


$(function() {
	$('.exp-col023')
		.unbind('click')
		.click(function () {
                    
			var index = $('.exp-col023').index($(this));
               // alert(index)
			$('#switchdiv023').slideToggle('slow');
			$('.exp-col023').not(':eq(' + index + ')').show();
			$(this).hide();
                        
                        setTimeout(function(){
                        if($('#switchdiv023').css('display') == 'block'){
                           $.cookie("Messages", '1');  
                        }
                      else  if($('#switchdiv023').css('display') == 'none'){
                          $.cookie("Messages", '0');    
                        }
                        }, 1000);
		});
                
                
                  var value = $.cookie('Messages');
                    //alert(value)
                    if(value == 1){
                      $('#switchdiv023').css('display','block');
                       //$.removeCookie('Personal')
                      $('.minus:eq(1)').show();
                       $('.plus:eq(1)').hide();
                    }
                     if(value == 0){
                      $('#switchdiv023').css('display','none');
                      $('.minus:eq(1)').hide();
                       $('.plus:eq(1)').show();
                    }
});

$(function() {
	$('.exp-col024')
		.unbind('click')
		.click(function () {
			var index = $('.exp-col024').index($(this));
			$('#switchdiv024').slideToggle('slow');
			$('.exp-col024').not(':eq(' + index + ')').show();
			$(this).hide();
                           setTimeout(function(){
                        if($('#switchdiv024').css('display') == 'block'){
                           $.cookie("Holy-Zone", '1');  
                        }
                      else  if($('#switchdiv024').css('display') == 'none'){
                          $.cookie("Holy-Zone", '0');    
                        }
                        }, 1000);
		});
                 var value = $.cookie('Holy-Zone');
                if(value == 1){
                      $('#switchdiv024').css('display','block');
                       //$.removeCookie('Personal')
                      $('.minus:eq(5)').show();
                       $('.plus:eq(5)').hide();
                    }
                     if(value == 0){
                      $('#switchdiv024').css('display','none');
                      $('.minus:eq(5)').hide();
                       $('.plus:eq(5)').show();
                    }
});

$(function() {
	$('.exp-col025')
		.unbind('click')
		.click(function () {
			var index = $('.exp-col025').index($(this));
			$('#switchdiv025').slideToggle('slow');
			$('.exp-col025').not(':eq(' + index + ')').show();
			$(this).hide();
                        setTimeout(function(){
                        if($('#switchdiv025').css('display') == 'block'){
                           $.cookie("chat-rooms", '1');  
                        }
                      else  if($('#switchdiv025').css('display') == 'none'){
                          $.cookie("chat-rooms", '0');    
                        }
                        }, 1000);
		});
                var value = $.cookie('chat-rooms');
                if(value == 1){
                      $('#switchdiv025').css('display','block');
                       //$.removeCookie('Personal')
                      $('.minus:eq(6)').show();
                       $('.plus:eq(6)').hide();
                    }
                     if(value == 0){
                      $('#switchdiv025').css('display','none');
                      $('.minus:eq(6)').hide();
                       $('.plus:eq(6)').show();
                    }
});

$(function() {
	$('.exp-col026')
		.unbind('click')
		.click(function () {
			var index = $('.exp-col026').index($(this));
			$('#switchdiv026').slideToggle('slow');
			$('.exp-col026').not(':eq(' + index + ')').show();
			$(this).hide();
		});
});

$(function() {
	$('.exp-col027')
		.on('click',function(){

			
			if($('.topplus').css('display') !="block"){
					$('#left_container .nw-content').slideUp();
				$('.topplus').show();
				$('.topminus').hide();
				
				$('.minus').hide();
				$('.plus').show();
				
			} else {
				$('#left_container .nw-content').slideDown();
				$('.topplus').hide();
				$('.topminus').show();
				
				$('.minus').show();
				$('.plus').hide();	
			}
			
			
			
		});
});

$(function() {
	$('.exp-col028')
		.unbind('click')
		.click(function () {
			var index = $('.exp-col028').index($(this));
			$('#switchdiv028').slideToggle('slow');
			$('.exp-col028').not(':eq(' + index + ')').show();
			$(this).hide();
		});
});

$(function() {
	$('.exp-col029')
		.on('click',function(){

			
			if($('.topplus-right').css('display') !="block"){
					$('#right_container .content').slideUp();
				$('.topplus-right').show();
				$('.topminus-right').hide();
				
				$('.minus-right').hide();
				$('.plus-right').show();
				
			} else {
				$('#right_container .content').slideDown();
				$('.topplus-right').hide();
				$('.topminus-right').show();
				
				$('.minus-right').show();
				$('.plus-right').hide();	
			}
			
			
			
		});
});




/************************************/
$(function() {
	$('.exp-col030')
		.unbind('click')
		.click(function () {
			var index = $('.exp-col030').index($(this));
			$('#switchdiv030').slideToggle('slow');
			$('.exp-col030').not(':eq(' + index + ')').show();
			$(this).hide();
                           setTimeout(function(){
                        if($('#switchdiv030').css('display') == 'block'){
                           $.cookie("Your-Church", '1');  
                        }
                      else  if($('#switchdiv030').css('display') == 'none'){
                          $.cookie("Your-Church", '0');    
                        }
                        }, 1000);
		});
                 var value = $.cookie('Your-Church');
                if(value == 1){
                      $('#switchdiv030').css('display','block');
                       //$.removeCookie('Personal')
                      $('.minus:eq(7)').show();
                       $('.plus:eq(7)').hide();
                    }
                     if(value == 0){
                      $('#switchdiv030').css('display','none');
                      $('.minus:eq(7)').hide();
                       $('.plus:eq(7)').show();
                    }
});