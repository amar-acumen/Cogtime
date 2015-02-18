<?php

//set_include_path(BASEPATH.'application/libraries' . PATH_SEPARATOR . get_include_path());
//include('I18N/DateTime.php');

function getShortDate($date, $format=1) {

	$dateTime = new I18N_DateTime( 'fr_FR' );


	if($format==1) {
		return date('d/m/y', strtotime($date));
	}
	else if($format==2) {
		return date('d-m-Y', strtotime($date));
	}
	else if($format==3) {
		return date('d/m/Y', strtotime($date));
	}
	else if($format==4) {
		$myFormat = $dateTime->setFormat('d F Y');

		return $dateTime->format(strtotime($date));
	}
	else if($format==5) {
		$myFormat = $dateTime->setFormat('d M Y');

		return $dateTime->format(strtotime($date));
	}
	else if($format==6) {
		$myFormat = $dateTime->setFormat('jS F');
		
		$formatted_date = $dateTime->format(strtotime($date));

		return $formatted_date;
	}
	else if($format==7) {
		$myFormat = $dateTime->setFormat('j M Y');

		$formatted_date = $dateTime->format(strtotime($date));

		return $formatted_date;
	}
}

function getShortDateWithTime($date, $format=1) {
	$dateTime = new I18N_DateTime( 'fr_FR' );

	//$myFormat = $dateTime->setFormat('l d F Y');
	//return utf8_encode(ucfirst($dateTime->format()));

	if($format==1) {
		$myFormat = $dateTime->setFormat('d/m/y à H\hi');
	}
	else if($format==2) {
		$myFormat = $dateTime->setFormat('l d F Y à H\hi');
	}

	#return $dateTime->format();
    return $dateTime->format(strtotime($date));
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

	return $age;
}


# ====================================================================
#               New Functions [Start]
# ====================================================================

    //// function to get date in specified format...
    function getDesiredDate($date, $format)
    {
        $dateTime = new I18N_DateTime( 'fr_FR' );
        $customFormat = $dateTime->setFormat($format);

        $formatted_dt = $dateTime->format(strtotime($date));

        return $formatted_dt;
    }


    # month-name in desired language format...
    function getMonthName($monthIndex, $current_language='fr')
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





# ====================================================================
#               New Functions [End]
# ====================================================================