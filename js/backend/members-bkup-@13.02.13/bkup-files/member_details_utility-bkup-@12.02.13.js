

//======================================for add an extra edu div==============================================
function admin_add_extra_edu_div()
{

	//var no_of_divs=$('#h_edu').val();
	var no_of_divs= parseInt( $("table[id^=edu_]:visible").length );
	no_of_divs++;
	//alert(no_of_divs);
	/*var content = '<table id="edu'+'_'+no_of_divs+'"><tr><td class="label" width="180" valign="top" height="30" align="left"></td><td class="label" width="20" valign="top" align="left"></td><td valign="top" align="left" style=" float: right;  margin-right:-12px;"><a class="close" href="javascript:void(0);" onclick="admin_closeExtraEduDiv('+no_of_divs+')" ><img src="'+base_url+'images/icons/close.png" alt="" ></a></td></tr>';
	
	content+= $('.edu_info').html()+"</table>";--old*/

	//alert( $("table[id^=edu_]:visible").length );
	tblID = "edu_"+ no_of_divs;
	$('#div_edu_copy table').attr('id', tblID);
	
	content = $('#div_edu_copy').html();
	
	
	if(no_of_divs<=5)
	{
		$('#h_edu').val(no_of_divs);
		$(content).appendTo('#add_more_edu');	
		//$('#add_more_edu').find('input').val('');
		//$('#edu_'+no_of_divs).find('input').val('');
		//$('#edu_'+no_of_divs).find('#dbId').val('0');
		//$(".txt_edu_country").msDropDown();
		$("table#add_more_edu  select.txt_edu_country1").msDropDown();
		
		
		
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
	
	//var db_id=$("#edu_"+no_of_divs).find('#dbId').val();--OLD
	
	//// set to be deleted "db-id(s)"...
	if( db_id!='' ) {
		var existing_id_str = $('#deleted_edu_divs').val();
		db_id_str = ( existing_id_str!='' )? existing_id_str +'#'+ db_id: db_id;
		$('#deleted_edu_divs').val(db_id_str);
		
		$(tblID).hide();
	} else {
		$(tblID).remove();
	}
	

	//$('#edu_'+no_of_divs).css('display','none');
	showUIMsg("Removed");		//------------------- remove	
	//var i=$('#h_edu').val();
	var i = parseInt( $("table[id^=edu_]:visible").length );
	i=i-1;
	
	$('#h_edu').val(i);
	//for rearrenge the remain divs
	$.each($('[id^=edu_]'),function(index){
									var k=index+1;

									$(this).attr('id','edu_'+k);
									//$(this).find('.close').attr('onClick','admin_closeExtraEduDiv('+k+')');
									
									
										  });
	
}

//======================================for add an extra work div==============================================
function admin_add_extra_work_div()
{

	//var no_of_divs=$('#h_work').val();
	//alert(no_of_divs);
	var no_of_divs= parseInt( $("table[id^=work_]:visible").length );
	no_of_divs++;
	
	/*var content = '<table id="work_'+no_of_divs+'"><tr><td class="label" width="180" valign="top" height="30" align="left"></td><td class="label" width="20" valign="top" align="left"></td><td valign="top" align="left" style=" float: right; margin-right:-12px;"><a class="close" href="javascript:void(0);" onclick="admin_closeExtraWorkDiv('+no_of_divs+')" ><img src="'+base_url+'images/icons/close.png" alt=""></a></td></tr>';
	
	content+= $('#div_copy').html()+"</table>";*/
	tblID = "work_"+ no_of_divs;
	$('#div_copy table').attr('id', tblID);
	$('#div_copy table .radio_cur_emp').val(no_of_divs);
	
	content = $('#div_copy').html();
	
	
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
		$("table#add_more_work  select.txt_work_country1").msDropDown();
		//$("table#add_more_work  .txt_work_country").msDropDown();
        //$("table#add_more_work  .txt_work_country").hide();
		
		
		
		
		
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
	
	
	//// set to be deleted "db-id(s)"...
	if( db_id!='' ) {
		var existing_id_str = $('#deleted_work_divs').val();
		db_id_str = ( existing_id_str!='' )? existing_id_str +'#'+ db_id: db_id;
		$('#deleted_work_divs').val(db_id_str);
		
		$(tblID).hide();
	} else {
		$(tblID).remove();
	}
	
	
	
	showUIMsg("Removed");				//------------ remove
	//var i=$('#h_work').val();
	var i = parseInt( $("table[id^=work_]:visible").length );
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
	$(str).eq(3).attr('disabled', true);
	$(str).eq(4).attr('disabled', true);
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
	

	//var no_of_divs=$('#h_skill').val();
	var no_of_divs = parseInt( $("table[id^=skill_]:visible").length );
	//alert(no_of_divs);
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
	//alert(skill_no);
	var db_id=$('#skill_'+skill_no).find('#dbId').val();
	//if(db_id)
	
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
	//var total_skills=$('#h_skill').val()-1;
	var total_skills=i-1;
	$('#h_skill').val(total_skills);
	

	//$('#skill_'+skill_no).remove();
	showUIMsg("Removed");
	$.each($('[id^=skill_]'),function(i){
												
						var j=i+1;
						
						$(this).attr('id','skill_'+j);
						/*var now_id=$(this).attr('id');
						alert(now_id);
						*/
						
						//var t=$(this).find('.close').attr("onclick");
						
						
						//$(this).find('.close').attr("onclick", "");	// not working in jquery varsion:1.5.2
						//$(this).find('.close').attr('onClick','admin_closeExtraSkillDiv('+j+')');
						$(this).find('.skill').text('Skill '+j+':');
												
												});
	
	/*skill_no=skill_no - 1;
	$('#h_skill').val(total_skills);*/
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



