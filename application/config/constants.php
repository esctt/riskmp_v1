<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
define('FILE_READ_MODE', 0644);
define('FILE_WRITE_MODE', 0666);
define('DIR_READ_MODE', 0755);
define('DIR_WRITE_MODE', 0777);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/

define('FOPEN_READ',							'rb');
define('FOPEN_READ_WRITE',						'r+b');
define('FOPEN_WRITE_CREATE_DESTRUCTIVE',		'wb'); // truncates existing file data, use with care
define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE',	'w+b'); // truncates existing file data, use with care
define('FOPEN_WRITE_CREATE',					'ab');
define('FOPEN_READ_WRITE_CREATE',				'a+b');
define('FOPEN_WRITE_CREATE_STRICT',				'xb');
define('FOPEN_READ_WRITE_CREATE_STRICT',		'x+b');

/*
 * BILLING CONSTANTS
 */
define('MONTHLY_PRICE', 29.00);
define('ANNUAL_PRICE', 189.00);
define('CALENDAR_MONTHS', serialize(array(
    "01" => "January",
    "02" => "February",
    "03" => "March",
    "04" => "April",
    "05" => "May",
    "06" => "June",
    "07" => "July",
    "08" => "August",
    "09" => "September",
    "10" => "October",
    "11" => "November",
    "12" => "December"
)));
define('PROVINCES', serialize(array(
    'AB' => 'Alberta',
    'BC' => 'British Columbia',
    'MB' => 'Manitoba',
    'NB' => 'New Brunswick',
    'NL' => 'Newfoundland and Labrador',
    'NS' => 'Nova Scotia',
    'NT' => 'Northwest Territories',
    'NU' => 'Nunavut',
    'ON' => 'Ontario',
    'PE' => 'Price Edward Island',
    'QC' => 'Quebec',
    'SK' => 'Saskatchewan',
    'YT' => 'Yukon'
)));
define('TAXAMT', serialize(array(
    'AB' => 0.05,
    'BC' => 0.05,
    'MB' => 0.05,
    'NB' => 0.13,
    'NL' => 0.13,
    'NS' => 0.15,
    'NT' => 0.05,
    'NU' => 0.05,
    'ON' => 0.13,
    'PE' => 0.14,
    'QC' => 0.14975,
    'SK' => 0.10,
    'YT' => 0.05
)));
define('DEFAULT_TAX', 0.13); //tax to be charged if user is not in Canada
define('BUSINESS_NUMBER', 809252828);
/*
 * OTHER CONSTANTS
 */
/* End of file constants.php */
/* Location: ./application/config/constants.php */