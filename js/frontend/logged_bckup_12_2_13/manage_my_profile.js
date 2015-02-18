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
   //alert(result_obj.result);
	if(result_obj.result=='success') {
		
		showUIMsg(result_obj.msg);
		closeDiv('1');
		//window.location.href = result_obj.redirect;
		//window.location.href = base_url + result_obj.redirect;
	}

	$('.error_msg').each(function(i){
		$(this).attr('style','display:none');
	});
    $('div[id^=err_msg]').html(''); 
	if(result_obj.result=='error') {
	
		for ( var id in result_obj.arr_messages ){
			
			if( result_obj.arr_messages[id] != '' ) {
				//divID = "div[id^='"+ id +"']";
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
		closeDiv('2');
		window.location.href = base_url + result_obj.redirect;
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
		closeDiv('3');
		window.location.href = base_url + result_obj.redirect;
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
        //beforeSubmit:  showBusyScreen,  // pre-submit callback 
        success:       validateWorkFrm // post-submit callback 
    }; 
 
	frm_obj2 = $('#frmManageWorkProfile');
    $('#disabled_mnth_to').removeAttr('disabled');
	$('#disabled_year_to').removeAttr('disabled');
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
		closeDiv('4');
		window.location.href = base_url + result_obj.redirect;
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
	//alert(data);
	if(result_obj.result=='success') {
		
		showUIMsg(result_obj.msg);
		closeDiv('5');
		window.location.href = base_url + result_obj.redirect;
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


//======================================for add an extra edu div==============================================
function add_extra_edu_div()
{
	
	var no_of_divs=$('#edu_div_h').val();
	no_of_divs++;
	
	/*var content='<div id="edu_div_'+no_of_divs+'"><h4 class="sepration"></h4><a class="close" href="javascript:void(0);" style="margin:5px 5px;" onclick="closeExtraEduDiv('+"'edu_div_'"+','+no_of_divs+')" ><img src="images/icons/close.png" alt=""></a>';
	content+=$('#'+id).html()+"</div>";
	*/
	
	tblID = "work_"+ no_of_divs;
	$('#hidden_edu_div table').attr('id', tblID);
	//$('#hidden_edu_div table .radio_cur_emp').val(no_of_divs);
	
	content = $('#hidden_edu_div').html();
	alert(content);
	if(no_of_divs<=5)
	{
		$('#edu_div_h').val(no_of_divs);
		
		$(content).appendTo('#add_more_edu_div');	
		
		 //$("div#add_more_edu_div  select.txt_country_edu1").msDropDown();
		 //$("div#add_more_edu_div  select.txt_country_edu1").hide();
		 
         //$(".txt_country").hide();
		/*$('#'+id+'_'+no_of_divs).find('input').val('');
		$('#'+id+'_'+no_of_divs).find('select').val('-1');
	*/
		//$('#'+id+'_'+no_of_divs).find('#dbId').val('0');
		
		
		
	}
	else
	{
		showUIMsg("Can not add more.");
	}
	
	
	
}// end of add_extra_div

function closeExtraEduDiv(id, no_of_divs)
{

	//var db_id=$("#"+id+"_"+no_of_divs).find('#dbId').val();
	
	/*showBusyScreen();
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
	*/
	

	$('#'+id+'_'+no_of_divs).hide();
/*	
	showUIMsg("Removed.");
		
	var i=$('#'+id+'_h').val();
	i=i-1;
	$('#'+id+'_h').val(i);
	//for rearrenge the remain divs
	$.each($('div[id^='+id+'_]'),function(index){

									$(this).attr('id',id+'_'+i);
									$(this).children('.close').attr('onClick','closeExtraEduDiv("'+id+'",'+i+')');
									i++;	
									
										  });
*/	
}


//================================add extra work div=======================================

function add_extra_work_div()
{
	
	var no_of_divs=$('#work_div_h').val();

	no_of_divs++;

	/*var content='<div id="work_div_'+no_of_divs+'"><h4 class="sepration"></h4><a class="close" href="javascript:void(0);" style="margin:5px 5px;" onclick="closeExtraWorkDiv('+"'work_div'"+','+no_of_divs+')" ><img src="images/icons/close.png" alt=""></a>';
	content+=$('#work_div_1').html()+"</div>";
*/	

/*	alert(content);

	var content='<div id="work_div_'+no_of_divs+'"><h4 class="sepration"></h4><a class="close" href="javascript:void(0);" style="margin:5px 5px;" onclick="closeExtraWorkDiv('+"'work_div'"+','+no_of_divs+')" ><img src="images/icons/close.png" alt=""></a><div class="lable01">Employer:</div><div class="field01"><input name="txt_work_company[]" type="text" value="" style="width:230px;" /></div><div class="clr"></div><div class="lable01">Work City:</div><div class="field01"><input name="txt_work_city[]" type="text" value="" style="width:230px;" /></div><div class="clr"></div><div class="lable01">Work State:</div><div class="field01"><input name="txt_work_state[]" type="text" value=""  style="width:230px;"/></div><div class="clr"></div><div class="lable01">Work Country:</div><div class="field01"><input name="txt_work_country[]" type="text" value="" style="width:230px;" /></div><div class="clr"></div><div class="lable01">Position:</div><div class="field01"><input name="txt_work_position[]" type="text"  value="" style="width:230px;"/></div><div class="clr"></div><div class="lable01">Period:</div><div class="field01" >From: <input id="" class="date_work_from" type="text" readonly="readonly" name="date_work_from" value="" style=" width: 60px;" /> <img class="calender"  onclick="$(this).prev().focus()" alt="" src="images/icons/cal.png">&nbsp;To: <input id="" class="date_work_to" type="text" readonly="readonly" name="date_work_to" value="" style=" width: 60px;" /> <img class="calender"  onclick="$(this).prev().focus()" alt="" src="images/icons/cal.png"></div><div class="clr"></div><div class="lable01">&nbsp;</div><div class="field01"><input name="is_current_employer" class="redio_cur_emp" type="radio" value="0"  /> Current Employer</div><input type="hidden" name="db_id[]" id="dbId" value=""></div>';
*/	



	tblID = "work_"+ no_of_divs;
	$('#hidden_work_div').attr('id', tblID);
	$('#hidden_work_div .radio_cur_emp').val(no_of_divs);
	
	content = $('#hidden_work_div').html();
	if(no_of_divs<=5)
	{
		/*var first_radio=false;
		
		if($('#work_div_1').find('.radio_cur_emp').attr('checked'))
		{
			first_radio=true;	
		}*/
		
		$('#work_div_h').val(no_of_divs);
		$(content).appendTo('#add_more_work_div');
		
		
		$('#work_div_'+no_of_divs).find('input').val('');
		//$('#work_div_'+no_of_divs).find('#dbId').val('');
		/*$('#work_div_'+no_of_divs).find('select').val('-1');
		$('#work_div_'+no_of_divs).find('.radio_cur_emp').attr('onclick','current_emp('+no_of_divs+')');
		$('#work_div_'+no_of_divs).find('select').removeAttr('disabled');
		$('#work_div_'+no_of_divs).find('select').removeAttr('id');
		
		$('#work_div_'+no_of_divs).find('.radio_cur_emp').prop('checked', false);
		if(first_radio)
			$('#work_div_1').find('.radio_cur_emp').attr('checked','checked');
		*/
		/*$.datepicker.setDefaults($.datepicker.regional['']);
	    $(".date_work_from").datepicker($.datepicker.regional['en']);
	    $(".date_work_to").datepicker($.datepicker.regional['en']);
		*/
		$(".profile_combo1").msDropDown();
       	
		
		$("div#add_more_work_div  select.txt_country_work1").msDropDown();

		
		
	}
	else
	{
		showUIMsg("Can not add more.");
	}
	
}// end of add_extra_div

function closeExtraWorkDiv(id, no_of_divs)
{
//	var db_id=$("#"+id+"_"+no_of_divs).find('#dbId').val();
	//alert(db_id);
	
	
	/*$.ajax({
		   "url"	:	base_url+"logged/my_profile/delete_work_info",
		   "data"	:	{"db_id" : db_id},
		   "type"	:	"post",
		   "success":	function(data)
		   			{
						hideBusyScreen();
						var r=JSON.parse(data);
						showUIMsg(r.msg);
					}
		   });
	*/


/*	$('#'+id+'_'+no_of_divs).remove();	
	
	showUIMsg("Removed.");
	
	var i=$('#'+id+'_h').val();
	i=i-1;
	$('#'+id+'_h').val(i);
	//for rearrenge the remain divs
	$.each($('div[id^='+id+'_]'),function(j){
									var k=j+1;

									$(this).attr('id',id+'_'+k);
									$(this).children('.close').attr('onClick','closeExtraWorkDiv("'+id+'",'+k+')');
									i++;
										
									$(this).find('.radio_cur_emp').attr('onclick','current_emp('+k+')');
									
										  });
*/	
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
	
/*	var db_id=$('#skill_div_'+skill).find('#'+skill).val();
	//if(db_id)
		
	

	var total_skills=$('#'+id+'_h').val()-1;
	$('#'+id+'_h').val(total_skills);
*/	
	/*showBusyScreen();
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
	*/
	

/*	$('#'+id+'_'+skill).remove();
	
	showUIMsg("Removed.");
	
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
	
*/	
}
function current_emp(div_no)
{
	alert(div_no);
	
	/*$('.year_to').removeAttr('disabled');
	$('.mnth_to').removeAttr('disabled');
	$('.year_to').removeAttr('id');
	$('.mnth_to').removeAttr('id');
	*/
	$('#work_div_'+div_no).find('.year_to').attr('disabled','disabled');
	$('#work_div_'+div_no).find('.mnth_to').attr('disabled','disabled');
	$('#work_div_'+div_no).find('.year_to').attr('id','disabled_year_to');
	$('#work_div_'+div_no).find('.mnth_to').attr('id','disabled_mnth_to');
}