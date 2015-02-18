var p_start_date = null;
var p_end_date = null;

$(function() {
	//displayDate();
	
	var date = new Date(), y = date.getFullYear(), m = date.getMonth();
	var firstDay = new Date(y, m, 1);
	var lastDay = new Date(y, m - 1, -15);
	
	//alert(lastDay);
	
	$( "#start_prayer_dt1" ).datepicker({
			minDate: new Date(),
			dateFormat: 'dd/mm/yy',
			onSelect: function(selectedDate) {  
				//p_start_date = $('#start_prayer_dt1').datepicker( "option", "dateFormat", 'mm/dd/yy' ).val(); 
				
				//$('#start_prayer_dt1').datepicker( "option", "dateFormat", 'dd/mm/yy' );
				$('#end_prayer_dt2').datepicker( "option", "minDate", selectedDate ); 
				//$('#end_prayer_dt2').datepicker( "option", "dateFormat", 'dd/mm/yy' );
				//$('#end_prayer_dt2').datepicker( "option", "minDate", selectedDate );
				//get_weeks(p_start_date,p_end_date);
			}
	});
	
	
	$( "#end_prayer_dt2" ).datepicker({
		minDate: new Date(), 
		dateFormat: 'dd/mm/yy',
		onSelect: function(selectedDate) {  //alert(selectedDate+' end');
		
			//p_end_date =  $('#end_prayer_dt2').datepicker( "option", "dateFormat", 'mm/dd/yy' ).val(); 
			//$('#end_prayer_dt2').datepicker( "option", "dateFormat", 'dd/mm/yy' );
			//get_weeks(p_start_date,p_end_date);
		}
	});
	
});

function setDefaultdates(){ 
   $( "#start_prayer_dt1" ).datepicker({
			minDate: new Date(),
			dateFormat: 'dd/mm/yy'});
   $( "#start_prayer_dt1" ).val('');
   
   
   $( "#end_prayer_dt2" ).datepicker({
			minDate: new Date(),
			dateFormat: 'dd/mm/yy'});
   $( "#end_prayer_dt2" ).val('');
}

function get_weeks(p_start_date,p_end_date){
	
	if(p_end_date != null && p_start_date != null)
	{
		var DateDiff = {
	 
				inDays: function(d1, d2) {
					var t2 = d2.getTime();
					var t1 = d1.getTime();
			 
					return parseInt((t2-t1)/(24*3600*1000));
				}
		}
		
		var d1 = new Date(p_start_date);// new Date(dString);
		var d2 = new Date(p_end_date);//new Date();
		
		var days_between = DateDiff.inDays(d1, d2);
		
		if(days_between > 7){
			$('#day_block').show();
		}
		else{
			$('#day_block').hide();
		}
	}
}

$(document).ready(function(arg) {
	
  $('#frm_prayer_time').submit(function(){ 
		if($('#start_prayer_dt1').val() ==''  && $('#end_prayer_dt2').val() == '') {
			showUIMsg("Please select between dates.");
			return false;
		}
	});
});


function view_tithe_commits(id)
 {
 	   	//showUILoader_nodialog('<img src="'+base_url+'images/loading_big.gif" width="50"/> ');
		
	
	$('#commitment-container'+id).slideToggle('slow');
	$('#commitment-container'+id).show();
	
	
	$.ajax({
		type : "post",
		url : base_url+"logged/prayer_wall/viewCommits/short",
		dataType : "json",
		data : ({'p_request_id' : id }),
		success : function(data)
		{
			  
			  if(data.des != ''){
				$('#commitment_content_'+id).html(base64_decode(data.des));
			  }else{
				  $('#commitment_content_'+id).html("<div class ='view_more' style=' text-align: center; font-size: 15px; color:#013D62;'>No Commitments.</div>");
				 
			  }
			  
			  
			 // hideUILoader_nodialog();
		}
	});
 }
 

function show_commit_reply(p_request_id){
	
	$('#commit_form'+p_request_id).slideToggle('slow');
}
//Post reply on a feed
var ajax_reply_post = null; 
function post_prayer_commit(p_request_id) 
{
		 
		var message  = $('#ta_commit_text_'+p_request_id).val();
		var start_time  = $('#txt_commit_from_time_'+p_request_id).val();
		var start_date  = $('#commit_date_to_'+p_request_id).val();
		var end_time  = $('#txt_commit_to_time_'+p_request_id).val();
		var end_date  = $('#commit_date_end1_'+p_request_id).val();
		
		var csv_weekdays = ''; 
		$('#weekdays_tr'+p_request_id+' :checkbox:checked').each(function(i){
				csv_weekdays += $(this).val()+', ';
		});
		var chk_day  = csv_weekdays;
		
		var csv_time = ''; 
		$('#time_tr'+p_request_id+' :checkbox:checked').each(function(i){
				csv_time += $(this).val()+', ';
		});
		var chk_time  = csv_time; 
		
		showUILoader_nodialog('<img src="'+base_url+'images/loading_big.gif" width="50"/> ');
		if(ajax_reply_post!=null) {
			ajax_reply_post.abort();
		}
		ajax_reply_post = $.ajax({
			
			url: base_url+'logged/prayer_wall/post_commitments/'+ p_request_id,
			dataType: 'json',
			data: {'message':message,
				   'start_time':start_time,
				    'start_date':start_date ,
					'end_time': end_time,	
					'end_date':end_date,
					'chk_day':chk_day,
					'chk_time':chk_time},
			type: 'post',
	
			success: function (data, status) {
				
				if(data.success == 'false'){
					
					  $('.error-message').each(function(i){
						  $(this).attr('style','display:none');
					  });
					  
					  for ( var id in data.arr_messages ){
						  //alert(data.arr_messages[id]);
						  if( $('#err_'+id) != null ) {
							  $('#err_'+id).html(data.arr_messages[id]);
							  
							  if(id == 'end_date'+p_request_id )
							  	$('#err_'+id).attr('style', 'display:block; margin-left: -247px;font-weight:normal;');
							  else  if(id == 'start_date'+p_request_id )
							  	$('#err_'+id).attr('style', 'display:block; margin-left: 9px;');
							  else
							  	$('#err_'+id).css('display', 'block');
						  }
						  
					  }

				}
				else{
					
					$('.error-message').each(function(i){
						$(this).attr('style','display:none');
					});
					$('#total_commits'+p_request_id).html('('+data.total_commits+')');
					$('#add_commit'+p_request_id)[0].reset();	
					
					$('#commit_li'+p_request_id).next().addClass('first');
					$('#commit_li'+p_request_id).hide();
					
		  			$('#ta_commit_text_'+p_request_id).val('Max 140 Char allowed');
					// added for detail page 
					
					//$('#commit_content').html(data.html);
					//$('#commitment-container'+p_request_id).html(data.html);
					
										
					showUIMsg(data.msg);
					$('#commit_form'+p_request_id).slideToggle('slow');
				}
				
				
				hideUILoader_nodialog();
				//
			},
			error: function(data, status, e) {
				hideUILoader_nodialog();
				showUIMsg("Error!");
				
			}
		});
}

