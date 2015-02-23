<?php

/**
 * For COUNTRY  selectbox option making
 * 
 * @param string 
 * @param 
 * @param mix $configArr
 * @return string
 */
function makeOptionCountry($mix_where = '', $s_id = '') {
    try {
        $CI = & get_instance();

        $cond = (trim($mix_where)) ? "WHERE " . $mix_where : '';
        $res = $CI->db->query("select id,s_country_name as s_country_name FROM {$CI->db->MST_COUNTRY} {$cond} order by s_country_name asc ");
        $mix_value = $res->result_array();

        $s_option = '';
        if ($mix_value) {
            $s_select = ''; //defined here for unsetting this var 
            foreach ($mix_value as $val) {
                $s_select = '';
                if (encrypt($val["id"]) == $s_id)
                    $s_select = " selected ";
                $s_option .= "<option $s_select value='" . encrypt($val["id"]) . "'>" . $val["s_country_name"] . "</option>";
            }
            unset($s_select, $val);
        }

        unset($cond, $res, $mix_value, $mix_where, $s_id);
        return $s_option;
    } catch (Exception $err_obj) {
        show_error($err_obj->getMessage());
    }
}

function makeOptionDay($s_id = '') {
    $s_select = ''; //defined here for unsetting this var 
    for ($i = 1; $i <= 31; $i++) {
        $s_select = '';
        if ($i < 10)
            $i = "0" . $i;
        if ($i == $s_id)
            $s_select = " selected ";
        $s_option .= "<option $s_select value='" . $i . "'>" . $i . "</option>";
    }
    unset($s_select, $val);


    unset($cond, $res, $mix_value, $mix_where, $s_id);
    return $s_option;
}

function makeOptionYear($s_id = '') {
    $s_select = ''; //defined here for unsetting this var 
    for ($i = date('Y'); $i >= 1950; $i--) {
        $s_select = '';

        if ($i == $s_id)
            $s_select = " selected ";
        $s_option .= "<option $s_select value='" . $i . "'>" . $i . "</option>";
    }
    unset($s_select, $val);


    unset($cond, $res, $mix_value, $mix_where, $s_id);
    return $s_option;
}

# To set year in statistics part backend---

function makeOptionDobYear($s_id = '') {
    $s_select = ''; //defined here for unsetting this var 
    for ($i = 1950; $i <= date('Y'); $i++) {
        $s_select = '';

        if ($i == $s_id)
            $s_select = " selected ";
        $s_option .= "<option $s_select value='" . $i . "'>" . $i . "</option>";
    }
    unset($s_select, $val);


    unset($cond, $res, $mix_value, $mix_where, $s_id);
    return $s_option;
}

function makeOptionMonth($s_id = '', $s_lang = 'en') {
    $month_arr = array('1' => array('en' => "January", "fr" => "Janvier"),
        '2' => array('en' => "February", "fr" => "F&eacute;vrier"),
        '3' => array('en' => "March", "fr" => "Mars"),
        '4' => array('en' => "April", "fr" => "Avril"),
        '5' => array('en' => "May", "fr" => "Mai"),
        '6' => array('en' => "June", "fr" => "Juin"),
        '7' => array('en' => "July", "fr" => "Juillet"),
        '8' => array('en' => "August", "fr" => "Ao&ucirc;t"),
        '9' => array('en' => "September", "fr" => "Septembre"),
        '10' => array('en' => "October", "fr" => "Octobre"),
        '11' => array('en' => "November", "fr" => "Novembre"),
        '12' => array('en' => "December", "fr" => "D&eacute;cembre"));


    $s_select = ''; //defined here for unsetting this var 
    foreach ($month_arr as $k => $val) {
        $s_select = '';

        if ($k == $s_id)
            $s_select = " selected ";
        $s_option .= "<option $s_select value='" . $k . "'>" . $val[$s_lang] . "</option>";
    }
    unset($s_select, $val);


    unset($cond, $res, $mix_value, $mix_where, $s_id);
    return $s_option;
}

function makeOptionNumber($start = 0, $end = 12, $i_gap = 1, $s_id = '') {
    $s_select = ''; //defined here for unsetting this var 
    for ($i = $start; $i <= $end; $i+=$i_gap) {
        $s_select = '';
        if ($i < 10)
            $i = "0" . $i;
        if ($i == $s_id)
            $s_select = ' selected="selected" ';
        $s_option .= "<option  $s_select value='" . $i . "'>" . $i . "</option>";
    }
    unset($s_select, $val);


    unset($cond, $res, $mix_value, $mix_where, $s_id);
    return $s_option;
}

function makeOptionDenomination($s_id = '', $mix_where = '') {
    try {
        $CI = & get_instance();
        $currentLanguage = get_current_language();


        $cond = (trim($mix_where)) ? " AND " . $mix_where : '';
        $res = $CI->db->query("SELECT id,s_name FROM {$CI->db->DENOMINATION} WHERE i_status = 1  {$cond}");
        $mix_value = $res->result_array();
        # pr($mix_value);
        $s_option = '';
        if ($mix_value) {
            $s_select = ''; //defined here for unsetting this var 
            foreach ($mix_value as $val) {
                $s_select = '';
                if (encrypt($val["id"]) == $s_id)
                    $s_select = " selected ";
                $s_option .= "<option {$s_select} value='" . encrypt($val["id"]) . "'>" . $val["s_name"] . "</option>";
            }
            unset($s_select, $val);
        }

        unset($cond, $res, $mix_value, $mix_where, $s_id);
        return $s_option;
    } catch (Exception $err_obj) {
        show_error($err_obj->getMessage());
    }
}

