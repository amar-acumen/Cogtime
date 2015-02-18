

//======================================for add an extra edu div==============================================
function admin_add_extra_edu_div()
{

	var no_of_divs=$('#h_edu').val();
	no_of_divs++;
	//alert(no_of_divs);
	var content = '<table id="edu'+'_'+no_of_divs+'"><tr><td class="label" width="180" valign="top" height="30" align="left"></td><td class="label" width="20" valign="top" align="left"></td><td valign="top" align="left" style=" float: right;  margin-right:-12px;"><a class="close" href="javascript:void(0);" onclick="admin_closeExtraEduDiv('+no_of_divs+')" ><img src="'+base_url+'images/icons/close.png" alt="" ></a></td></tr>';
	
	content+= $('.edu_info').html()+"</table>";


	
	if(no_of_divs<=5)
	{
		$('#h_edu').val(no_of_divs);
		$(content).appendTo('#add_more_edu');	
		//$('#add_more_edu').find('input').val('');
		$('#edu_'+no_of_divs).find('input').val('');
		//$('#edu_'+no_of_divs).find('#dbId').val('0');
		
		
		
	}
	else
	{
		showUIMsg("Can not add more.");
	}
	
	
	
}// end of add_extra_div

function admin_closeExtraEduDiv(no_of_divs)
{

	var db_id=$("#edu_"+no_of_divs).find('#dbId').val();
	
	/*showBusyScreen();
	$.ajax({
		   "url"	:	admin_base_url+"members/member_details/delete_edu_info",
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
	

	$('#edu_'+no_of_divs).css('display','none');
	showUIMsg("Removed");		//------------------- remove	
	var i=$('#h_edu').val();
	i=i-1;
	
	$('#h_edu').val(i);
	//for rearrenge the remain divs
	$.each($('[id^=edu_]'),function(index){
									var k=index+1;

									$(this).attr('id','edu_'+k);
									$(this).find('.close').attr('onClick','admin_closeExtraEduDiv('+k+')');
									
									
										  });
	
}

//======================================for add an extra work div==============================================
function admin_add_extra_work_div()
{

	var no_of_divs=$('#h_work').val();
	//alert(no_of_divs);
	no_of_divs++;
	
	/*var content = '<table id="work_'+no_of_divs+'"><tr><td class="label" width="180" valign="top" height="30" align="left"></td><td class="label" width="20" valign="top" align="left"></td><td valign="top" align="left" style=" float: right; margin-right:-12px;"><a class="close" href="javascript:void(0);" onclick="admin_closeExtraWorkDiv('+no_of_divs+')" ><img src="'+base_url+'images/icons/close.png" alt=""></a></td></tr>';
	
	content+= $('#div_copy').html()+"</table>";*/
	tblID = "work_"+ no_of_divs;
	$('#div_copy table').attr('id', tblID);
	$('#div_copy table .radio_cur_emp').val(no_of_divs);
	
	content = $('#div_copy').html();
	
	
	
	
	
	/*var work_content = '<table id="work_'+no_of_divs+'" width="100%" border="0" cellspacing="0" cellpadding="0" class="member-table work_info"><tr><td class="label" width="180" valign="top" height="30" align="left"></td><td class="label" width="20" valign="top" align="left"></td><td valign="top" align="left" style=" float: right; width:193px;"><a class="close" href="javascript:void(0);" onclick="admin_closeExtraWorkDiv('+no_of_divs+')" ><img src="'+base_url+'images/icons/close.png" alt="" ></a></td></tr> <tr><td align="left" valign="top" height="30" width="180" class="label">Employer</td><td align="left" valign="top" width="20" class="label">:</td><td align="left" valign="top" ><input type="text" name="txt_employer_name[]" value="" /></td></tr>  <tr><td align="left" valign="top" height="30" width="180" class="label">Work country</td><td align="left" valign="top" width="20" class="label">:</td><td align="left" valign="top" ><input type="text" name="txt_employer_country[]" value="" /></td></tr>  <tr><td align="left" valign="top" height="30" width="180" class="label">Work state</td><td align="left" valign="top" width="20" class="label">:</td> <td align="left" valign="top" ><input type="text" name="txt_employer_state[]" value="" /></td></tr>  <tr><td align="left" valign="top" height="30" width="180" class="label">Work city</td><td align="left" valign="top" width="20" class="label">:</td><td align="left" valign="top" ><input type="text" name="txt_employer_city[]" value="" /></td></tr>     <tr><td align="left" valign="top" height="30" width="180" class="label">Position</td><td align="left" valign="top" width="20" class="label">:</td><td align="left" valign="top" ><input type="text" name="txt_employer_position[]" value="" /></td></tr>     <tr><td align="left" valign="top" height="30" width="180" class="label">Period</td><td align="left" valign="top" width="20" class="label">:</td><td align="left" valign="top" >   <select name="mnth_from[]" class="profile_combo mnth_from" style="width:90px;margin-right:5px; "><option value="-1">Select</option><?php echo makeOptionMonth();?></select>      <script type="text/javascript">$(document).ready(function(arg) { $(".profile_combo").msDropDown();$(".profile_combo").hide(); })</script>   <select name="year_from[]" class="profile_combo year_from" style="width:60px;"><option value="-1">Select</option><?=makeOptionYear()?></select>  <span class="left"> &nbsp; To  &nbsp;</span>    <select name="mnth_to[]" class="profile_combo mnth_to" style="width:90px; margin-right:5px;" ><option value="-1">Select</option><?php echo makeOptionMonth();?></select>   <select name="year_to[]" class="profile_combo year_to" style="width:60px;" ><option value="-1">Select</option><?=makeOptionYear()?></select></td></tr>    <tr><td>&nbsp;</td><td>&nbsp;</td><td ><input name="is_current_employer" type="radio" class="radio_cur_emp"  onclick="return current_emp('+no_of_divs+');" value=""  /> Current Employer</td></tr>     <tr><td>&nbsp;</td></tr>   <input type="hidden" name="db_id[]" id="dbId" value=""  /> </table>';*/

	//alert(content);
	
	if(no_of_divs<=5)
	{
		/*var first_radio=false;
		if($('#work_1').find('.radio_cur_emp').attr('checked'))
		{
			first_radio=true;	
		}
		*/
		$('#h_work').val(no_of_divs);
		
		
		$(content).appendTo('#add_more_work');
		
		$("table#add_more_work  select.profile_combo1").msDropDown();
		//$("table#add_more_work  .txt_work_country").msDropDown();
        //$("table#add_more_work  .txt_work_country").hide();
		
		
		
		//$(work_content).appendTo('#add_more_work');		//----------------------- append -----------
		/*$('#work_'+no_of_divs).find('input').val('');
	
		//$('#work_'+no_of_divs).find('#dbId').val('0');
		$('#work_'+no_of_divs).find('select').val('-1');
		$('#work_'+no_of_divs).find('.radio_cur_emp').attr('onClick','current_emp('+no_of_divs+')');
		
		$('#work_'+no_of_divs).find('select').removeAttr('disabled');
		$('#work_'+no_of_divs).find('select').removeAttr('id');
		
		
		$('#work_'+no_of_divs).find('.radio_cur_emp').attr('checked', false);
		
		
		if(first_radio)
			$('#work_1').find('.radio_cur_emp').attr('checked','checked');*/
		
		
		
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

	var tblID = "#"+ $(obj).closest('table').attr('id');
	var db_id=$(tblID).find('#dbId').val();
	
	
	/*showBusyScreen();
	$.ajax({
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
		  

	//$('#work_'+no_of_divs).remove();
	$(tblID).remove();	
	
	 showUIMsg("Removed");				//------------ remove
	var i=$('#h_work').val();
	i=i-1;
	$('#h_work').val(i);
	//for rearrenge the remain divs
	$.each($('[id^=work_]'),function(index){
									var k=index+1;
									

									$(this).attr('id','work_'+k);
								
									$(this).find('.radio_cur_emp').val(k);
								
									//$(this).find('.close').attr('onClick','');
									//$(this).find('.close').attr('onClick','admin_closeExtraWorkDiv('+k+')');
										
									
										  });
										
	
}



//------------------------------radio button current employer----------------------------------
function current_emp(obj)
{
	var tblID = "#"+ $(obj).closest('table').attr('id');
	//$('select').removeAttr('disabled');
	$('div.sec-detail select').attr('disabled', false);
	//alert("table : "+tblID);
	//alert("radio value : "+$(obj).val());
	//alert($(tblID).last('select').attr('class'));
	//alert($(tblID).last('select').prev('select').attr('id'));
	
	//$('.experience_to select').removeAttr('id');
	//$(tblID).find('.experience_to select').attr('id','exp_to');
	
	
	//alert(tblID);
	
	
	
	
	//$(tblID).last('select').attr('class')
	var str = "table"+ tblID +" select";
	$(str).eq(2).attr('disabled', true);
	$(str).eq(3).attr('disabled', true);
	//$(str).last('select').attr('disabled', true);
	
	//$('.experience_month_to :has(div)').removeAttr('disabled');
	//$('.experience_year_to :has(div)').removeAttr('disabled');
	
	
	
	//$(str).eq(2).removeAttr('id');
	//$(str).eq(3).removeAttr('id');
	//$(str).eq(2).attr('id','disabled_mnth_to');
	//$(str).eq(3).attr('id','disabled_year_to');
	
	$('div.sec-detail select').msDropDown();
	
	//alert(div_no);
	/*$('.experience_month_to :has(div)').removeAttr('disabled');
	$('.experience_year_to :has(div)').removeAttr('disabled');
	*/
	
/*	$('.year_to').removeAttr('disabled');
	$('.mnth_to').removeAttr('disabled');
	$('.year_to').removeAttr('id');
	$('.mnth_to').removeAttr('id');
	
	$('#work_'+div_no).find('.year_to').attr('disabled','disabled');
	$('#work_'+div_no).find('.mnth_to').attr('disabled','disabled');
	$('#work_'+div_no).find('.year_to').attr('id','disabled_year_to');
	$('#work_'+div_no).find('.mnth_to').attr('id','disabled_mnth_to');
*/

	/*var last1 = $('#work_'+div_no).last('select').attr('class');
	var last2 = $('#work_'+div_no).last('select').before('select').attr('class');
	alert(last1);
	alert(last2);
	*/
	
	//alert("work 1 id : "+$('#work_1').last('select').attr('id'));
	//$.each($('table[id^=work_]'),function(i){
	//									alert($(this).last('select').attr('id'));
		
	//});
	
	/*$('[id=^work_]').last('select').removeAttr('disabled');
	$('[id=^work_]').last('select').before('select').removeAttr('disabled');
	$('[id=^work_]').last('select').removeAttr('id');
	$('[id=^work_]').last('select').before('select').removeAttr('id');
	*/
	
	//return false;
}





//================================add extra skill=======================================
function admin_add_extra_skill_div()
{
	

	var no_of_divs=$('#h_skill').val();
	//alert(no_of_divs);
	no_of_divs++;
	
	
	var content = '<table id="skill'+'_'+no_of_divs+'" width="100%" border="0" cellspacing="0" cellpadding="0" class="member-table  skill_info"><tr><td align="left" valign="top" height="30" width="180" class="label skill" >Skill '+no_of_divs+'</td><td align="left" valign="top" width="20" class="label">:</td><td align="left" valign="top" ><input type="text" name="txt_skill[]" value=""/><td valign="top" align="left" style=" float: right;  margin-right: 170px;"><a class="close" href="javascript:void(0);" style="margin-left:5px;" onclick="admin_closeExtraSkillDiv('+no_of_divs+')" ><img src="'+base_url+'images/icons/close.png" alt=""></a></td></td></tr><input type="hidden" name="skill_db_id[]" id="dbId"  value="0"> </table>';
	
	
	
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
	//alert(skill_no);
	var db_id=$('#skill_'+skill_no).find('#dbId').val();
	//if(db_id)
	
	

	var total_skills=$('#h_skill').val()-1;
	$('#h_skill').val(total_skills);
	
	//showBusyScreen();
	/*$.ajax({
		   "url"	:	admin_base_url+"members/member_details/delete_skill_info",
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

	$('#skill_'+skill_no).remove();
	 showUIMsg("Removed");
	$.each($('[id^=skill_]'),function(i){
												
						var j=i+1;
						
						$(this).attr('id','skill_'+j);
						/*var now_id=$(this).attr('id');
						alert(now_id);
						*/
						
						//var t=$(this).find('.close').attr("onclick");
						
						
						//$(this).find('.close').attr("onclick", "");	// not working in jquery varsion:1.5.2
						$(this).find('.close').attr('onClick','admin_closeExtraSkillDiv('+j+')');
						$(this).find('.skill').text('Skill '+j+':');
												
												});
	
	skill_no=skill_no - 1;
	$('#h_skill').val(total_skills);
	//alert($('#h_skill').val());
	
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



