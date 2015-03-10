<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Module_generate extends CI_Controller {

	/*
	**
	****Login Controller
	****Default to check Login and Logout of user
	**
	*/
	
	
	public function __construct(){
		parent::__construct();
		$this->load->driver('minify');
	}

	function deploy(){
       // if($this->session->userdata('admin_id') != '' && $this->session->userdata('admin_logged_in') != false){
            $type = $this->input->get('type');
            $css_dep = true;
            $js_prod = true;
            $js_indi = true;

            if($type == "css"){
                $css_dep = true;
                $js_prod = false;
                $js_indi = false;
            }

            if($type == "jsprod"){
                $css_dep = false;
                $js_prod = true;
                $js_indi = false;
            }

            if($type == "jsindi"){
                $css_dep = false;
                $js_prod = false;
                $js_indi = true;
            }


            if($css_dep){
                // css files
                $files_css = array(
                    'css/style.css',
                    'css/IMchat/chat.css',
                    'css/gemoticons.css',
                    'css/recaptcha.css',
                    'css/jquery-ui-1.8.13.custom.css',
                    'css/dd.css',
                    //user logged in css
                    //'css/jquery-ui-1.8.2.custom.css',
                   /* 'css/jquery-ui-1.8.13.custom.css',
                    'css/dd.css',
                    'css/flexslider.css',
                    'css/jquery.fancybox.css'*/
                    //'css/church.css'
                    //'css/big-slider.css'
                );
                $css = '';
                foreach($files_css as $data){
                    $css .= $this->minify->css->min($data);
                }

                $cssfile = fopen("css/production.css", "w") or die("Unable to open file!");
                fwrite($cssfile, $css);
                fclose($cssfile);

                echo '<br/> production.css';

                $files_css_logged = array(
                    //user logged in css
                    //'css/jquery-ui-1.8.2.custom.css',
                    'css/flexslider.css',
                    'css/jquery.fancybox.css'
                    //'css/church.css'
                    //'css/big-slider.css'
                );
                $css_logged = '';
                foreach($files_css_logged as $data){
                    $css_logged .= $this->minify->css->min($data);
                }

                $cssfile_logged = fopen("css/production_logged.css", "w") or die("Unable to open file!");
                fwrite($cssfile_logged, $css_logged);
                fclose($cssfile_logged);

                echo '<br/> production_logged.css';
            }

            if($js_prod){
                $files_js = array(
                    /*'assets/js/contrib/perfect-scrollbar-0.4.10.with-mousewheel.min.js',
                    'assets/js/contrib/jquery.mCustomScrollbar.concat.min.js',
                    'assets/js/contrib/jquery-ui-1.10.4.custom.min.js',
                    //'assets/js/contrib/gmap3.min.js',*/
                    'js/jquery-1.7.2.js',
                    'js/jquery.js',
                    'js/jquery.cookie.js',
                    'js/jquery/ui/jquery.blockUI.js',
                    'js/jquery/ui/jquery.ui.core.js',
                    'js/frontend/utilities.js',
                    'js/jquery/ui/jquery-ui-1.8.4.custom.js',
                    'js/lightbox.js',
                    'js/jquery.dd.js',
                    'js/jquery.form.js',
                    'js/jquery/JSON/json2.js',
                    'js/ModalDialog.js',
                    'js/frontend/login/login.js',
                    'js/utilities.js',
                    'js/login.js',
                    'js/notification.js',
                    'js/utility_js_for_admin_and_fe.js',
                    'js/frontend/header_slider.js',
                    'js/contentslider.js',
                    /* 'js/jquery.autofill.js',*/
                    // User logged in JS files
                    'js/jquery.hoverIntent.js',
                    'js/frontend/utils.js',
                    'js/ddsmoothmenu.js',
                    'js/switch.js',
                    'js/animate-collapse.js',
                    //'js/jquery-ui-1.8.2.custom.min.js',
                    //'js/stepcarousel.js',
                    //'js/frontend/logged/tweets/tweet_utilities.js',
                    //'js/frontend/logged/christian_news_js.js',
                    /*'js/tab.js',
                    'js/jquery.flexslider.js'
                    ,'js/jquery.eislideshow.js',
                    'js/jquery.hoverIntent.minified.js',
                    'js/jquery.naviDropDown.1.0.js',
                    'js/church_login.js',
                    'js/frontend/logged/holy_place/prayer_group.js',
                    'js/jquery/ui/jquery.ui.core.js',
                    'js/jquery.ui.datepicker.js',
                    'js/jquery.nicescroll.min.js',
                    'js/jquery.naviDropDown.1.0.js',
                    'js/jquery.blueberry.js',*/
                    'js/jquery.bxslider.min.js',
                    'js/jquery.fancybox.js',
                    // logged
                    'js/frontend/public_profile.js'
                );

                $js = '';
                foreach($files_js as $data){
                    $js .= $this->minify->js->min($data);
                }
                //$packer = new JavaScriptPacker($js, 62, true, true);
                //$js = $packer->pack();
                $jsfile = fopen("js/production.js", "w") or die("Unable to open file!");
                fwrite($jsfile, $js);
                fclose($jsfile);
                echo '<br/> production.js';


                $files_logged_js = array(
                    'js/frontend/wall/wall_helper.js',
                    'js/frontend/logged/my_friends.js',
                    'js/frontend/logged/my_net_pals.js',
                    'js/frontend/logged/my_prayer_partner.js',
                    'js/frontend/logged/message_box/my_message.js',
                    // logged
                    'js/frontend/public_profile.js'

                );
                $js_logged = '';
                foreach($files_logged_js as $data){
                    $js_logged .= $this->minify->js->min($data);
                }
                //$packer = new JavaScriptPacker($js, 62, true, true);
                //$js = $packer->pack();
                $jsfilelogged = fopen("js/logged.js", "w") or die("Unable to open file!");
                fwrite($jsfilelogged, $js_logged);
                fclose($jsfilelogged);
                echo '<br/> logged.js';
            }
            // #################  Individual js min generate ###################
            if($js_indi){
                $files_indvid_js = array(
                    'js/jquery-1.7.2.js',
                    'chat/js/chat.js',
                    'js/jquery.gemoticons.js',
                    'js/frontend/header_slider.js',
                    'js/contentslider.js',
                    'js/jquery.autofill.js',
                    'js/stepcarousel.js',
                    'js/frontend/logged/christian_news_js.js',
                    'js/frontend/logged/tweets/tweet_utilities.js',
                    'js/frontend/public_profile.js',
                    'js/frontend/logged/my_friends.js',
                    'js/frontend/logged/my_net_pals.js',
                    'js/frontend/logged/my_prayer_partner.js',
                    'js/frontend/logged/message_box/my_message.js',
                    'js/frontend/logged/video_helper.js',
                    'js/frontend/logged/events/my_events.js'
                  //  'js/jquery.hoverIntent.js',
                  //  'js/frontend/utils.js'
                );
                $js_indvid = '';
                foreach($files_indvid_js as $data){

                    $file_name  = explode('/',$data);
                    $file_name = $file_name[count($file_name)-1];
                    $js_indvid = $this->minify->js->min($data);

                   // $packer = new JavaScriptPacker($js_indvid, 62, true, true);
                   // $js_indvid = $packer->pack();

                    $jsfile = fopen("js/production/".$file_name, "w") or die("Unable to open file!");
                    fwrite($jsfile, $js_indvid);
                    fclose($jsfile);
                    echo '<br/> Individual files : '.$file_name;
                }
            }

      /* }else{
           echo 'Unauthorized access.';
           exit;
        }*/




		
	}

	
	
}

