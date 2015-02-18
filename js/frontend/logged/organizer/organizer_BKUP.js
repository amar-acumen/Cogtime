
$(document).ready(function() {
	
	// for AJAX page-submission...
    var option_note_add = { 
        beforeSubmit:  showBeforesubmit,  // pre-submit callback 
        success:       validateFrm // post-submit callback 
    }; 
 
    // bind to the form's submit event 
    $('#frmAddNote').submit(function() {
	
        $(this).ajaxSubmit(option_note_add);
		
        return false;
		
    });
$('.color-change').parent().css('background-color','#ffe58d');

});
function showBeforesubmit() {
	showUILoader_nodialog('<img src="'+base_url+'images/loading_big.gif" width="50"/> ');
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
			//alert(data.arr_messages[id]);
			if( $('#err_'+id) != null ) {
				$('#err_'+id).html(data.arr_messages[id]);
				$('#err_'+id).attr('style','display:block;margin-left: 130px;margin-top: 2px;');	
			}
		}

		
	}
	else {
		  // clearing form
		  $('#frmAddNote')[0].reset();
		  $('.error-message').each(function(i){
			$(this).attr('style','display:none');
		  });
		  
		  $('.cal-list-1').slideToggle('slow');
		  showUIMsg(data.msg);

		  if(data.decode_html == false){
			 $('#displayDateDIV').html(data.display_date);
			 $('#slider-span').html(data.cal_html);
			 $('#hd_date').val(data.selected_date);
			 $('#hd_tododate').val(data.selected_date);
			 $('#content_div').html(data.html);
		  }
		  else{
		    $('#displayDateDIV').html(data.display_date);
		    $('#left_cal').html(base64_decode(data.cal_html));
		    $('#hd_date').val(data.selected_date);
		    $('#hd_tododate').val(data.selected_date);
		    $('#content_div').html(base64_decode(data.html));
		  }
		  
		    
		  if($('#frmnote').val() == 'month_view')
		  {
			  location.reload();
		  }
	}
	
	hideUILoader_nodialog();
				
}
function clear_box(){
		  $('#frmAddNote')[0].reset();
		  $('.error-message').hide();
}

$(document).ready(function () {

 //counting the left characters in comment
/*     function limitChars(textid, limit, infodiv)
     {
      var text = $('#'+textid).val(); 
      var textlength = text.length;
      if(textlength > limit)
      {
      $('#'+textid).val(text.substr(0,limit));
       return false;
      }
      else
      {
      
       return true;
      }
     }
    
    
    $(function(){
        $('#txt_desc').keyup(function(){
            limitChars('txt_desc', 500, '');
        });
    });
    
    
    //// just to show correct no. of chracters left while the page is loaded...
    limitChars('txt_desc',500, '');
    */
 
});



$(document).ready(function() {
	
	// for AJAX page-submission...
    var options_list = { 
        //beforeSubmit:  showBeforesubmit,  // pre-submit callback 
        success:       validateAddtodolistFrm // post-submit callback 
    }; 
 
    // bind to the form's submit event 
    $('#frmAddTodolist').submit(function() {
	
        $(this).ajaxSubmit(options_list);
		
        return false;
		
    });

});


