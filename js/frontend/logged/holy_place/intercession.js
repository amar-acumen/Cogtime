
function limitText(limitField, limitCount, limitNum) {
	if (limitField.value.length > limitNum) {
		limitField.value = limitField.value.substring(0, limitNum);
	} 
}


function show_reply(p_request_id){
	
	$('#commit_form'+p_request_id).slideToggle('slow');
}
//Post reply on a feed
var ajax_reply_post = null; 
function post_reply(p_request_id) 
{
	
		var message  = $('#ta_commit_text_'+p_request_id).val();
		var start_time  = $('#txt_commit_from_time_'+p_request_id).val();
		var start_date  = $('#commit_date_to_'+p_request_id).val();
		var end_time  = $('#txt_commit_to_time_'+p_request_id).val();
		var end_date  = $('#commit_date_end_'+p_request_id).val();
		
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
					
					$('#commitment-container'+p_request_id).html(data.html);
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



// Post reply on a feed //

function view_commits(id)
 {
 	   	//showUILoader_nodialog('<img src="'+base_url+'images/loading_big.gif" width="50"/> ');
		
	
	$('#commitment-container'+id).slideToggle();
	
	$.ajax({
		type : "post",
		url : base_url+"logged/e_intercession/viewCommits",
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
 
 


 
function view_testimony(id)
 {
	
	$('#testimony-container'+id).slideToggle();
	
	$.ajax({
		type : "post",
		url : base_url+"logged/e_intercession/viewTestimony",
		dataType : "json",
		data : ({'p_request_id' : id }),
		success : function(data)
		{
			  
			  if(data.des != ''){
				$('#testimony_content_'+id).html(base64_decode(data.des));
			  }else{
				  $('#testimony_content_'+id).html("<div class ='view_more' style=' text-align: center; font-size: 15px; color:#013D62;'>No Testimony.</div>");
			  }
			  
			  
			 // hideUILoader_nodialog();
		}
	});
 }
var carriage_count = 0;
function restrict_carrige(e)
{
	/*//if(e.keyCode == 13)
	{
		$.ajax({
			type: 'post',
			url: base_url+'logged/prayer_wall/getCarriageCount',
			data: {'str':$('#ta_desc').val()},
			dataType: 'json',
	
			success: function (data, status) {
				
					if(data.count > 1){
						return false;
					}
				
				}	// end of success function...
		});
	}*/
	//alert($('#ta_desc').val().replace(/\r/g,"\r").replace(/\n/g,"\n"));

	if(e.keyCode == 13){
		carriage_count++;
		console.log(carriage_count);
	}
	/*if(e.keyCode == 8){
		carriage_count--;
	}*/
	if(e.keyCode == 13 && carriage_count >5){
		return false;
		//e.preventDefault();
	}
}
