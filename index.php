<?

//Header('Location: /engine/admin/');

require 'engine/conf/init.php';

try{
	
	$REGION = Region::getInstance();
	if(isset($_GET['change_region'])){
		Region::getInstance()->changeRegion(urldecode($_GET['change_region']));
	}
	
	$USER = AuthUser::getInstance();
	if(isset($_GET['logout'])){
		Auth::logout();
		header('Location: '.Common::editUrl(array('logout'), array()));
		exit();
	}
	
	$PAGE = Page::getInstance();
	if(!$PAGE->getId())
		throw new PageNotFoundException();
	
	if($PAGE->getField('hard_link')){
		Header('Location: '.$PAGE->getField('hard_link'), true, 301);
		exit();
	}
	
	$cu = $USER->getCartUserId();
	if(!$cu && isset($_SESSION['cart_user_id']))
		$cu = $_SESSION['cart_user_id'];
	$cart_o = new Cart($cu);
		
	$PAGE->setChildren(array()); // очищаем подменю для генерации из компонентов
	
	/* Fetch Page Content */
	foreach($PAGE->getComponents() as $v){
		$PAGE->addContent(Page::includeComponent($v['component'], $v['params'], array(), '', '', false));
	}
	
	if(!Site::isAjaxRequest() && !Site::isApp() && !$PAGE->getAttribute('no_design'))
		require TPL_DIR.$PAGE->getLayout();
	elseif(Site::isApp() || Site::isAjaxRequest() && isset($_REQUEST['_return_json'])){

		echo json_encode(
				array(
					'success' => SiteNotices::getList() ? SiteNotices::getList() : null,
					'error' => SiteNotices::getList('error') ? SiteNotices::getList('error') : null
				)
		);

	}else
		echo $PAGE->getContent();
	
}catch(PageNotFoundException $e){
	header('HTTP/1.1 404 Not Found');
	echo '404. Page not found.';
	exit();
}catch(Exception $e){
	if(Site::isAjaxRequest()){
		echo json_encode(
			array(
				'success' => null,
				'error' => $e->getMessage()
			)
		);
	}else{
		$PAGE->clearContent();
		$PAGE->addContent($e->getMessage());
		require TPL_DIR.'layout.php';
	}
}

// Comment for git new up

// hehe

// Updated in clone v 2.0

// up in clone one more

?>