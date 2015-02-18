//================================add extra skill=======================================
function admin_add_extra_skill_div()
{

	var no_of_divs= parseInt( $("div[id^=skill_block_]:visible").length );
	
	no_of_divs++;

	tblID = "skill_container_div_"+ no_of_divs;
	skill_name_err = "err_skill_name_"+ no_of_divs;
	skill_date_frm_err = "err_skill_date_from_"+ no_of_divs;
	skill_date_to_err = "err_skill_date_to_"+ no_of_divs;
	
	$('#skill_html .copy_html_div').attr('id', tblID);
	$('.skill_name_err').attr('id',skill_name_err);
	$('.skill_from_date_err').attr('id',skill_date_frm_err);
	$('.skill_to_date_err').attr('id',skill_date_to_err);
	
	$('#skill_html .copy_html_div').attr('id','skill_block_'+no_of_divs);
	
	$('#skill_html .copy_html_div .close_extra_div a').attr('onClick','');
	$('#skill_html .copy_html_div .close_extra_div a').attr('onClick','removeExtraSkillDiv("hide_only"'+','+no_of_divs+')');
	
	for(i=1; i < no_of_divs; i++){
		$('#skill_block_'+no_of_divs).find('.html_frm_date').attr('id','datepicker'+i);
		$('#skill_block_'+no_of_divs).find('.html_to_date').attr('id','datepicker_sub'+i);
	}
	$('#skill_block_'+no_of_divs).find('.html_frm_date').attr('id','datepicker'+no_of_divs);
	$('#skill_block_'+no_of_divs).find('.html_to_date').attr('id','datepicker_sub'+i);
	
	
	
	var content = $('#skill_html').html();
	
	$('#h_skill').val(no_of_divs);
	
	$(content).appendTo('#add_more_skill_container');
	
	//alert(g_mindate);
	//alert(g_maxdate);
	
	$('#datepicker'+no_of_divs).datepicker({dateFormat : 'dd/mm/yy'});
	$('#datepicker'+no_of_divs).datepicker( "option", "minDate", g_mindate );
	$('#datepicker'+no_of_divs).datepicker( "option", "maxDate", g_maxdate );
	
	$('#datepicker_sub'+no_of_divs).datepicker({dateFormat : 'dd/mm/yy'});
	$('#datepicker_sub'+no_of_divs).datepicker( "option", "minDate", g_mindate );
	$('#datepicker_sub'+no_of_divs).datepicker( "option", "maxDate", g_maxdate );

	
	
	//alert(g_mindate); alert(g_maxdate);
	
	// var isDisabled = $( ".datepicker1" ).datepicker( "isDisabled" ); 
	// alert(isDisabled);
	
	
}// end of add_extra_div

function removeExtraSkillDiv(record_id, div_id)
{
	if(record_id != 'hide_only'){
		$.ajax({
			type: 'get',
			url: admin_base_url+'build_kingdom/charity_projects/deleteSkill/'+record_id,
			dataType: 'json',
	
			success: function (data, status) {
					$('#skill_block_'+div_id).remove();
					showUIMsg('Skill removed successfully.');
				}	// end of success function...
		});
	}
	else{
		$('#skill_block_'+div_id).remove();
	}

}


function setDatePicker() {
        $( "#add_more_skill_container").each(function() {
			
             $( this ).datepicker($.datepicker.regional['en']);
        });
}


function limitText(limitField, limitCount, limitNum) {
	if (limitField.value.length > limitNum) {
		limitField.value = limitField.value.substring(0, limitNum);
	} 
}





$(document).ready(function(arg) {
	
	// for AJAX page-submission... add
    optionsArr = { 
        beforeSubmit:  showBusyScreen,  // pre-submit callback 
        success:       validateFrm, // post-submit callback 
		url:		admin_base_url + "build_kingdom/charity_projects/add_info"
    }; 
 
 
 	// for AJAX page-submission... edit
    optionsArr_edit = { 
        beforeSubmit:  showBusyScreen,  // pre-submit callback 
        success:       validateEditFrm, // post-submit callback 
		url:		admin_base_url + "build_kingdom/charity_projects/update_info_ajax"
    }; 
 
 
 
})

function post_frm_ajax()
{
	$('#frm_project').ajaxSubmit(optionsArr);
	
	return false;
}

