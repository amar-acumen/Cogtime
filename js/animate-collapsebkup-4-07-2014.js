     $(document).ready(function() {
          $('#lnkbtn').click(function() {
               if ($('#divContent').is(":hidden"))
               {
                    $('#divContent').slideDown("slow");
				
                    document.getElementById('lnkbtn').innerHTML = 'collapse... ';
               } else {
                    $('#divContent').slideUp("slow");
                    document.getElementById('lnkbtn').innerHTML = 'expand... ';
               }
          });
     });
	 
	 
/*-----------------------------------------------------------------------------------------------------	 */     
$(document).ready(function() {
          $('#lnkbible').click(function() {
               if ($('#bibleContent').is(":hidden"))
               {
                    $('#bibleContent').slideDown("slow");
					$('#bibleContent_list').slideUp("slow");
				
                    document.getElementById('lnkbible').innerHTML = '<img src="images/icons/table.png" alt="" /> <strong>Table View</strong> ';
               } 
			   else {
                    $('#bibleContent').slideUp("slow");
					$('#bibleContent_list').slideDown("slow");

                    document.getElementById('lnkbible').innerHTML = '<img src="images/icons/list.png" alt="" /> <strong>List View</strong>  ';
               }
          });
     });
	 
	 
/*-----------------------------------------------------------------------------------------------------	 */

var g_LAST_OPEN_DIV = -1;
	function showDiv(id) {
		divID = '#jason'+ id;
		linkID = '#'+ id;
		$(linkID).slideUp('slow', function() {
			$(divID).slideDown('slow');
		});
		
		if(g_LAST_OPEN_DIV > 0) closeDiv(g_LAST_OPEN_DIV);
		g_LAST_OPEN_DIV = id;
		
	}
	function closeDiv(id)
	{
		divID = '#jason'+ id;
		linkID = '#'+ id;
		$(divID).slideUp("slow");
		$(linkID).show();
		g_LAST_OPEN_DIV = -1;
	}