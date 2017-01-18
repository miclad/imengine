<?php

if(!defined('IN_CONTEXT')){
	define('NO_CONTENT_TYPE_HEADER', true);
	require '../../conf.php';
}

define('PASS', DBCommon::getConfig('qiwi_password'));

/**
 * На этот скрипт приходят уведомления от QIWI Кошелька.
 * SoapServer парсит входящий SOAP-запрос, извлекает значения тегов login, password, txn, status,
 * помещает их в объект класса Param и вызывает функцию updateBill объекта класса TestServer.
 *
 * Логика обработки магазином уведомления должна быть в updateBill.
 */

 $s = new SoapServer('IShopClientWS.wsdl', array('classmap' => array('tns:updateBill' => 'Param', 'tns:updateBillResponse' => 'Response')));
// $s = new SoapServer('IShopClientWS.wsdl');
 $s->setClass('TestServer');
 $s->handle();

 class Response {
  public $updateBillResult;
 }

 class Param {
  public $login;
  public $password;
  public $txn;      
  public $status;
 }

 class TestServer {
  function updateBill($param) {
  	
  	global $db;
  	
  	$resp = 0;
	
	// проверить password, login
	
	if($param->password != strtoupper(md5($param->txn.strtoupper(md5(PASS))))){
		$resp = 150;
	}else{
		
		$order_data = $db->query('select * from orders where id='.intval($param->txn))->getNext();
		if($order_data){
			
			$upd = array();
	
			// В зависимости от статуса счета $param->status меняем статус заказа в магазине
			if ($param->status == 60) {

				$upd[] = 'payed=1';
				
			} else{
				
				$upd[] = 'payed=0';
				
				if ($param->status > 100) {
					
					// заказ не оплачен (отменен пользователем, недостаточно средств на балансе и т.п.)
					
				} else if ($param->status >= 50 && $param->status < 60) {
					
					// счет в процессе проведения
					
				} else {
					
					// неизвестный статус заказа
					
				}
			
			}
			
			if(count($upd)){
				$db->query('update orders set '.implode(',', $upd).' where id='.$order_data['id']);
			}
		
		}else{
			
			$resp = 1;
			
		}
	
	}

	$temp = new Response();
	$temp->updateBillResult = $resp;
	return $temp;
	
  }
 }
?>
