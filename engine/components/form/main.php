<?

$response = array('success' => null, 'error' => null);

if($this->getConfig('formid') != 0){
	
	$form_data = $db->query('select * from forms where id='.intval($this->getConfig('formid')))->getNext();
	if(!$form_data)
		return false;
	
	if(isset($_GET['success']) && isset($_SESSION['form_sent'])){
		
		$PAGE->clearContent();
		$PAGE->setTitle('Спасибо!');
		$this->setActionTemplate('success');
		
	}else{
		
		$res = $db->query('select * from forms_fields where form_id='.$this->getConfig('formid').' order by pos');
		$desc = $res->fetchArray();
		$form = new UForm($desc, '', '', array(), $form_data['success_text'], $form_data['use_captcha'], true);
		
		if(isset($_POST['_send'])){
			$form->read();
			if(!$form->haveErrors()){
				$res = $form->sendEmail(($this->getConfig('email') ? $this->getConfig('email').',' : '') . $form_data['email'], 'Сообщение с сайта - '.$form_data['name'], ($this->getConfig('append_data') ? "\n".$this->getConfig('append_data')."\n" : '')."\nОтправлено со страницы http://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);
				$form->sent = true;
				$response['success'] = $form_data['success_text'];
			}else{
				$response['error'] = $form->getErrors();
			}
			
			if(Site::isAjaxRequest()){
				echo json_encode($response);
				exit();
			}
			
		}
		
		if($form->sent){
			$_SESSION['form_sent'] = true;
			//SiteNotices::addNotice($form_data['success_text']);
			header('Location: '.$PAGE->getUrl().'/?success');
			exit();
		}
		
		echo $form->printForm($form_data['submit_text']);
		
	}
	
}
?>