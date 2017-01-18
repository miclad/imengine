<?

if(!$USER->isAuthorized())
	throw new NeedAuthException();
	
$PAGE->addItemToPath('Профиль', '');

$dt_ob = new DataTable('users');
$dt_ob->initCurRow('id='.$USER->getId());

if(count($_POST) > 0){

	$data = array(
		'id' => $USER->getId(),
		'is_auto_pass' => 0,
		'is_pass_restored' => 0
	);
	
	$res = $dt_ob->updateRow($data);
	if(!$res){
		if(!Site::isAjaxRequest()){
			SiteNotices::addNotice('Изменения сохранены');
			Header('Location: '.$_SERVER['REQUEST_URI']);
			exit(0);
		}
	}else{
		$e = $dt_ob->renderErrors();
		if($e != ''){
			if(Site::isAjaxRequest())
				echo $e;
			else
				SiteNotices::addNotice($e);
		}
	}
}

?>