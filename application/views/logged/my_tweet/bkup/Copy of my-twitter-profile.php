<?php include("header.php");?>
<!-- NAVIGATION SECTION -->
<?php include("nav.php");?>
<!-- /NAVIGATION SECTION -->
<div class="clr"></div>
<!-- CONTENT SECTION -->
<div id="content_section">
      <div id="container">
            <div id="top_corner">&nbsp;</div>
            <div id="mid_content">
                  <!-- latest news -->
                  <?php include("latest_news.php");?>
                  <!-- /latest news -->
                  <div class="clr"></div>
                  <!-- main container -->
                  <div id="main_container">
                        <!-- left container -->
                        <?php include("leftbar.php");?>
                        <!-- /left container -->
                        <!-- mid container -->
                        <div id="mid_container">
                        	<!--my tweeter profile start -->    
                            <div class="tweeter-profile">
                             	<div class="search-panel">
                                	<p class="title">Profile Summary</p>
                                    <div class="tweeter-search">
                                    <span>Search People:</span>
                                    <form action="" method="post" enctype="multipart/form-data" class="tweeter-search-form">
                                    	<input name="" type="text" id="searchTweeter">
                                        <input name="" type="submit" value="">
                                    </form>
                                    </div>
                                </div>
                                
                                <!--tweeter header start -->  
                                <div style="background:url(images/tweeter-profile-bg.jpg) no-repeat 0 0;" class="tweeter-profile-header">
                                	<div class="follow-section">
                                    	<span class="follow-btn"><input name="Follow" type="button" value="Follow"></span>
                                    </div>
                                    <div class="clr"></div>
                                    <div class="img-thumb"><img src="images/tweeter-profile-pic.jpg" alt="tweeter profile pic" width="73" height="73"></div>
                                    <h2>Stephanie Sammons</h2>
                                    <h3>@StephSammons</h3>
                                </div> 
                                <!--tweeter header end --> 
                                <!--tweeter page nav start --> 
                                <div class="tweeter-nav">
                                	<ul>
                                    	<li class="home select"><a href="javascript:void(0);"><span></span><br>Home</a></li>
                                        <li><a href="javascript:void(0);"><span>666</span><br>My Tweets</a></li>
                                        <li><a href="javascript:void(0);"><span>666</span><br>I'm Following</a></li>
                                        <li><a href="javascript:void(0);"><span>666</span><br>My Followers</a></li>
                                        <li class="favorite"><a href="javascript:void(0);"><span>&nbsp;</span><br>Favorites</a></li>
                                        <li class="last"><a href="javascript:void(0);"><span>#</span><br>Trending</a></li>
                                    </ul>
                                    <div class="clr"></div>
                                </div> 
                                <!--tweeter page nav end -->  
                                <!--write tweet section start -->
                                <div class="write-tweet">
                                	<form action="" method="post" enctype="multipart/form-data">
                                    	<span class="tweet-top-curve"></span>
                                        <div class="tweet-mid-curve"><textarea name="" cols="" rows="" class="tweet-comment-box" id="tweetMessage"></textarea></div>
                                        <span class="tweet-bot-curve"></span>
                                        <input name="Cancel" type="button" value="Cancel" class="tweetCancel">
                                        <input name="" type="submit" value="Tweet" class="tweetSubmit">
                                    </form>
                                    <div class="clr"></div>
                                </div>
                                <!--write tweet section end -->
                            	<div>
                            	  <div class="tweets-blog">
                                  	 <div class="top-part">
                                          <img src="images/tweeter-thumb.jpg" alt="profile img" width="36" height="36" class="tweeter-thumb">
                                          <div class="tweet-container">
                                            <h2><a href="javascript:void(0);"><span>Stephanie Sammons</span> @StephSammons</a></h2>
                                            <p class="date-time">01-May-2013 14:48 PM <img src="images/icons/heart.png" alt="favorite" width="14" height="14" class="favorite-icon" title="Favorite"></p>
                                            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus dignissim, libero et ultrices facilisis, eros turpis facilisis ante, at suscipit turpis odio quis.</p>
                                          </div>
                                          <div class="clr"></div>
                                      </div>
                                      
                                      <!--<ul class="article-comment-block">
                                          <li class="no-bg">Articles (<span><a href="#">0</a></span>)</li> 
                                          <li>Comments (<span><a href="#">2</a></span>)</li>
                                          <li class="right">
                                              <ul>
                                                  <li class="first"><a href="#">Reply</a></li>
                                                  <li><a href="#">Undo favorite</a></li>
                                                  <li><a href="#">Retweet</a></li>
                                              </ul>
                                          </li>
                                      </ul> -->
                                      <div class="liquid">
                                          <ul class="article-comment-block">
                                              <li class="no-bg"><a  href="javascript:void(0);">reply</a></li>
                                              <li><a  href="javascript:void(0);">mark as favorite</a></li>
                                              <li><a  href="javascript:void(0);">retweet</a></li>
                                          </ul>
                                          <div class="clr"></div>
                                      
                                          <div class="tweeterBlock">
                                              <!--reply block start -->
                                              <div class="insideTweeterBlock">
                                              <form action="" method="post">
                                                  <textarea class="big-box" name="" cols="" rows="" onfocus="if(this.value=='Max 140 Characters')this.value='';" onblur="if(this.value=='')this.value='Max 140 Characters';" >Max 140 Characters</textarea>
                                                  <input name="post" type="submit" value="Post" class="small-blue-btn left"/>
                                                  <div class="clr"></div>
                                              </form>
                                              </div>
                                              <!--reply block end -->
                                              <!--mark as favorite block start -->
                                              <div class="insideTweeterBlock">
                                                  <p>Lorem ipsum dolor sit amet</p>
                                                  <div class="clr"></div>
                                                  <input name="favorite" type="button" value="Mark" class="small-blue-btn"/>
                                              </div>
                                              <!--mark as favorite block end -->
                                              <!--retweet block start -->
                                              <div class="insideTweeterBlock">
                                                  <p>Lorem ipsum dolor sit amet</p>
                                                  <div class="clr"></div>
                                                  <input name="retweet" type="button" value="Retweet" class="small-blue-btn" />
                                                  <input name="cancel" type="button" value="Cancel" class="small-green-btn" />
                                              </div>
                                              <!--retweet block end -->
                                          </div>
                                          <div class="clr"></div>
                                      </div>
                                      <div class="clr"></div>
                                  </div>
                                  
                                  <div class="tweets-blog">
                                  	 <div class="top-part">
                                          <img src="images/tweeter-thumb.jpg" alt="profile img" width="36" height="36" class="tweeter-thumb">
                                          <div class="tweet-container">
                                            <h2><a href="javascript:void(0);"><span>Stephanie Sammons</span> @StephSammons</a></h2>
                                            <p class="date-time">01-May-2013 14:48 PM <img src="images/icons/heart.png" alt="favorite" width="14" height="14" class="favorite-icon" title="Favorite"></p>
                                            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus dignissim, libero et ultrices facilisis, eros turpis facilisis ante, at suscipit turpis odio quis.</p>
                                          </div>
                                          <div class="clr"></div>
                                      </div>
                                      
                                      <!--<ul class="article-comment-block">
                                          <li class="no-bg">Articles (<span><a href="#">0</a></span>)</li> 
                                          <li>Comments (<span><a href="#">2</a></span>)</li>
                                          <li class="right">
                                              <ul>
                                                  <li class="first"><a href="#">Reply</a></li>
                                                  <li><a href="#">Undo favorite</a></li>
                                                  <li><a href="#">Retweet</a></li>
                                              </ul>
                                          </li>
                                      </ul> -->
                                      <div class="liquid">
                                          <ul class="article-comment-block">
                                              <li class="no-bg"><a  href="javascript:void(0);">reply</a></li>
                                              <li><a  href="javascript:void(0);">mark as favorite</a></li>
                                              <li><a  href="javascript:void(0);">retweet</a></li>
                                          </ul>
                                          <div class="clr"></div>
                                      
                                          <div class="tweeterBlock">
                                              <!--reply block start -->
                                              <div class="insideTweeterBlock">
                                              <form action="" method="post">
                                                  <textarea class="big-box" name="" cols="" rows="" onfocus="if(this.value=='Max 140 Characters')this.value='';" onblur="if(this.value=='')this.value='Max 140 Characters';" >Max 140 Characters</textarea>
                                                  <input name="post" type="submit" value="Post" class="small-blue-btn left"/>
                                                  <div class="clr"></div>
                                              </form>
                                              </div>
                                              <!--reply block end -->
                                              <!--mark as favorite block start -->
                                              <div class="insideTweeterBlock">
                                                  <p>Lorem ipsum dolor sit amet</p>
                                                  <div class="clr"></div>
                                                  <input name="favorite" type="button" value="Mark" class="small-blue-btn"/>
                                              </div>
                                              <!--mark as favorite block end -->
                                              <!--retweet block start -->
                                              <div class="insideTweeterBlock">
                                                  <p>Lorem ipsum dolor sit amet</p>
                                                  <div class="clr"></div>
                                                  <input name="retweet" type="button" value="Retweet" class="small-blue-btn" />
                                                  <input name="cancel" type="button" value="Cancel" class="small-green-btn" />
                                              </div>
                                              <!--retweet block end -->
                                          </div>
                                          <div class="clr"></div>
                                      </div>
                                      <div class="clr"></div>
                                  </div>
                                  
                                  
                                </div>  
                            </div>
                            <!--my tweeter profile end -->  
                            <div class="pagination">
                                 <div class="left">Viewing Page 1 of 10  </div>
                                 <div class="right"><a  href="javascript:void(0);" class="blue_link">&laquo; Previous</a> <a href="javascript:void(0);">1</a>  <a href="javascript:void(0);">2</a>  <a href="javascript:void(0);">3</a>  <a href="javascript:void(0);">4</a> <a href="javascript:void(0);">5</a>   <a  href="javascript:void(0);" class="blue_link">Next &raquo;</a></div>
                            </div>
                        </div>
                        <!-- /mid container -->
                        <!-- right container -->
                        <?php include("rightbar.php");?>
                        <!-- /right container -->
                  </div>
                  <!-- /main container -->
                  <div class="clr"></div>
            </div>
            <div id="bot_corner">&nbsp;</div>
      </div>
      <div class="clr"></div>
