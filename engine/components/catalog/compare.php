<?php

$PAGE->addItemToPath('Сравнение товаров');

$ids = Catalog::getCompare();
$items = array();
$properties = array();

if(count($ids)){
	
	$ob = array();
	$ar = array_reverse($ids);
	foreach($ar as $v){
		$ob[] = 'T.id != '.$v;
	}
	$order_by = implode(',', $ob);

	$items = Catalog::getElements(array('id' => $ids), '', $order_by)->fetchArray();
	
	if(count($items)){
		
		foreach($items as $k => $v){
			
			if(!count($properties)){
				$properties = Catalog::getSectionProperties($v['folder_id']);
			}
			
			$items[$k]['_properties'] = Catalog::getElementProperties($v['id'], true, true, true);
			
		}
		
	}

}