// validate ajax-submission...
function validateFrm(data)
{
	
	var data = JSON.parse(data);

	if(data.success==false) 
	{
		
		//hideBusyScreen();
		$('.error-message').each(function(i){
			$(this).attr('style','display:none');
		});
		
		
		for ( var id in data.arr_messages ){
			
			if( $('#err_'+id) != null ) {
				$('#err_'+id).html(data.arr_messages[id]);
				//console.log(data.arr_messages[id]);
				
				if(id =='date_to1' || id == 'skill_date_from_1'){
					$('#err_'+id).attr('style','display:block; margin-left: 118px; margin-top: -2px;');
				}
				else if(id == 'date_to2'){
					$('#err_'+id).attr('style','display:block; margin-left:300px; margin-top:-20px;');
				}
				else if(id ==  'skill_date_to_1'){
					$('#err_'+id).attr('style','display:block; margin-left: 302px; margin-top: -20px;');
				}
				else {
					
					$('#err_'+id).attr('style','display:block');
				}
			}
		}

		
	}
	else {
		showUIMsg(data.msg);		
		window.location.href = admin_base_url + "charity-projects.html";
		
		
	}
	// hide busy-screen...
	hideBusyScreen();
}








// edit

function post_frm_edit_ajax()
{
	$('#frm_edit_project').ajaxSubmit(optionsArr_edit);
	
	return false;
}

// validate ajax-submission...
function validateEditFrm(data)
{
	
	var data = JSON.parse(data);

	if(data.success==false) 
	{
		

		$('.error-message').each(function(i){
			$(this).attr('style','display:none');
		});
		
		
		for ( var id in data.arr_messages ){
			
			if( $('#err_'+id) != null ) {
				$('#err_'+id).html(data.arr_messages[id]);
				//console.log(data.arr_messages[id]);
				
				if(id =='date_to1' || id == 'skill_date_from_1'){
					$('#err_'+id).attr('style','display:block; margin-left: 118px; margin-top: -2px;');
				}
				else if(id == 'date_to2'){
					$('#err_'+id).attr('style','display:block; margin-left:300px; margin-top:-20px;');
				}
				else if(id ==  'skill_date_to_1'){
					$('#err_'+id).attr('style','display:block; margin-left: 302px; margin-top: -20px;');
				}
				else {
					
					$('#err_'+id).attr('style','display:block');
				}
			}
		}

		
	}
	else {
		showUIMsg(data.msg);		
		window.location.href = admin_base_url + "charity-projects.html";
		
		
	}
	// hide busy-screen...
	hideBusyScreen();
}






function delete_confirm_(file_id)
{
	//alert(file_id);
	$('#i_del_id').val( file_id );
	show_dialog('delete-page-popup');
	//return false;
}
function delete_()
{
	  var selected_id = parseInt( $('#i_del_id').val() );
		  //alert(selected_id);
	  if( selected_id!=0 )
	  {
		  showUIMsg("Deleted successfully.");
		  var delURL = admin_base_url + 'build_kingdom/charity_projects/delete_information/'+ selected_id;
		  window.location.href = delURL;
	  } else {
	  
		  var msg = "Sorry an error has occured, Please try again";
		  showUIMsg(msg);
		  
		  hide_dialog();
		  
	  }
}



function changeStatus(id,i_status ,cur_status, project_id)
{ 
	var URL = admin_base_url +'build_kingdom/charity_projects/change_status/';
	showBusyScreen();
	if(ajax_req!=null) {
		ajax_req.abort();
	}
	ajax_req = $.ajax({
		type: 'POST',
		url:URL,
		dataType: 'json',
		data: ({ 'record_id' : id
				 ,'i_status' : i_status
				 ,'cur_status' : cur_status
				 , 'project_id': project_id
			 }),
		success: function (data, status) {
						if(cur_status == 'accepted'){
							hide_dialog();
						}
						hideBusyScreen();
						if(data.result== true) {
							showUIMsg(data.msg);
							//$('#'+data.u_id+'_status').html(data.action_txt);
							$('#table_content').html('');
							$('#table_content').html(data.skill_list);
							$('#skill_chart').html('');
							$('#skill_chart').html(data.chart_html);
							
						}
						else if(data.result==false ){
							showUIMsg(data.msg);
						}
				},
		error: function (data, status, e) {
							hideBusyScreen();
							showUIMsg(data.msg);
		}
	});
}

/* DELETE DONATION */

