// JavaScript Document


$(document).ready(function() {

	// for AJAX page-submission...
    var options = { 
        beforeSubmit:  showLoad,  // pre-submit callback 
        success:       validateFrm // post-submit callback 
    }; 
 
    // bind to the form's submit event 
    $('#searchfrm').submit(function() {
        $(this).ajaxSubmit(options);
        return false;
		
    });
});
function delete_category()
{
	$.ajax({
			type: 'post',
			url: base_url+'logged/holy_place/remove_category/',
			dataType: 'json',
			data: $('#deleteCatFrm').serialize(),
			success: function (msg) {
						$('#catdiv').html(msg.html);
						$('#library_categorylist').html('');
						$('#library_categorylist').html(msg.cat_html);
						hide_dialog();
						showUIMsg('Category has been successfully deleted');
			}
		});
}
function showLoad()
{
 // $('#photo_album_btn').attr('disabled','disabled');
  showBusyScreen(); 
}


// validate ajax-submission...
function validateFrm(data)
{
	//alert( data);
	var data = JSON.parse(data);

	if(data.success==false) 
	{
		$('.error-message').each(function(i){
			$(this).attr('style','display:none');
		});


		for ( var id in data.arr_messages ){
			if( $('#err_'+id) != null ) {
				$('#err_'+id).html(data.arr_messages[id]);
				$('#err_'+id).css('display', 'block');
			}
		}
		
	}
	else 
	{
		if(data.searchtype	== 'by_verse')
		{
			$.post(base_url+'logged/holy_place/generate_read_bible_AJAX/'+data.slab, {'t' : 't'}, 
					function(pagingdata)
					{
						$('#table_content').html(pagingdata);
						var curUrl	= document.URL;
						if(curUrl.indexOf('#'))
						{
							 var n	= curUrl.split('#');
							 curUrl	= n[0];
						}
						var elID	= "#div_"+data.dest_verse;//alert(elID);
						scrollToGivenPosition(elID);
						hideBusyScreen(); ; 
					}
				); 
		}
		else if(data.searchtype	== 'by_book')
		{
			$("#table_content").html(data.content);
			hideBusyScreen(); 
		}
		
	}
	//hideLoading();
				
}
	
function clickToShowMore(id, vids, txt)
{
	showBusyScreen(); 
	$.ajax({
				type: 'post',
				url: base_url+'logged/holy_place/search_more/',
				dataType: 'json',
				data: 'verse_ids='+vids+'&bid='+id+'&stext='+txt,
				success: function (msg) {
						$('#click_'+id).fadeOut('slow',
							function()
							{
								$('#remain_'+id).html(msg.content);
								$('#remain_'+id).fadeIn();	
								hideBusyScreen(); 
							}
						);
				}	// end of success function...
		  });
}

function submitSearch()
{
	var str	= $("#searchtxt").val();
	var n=str.indexOf(":"); 
	if(n != -1)
	{
		searchVerse();
	}
	else
	{
		showBusyScreen(); 
		$('#searchfrm1').submit();
	}
}




function goToChapter(paging)
{
	showBusyScreen(); 
	if(paging=='prev')
	{
		var anchor_name	= $("#versediv .spot-edit:first a:first").attr("name").split('_');
		var chapter	= $("#versediv .spot-edit:first a:first").attr("chapter");
		
	}
	else if(paging=='next')
	{
		var anchor_name	= $("#versediv .spot-edit:last a:first").attr("name").split('_');
		var chapter	= $("#versediv .spot-edit:last a:first").attr("chapter");
	}
	$.ajax({
			type: 'post',
			url: base_url+'logged/holy_place/get_chapter/',
			dataType: 'json',
			data: 'verseid='+anchor_name[1]+'&paging='+paging+'&chapter='+chapter,
			success: function (msg) {
			$.post(base_url+'logged/holy_place/generate_read_bible_AJAX/'+msg.slab, {'t' : 't'}, 
				function(data)
				{
					$('#table_content').html(data);
					var elID	= "#div_"+msg.dest_verse;
					scrollToGivenPosition(elID);
					resetAll();
					hideBusyScreen(); 
				}
			); 
			
		}	// end of success function...
	});
}

