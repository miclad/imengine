<?

class Comments{
	
	/**
	 * List
	 *
	 * @param array $filter
	 * @param string $order_by
	 * @param string $limit
	 * @return DBResult
	 */
	function getList($filter = null, $order_by = '', $limit = 0){
		
		global $db;
		
		$q = '1';
		$left_join = ' users as U on U.id=N.created_by';
		$fields  = ',U.name as name_from,U.orig_ext';
			
		if(isset($filter['created_by']))
			$q .= ' and created_by='.intval($filter['created_by']);		
			
		if(isset($filter['object']) && isset($filter['object_id']))
			$q .= ' and '.$filter['object'].'_id='.intval($filter['object_id']);
			
		if(isset($filter['parent']))
			$q .= ' and parent='.intval($filter['parent']);
			
		if(isset($filter['id']))
			$q .= ' and N.id='.intval($filter['id']);
			
		if(isset($filter['to_id']) && intval($filter['to_id']) > 0)
			$q .= ' and N.id<'.intval($filter['to_id']);
			
		if(!$order_by)
			$order_by = 'N.created_at desc';
			
		$limit = intval($limit);
		
		$res = $db->query('
			select N.*'.$fields.'
			from comments as N'.($left_join ? ' left join '.$left_join : '').'
			where '.$q.'
			order by '.$order_by.
			($limit ? ' limit '.$limit : '')
		);
		
		return $res;
		
	}
	
	function getById($id){
		
		return self::getList(array('id' => $id))->getNext();
		
	}
	
}

?>