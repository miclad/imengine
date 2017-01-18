<?

if(!AuthUser::getInstance()->isAuthorized()){
	header('Location: /login/');
	exit();
}

$PAGE->addItemToPath('История заказов', '');
$PAGE->setAttribute('is_personal', true);
$PAGE->setAttribute('cur_personal_page', 'orders');

$cur_order = isset($_GET['id']) ? $_GET['id'] : 0;

/*
$regular = isset($_GET['regular']);
if(isset($_POST['set_regular']) && isset($_POST['id'])){
	$ord = $db->query('select * from orders where id='.intval($_POST['id']))->getNext();
	if($ord && $ord['user_id'] == $USER->getID()){
		Orders::saveAsRegular($_POST['id']);
	}
	Header('Location: '.$_SERVER['REQUEST_URI']);
	exit();
}
*/

$orders = Orders::getOrdersByUser($USER->getID());

$PAGE->setAttribute('sidebar', Page::includeComponent('personal:navbar'));
$PAGE->setAttribute('hide_title');

?>