<?php

/*
 minden hetkoznap reggel 8-kor fusson le, pl

0 8 * * 1-5 php /var/www/jorani/cron.php
*/

$isCLI = (php_sapi_name() == 'cli');

if (!$isCLI)
    exit(); // csak console-bol futunk


// ======================
// load jorani framework
// ======================

define('ENVIRONMENT', isset($_SERVER['CI_ENV']) ? $_SERVER['CI_ENV'] : 'development');

$system_path = 'system/';
$application_folder = 'application';


// The name of THIS file
define('SELF', pathinfo(__FILE__, PATHINFO_BASENAME));

// Path to the system directory
define('BASEPATH', $system_path);

// Path to the front controller (this file) directory
define('FCPATH', dirname(__FILE__).DIRECTORY_SEPARATOR);

// Name of the "system" directory
define('SYSDIR', basename(BASEPATH));

define('APPPATH', $application_folder.DIRECTORY_SEPARATOR);

$view_folder = APPPATH.'views';
define('VIEWPATH', $view_folder.DIRECTORY_SEPARATOR);

$GLOBALS['versionOfJorani'] = '1.0.0';

require_once 'vendor/autoload.php';
//require_once BASEPATH . 'core/CodeIgniter.php';

if (file_exists(APPPATH.'config/'.ENVIRONMENT.'/constants.php'))
{
    require_once(APPPATH.'config/'.ENVIRONMENT.'/constants.php');
}

if (file_exists(APPPATH.'config/constants.php'))
{
    require_once(APPPATH.'config/constants.php');
}


require_once(BASEPATH.'core/Common.php');

$charset = strtoupper(config_item('charset'));
ini_set('default_charset', $charset);

if (extension_loaded('mbstring'))
{
    define('MB_ENABLED', TRUE);
    // mbstring.internal_encoding is deprecated starting with PHP 5.6
    // and it's usage triggers E_DEPRECATED messages.
    @ini_set('mbstring.internal_encoding', $charset);
    // This is required for mb_convert_encoding() to strip invalid characters.
    // That's utilized by CI_Utf8, but it's also done for consistency with iconv.
    mb_substitute_character('none');
}
else
{
    define('MB_ENABLED', FALSE);
}

// There's an ICONV_IMPL constant, but the PHP manual says that using
// iconv's predefined constants is "strongly discouraged".
if (extension_loaded('iconv'))
{
    define('ICONV_ENABLED', TRUE);
    // iconv.internal_encoding is deprecated starting with PHP 5.6
    // and it's usage triggers E_DEPRECATED messages.
    @ini_set('iconv.internal_encoding', $charset);
}
else
{
    define('ICONV_ENABLED', FALSE);
}

require_once(BASEPATH.'core/compat/mbstring.php');
require_once(BASEPATH.'core/compat/hash.php');
require_once(BASEPATH.'core/compat/password.php');
require_once(BASEPATH.'core/compat/standard.php');

$UNI =& load_class('Utf8', 'core');
$CFG =& load_class('Config', 'core');
$IN	=& load_class('Input', 'core');
//$OUT =& load_class('Output', 'core');
$LANG =& load_class('Lang', 'core');


function &get_instance(){
    return CI_Controller::get_instance();
}

// Load the base controller class
require_once BASEPATH.'core/Controller.php';





// ============================================
// send leave reports to people who subscribed
// ============================================


require_once('application/views/calendar/week_report.php');
$a = new CronTest();
$a->report();
