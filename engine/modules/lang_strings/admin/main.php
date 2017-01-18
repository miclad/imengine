<?

if(!AuthUser::getInstance()->isAdmin())
	throw new NeedAuthException();
	
?>