</div>
<!-- /CONTENT SECTION -->
<div class="clr"></div>
<!-- FOOTER SECTION -->
<?php include("footer.php");?>
<!-- /FOOTER SECTION -->


<!-- /lightbox SECTION -->



<div class="lightbox user_status_change" style="width:550px;">
    <div class="close"><a href="javascript:void(0)" onclick="hide_dialog()"><img src="images/close.png" alt="" /></a></div>
    <div class="top"><div>&nbsp;</div></div>
    <div class="mid">
        <div class="heading"><div class="left"><h4> Change your Online Status  </h4></div></div>
           <div class="frm_box">
             <div class="lable01">Status:</div> 
             <div class="field01">  
             <select name="status" id="status" style="width:260px;">
                 <option>Online</option> <option>Offline</option>  <option>Invisible</option><option>Away, Do not disturb</option>
            </select>
            <script type="text/javascript">
                $(document).ready(function(arg) {
                    $("#status").msDropDown();
                    $("#status").hide();
                })
            </script> </div>
             <div class="clr"></div>
             <div class="lable01">Status Message:</div> 
             <div class="field01"><input name="" type="text" /> </div>
             <div class="clr"></div>
            
             <div class="lable01">&nbsp;</div> 
             <div class="field01"><input name="" type="button" value="Change" class="btn" /></div>
             <div class="clr"></div>
           </div>
    </div>
    <div class="bot"><div>&nbsp;</div></div>
