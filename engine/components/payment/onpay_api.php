<?

if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['pay_for'])){
	
	$order_data = $db->query('select * from orders where id='.intval($_POST['pay_for']))->getNext();
	$code = 0;
	$response_md5 = '';
	
	if($order_data){
		
		if(false && $order_data['payed'] == '1'){
			$code = 2;
		}else{
			
			switch($_POST['type']){			
				case 'check':
					
					// type;pay_for;order_amount;order_ticker;secret_key_for_api_in
					
					$md5 = strtoupper(md5($_POST['type'].';'.$_POST['pay_for'].';'.$_POST['order_amount'].';'.$_POST['order_currency'].';'.DBCommon::getConfig('onpay_secret')));
					if($md5 != $_POST['md5']){
						$code = 7;
					}else{

						// type;pay_for;order_amount;order_ticker;code;secret_key_api_in
						$response_md5 = md5('check;'.$order_data['id'].';'.$_POST['order_amount'].';'.$_POST['order_currency'].';'.$code.';'.DBCommon::getConfig('onpay_secret'));
						
					}					
					
				break;
				case 'pay':
					
					// type;pay_for;onpay_id;order_amount;order_ticker;secret_key_for_api_in
					
					$md5 = strtoupper(md5($_POST['type'].';'.$_POST['pay_for'].';'.$_POST['onpay_id'].';'.$_POST['order_amount'].';'.$_POST['order_currency'].';'.DBCommon::getConfig('onpay_secret')));
					if($md5 != $_POST['md5']){
						$code = 7;
					}else{
						
						$db->query('update orders set payed=1'.(DBCommon::getConfig('payment_ok_status') ? ',status='.intval(DBCommon::getConfig('payment_ok_status')) : '').' where id='.$order_data['id']);
						
						// type;pay_for;onpay_id;order_id;order_amount;order_currency;code;secret_key_api_in
						$response_md5 = md5('pay;'.$order_data['id'].';'.$_POST['onpay_id'].';'.$order_data['id'].';'.$_POST['order_amount'].';'.$_POST['order_currency'].';'.$code.';'.DBCommon::getConfig('onpay_secret'));
						
					}
					
					
				break;			
			}
		
		}
		
	}
	
	ob_start();
	require 'templates/onpay_response.php';
	$response = ob_get_contents();
	ob_end_clean();
	
	echo $response;
	
}

if($_SERVER['REQUEST_METHOD'] == 'POST'){
	file_put_contents(B_DIR.'onpay_request_'.$_POST['type'].'.txt', print_r($_POST, true));
	file_put_contents(B_DIR.'onpay_response_'.$_POST['type'].'.txt', $response);
}
	
exit();

?>