// validate ajax-submission...
function validateAddtodolistFrm(data)
{
	//alert( data);
	
	var data = JSON.parse(data);
        console.log(data);
	if(data.success==false) 
	{
		
		$('.error-message').each(function(i){
			$(this).attr('style','display:none');
		});
		

		for ( var id in data.arr_messages ){
			//alert(data.arr_messages[id]);
			if( $('#err_'+id) != null ) {
				$('#err_'+id).html(data.arr_messages[id]);
				
				//if(id == 'todo_strt_frm' || id == 'todo_end_frm' || id == 'todo_rem_time'){
					$('#err_'+id).attr('style','display:block;margin-left: 130px;margin-top: 2px;');
				/*}else
				{
					$('#err_'+id).css('display', 'block');
				}*/
			}
		}

		
	}
	else {
		  // clearing form
		  $('#frmAddTodolist')[0].reset();
		   $('#add_to_do_end_time').hide();
		   
		   $('.error-message').each(function(i){
			$(this).attr('style','display:none');
		  });
		  // slide up;
		 $('.cal-list-2').slideToggle('slow');
		  showUIMsg(data.msg);
		  if(data.decode_html == false){
			 $('#displayDateDIV').html(data.display_date);
			 $('#slider-span').html(data.cal_html);
			 $('#hd_date').val(data.selected_date);
			 $('#hd_tododate').val(data.selected_date);
			 $('#content_div').html(data.html);
		  }
		  else{
		   $('#displayDateDIV').html(data.display_date);
		   $('#left_cal').html(base64_decode(data.cal_html));
		   $('#hd_date').val(data.selected_date);
		   $('#hd_tododate').val(data.selected_date);
		   $('#content_div').html(base64_decode(data.html));
		  }
		  
		  if($('#frmlist').val() == 'month_view')
		  { 
		  	location.reload();
		  }
		 
	}
	
	//hideUILoader_nodialog();
				
}
function clear_todolist_box(){
		  $('#frmAddTodolist')[0].reset();
		  $('.error-message').hide();
}



// edit  note box ///

// NEW METHODS TO EDIT NOTE //
 function edit_note(id) 
 {
		$('#hd_edit_note_id').val(id);
		//$('#note_'+id).hide();
		 showUILoader_nodialog('<img src="'+base_url+'images/loading_big.gif" width="50"/> ');
	  
			 $.ajax({
				 type: "get",
				 url: base_url+'logged/organizer_day_view/edit_note_ajax/'+id,
				 dataType:"json",
				 success: function(json_response){
					 
					  if(json_response.result = 'success') {
						  
						  	
						    $('#txt_edit_title').val(json_response.note_arr.s_title);
							$('#txt_edit_desc').val(json_response.note_arr.s_description);
							
							$('#s_edit_time').val(json_response.note_arr.t_time);
							$('#hd_edit_date').val(json_response.note_arr.d_date);
												
						 	hideUILoader_nodialog();
							show_dialog('edit-note');
					  }
					  else {
						   hideUILoader_nodialog();
						  showUIMsg('Some error occurred. Please try again.');
					  }
				  },
				  error: function(){
					 hideUILoader_nodialog();
					  showUIMsg('Some error occurred. Please try again.');
				  }
			  });	 
		
}




var ajax_post = null;
function edit_note_ajax() 
{
		var id=$('#hd_edit_note_id').val();
		showUILoader_nodialog('<img src="'+base_url+'images/loading_big.gif" width="50"/> ');
		if(ajax_post!=null) {
			ajax_post.abort();
		}
		ajax_post = $.ajax({
			
			url: base_url+'logged/organizer_day_view/edit_note_ajax',
			dataType: 'json',
			data: {'txt_edit_desc': $('#txt_edit_desc').val(),'s_edit_time':$('#s_edit_time').val()
					,'i_note_id' : id, 'hd_edit_date': $('#hd_edit_date').val()},
			type: 'post',
	
			success: function (data, status) {
				
				if(data.success==false) 
				{
					
					$('.error-message').each(function(i){
						$(this).attr('style','display:none');
					});
					
			
					for ( var msg_id in data.arr_messages ){
						//alert(data.arr_messages[msg_id]);
						if( $('#err_'+msg_id) != null ) {
							$('#err_'+msg_id).html(data.arr_messages[msg_id]);
							$('#err_'+msg_id).attr('style','display:block;margin-left:49px;margin-top: 2px;');	
						}
					}
			
					
				}
				else {
					  // clearing form
					  $('#frmEditNote')[0].reset();
					  
					  showUIMsg(data.msg);
					 $('#displayDateDIV').html(data.display_date);
					 $('#slider-span').html(base64_decode(data.cal_html));
					 $('#hd_date').val(data.selected_date);
					 $('#hd_tododate').val(data.selected_date);
					 $('#content_div').html(base64_decode(data.html));	  
					  hide_dialog();
				}
				
				
				
				
				hideUILoader_nodialog();
				showUIMsg(data.msg);
			},
			error: function(data, status, e) {
				hideUILoader_nodialog();
				showUIMsg("Error!");
				
			}
		});
}


