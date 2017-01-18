<?
class DBEventHandlers{
	
	function onAfterUserUpdate(DataTable $dt_ob = null){
		
		if($dt_ob->getLastID() == AuthUser::getInstance()->getId()){
			if($dt_ob->getBeforeUpdateData('login') != $dt_ob->getRowValue('login') || isset($_POST['password']) && md5($_POST['password']) != AuthUser::getInstance()->getField('password')){
				Auth::logout();
				Auth::login($dt_ob->getRowValue('login'), '', true);
			}
		}	
		
	}
	
	function onBeforeUserSave(DataTable $dt_ob = null){
		
		if($dt_ob->getRowValue('service_type') == 'dealer'){
			$dt_ob->setRowValue('is_official_service', 1);
		}else{
			$dt_ob->setRowValue('is_official_service', 0);
		}
		
		$fields = array('email_notify', 'email_notify_status_change');
		
		foreach($fields as $v){
			if(is_array($dt_ob->getRowValue($v))){
				$src = $dt_ob->getRowValue($v);
				$vals = array();
				foreach($src as $item){
					if(!is_array($item) && $item != '')
						$vals[] = $item;
				}
				$dt_ob->setRowValue($v, implode('|', $vals));
			}
		}
		
	}
	
	function saveItemsPropertyValues(DataTable $dt_ob = null){		
		global $db;
		if(intval($dt_ob->getLastID()) && isset($_POST['properties']) && is_array($_POST['properties'])){
			foreach($_POST['properties'] as $k => $v){
				$res = $db->query('select id from items_property_values where item_id='.intval($dt_ob->getLastId()).' and property_id='.intval($k))->getNext();
				if($res){
					$GLOBALS['db']->query('update items_property_values set value="'.mysql_escape_string($v).'" where id='.$res['id']);
				}else{
					$GLOBALS['db']->query('insert into items_property_values set item_id='.intval($dt_ob->getLastID()).', property_id='.intval($k).', value="'.mysql_escape_string($v).'"');
				}
			}
		}
	}
	
	function prepareBannerData(DataTable $dt_ob){
		$show_case = $dt_ob->getRowValue('show_case');
		if(is_array($show_case)){
			
			/* categories */
			if(!isset($show_case['catalog']) && isset($show_case['only_sections'])){
				unset($show_case['only_sections']);				
			}
			if(!isset($show_case['only_sections']))
				$dt_ob->setRowValue('banner_sections', array());
				
			/* brands */	
			if(!isset($show_case['brands']) && isset($show_case['only_brands'])){
				unset($show_case['only_brands']);				
			}
			if(!isset($show_case['only_brands']))
				$dt_ob->setRowValue('banner_brands', array());
				
			if(!isset($show_case['selected_pages']))
				$dt_ob->setRowValue('banner_pages', array());
				
			$str = implode(' ', array_keys($show_case));
			if($str != ''){
				$str = ' '.$str.' ';
			}
			$dt_ob->setRowValue('show_case', $str);
		}else{
			$dt_ob->setRowValue('show_case', '');
			$dt_ob->setRowValue('banner_sections', array());
			$dt_ob->setRowValue('banner_brands', array());
			$dt_ob->setRowValue('banner_pages', array());
		}
	}
	
	function bannerAfterSave(DataTable $dt_ob){
		
		global $db;
		
		$image_filter = array();
		if($dt_ob->getRowValue('placing') && $dt_ob->getRowValue('file')){
			foreach($GLOBALS['banners_placings'] as $v){
				if($dt_ob->getRowValue('placing') == $v[0]){
					if(isset($v['image_filter'])){
						$image_filter = $v['image_filter'];
					}
					break;
				}
			}
			
			if(is_array($image_filter)){
				foreach($image_filter as $v){
					Images::imageResizeUnified(B_DIR.'img/ab/'.$dt_ob->getRowValue('file'), $v['width'], $v['height'], B_DIR.'img/ab/'.($v['prefix'] !== '' ? $v['prefix'].'_' : '').$dt_ob->getRowValue('file'), 2, $v['resize_type'], isset($v['filter']['name']) ? $v['filter']['name'] : false, isset($v['filter']['value']) ? $v['filter']['value'] : false, isset($v['dont_save_proportions']) && $v['dont_save_proportions'] ? false : true, true);
				}
			}
		}
		
	}
	
