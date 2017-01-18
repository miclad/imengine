<?

global $cart_o;

$filter = array('active' => 1);

$folder_recursive = true;
$ipp = intval($this->getConfig('items_per_page'));
//$order_by = 'B.name,T.model,T.price desc';
$order_by = 'T.pos';
$is_search = false;
$extra_cond = '';

if(isset($_GET['order_by']) && in_array($_GET['order_by'], array('p', 'price', 'price_desc', 'name'))){
	switch($_GET['order_by']){
		case 'p':
			$order_by = 'T.pos';
		break;
		case 'price':
			$order_by = 'price_min_real';
		break;
		case 'price_desc':
			$order_by = 'price_min_real desc';
		break;
		case 'name':
			$order_by = 'B.name,T.model,T.price desc';
		break;
	}
	$_SESSION['order_by'] = $order_by;
}

if(isset($_SESSION['order_by'])){
	$order_by = $_SESSION['order_by'];
}

if($this->getConfig('order_by'))
	$order_by = $this->getConfig('order_by');

$section_name = '';
$section_default_pict = '';
if(intval($this->getInitParam('folder_id')) || intval($this->getInitParam('category'))){
	$val = intval($this->getInitParam('folder_id')) ? intval($this->getInitParam('folder_id')) : intval($this->getInitParam('category'));
	$filter['folder_id'] = intval($val);
	if(!$this->is_inner || $this->getInitParam('a') == 'brands')
		$PAGE->setAttribute('current_section', $filter['folder_id']);
		
	$tree = new Tree('folders');
	$f_tree = $tree->getPath('*', $filter['folder_id']);
	$section_data = false;
	if(count($f_tree))
		$section_data = $f_tree[count($f_tree)-1];
	if($section_data)
		$section_name = $section_data['name'];

	$section_descr = '';
	for($i=count($f_tree)-1; $i>=0; $i--){
		if($f_tree[$i]['description'] != '')
			$section_descr = $f_tree[$i]['description'];
		if($f_tree[$i]['orig_ext'] != '')
			$section_default_pict = '/img/folders/lc'.$f_tree[$i]['id'].'.jpg';
		if($section_default_pict && $section_descr)
			break;
	}
		
}

if(is_array($this->getInitParam('category'))){
	$ids = array();
	$gt = $this->getInitParam('category');
	foreach($gt as $v){
		if(is_numeric($v) && $v > 0)
			$ids[] = intval($v);
	}
	if(count($ids)){
		$filter['folder_id'] = $ids;
		$PAGE->setAttribute('catalog_filter_categories', $ids);
	}
}

if(is_numeric($this->getInitParam('brand'))){
	$filter['brand'] = intval($this->getInitParam('brand'));
	$brand_data = $db->query('select * from brands where id='.intval($this->getInitParam('brand')))->getNext();
	if($brand_data){
		$PAGE->setAttribute('current_brand', $filter['brand']);
		$PAGE->setHeadTitle($PAGE->getHeadTitle().' '.$brand_data['name']);
	}
}

if(is_array($this->getInitParam('brand'))){
	$ids = array();
	$gt = $this->getInitParam('brand');
	foreach($gt as $v){
		if(is_numeric($v) && $v > 0)
			$ids[] = intval($v);
	}
	if(count($ids)){
		$filter['brand'] = $ids;
		$PAGE->setAttribute('catalog_filter_brands', $ids);
	}
}

if(is_numeric($this->getConfig('avail'))){
	$filter['avail'] = intval($this->getConfig('avail'));
}

if(is_numeric($this->getConfig('special'))){
	$filter['catalog_special_products'] = $this->getConfig('special');
}

if(is_numeric($this->getInitParam('purpose')) || is_array($this->getInitParam('purpose'))){
	$filter['catalog_item_purposes'] = $this->getInitParam('purpose');
	if(is_numeric($this->getInitParam('purpose'))){
		$pd = $db->query('select * from catalog_purposes where id='.intval($this->getInitParam('purpose')))->getNext();
		if($pd){
			$PAGE->setTitle($pd['name']);
		}
		$PAGE->setAttribute('current_purpose', $this->getInitParam('purpose'));
	}else
		$PAGE->setAttribute('catalog_filter_purposes', $this->getInitParam('purpose'));
}

if(intval($this->getInitParam('tag'))){
	$PAGE->setAttribute('catalog_current_tag', $this->getInitParam('tag'));
	$filter['catalog_tags_elements'] = intval($this->getInitParam('tag'));
}