function goToBook(paging)
{
	showBusyScreen(); 
	if(paging=='prev')
	{
		var anchor_name	= $("#versediv .spot-edit:first a:first").attr("name").split('_');
		var book	= $("#versediv .spot-edit:first a:first").attr("book");
		
	}
	else if(paging=='next')
	{
		var anchor_name	= $("#versediv .spot-edit:last a:first").attr("name").split('_');
		var book	= $("#versediv .spot-edit:last a:first").attr("book");
	}
	$.ajax({
		type: 'post',
		url: base_url+'logged/holy_place/get_book/',
		dataType: 'json',
		data: 'verseid='+anchor_name[1]+'&paging='+paging+'&book='+book,
		success: function (msg) {
			$.post(base_url+'logged/holy_place/generate_read_bible_AJAX/'+msg.slab, {'t' : 't'}, 
				function(data)
				{
					$('#table_content').html(data);
					var elID	= "#div_"+msg.dest_verse;
					scrollToGivenPosition(elID);
					resetAll();
					hideBusyScreen(); 
				}
			); 
			
		}	// end of success function...
	});
}

function gotoVerse(id)
{
	showBusyScreen(); 
	$.ajax({
		type: 'post',
		url: base_url+'logged/holy_place/getting_slab_from_verse/',
		dataType: 'json',
		data: 'verseid='+id,
		success: function (msg) {
			if(this_method=='all_books' || this_method=='books_chapter' || this_method=='verses')
			{
				location.href	= 'holy-place/read-bible/'+msg.slab+'/'+msg.dest_verse;
			}
			else
			{
				$.post(base_url+'logged/holy_place/generate_read_bible_AJAX/'+msg.slab, {'t' : 't'}, 
					function(data)
					{
						$('#table_content').html(data);
						var elID	= '#div_'+id;
						showscroll();
						scrollToGivenPosition(elID);
						resetAll();
						hideBusyScreen(); 
					}); 
			}
			
		}	// end of success function...
	});

}
function showscroll()
{
	$("#table_content").niceScroll();
}
function scrollToGivenPosition(elementId)
{
//alert($(elementId).position()+'elementId'+elementId);
	var lastElementTop = ($(elementId).position().top)*1;
	
	$("#table_content").scrollTop(lastElementTop);
}



function delete_hilits()
{
	$.ajax({
			type: 'post',
			url: base_url+'logged/holy_place/remove_hilits/',
			dataType: 'json',
			data: $('#deleteHilitsFrm').serialize(),
			success: function (msg) {
						if(this_method=='all_books' || this_method=='books_chapter' || this_method=='verses')
						{
							hide_dialog();
							showUIMsg('Highlights has been successfully deleted');
							$('#hilits_div').html(msg.html);
							$('#ajax_library').html(msg.library);
							showscroll();
						}
						else
						{
							$.post(base_url+'logged/holy_place/generate_read_bible_AJAX/'+msg.slab, {'t' : 't'}, 
								function(data)
								{
									hide_dialog();
									showUIMsg('Highlights has been successfully deleted');
									$('#table_content').html(data);
									$('#hilits_div').html(msg.html);
									$('#ajax_library').html(msg.library);
									showscroll();
								}
							); 
						}
						
			}
	});
}
function delete_bkmark()
{
	$.ajax({
			type: 'post',
			url: base_url+'logged/holy_place/remove_bookmark/',
			dataType: 'json',
			data: $('#deleteBkmarkFrm').serialize(),
			success: function (msg) {
						if(this_method=='all_books' || this_method=='books_chapter' || this_method=='verses')
						{
							hide_dialog();
							showUIMsg('Book Mark has been successfully deleted');
							$('#bookmark_div').html(msg.html);
							$('#ajax_library').html(msg.library);
							showscroll();
						}
						else
						{
							$.post(base_url+'logged/holy_place/generate_read_bible_AJAX/'+msg.slab, {'t' : 't'}, 
								function(data)
								{
									hide_dialog();
									showUIMsg('Book Mark has been successfully deleted');
									$('#table_content').html(data);
									$('#bookmark_div').html(msg.html);
									$('#ajax_library').html(msg.library);
									showscroll();
								}
							);
						}
			}
	});
}