function clear_edit_note_box(id){
		  $('#frmEditNote_'+id)[0].reset();
		  $('.error-message').hide();
}

function noteCancelBtn(s_type , id){
	$('#'+s_type+id).show();
}
// NEW METHODS TO EDIT NOTE //






function remove_note()
{
	var id = $('#note_del_id').val();
	 showUILoader_nodialog('<img src="'+base_url+'images/loading_big.gif" width="50"/> ');
  
		 $.ajax({
			 type: "get",
			 url: base_url+'logged/organizer_day_view/remove_note_ajax/'+id,
			 dataType:"json",
			 success: function(json_response){
				 
				  if(json_response.result = 'success') {
					   showUIMsg(json_response.msg);
						
						hideUILoader_nodialog();
					     $('#displayDateDIV').html(json_response.display_date);
						 $('#slider-span').html(base64_decode(json_response.cal_html));
						 $('#hd_date').val(json_response.selected_date);
						 $('#hd_tododate').val(json_response.selected_date);
						 $('#content_div').html(base64_decode(json_response.html));	  
						  hide_dialog();
				  }
				  else {
					   
					   
					   hideUILoader_nodialog();
					  showUIMsg('Some error occurred. Please try again.');
				  }
			  },
			  error: function(){
				  hideUILoader();
				  showUIMsg('Some error occurred. Please try again.');
			  }
		  });	 
}





// edit  to-do-list  box ///


    function edit_list(id)
	{
		$('#hd_edit_list_id').val(id);
		 showUILoader_nodialog('<img src="'+base_url+'images/loading_big.gif" width="50"/> ');
	  
			 $.ajax({
				 type: "get",
				 url: base_url+'logged/organizer_day_view/edit_todo_ajax/'+id,
				 dataType:"json",
				 success: function(json_response){
					 
					  if(json_response.result = 'success') {
						    $('#txt_edit_todo_title').val(json_response.note_arr.s_title);
							$('#ta_edit_todo_desc').val(json_response.note_arr.s_description);
							$('#edit_todo_strt_frm').val(json_response.note_arr.t_display_start_time);
							$('#edit_todo_end_frm').val(json_response.note_arr.t_display_end_time);
							$('#edit_todo_rem_time').val(json_response.note_arr.t_display_remind_time);
							$('#hd_edit_tododate').val(json_response.note_arr.d_display_date);
                                                         $('#hd_edit_display_time').val(json_response.note_arr.t_display_start_time);
												
						 	hideUILoader_nodialog();
							show_dialog('edit-list');
					  }
					  else {
						   hideUILoader_nodialog();
						  showUIMsg('Some error occurred. Please try again.');
					  }
				  },
				  error: function(){
					 hideUILoader_nodialog();
					  showUIMsg('Some error occurred. Please try again.');
				  }
			  });	 
		//show_dialog('edit-scrolling-headlines');
		//return false;
	}


