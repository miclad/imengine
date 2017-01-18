<?

$PAGE->clearContent();

$user_data = false;

if(isset($_GET['uid']) && is_numeric($_GET['uid']) && isset($_GET['c']) && trim($_GET['c']) != ''){
	$user_data = $db->query('select * from users where is_tmp=0 and id='.intval($_GET['uid']).' and pass_recovery_code is not null and pass_recovery_code != "" and pass_recovery_code="'.mysql_escape_string($_GET['c']).'"')->getNext();
}

if(!$user_data){
	SiteNotices::addNotice('Пользователь не найден');
}else{
	
	$form_desc = array(
		array('name' => 'password', 'datatype' => 'password', 'title' => 'Новый пароль', 'req' => 1)
	);
	
	$form = new UForm($form_desc, '', '', array(), '', false);
	
	if(isset($_POST) && count($_POST)){
		$form->read();
		if(!$form->haveErrors()){
			
			$db->query('update users set password="'.$form->getValue('password').'",pass_recovery_code="" where id='.$user_data['id']);
			Auth::login($user_data['login'], '', true, true);
			header('Location: '.$PAGE->getUrl().'/?a=success');
			$_SESSION['password_changed'];
			exit();			
			
		}else{
			//SiteNotices::addNotice($form->getErrors());
		}
	}
	
}

if(!isset($form)){
	$this->setActionTemplate('dummy');
}

?>