<?php

class Mailings{
	
	public static function send($content, $subject, $to, $from = ''){
		
		if(!$from){
			$from = DBCommon::getConfig('mailings_from');
			if(!$from){
				$from = 'Fixam.ru <info@fixam.ru>';
			}
		}
		
		$tpl = COMPONENTS_DIR.'mailings/templates/main.php';
		ob_start();
		require $tpl;
		$message = ob_get_contents();
		ob_end_clean();
		
		Email::sendMail($to, $subject, $message, $from, '', '', 'text/html');

	}
	
	public static function sendRegistration($data){
		
		if(!is_array($data)){
			$data = array(
				'login' => $data->getRowValue('login'),
				'email' => $data->getRowValue('email'),
				'password' => $data->getRowValue('password')
			);
		}
		
		$text = 'Благодарим вас за регистрацию на сайте <a href="http://'.$_SERVER['SERVER_NAME'].'">FIXAM.RU</a>!<br><br>';
		$text .= 'Ваш логин: '.$data['login'].'<br>';
		$text .= 'Пароль: '.$data['password'];
		
		self::send($text, 'Регистрация на fixam.ru', $data['email']);
		
	}
	
	public static function sendNewRequest($data){
		
		global $db;
		
		$users = Requests::getServicesByRequest($data);
		
		$text .= 'Создана новая заявка.<br><br>';
		$text .= 'Автомобиль: '.$data['car_title'].'<br>';
		$text .= 'Вид работ: ';
		switch($data['request_type']){
			case 'service':
				$text .= 'ТО - '.$data['service_mileage'];
			break;
			case 'repair':
				$text .= 'ремонт';
			break;
			case 'bodywork':
				$text .= 'кузовные работы';
			break;
		}
		$text .= '<br><br>';
		
		$link = 'http://'.$_SERVER['SERVER_NAME'].'/requests/#/requests/details/'.$data['id'].'/';
		
		$text .= 'Для ответа у вас есть 24 часа с момента поступления заявки.<br><br>';
		$text .= '<a href="'.$link.'">Перейти к заявке</a>';
		
		foreach($users as $v){
			
			if($v['only_client_id'] && $v['only_client_id'] != $data['created_by']){
				continue;
			}
			
			$db->query('update users set requests_received = requests_received+1 where id='.$v['id']);
			
			if($v['notify_new_requests'] == 1){
				self::send($text, 'Новая заявка', $v['email_notify'] ? str_replace('|', ',', $v['email_notify']) : $v['email']);
			}
			
		}
		
	}
	
	public static function sendServiceChosen(&$request_data, &$offer_data){
		
		global $db;
		
		$service_data = $db->query('select * from users where id='.$offer_data['service_id'])->getNext();
		if($service_data['notify_change_status']){
			
			$subject = 'Ваше предложение подтверждено';
			
			$text = 'Клиент выбрал ваше предложение на заявку.<br><br>';
			$text .= 'Номер заявки: '.$request_data['id'].'<br>';
			$text .= 'Автомобиль: '.$request_data['car_title'].'<br>';
			$text .= 'Вид работ: ';
			switch($request_data['request_type']){
				case 'service':
					$text .= 'ТО - '.$request_data['service_mileage'];
				break;
				case 'repair':
					$text .= 'ремонт';
				break;
				case 'bodywork':
					$text .= 'кузовные работы';
				break;
			}
			$text .= '<br><br>';

			$link = 'http://'.$_SERVER['SERVER_NAME'].'/requests/list/accepted/#/requests/details/'.$request_data['id'].'/';
			$text .= '<a href="'.$link.'">Перейти к заявке</a><br><br>';
			
			$text .= 'Свяжитесь с клиентом и согласуйте время визита.';
			
			self::send($text, $subject, $service_data['email_notify_status_change'] ? str_replace('|', ',', $service_data['email_notify_status_change']) : $service_data['email']);
			
		}
		
	}
	