var ajax_list_post = null;
function edit_list_ajax() 
{
		var id=$('#hd_edit_list_id').val();
		showUILoader_nodialog('<img src="'+base_url+'images/loading_big.gif" width="50"/> ');
		if(ajax_list_post!=null) {
			ajax_list_post.abort();
		}
		ajax_list_post = $.ajax({
			
			url: base_url+'logged/organizer_day_view/edit_todo_ajax',
			dataType: 'json',
			data: {'ta_edit_todo_desc': $('#ta_edit_todo_desc').val(),
					'edit_todo_strt_frm':$('#edit_todo_strt_frm').val(),
					'edit_todo_end_frm':$('#edit_todo_end_frm').val(),
					'edit_todo_rem_time':$('#edit_todo_rem_time').val(),
					
					'i_list_id' : id, 
					'hd_edit_tododate': $('#hd_edit_tododate').val(),'hd_edit_display_time':$('#hd_edit_display_time').val()},
			type: 'post',
	
			success: function (data, status) {
				
				if(data.success==false) 
				{
					
					$('.error-message').each(function(i){
						$(this).attr('style','display:none');
					});
					
			
					for ( var msg_id in data.arr_messages ){
						//alert(data.arr_messages[msg_id]);
						if( $('#err_'+msg_id) != null ) {
							$('#err_'+msg_id).html(data.arr_messages[msg_id]);
							$('#err_'+msg_id).attr('style','display:block;margin-left: 79px;margin-top: 2px;');	
						}
					}
			
					
				}
				else {
					  // clearing form
					  $('#frmEditTodolist')[0].reset();

					  showUIMsg(data.msg);
					 
					 $('#displayDateDIV').html(data.display_date);
					 $('#slider-span').html(base64_decode(data.cal_html));
					 $('#hd_date').val(data.selected_date);
					 $('#hd_tododate').val(data.selected_date);
					 $('#content_div').html(base64_decode(data.html));	  
					 hide_dialog();
				}
				
				
				
				
				hideUILoader_nodialog();
				showUIMsg(data.msg);
			},
			error: function(data, status, e) {
				hideUILoader_nodialog();
				showUIMsg("Error!");
				
			}
		});
}


function clear_edit_note_box(id){
		  $('#frmEditNote_'+id)[0].reset();
		  $('.error-message').hide();
}



function clear_todoeditlist_box(){
		  $('#frmEditTodolist')[0].reset();
		  $('.error-message').hide();
}

function show_note_delete_popup(id){
	$('#note_del_id').val(id);
	show_dialog('delete-note');
}

function show_list_delete_popup(id){
	$('#list_del_id').val(id);
	show_dialog('delete-list');
}

function remove_list()
{
	var id = $('#list_del_id').val();
	 
	 showUILoader_nodialog('<img src="'+base_url+'images/loading_big.gif" width="50"/> ');
  
		 $.ajax({
			 type: "get",
			 url: base_url+'logged/organizer_day_view/remove_list_ajax/'+id,
			 dataType:"json",
			 success: function(json_response){
				 
				  if(json_response.result = 'success') {
					   showUIMsg(json_response.msg);
						
						//hide_dialog('edit_list');
						hideUILoader_nodialog();
						 $('#displayDateDIV').html(json_response.display_date);
						 $('#slider-span').html(base64_decode(json_response.cal_html));
						 $('#hd_date').val(json_response.selected_date);
						 $('#hd_tododate').val(json_response.selected_date);
						 $('#content_div').html(base64_decode(json_response.html));	  
						  hide_dialog();
				  }
				  else {
					   
					  // hide_dialog('edit_list');
					   hideUILoader_nodialog();
					  showUIMsg('Some error occurred. Please try again.');
				  }
			  },
			  error: function(){
				 hideUILoader();
				// hide_dialog('edit_list');
				  showUIMsg('Some error occurred. Please try again.');
			  }
		  });	 
}