	function bannerAfterDelete(DataTable $dt_ob){
		
		if($dt_ob->getRowValue('file')){
			foreach($GLOBALS['banners_placings'] as $p){
				if(isset($p['image_filter'])){
					foreach($p['image_filter'] as $v)if(isset($v['prefix']) && $v['prefix'] !== ''){
						$fn = B_DIR.'img/ab/'.($v['prefix'] !== '' ? $v['prefix'].'_' : '').$dt_ob->getRowValue('file');
						if(file_exists($fn)){
							unlink($fn);
						}
					}
				}
			}
		}
		
	}
	
	function makeItemUrl(DataTable $dt_ob){
		$url = str_replace('/', '', $dt_ob->getRowValue('url'));
		if($url != ''){
			$url = '/'.$url;
			if($dt_ob->getRowValue('parent')){
				$par = $GLOBALS['db']->query('select * from '.$dt_ob->entity.' where id='.intval($dt_ob->getRowValue('parent')))->getNext();
				if($par){
					$url = $par['url'].$url;
				}
			}
		}
		$dt_ob->setRowValue('url', $url);
	}
	
	function onAfterUrlChanged(DataTable $dt_ob){
		
		if($dt_ob->getLastId() && $dt_ob->getBeforeUpdateData('url') != $dt_ob->getRowValue('url')){
			
			/* update url in all children */
			$old_url = $dt_ob->getBeforeUpdateData('url');
			$new_url = $dt_ob->getRowValue('url');
			$GLOBALS['db']->query('update '.$dt_ob->entity.' set url = concat("'.$new_url.'",substring(url,'.(strlen($old_url)+1).')) where url like "'.$old_url.'%" and id != '.$dt_ob->getLastId());
			//echo 'update '.$dt_ob->entity.' set url = concat("'.$new_url.'",substring(url,'.strlen($old_url).')) where url like "'.$old_url.'%" and id != '.$dt_ob->getLastId();
			//exit();
			
		}
		
	}
	
	function updateComponentSettings(DataTable $dt_ob = null){		
		global $db;
		if(intval($dt_ob->getLastId())){
			$db->query('delete from components_configs where centry_id='.$dt_ob->getLastId());
			$ce = new ComponentEntry($dt_ob->getLastId());
			$configs = $ce->getConfigs();
			foreach($configs as $k=>$v){
				if(isset($_POST[$k])){
					if($v[1][0] == 'select' && isset($v[1][2]) && $v[1][2] == 1)
						foreach($_POST[$k] as $vl)
							$db->query('insert into components_configs(centry_id,param,value) values('.$dt_ob->getLastId().',"'.$k.'","'.mysql_escape_string($vl).'")');
					else
						$db->query('insert into components_configs(centry_id,param,value) values('.$dt_ob->getLastId().',"'.$k.'","'.mysql_escape_string($_POST[$k]).'")');
				}
			}
		}		
	}
	
	function setMainPhoto(DataTable $dt_ob){
		
		global $db;
		
		if($dt_ob->getBeforeUpdateData('is_main') == 0 && $dt_ob->getRowValue('is_main') == 1){
			$db->query('update photogallery set is_main=0 where is_main=1 and folder_id='.intval($dt_ob->getRowValue('folder_id')).' and id != '.intval($dt_ob->getLastID()));
		}
		
	}
	
	function saveTags(DataTable $dt_ob){
		if(intval($dt_ob->getLastID()) && isset($_POST['tags'])){			
			self::saveElementTags($dt_ob->entity, $dt_ob->getLastID(), $_POST['tags']);
		}
	}
	
