<?

/* Include this at the bottom of your site init file */

// define site directories
defined('PUBLIC_ROOT') ? null : define('PUBLIC_ROOT', DS.'var'.DS.'www'.DS.'html'.DS.'medicine.missouri.edu');

defined('SITE_ROOT') ? null : define('SITE_ROOT', PUBLIC_ROOT.DS.SITE_ROOT_BASE);

defined('CORE_PATH') ? null : define('CORE_PATH', PUBLIC_ROOT.DS.'includes'.DS.'classes');

defined('LIB_PATH') ? null : define('LIB_PATH', SITE_ROOT.DS.'inc');


// load functions
require_once(CORE_PATH.DS.'functions.php');

// load core objects
require_once(CORE_PATH.DS.'Database.php');
require_once(CORE_PATH.DS.'QueryBuilder.php');
require_once(CORE_PATH.DS.'Session.php');
require_once(CORE_PATH.DS.'Department.php');
require_once(CORE_PATH.DS.'SaltPassword.php');
require_once(CORE_PATH.DS.'Group.php');
require_once(CORE_PATH.DS.'User.php');
require_once(CORE_PATH.DS.'Content.php');
require_once(CORE_PATH.DS.'Validation.php');
require_once(CORE_PATH.DS.'EmailNotification.php');
require_once(CORE_PATH.DS.'Uploader.php');
require_once(CORE_PATH.DS.'Emoticonize.php');

?>