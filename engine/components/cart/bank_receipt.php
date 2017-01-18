<?

$PAGE->setAttribute('no_design', true);

$isok = false;
$delivery = false;
$payment = false;

if(isset($_SESSION['order_sent'])){
	$isok = true;
	$order_data = $db->query('select * from orders where id='.intval($_SESSION['order_sent']))->getNext();
	if($order_data['payment']){
		$payment = $db->query('select * from payment_types where id='.$order_data['payment'])->getNext();
	}	
	if($order_data){
		$delivery = Delivery::parseDeliveryType($order_data['delivery_type']);
		$delivery = substr($delivery, 0, strpos($delivery, ','));
	}
}

?>