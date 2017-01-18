<?

if(isset($_POST['InvId'])){
	
	$order_data = $db->query('select * from orders where id='.intval($_POST['InvId']))->getNext();
	if($order_data){
		
		$login = DBCommon::getConfig('robokassa_login');	
		$password = DBCommon::getConfig('robokassa_password1');
		$password2 = DBCommon::getConfig('robokassa_password2');
		
		$sign = strtoupper(md5(number_format($order_data['sum_discount'], 2, null, '').':'.$order_data['id'].':'.$password2));
		
		if($sign == $_POST['SignatureValue']){
			echo 'OK'.$order_data['id'];
			$db->query('update orders set payed='.number_format($_POST['OutSum']).' where id='.$order_data['id']);
		}
		
	}	
	
}

exit();

?>