</div>


<script type="text/javascript" src="js/jquery.autofill.js"></script>
<script type="text/javascript">
<!--	
	var ticker_holder = $('.bargaining').get(0);
	var ticker_text = $('.ticker').get(0);
	var ticker_pos = ticker_text.parentNode.offsetWidth;
	
	var ticker_data = $(ticker_holder).html();
	$(ticker_text).parent().html('<marquee scrollamount="3" scrolldelay="20">' + ticker_data + '</marquee>');
	
	$('#sub-nav').hover
	(
	function() { $('marquee', this).get(0).stop();  },
	function() { $('marquee', this).get(0).start(); }
	); 
	$(document).ready(function(){
		$("#searchTweeter").autofill({'value':"Type Keyword"}); 
		$("#tweetMessage").autofill({'value':"Compose New Tweet"});
		
		$('.tweet-comment-box').focus(function(){
			$(this).animate({height:'50px'},500);
			$('.tweetSubmit, .tweetCancel').fadeIn('fast',function(){
					$('.tweetCancel').click(function(){
						$(this).parent('form').find('.tweet-comment-box').animate({height:'18px'},500);
						$('.tweetSubmit, .tweetCancel').stop(true,true).slideUp();
					});
			});
		});
		
	});
//-->
</script>
</body>
</html>
