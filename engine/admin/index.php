<?

require dirname(__FILE__).'/../conf/init.php';

$USER = AuthUser::getInstance();

if($USER->adminPanelAccess()){
	
	if(isset($_GET['logout'])){
		Auth::logout();
		Header('Location: /engine/admin/');
		exit();
	}
	
	$content = '';

	try{
		
		$module_name = isset($_GET['module']) && trim($_GET['module']) != '' ? $_GET['module'] : 'admin_start';
		$module_section = isset($_GET['section']) && trim($_GET['section']) != '' ? $_GET['section'] : '';
		
		$content = Modules::runAdmin($module_name, $module_section);
		
		if(!Site::isAjaxRequest())
			require ADMIN_TPL_DIR.'main.php';
		else 
			echo $content;
		
	}catch(Exception $e){
		if(!Site::isAjaxRequest()){
			SiteNotices::addNotice($e->getMessage());
			require ADMIN_TPL_DIR.'main.php';
		}else{
			echo $e->getMessage();
		}
	}

}else{
	
	// try to login
	
	$login_error = '';
	
	if(isset($_POST['do_login']) && isset($_POST['login']) && isset($_POST['password'])){
		
		$res = Auth::login($_POST['login'], $_POST['password']);
		if($res > 0){
			$redir_to = $_SERVER['REQUEST_URI'];
			Header('Location: '.$redir_to);
			exit();
		}elseif($res == 0){
			$login_error = 'Неверный логин/пароль';
		}else{
			$login_error = 'Аккаунт заблокирован из соображений безопасности. Свяжитесь с разработчиком';
		}
		
	}else{
		
		// save post data in session	
		if(count($_POST)){
			$_SESSION['_post_data'] = $_POST;
		}
		
	}
	
	require ADMIN_TPL_DIR.'login.php';
	
}

?>