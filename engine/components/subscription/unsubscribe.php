<?

$PAGE->addItemToPath('Отписка от рассылки', '');

if(isset($_GET['sent'])){
	echo '<div class="cnt_pad">На ваш адрес отправлено письмо со ссылкой, при переходе по которой ваш адрес будет удален из списка рассылки.</div>';
}elseif(isset($_GET['done'])){
	echo '<div class="cnt_pad">Ваш адрес удален из списка рассылки.</div>';
}else{
	
	if(isset($_GET['code']) && isset($_GET['id']) && trim($_GET['code']) != ''){
		
		$data = $db->query('select * from mailing_list where id='.intval($_GET['id']).' and unsubscribe_code is not null and unsubscribe_code="'.mysql_escape_string($_GET['code']).'"')->getNext();
		if($data){
			$db->query('delete from mailing_list where id='.intval($_GET['id']));
			if($USER->isAuthorized() && $USER->getField('login') == $data['email'])
				$db->query('update users set is_subscribed=0 where id='.$USER->getID());
			Header('Location: '.Common::editUrl(array('sent', 'id', 'code'), array('done' => 1)));
			exit();
		}else{
			echo '<div class="cnt_pad">Адрес не найден</div>';
		}
		
	}else{
		
		$form_desc = array(
			array('title' => 'Ваш e-mail', 'name' => 'email', 'datatype' => 'mail', 'req' => 1, 'error1' => 'Не указан e-mail')
		);
				
		$form = new UForm($form_desc, 'mailing_list', '', array('event' => 'news'), '', false);
				
		if(isset($_POST) && count($_POST)){
			$form->read();
			if(!$form->haveErrors()){
				
				$data = $db->query('select * from mailing_list where event="news" and email="'.mysql_escape_string($form->getValue('email')).'"')->getNext();
				if($data){
				
					$code = md5(Site::generatePassword());
					$db->query('update mailing_list set unsubscribe_code="'.mysql_escape_string($code).'" where id="'.$data['id'].'"');
					
					$mail_to = $data['email'];
					$mail_from = 'daStore.ru <'.DBCommon::getConfig('mailing_from').'>';
					$mail_subject = 'отписка от рассылки';
					
					ob_start();
					require 'templates/unsubscribe_email.php';
					$mail_content = ob_get_contents();
					ob_end_clean();
							
					Email::sendMail($mail_to, $mail_subject, $mail_content, $mail_from, '', '', 'text/html');
					
					Header('Location: '.Common::editUrl(array('sent'), array('sent' => 1)));
					exit();
				
				}else{
					SiteNotices::addNotice('Адрес не найден');
				}
				
			}else{
				SiteNotices::addNotice($form->getErrors());
			}
		}
				
		echo $form->printForm();
		
	}
		
}


?>