	function saveElementTags($entity, $element_id, $tags){
		
		global $db;
		
		$cur_tags = array();
		$new_tags = array();
		$to_delete = array();
		$to_add = array();
			
		/* get current tags array */
		$res = $GLOBALS['db']->query('select * from '.$entity.'_tags_elements where item_id='.intval($element_id));
		while($t = $res->getNext()){
			$cur_tags[] = $t['tag'];
		}
			
		/* get new tags array */
		$new_tags = array();
		$new_tags_txt = mb_strlen($tags) ? array_map('trim', explode(',', trim($tags))) : array();
		foreach($new_tags_txt as $v)if($v != ''){
			$res = $db->query('select * from tags where name="'.mysql_escape_string($v).'"')->getNext();
			if($res){
				$new_tags[] = $res['id'];
			}else{
				$res = $db->query('insert into tags set name="'.mysql_escape_string($v).'"');
				$new_tags[] = $res->lastID();
			}
		}
			
		/* to add */
		$to_add = array_diff($new_tags, $cur_tags);
			
		/* to delete */
		$to_delete = array_diff($cur_tags, $new_tags);
			
		if(count($to_delete)){
			$GLOBALS['db']->query('delete from '.$entity.'_tags_elements where item_id='.intval($element_id).' and (tag='.implode(' or tag=', array_map('intval',$to_delete)).')');
		}
		if(count($to_add)){
			$GLOBALS['db']->query('insert into '.$entity.'_tags_elements(item_id,tag) values('.intval($element_id).','.implode('),('.intval($element_id).',', array_map('intval', $to_add)).')');
		}
	}
	
	function onAfterComponentSave(DataTable $dt_ob){
		
		global $db;
		
		if($dt_ob->getLastID()){
			
			$component = $dt_ob->getRowValue('component');
			$page_id = intval($dt_ob->getRowValue('page_id'));
			
			if($component && $page_id){
			
				$allow_rewrite = null;
				$max_params = 0;
				$assign_get_vars = array();
				
				$rewrite_file = COMPONENTS_DIR.$component.'/inc/url_rewrite.php';
				if(file_exists($rewrite_file)){
					
					require $rewrite_file;
					
					if($allow_rewrite !== null && $allow_rewrite){
						
						$max_params = intval($max_params);
						if(!is_array($assign_get_vars))
							$assign_get_vars = array();
							
						$db->query('update structure set allow_extra_params=1,extra_params_limit='.$max_params.',set_get_vars="'.mysql_escape_string(implode(' ',$assign_get_vars)).'" where id='.$page_id);
						
					}
					
				}
			
			}
			
		}
		
	}
	
	function saveCatalogPropertyValues(DataTable $dt_ob = null){
		global $db;
		if(intval($dt_ob->getLastID()) && isset($_POST['properties']) && is_array($_POST['properties'])){
			foreach($_POST['properties'] as $k => $v){
				
				$prop_data = $db->query('select * from catalog_properties where id='.intval($k))->getNext();
				if(!$prop_data)
					continue;
					
				if($prop_data['type'] == 'list' && $prop_data['is_multiple']){
					
					$new_values = is_array($v) ? array_map('intval', $v) : array();
					$old_values = $db->query('select list_element_id from catalog_item_properties where item_id='.$dt_ob->getLastId().' and property_id='.intval($k))->fetchArray('list_element_id');
					
					$to_add = array_diff($new_values, $old_values);
					$to_delete = array_diff($old_values, $new_values);
					
					foreach($to_add as $a)
						$db->query('insert into catalog_item_properties set item_id='.$dt_ob->getLastId().',property_id='.$k.',list_element_id='.$a);
						
					if(count($to_delete))
						$db->query('delete from catalog_item_properties where item_id='.$dt_ob->getLastId().' and property_id='.$k.' and list_element_id in('.implode(',', $to_delete).')');
					
					
				}elseif($prop_data['type'] == 'number' || $prop_data['type'] == 'list'){
					
					$field = $prop_data['type'] == 'list' ? 'list_element_id' : 'value_int';
					
					$value = $prop_data['type'] == 'number' ? floatval($v) : intval($v);
					
					$row = $db->query('select * from catalog_item_properties where item_id='.intval($dt_ob->getLastId()).' and property_id='.intval($k))->getNext();
					if($row){ // update
						if($row[$field] != $value)
							$db->query('update catalog_item_properties set '.$field.'='.$value.' where id='.$row['id']);
					}else{ // new
						$db->query('insert into catalog_item_properties set '.$field.'='.$value.',item_id='.intval($dt_ob->getLastId()).',property_id='.intval($k));
					}
					
				}else{
					
					// look for the value
					$value_id = 0;
					if($v != ''){
						$res = $db->query('select * from catalog_property_values where name="'.mysql_escape_string($v).'"')->getNext();
						if($res){
							$value_id = $res['id'];
						}else{
							$res = $db->query('insert into catalog_property_values set name="'.mysql_escape_string($v).'"');
							$value_id = $res->lastID();
						}
					}
					$row = $db->query('select * from catalog_item_properties where item_id='.intval($dt_ob->getLastId()).' and property_id='.intval($k))->getNext();
					if($row){ // update
						if($row['value'] != $value_id)
							$db->query('update catalog_item_properties set value='.intval($value_id).' where id='.$row['id']);
					}else{ // new
						$db->query('insert into catalog_item_properties set value='.intval($value_id).',item_id='.intval($dt_ob->getLastId()).',property_id='.intval($k));
					}
					
				}
			}
		}	
	}
	
