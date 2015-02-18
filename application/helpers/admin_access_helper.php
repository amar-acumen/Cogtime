<?php

function adm($controller, $function='index') {
	$ci = get_instance();
	
	if(@$_SESSION['user_type'] == 4) {
		return '';
	}
	
	$controller = str_replace('-', '_', $controller);
	$function = str_replace('-', '_', $function);
	
	$ci->load->model('acl_modules_users_model');
	$access = $ci->acl_modules_users_model->get_access($controller, $function, @$_SESSION['user_id']);
	
	if($access) {
		return '';
	}
	else {
		return 'display:none;';
	}
}

function make_statistics_year($from,$to)
{
	$s_option="";
	for($i=$from;$i<=$to;$i++)
	{
		$s_option .= "<option value='$i'>$i</option>";
	}
	return $s_option;
	
}