if(intval($this->getConfig('is_top'))){
	$filter['is_top'] = intval($this->getConfig('is_top'));
}

if(is_numeric($this->getConfig('article_id'))){
	$folder_recursive = true;
	$filter['articles_products'] = $this->getConfig('article_id');
	unset($filter['avail']);
}

if(is_array($this->getConfig('exclude_ids')) && count($this->getConfig('exclude_ids'))){
	$extra_cond .= ($extra_cond != '' ? ' and ' : '').'T.id not in ('.implode(',', array_map('intval', $this->getConfig('exclude_ids'))).')';
}

$search_words = null;

if($this->getInitParam('search') != ''){
	$search_word = Common::prepareGetString($this->getInitParam('search'));
	$PAGE->clearContent();
	$PAGE->setAttribute('search_word', htmlspecialchars($search_word));
	$PAGE->setTitle('Поиск по запросу &laquo;'.$PAGE->getAttribute('search_word').'&raquo;');
	$is_search = true;	
	$q = '';
	$str = str_replace('%', '', $search_word);
	$str = str_replace('*', '', $str);
	$words_1 = explode(' ',$str);
	$words = array();
	foreach($words_1 as $w1){
		if(strpos($w1, '-') !== false){
			$words = array_merge($words, explode('-', $w1));
		}else{
			$words[] = $w1;
		}
	}
	if(count($words)){
		$alr = false;
		$search_words = $words;
		foreach($words as $t){
			$t = mysql_escape_string($t);
			if($alr)
				$q .= ' and ';
			$q .= '(T.model like "%'.$t.'%" or B.name like "%'.$t.'%" or FLD.name like "%'.$t.'%")';
			$alr = true;
		}
	}
	if($q != '')
		$extra_cond .= ($extra_cond != '' ? ' and ' : '') . $q;	
}

if(is_numeric($this->getConfig('id')) || is_array($this->getConfig('id')) && count($this->getConfig('id'))){
	$filter['id'] = $this->getConfig('id');
}

if($this->getConfig('is_new') != ''){
	$filter['is_new'] = intval($this->getConfig('is_new'));
}

if($this->getConfig('is_sale') != ''){
	$filter['is_sale'] = intval($this->getConfig('is_sale'));
}

if(isset($_GET['filter']['prop']) && is_array($_GET['filter']['prop'])){
	$filter['properties'] = $_GET['filter']['prop'];
}

if(isset($_GET['filter']['section']) && is_array($_GET['filter']['section'])){
	$filter['folder_id'] = $_GET['filter']['section'];
	$ch = array();
	$tree = new Tree('folders');
	foreach($filter['folder_id'] as $v){
		$sub = $tree->getFullTree($v);
		foreach($sub as $s){
			$ch[] = $s['id'];
		}
	}
	$filter['folder_id'] = array_merge($filter['folder_id'], $ch);
}

if(isset($_GET['filter']['price_min']) && is_numeric($_GET['filter']['price_min'])){
	$filter['price_min'] = $_GET['filter']['price_min'];
}
	
if(isset($_GET['filter']['price_max']) && is_numeric($_GET['filter']['price_max'])){
	$filter['price_max'] = $_GET['filter']['price_max'];
}

if($this->getConfig('related_to')){
	//$filter['catalog_related_products'] = intval($this->getConfig('related_to'));
	
	$p_data = $db->query('select * from catalog where id='.intval($this->getConfig('related_to')))->getNext();
	if($p_data){

		$rel_folders = array();
		$rel_products = array();
		$rel_cond = array();
		$rel_order_by = '';

		// rel folders
		$rel_tree = new Tree('folders');
		$rel_folders_path = $rel_tree->getPath('id', $p_data['folder_id']);
		foreach($rel_folders_path as $_v){
			$_t_rel_folders = $db->query('select folder2 from catalog_related_folders where folder1='.$_v['id'].' order by pos')->fetchArray('folder2');
			$rel_folders = array_merge($rel_folders, $_t_rel_folders);
		}
		
		// rel products
		$rel_products = $db->query('select product2 from catalog_related_products where product1='.$p_data['id'].' order by pos')->fetchArray('product2');
		
		$rel_folders = array_unique($rel_folders);
		
		if(count($rel_products)){
			$rel_cond[] = 'T.id in ('.implode(',', $rel_products).')';
			$_ob = array();
			foreach($rel_products as $_ob_id){
				$_ob[] = 'T.id != '.$_ob_id;
			}
			$rel_order_by = implode(',', $_ob);
		}
		
		if(count($rel_folders)){
			$rel_cond[] = 'T.folder_id in ('.implode(',', $rel_folders).')';
		}
		
		$rel_order_by = $rel_order_by.($rel_order_by ? ',' : '').'rand()';
		
		$extra_cond = ($extra_cond ? $extra_cond.' and ' : '').implode(' or ', $rel_cond);
		$order_by = $rel_order_by;
	
	}
	
}