function makeOptionTimezones($s_id = '',$s_text='') {
    $timezones = array(
                '(GMT-12:00) International Date Line West' => 'Pacific/Wake',
                '(GMT-11:00) Midway Island' => 'Pacific/Apia',
                '(GMT-11:00) Samoa' => 'Pacific/Apia',
                '(GMT-10:00) Hawaii' => 'Pacific/Honolulu',
                '(GMT-09:00) Alaska' => 'America/Anchorage',
                '(GMT-08:00) Pacific Time (US &amp; Canada); Tijuana' => 'America/Los_Angeles',
                '(GMT-07:00) Arizona' => 'America/Phoenix',
                '(GMT-07:00) Chihuahua' => 'America/Chihuahua',
                '(GMT-07:00) La Paz' => 'America/Chihuahua',
                '(GMT-07:00) Mazatlan' => 'America/Chihuahua',
                '(GMT-07:00) Mountain Time (US &amp; Canada)' => 'America/Denver',
                '(GMT-06:00) Central America' => 'America/Managua',
                '(GMT-06:00) Central Time (US &amp; Canada)' => 'America/Chicago',
                '(GMT-06:00) Guadalajara' => 'America/Mexico_City',
                '(GMT-06:00) Mexico City' => 'America/Mexico_City',
                '(GMT-06:00) Monterrey' => 'America/Mexico_City',
                '(GMT-06:00) Saskatchewan' => 'America/Regina',
                '(GMT-05:00) Bogota' => 'America/Bogota',
                '(GMT-05:00) Eastern Time (US &amp; Canada)' => 'America/New_York',
                '(GMT-05:00) Indiana (East)' => 'America/Indiana/Indianapolis',
                '(GMT-05:00) Lima' => 'America/Bogota',
                '(GMT-05:00) Quito' => 'America/Bogota',
                '(GMT-04:00) Atlantic Time (Canada)' => 'America/Halifax',
                '(GMT-04:00) Caracas' => 'America/Caracas',
                '(GMT-04:00) La Paz' => 'America/Caracas',
                '(GMT-04:00) Santiago' => 'America/Santiago',
                '(GMT-03:30) Newfoundland' => 'America/St_Johns',
                '(GMT-03:00) Brasilia' => 'America/Sao_Paulo',
                '(GMT-03:00) Buenos Aires' => 'America/Argentina/Buenos_Aires',
                '(GMT-03:00) Georgetown' => 'America/Argentina/Buenos_Aires',
                '(GMT-03:00) Greenland' => 'America/Godthab',
                '(GMT-02:00) Mid-Atlantic' => 'America/Noronha',
                '(GMT-01:00) Azores' => 'Atlantic/Azores',
                '(GMT-01:00) Cape Verde Is.' => 'Atlantic/Cape_Verde',
                '(GMT) Casablanca' => 'Africa/Casablanca',
                '(GMT) Edinburgh' => 'Europe/London',
                '(GMT) Greenwich Mean Time : Dublin' => 'Europe/London',
                '(GMT) Lisbon' => 'Europe/London',
                '(GMT) London' => 'Europe/London',
                '(GMT) Monrovia' => 'Africa/Casablanca',
                '(GMT+01:00) Amsterdam' => 'Europe/Berlin',
                '(GMT+01:00) Belgrade' => 'Europe/Belgrade',
                '(GMT+01:00) Berlin' => 'Europe/Berlin',
                '(GMT+01:00) Bern' => 'Europe/Berlin',
                '(GMT+01:00) Bratislava' => 'Europe/Belgrade',
                '(GMT+01:00) Brussels' => 'Europe/Paris',
                '(GMT+01:00) Budapest' => 'Europe/Belgrade',
                '(GMT+01:00) Copenhagen' => 'Europe/Paris',
                '(GMT+01:00) Ljubljana' => 'Europe/Belgrade',
                '(GMT+01:00) Madrid' => 'Europe/Paris',
                '(GMT+01:00) Paris' => 'Europe/Paris',
                '(GMT+01:00) Prague' => 'Europe/Belgrade',
                '(GMT+01:00) Rome' => 'Europe/Berlin',
                '(GMT+01:00) Sarajevo' => 'Europe/Sarajevo',
                '(GMT+01:00) Skopje' => 'Europe/Sarajevo',
                '(GMT+01:00) Stockholm' => 'Europe/Berlin',
                '(GMT+01:00) Vienna' => 'Europe/Berlin',
                '(GMT+01:00) Warsaw' => 'Europe/Sarajevo',
                '(GMT+01:00) West Central Africa' => 'Africa/Lagos',
                '(GMT+01:00) Zagreb' => 'Europe/Sarajevo',
                '(GMT+02:00) Athens' => 'Europe/Istanbul',
                '(GMT+02:00) Bucharest' => 'Europe/Bucharest',
                '(GMT+02:00) Cairo' => 'Africa/Cairo',
                '(GMT+02:00) Harare' => 'Africa/Johannesburg',
                '(GMT+02:00) Helsinki' => 'Europe/Helsinki',
                '(GMT+02:00) Istanbul' => 'Europe/Istanbul',
                '(GMT+02:00) Jerusalem' => 'Asia/Jerusalem',
                '(GMT+02:00) Kyiv' => 'Europe/Helsinki',
                '(GMT+02:00) Minsk' => 'Europe/Istanbul',
                '(GMT+02:00) Pretoria' => 'Africa/Johannesburg',
                '(GMT+02:00) Riga' => 'Europe/Helsinki',
                '(GMT+02:00) Sofia' => 'Europe/Helsinki',
                '(GMT+02:00) Tallinn' => 'Europe/Helsinki',
                '(GMT+02:00) Vilnius' => 'Europe/Helsinki',
                '(GMT+03:00) Baghdad' => 'Asia/Baghdad',
                '(GMT+03:00) Kuwait' => 'Asia/Riyadh',
                '(GMT+03:00) Moscow' => 'Europe/Moscow',
                '(GMT+03:00) Nairobi' => 'Africa/Nairobi',
                '(GMT+03:00) Riyadh' => 'Asia/Riyadh',
                '(GMT+03:00) St. Petersburg' => 'Europe/Moscow',
                '(GMT+03:00) Volgograd' => 'Europe/Moscow',
                '(GMT+03:30) Tehran' => 'Asia/Tehran',
                '(GMT+04:00) Abu Dhabi' => 'Asia/Muscat',
                '(GMT+04:00) Baku' => 'Asia/Tbilisi',
                '(GMT+04:00) Muscat' => 'Asia/Muscat',
                '(GMT+04:00) Tbilisi' => 'Asia/Tbilisi',
                '(GMT+04:00) Yerevan' => 'Asia/Tbilisi',
                '(GMT+04:30) Kabul' => 'Asia/Kabul',
                '(GMT+05:00) Ekaterinburg' => 'Asia/Yekaterinburg',
                '(GMT+05:00) Islamabad' => 'Asia/Karachi',
                '(GMT+05:00) Karachi' => 'Asia/Karachi',
                '(GMT+05:00) Tashkent' => 'Asia/Karachi',
                '(GMT+05:30) Chennai' => 'Asia/Kolkata',
                '(GMT+05:30) Kolkata' => 'Asia/Kolkata',
                '(GMT+05:30) Mumbai' => 'Asia/Kolkata',
                '(GMT+05:30) New Delhi' => 'Asia/Kolkata',
                '(GMT+05:45) Kathmandu' => 'Asia/Katmandu',
                '(GMT+06:00) Almaty' => 'Asia/Novosibirsk',
                '(GMT+06:00) Astana' => 'Asia/Dhaka',
                '(GMT+06:00) Dhaka' => 'Asia/Dhaka',
                '(GMT+06:00) Novosibirsk' => 'Asia/Novosibirsk',
                '(GMT+06:00) Sri Jayawardenepura' => 'Asia/Colombo',
                '(GMT+06:30) Rangoon' => 'Asia/Rangoon',
                '(GMT+07:00) Bangkok' => 'Asia/Bangkok',
                '(GMT+07:00) Hanoi' => 'Asia/Bangkok',
                '(GMT+07:00) Jakarta' => 'Asia/Bangkok',
                '(GMT+07:00) Krasnoyarsk' => 'Asia/Krasnoyarsk',
                '(GMT+08:00) Beijing' => 'Asia/Hong_Kong',
                '(GMT+08:00) Chongqing' => 'Asia/Hong_Kong',
                '(GMT+08:00) Hong Kong' => 'Asia/Hong_Kong',
                '(GMT+08:00) Irkutsk' => 'Asia/Irkutsk',
                '(GMT+08:00) Kuala Lumpur' => 'Asia/Singapore',
                '(GMT+08:00) Perth' => 'Australia/Perth',
                '(GMT+08:00) Singapore' => 'Asia/Singapore',
                '(GMT+08:00) Taipei' => 'Asia/Taipei',
                '(GMT+08:00) Ulaan Bataar' => 'Asia/Irkutsk',
                '(GMT+08:00) Urumqi' => 'Asia/Hong_Kong',
                '(GMT+09:00) Osaka' => 'Asia/Tokyo',
                '(GMT+09:00) Sapporo' => 'Asia/Tokyo',
                '(GMT+09:00) Seoul' => 'Asia/Seoul',
                '(GMT+09:00) Tokyo' => 'Asia/Tokyo',
                '(GMT+09:00) Yakutsk' => 'Asia/Yakutsk',
                '(GMT+09:30) Adelaide' => 'Australia/Adelaide',
                '(GMT+09:30) Darwin' => 'Australia/Darwin',
                '(GMT+10:00) Brisbane' => 'Australia/Brisbane',
                '(GMT+10:00) Canberra' => 'Australia/Sydney',
                '(GMT+10:00) Guam' => 'Pacific/Guam',
                '(GMT+10:00) Hobart' => 'Australia/Hobart',
                '(GMT+10:00) Melbourne' => 'Australia/Sydney',
                '(GMT+10:00) Port Moresby' => 'Pacific/Guam',
                '(GMT+10:00) Sydney' => 'Australia/Sydney',
                '(GMT+10:00) Vladivostok' => 'Asia/Vladivostok',
                '(GMT+11:00) Magadan' => 'Asia/Magadan',
                '(GMT+11:00) New Caledonia' => 'Asia/Magadan',
                '(GMT+11:00) Solomon Is.' => 'Asia/Magadan',
                '(GMT+12:00) Auckland' => 'Pacific/Auckland',
                '(GMT+12:00) Fiji' => 'Pacific/Fiji',
                '(GMT+12:00) Kamchatka' => 'Pacific/Fiji',
                '(GMT+12:00) Marshall Is.' => 'Pacific/Fiji',
                '(GMT+12:00) Wellington' => 'Pacific/Auckland',
                '(GMT+13:00) Nuku\'alofa' => 'Pacific/Tongatapu',
    );

    // asort($timezones);
    $s_select = ''; //defined here for unsetting this var 
    foreach ($timezones as $k => $val) {
        $s_select = '';

        if($s_text == '')
		{
        if ($val == $s_id)
            $s_select = " selected ";
		}
		else{
		if ($val == $s_id && $k== $s_text)
            $s_select = " selected ";
		}
        $s_option .= "<option $s_select value='" . $val . "'>" . $k . "</option>";
    }
    unset($s_select, $val);


    unset($cond, $res, $mix_value, $mix_where, $s_id);
    return $s_option;
}

