<?

if(!AuthUser::getInstance()->adminPanelAccess())
	throw new NeedAuthException();

Header('Location: /engine/admin/index.php?module=datatable&entity=structure');
	
$articles_num = $db->query('select count(*) as cnt from articles where 1')->getNext();
$users_num = $db->query('select count(*) as cnt from users where 1')->getNext();

$menu = MenuTree::getInstance()->getItems('');
require dirname(__FILE__).'/../templates/main.php';

?>