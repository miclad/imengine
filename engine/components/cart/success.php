<?

$PAGE->setAttribute('hide_title', true);
$isok = false;
$order_data = false;

if(isset($_SESSION['order_sent']) && $_SESSION['order_sent']){
	$isok = true;
	$payment = false;
	$order_data = $db->query('select * from orders where id='.intval($_SESSION['order_sent']))->getNext();
	if($order_data['payment']){
		$payment = $db->query('select * from payment_types where id='.$order_data['payment'])->getNext();
	}
	$delivery_type = Delivery::parseDeliveryType($order_data['delivery_type'], 'array');
}

if(!$order_data){
	throw new Exception('Упс, заказ не найден!');
}

if(!$isok){
	throw new PageNotFoundException();
}

$order_data['address'] = Delivery::buildAddressString($order_data);

$PAGE->clearPath();
$PAGE->addItemToPath('Подтверждение');

$products = Orders::getItems($order_data['id']);

$PAGE->setAttribute('sidebar', Page::includeComponent('cart:navbar'));

?>