	function catalogPricesChanged(DataTable $dt_ob){
		
		global $db;
		
		if($dt_ob->getLastId()){
			
			$price_type = $db->query('select * from catalog_price_types where id='.intval($dt_ob->getRowValue('price_type_id')))->getNext();		
			
			// обновляем цену товара, если измененная цена - "базовая"
			if($price_type && $price_type['is_base']){
				
				$dt_cat = new DataTable('catalog');
				$dt_cat->updateRow(array('id' => $dt_ob->getRowValue('product_id'), 'price' => $dt_ob->getRowValue('price')), true);
				
			}
			
		}
		
	}
	
	function updateCatalogPrices(DataTable $dt_ob){
		
		global $db;
		
		if($dt_ob->getLastId() && $dt_ob->getBeforeUpdateData('price') != $dt_ob->getRowValue('price')){
			
			// get base price
			$price_type = $db->query('select * from catalog_price_types where is_base=1')->getNext();
			if($price_type){
				
				$pr_ob = new DataTable('catalog_prices');
				$data = array(
					'price_type_id' => $price_type['id'],
					'product_id' => $dt_ob->getLastId(),
					'price' => $dt_ob->getRowValue('price')
				);
			
				$row = $db->query('select * from catalog_prices where product_id='.$dt_ob->getLastId().' and price_type_id='.$price_type['id'])->getNext();
				if($row){
					
					$data['id'] = $row['id'];
					$pr_ob->updateRow($data, false);
					
				}else{
					
					$pr_ob->insertRow($data, false);
					
				}
			
			}
			
		}
		
	}
	
	function orderSaveHistory(DataTable $dt_ob){
		global $db;
		if($dt_ob->getLastId()){
			$fields = array('status', 'status_payment');
			foreach($fields as $s){
				if($dt_ob->getRowValue($s) && $dt_ob->getRowValue($s) != $dt_ob->getBeforeUpdateData($s)){
					$db->query('insert into orders_status_history set created_by='.intval(AuthUser::getInstance()->getId()).',order_id='.$dt_ob->getLastId().',status='.$dt_ob->getRowValue($s).',comment="'.(isset($_POST[$s.'_comment']) ? mysql_escape_string($_POST[$s.'_comment']) : '').'"');
				}
			}
		}
	}
	
	public static function updateOrderSum(DataTable $dt_ob){
		$oid = $dt_ob->getRowValue('order_id');
		self::recalculateOrder($oid);
	}
	
	public static function orderItemAdded(DataTable $dt_ob){
		global $db;
		$oid = $dt_ob->getRowValue('order_id');
		$iid = $dt_ob->getLastId();
		$res = $db->query('select C.price,C.discount from order_items as I,catalog as C where C.id=I.item_id and I.id='.$iid)->getNext();
		if($res){
			$db->query('update order_items set price='.$res['price'].',discount='.$res['discount'].' where id='.$iid);
		}
	}
	
