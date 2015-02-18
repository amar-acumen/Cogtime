<?php

function get_current_language() {
    $CI = get_instance();

     $master_language = $CI->config->item('default_language');
     $languages = $CI->config->item('languages');
	
    $session_language = $CI->session->userdata('current_language');
    /* if no language variable in session default language is master language */
     if(isset($_COOKIE['ZL']['language'])  && array_key_exists( $_COOKIE['ZL']['language'], $languages) ) 
    { 
                
            $current_language = $_COOKIE['ZL']['language']; 
			$CI->session->set_userdata('current_language' , $current_language);
                       
	}
	elseif( $session_language!='' && array_key_exists( $session_language, $languages) ) 
	{
        $current_language = $CI->session->userdata('current_language');
    }
	else
	{
	   $current_language = $master_language;
	}	
				
   /* else 
	{
        # language selection based on logged-in user location [start]...
            $user_location_language = substr($_SERVER["HTTP_ACCEPT_LANGUAGE"], 0, 2);
        # language selection based on logged-in user location [end]...
       
       if( in_array( $user_location_language, $languages ) ) 
          $current_language = $user_location_language;
       else
          $current_language = $master_language;
    }*/

    return $current_language;
}

function t($str) {
    $ci = get_instance();
    $current_language = get_current_language();
     $tc = $ci->get_translations();

    //print_r($tc);

    if($tc !== null) {
        $tf = $tc->getById($str);
        
        if($tf !== null) {
            $data = $tf->getData();
            if( array_key_exists($current_language, $data) && trim($data[$current_language])!='' ) {
                return $data[$current_language];
            }
        }
    }

    return $str;
}

function tp($singular, $plural, $n) {
    $current_language = get_current_language();

    $type = null;
    $type = getplural($current_language, $n);

    if($type=='singular') {
        return t($singular);
    }
    else {
        return t($plural);
    }
}


function getplural($language, $n) {
    if($n==0||$n>1) {
        return 'plural';
    }
    else {
        return 'singular';
    }
}


///// NEW FUNCTIONS
# function to get the toggle-language...
function get_toggle_language($language="")
{
	$selected_language = ( !empty($language) )? $language: get_current_language();
	
	$toggle_language = ( $selected_language=='en' )? 'fr': 'en';
	
	return $toggle_language;
}
