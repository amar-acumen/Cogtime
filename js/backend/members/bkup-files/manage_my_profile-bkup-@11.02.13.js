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
	//alert(data);
	var result_obj = JSON.parse(data);
	//alert(result_obj.arr_messages);
	
	if(result_obj.result=='success') {
		
		showUIMsg(result_obj.msg);
		window.location.href = result_obj.redirect;
		//window.location.href = base_url + result_obj.redirect;
	}

	$('.error_msg').each(function(i){
		$(this).attr('style','display:none');
		//$(this).addClass('td-error-message');
	});
    $('div[id^=err_]').html(''); 
	
	if(result_obj.result=='error') {
		/*
		
	    $('div[id^=err_msg]').addClass('td-error-message'); 
		for ( var id in result_obj.arr_messages ){
			
			if( result_obj.arr_messages[id] != '' ) {
				//divID = "div[id^='"+ id +"']";
				divID = result_obj.arr_messages[id] +"> div#err_msg";
				//alert(divID);
				$(divID).remove();
				
				$('#'+id).after(result_obj.arr_messages[id]);
			}
			
		}
		
	*/
	
	    //$('div[id^=err_msg]').addClass('td-error-message'); 
		/*for ( var id in result_obj.arr_messages ){
			
			if( result_obj.arr_messages[id] != '' ) {
				//divID = "div[id^='"+ id +"']";
				divID = result_obj.arr_messages[id] +"> td#err_";
				//alert(divID);
				$(divID).remove();
				alert((id));
				$('#'+id).after(result_obj.arr_messages[id]);
				
			}
			
		}*/
		
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
	//alert(data);
	if(result_obj.result=='success') {
		
		showUIMsg(result_obj.msg);
		//closeDiv('2');
		window.location.href = result_obj.redirect;
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
    }; 
 
	frm_obj2 = $('#frmManageEduProfile');
	$(frm_obj2).ajaxSubmit(optionsArr);
	
	return false; 
}


