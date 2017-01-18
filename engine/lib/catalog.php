<?

class Catalog{
	
	private static $lastSeenSize = 24;
	private static $favorites = null;
	private static $compare = null;
	
	static function getSectionsByProductsFilter($filter = array(), $parent = 0, $level_limit = -1, $cur_section = 0){
		
		$sections_res = array();
		
		if(isset($filter['folder_id'])){
			unset($filter['folder_id']);
		}
		
		/* check if need to use filter */
		$use_filter = false;
		foreach($filter as $v){
			if(is_array($v) && count($v) || trim($v) != ''){
				$use_filter = true;
				break;
			}
		}
		
		$section_ids = array();
		
		$tree = new Tree('folders', 'entity = "catalog"');
		if($cur_section){
			$level_limit = -1;
			$tree->setCurrentElement($cur_section);
		}
		$sections = $tree->getFullTree($parent, 0, $level_limit);
		for($i=0; $i<count($sections); $i++){
			if(!$use_filter || self::hasElementsInSection($sections[$i]['id'], $filter)){
				$sections_res[] = $sections[$i];
			}
		}
		return $sections_res;
		
	}
	
	function hasElementsInSection($section_id, $filter){		
		
		$db = $GLOBALS['db'];
		$dt_ob = new DataTable('catalog');
		$dt_ob->setFilter(array_merge($filter, array('folder_id' => $section_id)), false);
		$e_num = $dt_ob->getElementsNum();
		if($e_num){
			return true;
		}else{
			$res = $db->query('select * from folders where entity="catalog" and parent='.$section_id);	
			while($t = $res->getNext()){
				$n = self::hasElementsInSection($t['id'], $filter);
				if($n)
					return true;
			}
		}	
		return false;
				
	}
	
