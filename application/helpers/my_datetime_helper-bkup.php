<?php

set_include_path(APPPATH.'libraries' . PATH_SEPARATOR . get_include_path());
include('I18N/DateTime.php');

function getShortDate($date, $format='') {

	getShortDateWithTime($date);

    $current_language = get_current_language();
    
    if( $current_language == 'en') {
        $dateTime = new I18N_DateTime( 'en_GB' );
    }
    else if( $current_language == 'fr') {
       # $dateTime = new I18N_DateTime( 'fr_FR' );
	    $dateTime = new I18N_DateTime( 'fr_FR' );
    }
    else if( $current_language == 'ge') {
        $dateTime = new I18N_DateTime( 'en_GB' );
    }
    else if( $current_language == 'tr') {
        $dateTime = new I18N_DateTime( 'en_GB' );
    }


    if($format==1) {
        return date('d/m/y', strtotime($date));
    }
    else if($format==2) {
        return date('d-m-Y', strtotime($date));
    }
    else if($format==3) {
        return date('d/m/Y', strtotime($date));
    }

    /* 13 août 2010 */
    else if($format==4) {
        $myFormat = $dateTime->setFormat('d F Y');

        return $dateTime->format(strtotime($date));
    }

    /* 13 aug 2010  used in admin section donot change*/
    else if($format==5) {
        $myFormat = $dateTime->setFormat('d-M-Y');

        return $dateTime->format(strtotime($date));
    }

    /* 13th août */
    else if($format==6) {
        $myFormat = $dateTime->setFormat('jS F');
        
        $formatted_date = $dateTime->format(strtotime($date));

        return $formatted_date;
    }

    /* Vendredi 13 août 2010  USED IN ORGANIZER SECTION DONT CHANGE*/
    else if($format==7) {
        $myFormat = $dateTime->setFormat('l, d-M-Y');

        return ucfirst($dateTime->format(strtotime($date)));
    }
	
	else if($format==8) {
        return date('Y-m-d', strtotime($date));
    }
	/*may 1 2011, 13:00pm */
	else if($format==9) {
        $myFormat = $dateTime->setFormat('F  d, Y');

        return ucfirst($dateTime->format(strtotime($date)));
    }
	 else if($format==10) {
        return date('Y/m/d', strtotime($date));
    }
	   
	else if($format==11) {
        return date('Y-m-d', strtotime($date));
    }

    /* Date format from Config File */
    else  {
        
        $CI= &get_instance();
        $myFormat = $dateTime->setFormat($CI->config->item('display_date_format'));

        return ucfirst($dateTime->format(strtotime($date)));
    }
}

