<?

if(isset($_POST['orderId'])){
	
	$order_data = $db->query('select * from orders where id='.intval($_POST['orderId']))->getNext();
	if($order_data){
		
		$hash_array = array(
			'eshopId',
			'orderId',
			'serviceName',
			'eshopAccount',
			'recipientAmount',
			'recipientCurrency',
			'paymentStatus',
			'userName',
			'userEmail',
			'paymentData'
		);
		
		$hash_str = '';
		foreach($hash_array as $v){
			$hash_str .= (isset($_POST[$v]) ? $_POST[$v] : '').'::';
		}
		$hash_str .= DBCommon::getConfig('rbk_secretKey');
		
		if(isset($_POST['hash']) && $_POST['hash'] == md5($hash_str)){
			echo 'OK';
			$db->query('update orders set payed='.number_format($_POST['recipientAmount']).' where id='.$order_data['id']);
		}
		
	}	
	
}

exit();

?>