// validate ajax-submission...
function validateEduFrm(data)
{
	
	var result_obj = JSON.parse(data);
	//alert(data);
	if(result_obj.result=='success') {
		
		showUIMsg(result_obj.msg);
		window.location.href = result_obj.redirect;
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
		//closeDiv('4');
		window.location.href = result_obj.redirect;
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
		window.location.href = result_obj.redirect;
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

/*
//======================================for add an extra edu div===============================================
function add_extra_edu_div(id)
{
	
	var no_of_divs=$('#'+id+'_h').val();
	no_of_divs++;
	
	var content='<div id="'+id+'_'+no_of_divs+'"><h4 class="sepration"></h4><a class="close" href="javascript:void(0);" style="margin:5px 5px;" onclick="closeExtraEduDiv('+"'"+id+"'"+','+no_of_divs+')" ><img src="images/icons/close.png" alt=""></a>';
	content+=$('#'+id).html()+"</div>";
	
	
	if(no_of_divs<=5)
	{
		$('#'+id+'_h').val(no_of_divs);
		
		$(content).appendTo('#add_more_'+id);	
		$('#'+id+'_'+no_of_divs).find('input').val('');
	
		$('#'+id+'_'+no_of_divs).find('#dbId').val('0');
		
		
		
	}
	else
	{
		showUIMsg("Can not add more.");
	}
	
	
	
}// end of add_extra_div

function closeExtraEduDiv(id, no_of_divs)
{

	var db_id=$("#"+id+"_"+no_of_divs).find('#dbId').val();
	
	showBusyScreen();
	$.ajax({
		   "url"	:	base_url+"logged/my_profile/delete_edu_info",
		   "data"	:	{"db_id" : db_id},
		   "type"	:	"post",
		   "success":	function(data)
						{
							
							hideBusyScreen();
							var r=JSON.parse(data);
							showUIMsg(r.msg);
							
						}
		   });

	$('#'+id+'_'+no_of_divs).remove();	
	var i=$('#'+id+'_h').val();
	i=i-1;
	$('#'+id+'_h').val(i);
	//for rearrenge the remain divs
	$.each($('div[id^='+id+'_]'),function(index){

									$(this).attr('id',id+'_'+i);
									$(this).children('.close').attr('onClick','closeExtraEduDiv("'+id+'",'+i+')');
									i++;	
									
										  });
	
}


//================================add extra work div=======================================

function add_extra_work_div(id)
{
	
	var no_of_divs=$('#'+id+'_h').val();
	no_of_divs++;
	
	var content='<div id="skill_div_'+no_of_divs+'"><h4 class="sepration"></h4><a class="close" href="javascript:void(0);" style="margin:5px 5px;" onclick="closeExtraWorkDiv('+"'"+id+"'"+','+no_of_divs+')" ><img src="images/icons/close.png" alt=""></a>';
	content+=$('#'+id).html()+"</div>";
	

	
	//alert(content);
	
	
	if(no_of_divs<=5)
	{
		$('#'+id+'_h').val(no_of_divs);
		//$(content).appendTo('#add_more_'+id);
		
		if(id=='work_div')
		{
			$(content).appendTo('#add_more_'+id);
			
			var sel_to=$('#'+id+'_'+no_of_divs).find('#mnth_to').attr('id');
			
			$('#help_div').find('#mnth_to').attr('id','mnth_to_'+no_of_divs);
			$('#help_div').find('#mnth_to').attr('id','mnth_nw_'+no_of_divs);
			
			
			
		}
		else
		{
			$(content).appendTo('#add_more_'+id);	
		}
	
	$('#'+id+'_'+no_of_divs).find('#dbId').val('0');
	var r= $('#'+id+'_'+no_of_divs).find('#dbId').val();
	alert(r);
		
	}
	else
	{
		showUIMsg("Can not add more.");
	}
	
	
	
}// end of add_extra_div

function closeExtraWorkDiv(id, no_of_divs)
{
	var db_id=$("#"+id+"_"+no_of_divs).find('#dbId').val();
	alert(db_id);
	
	
	$.ajax({
		   "url"	:	base_url+"logged/my_profile/delete_work_info",
		   "data"	:	{"db_id" : db_id},
		   "type"	:	"post",
		   "success":	function(data)
		   			{
			   		
					}
		   });
	
	$('#'+id+'_'+no_of_divs).remove();	
	var i=$('#'+id+'_h').val();
	i=i-1;
	$('#'+id+'_h').val(i);
	//for rearrenge the remain divs
	$.each($('div[id^='+id+'_]'),function(index){

									$(this).attr('id',id+'_'+i);
									$(this).children('.close').attr('onClick','closeExtraWorkDiv("'+id+'",'+i+')');
									i++;	
									
										  });
	
}



//================================add extra skill=======================================
function add_extra_skill_div(id)
{
	var skills = $('#'+id+'_h').val()	;
	var now_skills = skills;
	now_skills++;

	var test=$('#skill_div_'+skills).html();

	
	if(now_skills<=5)
	{
		$('#extra_skill_divs').append('<div id="'+id+'_'+now_skills+'"><div class="lable01">Skill '+now_skills+':</div><div class="field01"><input name="txt_skill[]" type="text" style="width:230px;" /><input type="hidden" name="skill_db_id[]" id="'+now_skills+'" value="0"/> </div><a  class="close_class" href="javascript:void(0);"  onClick="closeExtraSkillDiv('+"'"+id+"'"+','+now_skills+')" ><img src="images/icons/close.png" alt=""></a><div class="clr"></div> ');
		$('#'+id+'_h').val(now_skills);
	
		
	}
	else
	{
		showUIMsg("Can not add more.");	
	}
	
}
function closeExtraSkillDiv(id,skill)
{
	
	var db_id=$('#skill_div_'+skill).find('#'+skill).val();
	if(db_id)
		
	

	var total_skills=$('#'+id+'_h').val()-1;
	$('#'+id+'_h').val(total_skills);
	
	showBusyScreen();
	$.ajax({
		   "url"	:	base_url+"logged/my_profile/delete_skill_info",
		   "data"	:	{"db_id" : db_id},
		   "type"	:	"post",
//		   "dataType": "json",
		   "success":	function(data)
						{
							hideBusyScreen();
							var r=JSON.parse(data);
							showUIMsg(r.msg);
							
						}
		   });
	

	$('#'+id+'_'+skill).remove();
	
	$.each($('div[id^=skill_div_]'),function(i){
												
						var j=i+1;
						
						$(this).attr('id',id+'_'+j);
						var now_id=$(this).attr('id');
						
						$(this).children('.close_class').attr("onclick", "");
						$(this).children('.close_class').attr('onClick','closeExtraSkillDiv("'+id+'",'+j+')');
						$(this).children('.lable01').text('Skill '+j+':');
												
												});
	
	skill=skill-1;
	$('#'+id+'_h').val(total_skills);
	
	
}
*/

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
