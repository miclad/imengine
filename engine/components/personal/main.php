<?

if(AuthUser::getInstance()->isAuthorized()){
	header('Location: '.$PAGE->getUrl().'/profile/');
}else{
	header('Location: /login/');
}

exit();

?>