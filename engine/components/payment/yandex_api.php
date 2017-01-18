<?

//echo 'testing';

$output = '';
$hash_str = '';
$hash_orig = '';

if(isset($_POST['action']) && isset($_POST['invoiceId'])){
	
	if($_POST['action'] == 'checkOrder'){
		if(!isset($_SESSION['yandexInvoiceId'])){
			$_SESSION['yandexInvoiceId'] = array();
		}
		$_SESSION['yandexInvoiceId']['invoice'.$_POST['invoiceId']] = $_POST['orderNumber'];
	}
	
	$response = array(
		'performedDatetime' => date_format(date_create(),DATE_ATOM),
		'code' => 0,
		'invoiceId' => $_POST['invoiceId'],
		'shopId' => $_POST['shopId']
	);
	
	$order_id = 0;
	
	if(isset($_SESSION['yandexInvoiceId']['invoice'.$_POST['invoiceId']])){
		$order_id = intval($_SESSION['yandexInvoiceId']['invoice'.$_POST['invoiceId']]);
	}
	
	if(!$order_id && isset($_POST['orderNumber'])){
		$order_id = intval($_POST['orderNumber']);
	}
	
	$order_data = $db->query('select * from orders where id='.$order_id)->getNext();
	
	$response_md5 = '';
	
	if($order_data){
		
		$order_data['sum'] = number_format($order_data['sum_discount'] + $order_data['delivery_price'] - $order_data['use_bonus'], 2, '.', '');
		
		$hash_params = array('action', 'orderSumAmount', 'orderSumCurrencyPaycash', 'orderSumBankPaycash', 'shopId', 'invoiceId', 'customerNumber','shopPassword');
		
		$hash_str = '';		
		foreach($hash_params as $v){
			$val = '';
			switch($v){
				case 'orderSumAmount':
					$val = $order_data['sum'];
				break;
				case 'shopPassword':
					$val = DBCommon::getConfig('yandex_shop_password');
				break;
				case 'shopId':
					$val = DBCommon::getConfig('yandex_shop_id');
				break;
				default:
					$val = isset($_POST[$v]) ? $_POST[$v] : '';
				break;
			}
			if($hash_str != ''){
				$hash_str .= ';';
			}
			$hash_str .= $val;	
		} 
		$hash_orig = $hash_str;
		$hash_str = strtoupper(md5($hash_str));
		
		if($hash_str != $_POST['md5']){
			$response['code'] = 1;
			$response['message'] = 'Неверный хеш';
		}else{
			
			switch($_POST['action']){			
				case 'checkOrder':
					
					if($order_data['sum'] != $_POST['orderSumAmount']){
						$response['code'] = 1;
						$response['message'] = 'Неверная сумма заказа';
					}
					
					if(intval($order_data['payed']) > 0){
						$response['code'] = 100;
						$response['message'] = 'Заказ уже оплачен';
					}				
					
				break;
				case 'paymentAviso':
				
					$db->query('update orders set payed=1'.(DBCommon::getConfig('payment_ok_status') ? ',status_payment='.intval(DBCommon::getConfig('payment_ok_status')) : '').' where id='.$order_data['id']);					
					
				break;			
			}
		
		}
		
	}else{
		$response['code'] = 1;
		$response['message'] = 'Заказ не найден';
	}
	
	ob_start();
	require 'templates/yandex_response.php';
	$output = ob_get_contents();
	ob_end_clean();
	
	echo $output;
	
}

if($_SERVER['REQUEST_METHOD'] == 'POST'){
	//file_put_contents(dirname(__FILE__).'/yandex_request_'.$_POST['action'].'.txt', $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']."\n\n".print_r($_POST, true));
	//file_put_contents(dirname(__FILE__).'/yandex_response_'.$_POST['action'].'.txt', $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']."\n\n".$hash_orig."\n\nmd5: ".$hash_str."\n\n".print_r($_POST, true).$output);
}
	
exit();