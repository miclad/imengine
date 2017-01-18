<?

class PageNotFoundException extends Exception{
	
	function __construct($message = '', $code = 0){
		parent::__construct($message ? $message : 'Страница не найдена');
	}
	
}


?>