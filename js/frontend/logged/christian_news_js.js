function like_(id)
{
    showBusyScreen();
    $.ajax({
        url : base_url+"logged/christian_news/like_news",
        type : 'post',
        dataType : 'json',
        data : ({'news_id':id}),
        success : function (data)
        {
            
            if(data.result==true)
            {
                
                $('#like').html(data.likes);
				$('#like_con').html(data.likes);
            }
            else
            {
                
            showUIMsg(data.msg);
            }
            hideBusyScreen();
        }
    });
}

function showPostComment(){
	$('.commentspost').slideToggle('slow');
}

function comment_(id,URL)
{
    
    
    var comment = $('#txtarea_comment'+id).val();
    $.ajax({
        url : base_url+URL,
        type : 'post',
        dataType : 'json',
        data : ({'news_id' : id,'comment':comment}),
        success : function(data)
        {
            
            showUIMsg(data.msg);
            if(data.result==true)
            {
                
                $('#comment').html(data.comments);
				$('#comment_con').html(data.comments);
				$('#comentList').html(data.html);
                clear_frm();
				$('.commentspost').slideToggle('slow');
                //hide_dialog('comment_div');
            }
            else
            {
                //showUIMsg(data.msg);
            }
            
            
        }
    });
    
}

function clear_frm()
{
    $('#comment_frm')[0].reset();
}


function show_comments(news_id)
  {
		$('#comment_content_div'+news_id).slideToggle('slow');

	    $('#h_news_id').val(news_id);
        showUILoader_nodialog('<img src="'+base_url+'images/loading_big.gif" width="50"/> ');
        $.ajax({
            
            url: base_url +'logged/christian_news/NEW_fetch_comment_christian_news/'+news_id,
            dataType: 'json',
            type: 'post',
            data : ({'news_id':news_id}),
    
            success: function (data, status) {
              
                $('#comment_content'+news_id).html(data.html_data);
                hideUILoader_nodialog();
                //hideBusyScreen();
                //showUIMsg(data.msg);
            },
            error: function(data, status, e) {
                hideUILoader_nodialog();
                //show_dialog('view_comments_div');
                showUIMsg("Error!");
                
            }
        });
                 
         
  }
  
  
  
  
  function show_people_liked(news_id)
  {
       //$('.christian-comt > .comments-number').hide();//('display','none');
	     
	  //$('.comments-number').hide();
	   $('#like_content_div'+news_id).slideToggle('slow');
	   $('#h_news_id').val(news_id);
       showUILoader_nodialog('<img src="'+base_url+'images/loading_big.gif" width="50"/> ');
        $.ajax({
            
           /* url: base_url +'logged/christian_news/fetch_people_liked_post/',*/
		    url: base_url +'logged/christian_news/new_fetch_likes_on_christian_news/'+news_id,
            dataType: 'json',
            type: 'post',
            data : ({'news_id':news_id}),
    
            success: function (data, status) {
                //hideUILoader();
                 //$('#liked_html').html(data.html_data);
				 $('#like_content'+news_id).html(data.html_data);
                 hideUILoader_nodialog();
                //hideBusyScreen();
                //showUIMsg(data.msg);
            },
            error: function(data, status, e) {
                hideUILoader_nodialog();
                showUIMsg("Error!");
                
            }
        });
                 
         
  }
  
  
  
  
  var ajax_req_feeds = null;
function show_more_comments(page) {
    if(ajax_req_feeds!=null) {
        ajax_req_feeds.abort();
    }
    var news_id = $('#h_news_id').val();

    showUILoader_nodialog();
    ajax_req_feeds = $.ajax({
        type: 'get',
        url: base_url+'logged/christian_news/comments_ajax_pagination/'+news_id+'/'+page,
        dataType: 'json',

        success: function (data, status) {
  
            hideUILoader_nodialog();
            if(data.html!='') {
               
                $('#view_comments').append(data.html);
                $('#show_more_news').attr('page_value', data.current_page);
                 
            }
            else {
                $('#view_comments').append('<div class="section01" style="padding-top:5px;"><div  class="shade_norecords" style=""><p class="blue_bold12">No More Comment.</p></div></div>');
                $('#show_more_news_div').hide();
            }
            if(data.view_more==false)
            {
                $('#view_comments').append('<div class="section01" style="padding-top:5px;"><div  class="shade_norecords" style=""><p class="blue_bold12">No More Comment.</p></div></div>');
                $('#show_more_news_div').hide();
            }
            
           

        }    // end of success function...
    });
}




  var ajax_req_feeds = null;
function show_more_likes(page) {
    if(ajax_req_feeds!=null) {
        ajax_req_feeds.abort();
    }
    var news_id = $('#h_news_id').val();

    showUILoader_nodialog();
    ajax_req_feeds = $.ajax({
        type: 'get',
        url: base_url+'logged/christian_news/fetch_people_liked_post_ajax/'+news_id+'/'+page,
        dataType: 'json',

        success: function (data, status) {
  
            hideUILoader_nodialog();
            if(data.html!='') {
               
                $('#view_people_liked').append(data.html);
                $('#show_more_news').attr('page_value', data.current_page);
                 
            }
            else {
                ('#view_people_liked').append('<div class="section01" style="padding-top:5px;"><div  class="shade_norecords" style="width:270px;"><p class="blue_bold12">No More Like.</p></div></div>');
                $('#show_more_news_div').hide();
            }
            if(data.view_more==false)
            {
                $('#view_people_liked').append('<div class="clr"></div><div class="section01" style="padding-top:5px;"><div class="shade_norecords" style="width:262px"><p class="blue_bold12">No More Like.</p></div></div>');
                $('#show_more_news_div').hide();
            }
            
           

        }    // end of success function...
    });
}