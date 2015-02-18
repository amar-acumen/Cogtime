 
  function clear_box(mode){
      if(mode == 'add'){
          $('#frm_add_product_category')[0].reset();
          $('.error-message').hide();
      }else{
          $('#frm_edit_product_category')[0].reset();
           $('.error-message').hide();
        }
  }
  
  function sub_cat_clear_box(mode){
      if(mode == 'add'){
          $('#frm_add_product_sub_category')[0].reset();
          $('.error-message').hide();
      }else{
          $('#frm_edit_product_sub_category')[0].reset();
           $('.error-message').hide();
        }
  }
  
  function edit_cat(id)
    {
        $('#i_edit_id').val(id );
         showUILoader('<img src="'+base_url+'images/loading_big.gif" width="50"/> ');
      
             $.ajax({
                 type: "post",
                 url: base_url+'admin/social_hub/chat_categories/edit_info/'+id,
                 dataType:"json",
                 
                 success: function(json_response){
                
                      if(json_response.result = 'success') {
                          
                          $('#txt_edit_cat_name').val('');
                          $('#txt_edit_cat_name').val(json_response.s_category_name);
                         
                             hideUILoader('edit-product-category');
                          
                      }
                      else {
                           hideUILoader('edit-product-category');
                          showUIMsg('Some error occurred. Please try again.');
                      }
                  },
                  error: function(){
                     hideUILoader('edit-product-category');
                      showUIMsg('Some error occurred. Please try again.');
                  }
              });     
    
    }



//============================= delete data =================================
function delete_confirm_(record_id)
{
 
        $('#i_del_id').val( record_id );
        //$('.no_of_rings').html(total_rings);
        show_dialog('delete-page-popup');
        
    }
    
function delete_()
  {

      var selected_id = parseInt( $('#i_del_id').val() );
      //alert(selected_id );

  if( selected_id!=0 )
  {
       $.ajax({
                 type: "post",
                 url:base_url + 'admin/social_hub/chat_categories/delete_information',
                 dataType:"json",
                data: ({'id':selected_id}),
                 success: function(json_response){
                     hide_dialog();
                      if(json_response.result = 'success') {
                        //$('#row_'+selected_id).hide();
                        $('#table_content').html(base64_decode(json_response.response));
                        showUIMsg("Chat Category has been deleted successfully.");
                             
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


function delete_sub_cat_()
  {

      var selected_id = parseInt( $('#i_del_id').val() );
      //alert(selected_id );

  if( selected_id!=0 )
  {
       $.ajax({
                 type: "post",
                 url:base_url + 'admin/social_hub/chat_categories/delete_sub_information',
                 dataType:"json",
                data: ({'id':selected_id}),
                 success: function(json_response){
                     hide_dialog();
                      if(json_response.result = 'success') {
                        //$('#row_'+selected_id).hide();
                        $('#table_content').html(base64_decode(json_response.response));
                        showUIMsg("Chat Sub-Category has been deleted successfully.");
                             
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
        url:        admin_base_url + "social_hub/chat_categories/add_info"
    }; 
 
 
     // for AJAX page-submission... edit
    optionsArr_edit = { 
        beforeSubmit:  showBusyScreen,  // pre-submit callback 
        success:       validateEditFrm, // post-submit callback 
        url:        admin_base_url + "social_hub/chat_categories/edit_info"
    }; 
 
    // for AJAX page-submission... add ------------- sub cat-----------------------------
     subCat_optionsArr = { 
        beforeSubmit:  showBusyScreen,  // pre-submit callback 
        success:       validateSubCatFrm, // post-submit callback 
        url:        admin_base_url + "social_hub/chat_categories/add_sub_cat_info"
    }; 
    
     // for AJAX page-submission... edit
    subCat_optionsArr_edit = { 
        beforeSubmit:  showBusyScreen,  // pre-submit callback 
        success:       validateEditSubCatFrm, // post-submit callback 
        url:        admin_base_url + "social_hub/chat_categories/edit_sub_cat_info"
    }; 
 //------------------------------------- sub cat end -----------------------------
 
})

function post_frm_ajax()
{
    
    $('#frm_add_product_category').ajaxSubmit(optionsArr);
    
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
        showUIMsg(data.msg);    
        hide_dialog();    
        $('#table_content').html(base64_decode(data.response));
        clear_box('add');
    }

}




function post_frm_edit_ajax()
{
    $('#frm_edit_product_category').ajaxSubmit(optionsArr_edit);
    
    return false;
}

// validate ajax-submission...
function validateEditFrm(data)
{
    var data = JSON.parse(data);
    if(data.result=='failure') 
    {
        clear_all_err();

        for ( var id in data.arr_messages ){
    
            if( $('#err_'+id) != null ) {
                $('#err_'+id).html(data.arr_messages[id]);
                $('#err_'+id).css('display', 'block');
                $('#err_'+id).attr('style','margin-left: 12px;');
            }
        }
        
    }
    else {
        clear_all_err();
        showUIMsg(data.msg);        
        hide_dialog();
        //$('#td_name_'+data.id).html(data.updated_d_name);
        $('#product_'+data.id).html(data.updated_d_name);
    }

}

function clear_all_err()
{
    $('.error-message').each(function(i){
            $(this).attr('style','display:none');
        });
}

//============================== sub cat =================================

function post_sub_cat_frm_ajax()
{
    
    $('#frm_add_product_sub_category').ajaxSubmit(subCat_optionsArr);
    
    return false;
}

// validate ajax-submission...
function validateSubCatFrm(data)
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
        showUIMsg(data.msg);    
        hide_dialog();    
        $('#table_content').html(base64_decode(data.response));
        sub_cat_clear_box('add');
    }

}



function post_sub_cat_frm_edit_ajax()
{
    
    $('#frm_edit_product_sub_category').ajaxSubmit(subCat_optionsArr_edit);
    
    return false;
}

// validate ajax-submission...
function validateEditSubCatFrm(data)
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
        showUIMsg(data.msg);    
        hide_dialog();    
        $('#td_name_'+data.id+' a').html(data.updated_d_name);
        sub_cat_clear_box('edit');
    }

}


//============================== sub cat end =================================

