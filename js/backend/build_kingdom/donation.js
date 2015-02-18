function  show_donations(tabname){
	
	 showUILoader_nodialog('<img src="'+base_url+'images/loading_big.gif" width="50"/> ');
	
		
		$.ajax({
				type: 'get',
				url: admin_base_url+'build_kingdom/donation/donation_ajax_pagination/'+ tabname,
				success: function (data, status) {
							
							
							$('#'+tabname+'_donation_content').html('');
							$('#'+tabname+'_donation_content').html(data);
							
							$('.tab_content ul li').removeClass('select');
							$('#'+tabname).addClass('select');
							$('.tab_details > div').hide();
							$('#'+tabname+'_donation_content').show();
							
							hideUILoader_nodialog();
							
					}	// end of success function...
			});

}