	public static function sendCallRequest(&$request_data, &$offer_data){
		
		global $db;
		
		$service_data = $db->query('select * from users where id='.$offer_data['service_id'])->getNext();
		if(true || $service_data['notify_call_request']){
			
			$subject = 'Запрос звонка';
			
			$text = 'Клиент просит вас перезвонить.<br><br>';
			$text .= 'Номер заявки: '.$request_data['id'].'<br>';
			$text .= 'Автомобиль: '.$request_data['car_title'].'<br>';
			$text .= 'Вид работ: ';
			switch($request_data['request_type']){
				case 'service':
					$text .= 'ТО - '.$request_data['service_mileage'];
				break;
				case 'repair':
					$text .= 'ремонт';
				break;
				case 'bodywork':
					$text .= 'кузовные работы';
				break;
			}
			$text .= '<br><br>';

			$link = 'http://'.$_SERVER['SERVER_NAME'].'/requests/list/call_request/#/requests/details/'.$request_data['id'].'/';
			$text .= '<a href="'.$link.'">Перейти к заявке</a><br><br>';
			
			self::send($text, $subject, $service_data['email_notify_status_change'] ? str_replace('|', ',', $service_data['email_notify_status_change']) : $service_data['email']);
			
		}
		
	}
	
	public static function sendSchedule(&$data){
		
		global $db;
		
		$user_data = $db->query('select * from users where id='.$data['created_by'])->getNext();
		if($user_data['notify_in_calendar']){
			
			$subject = 'Запись';
			
			$text = 'Сервис назначил вам время визита:<br>';
			$text .= 'дата: '.Date::onlyDate($data['in_calendar']).'<br>';
			$text .= 'время: '.date_format(Date::createDTObject($data['in_calendar']), 'H:i').'<br><br>';
			
			$text .= '<b>Заявка:</b><br>';
			$text .= 'Автомобиль: '.$data['car_title'].'<br>';
			$text .= 'Вид работ: ';
			switch($data['request_type']){
				case 'service':
					$text .= 'ТО - '.$data['service_mileage'];
					$subject .= ' на ТО';
				break;
				case 'repair':
					$text .= 'ремонт';
					$subject .= ' на ремонт';
				break;
				case 'bodywork':
					$text .= 'кузовные работы';
					$subject .= ' на кузовные работы';
				break;
			}
			$text .= '<br><br>';

			$link = 'http://'.$_SERVER['SERVER_NAME'].'/requests/#/requests/details/'.$data['id'].'/';
			$text .= '<a href="'.$link.'">Перейти к заявке</a>';
			
			self::send($text, $subject, $user_data['email_notify'] ? $user_data['email_notify'] : $user_data['email']);
			
		}
		
	}
	
	public static function sendNewOffer(&$request_data, &$offer_data){
		
		global $db;
		
		$user_data = $db->query('select * from users where id='.$request_data['created_by'])->getNext();
		if($user_data['notify_new_offers']){
			
			$subject = 'Новое предложение';
			
			$text = 'На вашу заявку поступило новое предложение.<br><br>';
			$text .= 'Заявка: '.$request_data['car_title'].' (';
			switch($request_data['request_type']){
				case 'service':
					$text .= 'ТО - '.$request_data['service_mileage'];
				break;
				case 'repair':
					$text .= 'ремонт';
				break;
				case 'bodywork':
					$text .= 'кузовные работы';
				break;
			}
			$text .= ')<br><br>';
			
			$service_data = $db->query('select * from users where id='.$offer_data['service_id'])->getNext();
			if($service_data){
				$text .= 'Сервис: '.$service_data['company'].'<br>';
				$text .= 'Стоимость работ: '.Site::formatPrice($offer_data['price_work']).' руб.<br>';
				if($offer_data['price_parts_orig']){
					$text .= 'Стоимость оригинальных запчастей: '.Site::formatPrice($offer_data['price_parts_orig']).' руб.<br>';
				}
				if($offer_data['price_parts_analog']){
					$text .= 'Стоимость аналогов: '.Site::formatPrice($offer_data['price_parts_analog']).' руб.<br>';
				}
			}

			$link = 'http://'.$_SERVER['SERVER_NAME'].'/requests/#/requests/details/'.$request_data['id'].'/';
			$text .= '<br><a href="'.$link.'">Перейти к заявке</a>';		
			
			self::send($text, $subject, $user_data['email_notify'] ? $user_data['email_notify'] : $user_data['email']);
			
		}
		
		
	}
	
	
}