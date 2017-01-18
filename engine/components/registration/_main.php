<?

$page_errors = array();

if(isset($_GET['success'])){
	
	$this->setActionTemplate('success');
	
}elseif(isset($_GET['activate']) && is_numeric($_GET['activate']) && $_GET['activate'] > 0 && isset($_GET['s'])){
	
	$res = $db->query('select * from users where id='.$_GET['activate'].' and activation_string="'.mysql_escape_string($_GET['s']).'" and activated=0');
	if($user_data = $res->getNext()){
		$db->query('update users set activation_string="", activated = 1');
		Auth::login($user_data['login'], '', true);
		Header('Location: /personal/');
		exit();
	}	
	
}elseif(!$USER->isAuthorized()){
	
	$GLOBALS["error_codes"][9] = 'Пользователь с указанным телефоном уже зарегистрирован на сайте';
	
	if(isset($_POST['auto'])){
		$max_id = $db->query('select max(id) as mid from users')->getNext();
		$new_id = $max_id['mid']+1;
		$login = 'guest'.$new_id;
		$password = Site::generatePassword();
		$dt_ob = new DataTable('users');
		$dt_ob->setPostArray(array('level' => 'user', 'login' => $login, 'password' => $password, 'is_tmp' => 1, 'activated_tsmp' => date('Y.m.d H:i:s'), 'added_tsmp' => date('Y.m.d H:i:s')));
		$iid = $dt_ob->insertRow();
		if($iid){
			Auth::login($login, $password, false, true);
			if(!Site::isAjaxRequest()){
				Header('Location: '.$PAGE->getUrl().'/?success');				
			}else{
				header("Content-Type: text/xml; charset=utf-8");
				require 'templates/xml_ok.php';
			}
			exit();
		}
	}else{
	
		$GLOBALS['entities']['users'][1]['email'][2] = 1;
		$GLOBALS['entities']['users'][1]['login'][1][1] = 'mail';
		
		$dt_ob = new DataTable('users');
		
		$dt_ob->fields['login'][0] = 'E-mail';
	
		if(count($_POST)){
			
			$dt_ob->setRowValue('group_id', 0);			
			
			$captcha_error = false;
			if(!isset($_POST['captcha_word']) || !isset($_SESSION['control_word']) || $_SESSION['control_word'] == '' || md5($_POST['captcha_word']) != $_SESSION['control_word']){
				$captcha_error = true;
			}
			
			//$dt_ob->setRowValue('login', CheckData::trimPhone($dt_ob->getRowValue('login')));
			
			
			$res = 0;
			$chk = $dt_ob->checkData();
			if($chk == '' && !$captcha_error){
				$dt_ob->setRowValue('email', $dt_ob->getRowValue('login'));
				$res = $dt_ob->insertRow();
			}
			if($res){
				
				$mail_content = "Здравствуйте!\n\n";				
				$mail_content .= "Благодарим Вас за регистрацию на сайте \"".str_replace('www.','',$_SERVER['SERVER_NAME'])."\"\n\n";				
				$mail_content .= "Ваш логин: ".$dt_ob->getRowValue('login')."\n";
				$mail_content .= "Ваш пароль: ".$_POST['password']."\n\n";
				
				$mail_to = $_POST['login'];
				$mail_subject = 'Регистрация на сайте '.str_replace('www.','',$_SERVER['SERVER_NAME']);
				$mail_from = 'reg@'.str_replace('www.','',$_SERVER['SERVER_NAME']);
				Email::sendMail($mail_to, $mail_subject, $mail_content, $mail_from);
				Auth::login($dt_ob->getRowValue('login'), '', true);
				
				// регистрируем в базе подписчиков
				//$db->query('insert into mailing_list set name="'.mysql_escape_string($dt_ob->getRowValue('name')).'",email="'.mysql_escape_string($dt_ob->getRowValue('login')).'",event="news"');
				
				if(!Site::isAjaxRequest()){
					Header('Location: '.$PAGE->getUrl().'/?success');
					exit();
				}
			}else{
				$e = $dt_ob->renderErrors();
				if($e != '')
					$page_errors[] = $e;
				if($captcha_error)
					$page_errors[] = 'Введен неверный защитный код';
			}
		}
		
		if(Site::isAjaxRequest()){
			header("Content-Type: text/xml; charset=utf-8");
			if(isset($page_errors) && count($page_errors)){
				require 'templates/xml_errors.php';
			}else{
				$login = $_POST['login'];
				require 'templates/xml_ok.php';
			}
			exit();
		}else{
			foreach($page_errors as $v){
				//SiteNotices::addNotice($v);
			}
		}
		
	}

}else{
	
	$this->setActionTemplate('dummy');
	echo 'Вы уже авторизованы и можете <a href="/personal/">перейти в личный кабинет</a>.';
	
}

?>