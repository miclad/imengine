<?php

if(!$USER->isAuthorized())
	exit();

if(isset($_POST['product_id']) && is_numeric($_POST['product_id']) && intval($_POST['product_id']) > 0){
	
	$product_id = intval($_POST['product_id']);
	
	$check = $db->query('select * from catalog_favorites where product_id='.$product_id.' and user_id='.$USER->getId())->getNext();
	if(!$check){
		
		$db->query('insert into catalog_favorites set product_id='.$product_id.',user_id='.$USER->getID());
		
	}
	
}

$cnt = $db->query('select count(*) as cnt from catalog_favorites where user_id='.$USER->getId())->getNext();

echo $cnt['cnt'];
exit();