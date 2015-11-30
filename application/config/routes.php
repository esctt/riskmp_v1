<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There area two reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router what URI segments to use if those provided
| in the URL cannot be matched to a valid route.
|
*/

$route['project/(:any)'] = 'project/$1';
$route['project'] = 'user/dashboard';
$route['task/(:any)'] = 'task/$1';
$route['task'] = 'user/dashboard';
$route['risk/(:any)'] = 'risk/$1';
$route['risk'] = 'user/dashboard';
$route['response/(:any)'] = 'response/$1';
$route['response'] = 'user/dashboard';
$route['short_response_report/(:any)'] = 'project/short_response_report/$1';
$route['short_risk_report/(:any)'] = 'project/short_risk_report/$1';
$route['short_task_report/(:any)'] = 'project/short_task_report/$1';

$route['data_fetch/(:any)'] = 'data_fetch/$1';
$route['dashboard'] = 'user/dashboard';
$route['login'] = 'user/login_view';

$route['agreement'] = 'user/agreement';

$route['logout'] = 'user/logout';
$route['user/(:any)'] = 'user/$1';
$route['register'] = 'user/register';
$route['home'] = 'pages/home';
$route['about'] = 'pages/about';
$route['support'] = 'pages/about';
$route['install'] = 'pages/installing';
$route['pages/(:any)'] = 'pages/$1';
$route['paytest/(:any)'] = 'paypal/paytest/$1';
$route['paypal_ipn'] = 'paypal_ipn';
$route['paypal_ipn/(:any)'] = 'paypal_ipn/$1';
$route['admin/(:any)'] = 'admin/$1';
$route['global_risk_report'] = 'user/global_risk_report';
$route['short_global_risk_report'] = 'user/short_global_risk_report';
$route['default_controller'] = 'pages/home';


/* End of file routes.php */
/* Location: ./application/config/routes.php */