	/**
	 * Выборка из каталога по фильтру
	 *
	 * @return DBResult
	 */
	function getElements($filter, $extra_cond = '', $order_by = 'pos', $ipp = 0, $limit = 0, $folder_recursive = false, $ignore_deleted = true){
		
		global $db;
		
		$fo = new DBFilter('catalog');
		$fo->setFilter($filter, false);		
		$fo->setFolderRecursive($folder_recursive);
		
		$q = $fo->getQuery();
		
		$q['tables'] .= ',folders as FLD';
		$q['where'] .= ' and FLD.id=T.folder_id';
		$q['tables'] = '('.$q['tables'].') left join brands as B on B.id=T.brand';
		
		$q['tables'] .= ' left join currency as CR on CR.id=T.currency';
		$q['tables'] .= ' left join catalog_colors as C_C on C_C.id=T.color';
					
		$q['fields'] .= ',FLD.orig_ext as section_orig_ext,FLD.name as section_name,FLD.name_prepos as section_name_prepos,FLD.url as folder_url,FLD.pos as section_pos,B.name as brand_name,B.logo as brand_logo,B.site_url';		
		$q['fields'] .= ',IF(T.discount=0 or T.discount is null,T.price,round(T.price*(1-(T.discount/100)))) as price,T.price as price_real,T.discount';
		
		$q['fields'] .= ',(select PH.id from catalog_photos as PH where PH.item_id=T.id order by PH.pos limit 1) as photo_id';
		
		$q['fields'] .= ',T.model as _model,concat(T.model, if(ifnull(C_C.name, "") = "", "", concat(" ", C_C.name)), if(ifnull(T.package, "") = "", "", concat(" (", T.package, ")"))) as model';
		$q['fields'] .= ',concat(B.name, " ", T.model, " ", ifnull(C_C.name, ""), " ", ifnull(T.package, "")) as name';
		$q['fields'] .= ',C_C.name as color';
		
		if(isset($filter['price_type'])){
			$q['fields'] .= ',@price := (select PRC.price from catalog_prices as PRC where PRC.product_id=T.id and price_type_id='.intval($filter['price_type']).')';
			$q['fields'] .= ',@price as price';
		}
					
		if(isset($filter['price_min'])){
			$q['where'] .= ' and T.price >= '.intval($filter['price_min']);
		}
		if(isset($filter['price_max'])){
			$q['where'] .= ' and T.price <= '.intval($filter['price_max']);
		}
		
		if(isset($filter['properties']) && is_array($filter['properties']) && count($filter['properties'])){
			
			$tq = array();
			
			foreach($filter['properties'] as $p_id => $p_val){
				
				if(!is_numeric($p_id))
					continue;
					
				$prop_data = $db->query('select * from catalog_properties where id='.intval($p_id))->getNext();
				if(!$prop_data)
					continue;
					
				$prop_q = array();
				
				$fld = 'value';
				switch($prop_data['type']){
					case 'checkbox':
						$fld = 'term_id';
					break;
					case 'number':
						$fld = 'value_int';
					break;
					case 'text':
						$fld = 'value';
					break;
					case 'list':
						$fld = 'list_element_id';
					break;
				}
				if($prop_data['type'] == 'number'){
					
					if(isset($p_val['min']) && isset($p_val['max'])){
						$prop_q[] = 'CPV.'.$fld.'>='.floatval($p_val['min']).' and CPV.'.$fld.'<='.floatval($p_val['max']);
					}
					
				}else{
					if(is_numeric($p_val)){
						$prop_q[] = 'CPV.'.$fld.'='.intval($p_val);
					}elseif(is_array($p_val)){
						foreach($p_val as $v){
							$prop_q[] = 'CPV.'.$fld.'='.intval($v);
						}
					}
				}
				
				if(count($prop_q)){
					$tq[] = 'CPV.property_id='.$p_id.' and ('.implode(' or ', $prop_q).')';
				}				
				
			}
			
			if(count($tq)){
				
				$q['fields'] .= ',(select count(distinct CPV.property_id) from catalog_item_properties as CPV where T.id=CPV.item_id and ('.implode(' or ', $tq).')) as props_count';
				if(!isset($q['having']))
					$q['having'] = '';
				if($q['having']){
					$q['having'] .= ' and ';
				}
				$q['having'] .= 'props_count>='.count($tq);
				
			}
			
		}
		
		// get main package
		if(!isset($filter['_no_combine_products'])){
			$q['tables'] = '('.$q['tables'].') left join catalog as T2 on T2.model=T.model and T2.brand=T.brand and T2.is_main_package=1 and T.is_main_package=0';
			$q['fields'] .= ',IFNULL(T2.id,T.id) as _main_package_id';
			$q['fields'] .= ',IF(T2.id IS NULL, T.short_desc, T2.short_desc) as short_desc';
			$q['fields'] .= ',IF(T2.id IS NULL, T.url, T2.url) as url';
		}else{
			$q['fields'] .= ',T.id as _main_package_id';
			$q['fields'] .= ',T.short_desc,T.url,T.description';
		}
		
		if($extra_cond)
			$q['where'] .= ' and ('.$extra_cond.')';
			
		$order_by = 'T.avail=0,'.$order_by;
		
		$q = '
				select '.$q['fields'].'
				from ('.$q['tables'].')
				where '.$q['where'].
				(isset($q['group_by']) ? ' group by '.$q['group_by'] : '').
				(isset($q['having']) ? ' having '.$q['having'] : '').'
				order by '.$order_by.($limit ? ' limit '.$limit : '');
		
		$res = $GLOBALS['db']->query($q);		                              
		                               
		if($ipp)
			$res->startPager($ipp);
		
		return $res;
		
	}
	
	function getElementsNum($filter, $folder = 0){
		$dt_ob = new DataTable('catalog');
		$sum = 0;
		if($folder){
			$tree = new Tree('folders', 'entity="catalog"');
			$folders = $tree->getFullTree($folder);
			$dt_ob->setFilter(array_merge($filter, array('folder_id' => $folder)), false);
			$sum = $dt_ob->getElementsNum();
			foreach($folders as $v){		
				$dt_ob->setFilter(array_merge($filter, array('folder_id' => $v['id'])), false);
				$sum += $dt_ob->getElementsNum();
			}
		}else{
			$dt_ob->setFilter($filter, false);
			$sum = $dt_ob->getElementsNum();
		}
		return $sum;
	}
	
	function getPathToSection($section_id, $brand_id = 0){
		
		$tree = new Tree('folders');
		$p = $tree->getPath('name,id', $section_id);
		
		$res_path = array();
		foreach($p as $v)
			$res_path[] = array('name' => $v['name'], 'link' => '/catalog/?section='.$v['id'].($brand_id ? '&brand='.$brand_id : ''), 'id' => $v['id']);
			
		return $res_path;
		
	}
	
