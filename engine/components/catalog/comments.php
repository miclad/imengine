<?

if($this->getConfig('product_id')){
	
	$comments = Catalog::getComments($this->getConfig('product_id'), $this->getConfig('limit'), $this->getConfig('order_by'), $this->getConfig('with_text'), $this->getConfig('id'));
	if(!$this->getConfig('limit'))
		$comments->startPager(20);	

	$product_data = $db->query('select * from catalog where id='.intval($this->getConfig('product_id')))->getNext();
	
	if(Site::isAjaxRequest()){
		$this->setActionTemplate('comments_list');
	}
	
}else{
	
	$folders = array();
	
	if($this->getConfig('folder_id')){
		$folders = array(intval($this->getConfig('folder_id')));
		$tree = new Tree('folders', '1');
		$sub = $tree->getFullTree($folders[0]);
		foreach($sub as $v){
			$folders[] = $v['id'];
		}
	}
	
	$query = '
		select 
			ifnull(C.author_name, U.name) as author_name,
			ifnull(C.author_email, U.email) as author_email,
			ifnull(C.author_city, U.address_city) as author_city,
			C.*,
			CAT.model,
			B.name as brand_name,
			F.url as folder_url,
			CAT.url
		from 
			(
				catalog_comments as C,
				catalog as CAT,
				brands as B,
				folders as F
			)
		left join 
			users as U 
			on 
			U.id=C.user_id
		where 
			C.product_id=CAT.id 
			and 
			B.id=CAT.brand 
			and 
			C.active=1 
			and 
			F.id=CAT.folder_id
			'.(count($folders) ? 'and CAT.folder_id in ('.implode(',', $folders).')' : '').'
		order by 
			C.created_at desc'.
		($this->getConfig('limit') ? ' limit '.$this->getConfig('limit') : '').' 
		'
	;
	
	$comments = $db->query($query);
	
}

?>