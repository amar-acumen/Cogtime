<?php  
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$config['Sandbox'] = TRUE;
$config['APIVersion'] = '85.0';
#$config['APIUsername'] = $config['Sandbox'] ? 'acumen_1336143517_biz_api1.gmail.com' : 'PRODUCTION_USERNAME_GOES_HERE';
#$config['APIPassword'] = $config['Sandbox'] ? '1336143541' : 'PRODUCTION_PASSWORD_GOES_HERE';
#$config['APISignature'] = $config['Sandbox'] ? 'ALF.D3o7K6X3w0JVvKtw5wKVP6YjA.qTO0htfm4vLeOuUPwMI9D-f0ix' : 'PRODUCTION_SIGNATURE_GOES_HERE';


$config['APIUsername'] = $config['Sandbox'] ? 'sidneynazz_api1.gmail.com' : 'PRODUCTION_USERNAME_GOES_HERE';
$config['APIPassword'] = $config['Sandbox'] ? '1364905946' : 'PRODUCTION_PASSWORD_GOES_HERE';
$config['APISignature'] = $config['Sandbox'] ? 'AlpKjcufLj8sJqnFfSEJgHbFhTxOAtEpOr1DMAierdVTI0MaHS8OttfT' : 'PRODUCTION_SIGNATURE_GOES_HERE';


$config['DeviceID'] = $config['Sandbox'] ? '' : 'PRODUCTION_DEVICE_ID_GOES_HERE';
$config['ApplicationID'] = $config['Sandbox'] ? 'APP-80W284485P519543T' : 'PRODUCTION_APP_ID_GOES_HERE';
$config['DeveloperEmailAccount'] = $config['Sandbox'] ? 'pay@seoursite.com': 'PRODUCTION_DEV_EMAIL_GOES_HERE';//'acumen.demo@gmail.com' : 'PRODUCTION_DEV_EMAIL_GOES_HERE';

$config['PayFlowUsername'] = $config['Sandbox'] ? 'tester' : 'PRODUCTION_USERNAME_GOGES_HERE';
$config['PayFlowPassword'] = $config['Sandbox'] ? 'Passw0rd~' : 'PRODUCTION_PASSWORD_GOES_HERE';
$config['PayFlowVendor'] = $config['Sandbox'] ? 'angelleye' : 'PRODUCTION_VENDOR_GOES_HERE';
$config['PayFlowPartner'] = $config['Sandbox'] ? 'PayPal' : 'PRODUCTION_PARTNER_GOES_HERE';

$config['API_ENDPOINT'] = $config['Sandbox'] ? 'https://api-3t.sandbox.paypal.com/nvp':'https://api-3t.paypal.com/nvp';  // For Live
//define('API_ENDPOINT', 'https://api-3t.sandbox.paypal.com/nvp');

/* End of file paypal.php */
/* Location: ./system/application/config/paypal.php */