function delete_note(){
	$.ajax({
			type: 'post',
			url: base_url+'logged/holy_place/remove_note/',
			dataType: 'json',
			data: $('#deletefrmnote').serialize(),
			success: function (msg) {
						if(this_method=='all_books' || this_method=='books_chapter' || this_method=='verses')
						{
							hide_dialog();
							showUIMsg('Note has been successfully deleted');
							$('#notediv').html(msg.html);
							$('#ajax_library').html(msg.library);
							showscroll();
						}
						else
						{
							$.post(base_url+'logged/holy_place/generate_read_bible_AJAX/'+msg.slab, {'t' : 't'}, 
								function(data)
								{
									hide_dialog();
									showUIMsg('Note has been successfully deleted');
									$('#table_content').html(data);
									$('#notediv').html(msg.html);
									$('#ajax_library').html(msg.library);
									showscroll();
								}
							); 
						}
			}
	});
}


function showEditNote(id)
{
	showBusyScreen();
	$('#err_cat').html('');
	$('#err_note').html('');
	$.ajax({
			type: 'post',
			url: base_url+'logged/holy_place/show_edit_note/'+id,
			dataType: 'json',
			data: '',
			success: function (msg) {
					hideBusyScreen();
					$('#category').val(msg.category);
					$('#note').val(msg.text);
					$('#noteid').val(id);
					show_dialog('edit_note');
			}
	});
	
}


function editNote(){
	$.ajax({
			type: 'post',
			url: base_url+'logged/holy_place/edit_note/',
			dataType: 'json',
			data: $('#frmnoteedit').serialize(),
			success: function (msg) {
					if(msg.success==false)
					{
						if(msg.data.err_cat)
						{
							$('#err_cat').html(msg.data.err_cat);
						}
						if(msg.data.err_note)
						{
							$('#err_note').html(msg.data.err_note);
						}
					}
					else
					{
						$('#notediv').html(msg.html);
						$('#ajax_library').html(msg.library);
						hide_dialog();
						showUIMsg('Note has been successfully updated');
					}
			}
	});
}

function showAllByCategory(val,gotourl,divid)
{
	$.ajax({
				type: 'post',
				url: gotourl,
				dataType: 'json',
				data: 'cat='+val,
				success: function (msg) {
						$('#'+divid).html(msg.html);
				}	// end of success function...
		  });
}


function spot_edit_click(obj)
{
	var objSpotEditBox	= obj.parents('.contents').find('.spot-edit-box');
	objSpotEditBox.show();
	$('body').append('<div class="ghost-layer" onclick="ghost_layer_click($(this))"></div>');
	if(obj.offset().top > 655)
	{
		var parentobj	= $(objSpotEditBox).children('.spot-edit-container');
		//console.log($(parentobj).children('.floating-popup'));
		$(parentobj).children('.floating-popup').css('top','-153px');
	}
}
function ghost_layer_click(obj)
{
	$('.spot-edit-box').each(function(){
			if($(this).css('display')=='block'){
				$(this).hide();	
			}
		});
	obj.remove();
}


/*********************************24-06-13*******************************************/
function closeDialog(dialog_type)
{
	hide_dialog();
	$('.turn-on-btns a').removeClass('select');
	if(dialog_type=='note')
	{
		$('.inside-grey-container').unbind('click');
	}
	if(dialog_type=='hilits')
	{
		$('#table_content .hilitstest').off('mouseup');
		$('#highlightIcon').unbind('click');
		$("#hilitsAdd").unbind('click');
	}
	$('.contents').css('cursor', 'auto');
}

$(window).load(function(){
	setTimeout("$('.note-container, .bookmark-container, .highlight-container, .reading-plan-container, .category-container').css({opacity:'100',display:'none'});",1);
});


