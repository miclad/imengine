<?

class NeedAuthException extends Exception{
	
	function __construct($message = '', $code = 0){
		$message = $message ? $message : 'Для просмотра этой страницы необходима авторизация';
		if(Site::isApp()){
			
			header('WWW-Authenticate: Basic realm="Fixam App"');
			header('HTTP/1.1 401 Unauthorized');
			exit();
			
		}elseif(!Site::isAjaxRequest()){
			if(!AuthUser::getInstance()->isAuthorized()){
				Header('Location: /login/');
				exit();
			}
			parent::__construct($message);
		}else{
			echo $message;
			exit();
		}
	}
	
}


?>