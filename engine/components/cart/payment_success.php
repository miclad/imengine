<?

$order_data = false;

if(isset($_GET['orderNumber'])){
	$order_data = $db->query('select * from orders where id='.intval($_GET['orderNumber']))->getNext();
}

if(!$order_data || !$order_data['payed']){
	$this->setActionTemplate('payment_fail');
}

?>