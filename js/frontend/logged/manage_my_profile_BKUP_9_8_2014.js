function modify_my_profile_ajax()
{

    // for AJAX page-submission...
    optionsArr = {
        beforeSubmit: showBusyScreen, // pre-submit callback 
        success: validateFrm // post-submit callback 
    };

    frm_obj = $('#frmManageProfile');
    $(frm_obj).ajaxSubmit(optionsArr);

    return false;
}


// validate ajax-submission...
function validateFrm(data)
{

    var result_obj = JSON.parse(data);


    if (result_obj.result == 'success') {


        //alert(result_obj.is_wan_prayer_partner);
        if (result_obj.profile_img) {

            $('.user_pro_image').attr('style', 'background:url(' + result_obj.profile_img + ') no-repeat center;width:60px; height:60px;');
        }

        if (result_obj.is_wan_prayer_partner == 'Y') {
            $('.seeking-prayer-partner').attr('style', 'display:block;');
        }
        else {
            $('.seeking-prayer-partner').attr('style', 'display:none;');
        }


        $.ajax({
            "type": "post",
            "url": base_url + 'logged/my_profile/ajax_personal_info_submit',
            "success": function(response)
            {
                $('#personal_info_section').html(response);
            }
        });
        closeDiv('1');
        showUIMsg(result_obj.msg);


    }
    else
    {
        showUIMsg(result_obj.msg);
    }

    $('.error_msg').each(function(i) {
        $(this).attr('style', 'display:none');
    });
    $('div[id^=err_]').html('');
    if (result_obj.result == 'error') {


        $('.error_msg').each(function(i) {
            $(this).attr('style', 'display:none');
        });


        for (var id in result_obj.arr_messages) {

            if ($('#err_' + id) != null) {
                $('#err_' + id).html(result_obj.arr_messages[id]);
                $('#err_' + id).css('display', 'block');
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
        beforeSubmit: showBusyScreen, // pre-submit callback 
        success: validateBasicFrm // post-submit callback 
    };

    frm_obj2 = $('#frmManageBasicProfile');
    $(frm_obj2).ajaxSubmit(optionsArr);

    return false;
}


// validate ajax-submission...
function validateBasicFrm(data)
{

    var result_obj = JSON.parse(data);

    if (result_obj.result == 'success') {
        $.ajax({
            "type": "post",
            "url": base_url + 'logged/my_profile/ajax_basic_info_submit',
            "success": function(response)
            {
                $('#basic_info_section').html(response);
            }
        });

        closeDiv('2');
        showUIMsg(result_obj.msg);
    } else
    {
        showUIMsg(result_obj.msg);
    }

    $('.error_msg').each(function(i) {
        $(this).attr('style', 'display:none');
    });
    $('div[id^=err_msg]').html('');
    if (result_obj.result == 'error') {

        for (var id in result_obj.arr_messages) {

            if (result_obj.arr_messages[id] != '') {
                divID = result_obj.arr_messages[id] + "> div#err_msg";
                //alert(divID);
                $(divID).remove();
                $('#' + id).after(result_obj.arr_messages[id]);
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
        beforeSubmit: showBusyScreen, // pre-submit callback 
        success: validateEduFrm // post-submit callback 
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
    if (result_obj.result == 'success') {

        showUIMsg(result_obj.msg);

        $.ajax({
            "type": "post",
            "url": base_url + 'logged/my_profile/ajax_edu_submit',
            "success": function(response)
            {
                //console.log(response);
                $('#education_section').html(response);
            }
        });

        closeDiv('3');
        showUIMsg(result_obj.msg);
    } else {
        showUIMsg(result_obj.msg);
    }

    $('.error_msg').each(function(i) {
        $(this).attr('style', 'display:none');
    });
    $('div[id^=err_msg]').html('');
    if (result_obj.result == 'error') {

        for (var id in result_obj.arr_messages) {

            if (result_obj.arr_messages[id] != '') {
                divID = result_obj.arr_messages[id] + "> div#err_msg";

                $(divID).remove();
                $('#' + id).after(result_obj.arr_messages[id]);
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
        success: validateWorkFrm // post-submit callback 
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

    var result_obj = JSON.parse(data);

    if (result_obj.result == 'success') {




        $.ajax({
            "type": "post",
            "url": base_url + 'logged/my_profile/ajax_work_submit',
            "success": function(response)
            {
                $('#disabled_mnth_to').attr('disabled', 'disabled');
                $('#disabled_year_to').attr('disabled', 'disabled');
                $('#work_section').html(response);
            }
        });

        closeDiv('4');
        showUIMsg(result_obj.msg);

    }

    $('.error_msg').each(function(i) {
        $(this).attr('style', 'display:none');
    });
    $('div[id^=err_msg]').html('');
    if (result_obj.result == 'error') {

        for (var id in result_obj.arr_messages) {

            if (result_obj.arr_messages[id] != '') {
                divID = result_obj.arr_messages[id] + "> div#err_msg";

                $(divID).remove();
                $('#' + id).after(result_obj.arr_messages[id]);
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
        beforeSubmit: showBusyScreen, // pre-submit callback 
        success: validateSkillFrm // post-submit callback 
    };

    frm_obj2 = $('#frmManageSkillProfile');
    $(frm_obj2).ajaxSubmit(optionsArr);

    return false;
}


// validate ajax-submission...
function validateSkillFrm(data)
{

    var result_obj = JSON.parse(data);

    if (result_obj.result == 'success') {
        $.ajax({
            "type": "post",
            "url": base_url + 'logged/my_profile/ajax_skill_submit',
            "success": function(response)
            {
                $('#skill_section').html(response);
            }
        });

        closeDiv('5');
        showUIMsg(result_obj.msg);
    }
    else
    {
        showUIMsg(result_obj.msg);
    }

    $('.error_msg').each(function(i) {
        $(this).attr('style', 'display:none');
    });
    $('div[id^=err_msg]').html('');
    if (result_obj.result == 'error') {

        for (var id in result_obj.arr_messages) {

            if (result_obj.arr_messages[id] != '') {
                divID = result_obj.arr_messages[id] + "> div#err_msg";
                //alert(divID);
                $(divID).remove();
                $('#' + id).after(result_obj.arr_messages[id]);
            }

        }

    }

    // hide busy-screen...
    hideBusyScreen();
}


//======================================for add an extra edu div==============================================
function add_extra_edu_div()
{

    var no_of_divs = $('#edu_div_h').val();
    no_of_divs++;
    var edu_id_order = $('#edu_divs_id_order').val();
    edu_id_order++;




    divID = "edu_div_" + edu_id_order;
    $('#hidden_edu_div #copy_edu').attr('id', divID);


    content = $('#hidden_edu_div').html();

    if (no_of_divs <= 5)
    {
        $('#edu_div_h').val(no_of_divs);
        $(content).appendTo('#add_more_edu_div');
        $('#edu_divs_id_order').val(edu_id_order);
        $('#hidden_edu_div #' + divID).attr('id', 'copy_edu');
    }
    else
    {
        showUIMsg("Can not add more.");
    }



}// end of add_extra_div

function closeExtraEduDiv(obj)
{

    var divID = "#" + $(obj).closest('div').attr('id');
    var db_id = $(divID).find('#dbId').val();
    var i = $('#edu_div_h').val();
    i = i - 1;
    $('#edu_div_h').val(i);
    //// set to be deleted "db-id(s)"...
    if (db_id != '') {
        var existing_id_str = $('#deleted_edu_divs').val();
        db_id_str = (existing_id_str != '') ? existing_id_str + '#' + db_id : db_id;
        $('#deleted_edu_divs').val(db_id_str);

        $(divID).hide();
    } else {
        $(divID).remove();
    }




}


//================================add extra work div=======================================

function add_extra_work_div()
{

    var no_of_divs = $('#work_div_h').val();

    no_of_divs++;
    var work_id_order = $('#work_div_id_order').val();
    work_id_order++;




    divID = "work_div_" + work_id_order;
    $('#hidden_work_div #copy_work').attr('id', divID);



    content = $('#hidden_work_div').html();

    if (no_of_divs <= 5)
    {
        $('#work_div_h').val(no_of_divs);
        $(content).appendTo('#add_more_work_div');
        $('#work_div_id_order').val(work_id_order);
        $('#hidden_work_div #' + divID).attr('id', 'copy_work');
    }
    else
    {
        showUIMsg("Can not add more.");
    }



}// end of add_extra_div

function closeExtraWorkDiv(obj)
{

    var divID = "#" + $(obj).closest('div').attr('id');
    var db_id = $(divID).find('#dbId').val();

    var i = $('#work_div_h').val();
    i = i - 1;
    $('#work_div_h').val(i);
    //// set to be deleted "db-id(s)"...
    if (db_id != '') {
        var existing_id_str = $('#deleted_work_divs').val();
        db_id_str = (existing_id_str != '') ? existing_id_str + '#' + db_id : db_id;
        $('#deleted_work_divs').val(db_id_str);

        $(divID).hide();
    } else {
        $(divID).remove();
    }










}
function current_emp(obj)
{
    $('.year_to').removeAttr('disabled');
    $('.mnth_to').removeAttr('disabled');
    $('.year_to').removeAttr('id');
    $('.mnth_to').removeAttr('id');



    var divID = "#" + $(obj).parent('div').parent('div').attr('id');




    var str = "div" + divID + " select";
    $(str).eq(3).attr('disabled', true);
    $(str).eq(4).attr('disabled', true);

    $(str).eq(3).attr('id', 'disabled_mnth_to');
    $(str).eq(4).attr('ud', 'disabled_year_to');



}



//================================add extra skill=======================================
function add_extra_skill_div()
{
    var skills = $('#skill_div_h').val();
    var now_skills = skills;
    now_skills++;

    var skill_id_order = $('#skill_div_id_order').val();
    //alert(skill_id_order);
    skill_id_order++;


    var test = $('#skill_div_' + skills).html();
    var skill_div_no = 0;
    $.each($('div[id^=skill_div_]:visible'), function(index) {
        skill_div_no++;

    });

    if (now_skills <= 5)
    {
        $('#extra_skill_divs').append('<div id="skill_div_' + skill_id_order + '"><div class="lable01">Skill ' + now_skills + ':</div><div class="field01"><input name="txt_skill[]" type="text" style="width:230px;" /><input type="hidden" name="skill_db_id[]" id="dbId" value=""/> </div><a  class="close_class" href="javascript:void(0);"  onClick="closeExtraSkillDiv(this)" ><img src="images/icons/close.png" alt=""></a><div class="clr"></div> ');

        $('#skill_div_id_order').val(skill_id_order);
        $('#skill_div_h').val(now_skills);


    }
    else
    {
        showUIMsg("Can not add more.");
    }

}
function closeExtraSkillDiv(obj)
{
    var divID = "#" + $(obj).closest('div').attr('id');
    var db_id = $(divID).find('#dbId').val();

    var i = $('#skill_div_h').val();
    i = i - 1;
    $('#skill_div_h').val(i);
    //// set to be deleted "db-id(s)"...
    if (db_id != '') {
        var existing_id_str = $('#deleted_skill_divs').val();

        db_id_str = (existing_id_str != '') ? existing_id_str + '#' + db_id : db_id;
        $('#deleted_skill_divs').val(db_id_str);

        //$('#extra_skill_divs').children(divID).hide();
        $(divID).hide();
    } else {
        $(divID).remove();
    }

    var s = 1;
    $.each($('div[id^=skill_div_]:visible'), function(index) {
        //alert(s);

        //$(this).attr('id','skill_div_'+s);
        $(this).children('.lable01').text('Skill ' + s + ':');
        s++;

    });


}
//=================================== click cancel button ===========================================
function closeSkillDiv()
{
    closeDiv('5');
    $.ajax({
        "type": "post",
        "url": base_url + 'logged/my_profile/ajax_skill_cancel',
        "success": function(response)
        {

            $('#frmManageSkillProfile').html(response);
        }
    });

}



function closeWorkDiv()
{
    closeDiv('4');
    $.ajax({
        "type": "post",
        "url": base_url + 'logged/my_profile/ajax_work_cancel',
        "success": function(response)
        {

            $('#frmManageWorkProfile').html(response);
        }
    });

}


function closeEduDiv()
{
    closeDiv('3');
    $.ajax({
        "type": "post",
        "url": base_url + 'logged/my_profile/ajax_edu_cancel',
        "success": function(response)
        {
            //alert("success");
            $('#frmManageEduProfile').html(response);
        }
    });

}


function closeBasicInfoDiv()
{
    closeDiv('2');
    $.ajax({
        "type": "post",
        "url": base_url + 'logged/my_profile/ajax_basic_info_cancel',
        "success": function(response)
        {

            $('#frmManageBasicProfile').html(response);
        }
    });

}


function closePersonalInfoDiv()
{
    closeDiv('1');
    $.ajax({
        "type": "post",
        "url": base_url + 'logged/my_profile/ajax_personal_info_cancel',
        "success": function(response)
        {

            $('#frmManageProfile').html(response);
        }
    });

}

