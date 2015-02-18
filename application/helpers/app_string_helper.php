<?php
function short_text($txt, $length) {
    return preg_replace('/(.*\.)[^\.]*$/', '$1', substr($txt, 0, $length));
}

function my_substr($txt,$length) {
	
    if(utf8_strlen($txt)>$length) {
        return utf8_substr($txt, 0, $length).'...';
    }
    else
        return $txt;
}


function is_int_val($data) {
    if (is_int($data) === true)
        return true;

    elseif (is_string($data) === true && is_numeric($data) === true) {
        return (utf8_strpos($data, '.') === false);
    }
    return false;
}

function is_numeric_val($data) {
    return preg_match('/^[\d]*[\.]?[\d]*$/', $data);
}


function sprintf3($str, $vars, $char = '%%') {
    $tmp = array();
    foreach($vars as $k => $v) {
        $tmp[$char . $k . $char] = $v;
    }
    return str_replace(array_keys($tmp), array_values($tmp), $str);
}

function implode_with_quote($glue, $arr) { //like Array(21,suman,hello) converted to "'21', 'suman', 'hello'"
    $csv='';
    for($i=0; $i<count($arr); $i++) {
        $item = utf8_trim($arr[$i]);

        if($item!='') {
            if($i!=count($arr)-1)
                $csv .= "'".$item."'".$glue;
            else
                $csv .= "'".$item."'";
        }
    }
    return $csv;
}

function explode_trim($seperator, $str) {
    $arr = explode($seperator, $str);
    $new_arr = array();
    foreach($arr as $key=>$item) {
        $new_arr[$key] = $item;
    }

    return $new_arr;
}

function basic_array($arr) {
    $new_arr = array();
    foreach($arr as $item) {
        $new_arr = array_merge($new_arr, array_values($item));
    }

    return array_unique($new_arr);
}

function basic_array_by_field($arr, $field) {
    $new_arr = array();
    foreach($arr as $item) {
        $new_arr[] = $item[$field];
    }

    return array_unique($new_arr);
}


function correct_csv($csv) {
    $csv = utf8_trim(preg_replace('/(,[\s]*)(?:[,\s]+)/', '$1', $csv), ',');
    $arr = explode(',', $csv);
    $correct_csv = implode(',', array_unique($arr));
    return $correct_csv;
}

function put_in_set($source, $list) {
    $new_array = array_unique($source);

    if( is_array($list) && count($list)>0 ) {
        $temp_arr = array_diff($list, $source);
        $new_array = array_merge($new_array, $temp_arr);
    }
    else if( ! is_array($list) && $list!='' ) {
        if( !in_array($list, $source) ) {
            $new_array[] = $list;
        }
    }
    else {
        return array_unique($source);
    }

    return $new_array;
}

function count_csv($str) {
    if( utf8_trim($str)=='' ) {
        return 0;
    }
    $arr = explode(',', $str);
    return count($arr);
}

function br2nl($text) {
    return preg_replace('/<br\\s*?\/??>/i', '', $text);
}

function file_get_contents_utf8($fn) {
    $content = file_get_contents($fn);
    return mb_convert_encoding($content, 'UTF-8');
}


function get_ext($filename) {
    $matches = array();
    $return_arr = array('filename'=>'', 'ext'=>'');

    preg_match('/(^.*)\.([^\.]*)/', $filename, $matches);
    if( isset($matches[1]) ) {
        $return_arr['filename'] = $matches[1];
    }
    if( isset($matches[2]) ) {
        $return_arr['ext'] = $matches[2];
    }

    return $return_arr;
}

function is_assoc($array) { 
    foreach (array_keys($array) as $k => $v) {
        if ($k !== $v) {
            return true;
        }
    }
    return false;
}


function escape_singlequotes($str) {
    $chars= array("'", "&#039;");
    foreach($chars as $char) {
        switch ($char) {
            case "'":
                $str = str_replace($char, "\'", $str);
                break;
            case "\"":
                $str = str_replace($char, "\\\"", $str);
                break;
            case "&#039;":
                $str = str_replace($char, "\&#039;", $str);
                break;
            case "&quot;":
                $str = str_replace($char, "\&quot;", $str);
                break;
        }
    }

    return $str;
}

function escape_doublequotes($str) {
    $chars= array("\"", "&quot;");
    foreach($chars as $char) {
        switch ($char) {
            case "'":
                $str = str_replace($char, "\'", $str);
                break;
            case "\"":
                $str = str_replace($char, "\\\"", $str);
                break;
            case "&#039;":
                $str = str_replace($char, "\&#039;", $str);
                break;
            case "&quot;":
                $str = str_replace($char, "\&quot;", $str);
                break;
        }
    }

    return $str;
}


function nl2br2($string) {
    $string = str_replace(array("\r\n", "\r", "\n"), "", $string);
    return $string;
}


function dump($obj) {
    echo '<pre>';
    print_r($obj);
    echo '</pre>';
}





function replace_special_char($str) {
    return trim(preg_replace('/[^a-z0-9]+/', '-', strtolower($str)), '-');
}


## added new to sort friends
function array_sort_by_column(&$arr, $col, $dir = SORT_ASC) {
	$sort_col = array();
	foreach ($arr as $key=> $row) {
		$sort_col[$key] = $row[$col];
	}

	array_multisort($sort_col, $dir, $arr);
}

### WORDS STARTING WITH "#" TWITTER SECTION #### 

function get_twitter_tags($string) {
	 $arr_matches = array();
	 preg_match_all('/(?!\b)(#\w+\b)/',$string,$arr_matches);
	 //pr($arr_matches);  
	 return $arr_matches[1];
}


### WORDS STARTING WITH "@" TWITTER SECTION O EXRACT TWIT_USER_ID #### 

function get_twitter_uid($string) {
	 $arr_matches = array();
	 preg_match_all('/(?!\b)(@\w+\b)/',$string,$arr_matches);    
	 return $arr_matches[1];
}

###added  to spit a string with special chars into array 

  function str_split_unicode($str, $l = 0) 
  {
		  if ($l > 0) {
			  $ret = array();
			  $len = mb_strlen($str, "UTF-8");
			  for ($i = 0; $i < $len; $i += $l) {
				  $ret[] = mb_substr($str, $i, $l, "UTF-8");
			  }
			  return $ret;
		  }
		  return preg_split("//u", $str, -1, PREG_SPLIT_NO_EMPTY);
  }
  
  
/**
* word-sensitive substring function with html tags awareness
* @param text The text to cut
* @param len The maximum length of the cut string
* @returns string
**/ 
function substrws( $text, $len=180 ) {

    if( (strlen($text) > $len) ) {

        $whitespaceposition = strpos($text," ",$len)-1;

        if( $whitespaceposition > 0 )
            $text = substr($text, 0, ($whitespaceposition+1));

        // close unclosed html tags
        if( preg_match_all("|<([a-zA-Z]+)>|",$text,$aBuffer) ) {

            if( !empty($aBuffer[1]) ) {

                preg_match_all("|</([a-zA-Z]+)>|",$text,$aBuffer2);

                if( count($aBuffer[1]) != count($aBuffer2[1]) ) {

                    foreach( $aBuffer[1] as $index => $tag ) {

                        if( empty($aBuffer2[1][$index]) || $aBuffer2[1][$index] != $tag)
                            $text .= '</'.$tag.'>';
                    }
                }
            }
        }
		
		$text = $text.'...';
    }

    return $text;
} 


