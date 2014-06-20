<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| TIMEZONES
|--------------------------------------------------------------------------
|
| Set the default timezone for the app.
|
*/

$config['datetime_timezone_default']	= 'Europe/London';


/*
|--------------------------------------------------------------------------
| DATE/TIME FORMATS
|--------------------------------------------------------------------------
|
| The following date/time formats are supported
|
*/

$config['datetime_format_date']			= array();
$config['datetime_format_date_default']	= 'DD/MM/YYYY';

$config['datetime_format_date']['DD/MM/YYYY']			= new stdClass();
$config['datetime_format_date']['DD/MM/YYYY']->slug		= 'DD/MM/YYYY';
$config['datetime_format_date']['DD/MM/YYYY']->label	= 'DD/MM/YYYY';
$config['datetime_format_date']['DD/MM/YYYY']->format	= 'd/m/Y';

$config['datetime_format_date']['DD-MM-YYYY']			= new stdClass();
$config['datetime_format_date']['DD-MM-YYYY']->slug		= 'DD-MM-YYYY';
$config['datetime_format_date']['DD-MM-YYYY']->label	= 'DD-MM-YYYY';
$config['datetime_format_date']['DD-MM-YYYY']->format	= 'd-m-Y';

$config['datetime_format_date']['DD/MM/YY']				= new stdClass();
$config['datetime_format_date']['DD/MM/YY']->slug		= 'DD/MM/YY';
$config['datetime_format_date']['DD/MM/YY']->label		= 'DD/MM/YY';
$config['datetime_format_date']['DD/MM/YY']->format		= 'd/m/y';

$config['datetime_format_date']['DD-MM-YY']				= new stdClass();
$config['datetime_format_date']['DD-MM-YY']->slug		= 'DD-MM-YY';
$config['datetime_format_date']['DD-MM-YY']->label		= 'DD-MM-YY';
$config['datetime_format_date']['DD-MM-YY']->format		= 'd-m-y';

$config['datetime_format_date']['MM/DD/YYYY']			= new stdClass();
$config['datetime_format_date']['MM/DD/YYYY']->slug		= 'MM/DD/YYYY';
$config['datetime_format_date']['MM/DD/YYYY']->label	= 'MM/DD/YYYY';
$config['datetime_format_date']['MM/DD/YYYY']->format	= 'm/d/Y';

$config['datetime_format_date']['MM-DD-YYYY']			= new stdClass();
$config['datetime_format_date']['MM-DD-YYYY']->slug		= 'MM-DD-YYYY';
$config['datetime_format_date']['MM-DD-YYYY']->label	= 'MM-DD-YYYY';
$config['datetime_format_date']['MM-DD-YYYY']->format	= 'm-d-Y';

$config['datetime_format_date']['MM/DD/YY']				= new stdClass();
$config['datetime_format_date']['MM/DD/YY']->slug		= 'MM/DD/YY';
$config['datetime_format_date']['MM/DD/YY']->label		= 'MM/DD/YY';
$config['datetime_format_date']['MM/DD/YY']->format		= 'm/d/y';

$config['datetime_format_date']['MM-DD-YY']				= new stdClass();
$config['datetime_format_date']['MM-DD-YY']->slug		= 'MM-DD-YY';
$config['datetime_format_date']['MM-DD-YY']->label		= 'MM-DD-YY';
$config['datetime_format_date']['MM-DD-YY']->format		= 'm-d-y';


$config['datetime_format_time']			= array();
$config['datetime_format_time_default']	= '24H';

$config['datetime_format_time']['24H']					= new stdClass();
$config['datetime_format_time']['24H']->slug			= '24H';
$config['datetime_format_time']['24H']->label			= '24 Hour';
$config['datetime_format_time']['24H']->format			= 'H:i:s';

$config['datetime_format_time']['12H']					= new stdClass();
$config['datetime_format_time']['12H']->slug			= '12H';
$config['datetime_format_time']['12H']->label			= '12 Hour';
$config['datetime_format_time']['12H']->format			= 'g:i:s A';



/* End of file date_format.php */
/* Location: ./config/date_format.php */