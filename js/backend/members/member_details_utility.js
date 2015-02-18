//======================================for add an extra edu div==============================================
function admin_add_extra_edu_div()
{

	//var no_of_divs=$('#h_edu').val();
	var no_of_divs= parseInt( $("table[id^=edu_]:visible").length );
	no_of_divs++;

	//alert( $("table[id^=edu_]:visible").length );
	tblID = "edu_"+ no_of_divs;
	$('#div_edu_copy table').attr('id', tblID);
	
	content = $('#div_edu_copy').html();
	
	
	if(no_of_divs<=5)
	{
		$('#h_edu').val(no_of_divs);
		$(content).appendTo('#add_more_edu');	
	}
	else
	{
		showUIMsg("Can not add more.");
	}
	
	
	
}// end of add_extra_div

function admin_closeExtraEduDiv(obj)
{

	var tblID = "#"+ $(obj).closest('table').attr('id');
	var db_id=$(tblID).find('#dbId').val();
	
	alert(tblID);
	//// set to be deleted "db-id(s)"...
	if( db_id!='' ) {
		var existing_id_str = $('#deleted_edu_divs').val();
		db_id_str = ( existing_id_str!='' )? existing_id_str +'#'+ db_id: db_id;
		$('#deleted_edu_divs').val(db_id_str);
		
		$(tblID).hide();
	} else {
		$(tblID).remove();
	}
	

	//showUIMsg("Removed");		//------------------- remove	
	var i = parseInt( $("table[id^=edu_]:visible").length );
	i=i-1;
	
	$('#h_edu').val(i);
	//for rearrenge the remain divs
	/*$.each($('[id^=edu_]'),function(index){
									var k=index+1;

									$(this).attr('id','edu_'+k);
									//$(this).find('.close').attr('onClick','admin_closeExtraEduDiv('+k+')');
									
									
										  });*/
	
}

//======================================for add an extra work div==============================================
function admin_add_extra_work_div()
{

	var no_of_divs= parseInt( $("table[id^=work_]:visible").length );
	no_of_divs++;
	
	tblID = "work_"+ no_of_divs;
	$('#div_copy table').attr('id', tblID);
	$('#div_copy table .radio_cur_emp').val(no_of_divs);
	
	content = $('#div_copy').html();
	
	
	if(no_of_divs<=5)
	{
		$('#h_work').val(no_of_divs);
		$(content).appendTo('#add_more_work');
	}
	else
	{
		showUIMsg("Can not add more.");
	}
	
	
	
}// end of add_extra_div

function admin_closeExtraWorkDiv_pre(no_of_divs)
{

	var db_id=$("#work_"+no_of_divs).find('#dbId').val();
	
	
	showBusyScreen();
	/*$.ajax({
		   "url"	:	admin_base_url+"members/member_details/delete_work_info",
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

	$('#work_'+no_of_divs).remove();	
	var i=$('#h_work').val();
	i=i-1;
	$('#h_work').val(i);
	//for rearrenge the remain divs
	$.each($('[id^=work_]'),function(index){
									var k=index+1;
									

									$(this).attr('id','work_'+k);
									$(this).find('.close').attr('onClick','');
									$(this).find('.close').attr('onClick','admin_closeExtraWorkDiv('+k+')');
										
									
										  });
	
}


function admin_closeExtraWorkDiv(obj)
{
//alert(obj)
	var tblID = "#"+ $(obj).closest('table').attr('id');
	var db_id=$(tblID).find('#dbId').val();
	
	
	//// set to be deleted "db-id(s)"...
	if( db_id!='' ) {
		var existing_id_str = $('#deleted_work_divs').val();
		db_id_str = ( existing_id_str!='' )? existing_id_str +'#'+ db_id: db_id;
		$('#deleted_work_divs').val(db_id_str);
		
		$(tblID).hide();
	} else {
		$(tblID).remove();
	}
	
	
	
	var i = parseInt( $("table[id^=work_]:visible").length );
	i=i-1;
	$('#h_work').val(i);
}



//------------------------------radio button current employer----------------------------------
function current_emp(obj)
{
	var tblID = "#"+ $(obj).closest('table').attr('id');
	$('div.sec-detail select').attr('disabled', false);
	
	
	var str = "table"+ tblID +" select";
	$(str).eq(3).attr('disabled', true);
	$(str).eq(4).attr('disabled', true);
}





//================================add extra skill=======================================
function admin_add_extra_skill_div()
{
	var no_of_divs = parseInt( $("table[id^=skill_]:visible").length );
	no_of_divs++;
	
	
	var content = '<table id="skill_'+no_of_divs+'" width="100%" border="0" cellspacing="0" cellpadding="0" class="member-table  skill_info"><tr><td align="left" valign="top" height="30" width="180" class="label skill" >Skill '+no_of_divs+'</td><td align="left" valign="top" width="20" class="label">:</td><td align="left" valign="top" ><input type="text" name="txt_skill[]" value=""/><td valign="top" align="left" style=" float: right;  margin-right: 170px;"><a class="close" href="javascript:void(0);" style="margin-left:5px;" onclick="admin_closeExtraSkillDiv('+no_of_divs+')" ><img src="'+base_url+'images/icons/close.png" alt=""></a></td></td></tr><input type="hidden" name="skill_db_id[]" id="dbId"  value="0"> </table>';
	
	
	
	if(no_of_divs<=5)
	{
		$('#h_skill').val(no_of_divs);
		$(content).appendTo('#add_more_skill');
		
	}
	else
	{
		showUIMsg("Can not add more.");
	}
	
	
}
function admin_closeExtraSkillDiv(skill_no)
{
	var db_id=$('#skill_'+skill_no).find('#dbId').val();
	
	//// set to be deleted "db-id(s)"...
	if( db_id!='' ) {
		var existing_id_str = $('#deleted_skill_divs').val();
		db_id_str = ( existing_id_str!='' )? existing_id_str +'#'+ db_id: db_id;
		$('#deleted_skill_divs').val(db_id_str);
		
		$('#skill_'+skill_no).hide();
	} else {
		$('#skill_'+skill_no).remove();
	}
	
	
	
	var i = parseInt( $("table[id^=skill_]:visible").length );
	var total_skills=i-1;
	$('#h_skill').val(total_skills);
	

	//$('#skill_'+skill_no).remove();
	//showUIMsg("Removed");
}


function go_back_() {
		

	var current_page =  $('#current_page_val').val();
	//alert(current_page);
	
	showBusyScreen();
	//URL = admin_base_url+'members/members/get_member_list/'+current_page
	URL = admin_base_url+'members/members.html';
	window.location.href = URL;
	/* $.ajax({
		type: 'GET',
		url: admin_base_url+'members/members/get_member_list/'+current_page,

		success: function (data, status) {
			hideBusyScreen();
		/*	try {
				var obj = JSON.parse(data);
				if(obj.success==1) {
					alert(obj.content);
					showUIMsg('hello');
					//showUIMsg('Project deleted successfully.');
					//$('#table_content').html(obj.content);
				}
				else {
					showUIMsg('You do not have access to this page.');
				}
			}*/
			/*catch(e) {
				showUIMsg('An error occurred. Please try again.');
			}
		}
	});*/
}



