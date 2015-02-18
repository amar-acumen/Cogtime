///////////// JS FUNCTION(S) FOR "member-details" PAGE /////////////

/* ############## ELEMENT BLOCKING LOADER [BEGIN] ############## */

	// loading screen...
	function showAJAXLoader(divID, type)
	{
		var HTML = '<img src="'+ base_url +'images/loader_atom.gif" />';
		var element_div = (typeof type === 'undefined')? 'div.'+ divID: 'div#'+ divID;
		
		$(element_div).block({
			message: HTML,
			css: {
					border: 'none'
					//color: '#ffffff'
				 },
			overlayCSS: { backgroundColor: '#ffffff' }
		});
	}
	
	// now, hiding the loading screen...
	function hideAJAXLoader(divID, type)
	{
		var element_div = (typeof type === 'undefined')? 'div.'+ divID: 'div#'+ divID;
		$(element_div).unblock();
	}

/* ############## ELEMENT BLOCKING LOADER [END] ############## */




//// function to "PERSONAL-INFO" form...
function show_display_form_AJAX(selected_tab, tab_obj)
{
	showAJAXLoader('content_area');
	
	$('.tab_content ul li').removeClass('select');
	$(tab_obj).parent('li').addClass('select');
	
	var selected_user_ID = $('#selected_user').val();
	var AJAX_URL = admin_base_url +"members/member_detail_utilities/display_tabbed_forms_AJAX/"+ selected_user_ID +"/"+ selected_tab;
	
	$.get(AJAX_URL, function(data) {
		$('#tab_content').html(data);
		
		show_tab_content('tab_details');
		
		hideAJAXLoader('content_area');
	});
}


// =============================================================================
// 			EDIT & CANCEL PART [AJAX CALL] - BEGIN
// =============================================================================

	//// function to show the "Edit Form" part...
	function show_edit_part_AJAX(tab_type, if_loader)
	{
		if(typeof if_loader === 'undefined')
			showAJAXLoader('tab_content', 'div-id');
		
		var selected_user_ID = $('#selected_user').val();
		var AJAX_URL = admin_base_url +"members/member_detail_utilities/show_edit_part_AJAX/"+ selected_user_ID +"/"+ tab_type;
		
		$.get(AJAX_URL, function(data) {
			$('.sec-detail').html(data);
			show_N_hide('edit');
			
			
			if(typeof if_loader === 'undefined')
				hideAJAXLoader('tab_content', 'div-id');
		});
	}
	
	
	//// function to cancel the "Edit Form" part...
	function cancel_edit_part_AJAX(tab_type, if_loader)
	{
		if(typeof if_loader === 'undefined')
			showAJAXLoader('tab_content', 'div-id');
		
		var selected_user_ID = $('#selected_user').val();
		var AJAX_URL = admin_base_url +"members/member_detail_utilities/cancel_edit_part_AJAX/"+ selected_user_ID +"/"+ tab_type;
		
		$.get(AJAX_URL, function(data) {
			$('.sec-detail').html(data);
			show_N_hide('display');
			
			
			if(typeof if_loader === 'undefined')
				hideAJAXLoader('tab_content', 'div-id');
		});
	}
	
	
	
	function show_N_hide(display_type) {
		
		if( display_type=='edit' ) {
			
			$('.sec-detail').find('.text-table').hide();
			if($('.sec-detail').find('.form-table').css('display')=='none'){
				$('.sec-detail').find('.form-table').show();
			}
			
		} else {
			
			$('.sec-detail').find('.form-table').hide();
			if($('.sec-detail').find('.text-table').css('display')=='none'){
				$('.sec-detail').find('.text-table').show();
			}
			
		}
	}

// =============================================================================
// 			EDIT & CANCEL PART [AJAX CALL] - END
// =============================================================================


//// to show tab-content...
function show_tab_content(tabClass)
{
	$('.'+ tabClass +' > div').hide();
	$('.'+ tabClass +' > div').show();
	$('.'+ tabClass +' .sec-title').show();
	$('.'+ tabClass +' .sec-detail').show();
	$('.'+ tabClass +' .title-body').show();
}