function limitTextarea(limitField, limitCount, limitNum) {
	if (limitField.value.length > limitNum) {
		limitField.value = limitField.value.substring(0, limitNum);
	} 
}



function view_intercession_commits(id)
 {
 	   	//showUILoader_nodialog('<img src="'+base_url+'images/loading_big.gif" width="50"/> ');
		
	
	$('#commitment-container'+id).slideToggle();
	
	$.ajax({
		type : "post",
		url : base_url+"logged/e_intercession/viewCommits/short",
		dataType : "json",
		data : ({'p_request_id' : id }),
		success : function(data)
		{
			  
			  if(data.des != ''){
				$('#commitment_content_'+id).html(base64_decode(data.des));
			  }else{
				  $('#commitment_content_'+id).html("<div class ='view_more' style=' text-align: center; font-size: 15px; color:#013D62;'>No Commitments.</div>");
			  }
			  
			  
			 // hideUILoader_nodialog();
		}
	});
 }
 
 
 
function show_intercession_reply(p_request_id){
	
	$('#commit_form'+p_request_id).slideToggle('slow');
}
//Post reply on a feed
var ajax_reply_post = null; 
function post_intercession_commit(p_request_id) 
{
		
		var message  = $('#ta_commit_text_'+p_request_id).val();
		var start_time  = $('#txt_commit_from_time_'+p_request_id).val();
		var start_date  = $('#commit_date_to_'+p_request_id).val();
		var end_time  = $('#txt_commit_to_time_'+p_request_id).val();
		var end_date  = $('#commit_date_end1_'+p_request_id).val();
		
		var csv_weekdays = ''; 
		$('#weekdays_tr'+p_request_id+' :checkbox:checked').each(function(i){
				csv_weekdays += $(this).val()+', ';
		});
		var chk_day  = csv_weekdays;
		
		var csv_time = ''; 
		$('#time_tr'+p_request_id+' :checkbox:checked').each(function(i){
				csv_time += $(this).val()+', ';
		});
		var chk_time  = csv_time; 
		
		showUILoader_nodialog('<img src="'+base_url+'images/loading_big.gif" width="50"/> ');
		if(ajax_reply_post!=null) {
			ajax_reply_post.abort();
		}
		ajax_reply_post = $.ajax({
			
			url: base_url+'logged/e_intercession/post_commitments/'+ p_request_id,
			dataType: 'json',
			data: {'message':message,
				   'start_time':start_time,
				    'start_date':start_date ,
					'end_time': end_time,	
					'end_date':end_date,
					'chk_day':chk_day,
					'chk_time':chk_time},
			type: 'post',
	
			success: function (data, status) {
				
				if(data.success == 'false'){
					
					  $('.error-message').each(function(i){
						  $(this).attr('style','display:none');
					  });
					  
					  for ( var id in data.arr_messages ){
						  //alert(data.arr_messages[id]);
						  if( $('#err_'+id) != null ) {
							  $('#err_'+id).html(data.arr_messages[id]);
							
							  if(id == 'end_date'+p_request_id )
							  	$('#err_'+id).attr('style', 'display:block; margin-left: -247px;font-weight:normal;');
							  else  if(id == 'start_date'+p_request_id )
							  	$('#err_'+id).attr('style', 'display:block; margin-left: 9px;');
							  else
							  	$('#err_'+id).css('display', 'block');
						  }
						  
					  }

				}
				else{
					
					$('.error-message').each(function(i){
						$(this).attr('style','display:none');
					});
					$('#total_commits'+p_request_id).html('('+data.total_commits+')');
					$('#commit_li'+p_request_id).next().addClass('first');
					$('#commit_li'+p_request_id).hide();
					
					$('#add_commit'+p_request_id)[0].reset();	
		  			$('#ta_commit_text_'+p_request_id).val('Max 140 Char allowed');
					showUIMsg(data.msg);
					$('#commit_form'+p_request_id).slideToggle('slow');
					
					//$('#commitment-container'+p_request_id).html(data.html);
				}
				
				
				hideUILoader_nodialog();
				//
			},
			error: function(data, status, e) {
				hideUILoader_nodialog();
				showUIMsg("Error!");
				
			}
		});
}