function getShortDateWithTime($date, $format=1) {
    $current_language = 'en';
	/*
	// ADDED BY CLIENT REQUEST //
	$format = 2;
    */
    
 if( $current_language == 'en') {
        $dateTime = new I18N_DateTime( 'en_GB' );
    }
    else if( $current_language == 'fr') {
       # $dateTime = new I18N_DateTime( 'fr_FR' );
	    $dateTime = new I18N_DateTime( 'fr_FR' );
    }
    else if( $current_language == 'ge') {
        $dateTime = new I18N_DateTime( 'en_GB' );
    }
    else if( $current_language == 'tr') {
        $dateTime = new I18N_DateTime( 'en_GB' );
    }


    if($format==1) {
        if( $current_language == 'en') {
            $myFormat = $dateTime->setFormat('H:i:s');
        }
        else if( $current_language == 'fr') {
            $myFormat = $dateTime->setFormat('H:i:s');
        }
    }
    else if($format==2) { # used in prayer group notifications section
        if( $current_language == 'en') {
            $myFormat = $dateTime->setFormat('F d, Y \a\t H:i');
        }
        else if( $current_language == 'fr') {
            $myFormat = $dateTime->setFormat('l d F Y à H\hi');
        }
    }
	else  if($format==3) {
        if( $current_language == 'en') {
            $myFormat = $dateTime->setFormat('H:i d/m/Y');
        }
        else if( $current_language == 'fr') {
            $myFormat = $dateTime->setFormat('d/m/y à H\hi');
        }
    }
	  else if($format==4) {
        if( $current_language == 'en') {
            $myFormat = $dateTime->setFormat('H:i A');
        }
        else if( $current_language == 'fr') {
            $myFormat = $dateTime->setFormat('à H\hi');
        }
    }
	else if($format==5) {
		  if( $current_language == 'en') {
			  $myFormat = $dateTime->setFormat('d/m/y  H:i:s A');
		  }
		  else if( $current_language == 'fr') {
			  $myFormat = $dateTime->setFormat('d/m/y à H\hi');
		  }
    }/* used in fO*/
	else if($format==6) {
		  if( $current_language == 'en') {
			  $myFormat = $dateTime->setFormat('d-M-Y  H:i');
		  }
		  else if( $current_language == 'fr') {
			  $myFormat = $dateTime->setFormat('d-M-Y à H\hi');
		  }
    }/* used in backend admin events section dont change*/
	else if($format==7) {
		  if( $current_language == 'en') {
			  $myFormat = $dateTime->setFormat('d-M-Y  H:i');
		  }
		  else if( $current_language == 'fr') {
			  $myFormat = $dateTime->setFormat('d-M-Y à H\hi');
		  }
    }/* used in backend admin events section dont change*/
	else if($format==8) {
		  if( $current_language == 'en') {
			  $myFormat = $dateTime->setFormat('H:i');
		  }
		  else if( $current_language == 'fr') {
			  $myFormat = $dateTime->setFormat('H\hi');
		  }
    }
    else if($format==9) {
          if( $current_language == 'en') {
              $myFormat = $dateTime->setFormat('d-M-Y  H:i');
          }
          else if( $current_language == 'fr') {
              $myFormat = $dateTime->setFormat('d-M-Y à H\hi');
          }
    }/* used in backend admin intercession & prayer wall section dont change*/
	else if($format==10) {
            $myFormat = $dateTime->setFormat('g');
    }
	else if($format==11) {
            $myFormat = $dateTime->setFormat('a');
    }
	else if($format==12) {
            $myFormat = $dateTime->setFormat('H');
    }
	else if($format==13) {
            $myFormat = $dateTime->setFormat('H:i:s');
    }
	/* for events */
	else if($format==14) {
		  if( $current_language == 'en') {
			  $myFormat = $dateTime->setFormat('d-m-Y  H:i');
		  }
		  else if( $current_language == 'fr') {
			  $myFormat = $dateTime->setFormat('d-m-Y à H\hi');
		  }
	
    #return $dateTime->format();
    return $dateTime->format(strtotime($date));
}


function getShortTime($mysql_time, $format=1) {
    $current_language = get_current_language();

    $meridian = 'am';
    
    $hour = substr($mysql_time, 0, 2);

    $hour_original = $hour;

    if($hour>12) {
        $hour = $hour - 12;
        $meridian = 'pm';
    }

    $minute = substr($mysql_time, 3, 2);
    $second = substr($mysql_time, 6, 2);

    if($format==1) {
        if( $current_language == 'en') {
            return $hour.':'.$minute.$meridian;
        }
        else if( $current_language == 'fr') {
            return $hour.'h'.$minute.$meridian;
        }
    }
    else if($format==2) {
        if( $current_language == 'en') {
            return $hour_original.':'.$minute;
        }
        else if( $current_language == 'fr') {
            return $hour_original.'h'.$minute;
        }
    }
}


function get_age_from_dob($dob) {
    if($dob=='') {
        return '&nbsp;';
    }
    $year_dob = substr($dob, 0, 4);
    $month_dob = substr($dob, 5, 2);
    $day_dob = substr($dob, 8, 2);

    $total_dob = $year_dob*365+$month_dob*30+$day_dob;

    $now = date('Y-m-d');

    $year_now = substr($now, 0, 4);
    $month_now = substr($now, 5, 2);
    $day_now = substr($now, 8, 2);

    $total_now = $year_now*365+$month_now*30+$day_now;
    

    $age = (int) (($total_now-$total_dob)/(365));

    if( $age <= 10 || $age >=200 ) {
        return '';
    }

    return $age;
}


function get_time_elapsed($datetime) {
//     $a = 'a moments ago';
//     $b = '12 seconds ago';
//     $c = '34 minutes ago';
//     $d = 'about an hour ago';
//     $e = '2 hours ago';
//     $f = '12 hours ago';
//     $a = 'Yesterday at 11:44pm';
//     $a = 'Saturday at 5:58pm';
//     $a = 'May 20 at 8:46pm';
//     $a = 'May 16 at 12:37pm';

    $current_language = 'en';//get_current_language();
# echo ' @@'.get_db_datetime();
    $current_timestamp = strtotime(get_db_datetime());
    $another_timestamp = strtotime($datetime);

    $time_diff = $current_timestamp - $another_timestamp;

    $str = '';

    if( $current_language == 'en') {
        $dateTime = new I18N_DateTime( 'en_GB' );

        if( $time_diff == 0 || $time_diff == 1 ) {
            $str = 'a moment ago';
        }
        else if( $time_diff <= 59 ) {
             $str = $time_diff.' seconds ago';
        }
        else if( (int) ($time_diff/60) <= 59 ) {
            $minute_diff = (int) ($time_diff/60);
            if( $minute_diff == 1 ) {
                $str = $minute_diff.' minute ago';
            }
            else {
                $str = $minute_diff.' minutes ago';
            }
        }
        else if( (int) ($time_diff/3600) < 24 ) {
            $hour_diff = (int) ($time_diff/3600);
            if( $hour_diff == 1 ) {
                $str = $hour_diff.' hour ago';
            }
            else {
                $str = $hour_diff.' hours ago';
            }
        }
		else {
			$str = getShortDateWithTime($datetime,9); // 6
		}
        /*else if( (int) ($time_diff/3600) < 48 ) {
            $myFormat = $dateTime->setFormat('\a\t g:i a');
            $str = 'Yesterday '.$dateTime->format($another_timestamp);
        }
        else if( (int) ($time_diff/3600) < 96 ) {
            $myFormat = $dateTime->setFormat('l \a\t g:i a');
            $str = $dateTime->format($another_timestamp);
        }
        else {
            $myFormat = $dateTime->setFormat('F d \a\t g:i A');
            $str = $dateTime->format($another_timestamp);
        }*/
    }
    else if( $current_language == 'fr') {
        $dateTime = new I18N_DateTime( 'fr_FR' );

        if( $time_diff == 0 || $time_diff == 1 ) {
            $str = 'il ya un instant';
        }
        else if( $time_diff <= 59 ) {
            $str = 'il ya '.$time_diff.' secondes';
        }
        else if( (int) ($time_diff/60) <= 59 ) {
            $minute_diff = (int) ($time_diff/60);
            if( $minute_diff == 1 ) {
                $str = 'il ya '.$minute_diff.' minute';
            }
            else {
                $str = 'il ya '. $minute_diff.' minutes';
            }
        }
        else if( (int) ($time_diff/3600) < 24 ) {
            $hour_diff = (int) ($time_diff/3600);
            if( $hour_diff == 1 ) {
                $str = 'il ya '.$hour_diff.' heure';
            }
            else {
                $str = 'il ya '.$hour_diff.' heures';
            }
        }
		else {
			getShortDateWithTime($datetime);
		}
        /*else if( (int) ($time_diff/3600) < 48 ) {
            $myFormat = $dateTime->setFormat('à g\hi a');
            $str = 'Hier '.$dateTime->format($another_timestamp);
        }
        else if( (int) ($time_diff/3600) < 96 ) {
            $myFormat = $dateTime->setFormat('l à g\hi a');
            $str = $dateTime->format($another_timestamp);
        }
        else {
            $myFormat = $dateTime->setFormat('F d à g\hi A');
            $str = $dateTime->format($another_timestamp);
        }*/
    }

    return $str;
}




function get_formated_time($total_seconds) {
	$hours = $mins = $secs = 0;

	$mins = (int) ($total_seconds / 60);
	$secs = $total_seconds % 60;
	
	if($mins>=60) {
		$hours = (int) ($mins / 60);
		$mins = $mins % 60;
	}
	
	$return_str = '';
	
	$current_language = get_current_language();
    
    if($current_language == 'en') {
		if($hours==1) {
			$return_str .= $hours.' hour ';
		}
		else if($hours!=0) {
			$return_str .= $hours.' hours ';
		}
		
		if($mins==1) {
			$return_str .= $mins.' minute ';
		}
		else if($mins!=0) {
			$return_str .= $mins.' minutes ';
		}
		
		if($secs==1) {
			$return_str .= $secs.' second ';
		}
		else if($secs!=0) {
			$return_str .= $secs.' seconds ';
		}
	}
	else if($current_language == 'fr') {
		if($hours==1) {
			$return_str .= $hours.' hour ';
		}
		else if($hours!=0) {
			$return_str .= $hours.' hours ';
		}
		
		if($mins==1) {
			$return_str .= $mins.' minute ';
		}
		else if($mins!=0) {
			$return_str .= $mins.' minutes ';
		}
		
		if($secs==1) {
			$return_str .= $secs.' second ';
		}
		else if($secs!=0) {
			$return_str .= $secs.' seconds ';
		}
	}
	
	return $return_str = trim($return_str, ', ');
}



//// function to get date in specified format...
function getDesiredDate($date, $format)
{
    $current_language = get_current_language();
    
    if( $current_language == 'en') {
        $dateTime = new I18N_DateTime( 'en_GB' );
    }
    else if( $current_language == 'fr') {
        $dateTime = new I18N_DateTime( 'fr_FR' );
    }
    
	# $dateTime = new I18N_DateTime( 'en_GB' );
	$customFormat = $dateTime->setFormat($format);

	$formatted_dt = $dateTime->format(strtotime($date));

	return $formatted_dt;
}


# month-name in desired language format...
function getMonthName($monthIndex, $current_language='en')
{
	#$current_language = get_current_language();

		if( $current_language == 'en') {
				$dateTime = new I18N_DateTime( 'en_GB' );
		}
		else if( $current_language == 'fr') {
				$dateTime = new I18N_DateTime( 'fr_FR' );
		}

		$myFormat = $dateTime->setFormat('F');

		$strTime = mktime(0,0,0,$monthIndex);
		$month_name = $dateTime->format($strTime);

		#return utf8_encode($month_name);
		return  $month_name;
}


/////////GET DATABASE DATETIME////////
function get_db_datetime() {
	try
	{ 
		return date('Y-m-d H:i:s');
	   /* $CI= &get_instance();
		$sq= "SELECT NOW() AS db_dt ";
		$rs = $CI->db->query($sq);
		if(is_array($rs->result())) {
			  foreach($rs->result() as $row)
			  {
				  $ret_=$row->db_dt; 
			  }    
			  $rs->free_result();          
		  }
	   
	   return $ret_;*/
	}
	catch(Exception $err_obj)
	{
		show_error($err_obj->getMessage());
	}   
  }
  
/*  
  function dateDifference($startDate, $endDate)
	{
		$startDate = strtotime($startDate);
		$endDate = strtotime($endDate);
		if ($startDate === false || $startDate < 0 || $endDate === false || $endDate < 0 || $startDate > $endDate)
			return false;
		   
		$years = date('Y', $endDate) - date('Y', $startDate);
	   
		$endMonth = date('m', $endDate);
		$startMonth = date('m', $startDate);
	   
		// Calculate months
		$months = $endMonth - $startMonth;
		if ($months <= 0)  {
			$months += 12;
			$years--;
		}
		if ($years < 0)
			return false;
	   
		// Calculate the days
					$offsets = array();
					if ($years > 0)
						$offsets[] = $years . (($years == 1) ? ' year' : ' years');
					if ($months > 0)
						$offsets[] = $months . (($months == 1) ? ' month' : ' months');
					$offsets = count($offsets) > 0 ? '+' . implode(' ', $offsets) : 'now';

					$days = $endDate - strtotime($offsets, $startDate);
					$days = date('z', $days);   
				   
		return array($years, $months, $days);
	} 
*/



function date_difference ($first, $second)
{
    $month_lengths = array (31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);

    $retval = FALSE;

    if (    checkdate($first['month'], $first['day'], $first['year']) &&
            checkdate($second['month'], $second['day'], $second['year'])
        )
    {
        $start = smoothdate ($first['year'], $first['month'], $first['day']);
        $target = smoothdate ($second['year'], $second['month'], $second['day']);
                            
        if ($start <= $target)
        {
            $add_year = 0;
            while (smoothdate ($first['year']+ 1, $first['month'], $first['day']) <= $target)
            {
                $add_year++;
                $first['year']++;
            }
                                                                                                            
            $add_month = 0;
            while (smoothdate ($first['year'], $first['month'] + 1, $first['day']) <= $target)
            {
                $add_month++;
                $first['month']++;
                
                if ($first['month'] > 12)
                {
                    $first['year']++;
                    $first['month'] = 1;
                }
            }
                                                                                                                                                                            
            $add_day = 0;
            while (smoothdate ($first['year'], $first['month'], $first['day'] + 1) <= $target)
            {
                if (($first['year'] % 100 == 0) && ($first['year'] % 400 == 0))
                {
                    $month_lengths[1] = 29;
                }
                else
                {
                    if ($first['year'] % 4 == 0)
                    {
                        $month_lengths[1] = 29;
                    }
                }
                
                $add_day++;
                $first['day']++;
                if ($first['day'] > $month_lengths[$first['month'] - 1])
                {
                    $first['month']++;
                    $first['day'] = 1;
                    
                    if ($first['month'] > 12)
                    {
                        $first['month'] = 1;
                    }
                }
                
            }
                                                                                                                                                                                                                                                        
            #$retval = array ('years' => $add_year, 'months' => $add_month, 'days' => $add_day);
			
			$retval = array($add_year, $add_month, $add_day);
        }
    }
                                                                                                                                                                                                                                                                                
    return $retval;
}

function dateDifference($customer_dob,$current_date)
 {
  
	$date_diff_array =array();
	$array_customer_dob = explode("-",date("Y-m-d",strtotime($customer_dob)));
	$array_policy_create_dt = explode("-",date("Y-m-d",strtotime($current_date)));
	
	$begin = array ('year' => $array_customer_dob[0], 'month' => $array_customer_dob[1], 'day' => $array_customer_dob[2]);
	$end =  array ('year' => $array_policy_create_dt[0], 'month' => $array_policy_create_dt[1], 'day' => $array_policy_create_dt[2]);
	$date_diff_array = date_difference ($begin, $end);
	return $date_diff_array ;
 }
 
 function smoothdate ($year, $month, $day)
{
    return sprintf ('%04d', $year) . sprintf ('%02d', $month) . sprintf ('%02d', $day);
}

function get_month_difference($date1,$date2)
{
	$date1 =  explode('-',$date1);
	$date2 =  explode('-',$date2);	
	$d1	=	mktime(0,0,0,$date1[1],$date1[2],$date1[0]);
	$d2	=	mktime(0,0,0,$date2[1],$date2[2],$date2[0]);
	$month	= floor(($d2-$d1)/2628000); 
	return $month;
}


// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//          NEW FUNCTION(S) [BEGIN]
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    # function fetch month-name...
    function get_month_name($month=null, $yr=null)
    {
        $UNX_TSTAMP = mktime(0, 0, 0, (int)$month, 1, (int)$yr);
        $MONTH_NAME = date("F", $UNX_TSTAMP);
    
        return $MONTH_NAME;
    }

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//          NEW FUNCTION(S) [END]
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

function date_difference_due_time ($date1timestamp, $date2timestamp) {
	$all = round(($date1timestamp - $date2timestamp) / 60);
	$d = floor ($all / 1440);
	$h = floor (($all - $d * 1440) / 60);
	$m = $all - ($d * 1440) - ($h * 60);
	
	if($d < 0){
		
		 $str = $h." hours ".$m." minutes left.";

	}
	else{
    	$str = $d." days ".$h." hours ".$m." minutes left.";
	}
//Since you need just hours and mins
return $str; //array('hours'=>$h, 'mins'=>$m,'day'=>$d);
}



function get_time_elapsed_blog($datetime) {
//     $a = 'a moments ago';
//     $b = '12 seconds ago';
//     $c = '34 minutes ago';
//     $d = 'about an hour ago';
//     $e = '2 hours ago';
//     $f = '12 hours ago';
//     $a = 'Yesterday at 11:44pm';
//     $a = 'Saturday at 5:58pm';
//     $a = 'May 20 at 8:46pm';
//     $a = 'May 16 at 12:37pm';

    $current_language = 'en';//get_current_language();
# echo ' @@'.get_db_datetime();
    $current_timestamp = strtotime(get_db_datetime());
    $another_timestamp = strtotime($datetime);

    $time_diff = $current_timestamp - $another_timestamp;

    $str = '';

    if( $current_language == 'en') {
        $dateTime = new I18N_DateTime( 'en_GB' );

        if( $time_diff == 0 || $time_diff == 1 ) {
            $str = 'a moment ago';
        }
        else if( $time_diff <= 59 ) {
             $str = $time_diff.' seconds ago';
        }
        else if( (int) ($time_diff/60) <= 59 ) {
            $minute_diff = (int) ($time_diff/60);
            if( $minute_diff == 1 ) {
                $str = $minute_diff.' minute ago';
            }
            else {
                $str = $minute_diff.' minutes ago';
            }
        }
        else if( (int) ($time_diff/3600) < 24 ) {
            $hour_diff = (int) ($time_diff/3600);
            if( $hour_diff == 1 ) {
                $str = $hour_diff.' hour ago';
            }
            else {
                $str = $hour_diff.' hours ago';
            }
        }
		else {
			$str = 'on '.getShortDateWithTime($datetime,6);
		}
        /*else if( (int) ($time_diff/3600) < 48 ) {
            $myFormat = $dateTime->setFormat('\a\t g:i a');
            $str = 'Yesterday '.$dateTime->format($another_timestamp);
        }
        else if( (int) ($time_diff/3600) < 96 ) {
            $myFormat = $dateTime->setFormat('l \a\t g:i a');
            $str = $dateTime->format($another_timestamp);
        }
        else {
            $myFormat = $dateTime->setFormat('F d \a\t g:i A');
            $str = $dateTime->format($another_timestamp);
        }*/
    }
    else if( $current_language == 'fr') {
        $dateTime = new I18N_DateTime( 'fr_FR' );

        if( $time_diff == 0 || $time_diff == 1 ) {
            $str = 'il ya un instant';
        }
        else if( $time_diff <= 59 ) {
            $str = 'il ya '.$time_diff.' secondes';
        }
        else if( (int) ($time_diff/60) <= 59 ) {
            $minute_diff = (int) ($time_diff/60);
            if( $minute_diff == 1 ) {
                $str = 'il ya '.$minute_diff.' minute';
            }
            else {
                $str = 'il ya '. $minute_diff.' minutes';
            }
        }
        else if( (int) ($time_diff/3600) < 24 ) {
            $hour_diff = (int) ($time_diff/3600);
            if( $hour_diff == 1 ) {
                $str = 'il ya '.$hour_diff.' heure';
            }
            else {
                $str = 'il ya '.$hour_diff.' heures';
            }
        }
		else {
			getShortDateWithTime($datetime);
		}
        /*else if( (int) ($time_diff/3600) < 48 ) {
            $myFormat = $dateTime->setFormat('à g\hi a');
            $str = 'Hier '.$dateTime->format($another_timestamp);
        }
        else if( (int) ($time_diff/3600) < 96 ) {
            $myFormat = $dateTime->setFormat('l à g\hi a');
            $str = $dateTime->format($another_timestamp);
        }
        else {
            $myFormat = $dateTime->setFormat('F d à g\hi A');
            $str = $dateTime->format($another_timestamp);
        }*/
    }

    return $str;
}


function get_date_diff($to,$from)
{
	$ci = get_instance();
	$sql	= "SELECT DATEDIFF('".$from."','".$to."') AS difference";
	$rst	= $ci->db->query($sql)->result_array();
	return $rst;
}


/*function GetDays($sStartDate, $sEndDate){  
      // Firstly, format the provided dates.  
      // This function works best with YYYY-MM-DD  
      // but other date formats will work thanks  
      // to strtotime().  
     echo  'start'. $sStartDate = gmdate("Y-m-d", strtotime($sStartDate));  
      echo 'end '.$sEndDate = gmdate("Y-m-d", strtotime($sEndDate));  //exit;
      
      // Start the variable off with the start date  
      $aDays[] = $sStartDate;  
      echo date("Y-m-d", strtotime("+1 day", strtotime('2014-05-29')));  exit;
      // Set a 'temp' variable, sCurrentDate, with  
      // the start date - before beginning the loop  
      $sCurrentDate = $sStartDate;  
      
      // While the current date is less than the end date  
      while($sCurrentDate < $sEndDate){  
        // Add a day to the current date  
       echo 'inside loop --- '.$sCurrentDate = gmdate("Y-m-d", strtotime("+1 day", strtotime($sCurrentDate)));  
      
        // Add this new day to the aDays array  
        $aDays[] = $sCurrentDate;  
      }  
      
      // Once the loop has finished, return the  
      // array of days.  
      return $aDays;  
}  
*/

function GetDays($sStartDate, $sEndDate){  
      // Firstly, format the provided dates.  
      // This function works best with YYYY-MM-DD  
      // but other date formats will work thanks  
      // to strtotime().  
      $sStartDate = date("Y-m-d", strtotime($sStartDate));  
      $sEndDate = date("Y-m-d", strtotime($sEndDate));  //exit;
      
      // Start the variable off with the start date  
      $aDays[] = $sStartDate;  
      //echo date("Y-m-d", strtotime("+1 day", strtotime('2014-05-29')));  exit;
      // Set a 'temp' variable, sCurrentDate, with  
      // the start date - before beginning the loop  
      $sCurrentDate = $sStartDate;  
      
      // While the current date is less than the end date  
      while($sCurrentDate < $sEndDate){  
        // Add a day to the current date  
        $sCurrentDate = date("Y-m-d", strtotime("+1 day", strtotime($sCurrentDate)));  
      
        // Add this new day to the aDays array  
        $aDays[] = $sCurrentDate;  
      }  
      //pr($aDays,1);
      // Once the loop has finished, return the  
      // array of days.  
      return $aDays;  
}  


function getUserDateTime($server_time, $USER_TIME_ZONE, $type=''){
	
	$offset = delta_offset('Europe/London',$USER_TIME_ZONE); 
	$delta_time =  1 * ($offset); // -3600
	
	if($delta_time < 0)
		$trigger_time = strtotime($server_time) - $delta_time; 
	else
		$trigger_time = strtotime($server_time) - $delta_time; 
	
	
	if($type == 'dateonly')
		return date('Y-m-d',$trigger_time);//gmdate('Y-m-d',strtotime($USER_TIME_ZONE,$trigger_time)); //;
	else if($type == 'timeonly')
		return date('H:i:s',$trigger_time);
	else
		return date('Y-m-d H:i:s',$trigger_time);#gmdate('Y-m-d H:i:s',strtotime($USER_TIME_ZONE,$trigger_time));//;
}


function getServerDateTime($server_time, $USER_TIME_ZONE, $type=''){
	
	$offset = delta_offset('Europe/London',$USER_TIME_ZONE); 
	$delta_time =  1 * ($offset); // -3600 
	$trigger_time = strtotime($server_time) + $delta_time; 

	
	if($type == 'dateonly')
		return date('Y-m-d',$trigger_time);//gmdate('Y-m-d',strtotime($USER_TIME_ZONE,$trigger_time)); //;
	else if($type == 'timeonly')
		return date('H:i:s',$trigger_time);
	else
		return date('Y-m-d H:i:s',$trigger_time);#gmdate('Y-m-d H:i:s',strtotime($USER_TIME_ZONE,$trigger_time));//;
}

function delta_offset($server_timezone, $user_timezone) {
    $dt = new DateTime('now', new DateTimeZone($server_timezone));
    $offset_server = $dt->getOffset();
    $dt->setTimezone(new DateTimeZone($user_timezone));
    $offset_user = $dt->getOffset();
	
	$offset_ = $offset_user - $offset_server;
    return $offset_;
}



#### to display events as per user local time per time zone

function getOriginalTime_timezone($sel_date, $user_timezone){
		
	    $date = new DateTime($sel_date);   
		//pr($date);
		date_default_timezone_set($user_timezone);
		return date("d-M-Y  H:i", $date->format('U')); 
}


function getEventUserDateTime($server_time,$SERVER_TIME_ZONE, $USER_TIME_ZONE, $type=''){
	
	/*$offset = delta_offset($SERVER_TIME_ZONE,$USER_TIME_ZONE); 
	$delta_time =  1 * ($offset); // -3600
	
	if($delta_time < 0)
		$trigger_time = strtotime($server_time) - $delta_time; 
	else
		$trigger_time = strtotime($server_time) - $delta_time; */
	 date_default_timezone_set('Europe/London');
	 $date = new DateTime($server_time);   
	 date_default_timezone_set($USER_TIME_ZONE);
	//echo date("Y-m-d  H:i:s", $date->format('U'));
	
	if($type == 'dateonly')
		return date('Y-m-d',$date->format('U'));//gmdate('Y-m-d',strtotime($USER_TIME_ZONE,$trigger_time)); //;
	else if($type == 'timeonly')
		return date('H:i:s',$date->format('U'));
	else
		return date('Y-m-d H:i:s',$date->format('U'));#gmdate('Y-m-d H:i:s',strtotime($USER_TIME_ZONE,$trigger_time));//;
}