<?php

if(!AuthUser::getInstance()->isAuthorized())
	throw new NeedAuthException();

if(isset($_POST['folder_id'])){
	echo json_encode(Catalog::getElements(array('folder_id' => $_POST['folder_id']))->fetchArray());
}