	function getById($id, $filter = null){
		if(!is_array($filter)){
			$filter = array();
		}
		$data = Catalog::getElements(array_merge(array('id' => intval($id)), $filter))->getNext();
		if($data){
			//
		}
		return $data;		
	}
	
	/**
	 * returns product variants
	 *
	 * @param int $id
	 * @return DBResult
	 */
	function getProductVariants($id, $order_by = 'pos'){
		global $db;
		$res = $db->query(
			'select *
			from catalog_item_variants
			where item_id='.intval($id).'
			order by '.$order_by
		);
		return $res;
	}
	
	static function getSectionBrands($section_id, $count_elements = false){
		
		$db = &$GLOBALS['db'];
		$ids = array();
		if(is_array($section_id))
			$ids = array_map('intval', $section_id);
		else{
			$tree = new Tree('folders');
			$sections = $tree->getFullTree(intval($section_id));
			for($i=0; $i<count($sections); $i++){
				if($i == count($sections)-1 || $sections[$i+1]['level'] <= $sections[$i]['level'])
					$ids[] = $sections[$i]['id'];
			}
			if(!count($ids))
				$ids[] = $section_id;
		}
		$res = $db->query('select distinct B.* from brands as B, catalog as C where B.id=C.brand and C.folder_id in ('.implode(',',$ids).') and C.active=1 order by B.name');
		if($count_elements){
			$dt = new DataTable('catalog');
			$dt->setFilterFolderRecursive(true);
			$arr = array();
			while($t = $res->getNext()){
				$flt = array('folder_id' => $section_id, 'brand' => $t['id']);				
				$dt->setFilter($flt, false);
				$t['elements_num'] = $dt->getElementsNum();
				$arr[] = $t;
			}
			return $arr;
		}else{
			return $res->fetchArray();
		}
		
	}
	
	function getSections($parent_section = 0, $brand = 0){
		
		$db = $GLOBALS['db'];
		
		if($brand == 0){
			$res = $db->query('select * from folders where and entity="catalog" and parent='.intval($parent_section).' order by pos');
			return $res->fetchArray();
		}else{
			
			$sections_res = array();
			
			$tree = new Tree('folders', 'entity="catalog"');
			$sections = $tree->getFullTree($parent_section);
			$res_indexes = array();
			$current_parent = 0;
			for($i=0; $i<count($sections); $i++){
				if($sections[$i]['_tree_level'] == 0){
					$current_parent = $i;
				}
				if(!in_array($current_parent, $res_indexes) && ($i == count($sections)-1 || $sections[$i+1]['level'] <= $sections[$i]['level'])){
					$elts_res = $db->query('select count(*) as cnt from catalog where folder_id='.$sections[$i]['id'].' and brand='.intval($brand));
					$t = $elts_res->getNext();
					if($t['cnt'])
						$res_indexes[] = $current_parent;
				}
			}
			
			for($i=0; $i<count($res_indexes); $i++)
				$sections_res[] = $sections[$res_indexes[$i]];
				
			return $sections_res;
			
		}
		
	}
	
	function getElementPhoto($element_id, $format = 'l', $use_placeholder = true){
		$db = &$GLOBALS['db'];
		$res = $db->query('select id from catalog_photos where element_id='.intval($element_id).' order by pos limit 1');
		if($t = $res->getNext()){
			return '/img/catalog/'.$format.$t['id'].'.jpg';
		}elseif($use_placeholder){
			return '/img/nophoto_'.$format.'.jpg';
		}
		return '';
	}
	