if($this->getConfig('price') && is_array($this->getConfig('price'))){
	$price = array_map('intval', $this->getConfig('price'));
	if(isset($price['min']) && $price['min'] > 0)
		$extra_cond = ($extra_cond ? $extra_cond.' and ' : '').'T.price >= '.$price['min'];
	if(isset($price['max']) && $price['max'] > 0)
		$extra_cond = ($extra_cond ? $extra_cond.' and ' : '').'T.price <= '.$price['max'];
	
}

if($this->getConfig('collective_buy')){

	$collective = array(
		'B.collective_buy=1',
		'FLD.collective_buy=1',
		'T.collective_buy=1'
	);
	
	$extra_cond = ($extra_cond ? $extra_cond.' and ' : '').implode(' or ', $collective);
	
	$filter['price_type'] = intval(DBCommon::getConfig('collective_price_type'));
	
}

if($this->getConfig('favorites')){
	
	$fav = Catalog::getFavorites();

	if(!count($fav))
		$fav = array(0);
	
	$this->setConfig('ids', $fav);
	$this->setconfig('order_by', 'ids');
	
	//$this->setActionTemplate('items_inner');
	
}

if($this->getConfig('compare')){
	
	$comp = Catalog::getCompare();

	if(!count($comp))
		$comp = array(0);
	
	$this->setConfig('ids', $fav);
	$this->setConfig('order_by', 'ids');
	
	$this->setActionTemplate('items_compare');
	
}

if(is_array($this->getConfig('ids'))){
	$filter['id'] = $this->getConfig('ids');
	if($this->getConfig('order_by') == 'ids'){
		$ob = array();
		$ar = array_reverse($this->getConfig('ids'));
		foreach($ar as $v){
			$ob[] = 'T.id != '.$v;
		}
		$order_by = implode(',', $ob);
	}
}

if($this->getConfig('extra_cond')){
	$extra_cond = ($extra_cond ? $extra_cond.' and ' : '').$this->getConfig('extra_cond');
}

$items = Catalog::getElements($filter, $extra_cond, $order_by, $ipp, intval($this->getConfig('limit')), $folder_recursive, true, (is_array($this->getInitParam('prop')) ? $this->getInitParam('prop') : false));

$count = $items->selectedRowsCount();
$count_text = $count ? $count.' '.Common::makeEnding($count, 'товар', '', 'а', 'ов') : '';

if(Site::isAjaxRequest()){
	if(isset($_GET['return_count']) && $_GET['return_count']){
		echo $items->selectedRowsCount(); // возвращаем кол-во найденных товаров для фильтра
		$this->setActionTemplate('dummy');
		$PAGE->clearContent();
		exit();
	}else{		
		
		$this->setConfig('items_per_row', 3);
		
		ob_start();
		require 'templates/'.(isset($_GET['page']) || isset($_GET['show_all_pages']) ? 'items_gallery' : (isset($_GET['filter']) ? 'items_gallery' : 'items')).'.php';
		$content = ob_get_contents();
		ob_end_clean();
		
		if(isset($_REQUEST['return']) && $_REQUEST['return'] == 'json'){
			echo json_encode(
				array(
					'content' => $content,
					'count' => $count,
					'count_text' => $count_text
				)
			);			
		}else{
			echo $content;
		}
		exit();
		
	}
}

if(!$this->is_inner || $this->getConfig('with_filter')){
	if($items->selectedRowsCount() || !$this->getConfig('is_search'))
		$PAGE->setAttribute('sidebar', Page::includeComponent('catalog:filter', array('folder_id' => $PAGE->getAttribute('current_section'), 'collective_buy' => $this->getConfig('collective_buy'), 'search' => $search_words, 'ids' => $this->getConfig('ids'))));
}

?>