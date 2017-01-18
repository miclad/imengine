<?

$folder_id = intval($this->getConfig('folder_id', false));
$brand = intval($this->getConfig('brand', false));
$product_ids = is_array($this->getConfig('ids')) ? array_map('intval', $this->getConfig('ids')) : null;

if($brand){
	
	$brand_data = $db->query('select * from brands where id='.$brand)->getNext();
	if($brand_data){
		
		$sections = array();
		
		$sections_res = $db->query('
			select F.*,P.name as parent_name,P.parent as parent_parent,P.id as parent_id,P.url as parent_url,P.pos as parent_pos
			from (folders as F,catalog as C) left join folders as P on P.id=F.parent
			where C.folder_id=F.id and C.brand='.$brand.'
			group by F.id
			order by F.name'
		);
		
		while($v = $sections_res->getNext()){
			
			// parent
			if(!$v['parent'] || !$v['parent_parent']){
				if(!isset($sections['parent'.$v['id']]))
					$sections['parent'.$v['id']] = array_merge($v, array('children' => array()));
			}else{
				
				if(!isset($sections['parent'.$v['parent']])){
					$sections['parent'.$v['parent']] = array(
						'name' => $v['parent_name'],
						'id' => $v['parent_id'],
						'url' => $v['parent_url'],
						'children' => array()
					);
				}
				
				$sections['parent'.$v['parent']]['children'][] = $v;
				
			}
			
		}
		
		//echo '<pre>'.print_r($sections, true).'</pre>';
		
		$price_min = $db->query('
			select round(min(C.price*(1-(ifnull(C.discount,0)/100)))) as price 
			from catalog as C
			where 
			C.brand='.$brand.' and C.active=1'
		)->getNext();
		
		$price_max = $db->query('
			select round(max(C.price*(1-(ifnull(C.discount,0)/100)))) as price 
			from catalog as C
			where 
			C.brand='.$brand.' and C.active=1'
		)->getNext();
		
		$properties = array();
		
	}else{
		$this->setActionTemplate('dummy');
	}
	
}elseif($folder_id || $this->getConfig('collective_buy') || $this->getConfig('search') || $product_ids && count($product_ids)){
	
	$cond = array();
	$tables = '';
	
	if($folder_id){
		
		$folder_data = $db->query('select * from folders where id='.$folder_id)->getNext();
		if(!$folder_data || $folder_data['entity'] != 'catalog')
			throw new PageNotFoundException();

		$tree = new Tree('folders');
		$children = array();
		if($PAGE->getAttribute('catalog_filter_categories')){
			$children = $PAGE->getAttribute('catalog_filter_categories');
		}else{
			$children_res = $tree->getFullTree($folder_data['id']);
			foreach($children_res as $v){
				$children[] = $v['id'];
			}
		}
	
	}
	
	if(true){
		
		if($folder_id){
			
			$brands = Catalog::getSectionBrands($PAGE->getAttribute('catalog_filter_categories') ? $children : $folder_id, true);
			
			$f_ids = count($children) ? $children : array($folder_data['id']);

			$f_ids[] = $folder_data['id'];
			
			$cond[] = 'C.folder_id in ('.implode(',', $f_ids).')';
			
		}
		
		if($this->getConfig('collective_buy') || $this->getConfig('search') || $product_ids && count($product_ids)){
			
			$tables = 'brands as B,folders as F';
			
			if($this->getConfig('collective_buy')){
				
				$cond[] = '(B.collective_buy = 1 or F.collective_buy = 1 or C.collective_buy = 1)';
				
			}elseif($this->getConfig('search')){
				
				$search_words = $this->getConfig('search');
				if(!is_array($search_words))
					$search_words = array($search_words);
				
				$q = '';
				
				foreach($search_words as $t){
					$t = mysql_escape_string($t);
					if($q != '')
						$q .= ' and ';
					$q .= '(C.model like "%'.$t.'%" or B.name like "%'.$t.'%" or F.name like "%'.$t.'%")';
				}
				
				if($q)
					$cond[] = '('.$q.')';
				
			}elseif($product_ids && count($product_ids)){
				
				$cond[] = 'C.id in ('.implode(',', $product_ids).')';
				
			}
			
			$brands = $db->query('
				select
					B.*,
					count(C.id) as elements_num
				from 
					brands as B,
					folders as F,
					catalog as C
				where
					C.brand = B.id
					and
					C.folder_id = F.id
					and
					C.active = 1
					and
					'.(count($cond) ? $cond[count($cond)-1] : '1').'
				group by
					B.id
				order by
					B.name					
			')->fetchArray();
			
			$cond[] = 'B.id=C.brand and F.id=C.folder_id';
			
		}	
		
		$brands = Common::arraySortByField($brands, 'elements_num');

		$price_min = $db->query('
			select 
				round(min(C.price*(1-(ifnull(C.discount,0)/100)))) as price
			from 
				catalog as C'.($tables ? ','.$tables : '').'
			where 
				C.active = 1
				and
				'.implode(' and ', $cond)
		)->getNext();

		$price_max = $db->query('
			select 
				round(max(C.price*(1-(ifnull(C.discount,0)/100)))) as price
			from 
				catalog as C'.($tables ? ','.$tables : '').'
			where 
				C.active = 1
				and
				'.implode(' and ', $cond)
		)->getNext();
		
		if(count($cond)){

			if($folder_id){
				$properties = Catalog::getSectionProperties($folder_id, true);
			}else{
				
				$properties = $db->query('
					select
						P.*
					from 
						catalog_properties as P,
						catalog_item_properties as I,
						catalog as C,
						brands as B,
						folders as F
					where
						P.id = I.property_id
						and
						I.item_id = C.id
						and
						P.show_in_filter = 1
						and
						'.implode(' and ', $cond).'
					group by 
						P.id
					order by
						P.name
				')->fetchArray();
				
			}

			for($i=0; $i<count($properties); $i++){

				$variants = array();

				switch($properties[$i]['type']){
					case 'text':

						$properties[$i]['variants'] = $db->query('
							select 
								V.name,V.id,count(V.id) as items_num
							from 
								catalog_property_values as V,
								catalog_item_properties as IP,
								catalog as C'.($tables ? ','.$tables : '').'
							where 
								IP.property_id='.$properties[$i]['id'].'
								and 
								V.id=IP.value 
								and 
								V.name != "" 
								and 
								IP.item_id=C.id 
								and 
								'.implode(' and ', $cond).'
							group 
								by V.id
							order by 
								items_num desc'
						);

					break;
					case 'number':

						$properties[$i]['values'] = $db->query('
							select 
								min(value_int) as min_val, 
								max(value_int) as max_val
							from 
								catalog_item_properties as P,
								catalog as C'.($tables ? ','.$tables : '').'
							where 
								C.id=P.item_id 
								and 
								P.property_id='.$properties[$i]['id'].
								' and '.
								implode(' and ', $cond)								
						)->getNext();

					break;
					case 'checkbox':

						$properties[$i]['variants'] = $db->query('
							select 
								T.name,T.id 
							from 
								catalog_terms as T,
								catalog_property_terms as PT
							where 
								T.id=PT.term_id 
								and 
								PT.property_id='.$properties[$i]['id'].'
							order by 
								PT.pos
						');

					break;
					case 'list':

						$properties[$i]['variants'] = $db->query('
							select 
								L.title as name,L.id,count(L.id) as items_num
							from 
								lists_elements as L,
								catalog_item_properties as IP,
								catalog as C'.($tables ? ','.$tables : '').'
							where 
								IP.property_id='.$properties[$i]['id'].'
								and 
								L.id=IP.list_element_id
								and
								IP.item_id=C.id 
								and 
								'.implode(' and ', $cond).'
							group 
								by L.id
							order by 
								items_num desc'
						);

					break;
				}
			}
			
		}

		$categories_url = '';
		if(isset($_GET['category']) && is_array($_GET['category'])){
			foreach($_GET['category'] as $k => $v){
				$categories_url .= '&category[]='.$v;
			}
		}
		
	}else{
		$this->setActionTemplate('dummy');
	}

}else{
	$this->setActionTemplate('dummy');
}


?>