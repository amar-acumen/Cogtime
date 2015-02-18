function modify_my_profile_ajax()
{
	
	// for AJAX page-submission...
    optionsArr = { 
        beforeSubmit:  showBusyScreen,  // pre-submit callback 
        success:       validateFrm // post-submit callback 
    }; 
 
	frm_obj = $('#frmManageProfile');
	$(frm_obj).ajaxSubmit(optionsArr);
	return false; 
}





// validate ajax-submission...
function validateFrm(data)
{
	var result_obj = JSON.parse(data);
	
	if(result_obj.result=='success') {
		
		showUIMsg(result_obj.msg);
		
		//// NEW CODE
			cancel_edit_part_AJAX('personal', 'no-show');
		//// NEW CODE
		
	}

	$('.error_msg').each(function(i){
		$(this).attr('style','display:none');
		//$(this).addClass('td-error-message');
	});
    $('div[id^=err_]').html(''); 
	
	if(result_obj.result=='error') {
		
		$('.error_msg').each(function(i){
			$(this).attr('style','display:none');
		});
		
		
		for ( var id in result_obj.arr_messages ){
			//alert(data.arr_messages[id]);
			if( $('#err_'+id) != null ) {
				$('#err_'+id).html(result_obj.arr_messages[id]);
				$('#err_'+id).css('display', 'block');
			}
		}
	
	
	}
	
	// hide busy-screen...
	hideBusyScreen();
}


// BASIC INFO 
function modify_my_basic_profile_ajax()
{
	// for AJAX page-submission...
    optionsArr = { 
        beforeSubmit:  showBusyScreen,  // pre-submit callback 
        success:       validateBasicFrm // post-submit callback 
    }; 
 
	frm_obj2 = $('#frmManageBasicProfile');
	$(frm_obj2).ajaxSubmit(optionsArr);
	
	return false; 
}


// validate ajax-submission...
function validateBasicFrm(data)
{
	var result_obj = JSON.parse(data);
	if(result_obj.result=='success') {
		
		showUIMsg(result_obj.msg);
		
		//// NEW CODE
			cancel_edit_part_AJAX('basic', 'no-show');
		//// NEW CODE
		
	}

	$('.error_msg').each(function(i){
		$(this).attr('style','display:none');
	});
    $('div[id^=err_msg]').html(''); 
	if(result_obj.result=='error') {
	
		for ( var id in result_obj.arr_messages ){
			
			if( result_obj.arr_messages[id] != '' ) {
				divID = result_obj.arr_messages[id] +"> div#err_msg";
				//alert(divID);
				$(divID).remove();
				$('#'+id).after(result_obj.arr_messages[id]);
			}
			
		}
		
	}
	
	// hide busy-screen...
	hideBusyScreen();
}


// EDU INFO 
function modify_my_edu_profile_ajax()
{
	
	// for AJAX page-submission...
    optionsArr = { 
        beforeSubmit:  showBusyScreen,  // pre-submit callback 
        success:       validateEduFrm // post-submit callback 
		//dataType:  'json'
    }; 
 
	frm_obj2 = $('#frmManageEduProfile');
	$(frm_obj2).ajaxSubmit(optionsArr);
	
	return false; 
}


// validate ajax-submission...
function validateEduFrm(data)
{
	var result_obj = JSON.parse(data);
	//var result_obj = data;
	if(result_obj.result=='success') {
		
		showUIMsg(result_obj.msg);
		
		//// NEW CODE
			cancel_edit_part_AJAX('edu', 'no-show');
		//// NEW CODE
		
	}

	$('.error_msg').each(function(i){
		$(this).attr('style','display:none');
	});
    $('div[id^=err_msg]').html(''); 
	if(result_obj.result=='error') {
	
		for ( var id in result_obj.arr_messages ){
			
			if( result_obj.arr_messages[id] != '' ) {
				divID = result_obj.arr_messages[id] +"> div#err_msg";
				//alert(divID);
				$(divID).remove();
				$('#'+id).after(result_obj.arr_messages[id]);
			}
			
		}
		
	}
	
	// hide busy-screen...
	hideBusyScreen();
}


