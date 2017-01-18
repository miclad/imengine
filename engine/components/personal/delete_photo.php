<?

if(!$USER->isAuthorized())
	throw new NeedAuthException();

if($USER->getField('orig_ext')){
	
	$dt_ob = new DataTable('users');
	$dt_ob->setPostArray(array('pict' => array('delete' => 1), 'id' => $USER->getId(), 'login' => $USER->getField('login')));
	$dt_ob->updateRow();
	
	SiteNotices::addNotice('Фотография удалена');
	
}

Header('Location: /personal/profile/');
exit();

?>