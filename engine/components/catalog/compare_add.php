<?php

if(!$USER->isAuthorized())
	exit();

if(isset($_POST['product_id']) && is_numeric($_POST['product_id']) && intval($_POST['product_id']) > 0){
	
	$product_id = intval($_POST['product_id']);
	
	$check = $db->query('select * from catalog_compare where product_id='.$product_id.' and user_id='.$USER->getId())->getNext();
	if(!$check){
		
		$db->query('insert into catalog_compare set product_id='.$product_id.',user_id='.$USER->getID());
		
	}
	
}

$cnt = $db->query('select count(*) as cnt from catalog_compare where user_id='.$USER->getId())->getNext();

echo $cnt['cnt'];
exit();