function get_time_zone_by_value($s_id = '') {
    $timezones = array(
                '(GMT-12:00) International Date Line West' => 'Pacific/Wake',
                '(GMT-11:00) Midway Island' => 'Pacific/Apia',
                '(GMT-11:00) Samoa' => 'Pacific/Apia',
                '(GMT-10:00) Hawaii' => 'Pacific/Honolulu',
                '(GMT-09:00) Alaska' => 'America/Anchorage',
                '(GMT-08:00) Pacific Time (US &amp; Canada); Tijuana' => 'America/Los_Angeles',
                '(GMT-07:00) Arizona' => 'America/Phoenix',
                '(GMT-07:00) Chihuahua' => 'America/Chihuahua',
                '(GMT-07:00) La Paz' => 'America/Chihuahua',
                '(GMT-07:00) Mazatlan' => 'America/Chihuahua',
                '(GMT-07:00) Mountain Time (US &amp; Canada)' => 'America/Denver',
                '(GMT-06:00) Central America' => 'America/Managua',
                '(GMT-06:00) Central Time (US &amp; Canada)' => 'America/Chicago',
                '(GMT-06:00) Guadalajara' => 'America/Mexico_City',
                '(GMT-06:00) Mexico City' => 'America/Mexico_City',
                '(GMT-06:00) Monterrey' => 'America/Mexico_City',
                '(GMT-06:00) Saskatchewan' => 'America/Regina',
                '(GMT-05:00) Bogota' => 'America/Bogota',
                '(GMT-05:00) Eastern Time (US &amp; Canada)' => 'America/New_York',
                '(GMT-05:00) Indiana (East)' => 'America/Indiana/Indianapolis',
                '(GMT-05:00) Lima' => 'America/Bogota',
                '(GMT-05:00) Quito' => 'America/Bogota',
                '(GMT-04:00) Atlantic Time (Canada)' => 'America/Halifax',
                '(GMT-04:00) Caracas' => 'America/Caracas',
                '(GMT-04:00) La Paz' => 'America/Caracas',
                '(GMT-04:00) Santiago' => 'America/Santiago',
                '(GMT-03:30) Newfoundland' => 'America/St_Johns',
                '(GMT-03:00) Brasilia' => 'America/Sao_Paulo',
                '(GMT-03:00) Buenos Aires' => 'America/Argentina/Buenos_Aires',
                '(GMT-03:00) Georgetown' => 'America/Argentina/Buenos_Aires',
                '(GMT-03:00) Greenland' => 'America/Godthab',
                '(GMT-02:00) Mid-Atlantic' => 'America/Noronha',
                '(GMT-01:00) Azores' => 'Atlantic/Azores',
                '(GMT-01:00) Cape Verde Is.' => 'Atlantic/Cape_Verde',
                '(GMT) Casablanca' => 'Africa/Casablanca',
                '(GMT) Edinburgh' => 'Europe/London',
                '(GMT) Greenwich Mean Time : Dublin' => 'Europe/London',
                '(GMT) Lisbon' => 'Europe/London',
                '(GMT) London' => 'Europe/London',
                '(GMT) Monrovia' => 'Africa/Casablanca',
                '(GMT+01:00) Amsterdam' => 'Europe/Berlin',
                '(GMT+01:00) Belgrade' => 'Europe/Belgrade',
                '(GMT+01:00) Berlin' => 'Europe/Berlin',
                '(GMT+01:00) Bern' => 'Europe/Berlin',
                '(GMT+01:00) Bratislava' => 'Europe/Belgrade',
                '(GMT+01:00) Brussels' => 'Europe/Paris',
                '(GMT+01:00) Budapest' => 'Europe/Belgrade',
                '(GMT+01:00) Copenhagen' => 'Europe/Paris',
                '(GMT+01:00) Ljubljana' => 'Europe/Belgrade',
                '(GMT+01:00) Madrid' => 'Europe/Paris',
                '(GMT+01:00) Paris' => 'Europe/Paris',
                '(GMT+01:00) Prague' => 'Europe/Belgrade',
                '(GMT+01:00) Rome' => 'Europe/Berlin',
                '(GMT+01:00) Sarajevo' => 'Europe/Sarajevo',
                '(GMT+01:00) Skopje' => 'Europe/Sarajevo',
                '(GMT+01:00) Stockholm' => 'Europe/Berlin',
                '(GMT+01:00) Vienna' => 'Europe/Berlin',
                '(GMT+01:00) Warsaw' => 'Europe/Sarajevo',
                '(GMT+01:00) West Central Africa' => 'Africa/Lagos',
                '(GMT+01:00) Zagreb' => 'Europe/Sarajevo',
                '(GMT+02:00) Athens' => 'Europe/Istanbul',
                '(GMT+02:00) Bucharest' => 'Europe/Bucharest',
                '(GMT+02:00) Cairo' => 'Africa/Cairo',
                '(GMT+02:00) Harare' => 'Africa/Johannesburg',
                '(GMT+02:00) Helsinki' => 'Europe/Helsinki',
                '(GMT+02:00) Istanbul' => 'Europe/Istanbul',
                '(GMT+02:00) Jerusalem' => 'Asia/Jerusalem',
                '(GMT+02:00) Kyiv' => 'Europe/Helsinki',
                '(GMT+02:00) Minsk' => 'Europe/Istanbul',
                '(GMT+02:00) Pretoria' => 'Africa/Johannesburg',
                '(GMT+02:00) Riga' => 'Europe/Helsinki',
                '(GMT+02:00) Sofia' => 'Europe/Helsinki',
                '(GMT+02:00) Tallinn' => 'Europe/Helsinki',
                '(GMT+02:00) Vilnius' => 'Europe/Helsinki',
                '(GMT+03:00) Baghdad' => 'Asia/Baghdad',
                '(GMT+03:00) Kuwait' => 'Asia/Riyadh',
                '(GMT+03:00) Moscow' => 'Europe/Moscow',
                '(GMT+03:00) Nairobi' => 'Africa/Nairobi',
                '(GMT+03:00) Riyadh' => 'Asia/Riyadh',
                '(GMT+03:00) St. Petersburg' => 'Europe/Moscow',
                '(GMT+03:00) Volgograd' => 'Europe/Moscow',
                '(GMT+03:30) Tehran' => 'Asia/Tehran',
                '(GMT+04:00) Abu Dhabi' => 'Asia/Muscat',
                '(GMT+04:00) Baku' => 'Asia/Tbilisi',
                '(GMT+04:00) Muscat' => 'Asia/Muscat',
                '(GMT+04:00) Tbilisi' => 'Asia/Tbilisi',
                '(GMT+04:00) Yerevan' => 'Asia/Tbilisi',
                '(GMT+04:30) Kabul' => 'Asia/Kabul',
                '(GMT+05:00) Ekaterinburg' => 'Asia/Yekaterinburg',
                '(GMT+05:00) Islamabad' => 'Asia/Karachi',
                '(GMT+05:00) Karachi' => 'Asia/Karachi',
                '(GMT+05:00) Tashkent' => 'Asia/Karachi',
                '(GMT+05:30) Chennai' => 'Asia/Kolkata',
                '(GMT+05:30) Kolkata' => 'Asia/Kolkata',
                '(GMT+05:30) Mumbai' => 'Asia/Kolkata',
                '(GMT+05:30) New Delhi' => 'Asia/Kolkata',
                '(GMT+05:45) Kathmandu' => 'Asia/Katmandu',
                '(GMT+06:00) Almaty' => 'Asia/Novosibirsk',
                '(GMT+06:00) Astana' => 'Asia/Dhaka',
                '(GMT+06:00) Dhaka' => 'Asia/Dhaka',
                '(GMT+06:00) Novosibirsk' => 'Asia/Novosibirsk',
                '(GMT+06:00) Sri Jayawardenepura' => 'Asia/Colombo',
                '(GMT+06:30) Rangoon' => 'Asia/Rangoon',
                '(GMT+07:00) Bangkok' => 'Asia/Bangkok',
                '(GMT+07:00) Hanoi' => 'Asia/Bangkok',
                '(GMT+07:00) Jakarta' => 'Asia/Bangkok',
                '(GMT+07:00) Krasnoyarsk' => 'Asia/Krasnoyarsk',
                '(GMT+08:00) Beijing' => 'Asia/Hong_Kong',
                '(GMT+08:00) Chongqing' => 'Asia/Hong_Kong',
                '(GMT+08:00) Hong Kong' => 'Asia/Hong_Kong',
                '(GMT+08:00) Irkutsk' => 'Asia/Irkutsk',
                '(GMT+08:00) Kuala Lumpur' => 'Asia/Singapore',
                '(GMT+08:00) Perth' => 'Australia/Perth',
                '(GMT+08:00) Singapore' => 'Asia/Singapore',
                '(GMT+08:00) Taipei' => 'Asia/Taipei',
                '(GMT+08:00) Ulaan Bataar' => 'Asia/Irkutsk',
                '(GMT+08:00) Urumqi' => 'Asia/Hong_Kong',
                '(GMT+09:00) Osaka' => 'Asia/Tokyo',
                '(GMT+09:00) Sapporo' => 'Asia/Tokyo',
                '(GMT+09:00) Seoul' => 'Asia/Seoul',
                '(GMT+09:00) Tokyo' => 'Asia/Tokyo',
                '(GMT+09:00) Yakutsk' => 'Asia/Yakutsk',
                '(GMT+09:30) Adelaide' => 'Australia/Adelaide',
                '(GMT+09:30) Darwin' => 'Australia/Darwin',
                '(GMT+10:00) Brisbane' => 'Australia/Brisbane',
                '(GMT+10:00) Canberra' => 'Australia/Sydney',
                '(GMT+10:00) Guam' => 'Pacific/Guam',
                '(GMT+10:00) Hobart' => 'Australia/Hobart',
                '(GMT+10:00) Melbourne' => 'Australia/Sydney',
                '(GMT+10:00) Port Moresby' => 'Pacific/Guam',
                '(GMT+10:00) Sydney' => 'Australia/Sydney',
                '(GMT+10:00) Vladivostok' => 'Asia/Vladivostok',
                '(GMT+11:00) Magadan' => 'Asia/Magadan',
                '(GMT+11:00) New Caledonia' => 'Asia/Magadan',
                '(GMT+11:00) Solomon Is.' => 'Asia/Magadan',
                '(GMT+12:00) Auckland' => 'Pacific/Auckland',
                '(GMT+12:00) Fiji' => 'Pacific/Fiji',
                '(GMT+12:00) Kamchatka' => 'Pacific/Fiji',
                '(GMT+12:00) Marshall Is.' => 'Pacific/Fiji',
                '(GMT+12:00) Wellington' => 'Pacific/Auckland',
                '(GMT+13:00) Nuku\'alofa' => 'Pacific/Tongatapu',
    );
    foreach ($timezones as $k => $val) {
        
        if ($val == $s_id){
            $t_zone = $k;
        }
           
    }
    return $t_zone;
}

function makeOption_Age_range($start = 21, $end = 50, $i_gap = 5, $s_id = '') {
    $s_select = ''; //defined here for unsetting this var 
    $s_option .= "<option $s_select value='20'>Upto 20 years</option>";
    for ($i = $start; $i <= $end; $i+=$i_gap) {
        $up = $i + $i_gap - 1;
        $lw = $i;
        $s_select = '';
        if ($i < 10)
            $i = "0" . $i;
        if ($i == $s_id)
            $s_select = " selected ";
        if ($up == '50' && $lw == '46') {
            $s_option .= "<option $s_select value='" . $i . "'>" . $lw . " - " . $up . " years</option>";
            $s_option .= "<option $s_select value='" . $up . "'>Above " . $up . " years</option>";
        } else {
            $s_option .= "<option $s_select value='" . $i . "'>" . $lw . " - " . $up . " years</option>";
        }
    }
    unset($s_select, $val);


    unset($cond, $res, $mix_value, $mix_where, $s_id);
    return $s_option;
}

function makeOption_time($start_time = '00:00', $i_gap = 15, $s_time) {

    $s_select = '';
    $day = date('Y/m/d');
    ;

    $startTime = date(strtotime($day . '00:00'));
    $endTime = date(strtotime($day . "23:45"));

    $timeDiff = round(($endTime - $startTime) / 60 / 60);

    $startHour = date("G", $startTime);
    $endHour = $startHour + $timeDiff;



    for ($i = $startHour; $i < $endHour; $i++) {
        for ($j = 0; $j <= 45; $j+=$i_gap) {


            $time = str_pad($i, 2, '0', STR_PAD_LEFT) . ":" . str_pad($j, 2, '0', STR_PAD_LEFT);
            $time_val = str_pad($i, 2, '0', STR_PAD_LEFT) . ":" . str_pad($j, 2, '0', STR_PAD_LEFT) . ":00";
            if ($time_val == $s_time) {
                $s_select = "selected";
                $s_option .= "<option $s_select value='" . $time_val . "'>" . $time . "</option>";
            } else {
                $s_option .= "<option value='" . $time_val . "'>" . $time . "</option>";
            }
        }
    }
    $s_option .= "<option value='24:00:00'>" . "24:00" . "</option>";
    return $s_option;
}

