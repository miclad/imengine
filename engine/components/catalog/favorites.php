<?php

$PAGE->addItemToPath('Избранное');

$count = $db->query('select * from catalog_favorites where user_id='.$USER->getId().' limit 1')->getNext();

$content = '';
if($count){
	echo Page::includeComponent('catalog:items', array_merge($this->getConfigs(), array('favorites' => 1, 'with_filter' => true)));
}else{
	echo $this->getConfig('favorites_empty_text');
}