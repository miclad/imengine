<?

if(!AuthUser::getInstance()->isAuthorized()){
	header('Location: /login/');
	exit();
}

$GLOBALS['entities']['users'][1]['email'][2] = 0;
$GLOBALS['entities']['users'][1]['phone'][2] = 1;

if($USER->isAdmin()){
	$GLOBALS['entities']['users'][1]['phone'][1][1] = 'text';
}

if($this->getConfig('change_password')){
	foreach($GLOBALS['entities']['users'][1] as $k => $v){
		if($k != 'password')
			$GLOBALS['entities']['users'][1][$k][2] = 0;
	}
}
	
$PAGE->addItemToPath('Профиль', '');

$dt_ob = new DataTable('users');
$dt_ob->initCurRow('id='.$USER->getId());

if(count($_POST) > 0 && isset($_POST['_save'])){
	
	$login_is_phone = true;//preg_match('/^[0-9]{10}$/', $USER->getField('login'));
	
	$phone = CheckData::trimPhone($dt_ob->getRowValue('phone'));
	if($phone != '')
		$dt_ob->setRowValue('phone', $phone);	

	$dt_ob->setRowValue('login', $USER->getField('login'));
	$dt_ob->setRowValue('id', $USER->getId());
	$dt_ob->setRowValue('group_id', $USER->getField('group_id'));
	$dt_ob->setRowValue('is_admin', $USER->getField('is_admin'));
	
	// check unique phone
	
	$res = $db->query('select * from users where login="'.mysql_escape_string($dt_ob->getRowValue('email')).'" and id != '.$USER->getId());
	if($res->selectedRowsCount()){
		
		SiteNotices::addNotice('Пользователь с указанным электронным адресом уже зарегистрирован');
		
	}else{
		
		if($phone){
			$dt_ob->setRowValue('login', $phone);
		}	   
	
		/* set birthdate */
		$bd = '';
		if(isset($_POST['birthdate']['day']) && $_POST['birthdate']['day'] && isset($_POST['birthdate']['month']) && $_POST['birthdate']['month'] && isset($_POST['birthdate']['year']) && $_POST['birthdate']['year']){
			$bd = intval($_POST['birthdate']['year']).'-'.intval($_POST['birthdate']['month']).'-'.intval($_POST['birthdate']['day']);
		}

		if(!$USER->getField('birthdate') && $bd){
			$dt_ob->setRowValue('birthdate', $bd);		
		}else{
			$dt_ob->setRowValue('birthdate', $USER->getField('birthdate'));
		}

		$res = $dt_ob->updateRow();
		
		if($res){
			if($this->getConfig('change_password'))
				SiteNotices::addNotice('Пароль изменен');
			else
				SiteNotices::addNotice('Изменения сохранены');
			Header('Location: '.$_SERVER['REQUEST_URI']);
			exit(0);
		}else{
			$e = $dt_ob->renderErrors();
			if($e != '')
				SiteNotices::addNotice($e);
		}
		
	}
}

$PAGE->setAttribute('sidebar', Page::includeComponent('personal:navbar'));
$PAGE->setAttribute('hide_title');

?>