function makeOption_Endtime($start_time = '00:00', $i_gap = 15, $s_time) {
    $s_select = '';
    $day = date('Y/m/d');
    ;

    $startTime = date(strtotime($day . $start_time));

    $start_min = date('i', strtotime($day . $start_time));
    $endTime = date(strtotime($day . "23:45"));

    #echo ' end time: h:i  '.date('H:i',strtotime($day."23:45"));

    $timeDiff = round(($endTime - $startTime) / 60 / 60);

    $startHour = date("G", $startTime);
    $endHour = $startHour + $timeDiff;

#echo ' : strt hour: h:i  '.date('H:i',strtotime($startHour));
    $counter = 0;
    for ($i = $startHour; $i < $endHour; $i++) {
        $counter ++;
        if ($counter == 1) {
            $a = $start_min;
        } else {
            $a = 0;
        }
        for ($j = $a; $j <= 45; $j+=15) {


            $time = str_pad($i, 2, '0', STR_PAD_LEFT) . ":" . str_pad($j, 2, '0', STR_PAD_LEFT);
            $time_val = str_pad($i, 2, '0', STR_PAD_LEFT) . ":" . str_pad($j, 2, '0', STR_PAD_LEFT) . ":00";
            if ($time == $s_time) {
                $s_select = "selected";
                $s_option .= "<option $s_select value='" . $time_val . "'>" . $time . "</option>";
            } else {
                $s_option .= "<option value='" . $time_val . "'>" . $time . "</option>";
            }
        }
    }
    $s_option .= "<option value='24:00:00'>" . "24:00" . "</option>";
    return $s_option;
}

function makeOptionRingCategory($mix_where = '', $s_id = '') {
    try {
        $CI = & get_instance();

        $cond = (trim($mix_where)) ? " AND  " . $mix_where : '';
        $res = $CI->db->query("SELECT * FROM {$CI->db->RING_CAT} WHERE i_parent_category = 0 {$cond}");
        $mix_value = $res->result_array();

        $s_option = '';
        if ($mix_value) {
            $s_select = ''; //defined here for unsetting this var 
            foreach ($mix_value as $val) {
                $s_select = '';
                if (encrypt($val["id"]) == $s_id)
                    $s_select = " selected ";
                $s_option .= "<option $s_select value='" . encrypt($val["id"]) . "'>" . $val["s_category_name"] . "</option>";
            }
            unset($s_select, $val);
        }

        unset($cond, $res, $mix_value, $mix_where, $s_id);
        return $s_option;
    } catch (Exception $err_obj) {
        show_error($err_obj->getMessage());
    }
}

##  list <ul><li></li><ul> of year ###

function makeListYear($s_id = '') {
    $s_select = ''; //defined here for unsetting this var 
    for ($i = date('Y'); $i >= 2011; $i--) {
        $s_select = '';

        if ($i == $s_id)
            $s_select = " curr_selected ";
        $s_option .= "<li $s_select value='" . $i . "'>" . $i . "</li>";
    }
    unset($s_select, $val);


    unset($cond, $res, $mix_value, $mix_where, $s_id);
    return $s_option;
}

function makeListMonth($start_month, $s_lang = 'en') {
    // echo date('');
    $month_arr = array(1 => array('en' => "January"),
        2 => array('en' => "February"),
        3 => array('en' => "March"),
        4 => array('en' => "April"),
        5 => array('en' => "May"),
        6 => array('en' => "June"),
        7 => array('en' => "July"),
        8 => array('en' => "August"),
        9 => array('en' => "September"),
        10 => array('en' => "October"),
        11 => array('en' => "November"),
        12 => array('en' => "December"));


    $s_select = ''; //defined here for unsetting this var 

    $rest_month = 12 - $start_month;

    for ($i = intval($start_month); $i <= 12; $i++) {
        $s_option .= "<li value='" . $i . "'>" . $month_arr[$i]['en'] . "</li>";
    }

    if ($rest_month != 0) {

        for ($j = 1; $j < $start_month; $j++) {
            $s_option .= "<li value='" . $j . "'>" . $month_arr[$j]['en'] . "</li>";
        }
    }
    unset($s_select, $val);

    //pr($s_option,1);
    unset($cond, $res, $mix_value, $mix_where, $s_id);
    return $s_option;
}

function GetMonthName_by_id($month_id) {
    // echo date('');
    $month_arr = array(1 => "January",
        2 => "February",
        3 => "March",
        4 => "April",
        5 => "May",
        6 => "June",
        7 => "July",
        8 => "August",
        9 => "September",
        10 => "October",
        11 => "November",
        12 => "December");
    return $month_arr[$month_id];
}

#Etrade Category

function makeOptionEtradeTopCategory($mix_where = '', $s_id = '') {
    try {
        $CI = & get_instance();

        $cond = (trim($mix_where)) ? "WHERE " . $mix_where : '';
        $res = $CI->db->query("SELECT * FROM {$CI->db->TRADE_CAT} WHERE i_parent_category = 0 ORDER BY s_category_name ASC");
        $mix_value = $res->result_array();

        $s_option = '';
        if ($mix_value) {
            $s_select = ''; //defined here for unsetting this var 
            foreach ($mix_value as $val) {
                $s_select = '';
                if (encrypt($val["id"]) == $s_id)
                    $s_select = " selected ";
                $s_option .= "<option $s_select value='" . encrypt($val["id"]) . "'>" . $val["s_category_name"] . "</option>";
            }
            unset($s_select, $val);
        }

        unset($cond, $res, $mix_value, $mix_where, $s_id);
        return $s_option;
    } catch (Exception $err_obj) {
        show_error($err_obj->getMessage());
    }
}

