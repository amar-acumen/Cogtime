function toggleProjectMenu(project_id, tabname){

	 $('.donate li a').removeClass('addselect');
    // $('.donate-time-donors-content >.donate-section-box').hide();
	
	//$('#'+tabname+'_'+'donate_'+project_id).css('display')=="block"){
     $('#'+tabname+'_'+project_id).addClass('addselect');
     $('#'+tabname+'_'+'donate_'+project_id).slideToggle('slow');
}

function showTimeDonor(project_id){
    $('#time_donor_'+project_id).slideToggle('slow');
}

	
function donateFundToProject(project_id){ 
					
 var datatosend=$("#frm_project_donation"+project_id).serialize(); 
 showUILoader_nodialog('<img src="'+base_url+'images/loading_big.gif" width="50"/> ');
 showDelayMsg('Please do not refresh , your card is processing...');

						  
  $.ajax({
	 type: "POST",
	 url: base_url+'logged/build_the_kingdom/donateFundToProject',
	 data: datatosend,
	 dataType: 'json',
	 success: function(data){
		 
		 $('.error-message').each(function(i){
			$(this).attr('style','display:none');
		 });
		 hideDelayMsg();
		 hideUILoader_nodialog();
		 if(data.success ==false){
			
			  for ( var id in data.arr_messages ){
				  //alert(data.arr_messages[id]);
				  if( $('#err_'+id) != null ) {
					  $('#err_'+id).html(data.arr_messages[id]);
					  $('#err_'+id).attr('style', 'display:block;margin-left:125px;');
					  
				  }
			  }
			  showUIMsg(data.msg);
		 }
		 else{
				$("#frm_project_donation"+project_id)[0].reset();
				$("#donation_rec"+project_id).html(data.total_donation);
				$('#fund_donate_'+project_id).slideToggle('slow');
				showUIMsg(data.msg);
		 }
			
	 }
   });	 
}
 
function send_project_query(project_id, project_name){ 

  showUILoader_nodialog('<img src="'+base_url+'images/loading_big.gif" width="50"/> ');
  $.ajax({
	 type: "POST",
	 url: base_url+'logged/build_the_kingdom/sendProjectQuery',
	 data: {'s_content':$('#ta_query'+project_id).val(),
			'project_name' : project_name,
			'project_id': project_id},
	 dataType: 'json',
	 success: function(data){
		 
		 hideUILoader_nodialog();
		 if(data.success ==false){
			showUIMsg(data.msg);
		 }
		 else{
				$('#ta_query'+project_id).val('');
				$('#info_donate_'+project_id).slideToggle('slow');
				showUIMsg(data.msg);
		 }
			
	 }
   });	 
}

/// POST RESUME AND DONATE SKILL

function donateSkill(project_id){	 
	// for AJAX page-submission...
    var options = { 
        beforeSubmit:  showLoading,   // pre-submit callback 
        success:       validateFrm // post-submit callback 
    }; 
 
    // bind to the form's submit event 
    //$('#donate_skill'+project_id).submit(function() {
        $('#donate_skill'+project_id).ajaxSubmit(options);
        return false;
    //});
}


function showLoading()
{
   //showUILoader_nodialog('<img src="'+base_url+'images/loading_big.gif" width="50"/> ');
   
	var j = 0;
	$(':checkbox:checked').each(function(i){
		if($(this).attr('id').substr(0, 9) == 'chk_skill') {
			j++;
		}
	});
	
	if(j == 0){
		showUIMsg('Please select a skill.');
		return false;
	}
	else{
		return true;
	}
}


// validate ajax-submission...
function validateFrm(data)
{

	var data = JSON.parse(data);
	$('.error-message').each(function(i){
			$(this).attr('style','display:none');
	});
	if(data.result==false) 
	{
		
		for ( var id in data.arr_messages ){
			//alert(data.arr_messages[id]);
			if( $('#err_'+id) != null ) {
				$('#err_'+id).html(data.arr_messages[id]);
				
				if(id == 'file_cv'+data.p_id){
					$('#err_'+id).attr('style', 'display:block;margin-left: 125px; ');
				}
				else
				$('#err_'+id).attr('style', 'display:block;margin-left: 125px; margin-top: -5px;');
			}
		}
		showUIMsg(data.msg);
		
	}
	else {
		  // clearing form
		  $('#donate_skill'+data.p_id)[0].reset();
     	  
		  $('#time_'+'donate_'+data.p_id).hide();
     	  $('#time_'+data.p_id).removeClass('addselect');
		  showUIMsg(data.msg);
	}
	hideUILoader_nodialog();			
}
