<?

$response = array('error' => '', 'result' => '');

if(true){

	$user_data = false;
	
	$q = '';
	if(false && isset($_POST['email']) && trim($_POST['email']) != ''){
		$q = 'login="'.mysql_escape_string(trim($_POST['email'])).'"';
	}elseif(isset($_POST['phone']) && $_POST['phone'] != ''){// && isset($_POST['secret']) && $_POST['secret'] != ''){
		$ph = CheckData::trimPhone($_POST['phone']);
		if($ph != ''){
			$q = 'login="'.mysql_escape_string($ph).'"';// and md5(concat(email,"_",id))="'.mysql_escape_string($_POST['secret']).'"';
		}
	}
	
	if(false && $q){
	
		$user_data = $db->query('select * from users where '.$q)->getNext();
	
		if($user_data){
				
				// меняем пароль
				$password = Site::generatePassword();
				$up = $db->query('update users set is_auto_pass=1,is_pass_restored=1,password="'.md5($password).'" where id='.$user_data['id']);
				if($up->affectedRowsCount()){
					
					if(true || !isset($_POST['method']) || $_POST['method'] == 'email'){
					
						if($user_data['email']){
							$mail_to = $user_data['email'];
							$mail_subject = 'Восстановление пароля на '.str_replace('www.', '', $_SERVER['HTTP_HOST']);
							$mail_content = "Здравствуйте".($user_data['name'] ? ', '.$user_data['name'] : '')."!\n\n";
							$mail_content .= "Ваш логин: ".$user_data['login']."\n";
							$mail_content .= "Пароль: ".$password;
							$mail_content .= "\n\nВы всегда можете изменить пароль в личном кабинете на сайте.";
							$mail_content .= "\n\n--\nС уважением,\nИнтернет-магазин ".str_replace('www.', '', $_SERVER['HTTP_HOST']);
							@Email::sendMail($mail_to, $mail_subject, $mail_content, str_replace('www.', '', $_SERVER['HTTP_HOST'])." <support@".str_replace('www.', '', $_SERVER['HTTP_HOST']).">");
							$response['result'] = 'Пароль выслан на ваш e-mail '.$user_data['email'];
						}else{
							$response['error'] = 'Не удалось отправить пароль на почту, т.к. в вашем профиле отсутствует контактный e-mail.';//, попробуйте получить пароль по смс';
						}
					
					}else{
						
						if($user_data['login'] && preg_match('/^[0-9]+$/', $user_data['login'])){
							
							$sms_text = 'Ваш новый пароль: '.$password;
							SMS::sendOne($user_data['login'], DBCommon::getConfig('sms_sender'), $sms_text);
							
							$response['result'] = 'Пароль выслан на ваш телефон +7'.$user_data['login'];
							
						}else{
							
							$response['error'] = 'Не удалось отправить пароль на телефон, попробуйте получить пароль по e-mail';
							
						}
						
					}
					
				}
				
		}else{
			$response['error'] = 'Пользователь не найден';
		}
		
		$q = '';
		
	}
	
	if($q != ''){
		$sent = false;
		$res = $db->query('select * from users where '.$q);
		$user_data = $res->getNext();
		if($user_data){

				$code = md5(Site::generatePassword(32));
				$res = $db->query('update users set pass_recovery_code="'.$code.'" where id='.$user_data['id']);
				if($res->affectedRowsCount()){
					$sent = true;
					$mail_to = true || $user_data['email'] ? $user_data['email'] : $user_data['login'];
					$mail_subject = 'Восстановление пароля';
					$mail_content = "Здравствуйте!\n\n";
					$mail_content .= "Для восстановления пароля в интернет-магазине ".str_replace('www.', '', $_SERVER['HTTP_HOST'])." перейдите по ссылке\n";
					$mail_content .= "http://".$_SERVER['HTTP_HOST'].$PAGE->getUrl()."/?a=change_pass&uid=".$user_data['id']."&c=".$code;
					$mail_content .= "\n\n--\nС уважением,\nИнтернет-магазин ".str_replace('www.', '', $_SERVER['HTTP_HOST']);
					Email::sendMail($mail_to, $mail_subject, $mail_content, 'EyesColor <support@eyescolor.ru>');
					
					$_SESSION['password_recovery_email'] = $user_data['email'];
					
				}
				

		}else{
			$response['error'] = 'Пользователь не найден';
		}
		
		if($sent){
			if(!Site::isAjaxRequest()){
				$_SESSION['password_recovery_email_sent'] = true;
				Header('Location: '.$PAGE->getUrl().'/?a=email_sent');
				exit();
			}else{
				$response['result'] = 'На ваш e-mail отправлено письмо с указаниями по восстановлению пароля.';
			}
		}
		
	}
	
}

if(Site::isAjaxRequest()){
	echo json_encode($response);
	exit();
}else{
	
	if($response['result'] && !$response['error']){
		Header('Location: '.$PAGE->getUrl().'/?a=email_sent');
		$this->setActionTemplate('dummy');
	}
	
	if($response['error']){
		//SiteNotices::addNotice($response['error']);
	}
		
}

?>