	static function getElementProperties($element_id, $ignore_empty = true, $in_card = false, $full_mode = false){
		
		global $db;
		
		$arr = array();
		
		/* get element section */
		$res = $GLOBALS['db']->query('select folder_id from catalog where id='.intval($element_id));
		if($element_data = $res->getNext()){
			
			/* get section properties */
			$prop_ids = array();
			$section_properties = Catalog::getSectionProperties($element_data['folder_id']);
			for($i=0; $i < count($section_properties); $i++){
				$prop_ids[] = $section_properties[$i]['id'];
			}
		
			if(count($prop_ids)){
				
				$res = $GLOBALS['db']->query('select P.is_multiple,P.id, P.name, P.type, V.value_int, VN.name as value,VN.id as value_id,G.name as group_name,V.list_element_id,
												group_concat(V.list_element_id) as term_concat
				                              from catalog_properties as P
				                              left join catalog_item_properties as V
				                              on V.property_id=P.id and V.item_id='.intval($element_id).'
				                              left join catalog_property_values as VN on V.value=VN.id
				                              left join catalog_property_groups as G on G.id=P.group_id
				                              where P.id in ('.implode(',', $prop_ids).')'.($in_card ? ' and P.in_card=1' : '').'
				                              group by P.id
				                              order by P.group_id>0,G.pos,P.pos');
				
				while($t = $res->getNext()){
					if(!$ignore_empty || $t['value'] != '' || $t['value_int'] != '' || $t['term_concat'] != '' || $t['list_element_id'] != ''){
						if(!$full_mode){
							switch($t['type']){
								case 'number':
									$arr[$t['id']] = $t['value_int'];
								break;
								case 'checkbox':
									$arr[$t['id']] = $t['term_concat'];
								break;
								case 'list':
									if($t['is_multiple'])
										$arr[$t['id']] = $t['term_concat'];
									else
										$arr[$t['id']] = $t['list_element_id'];
								break;
								default:
									$arr[$t['id']] = $t['value'];
								break;
							}
						}else{							
							if($t['type'] == 'number')
								$t['value'] = $t['value_int'];
							elseif($t['type'] == 'list'){
								
								if($t['is_multiple']){
									$t['values'] = array();
									if($t['term_concat'] != ''){								
										$t['values'] = $db->query('select * from lists_elements where id in ('.$t['term_concat'].') order by title')->fetchArray();
										$t['value'] = '';
										foreach($t['values'] as $__v){
											if($t['value'] != '')
												$t['value'] .= ', ';
											$t['value'] .= $__v['title'];
										}
									}
								}else{
									$t['value'] = $db->query('select title from lists_elements where id='.intval($t['list_element_id']))->getNext('title');
								}
								
							}elseif($t['type'] == 'checkbox'){
								$t['values'] = array();
								if($t['term_concat'] != ''){								
									$t['values'] = $db->query('select * from catalog_terms where id in ('.$t['term_concat'].') order by name')->fetchArray();
								}
							}
							$arr[$t['id']] = $t;
						}
					}
				}
				
			}
					
		}
			
		return $arr;
		
	}
	
	static function getSectionProperties($folder_id, $in_filter = false, $parent = -1, $limit = -1){
		
		$tree = new Tree('catalog_properties', 'folder_id=0'.($in_filter ? ' and show_in_filter = 1' : ''));		
		$arr = $tree->getFullTree();
		
		if(intval($folder_id)){
			
			$tree = new Tree('folders', 'entity="catalog"');
			$path = $tree->getPath('*', $folder_id);
			foreach($path as $v){
				$tree = new Tree('catalog_properties', 'folder_id='.$v['id'].($in_filter ? ' and show_in_filter = 1' : ''));
				$p_res = $tree->getFullTree($parent > -1 ? $parent : 0, 0, $limit);
				$arr = array_merge($arr, $p_res);
			}
			
		}
		return $arr;
		
	}
	
	static function getSectionTags($section_id = 0, $limit = 0){
		if($section_id == 0){
			$res = $GLOBALS['db']->query('select count(*) as cnt, tag from tags group by tag order by rand()'.($limit ? ' limit '.$limit : ''));
			return $res->fetchArray();
		}else{
			$tree = new Tree('folders', 'entity="catalog"');
			$children = $tree->getFullTree($section_id);
			$ids[] = $section_id;
			foreach($children as $v){
				$ids[] = $v['id'];
			}
			$res = $GLOBALS['db']->query('select count(*) as cnt,T.tag 
			                              from catalog as C,tags as T where C.folder_id in ('.implode(',', $ids).') and C.id=T.item_id
			                              group by T.tag order by rand()');
			return $res->fetchArray();
		}
		
	}
	
	static function getElementTags($element_id){
		$res = $GLOBALS['db']->query('select count(*) as cnt,tag from tags where item_id='.intval($element_id).' group by tag order by rand()');
		return $res->fetchArray();
	}	
	
	function getSpecialTitle($id){
		$res = $GLOBALS['db']->query('select ST.* from catalog_special_types as ST,catalog_special_products as SP where SP.product_id='.intval($id).' and SP.special_type=ST.id order by rand() limit 1');
		if($t = $res->getNext()){
			return $t['name_single'] != '' ? $t['name_single'] : $t['name'];
		}
		return '';
	}
	
	function getSpecialProducts($section_id, $limit = 0, $special = true){
		$special_products = array();
		$rel_fld = array();
		//$rel_fld[] = $section_id;
		if($section_id){
			$res = $GLOBALS['db']->query('select folder2 from related_folders where folder1='.$section_id);
			while($t = $res->getNext()){
				$rel_fld[] = $t['folder2'];
			}
		}
		if(count($rel_fld)){
			$filter = array(
				'folder_id' => $rel_fld
			);
			if($special){
				$filter['catalog_special_products'] = true;
			}
			$special_products = Catalog::getElements($filter, '', 'rand()', 0, $limit)->fetchArray();
			if($special){
				for($i=0; $i<count($special_products); $i++){
					$special_products[$i]['_mark'] = 1;
					$special_products[$i]['_mark_title'] = Catalog::getSpecialTitle($special_products[$i]['id']);
				}
			}
		}
		return $special_products;
	}
	
	/* SET functions */
	
	function getSetElements($set_id){
		
		return Catalog::getElements(array('catalog_sets' => $set_id))->fetchArray();
		
	}
	
	function getSetByElement($element_id){
		
		$res = $GLOBALS['db']->query('select set_id from catalog_sets where item_id='.intval($element_id).' order by rand() limit 1');
		if($t = $res->getNext()){
			return Catalog::getById($t['set_id']);
		}else{
			return false;
		}
		
	}
	
	/**
	Discount 
	
	return
	array(
		perc - размер скидки в процентах	
		value - размер скидки в деньгах
		total_sum - общая сумма с учетом скидки
		next_discount - размер следующей большей скидки в процентах
		next_diff - сумма для получения ближайшей большей скидки
		next_discount_text - текст "до скидки N% осталось X руб."
		by_card - скидка назначена по дисконтной карте (bool)
	)
	*/
	
	function getDiscount($sum, $discount_card = null, $products = null){
		
		global $db;
		
		$discount_card_data = false;
		$by_card = false;
		if(is_null($discount_card)){
			$discount_card = AuthUser::getInstance()->getField('discount_card');
		}
		$discount_card = intval($discount_card);
		if($discount_card){
			$discount_card_data = $db->query('select * from discount_cards where number='.$discount_card)->getNext();
		}
		

		$res = array(
			'perc' => 0, 
			'value' => 0, 
			'total_sum' => $sum, 
			'next_discount' => 0, 
			'next_diff' => '', 
			'next_discount_text' => '', 
			'by_card' => false
		);
		
		$list = $GLOBALS['db']->query('select * from discounts where 1 order by sum_value')->fetchArray();
		$cur_d = -1;
		
		for($i = count($list)-1; $i >= 0; $i--){
			if($sum >= $list[$i]['sum_value']){
				$res['perc'] = $list[$i]['discount'];
				$cur_d = $i;				
				break;
			}
		}
		
		if($discount_card_data && $discount_card_data['discount'] > $res['perc']){
			$res['perc'] = $discount_card_data['discount'];
			$res['by_card'] = true;
		}
		
		if($res['perc']){
			if(is_null($products)){
				$res['value'] = ceil($sum*$res['perc']/100);
				$res['total_sum'] = $sum - $res['value'];					
			}else{
				if(is_numeric($products))
					$products = Cart::getWishlistItems($products);
				$sm = 0;
				$sum = 0;
				foreach($products as $v){
					$sum += $v['price_real']*(1-$v['discount']/100)*$v['items_num'];
					$dsc = $res['perc'] > $v['discount'] ? $res['perc'] : $v['discount'];
					$sm += $v['price_real']*(1-$dsc/100)*$v['items_num'];
				}
				$res['total_sum'] = round($sm);
				$res['value'] = round($sum) - $res['total_sum'];
			}
		}
		
		if(!$res['by_card'] && $cur_d < count($list)-1 && count($list)){
			$res['next_discount'] = $list[$cur_d+1]['discount'];
			$res['next_diff'] = $list[$cur_d+1]['sum_value'] - $sum;
			if($res['next_discount'])
				$res['next_discount_text'] = $res['next_diff'] . ' руб. до скидки '.$res['next_discount'].'%';
		}
		
		return $res;
				
	}
	
	static function calculatePrice($arr){
		if(isset($arr['currency']) && isset($arr['price'])){			
			$price = $arr['price'];
			if($arr['currency'] != SiteCurrency::getInstance()->getID()){
				$res = $GLOBALS['db']->query('select * from currency where id='.intval($arr['currency']));
				if($t = $res->getNext()){
					$c_course = SiteCurrency::getInstance()->getCourse();
					if($c_course > 0)
						$price = $arr['price']*$t['course']/$c_course;
				}
			}
			return $price;
		}
		return 0;
	}
	
	function ifUserVoted(){
		$voted = false;
		$cook = isset($_COOKIE['vote_for_discount']) ? $_COOKIE['vote_for_discount'] : '';
		if($cook){
			/*
			$dn = date('w');
			if($dn == 0)
				$dn = 7;
			$dn--;
			$fd = date('Y-m-d', time() - 86400*$dn);
			*/
			$fd = date('Y-m-d');
			if($cook >= $fd)
				$voted = true;
		}
		return $voted;
	}
	
	function getRealPrice($product){
		
		$discount = 0;
		
		if(isset($product['product_of_week']) && $product['product_of_week']){
			$discount = DBCommon::getConfig('product_of_week_discount');
		}elseif(isset($product['product_of_day']) && $product['product_of_day']){
			$discount = DBCommon::getConfig('product_of_day_discount');
		}
		
		if($discount){
			return round($product['price']*(1-$discount/100));
		}
		
		return $product['price'];
		
	}
	
	/**
	 * @param array $filter_all
	 * @return DBResult
	 */
	function getBrandsByFilter($filter_all){
		
		global $db;
		
		$avail_filter = array('folder_id', 'catalog_item_purposes');
		$filter = array();
		foreach($filter_all as $k => $v){
			if(in_array($k, $avail_filter)){
				$filter[$k] = $v;
			}
		}
		
		$dbf = new DBFilter('catalog');
		$dbf->setFilter($filter, false);
		$dbf->setFolderRecursive(true);
		
		$query = $dbf->getQuery();
		
		$query['fields'] = 'B.name,B.id,count(IFNULL(P.id,T.id)) as items_num';
		$query['tables'] .= ',brands as B';
		$query['tables'] = '('.$query['tables'].') left join catalog_packages as P on P.product_id=T.id';
		$query['where'] .= ' and B.id=T.brand';
		$query['group_by'] = 'B.id';
		
		$res = $db->query('
			select '.$query['fields'].'
			from ('.$query['tables'].')
			where '.$query['where'].'
			group by '.$query['group_by'].'
			order by items_num desc,B.name'
		);
		
		return $res;		
		
	}
	
	/**
	 * @param array $filter_all
	 * @return DBResult
	 */
	function getFoldersByFilter($filter_all){
		
		global $db;
		
		$avail_filter = array('brand', 'catalog_item_purposes');
		$filter = array();
		foreach($filter_all as $k => $v){
			if(in_array($k, $avail_filter)){
				$filter[$k] = $v;
			}
		}
		
		$dbf = new DBFilter('catalog');
		$dbf->setFilter($filter, false);
		$dbf->setFolderRecursive(true);
		
		$query = $dbf->getQuery();
		
		$query['fields'] = 'F.name,F.id,count(IFNULL(P.id,T.id)) as items_num';
		$query['tables'] .= ',folders as F';
		$query['tables'] = '('.$query['tables'].') left join catalog_packages as P on P.product_id=T.id';
		$query['where'] .= ' and F.id=T.folder_id';
		$query['group_by'] = 'F.id';
		
		$res = $db->query('
			select '.$query['fields'].'
			from ('.$query['tables'].')
			where '.$query['where'].'
			group by '.$query['group_by'].'
			order by items_num desc,F.name'
		);
		
		return $res;		
		
	}
	
	/**
	 * @param array $filter_all
	 * @return DBResult
	 */
	function getTagsByFilter($filter_all){
		
		global $db;
		
		$avail_filter = array('folder_id', 'catalog_item_purposes', 'brand');
		$filter = array();
		foreach($filter_all as $k => $v){
			if(in_array($k, $avail_filter)){
				$filter[$k] = $v;
			}
		}
		
		$dbf = new DBFilter('catalog');
		$dbf->setFilter($filter, false);
		$dbf->setFolderRecursive(true);
		
		$query = $dbf->getQuery();
		
		$query['fields'] = 'tags.name,tags.id,count(IFNULL(P.id,T.id)) as items_num';
		$query['tables'] .= ',tags,catalog_tags_elements';
		$query['tables'] = '('.$query['tables'].') left join catalog_packages as P on P.product_id=T.id';
		$query['where'] .= ' and catalog_tags_elements.item_id=T.id and tags.id=catalog_tags_elements.tag';
		$query['group_by'] = 'tags.id';
		
		$res = $db->query('
			select '.$query['fields'].'
			from ('.$query['tables'].')
			where '.$query['where'].'
			group by '.$query['group_by'].'
			order by tags.name'
		);
		
		return $res;		
		
	}
	
	function updatePricesByOffers(){
		
		global $db;
		
		$filter = array();
		
		$fo = new DBFilter('catalog');
		$fo->setFilter($filter, false);		
		$fo->setFolderRecursive(true);
		
		$q = $fo->getQuery();
		
		$q['fields'] .= ',min(IF('.str_replace(',','.',SiteCurrency::getInstance()->getCourse()).' = CR.course, OF.price, OF.price*CR.course/'.(SiteCurrency::getInstance()->getCourse() > 0 ? SiteCurrency::getInstance()->getCourse() : 1).')) as price_min';
		$q['fields'] .= ',max(IF('.str_replace(',','.',SiteCurrency::getInstance()->getCourse()).' = CR.course, OF.price, OF.price*CR.course/'.(SiteCurrency::getInstance()->getCourse() > 0 ? SiteCurrency::getInstance()->getCourse() : 1).')) as price_max';		
		$q['fields'] .= ',P.id as package_id';
		$q['tables'] .= ' left join catalog_packages as P on P.product_id=T.id';		
		$q['tables'] .= ' left join partners_offers as OF on OF.product_id=T.id and (P.id is NULL and OF.package=0 or P.id is not null and OF.package=P.id) left join currency as CR on CR.id=OF.currency';		
			
		$q['group_by'] = 'T.id,P.id';
		if(isset($filter['id']))
			$q['group_by'] = 'T.id';
		
		//$order_by = '(price_min is null and price_max is null),'.$order_by;
		
		$res = $GLOBALS['db']->query(
			'select '.$q['fields'].'
			from ('.$q['tables'].')
			where '.$q['where'].
			(isset($q['group_by']) ? ' group by '.$q['group_by'] : '')
		);
		
		$counter = 0;
		while($v = $res->getNext()){
			if($v['package_id'])
				$r = $db->query('update catalog_packages set price_min='.intval($v['price_min']).',price_max='.intval($v['price_max']).' where id='.$v['package_id']);
			else
				$r = $db->query('update catalog set currency=1,price_min='.intval($v['price_min']).',price_max='.intval($v['price_max']).' where id='.$v['id']);
			$counter++;
		}
		return $counter;
		
	}
	
	function getPropertyValues($property_id){
		
		global $db;
		
		$res = $db->query('
			select PV.*
			from 
			catalog_item_properties as IP,catalog_property_values as PV
			where IP.property_id='.intval($property_id).' and IP.value=PV.id
			group by PV.id
			order by PV.name
		')->fetchArray();
		
		return $res;
		
	}
	
	function getMainPackage($product){
		
		global $db;
		
		if(!$product['is_main_package']){
			$res = $db->query('select * from catalog where brand='.$product['brand'].' and model="'.mysql_escape_string($product['model']).'" and is_main_package=1')->getNext();
			if($res)
				return $res;
		}
		
		return $product;
		
	}
	
	function getComments($product_id, $limit = 0){
		
		global $db;
		
		$list = $db->query(
			'select 
				ifnull(C.author_name, U.name) as author_name,
				ifnull(C.author_email, U.email) as author_email,
				ifnull(C.author_city, U.address_city) as author_city,
				C.*
			from 
				catalog_comments as C 
			left join 
				users as U
				on 
				U.id=C.user_id
			where 
				product_id='.intval($product_id).'
				and 
				active=1
			order by 
				id desc'.
			($limit ? ' limit '.$limit : '')
		);
		
		return $list;
		
	}
	
	function getProductFlavors($id){
		return $GLOBALS['db']->query('select F.*,C.quantity,C.barcode,C.id,C.flavor_id from flavors as F,catalog_products_flavors as C where C.quantity>0 and C.product_id='.$id.' and C.flavor_id=F.id order by F.name')->fetchArray();		
	}
	
	function addLastSeenProduct($id){
		$products = self::getLastSeenProducts();
		if(!in_array($id, $products)){
			if(count($products) >= self::$lastSeenSize){
				while(count($products) >= self::$lastSeenSize){
					array_pop($products);
				}
			}
			array_unshift($products, $id);
			setcookie('last_seen_products', implode('|', $products), time() + (86400*360), '/', ($_SERVER['REMOTE_ADDR'] != $_SERVER['SERVER_ADDR'] ? '.'.str_replace('www.', '', $_SERVER['SERVER_NAME']) : ''));
		}
	}
	
	function getLastSeenProducts($limit = 0){
		$products = array();
		if(isset($_COOKIE['last_seen_products'])){
			$products = explode('|', $_COOKIE['last_seen_products']);
		}
		return $limit ? array_slice($products, 0, $limit) : $products;
	}
	
	public static function findProductVariant($product_id, $params){
		
		global $db;
		
		$q = array();
		
		$avail_params = array(
			'color',
			'lens_d',
			'lens_cyl',
			'lens_bc',
			'lens_ax'
		);
		
		foreach($avail_params as $k){
			if(isset($params[$k]) && $params[$k] !== '')
				$q[] = $k.'='.floatval($params[$k]);
			else
				$q[] = $k.' IS NULL';
		}
		
		$res = $db->query('
			select 
				* 
			from 
				catalog_item_variants 
			where 
				item_id='.intval($product_id).'
				and
				'.implode(' and ', $q).'
		');
		
		return $res->selectedRowsCount() == 1 ? $res->getNext('id') : false;
		
	}
	
	function buildLensTitle($data){
		
		$avail_params = array(
			'variant_article' => 'Арт.',
			'variant_name' => '',
			'lens_color_name' => '',
			'lens_d' => 'D',
			'lens_cyl' => 'CYL',
			'lens_bc' => 'BC',
			'lens_ax' => 'AX'
		);
		
		$result = array();
		
		foreach($avail_params as $p => $n){
			if(isset($data[$p]) && $data[$p] != ''){
				$result[] = trim($n.' '.$data[$p].'');
			}
		}
		
		return implode(', ', $result);
		
	}
	
	function getCertificates($product_id){
		
		global $db;
		
		$list = array();
		
		$res = $db->query('
			select
				G.id
			from 
				catalog_certificates_groups as G,
				catalog_item_properties as P
			where
				G.list_element_id = P.list_element_id
				and
				P.item_id = '.intval($product_id).'
			group by
				G.id
		');
		
		while($v = $res->getNext()){
			
			$list = array_merge(
				$list,
				$db->query('select * from catalog_certificates where group_id='.$v['id'].' order by pos')->fetchArray()
			);
			
		}
		
		return $list;
		
	}
	
	function isInFavorites($product_id){
		
		global $db;
		
		$in = false;
		if(AuthUser::getInstance()->isAuthorized()){
			if(self::$favorites === null){
				self::$favorites = self::getFavorites();
			}
			$in = is_array(self::$favorites) && in_array($product_id, self::$favorites);
		}
		
		return $in;
		
	}
	
	function getFavorites(){
		
		global $db;
		
		if(AuthUser::getInstance()->isAuthorized()){
			if(self::$favorites === null){
				self::$favorites = array();
				$res = $db->query('select * from catalog_favorites where user_id='.AuthUser::getInstance()->getId().' order by id desc');
				while($v = $res->getNext()){
					self::$favorites[] = $v['product_id'];
				}
			}			
		}
		
		return is_array(self::$favorites) ? self::$favorites : array();
		
	}
	
	function getCompare(){
		
		global $db;
		
		if(AuthUser::getInstance()->isAuthorized()){
			if(self::$compare === null){
				self::$compare = array();
				$res = $db->query('select * from catalog_compare where user_id='.AuthUser::getInstance()->getId().' order by id desc');
				while($v = $res->getNext()){
					self::$compare[] = $v['product_id'];
				}
			}			
		}
		
		return is_array(self::$compare) ? self::$compare : array();
		
	}
	
	function isInCompare($product_id){
		
		global $db;
		
		$in = false;
		if(AuthUser::getInstance()->isAuthorized()){
			if(self::$compare === null){
				self::$compare = self::getCompare();
			}
			$in = is_array(self::$compare) && in_array($product_id, self::$compare);
		}
		
		return $in;
		
	}
	
}

?>