function ShowDonationDelete(file_id)
{
	//alert(file_id);
	$('#i_donation_id').val( file_id );
	show_dialog('delete-page-popup');
	//return false;
}
function deleteDonation()
{
	  var selected_id = parseInt( $('#i_donation_id').val() );
		  //alert(selected_id);
	  if( selected_id!=0 )
	  {
		  showUIMsg("Deleted successfully.");
		  
		  $.ajax({
			type: 'get',
			url: admin_base_url+'build_kingdom/charity_projects/delete_donation/'+ selected_id,
			dataType: 'json',
	
			success: function (data, status) {
				
					hide_dialog();
					if(data.success == true){
						$('#req_row_'+selected_id).hide();
						showUIMsg('Donation request deleted successfully.');
					}
				}	// end of success function...
		});
	  } else {
	  
		  var msg = "Sorry an error has occured, Please try again";
		  showUIMsg(msg);
		  hide_dialog();
		  
	  }
}

function showDonorList(block_dt,skill_name){
	
	showUILoader_nodialog('<img src="'+base_url+'images/loading_big.gif" width="50"/> ');
	var project_id = $('#project_id').val();
	
	$.ajax({
			type: 'get',
			url: admin_base_url+'build_kingdom/charity_projects/dayWiseSkillRequestAjaxPagination/'+ project_id,
			data: {'search_daywise':'Y',
				   'block_dt': block_dt,
				   'skill_name':skill_name},
	
			success: function (data, status) {
						
						$('#table_content').html('');
						$('#table_content').html(data);
						hideUILoader_nodialog();
				}	// end of success function...
		});
	
}


/*=========Open or close project status ============*/

function changeProjectStatus(id,i_status ,cur_status)
{ 
	var URL = admin_base_url +'build_kingdom/charity_projects/change_project_status/';
	showBusyScreen();
	if(ajax_req!=null) {
		ajax_req.abort();
	}
	ajax_req = $.ajax({
		type: 'POST',
		url:URL,
		dataType: 'json',
		data: ({ 'record_id' : id
				 ,'i_status' : i_status
				 ,'cur_status' : cur_status
			 }),
		success: function (data, status) {
						
						hideBusyScreen();
						if(data.result== true) {
							showUIMsg(data.msg);
							$('#'+id+'_status').html(data.action_txt);
							$('#table_content').html();
							$('#table_content').html(data.html);
							
						}
						else if(data.result==false ){
							showUIMsg(data.msg);
						}
				},
		error: function (data, status, e) {
							hideBusyScreen();
							showUIMsg(data.msg);
		}
	});
}


/* SHOW PROJECT DETAILS */

function  show_project_details(project_id){
	
	showUILoader_nodialog('<img src="'+base_url+'images/loading_big.gif" width="50"/> ');
	
	$.ajax({
			type: 'get',
			url: admin_base_url+'build_kingdom/charity_projects/getProjectDetail/'+ project_id,
			dataType: 'json',
			success: function (data, status) {
						
						$('#project_content').html('');
						$('#project_content').html(data.html);
						hideUILoader_nodialog();
						show_dialog('project-details');

				}	// end of success function...
		});

}

/* SHOW PROJECT skill donor */

function  show_project_skill_donor(project_id){
	
	showUILoader_nodialog('<img src="'+base_url+'images/loading_big.gif" width="50"/> ');
	
	$.ajax({
			type: 'get',
			url: admin_base_url+'build_kingdom/charity_projects/getSkillDonor/'+ project_id,
			dataType: 'json',
			success: function (data, status) {
						
						$('#skill_donor_content').html('');
						$('#skill_donor_content').html(data.html);
						hideUILoader_nodialog();
					    show_dialog('skill-donations')
				}	// end of success function...
		});

}

/* SHOW PROJECT fund donor DETAILS */

function  show_project_fund_donor(project_id){
	
	showUILoader_nodialog('<img src="'+base_url+'images/loading_big.gif" width="50"/> ');
	
	$.ajax({
			type: 'get',
			url: admin_base_url+'build_kingdom/charity_projects/getFundDonor/'+ project_id,
			dataType: 'json',
			success: function (data, status) {
						
						$('#fund_donor_content').html('');
						$('#fund_donor_content').html(data.html);
						hideUILoader_nodialog();
					    show_dialog('donors')
				}	// end of success function...
		});

}




