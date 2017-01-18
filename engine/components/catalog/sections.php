<?

$parent_data = $this->getConfig('parent') ? $db->query('select * from folders where id='.intval($this->getConfig('parent')))->getNext() : false;

$order_by = $this->getConfig('order_by') ? $this->getConfig('order_by') : 'pos';

if($this->getConfig('tree_mode')){
	
	$tree = new Tree('folders', 'entity="catalog" and active=1');
	$list = $tree->getFullTree(intval($this->getConfig('parent')), 0, $this->getConfig('level_limit') !== '' ? intval($this->getConfig('level_limit')) : '', 'M.'.$order_by);
	
}else{
	
	$list = $db->query('
		select
			*
		from 
			folders
		where
			entity = "catalog"
			and
			parent = '.intval($this->getConfig('parent')).'
			and
			active = 1
		order by
			'.$order_by.($this->getConfig('limit') ? ' limit '.intval($this->getConfig('limit')) : '')
	)->fetchArray();
	
	if($this->getConfig('with_children')){
		foreach($list as $k => $v){
			$list[$k]['children'] = $db->query('select * from folders where parent='.$v['id'].' and active=1 order by name');
		}
	}
	
}

?>