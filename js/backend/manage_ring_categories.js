 
  function clear_box(mode){
	  if(mode == 'add'){
		  $('#frm_add_ring_category')[0].reset();
		  $('.error-message').hide();
	  }else{
		  $('#frm_edit_ring_category')[0].reset();
		   $('.error-message').hide();
		}
  }
  
  function edit_cat(id)
	{
		$('#i_edit_id').val(id );
		 showUILoader('<img src="'+base_url+'images/loading_big.gif" width="50"/> ');
	  
			 $.ajax({
				 type: "post",
				 url: base_url+'admin/social_hub/ring_categories/edit_info/'+id,
				 dataType:"json",
				 
				 success: function(json_response){
				
					  if(json_response.result = 'success') {
						  
						  $('#txt_edit_cat_name').val('');
						  $('#txt_edit_cat_name').val(json_response.s_category_name);
						 
						 	hideUILoader('edit-ring-category');
						  
					  }
					  else {
						   hideUILoader('edit-ring-category');
						  showUIMsg('Some error occurred. Please try again.');
					  }
				  },
				  error: function(){
					 hideUILoader('edit-ring-category');
					  showUIMsg('Some error occurred. Please try again.');
				  }
			  });	 
	
	}



//============================= delete data =================================
function delete_confirm_(record_id, total_rings)
{
 
		
		$('#i_del_id').val( record_id );
		$('.no_of_rings').html(total_rings);
		show_dialog('delete-page-popup');
		if(total_rings == '0')
		{
		show_dialog('delete-page-popup');
		}
		else
		{
		hide_dialog('delete-page-popup');
		show_dialog('delete-move-page-popup');
		}
	}
	
function delete_()
  {

	  var selected_id = parseInt( $('#i_del_id').val() );

  if( selected_id!=0 )
  {
  	 $.ajax({
				 type: "post",
				 url:base_url + 'admin/social_hub/ring_categories/delete_information',
				 dataType:"json",
				data: ({'id':selected_id}),
				 success: function(json_response){
					 hide_dialog();
					  if(json_response.result = 'success') {
						$('#row_'+selected_id).hide();
						showUIMsg("Ring Category has been deleted successfully.");
						 	
					  }
					  else {
					 
						   
						  showUIMsg('Some error occurred. Please try again.');
					  }
				  },
				  error: function(){
					  showUIMsg('Some error occurred. Please try again.');
				  }
			  });
			  
			  
	}		  
			  
			  
	
  
 
}
//========================== end of delete =======================================


//============================== ajax form submit for edit =================================
$(document).ready(function(arg) {
	
	// for AJAX page-submission... add
    optionsArr = { 
        beforeSubmit:  showBusyScreen,  // pre-submit callback 
        success:       validateFrm, // post-submit callback 
		url:		admin_base_url + "social_hub/ring_categories/add_info"
    }; 
 
 
 	// for AJAX page-submission... edit
    optionsArr_edit = { 
        beforeSubmit:  showBusyScreen,  // pre-submit callback 
        success:       validateEditFrm, // post-submit callback 
		url:		admin_base_url + "social_hub/ring_categories/edit_info"
    }; 
 
 
 
})

function post_frm_ajax()
{
	
	$('#frm_add_ring_category').ajaxSubmit(optionsArr);
	
	return false;
}

// validate ajax-submission...
function validateFrm(data)
{
	
	var data = JSON.parse(data);

	if(data.result=='failure') 
	{
		$('.error-message').each(function(i){
			$(this).attr('style','display:none');
		});
		
		for ( var id in data.arr_messages ){
			
			if( $('#err_'+id) != null ) {
				$('#err_'+id).html(data.arr_messages[id]);
				$('#err_'+id).css('display', 'block');
				$('#err_'+id).attr('style','margin-left: 12px;');
			}
		}
		
	}
	else {
		$('#frm_add_ring_category')[0].reset();
		showUIMsg(data.msg);	
		hide_dialog();	
		$('#table_content').html(base64_decode(data.response));
		clear_box('add');
	}

}




function post_frm_edit_ajax()
{
	$('#frm_edit_ring_category').ajaxSubmit(optionsArr_edit);
	
	return false;
}

// validate ajax-submission...
function validateEditFrm(data)
{
	var data = JSON.parse(data);
	if(data.result=='failure') 
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
	else {
		$('#frm_edit_ring_category')[0].reset();
		showUIMsg(data.msg);		
		hide_dialog();
		$('#td_name_'+data.id).html(data.updated_d_name);
	}

}

function show_move_page()
{
showUILoader('<img src="<?= base_url() ?>images/loading_big.gif" width="50"/> ');
var del_id=$('#i_del_id').val();
 $.ajax({
				 type: "post",
				 url:base_url + 'admin/social_hub/ring_categories/get_cat_rings',
				 dataType:"json",
				data:({"del_id":$('#i_del_id').val()}),
				 success: function(json_response){
				 hideUILoader();
					show_dialog('move-page-popup');
					  if(json_response.result == 'success') {
						
						$("#cat-list").html('');
						$("#cat-list").html(json_response.html);
						$("#i_cat_id").val(del_id);
					  }
					  
				  },
				  error: function(){
					  showUIMsg('Some error occurred. Please try again.');
				  }
			  });
			  
}

function get_subcat_list(p_cat_id)
{
showUILoader('<img src="<?= base_url() ?>images/loading_big.gif" width="50"/> ');
 $.ajax({
				 type: "post",
				 url:base_url + 'admin/social_hub/ring_categories/get_subcat_rings',
				 dataType:"json",
				data:({"del_id":p_cat_id}),
				 success: function(json_response){
					
					  if(json_response.result == 'success') {
						
						$("#sub-cat-list").html('');
						$("#sub-cat-list").html(json_response.html);
						hideUILoader();
					  }
					  
				  },
				  error: function(){
					  showUIMsg('Some error occurred. Please try again.');
				  }
			  });
}

function move_()
{
 $('#err-sub-cat').hide();

showUILoader('<img src="<?= base_url() ?>images/loading_big.gif" width="50"/> ');
	var cat_id=$('#i_cat_id').val();
	 $.ajax({
				 type: "post",
				 url:base_url + 'admin/social_hub/ring_categories/move_rings',
				 dataType:"json",
				data:({"del_id":cat_id,'cat_id':$('#cat-list').val(),'subcat_id':$('#sub-cat-list').val()}),
				 success: function(json_response){
					
					  if(json_response.result == 'success') {
						hide_dialog('move-page-popup');
						showUIMsg("Rings moved successfully.You can delete the ring now.");
						window.location.reload();
						
						
					  }
					  else
					  {
					  hideUILoader();
					  $('#err-sub-cat').show();
					  }
					  
				  },
				  error: function(){
					  showUIMsg('Some error occurred. Please try again.');
				  }
			  });
}