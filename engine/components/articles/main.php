<?

$folder_id = !$this->is_inner && $this->getInitParam('folder_id') ? intval($this->getInitParam('folder_id')) : intval($this->getConfig('folder_id'));
$q = '1';
if($folder_id){
	$folders_array = array($folder_id);
	$tree = new Tree('folders');
	$ch = $tree->getFullTree($folder_id);
	foreach($ch as $v){
		$folders_array[] = $v['id'];
	}
	$q = count($folders_array) > 1 ? 'folder_id in ('.implode(',',$folders_array).')' : 'folder_id = '.$folder_id;
	
	$folder_data = $db->query('select * from folders where id='.$folder_id)->getNext();
	if($folder_data){
		$section_descr = $folder_data['description'];
	}
	
}

$section_name = 'Новости';

if($this->getConfig('template') != ''){
	$this->setTemplate($this->getConfig('template'));
}

if($this->getConfig('show_rand') || $this->getConfig('id') || (!$this->is_inner && is_numeric($this->getInitParam('uid')))){
	
	if($this->getConfig('show_rand')){
		$res = $db->query('select * from articles where '.$q.' order by rand() limit 1');
		$this->setActionTemplate('details');
	}else{
		$id = $this->getConfig('id') ? intval($this->getConfig('id')) : intval($this->getInitParam('uid'));
		$res = $db->query('select * from articles where id='.$id.' and '.$q);
		$this->setActionTemplate('details');
	}	
	
	if($data = $res->getNext()){
		
		if(is_numeric($this->getConfig('shorten'))){
			$data['content'] = Common::cropText($data['content'], $this->getConfig('shorten'));
		}
		
		/*
		if($data['folder_id'] && !$this->is_inner){
			$tree = new Tree('folders', 'entity="catalog"');
			$p = $tree->getPath('*', $data['folder_id']);
			if(count($p) > 1){
				$PAGE->addItemToPath($p[count($p)-1]['name'], $PAGE->getUrl().'/'.$p[count($p)-1]['id'].'/');
			}
		}
		*/

		if(!$this->is_inner){
			$PAGE->clearContent();
			$PAGE->setHeadTitle($data['title']);
			//$PAGE->setTitle('<span>'.Site::formatDate($data['date']).'</span>'.$data['title']);
			//$PAGE->addItemToPath($data['title'], '');
		}
		
		if($data['html_title'])
			$PAGE->setHeadTitle($data['html_title']);
		if($data['html_keywords'])
			$PAGE->setMetaKeywords($data['html_keywords']);
		if($data['html_description'])
			$PAGE->setMetaDescription($data['html_description']);
		
		$comments = Page::includeComponent('comments', array('entity' => 'articles', 'id' => $data['id'], 'limit' => 3), array(), $this->getInitParam('action') ? $this->getInitParam('action') : 'comments');
		
		$PAGE->addChild('Список новостей', $PAGE->getUrl().'/');
		$PAGE->addItemToPath($data['title']);
		
		//$data['props'] = DataTable::getElementProperties($data['id']);
		//$data['photos'] = $db->query('select * from articles_photos where item_id='.$data['id'].' order by pos');
		
		//$prev = DBCommon::getNextItem('articles', 'date', $data['date'], $data['id'], 'folder_id='.$data['folder_id']);
		//$next = DBCommon::getPreviousItem('articles', 'date', $data['date'], $data['id'], 'folder_id='.$data['folder_id']);
		
	}else{
		throw new PageNotFoundException();
	}
	
}else{
	
	$order_by = $this->getConfig('order_by') ? $this->getConfig('order_by') : 'date desc';
	
	$join = '';
	
	if($this->getConfig('is_popular'))
		$q .= ' and is_popular=1';
	
	if($this->getConfig('product_id')){
		$join = 'catalog_articles';
		$q .= ' and catalog_articles.product_id='.intval($this->getConfig('product_id')).' and catalog_articles.article_id=A.id';
	}
		
	$list = $db->query('
		select A.*,IF((F.page_url != "" and F.page_url is not null),F.page_url,"/news") as page_url,F.name as folder_name,
		0 as comments_num
		from (articles as A'.($join ? ','.$join : '').')
		left join folders as F on F.id=A.folder_id		
		where '.$q.' and A.active=1 
		order by '.$order_by.($this->getConfig('limit') ? ' limit '.$this->getConfig('limit') : '')
	);	
	
	/*
	if($folder_id){
		$tree = new Tree('folders', 'entity="catalog"');
		$p = $tree->getPath('*', $folder_id);
		if(count($p) > 1){
			$PAGE->addItemToPath($p[count($p)-1]['name']);
		}
	}
	*/
	
	if(is_numeric($this->getConfig('items_per_page')))
		$list->startPager($this->getConfig('items_per_page'));
	
}
?>