//work info
function modify_my_work_profile_ajax()
{
	// for AJAX page-submission...
    optionsArr = { 
        beforeSubmit:  showBusyScreen,  // pre-submit callback 
        success:       validateWorkFrm // post-submit callback 
    }; 
 
	frm_obj2 = $('#frmManageWorkProfile');
	//$('#disabled_mnth_to').removeAttr('disabled');
	//$('#disabled_year_to').removeAttr('disabled');
	$(frm_obj2).ajaxSubmit(optionsArr);
	
	return false; 
}


// validate ajax-submission...
function validateWorkFrm(data)
{
	//alert(data)
	var result_obj = JSON.parse(data);
	//alert(data);
	if(result_obj.result=='success') {
		
		showUIMsg(result_obj.msg);
		
		//// NEW CODE
			cancel_edit_part_AJAX('work', 'no-show');
		//// NEW CODE
		
	}

	$('.error_msg').each(function(i){
		$(this).attr('style','display:none');
	});
    $('div[id^=err_msg]').html(''); 
	if(result_obj.result=='error') {
	
		for ( var id in result_obj.arr_messages ){
			
			if( result_obj.arr_messages[id] != '' ) {
				divID = result_obj.arr_messages[id] +"> div#err_msg";
				//alert(divID);
				$(divID).remove();
				$('#'+id).after(result_obj.arr_messages[id]);
			}
			
		}
		
	}
	
	// hide busy-screen...
	hideBusyScreen();
}


//skill info
function modify_my_skill_profile_ajax()
{
	// for AJAX page-submission...
    optionsArr = { 
        beforeSubmit:  showBusyScreen,  // pre-submit callback 
        success:       validateSkillFrm // post-submit callback 
    }; 
 
	frm_obj2 = $('#frmManageSkillProfile');
	$(frm_obj2).ajaxSubmit(optionsArr);
	
	return false; 
}


// validate ajax-submission...
function validateSkillFrm(data)
{
	
	var result_obj = JSON.parse(data);
	
	if(result_obj.result=='success') {
		
		showUIMsg(result_obj.msg);
		
		//// NEW CODE
			cancel_edit_part_AJAX('skills', 'no-show');
		//// NEW CODE
		
	}

	$('.error_msg').each(function(i){
		$(this).attr('style','display:none');
	});
    $('div[id^=err_msg]').html(''); 
	if(result_obj.result=='error') {
	
		for ( var id in result_obj.arr_messages ){
			
			if( result_obj.arr_messages[id] != '' ) {
				divID = result_obj.arr_messages[id] +"> div#err_msg";
				//alert(divID);
				$(divID).remove();
				$('#'+id).after(result_obj.arr_messages[id]);
			}
			
		}
		
	}
	
	// hide busy-screen...
	hideBusyScreen();
}



// account info

function modify_my_account_ajax()
{
	// for AJAX page-submission...
    optionsArr = { 
        beforeSubmit:  showBusyScreen,  // pre-submit callback 
        success:       validateAccntFrm // post-submit callback 
    }; 
 
	frm_obj2 = $('#frmManageAccntProfile');
	$(frm_obj2).ajaxSubmit(optionsArr);
	
	return false; 
}


// validate ajax-submission...
function validateAccntFrm(data)
{
	
	var result_obj = JSON.parse(data);
	//alert(data);
	if(result_obj.result=='success') {
		
		showUIMsg(result_obj.msg);
		//closeDiv('2');
		window.location.href = result_obj.redirect;
	}
$('div[id^=err_msg]').remove();
	$('.error_msg').each(function(i){
		$(this).attr('style','display:none');
	});
    $('div[id^=err_msg]').html(''); 
	if(result_obj.result=='error') {
	
		for ( var id in result_obj.arr_messages ){
			
			if( result_obj.arr_messages[id] != '' ) {
				divID = result_obj.arr_messages[id] +"> div#err_msg";
				//alert(divID);
				$(divID).remove();
				$('#'+id).after(result_obj.arr_messages[id]);
			}
			
		}
		
	}
	
	// hide busy-screen...
	hideBusyScreen();
}