$(function(){

	$('.scroll_library').niceScroll();
	$('.scroll_note').niceScroll();
	$('.scroll_bookmark').niceScroll();
	$('.scroll_highlights').niceScroll();
	$('#reading_plan_list').niceScroll();
		
	$('#myNoteBtn').click(function(){
		$('.general-container').slideUp();
		$('.note-container').slideDown(function(){
			$(".scroll_note").getNiceScroll().resize();
			$('.note-container .back').click(function(){
				$(this).parents('.note-container').slideUp();
				$('.general-container').slideDown();
			});
		});
		$.ajax({
			type: 'post',
			url: base_url+'logged/holy_place/get_all_note/',
			dataType: 'json',
			data: '',
			success: function (msg) {
					$('#notediv').html(msg.html);
			}	// end of success function...
		});
	});
	
	
	$('#myBookmarkBtn').click(function(){
		$('.general-container').slideUp();
		$('.bookmark-container').slideDown(function(){
			$(".scroll_bookmark").getNiceScroll().resize();
			$('.bookmark-container .back').click(function(){
				$(this).parents('.bookmark-container').slideUp();
				$('.general-container').slideDown();
			});
		});
		$.ajax({
			type: 'post',
			url: base_url+'logged/holy_place/get_all_bookmark/',
			dataType: 'json',
			data: '',
			success: function (msg) {
					$('#bookmark_div').html(msg.html);
			}	// end of success function...
		});
	});
	
	$('#myHighlightBtn').click(function(){
		$('.general-container').slideUp();
		$('.highlight-container').slideDown(function(){
			$(".scroll_highlights").getNiceScroll().resize();
			$('.highlight-container .back').click(function(){
				$(this).parents('.highlight-container').slideUp();
				$('.general-container').slideDown();
			});
		});
		$.ajax({
			type: 'post',
			url: base_url+'logged/holy_place/get_all_highilights/',
			dataType: 'json',
			data: '',
			success: function (msg) {
					$('#hilits_div').html(msg.html);
			}	// end of success function...
		});
	});
	
	
	$('#readingPlan').click(function(){
		$('.general-container').slideUp();
		$('.reading-plan-container').addClass('active').slideDown(function(){
			$('.reading-plan-container .back').click(function(){
				$(this).parents('.reading-plan-container').slideUp();
				$('.general-container').slideDown();
			});
		});
	});
	
	
	
	$('.reading-plan-title').click(function(){
		
		$('.reading-plan-title').removeClass('select');
		$('.reading-plan-detail').slideUp('slow');
		
		if( $(this).parent('.reading-plan-box').find(".reading-plan-detail" ).css('display') == 'none' ) {
			$(this).parent('.reading-plan-box').find(".reading-plan-detail" ).slideDown('slow');
			
			if($(this).hasClass('select'))
		{
			$(this).removeClass("select");
		}
		else
		{
			$(this).addClass("select");
		}
		}
		else {
			$(this).parents('.reading-plan-box').find(".reading-plan-detail" ).slideUp('slow');
		}
	});
	/*accordian script end*/
	$('.select-plan-details .view-reading').click(function(){
		$(this).parent('.control-buttons').slideUp();
		$(this).parents('.select-plan-details').find('.view-reading-plan-detail').slideDown();
		$("#reading_plan_list").getNiceScroll().resize();
	});


	  $('.view-plan-detail-title .back2').click(function(){
	  $(this).parent('.view-plan-detail-title').parent('.view-reading-plan-detail').slideUp();
	  $(this).parents('.select-plan-details').find('.control-buttons').slideDown();
	 });
	 
	 
	 $('.select-plan-details .todays-assignment').click(function(){
		$(this).parent('.control-buttons').slideUp();
		$(this).parents('.select-plan-details').find('.view-today-plan-detail').slideDown();
	});
	
	 $('.view-today-plan-detail-title .back2').click(function(){
	  $(this).parent('.view-today-plan-detail-title').parent('.view-today-plan-detail').slideUp();
	  $(this).parents('.select-plan-details').find('.control-buttons').slideDown();
	 });

});
/*********************************24-06-13*******************************************/