function makeOptionEtradeCategory($mix_where = '', $s_id = '') {
    try {
        $CI = & get_instance();

        $cond = (trim($mix_where)) ? "WHERE " . $mix_where : '';
        $res = $CI->db->query("SELECT *,subcat.i_parent_category AS subcat_i_parent_category, 
								(SELECT s_category_name FROM {$CI->db->TRADE_CAT} AS pcat WHERE pcat.id=subcat_i_parent_category) AS pcat_name
								FROM {$CI->db->TRADE_CAT} AS subcat WHERE subcat.i_parent_category > 0 ORDER BY pcat_name ASC");
        $mix_value = $res->result_array();

        $s_option = '';
        if ($mix_value) {
            $s_select = ''; //defined here for unsetting this var 
            foreach ($mix_value as $val) {
                $s_select = '';
                if (encrypt($val["id"]) == $s_id)
                    $s_select = " selected ";
                if ($val["pcat_name"] == '')
                    $s_option .= "<option $s_select value='" . encrypt($val["id"]) . "'>" . $val["s_category_name"] . "</option>";
                else
                    $s_option .= "<option $s_select value='" . encrypt($val["id"]) . "'>" . $val["pcat_name"] . " > " . $val["s_category_name"] . "</option>";
            }
            unset($s_select, $val);
        }

        unset($cond, $res, $mix_value, $mix_where, $s_id);
        return $s_option;
    } catch (Exception $err_obj) {
        show_error($err_obj->getMessage());
    }
}

function makeOptionBibleCategory($mix_where = '', $s_id = '') {
    try {
        $CI = & get_instance();
        $i_profile_id = intval(decrypt($CI->session->userdata('user_id')));
        $cond = (trim($mix_where)) ? "WHERE i_user_id='" . $i_profile_id . "'" . $mix_where : "WHERE i_user_id='" . $i_profile_id . "'";
        $res = $CI->db->query("SELECT * FROM {$CI->db->BIBLE_CAT} {$cond}");
        $mix_value = $res->result_array();

        $s_option = '';
        if ($mix_value) {
            $s_select = ''; //defined here for unsetting this var 
            foreach ($mix_value as $val) {
                $s_select = '';
                if (encrypt($val["id"]) == $s_id)
                    $s_select = " selected ";
                $s_option .= "<option $s_select value='" . encrypt($val["id"]) . "'>" . $val["s_category"] . "</option>";
            }
            unset($s_select, $val);
        }

        unset($cond, $res, $mix_value, $mix_where, $s_id);
        return $s_option;
    } catch (Exception $err_obj) {
        show_error($err_obj->getMessage());
    }
}

function makeDaysList($start_day = 1, $limit = 10) {

    $s_option = '';
    $last_limit = 365;



    if ($start_day > 1) {
        $prev_day = $start_day - $limit;
        $s_option .= '<li onclick="show_prev(' . $prev_day . ',' . $limit . ')" ><a href="javascript:void(0);" >&laquo;</a></li>';
    }

    for ($i = 1; $i <= $limit; $i++) {
        $day = $start_day;
        if ($day <= $last_limit) {
            $start_day++;
            //foreach (range($start_day, $limit) as $number) {
            //$lowerlimit  = $number;
            $s_option .= '<li value="' . $day . '" id="li_day' . $day . '" onclick="select_val(this.value);"><a href="javascript:void(0);" >' . $day . '</a></li>';
        }
    }

    //echo $limit = $limit + $lowerlimit;

    if ($start_day <= $last_limit) {
        $s_option .= '<li onclick="show_next(' . $start_day . ' , ' . $limit . ')" ><a href="javascript:void(0);" >&raquo;</a></li> ';
    }

    //echo $s_option;

    return $s_option;
}

function makeOptionBibleBook($mix_where = '', $s_id = '') {
    try {
        $CI = & get_instance();

        $cond = (trim($mix_where)) ? "WHERE " . $mix_where : '';
        $res = $CI->db->query("SELECT * FROM {$CI->db->BIBLE_BOOK} {$cond}");
        #echo $CI->db->last_query();exit;
        $mix_value = $res->result_array();

        $s_option = '';
        if ($mix_value) {
            $s_select = ''; //defined here for unsetting this var 
            foreach ($mix_value as $val) {
                $s_select = '';
                if (encrypt($val["id"]) == $s_id)
                    $s_select = " selected ";
                $s_option .= "<option $s_select value='" . encrypt($val["id"]) . "'>" . $val["s_book_name"] . "</option>";
            }
            unset($s_select, $val);
        }

        unset($cond, $res, $mix_value, $mix_where, $s_id);
        return $s_option;
    } catch (Exception $err_obj) {
        show_error($err_obj->getMessage());
    }
}

function makeOptionBiblechapter($mix_where = '', $s_id = '') {
    try {
        $CI = & get_instance();

        $cond = (trim($mix_where)) ? "WHERE " . $mix_where : '';
        $res = $CI->db->query("SELECT * FROM {$CI->db->BIBLE_CHAPTER} {$cond}");
        $mix_value = $res->result_array();

        $s_option = '';
        if ($mix_value) {
            $s_select = ''; //defined here for unsetting this var 
            foreach ($mix_value as $val) {
                $s_select = '';
                if (encrypt($val["id"]) == $s_id)
                    $s_select = " selected ";
                $s_option .= "<option $s_select value='" . encrypt($val["id"]) . "'>" . $val["s_chapter"] . "</option>";
            }
            unset($s_select, $val);
        }

        unset($cond, $res, $mix_value, $mix_where, $s_id);
        return $s_option;
    } catch (Exception $err_obj) {
        show_error($err_obj->getMessage());
    }
}

function makeOptionBibleverse($mix_where = '', $s_id = '') {
    try {
        $CI = & get_instance();

        $cond = (trim($mix_where)) ? "WHERE " . $mix_where : '';
        $res = $CI->db->query("SELECT * FROM {$CI->db->BIBLE_VERSES} {$cond}");

        $mix_value = $res->result_array();

        $s_option = '';
        if ($mix_value) {
            $s_select = ''; //defined here for unsetting this var 
            foreach ($mix_value as $val) {
                $s_select = '';
                if (encrypt($val["id"]) == $s_id)
                    $s_select = " selected ";
                $s_option .= "<option $s_select value='" . encrypt($val["id"]) . "'>" . $val["i_verses"] . "</option>";
            }
            unset($s_select, $val);
        }

        unset($cond, $res, $mix_value, $mix_where, $s_id);
        return $s_option;
    } catch (Exception $err_obj) {
        show_error($err_obj->getMessage());
    }
}

function makeOptionCountryNew($mix_where = '', $s_id = '') {
    try {
        $CI = & get_instance();

        $cond = (trim($mix_where)) ? "WHERE " . $mix_where : '';
        $res = $CI->db->query("select id,s_country as s_country FROM {$CI->db->COUNTRY} {$cond} order by s_country asc");
        $mix_value = $res->result_array();

        $s_option = '';
        if ($mix_value) {
            $s_select = ''; //defined here for unsetting this var 
            foreach ($mix_value as $val) {
                $s_select = '';
                if (encrypt($val["id"]) == $s_id)
                    $s_select = " selected ";
                $s_option .= "<option $s_select value='" . encrypt($val["id"]) . "'>" . $val["s_country"] . "</option>";
            }
            unset($s_select, $val);
        }

        unset($cond, $res, $mix_value, $mix_where, $s_id);
        return $s_option;
    } catch (Exception $err_obj) {
        show_error($err_obj->getMessage());
    }
}

function makeOptionState($mix_where = '', $s_id = '') {
    try {
        $CI = & get_instance();

        $cond = (trim($mix_where)) ? "WHERE " . $mix_where : '';

        $res = $CI->db->query("select id,s_state as s_state FROM {$CI->db->STATE} {$cond} order by s_state asc");
        $mix_value = $res->result_array();

        $s_option = '';
        if ($mix_value) {
            $s_select = ''; //defined here for unsetting this var 
            foreach ($mix_value as $val) {
                //
                $s_select = '';
                if (encrypt($val["id"]) == $s_id) {
                    $s_select = " selected ";
                    //echo ($val["id"]).' @@ '.decrypt($s_id); exit;
                }
                $s_option .= "<option $s_select value='" . encrypt($val["id"]) . "'>" . $val["s_state"] . "</option>";
            }
            unset($s_select, $val);
        }

        unset($cond, $res, $mix_value, $mix_where, $s_id);
        return $s_option;
    } catch (Exception $err_obj) {
        show_error($err_obj->getMessage());
    }
}

function makeOptionCity($mix_where = '', $s_id = '') {
    try {
        $CI = & get_instance();

        $cond = (trim($mix_where)) ? "WHERE " . $mix_where : '';
        $res = $CI->db->query("select id,s_city as s_city FROM {$CI->db->CITY} {$cond} order by s_city asc");
        $mix_value = $res->result_array();

        $s_option = '';
        if ($mix_value) {
            $s_select = ''; //defined here for unsetting this var 
            foreach ($mix_value as $val) {
                $s_select = '';
                if (encrypt($val["id"]) == $s_id)
                    $s_select = " selected ";
                $s_option .= "<option $s_select value='" . encrypt($val["id"]) . "'>" . $val["s_city"] . "</option>";
            }
            unset($s_select, $val);
        }

        unset($cond, $res, $mix_value, $mix_where, $s_id);
        return $s_option;
    } catch (Exception $err_obj) {
        show_error($err_obj->getMessage());
    }
}

### new month option  box for  paypal

function makeNewOptionMonth($s_id = '') {
    $month_arr = array('01' => array("January"),
        '02' => array("February"),
        '03' => array("March"),
        '04' => array("April"),
        '05' => array("May"),
        '06' => array("June"),
        '07' => array("July"),
        '08' => array("August"),
        '09' => array("September"),
        '10' => array("October"),
        '11' => array("November"),
        '12' => array("December"));


    $s_select = ''; //defined here for unsetting this var 
    foreach ($month_arr as $k => $val) {
        $s_select = '';

        if ($k == $s_id)
            $s_select = " selected ";
        $s_option .= "<option $s_select value='" . $k . "'>" . $k . "</option>";
    }
    unset($s_select, $val);


    unset($cond, $res, $mix_value, $mix_where, $s_id);
    return $s_option;
}

function makePaypalOptionYear($s_id = '') {
    $s_select = ''; //defined here for unsetting this var 
    for ($i = date('Y'); $i < 2050; $i++) {
        $s_select = '';

        if ($i == $s_id)
            $s_select = " selected ";
        $s_option .= "<option $s_select value='" . $i . "'>" . $i . "</option>";
    }
    unset($s_select, $val);


    unset($cond, $res, $mix_value, $mix_where, $s_id);
    return $s_option;
}

function makeMySwapProductOptions($mix_where = '', $s_id = '') {
    try {
        $CI = & get_instance();

        $cond = (trim($mix_where)) ? "WHERE " . $mix_where : '';
        $res = $CI->db->query("select * FROM {$CI->db->ESWAP_PROD} {$cond}");
        $mix_value = $res->result_array();

        $s_option = '';
        if ($mix_value) {
            $s_select = ''; //defined here for unsetting this var 
            foreach ($mix_value as $val) {
                $s_select = '';
                if (encrypt($val["id"]) == $s_id)
                    $s_select = " selected ";
                $s_option .= "<option $s_select value='" . encrypt($val["id"]) . "'>" . $val["s_name"] . "</option>";
            }
            unset($s_select, $val);
        }

        unset($cond, $res, $mix_value, $mix_where, $s_id);
        return $s_option;
    } catch (Exception $err_obj) {
        show_error($err_obj->getMessage());
    }
}

function makeNetpalLanguage($selectlang) {
    try {
        $CI = & get_instance();
        $res = $CI->db->query("SELECT s_language FROM {$CI->db->LANGUAGE} WHERE  is_enabled=1");
        $rstlang = $res->result_array();

        foreach ($rstlang as $v) {
            $lang = explode(',', $v['s_language']);
            foreach ($lang as $val) {
                $arrlang[] = ucfirst(trim($val));
            }
        }

        $arrlang = array_unique($arrlang);

        $s_option = '';
        if ($arrlang) {
            $s_select = ''; //defined here for unsetting this var 
            foreach ($arrlang as $val) {
                $s_select = '';
                if ($val == $selectlang)
                    $s_select = " selected ";
                $s_option .= "<option $s_select value='" . $val . "'>" . $val . "</option>";
            }
            unset($s_select, $val);
        }

        unset($cond, $res, $mix_value, $mix_where, $s_id);
        return $s_option;
    } catch (Exception $err_obj) {
        show_error($err_obj->getMessage());
    }
}

function makeAdminPrivileges($mix_where = '', $s_id_arr) {
    try {

        $CI = & get_instance();

        $cond = (trim($mix_where)) ? "WHERE i_status = 1 AND " . $mix_where : 'WHERE i_status = 1 ';
        $res = $CI->db->query("select * FROM {$CI->db->GRP_PRIVILEGE} {$cond}");

        $mix_value = $res->result_array();

        $s_option = '';
        if ($mix_value) {
            $s_select = ''; //defined here for unsetting this var 
            foreach ($mix_value as $val) {
                $s_select = '';
                if (in_array($val["id"], $s_id_arr)) {
                    $s_select = ' checked = "checked" ';
                }
                $s_option .= '<label><input name="txt_name[]" type="checkbox" ' . $s_select . ' value="' . $val['id'] . '"/> ' . $val['s_privilege'] . '</label>';
            }
            unset($s_select, $val);
        }

        unset($cond, $res, $mix_value, $mix_where, $s_id);
        return $s_option;
    } catch (Exception $err_obj) {
        show_error($err_obj->getMessage());
    }
}

function makeAdminGroup($mix_where = '', $s_id) {
    try {

        $CI = & get_instance();

        $cond = (trim($mix_where)) ? "WHERE i_status =1 AND " . $mix_where : '';
        $res = $CI->db->query("select * FROM {$CI->db->ADMIN_USER_GRP} {$cond}");
        $mix_value = $res->result_array();

        $s_option = '';
        if ($mix_value) {
            $s_select = ''; //defined here for unsetting this var 
            foreach ($mix_value as $val) {
                $s_select = '';
                if ($val['id'] == $s_id) {
                    $s_select = 'selected';
                }
                $s_option .= '<option ' . $s_select . ' value="' . $val['id'] . '" > ' . $val['s_name'] . '</option>';
            }
            unset($s_select, $val);
        }

        unset($cond, $res, $mix_value, $mix_where, $s_id);
        return $s_option;
    } catch (Exception $err_obj) {
        show_error($err_obj->getMessage());
    }
}

# CHAT Category

function makeOptionChatTopCategory($mix_where = '', $s_id = '') {
    try {
        $CI = & get_instance();

        $cond = (trim($mix_where)) ? "WHERE " . $mix_where : '';
        $res = $CI->db->query("SELECT * FROM {$CI->db->chat_category} WHERE i_parent_category = 0 ORDER BY s_category_name ASC");
        $mix_value = $res->result_array();

        $s_option = '';
        if ($mix_value) {
            $s_select = ''; //defined here for unsetting this var 
            foreach ($mix_value as $val) {
                $s_select = '';
                if (($val["id"]) == $s_id)
                    $s_select = " selected ";
                $s_option .= "<option $s_select value='" . ($val["id"]) . "'>" . $val["s_category_name"] . "</option>";
            }
            unset($s_select, $val);
        }

        unset($cond, $res, $mix_value, $mix_where, $s_id);
        return $s_option;
    } catch (Exception $err_obj) {
        show_error($err_obj->getMessage());
    }
}

function makeOptionChatCategory($mix_where = '', $s_id = '') {
    try {
        $CI = & get_instance();

        $cond = (trim($mix_where)) ? "WHERE " . $mix_where : '';
        $res = $CI->db->query("SELECT *,subcat.i_parent_category AS subcat_i_parent_category, 
								(SELECT s_category_name FROM {$CI->db->chat_category} AS pcat WHERE pcat.id=subcat_i_parent_category) AS pcat_name
								FROM {$CI->db->chat_category} AS subcat WHERE subcat.i_parent_category > 0 ORDER BY pcat_name ASC");
        $mix_value = $res->result_array();

        $s_option = '';
        if ($mix_value) {
            $s_select = ''; //defined here for unsetting this var 
            foreach ($mix_value as $val) {
                $s_select = '';
                if (($val["id"]) == $s_id)
                    $s_select = " selected ";
                if ($val["pcat_name"] == '')
                    $s_option .= "<option $s_select value='" . ($val["id"]) . "'>" . $val["s_category_name"] . "</option>";
                else
                    $s_option .= "<option $s_select value='" . ($val["id"]) . "'>" . $val["pcat_name"] . " > " . $val["s_category_name"] . "</option>";
            }
            unset($s_select, $val);
        }

        unset($cond, $res, $mix_value, $mix_where, $s_id);
        return $s_option;
    } catch (Exception $err_obj) {
        show_error($err_obj->getMessage());
    }
}

#######

function generateAttributesListing($mix_where = '', $subCatID = '') {
    try {
        $CI = & get_instance();

        $cond = (trim($mix_where)) ? " " . $mix_where : '';
        $res = $CI->db->query("SELECT p.*, c.s_category_name FROM {$CI->db->category_attribute} AS p 
								LEFT JOIN cg_trade_category c ON p.i_category_id = c.id 
								WHERE p.i_category_id =  {$subCatID}
								{$cond} ORDER BY p.s_name ASC");
        $mix_value = $res->result_array();

        //pr($mix_value,1);
        $html = '';
        if (count($mix_value)) {
            $html .= '<div><strong>Product Attributes</strong></div>
					 <div class="clr"></div>
					 ';
            foreach ($mix_value as $k => $val) {

                $html .= '<div class="lable01 newwidth-lable01">' . $val['s_name'] . ':</div>';
                $name = str_replace(' ', '_', strtolower($val['s_name']));

                if ($val['i_type'] == 1) {

                    $html .= '<div class="field03 newwidth">
								<input type="text" class="txtwidth" name="' . $name . '">
							  </div>
							  <div class="clr"></div>';
                } else {
                    $html .= '<div class="field03 newwidth">
								<input type="text" class="txtwidth" name="' . $name . '[]">
							  </div>
							 <div class="clr"></div>
							 <div class="attr_div" val="' . $name . '"></div>
							  <div class="clr"></div>
							  <div class="lable01 newwidth-lable01"></div>
							  <div class="field03 newwidth">
							  <span style="cursor:pointer; float:right; font-size:13px;" onclick="add_div($(this));">Add More</span>
							</div>
							 ';
                }
            }
        }
        return $html;
    } catch (Exception $err_obj) {
        show_error($err_obj->getMessage());
    }
}

function makeOptionRingCategory_NEW($mix_where = '', $s_id = '') {

    try {
        $CI = & get_instance();

        $cond = (trim($mix_where)) ? "WHERE " . $mix_where : '';
        $res = $CI->db->query("SELECT *,subcat.i_parent_category AS subcat_i_parent_category, 
								(SELECT s_category_name FROM {$CI->db->RING_CAT} AS pcat 
								WHERE pcat.id=subcat_i_parent_category) AS pcat_name
								FROM {$CI->db->RING_CAT} AS subcat WHERE subcat.i_parent_category > 0 ORDER BY pcat_name ASC");
        $mix_value = $res->result_array();

        $s_option = '';
        if ($mix_value) {
            $s_select = ''; //defined here for unsetting this var 
            foreach ($mix_value as $val) {
                $s_select = '';
                if (encrypt($val["id"]) == $s_id)
                    $s_select = " selected ";
                if ($val["pcat_name"] == '')
                    $s_option .= "<option $s_select value='" . encrypt(($val["id"])) . "'>" . $val["s_category_name"] . "</option>";
                else
                    $s_option .= "<option $s_select value='" . encrypt($val["id"]) . "'>" . $val["pcat_name"] . " > " . $val["s_category_name"] . "</option>";
            }
            unset($s_select, $val);
        }

        unset($cond, $res, $mix_value, $mix_where, $s_id);
        return $s_option;
    } catch (Exception $err_obj) {
        show_error($err_obj->getMessage());
    }
}

function makeOptionGenre($mix_where = '', $s_id = '') {
    try {
        $CI = & get_instance();

        $cond = (trim($mix_where)) ? "WHERE " . $mix_where . ' AND' : 'WHERE';

        $res = $CI->db->query("SELECT * FROM {$CI->db->genre} {$cond} i_status='1' order by genre_name desc");
        $mix_value = $res->result_array();

        $s_option = '';
        if ($mix_value) {
            $s_select = ''; //defined here for unsetting this var 
            foreach ($mix_value as $val) {
                $s_select = '';
                if (encrypt($val["id"]) == $s_id)
                    $s_select = " selected ";
                $s_option .= "<option $s_select value='" . $val["id"] . "'>" . $val["genre_name"] . "</option>";
            }
            unset($s_select, $val);
        }

        unset($cond, $res, $mix_value, $mix_where, $s_id);
        return $s_option;
    } catch (Exception $err_obj) {
        show_error($err_obj->getMessage());
    }
}

function makeOptionFruit($s_id = '') {
    try {
        $CI = & get_instance();

        //$cond = (trim($mix_where)) ? "WHERE ".$mix_where: '';      

        $res = $CI->db->query("SELECT * FROM {$CI->db->BIBLE_FRUIT}  order by s_fruit_name desc");
        $mix_value = $res->result_array();

        $s_option = '';

        if ($mix_value) {
            $s_select = ''; //defined here for unsetting this var 
            foreach ($mix_value as $val) {
                $s_select = '';
                if ($val["id"] == $s_id)
                    $s_select = " selected ";
                $s_option .= "<option $s_select value='" . $val["id"] . "'>" . $val["s_fruit_name"] . "</option>";
            }
            unset($s_select, $val);
        }

        unset($cond, $res, $mix_value, $mix_where, $s_id);
        return $s_option;
    } catch (Exception $err_obj) {
        show_error($err_obj->getMessage());
    }
}

function get_skill_chart($project_id) {

    $CI = & get_instance();
    $project_info = $CI->projects_model->get_by_id($project_id);
    $project_info = $project_info;

    $skill_arr = $CI->projects_model->get_all_skill_by_project_id($project_id);
    $skill = array();

    if (count($skill_arr)) {
        foreach ($skill_arr as $s_val) {
            array_push($skill, $s_val['s_name']);
        }
    }

    $data['skill_arr'] = $skill;
    //$data['suffice_skill_arr'] = $this->projects_model->getSkillSufficency($skill_arr);
    //pr($skill_arr);
    ###############################################
    ### forming manage skill calendar array
    ###############################################


    $no_days = get_date_diff($project_info['dt_start_date'], $project_info['dt_end_date']);
    $total_days = $no_days[0]['difference'];

    $projectStartDay = getDesiredDate($project_info['dt_start_date'], 'd');
    $projectStartMnth = getDesiredDate($project_info['dt_start_date'], 'm');
    $projectStartYear = getDesiredDate($project_info['dt_start_date'], 'y');

    $calendar_arr = array();

    if ($total_days != 0) {

        $inc_end_date = '';
        $i = 0;
        while ($inc_end_date < $project_info['dt_end_date']) {

            $calendar_arr[$i]['day'] = date('d', mktime(0, 0, 0, $projectStartMnth, $projectStartDay, $projectStartYear));
            $calendar_arr[$i]['month'] = date('M', mktime(0, 0, 0, $projectStartMnth, $projectStartDay, $projectStartYear));

            $calendar_arr[$i]['year'] = date('Y', mktime(0, 0, 0, $projectStartMnth, $projectStartDay, $projectStartYear));
            $calendar_arr[$i]['day_name'] = getShortDay(date('l', mktime(0, 0, 0, $projectStartMnth, $projectStartDay, $projectStartYear)));


            $projectStartDay = $projectStartDay + 1;
            $inc_end_date = $calendar_arr[$i]['year'] . '-' . date('m', mktime(0, 0, 0, $projectStartMnth, $projectStartDay, $projectStartYear)) . '-' . $calendar_arr[$i]['day'];
            $calendar_arr[$i]['dt'] = $inc_end_date;
            $i++;
        }
    }

    $calendar_arr = $calendar_arr;

    ###############################################
    ### end forming manage skill calendar array
    ###############################################

    $html = '';
    $html .= '<table width="100%" border="0" cellspacing="0" cellpadding="0">
                                      <tr>
                                            <td align="left" style="width:181px;" valign="top">
                                            	<table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                    <tr>
                                                          <td style="height:26px; width:142px;">&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                          <td class="blue_back_line" style=" border-left:1px solid #f0f0f0;"><div>&nbsp;</div></td>
                                                    </tr>';


    if (count($project_info['skill'])) {

        foreach ($project_info['skill'] as $key => $p_val) {
            $html .= '<tr>
								<td align="left" class="white_td_line" style=" border-left:1px solid #d9d9d9; padding-left:10px;">
								<div>' . $p_val['s_name'] . ' (' . $p_val['i_total_manpower_req'] . ')</div>
                                </td>
                                </tr>';
        }
    }


    $html .= '</table>	
                             </td>
                               <td align="left" valign="top">
                               <div class="scroll_div" style="width:315px;">
								<table width="100%" border="0" cellspacing="0" cellpadding="0">
								<tr class="blue_back">';

    if (count($calendar_arr)) {
        foreach ($calendar_arr as $c_val) {
            $CSS = ($c_val['day_name'] == 'Sun') ? 'class="red_txt"' : '';
            $html .= ' <th align="center" ' . $CSS . '>' . $c_val['day_name'] . '</th>';
        }
    }

    $html .= ' </tr> <tr class="blue_back">';
    if (count($calendar_arr)) {
        foreach ($calendar_arr as $c_val) {
            $CSS = ($c_val['day_name'] == 'Sun') ? 'class="red_txt"' : '';
            $html .= '<td align="center">' . $c_val['day'] . '<br />
							   ' . $c_val['month'] . '<br />' . $c_val['year'] . '</td>';
        }
    }
    $html .= '</tr>';

    //pr($project_info['skill']);
    if (count($project_info['skill'])) {
        foreach ($project_info['skill'] as $key => $p_val) {

            $html .= '<tr>';

            #pr($calendar_arr);
            if (count($calendar_arr)) {
                foreach ($calendar_arr as $c_val) {

                    $CSS = ($c_val['day_name'] == 'Sun') ? 'class="red_txt"' : '';

                    $is_sufficient = checkSufficientSkillPerDay($p_val['s_name'], $p_val['i_project_id'], $c_val['dt']);

                    if ($c_val['dt'] <= $p_val['dt_end_date'] && $c_val['dt'] >= $p_val['dt_start_date']) {

                        if ($is_sufficient >= $p_val['i_total_manpower_req']) {
                            $div_css = 'blue_td';
                        } else if ($is_sufficient < $p_val['i_total_manpower_req'] && $is_sufficient != 0) {
                            $div_css = 'yellow_td';
                        } else if ($is_sufficient == 0) {
                            $div_css = 'red_td';
                        }

                        //$div_css = ($is_sufficient >= $p_val['i_total_manpower_req'])?'blue_td':'red_td';
                    } else {

                        $div_css = '';
                    }

                    #red_td 	 blue_td dt_end_date dt

                    $html .= '<td align="center">
				   				<div class="' . $div_css . '">&nbsp; </div></td>';
                }
            }

            $html .= ' </tr>';
        }
    }

    $html .= '   </table> </div> </td></tr>
                                      <tr>
                                          <td align="center">&nbsp;</td>
                                          <td colspan="2" valign="top" style="height:30px;"><img src="images/blue_dot.png" alt="" /> Full   &nbsp; &nbsp; 
                                          <img src="images/yellow_dot.png" alt="" /> Partially Fulfilled
                                          &nbsp; &nbsp; 
                                          <img src="images/red_dot.png" alt="" /> Vacant
                                          </td>
                                    </tr>
                                   
                                </table>';

    return $html;
}

function getProperTimeZone($zone_val = '') {

    $timezones = array(
        '(GMT-12:00) International Date Line West' => 'Pacific/Wake',
        '(GMT-11:00) Midway Island' => 'Pacific/Apia',
        '(GMT-11:00) Samoa' => 'Pacific/Apia',
        '(GMT-10:00) Hawaii' => 'Pacific/Honolulu',
        '(GMT-09:00) Alaska' => 'America/Anchorage',
        '(GMT-08:00) Pacific Time (US &amp; Canada); Tijuana' => 'America/Los_Angeles',
        '(GMT-07:00) Arizona' => 'America/Phoenix',
        '(GMT-07:00) Chihuahua' => 'America/Chihuahua',
        '(GMT-07:00) La Paz' => 'America/Chihuahua',
        '(GMT-07:00) Mazatlan' => 'America/Chihuahua',
        '(GMT-07:00) Mountain Time (US &amp; Canada)' => 'America/Denver',
        '(GMT-06:00) Central America' => 'America/Managua',
        '(GMT-06:00) Central Time (US &amp; Canada)' => 'America/Chicago',
        '(GMT-06:00) Guadalajara' => 'America/Mexico_City',
        '(GMT-06:00) Mexico City' => 'America/Mexico_City',
        '(GMT-06:00) Monterrey' => 'America/Mexico_City',
        '(GMT-06:00) Saskatchewan' => 'America/Regina',
        '(GMT-05:00) Bogota' => 'America/Bogota',
        '(GMT-05:00) Eastern Time (US &amp; Canada)' => 'America/New_York',
        '(GMT-05:00) Indiana (East)' => 'America/Indiana/Indianapolis',
        '(GMT-05:00) Lima' => 'America/Bogota',
        '(GMT-05:00) Quito' => 'America/Bogota',
        '(GMT-04:00) Atlantic Time (Canada)' => 'America/Halifax',
        '(GMT-04:00) Caracas' => 'America/Caracas',
        '(GMT-04:00) La Paz' => 'America/Caracas',
        '(GMT-04:00) Santiago' => 'America/Santiago',
        '(GMT-03:30) Newfoundland' => 'America/St_Johns',
        '(GMT-03:00) Brasilia' => 'America/Sao_Paulo',
        '(GMT-03:00) Buenos Aires' => 'America/Argentina/Buenos_Aires',
        '(GMT-03:00) Georgetown' => 'America/Argentina/Buenos_Aires',
        '(GMT-03:00) Greenland' => 'America/Godthab',
        '(GMT-02:00) Mid-Atlantic' => 'America/Noronha',
        '(GMT-01:00) Azores' => 'Atlantic/Azores',
        '(GMT-01:00) Cape Verde Is.' => 'Atlantic/Cape_Verde',
        '(GMT) Casablanca' => 'Africa/Casablanca',
        '(GMT) Edinburgh' => 'Europe/London',
        '(GMT) Greenwich Mean Time : Dublin' => 'Europe/London',
        '(GMT) Lisbon' => 'Europe/London',
        '(GMT) London' => 'Europe/London',
        '(GMT) Monrovia' => 'Africa/Casablanca',
        '(GMT+01:00) Amsterdam' => 'Europe/Berlin',
        '(GMT+01:00) Belgrade' => 'Europe/Belgrade',
        '(GMT+01:00) Berlin' => 'Europe/Berlin',
        '(GMT+01:00) Bern' => 'Europe/Berlin',
        '(GMT+01:00) Bratislava' => 'Europe/Belgrade',
        '(GMT+01:00) Brussels' => 'Europe/Paris',
        '(GMT+01:00) Budapest' => 'Europe/Belgrade',
        '(GMT+01:00) Copenhagen' => 'Europe/Paris',
        '(GMT+01:00) Ljubljana' => 'Europe/Belgrade',
        '(GMT+01:00) Madrid' => 'Europe/Paris',
        '(GMT+01:00) Paris' => 'Europe/Paris',
        '(GMT+01:00) Prague' => 'Europe/Belgrade',
        '(GMT+01:00) Rome' => 'Europe/Berlin',
        '(GMT+01:00) Sarajevo' => 'Europe/Sarajevo',
        '(GMT+01:00) Skopje' => 'Europe/Sarajevo',
        '(GMT+01:00) Stockholm' => 'Europe/Berlin',
        '(GMT+01:00) Vienna' => 'Europe/Berlin',
        '(GMT+01:00) Warsaw' => 'Europe/Sarajevo',
        '(GMT+01:00) West Central Africa' => 'Africa/Lagos',
        '(GMT+01:00) Zagreb' => 'Europe/Sarajevo',
        '(GMT+02:00) Athens' => 'Europe/Istanbul',
        '(GMT+02:00) Bucharest' => 'Europe/Bucharest',
        '(GMT+02:00) Cairo' => 'Africa/Cairo',
        '(GMT+02:00) Harare' => 'Africa/Johannesburg',
        '(GMT+02:00) Helsinki' => 'Europe/Helsinki',
        '(GMT+02:00) Istanbul' => 'Europe/Istanbul',
        '(GMT+02:00) Jerusalem' => 'Asia/Jerusalem',
        '(GMT+02:00) Kyiv' => 'Europe/Helsinki',
        '(GMT+02:00) Minsk' => 'Europe/Istanbul',
        '(GMT+02:00) Pretoria' => 'Africa/Johannesburg',
        '(GMT+02:00) Riga' => 'Europe/Helsinki',
        '(GMT+02:00) Sofia' => 'Europe/Helsinki',
        '(GMT+02:00) Tallinn' => 'Europe/Helsinki',
        '(GMT+02:00) Vilnius' => 'Europe/Helsinki',
        '(GMT+03:00) Baghdad' => 'Asia/Baghdad',
        '(GMT+03:00) Kuwait' => 'Asia/Riyadh',
        '(GMT+03:00) Moscow' => 'Europe/Moscow',
        '(GMT+03:00) Nairobi' => 'Africa/Nairobi',
        '(GMT+03:00) Riyadh' => 'Asia/Riyadh',
        '(GMT+03:00) St. Petersburg' => 'Europe/Moscow',
        '(GMT+03:00) Volgograd' => 'Europe/Moscow',
        '(GMT+03:30) Tehran' => 'Asia/Tehran',
        '(GMT+04:00) Abu Dhabi' => 'Asia/Muscat',
        '(GMT+04:00) Baku' => 'Asia/Tbilisi',
        '(GMT+04:00) Muscat' => 'Asia/Muscat',
        '(GMT+04:00) Tbilisi' => 'Asia/Tbilisi',
        '(GMT+04:00) Yerevan' => 'Asia/Tbilisi',
        '(GMT+04:30) Kabul' => 'Asia/Kabul',
        '(GMT+05:00) Ekaterinburg' => 'Asia/Yekaterinburg',
        '(GMT+05:00) Islamabad' => 'Asia/Karachi',
        '(GMT+05:00) Karachi' => 'Asia/Karachi',
        '(GMT+05:00) Tashkent' => 'Asia/Karachi',
        '(GMT+05:30) Chennai' => 'Asia/Kolkata',
        '(GMT+05:30) Kolkata' => 'Asia/Kolkata',
        '(GMT+05:30) Mumbai' => 'Asia/Kolkata',
        '(GMT+05:30) New Delhi' => 'Asia/Kolkata',
        '(GMT+05:45) Kathmandu' => 'Asia/Katmandu',
        '(GMT+06:00) Almaty' => 'Asia/Novosibirsk',
        '(GMT+06:00) Astana' => 'Asia/Dhaka',
        '(GMT+06:00) Dhaka' => 'Asia/Dhaka',
        '(GMT+06:00) Novosibirsk' => 'Asia/Novosibirsk',
        '(GMT+06:00) Sri Jayawardenepura' => 'Asia/Colombo',
        '(GMT+06:30) Rangoon' => 'Asia/Rangoon',
        '(GMT+07:00) Bangkok' => 'Asia/Bangkok',
        '(GMT+07:00) Hanoi' => 'Asia/Bangkok',
        '(GMT+07:00) Jakarta' => 'Asia/Bangkok',
        '(GMT+07:00) Krasnoyarsk' => 'Asia/Krasnoyarsk',
        '(GMT+08:00) Beijing' => 'Asia/Hong_Kong',
        '(GMT+08:00) Chongqing' => 'Asia/Hong_Kong',
        '(GMT+08:00) Hong Kong' => 'Asia/Hong_Kong',
        '(GMT+08:00) Irkutsk' => 'Asia/Irkutsk',
        '(GMT+08:00) Kuala Lumpur' => 'Asia/Singapore',
        '(GMT+08:00) Perth' => 'Australia/Perth',
        '(GMT+08:00) Singapore' => 'Asia/Singapore',
        '(GMT+08:00) Taipei' => 'Asia/Taipei',
        '(GMT+08:00) Ulaan Bataar' => 'Asia/Irkutsk',
        '(GMT+08:00) Urumqi' => 'Asia/Hong_Kong',
        '(GMT+09:00) Osaka' => 'Asia/Tokyo',
        '(GMT+09:00) Sapporo' => 'Asia/Tokyo',
        '(GMT+09:00) Seoul' => 'Asia/Seoul',
        '(GMT+09:00) Tokyo' => 'Asia/Tokyo',
        '(GMT+09:00) Yakutsk' => 'Asia/Yakutsk',
        '(GMT+09:30) Adelaide' => 'Australia/Adelaide',
        '(GMT+09:30) Darwin' => 'Australia/Darwin',
        '(GMT+10:00) Brisbane' => 'Australia/Brisbane',
        '(GMT+10:00) Canberra' => 'Australia/Sydney',
        '(GMT+10:00) Guam' => 'Pacific/Guam',
        '(GMT+10:00) Hobart' => 'Australia/Hobart',
        '(GMT+10:00) Melbourne' => 'Australia/Sydney',
        '(GMT+10:00) Port Moresby' => 'Pacific/Guam',
        '(GMT+10:00) Sydney' => 'Australia/Sydney',
        '(GMT+10:00) Vladivostok' => 'Asia/Vladivostok',
        '(GMT+11:00) Magadan' => 'Asia/Magadan',
        '(GMT+11:00) New Caledonia' => 'Asia/Magadan',
        '(GMT+11:00) Solomon Is.' => 'Asia/Magadan',
        '(GMT+12:00) Auckland' => 'Pacific/Auckland',
        '(GMT+12:00) Fiji' => 'Pacific/Fiji',
        '(GMT+12:00) Kamchatka' => 'Pacific/Fiji',
        '(GMT+12:00) Marshall Is.' => 'Pacific/Fiji',
        '(GMT+12:00) Wellington' => 'Pacific/Auckland',
        '(GMT+13:00) Nuku\'alofa' => 'Pacific/Tongatapu',
    );
    $fomatted_val = '';
    $total_place = 0;

    foreach ($timezones as $k => $val) {
        if ($zone_val == $val) {
            if ($total_place == 0)
                $fomatted_val = $k;
            else {
                $arr = array();
                $arr = explode(') ', $k);
                $fomatted_val .= ', ' . $arr[1];
            }
            $total_place++;
        }
    }

    //pr(explode(' ', '(GMT+12:00) Wellington'));

    return $fomatted_val;
}

function makeOptionBibleStartVerse($mix_where = '', $s_id = '') {
    try {
        $CI = & get_instance();

        $cond = (trim($mix_where)) ? "WHERE " . $mix_where : '';
        $res = $CI->db->query("SELECT * FROM {$CI->db->BIBLE_VERSES} {$cond}");
        $mix_value = $res->result_array();

        $s_option = '';
        if ($mix_value) {
            $s_select = ''; //defined here for unsetting this var 
            foreach ($mix_value as $val) {
                $s_select = '';
                if (encrypt($val["id"]) == $s_id)
                    $s_select = " selected ";
                $s_option .= "<option $s_select value='" . encrypt($val["id"]) . "'>" . $val["i_verses"] . "</option>";
            }
            unset($s_select, $val);
        }

        unset($cond, $res, $mix_value, $mix_where, $s_id);
        return $s_option;
    } catch (Exception $err_obj) {
        show_error($err_obj->getMessage());
    }
}

function makeOptionBibleEndVerse($mix_where = '', $s_id = '') {
    try {
        $CI = & get_instance();

        $cond = (trim($mix_where)) ? "WHERE " . $mix_where : '';
        $res = $CI->db->query("SELECT * FROM {$CI->db->BIBLE_VERSES} {$cond}");
        $mix_value = $res->result_array();
		//echo $CI->db->last_query();
        $s_option = '';
        if ($mix_value) {
            $s_select = ''; //defined here for unsetting this var 
            foreach ($mix_value as $val) {
                $s_select = '';
                if (encrypt($val["id"]) == $s_id)
                    $s_select = " selected ";
                $s_option .= "<option $s_select value='" . encrypt($val["id"]) . "'>" . $val["i_verses"] . "</option>";
            }
            unset($s_select, $val);
        }

        unset($cond, $res, $mix_value, $mix_where, $s_id);
        return $s_option;
    } catch (Exception $err_obj) {
        show_error($err_obj->getMessage());
    }
}

function makeOptionRingSubCategory($mix_where = '', $s_id = '')
{
 try {
        $CI = & get_instance();

        $cond = (trim($mix_where)) ? " " . $mix_where : '';
        $res = $CI->db->query("SELECT * FROM {$CI->db->RING_CAT} WHERE {$cond}");
		//echo $CI->db->last_query();
        $mix_value = $res->result_array();

        $s_option = '';
        if ($mix_value) {
            $s_select = ''; //defined here for unsetting this var 
            foreach ($mix_value as $val) {
                $s_select = '';
                if (encrypt($val["id"]) == $s_id)
                    $s_select = " selected ";
                $s_option .= "<option $s_select value='" . encrypt($val["id"]) . "'>" . $val["s_category_name"] . "</option>";
            }
            unset($s_select, $val);
        }

        unset($cond, $res, $mix_value, $mix_where, $s_id);
        return $s_option;
    } catch (Exception $err_obj) {
        show_error($err_obj->getMessage());
    }
}

function makeOptionSubCategoryMc($type, $s_id = '')
{
 try {
        $CI = & get_instance();

        $cond = (trim($mix_where)) ? " " . $mix_where : '';
		if($type == 'audio')
		{
        $res = $CI->db->query("SELECT * FROM cg_mc_audio_cat");
		}
		else if($type == 'video')
		{
		$res = $CI->db->query("SELECT * FROM cg_mc_video_cat");
		}
		else if($type == 'news')
		{
		$res = $CI->db->query("SELECT * FROM cg_christian_news_cat");
		}
		//echo $CI->db->last_query();
        $mix_value = $res->result_array();

        $s_option = '';
        if ($mix_value) {
            $s_select = ''; //defined here for unsetting this var 
            foreach ($mix_value as $val) {
                $s_select = '';
                if (encrypt($val["id"]) == $s_id)
                    $s_select = " selected ";
				if($type == 'news')
				{
                $s_option .= "<option $s_select value='" . encrypt($val["id"]) . "'>" . $val["s_cat_name"] . "</option>";
				}
				else
				{
				$s_option .= "<option $s_select value='" . encrypt($val["id"]) . "'>" . $val["s_name"] . "</option>";
				}
            }
            unset($s_select, $val);
        }

        unset($cond, $res, $mix_value, $mix_where, $s_id);
        return $s_option;
    } catch (Exception $err_obj) {
        show_error($err_obj->getMessage());
    }
}
function makeOptionChurchRingCategory($mix_where = '', $s_id = '') {
    try {
        $CI = & get_instance();

        $cond = (trim($mix_where)) ? " AND  " . $mix_where : '';
        $res = $CI->db->query("SELECT * FROM cg_church_ring_category WHERE i_parent_category = 0 AND church_id = '".$_SESSION['logged_church_id']."' {$cond}");
        $mix_value = $res->result_array();

        
        
        $s_option = '';
        if ($mix_value) {
            $s_select = ''; //defined here for unsetting this var 
            foreach ($mix_value as $val) {
                $s_select = '';
                if (encrypt($val["id"]) == $s_id)
                    $s_select = " selected ";
                $s_option .= "<option $s_select value='" . encrypt($val["id"]) . "'>" . $val["s_category_name"] . "</option>";
            }
            unset($s_select, $val);
        }

        unset($cond, $res, $mix_value, $mix_where, $s_id);
        return $s_option;
    } catch (Exception $err_obj) {
        show_error($err_obj->getMessage());
    }
}