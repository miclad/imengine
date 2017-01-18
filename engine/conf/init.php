<?

$session_name = session_name();
if(isset($_POST[$session_name])){
	session_id($_POST[$session_name]);
}

session_start();

if(!defined('NO_CONTENT_TYPE_HEADER'))
	Header('Content-type: text/html; charset=utf-8');
	
define('IN_CONTEXT',1);
define('B_DIR', realpath(dirname(__FILE__).'/../../').'/');
define('ENGINE_DIR', B_DIR.'engine/');
define('LIB_DIR', ENGINE_DIR.'lib/');
define('MODULES_DIR', ENGINE_DIR.'modules/');
define('TPL_DIR', ENGINE_DIR.'templates/public/');
define('ADMIN_TPL_DIR', ENGINE_DIR.'templates/admin/');
define('COMPONENTS_DIR', ENGINE_DIR.'components/');

require ENGINE_DIR.'conf/autoload.php';
require ENGINE_DIR.'conf/db_conf.php';
require ENGINE_DIR.'conf/fix_magic_quotes.php';
require ENGINE_DIR.'conf/events.php';
require LIB_DIR.'vars.php';
require LIB_DIR.'entities.php';
require LIB_DIR.'entinits.php';
require LIB_DIR.'error_codes.php';

$db = new DataBase(DB_HOST, DB_USER, DB_PASS, DB_NAME);

?>