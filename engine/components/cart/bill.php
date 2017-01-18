<?

$order_data = false;

$order_id = isset($_SESSION['order_sent']) ? $_SESSION['order_sent'] : 0;

if($order_id){
	$order_data = $db->query('select * from orders where id='.intval($order_id))->getNext();
}

if(!$order_data){
	throw new Exception('Заказ не найден');
}

$items = Orders::getItems($order_data['id']);
$PAGE->setAttribute('no_design', true);

?>