/// SETTING REMINDER POPUP OFF /////////////////////////////////
function generateEndtime(start_time)
{
	 var id = $('#i_list_id').val();
	 showUILoader_nodialog('<img src="'+base_url+'images/loading_big.gif" width="50"/> ');
  
		 $.ajax({
			 type: "get",
			 url: base_url+'logged/organizer_day_view/generate_end_time_list/'+start_time,
			 dataType:"json",
			 success: function(json_response){
				 
				  if(json_response.success == true) {
					   showUIMsg(json_response.msg);
						
						//hide_dialog('edit_list');
						
						$('#todo_end_frm').html('');
						$('#todo_end_frm').html(json_response.sel_html);
						$('#add_to_do_end_time').attr('style','display:block;');
						$('#add_to_do_end_time').show();
						//$('#end_time_select').show();
						hideUILoader_nodialog();
				  }
				  else {
					   
					  // alert(json_response.success +' al '+ 2+ ' id: '+ id);
					   $('#add_to_do_end_time').attr('style','display:none;');
					   hideUILoader_nodialog();
					  //showUIMsg('Some error occurred. Please try again.');
				  }
			  },
			  error: function(){
				 hideUILoader();
				 //hide_dialog('edit_list');
				  showUIMsg('Some error occurred. Please try again.');
			  }
		  });	 
}


function generateEndtime_edit(start_time , id)
{
	  showUILoader_nodialog('<img src="'+base_url+'images/loading_big.gif" width="50"/> ');
  
		 $.ajax({
			 type: "get",
			 url: base_url+'logged/organizer_day_view/generate_end_time_list/'+start_time,
			 dataType:"json",
			 success: function(json_response){
				 
				 
				  if(json_response.success == true) {
					   showUIMsg(json_response.msg);
						//alert(json_response.success+ 1);
						//hide_dialog('edit_list');
						
						$('#edit_todo_end_frm_'+id).html('');
						$('#edit_todo_end_frm_'+id).html(json_response.sel_html);
						$('#edit_end_time_div_'+id).attr('style','display:block;');
						hideUILoader_nodialog();
				  }
				  else if(json_response.success == false) {
					   //alert(json_response.success +' al '+ 2+ ' id: '+ id);
					   $('#edit_end_time_div_'+id).attr('style','display:none;');
					   hideUILoader_nodialog();
					   // showUIMsg('Some error occurred. Please try again.');
				  }
			  },
			  error: function(){
				 hideUILoader();
				 //hide_dialog('edit_list');
				  showUIMsg('Some error occurred. Please try again.');
			  }
		  });	 
}



function clearFrmAdd(formName){
	
	 $('#'+formName)[0].reset();
	 $('.error-message').each(function(i){
			$(this).attr('style','display:none');
	  });
	 $('.note-todo-content-box .note-todo-inside').hide();
}

// text limitation in todo list and note

/*$(document).ready(function () {
    //counting the left characters in comment
     function limitChars(textid, limit, infodiv)
     {
		var text = $('#'+textid).val(); 
		var textlength = text.length;
		if(textlength > limit)
		{
		$('#'+textid).val(text.substr(0,limit));
		 return false;
		}
    
     }
    
		
      $(function(){
        $('#txt_edit_desc_'+id).keyup(function(){
            limitChars('txt_edit_desc_'+id, 500, '');
        });
      });
      $(function(){
        $('#ta_edit_todo_desc').keyup(function(){
            limitChars('ta_edit_todo_desc', 500, '');
        });
      });
	  
	 
   		 //// just to show correct no. of chracters left while the page is loaded...
    	 limitChars('txt_edit_desc',500, '');
		 limitChars('ta_edit_todo_desc',500, '');
    
 
});
*/

function limitText(limitField, limitCount, limitNum) {
	if (limitField.value.length > limitNum) {
		limitField.value = limitField.value.substring(0, limitNum);
	} 
}


function showAddNote(adddate,caldate){
	
	$('#hd_date').val(adddate);
	$('#start_to2').val(caldate);
	$('.add-cal-list >.shade_box_01 ').hide();
	$('.cal-list-1').slideToggle('slow');
}

function showAddList(adddate,caldate){
	
	$('#hd_tododate').val(adddate);
	$('#start_to').val(caldate);
	$('.add-cal-list >.shade_box_01 ').hide();
	$('.cal-list-2').slideToggle('slow');
}


function gotoDay(val_date){
	
	$('#goto_date').val(val_date);
	$('#gotoFrm').submit();
}
