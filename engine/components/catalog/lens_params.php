<?php

$eyes = array('left' => 'Левый глаз', 'right' => 'Правый глаз');

$options = array('color', 'lens_intensity', 'lens_d', 'lens_dia', 'lens_bc', 'lens_ax', 'lens_cyl');
$cond = array('1');
if(in_array($this->getInitParam('initial_param'), $options)){
	$cond = array($this->getInitParam('initial_param').'='.floatval($this->getInitParam('initial_value')));
}
$values = array();

$has_params = false;

$product = Catalog::getById($this->getConfig('product_id'), $this->getConfig('collective_buy') ? array('price_type' => intval(DBCommon::getConfig('collective_price_type'))) : null);
$is_first = true;

foreach($options as $k => $_opt){
	
	if($_opt == $this->getConfig('initial_param')){
		$is_first = false;
		continue;
	}
	
	if($_opt == 'color'){
		
		$values[$_opt] = $db->query('
			select 
				V.color,
				C.name as color_title,
				C.name as name,
				V.color as value,
				0 as _selected
			from 
				catalog_item_variants as V,
				catalog_colors as C
			where
				V.item_id = '.intval($this->getConfig('product_id')).'
				and
				V.color = C.id
				and
				'.(!$is_first ? implode(' and ', $cond) : '1').'
			group by
				V.color
			order by
				C.name
		')->fetchArray();	
		
	}else{
		
		$values[$_opt] = $db->query('
			select 
				'.$_opt.' as value,
				'.$_opt.' as name,
				0 as _selected
			from 
				catalog_item_variants
			where
				item_id = '.intval($this->getConfig('product_id')).'
				and
				'.$_opt.' is not null
				and
				'.(!$is_first ? implode(' and ', $cond) : '1').'
			group by
				'.$_opt.'
			order by
				'.$_opt.'
		')->fetchArray();		
	
	}
	
	if(count($values[$_opt])){
		
		$is_first = false;
		
		$found_selected = false;
		$selected = false;
		
		if(isset($_GET['selected'][$_opt])){
			foreach($values[$_opt] as $_k => $_val){
				if($_GET['selected'][$_opt] == $_val['value']){
					$found_selected = true;
					$selected = $_k;
				}
			}
		}
		
		if(!$found_selected){
			foreach($values[$_opt] as $_k => $_val){
				switch($_opt){
					case 'lens_d':
						if($_k == count($values[$_opt])-1 || $_val['value'] < 0 && $values[$_opt][$_k+1]['value'] >= 0){
							$selected = $_k;
							$found_selected = true;
						}
					break;
					case 'color':
						$selected = $_k;
						$found_selected = true;
					break;
					default:
						if($_k == count($values[$_opt])-1){
							$selected = $_k;
							$found_selected = true;
						}
					break;
				}
			}	
		}
		
		if($found_selected){
			$values[$_opt][$selected]['_selected'] = 1;
			$cond[] = $_opt.'='.$values[$_opt][$selected]['value'];
		}
		
	}
	
	if(count($values[$_opt])){
		$has_params = true;
	}
	
}

if($this->getInitParam('_return') == 'json'){
	
	if(!Site::isAjaxRequest())
		echo '<pre>'.print_r($values, true).'</pre>';
	else
		echo json_encode($values);
	exit();
}