	public static function recalculateOrder($oid){
		global $db;
		$o_data = $GLOBALS['db']->query('select * from orders where id='.intval($oid))->getNext();
		$new_data = $GLOBALS['db']->query('select sum(I.items_num) as items_num,sum(round(IF(I.price=0,C.price,I.price)*(1-(IF(I.discount=0,C.discount,I.discount))/100))*I.items_num) as total_sum from order_items as I,catalog as C where I.order_id='.$oid.' and I.item_id=C.id')->getNext();
		if($new_data){
			$items = $db->query('select IF(I.price=0,C.price,I.price) as price_real,IF(I.discount,C.discount,I.discount) as discount,round(IF(I.price=0,C.price,I.price)*(1-(IF(I.discount=0,C.discount,I.discount))/100)) as price,I.items_num,I.item_id as id from order_items as I,catalog as C where I.order_id='.$oid.' and C.id=I.item_id')->fetchArray();
			$discount = Catalog::getDiscount($new_data['total_sum'], ($o_data ? $o_data['discount_card'] : 0), $items, $o_data['user_id']);
			$db->query('update orders set items_num='.intval($new_data['items_num']).',sum='.intval($new_data['total_sum']).',sum_discount='.intval($discount['total_sum']).',discount_value='.$discount['perc'].',discount_by_card='.intval($discount['by_card']).' where id='.$oid);
		}
	}
	
	function updateBonus(DataTable $dt_ob){
		
		global $db;
		
		if($dt_ob->getLastId()){
			
			$order_data = $db->query('select O.status,O.status_payment,O.phone,U.bonus as bonus_before,O.user_id,O.use_bonus,O.sum_discount,O.id,O.bonus_updated from orders as O,users as U where O.id='.$dt_ob->getLastId().' and O.user_id=U.id')->getNext();
			
			if(!$order_data || !$order_data['user_id'])
				return false;
			
			$changed_status = 0;
			$status_data = false;
			
			if($dt_ob->getRowValue('status') && $dt_ob->getRowValue('status') != $dt_ob->getBeforeUpdateData('status')){
				$changed_status = $dt_ob->getRowValue('status');
			}elseif($dt_ob->getRowValue('status_payment') && $dt_ob->getRowValue('status_payment') != $dt_ob->getBeforeUpdateData('status_payment')){
				$changed_status = $dt_ob->getRowValue('status_payment');
			}
			
			if($changed_status)
				$status_data = $db->query('select * from order_statuses where id='.intval($changed_status))->getNext();
			
			if($status_data && $status_data['append_bonus']){
				
				if(!$order_data['bonus_updated']){
						
					$bonus_value = 0;
					$order_sum = $order_data['sum_discount'];
					
					/*
					$order_items = $db->query('select price,items_num from order_items where discount >= 0 and order_id='.$order_data['id']);	
					while($v = $order_items->getNext()){
						$order_sum += $v['price']*$v['items_num'];
					}
					*/
						
					$bonus_value = Bonus::getInstance()->getValue($order_sum);
						
					$db->query('update users set bonus = bonus+'.$bonus_value.' where id='.intval($order_data['user_id']));
					$db->query('update orders set bonus_updated='.$bonus_value.',bonus_updated_at = NOW() where id='.$order_data['id']);
						
				}
				
			}else{
				
				if($order_data['bonus_updated']){
				
					// проверяем, были ли начислены бонусы, и надо ли их снять

					$status_data_2 = $db->query('select * from order_statuses where id='.intval($order_data['status'] == $changed_status ? $order_data['status_payment'] : $order_data['status']))->getNext();

					if((!$status_data || !$status_data['append_bonus']) && (!$status_data_2 || !$status_data_2['append_bonus'])){
						
						$db->query('update users set bonus=bonus-'.intval($order_data['bonus_updated']).' where id='.intval($order_data['user_id']));
						$db->query('update orders set bonus_updated=0,bonus_updated_at = NOW() where id='.intval($order_data['id']));
						
					}
				
				}
				
			}		
			
		}
		
	}
	
	function bonusReturn(DataTable $dt_ob, $event = 'update'){
		
		global $db;
		
		$bonus_update = intval($dt_ob->getBeforeUpdateData('use_bonus'))  - intval($dt_ob->getBeforeUpdateData('bonus_updated'));
		
		$db->query('update users set bonus=bonus + '.$bonus_update.' where id='.intval($dt_ob->getBeforeUpdateData('user_id')));
		
	}
	
}
?>