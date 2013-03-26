<?

// define site basics
defined('DEPARTMENT') ? null : define('DEPARTMENT', 'IT');

defined('SITE_NAME') ? null : define('SITE_NAME', 'Office of Communications');

defined('IT_PHONE') ? null : define('IT_PHONE', '(573)882-0348');

defined('POST_REVISION_COUNT') ? null : define('POST_REVISION_COUNT', 10);

defined('IT_EMAIL') ? null : define('IT_EMAIL', 'jenkinsac@health.missouri.edu');

defined('IT_CONTACT_NAME') ? null : define('IT_CONTACT_NAME', 'Adam Jenkins');

defined('DOMAIN') ? null : define('DOMAIN', 'http://medicine.missouri.edu');

defined('DOMAIN_SSL') ? null : define('DOMAIN_SSL', 'https://medicine.missouri.edu');

// define site directories
defined('DS') ? null : define('DS', DIRECTORY_SEPARATOR);

defined('PUBLIC_ROOT') ? null : define('PUBLIC_ROOT', __dir__ . '/../../');

defined('SITE_ROOT') ? null : define('SITE_ROOT', __dir__ . '/../');

defined('ADMIN_ROOT') ? null : define('ADMIN_ROOT', SITE_ROOT);

defined('LIB_PATH') ? null : define('LIB_PATH', SITE_ROOT.DS.'includes');

defined('CORE_PATH') ? null : define('CORE_PATH', LIB_PATH.DS.'classes');

defined('CMS_VERSION') ? null : define('CMS_VERSION', '1.3.4');

// load config
require_once(LIB_PATH.DS.'config.php');

// load functions
require_once(CORE_PATH.DS.'functions.php');

// load core objects
require_once(CORE_PATH.DS.'Database.class.php');
require_once(CORE_PATH.DS.'QueryBuilder.class.php');
require_once(CORE_PATH.DS.'Session.class.php');
require_once(CORE_PATH.DS.'Department.class.php');
require_once(CORE_PATH.DS.'SaltPassword.class.php');
require_once(CORE_PATH.DS.'Group.class.php');
require_once(CORE_PATH.DS.'User.class.php');
require_once(CORE_PATH.DS.'Content.class.php');
require_once(CORE_PATH.DS.'EmailNotification.class.php');
require_once(CORE_PATH.DS.'Pagination.class.php');
require_once(CORE_PATH.DS.'Diff.php');
require_once(CORE_PATH.DS.'Diff'.DS.'Renderer'.DS.'Html'.DS.'SideBySide.php');


// load site specific objects

// upload id
$up_id = uniqid();