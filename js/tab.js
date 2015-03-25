// JavaScript Document for content tab
$(document).ready(function() {
	$(".tab_content ul li a").click(function() { 
		$('.tab_content ul li').removeClass('select');
		$(this).parent('li').addClass('select');
		var index = jQuery('.tab_content ul li a').index(jQuery(this)); //console.log($('.tab_details > div').filter(':eq(' + index + ')'));
		//console.log($(this));
		
		$('.tab_details > div').hide();
		$('.tab_details > div').filter(':eq(' + index + ')').show();
		$('.tab_details .sec-title').filter(':eq(' + index + ')').show();
		$('.tab_details .sec-detail').filter(':eq(' + index + ')').show();
		$('.tab_details .title-body').filter(':eq(' + index + ')').show();
		
		
		
  	});
	
	
	/*public profile tab start*/
	$(".profile-tabs ul li a").click(function() {
		$('.profile-tabs ul li').removeClass('select');
		$(this).parent('li').addClass('select');
		var index = jQuery('.profile-tabs ul li a').index(jQuery(this));
		$('.profile-content > div').hide();
		$('.profile-content > div').filter(':eq(' + index + ')').show();
  	});
	/*public profile tab end*/


	/*read write prayer point tab start*/
	$(".expandable .left a").click(function() {
		$('.expandable .left li').removeClass('select');
		$(this).parent('li').addClass('select');
		var index = jQuery('.expandable .left a').index(jQuery(this));
		
		$('.read-write-prayer-points > div').slideUp();
		$('.read-write-prayer-points > div').filter(':eq(' + index + ')').slideDown();
  	});
	
	$('.save_btn').live('click', function(){
		$(this).parents ('.read-write-prayer-points > div').slideUp();
    });  
	
	/*read write prayer point tab end*/
	
	/*note todo calender start*/
	
	$(".note-todo-tab li a").click(function() {
		
		var index = $(this).parent().index();
			
		if($('.note-todo-inside').eq(index).css('display')=="block"){
			$('.note-todo-inside').eq(index).slideUp();
			return false;
		}
		$('.note-todo-content-box .note-todo-inside').hide();
		$('.note-todo-tab li').removeClass('select');
			
		$('.note-todo-inside').eq(index).slideDown();
			$(this).parent('li').addClass('select');
			
			if($(this).parent().hasClass('select')){

		}

  	});
	
	
	
	$('.edit-this').click(function(){
		$(this).parents('.content-area').find('.show').slideUp();
		
		$(this).parents('.content-area').find('.edit').slideDown(function(){
			$('.cancel-now').click(function(){
				$(this).parents('.edit').slideUp();
				$(this).parents('.content-area').find('.show').slideDown();
			});	
		});
	});
	
	
	// added new //
	$('.edit-note').live('click', function(){
		//console.log($(this));
		$(this).parents('.content-area').find('.show').slideUp();
		$(this).parents('.content-area').find('.edit').slideDown(function(){
			$('.cancel-now').click(function(){
				$(this).parents('.edit').slideUp();
				$(this).parents('.content-area').find('.show').slideDown();
				});	
			});
	   });          
	
	
	
	
	$('.remove-this').click(function(){
		$(this).parents('.note-todo-event').remove();	
	});
	

	
	/*right panel tweet section end*/
	
	
	$('.search-create-prayer-box .minimize').click(function(){
		$(this).parent().slideUp();
		$(this).fadeOut();
		$(this).parents('#main_container').find('.search-create-btn-holder li').removeClass('select');
		
		$('#add_prayer_request')[0].reset();
		$('.error-message').each(function(i){
			$(this).attr('style','display:none');
		 });	
	});
	
	/* edit my commetment in manage-my-commitments page start*/
    $('.comment-for-pray-form .minimize').click(function(){
		$(this).parents('.prayer-wall-dotted-box').find('.comment-for-pray-form').slideUp();
		$(this).parents('.prayer-wall-dotted-box').find('.commit-content').slideDown();
	});
    
    
      
	
	
    
    
    
 /*search testimony open close form start*/
 $('.search-testimony').click(function(){
 	  $('.search-testimony-form').slideToggle('slow');
 });
 $('.testi-minimize').click(function(){
  	$('.search-testimony-form').slideToggle('slow');
 });
 /*search testimony open close form end*/
    
    
   // added for intercession
 
  $(".search-create-intercession-btn-holder li a").click(function() {
		var index = $(this).parent().index();
		if($(this).parent().parent().parent().find('.search-create-prayer-box').eq(index).css('display')=="block"){
			$(this).parent().parent().parent().find('.search-create-prayer-box').eq(index).slideUp();
			$(this).parent().removeClass('select');
			return false;
		}
		$(this).parent().parent().parent().find('.search-create-form-container .search-create-prayer-box').hide();
		$('.search-create-intercession-btn-holder li').removeClass('select');
			
		$(this).parent().parent().parent().find('.search-create-prayer-box').eq(index).slideDown();
			$(this).parent('li').addClass('select');
			$('.search-create-prayer-box .intercession-minimize').fadeIn();
			
			
		/*	if($(this).parent().hasClass('select')){
		}*/
  	});
	$('.search-create-prayer-box .intercession-minimize').click(function(){
		$(this).parent().slideUp();
		$('.intercession-minimize ').show();
		$(this).fadeOut();
		$(this).parents('#main_container').find('.search-create-intercession-btn-holder li').removeClass('select');
		
			
	});
     
	

	$("#table_content").on("click", ".addHighlight", function() { 
			$(this).parents('.spot-edit-container').find('.add-highlight').slideDown(function(){
			$('.add-highlight .back').click(function(){
				$(this).parents('.add-highlight').slideUp();
			});
		});
	});
	
	$("#table_content").on("click", ".addNote", function() { 
		$(this).parents('.spot-edit-container').find('.add-note').slideDown(function(){
			$('.add-note .back').click(function(){
				$(this).parents('.add-note').slideUp();
			});
		});
	});
	$("#table_content").on("click", ".addBookmark", function() { 
		$(this).parents('.spot-edit-container').find('.add-bookmark').slideDown(function(){
			$('.add-bookmark .back').click(function(){
				$(this).parents('.add-bookmark').slideUp();
			});
		});
	});
	
	$("#table_content").on("click", ".floating-popup .close", function() { 
		$('.floating-popup').hide();
		$('.spot-edit-box').hide(); 
		$('.ghost-layer').remove();
		return false;
		
	});
	

    $('a.prayer-link').click(function(){
    	var index=$('a.prayer-link').index(this);
        $('.form-prayer-box >.Create-list-box').not(':eq('+ index +')').css('display','none');
        
        $('.form-prayer-box >.Create-list-box').filter(':eq(' + index + ')').slideToggle('slow');
    })
	
	//19 july
	$('.create-group').click(function(){
		$('.slid-Create-Prayer').slideToggle('slow');
	})
	
	//7-08-13
	
	$('.btn_post-link').click(function(){
		$('.Post-in-ministers-shout').slideToggle('slow')
	})
	//8-08-13
	
	$('.register-your-church').click(function(){
		$('.register-your-church-section').slideToggle('slow')
	})
	
	
	
	/*$('.click-church').click(function(){
		$('.ModifySearch').css('display','block');
		$('.register-your-church-form').slideUp('slow');
	
	})*/
	
	$('.ModifySearch').click(function(){
		$(this).css('display','none');
		$('.register-your-church-form').slideDown('slow');
	})
	
	
	//19-08-13
		$('.comment-box > .left > a').click(function(){
			$('.commentspost').css('display','none');
			var index=$('.comment-box > .left > a').index($(this));
			$('.commentsliked > .comments-number').not(':eq('+ index +')').css('display','none');
			$('.commentsliked > .comments-number').filter(':eq('+ index +')').slideToggle('slow');
		})
		
		
		$('.comment-box > .right > a:first-child').click(function(){
				$('.commentsliked > .comments-number').css('display','none');
				$('.commentspost').slideToggle('slow');
		})
		
		
		/*$('.left-like li').click(function(){
			//$('.comments-number').hide();
			var index=$('.left-like li').index($(this));
			$('.christian-comt > .comments-number').not(':eq('+ index +')').css('display','none');
			alert(index)
			$('.christian-comt > .comments-number').filter(':eq('+ index +')').slideToggle('slow');
		})*/
		
		
		//07-9-13
		
	
				
				
				$('.ne-etrade-radio > input[type=radio]').click(function() {
					
					$(this).parent().parent().find('.request-to-swap >.request-swap').slideUp('slow');
				   if($(this).is(':checked')) { 
				   //alert('hi');
				   	var index= $(this).parent().index()-1;
					//alert(index);
					$(this).parent().parent().find('.request-to-swap >.request-swap').filter(':eq('+ index +')').slideDown('slow');
				    }
					
				});
		//07-9-13
	
	//14-9-13
	$('.dp-list li a').click(function(){
		$('.dp-list li a').removeClass('select');
		$('.dp-list-main-box > .dp-list-content-box').removeClass('select-display');
		$(this).addClass('select');
		var index=$('.dp-list li a').index(this);
		//alert(index);
		$('.dp-list-main-box > .dp-list-content-box').filter(':eq('+ index +')').addClass('select-display');
	
	})
	
	
	//07oct-2013
	
	
	/*$('.left a').click(function(){
	
	var index=$(this).index();
	$('.new-wl > .comments-number').css('display','none');
	$('.new-wl-right > .comments-number').css('display','none');
	$(this).parent().parent().next('.new-wl').children('.comments-number').filter(':eq('+ index +')').slideDown('slow');
	})*/
	
	
	/*$('.right a').click(function(){
	
	var index=$(this).index();
	
	
	$('.new-wl > .comments-number').css('display','none');
	$('.new-wl-right > .comments-number').css('display','none');
	$(this).parent().parent().next().next('.new-wl-right').children('.comments-number').filter(':eq('+ index +')').slideDown('slow');
	
	
	
	
	})
	
	
		$('.wal-minus').click(function(){
			$(this).parents('.comments-number').slideUp('fast');
		
		})*/
	
	 $('.wal-minus').click(function(){
			$(this).parents('.comments-number').slideUp('fast');
	 });
	
	//10-oct-2013
	$('.dp-list li a').click(function(){
		$('.dp-list li a').removeClass('select');
		$('.prfile-list-main-box > .prfile-sec-content-box ').removeClass('select-display');
		$(this).addClass('select');
		var index=$('.dp-list li a').index(this);
		//alert(index);
		$('.prfile-list-main-box > .prfile-sec-content-box ').filter(':eq('+ index +')').addClass('select-display');
	
	})
	
	$('.send-pro-message').click(function(){
		var index=$(this).index();
		$('.new-wl > .comments-number ').css('display','none');
		$(this).parent().parent().next('.new-wl').children('.comments-number').filter(':eq('+ index +')').slideDown('slow');
	});
	
	//07-9-13
			
			$('.ne-etrade-radio > input[type=radio]').click(function() {
				
				$(this).parent().parent().find('.request-to-swap >.request-swap').slideUp('slow');
			   if($(this).is(':checked')) { 
			   //alert('hi');
				var index= $(this).parent().index()-1;
				//alert(index);
				$(this).parent().parent().find('.request-to-swap >.request-swap').filter(':eq('+ index +')').slideDown('slow');
				}
				
			});
		//07-9-13
	
	//14-9-13
	$('.dp-list li a').click(function(){
		$('.dp-list li a').removeClass('select');
		$('.dp-list-main-box > .dp-list-content-box').removeClass('select-display');
		$(this).addClass('select');
		var index=$('.dp-list li a').index(this);
		//alert(index);
		$('.dp-list-main-box > .dp-list-content-box').filter(':eq('+ index +')').addClass('select-display');
	
	})
	
	
	//07oct-2013
	
	
	$('.left a').click(function(){
	
	var index=$(this).index();
	
	
	$('.new-wl > .comments-number').css('display','none');
	$('.new-wl-right > .comments-number').css('display','none');
	$(this).parent().parent().next('.new-wl').children('.comments-number').filter(':eq('+ index +')').slideDown('slow');
	})
	$('.right a').click(function(){
	var index=$(this).index();
	$('.new-wl > .comments-number').css('display','none');
	$('.new-wl-right > .comments-number').css('display','none');
	$(this).parent().parent().next().next('.new-wl-right').children('.comments-number').filter(':eq('+ index +')').slideDown('slow');
	})
	$('.wal-minus').click(function(){
		$(this).parents('.comments-number').slideUp('fast');
	})
	
	//10-oct-2013
	$('.dp-list li a').click(function(){
		$('.dp-list li a').removeClass('select');
		$('.prfile-list-main-box > .prfile-sec-content-box ').removeClass('select-display');
		$(this).addClass('select');
		var index=$('.dp-list li a').index(this);
		//alert(index);
		$('.prfile-list-main-box > .prfile-sec-content-box ').filter(':eq('+ index +')').addClass('select-display');
	})
	$('.send-pro-message').click(function(){
	var index=$(this).index();
	$('.new-wl > .comments-number ').css('display','none');
	$(this).parent().parent().next('.new-wl').children('.comments-number').filter(':eq('+ index +')').slideDown('slow');
	})
	//10-oct-2013
	
   //17-oct-2013
   	//$( "#date-inbox" ).datepicker({
//		showOn: "button",
//		buttonImage: "images/icons/cal.png",
//		buttonImageOnly: true
//	});

	$("#date-inbox").datepicker();
    //17-oct-2013
	//18-oct-2013
		$(document).delegate('.my-sub' , 'click', function(){
			$(this).parent().next().next().slideToggle();
			//$(this).parent().next().next('my-msg-body').toggle()'slow');
			})
			
		$('.rlp-btn').click(function(){
			$(this).parent().parent().prev().slideToggle();
			})
			
		$('.my-compose-minus').click(function(){
			$(this).parents('.my-compose-section').slideUp('slow');
			})
			
			$('.compose-msg-link').click(function(){
				$('.my-compose-section').slideToggle();
				})
			
	//18-oct-2013
	//07-dec-2013
	$('.restock-bttn').click(function(){
		$('.restock-product-content>.restock-section').slideToggle('slow');
	})
	//07-dec-2013
	
	 //20-jan-14
        
   $(".calender-add-todolist li a").click(function() {
		
		
		var index = $('.calender-add-todolist li a').index($(this)); //alert(index)
        
		if(index == 0)
			$('.add-cal-list >.shade_box_01 ').filter(':eq(1)').hide();
		else
			$('.add-cal-list >.shade_box_01 ').filter(':eq(0)').hide();
		 $(this).parent().parent().next().next('.add-cal-list').children('.shade_box_01').filter(':eq(' + index + ')').slideToggle('slow');
		
		
  	});
       
  

        
 });
 
 
 
 
function open_search_prayer_wall(){
	
	$('#search_p_wall ').slideToggle();
	//$('#search_p_wall ').attr('style','display:block;');
	$('#minimize_p_wall').attr('style','display:block;');
	
	$('#create_box ').attr('style','display:none;');
	$('#create_box .search-create-prayer-box').slideUp();
	
}
function open_create_prayer_wall(){
	$('#create_box .search-create-prayer-box').slideToggle();
	$('#create_box ').attr('style','display:block;');
	$('.minimize').show();
	
	$('#search_p_wall ').attr('style','display:none;');
	$('#search_p_wall ').slideUp();
	$('#minimize_p_wall').attr('style','display:none;');
	
	$('.error-message').each(function(i){
			$(this).attr('style','display:none');
	});
	$('#add_prayer_request')[0].reset();	
	
}






