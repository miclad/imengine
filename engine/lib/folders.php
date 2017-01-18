<?

class Folders{
	
	static function getFoldersByItemsFilter($entity, $filter, $extra_q = 1){
		
		global $db;
		
		$folders = array();
		
		/* get folders list */
		$folder_ids = array();
		$folder_ids_res = $db->query('select folder_id from '.$entity.' where '.$filter.' group by folder_id');
		while($v = $folder_ids_res->getNext()){
			$folder_ids[] = $v['folder_id'];
		}
		
		if(count($folder_ids)){
			$folders = $db->query('select * from folders where id in ('.implode(',', $folder_ids).') and '.$extra_q.' order by parent,pos')->fetchArray();
		}
		
		return $folders;		
		
	}
	
	static function getFoldersTreeByItemsFilter($entity, $filter = array(), $q = '1', $parent = 0, $level_limit = -1, $cur_section = 0, $only_brench = false){
		
		$folders_res = array();
		
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
		
		$tree = new Tree('folders', 'entity = "'.$entity.'" and '.$q);
		if($cur_section){
			$level_limit = -1;
			$tree->setCurrentElement($cur_section, $only_brench);
		}
		$folders = $tree->getFullTree($parent, 0, $level_limit);
		
		for($i=0; $i<count($folders); $i++){
			if(!$use_filter || self::hasItemsInFolder($entity, $folders[$i]['id'], $filter)){
				$folders_res[] = $folders[$i];
			}
		}
		return $sections_res;
		
	}
	
	static function hasItemsInFolder($entity, $folder_id, $filter){	
		
		$db = $GLOBALS['db'];
		$dt_ob = new DataTable($entity);
		$dt_ob->setFilter(array_merge($filter, array('folder_id' => $folder_id)), false);
		$e_num = $dt_ob->getElementsNum();
		if($e_num){
			return true;
		}else{
			$res = $db->query('select * from folders where entity="'.$entity.'" and parent='.$folder_id);	
			while($t = $res->getNext()){
				$n = self::hasItemsInFolder($t['id'], $filter);
				if($n)
					return true;
			}
		}	
		return false;
				
	}
	
	static function getDataByUrl($entity, $url){
		
		global $db;
		
		$data = false;
		$url = preg_replace("/^\/*(.*?)\/*$/", "$1", $url);
		
		if($url != ''){
			
			$t_url = explode('/', $url);
			
			/* check section */
			$res = $db->query('select * from folders where url="/'.mysql_escape_string($url).'" and entity="'.mysql_escape_string($entity).'"')->getNext();
			if($res){
				$data['type'] = 'folder';
				$data['data'] = $res;
			}else{
				$sct_url = count($t_url) > 1 ? '/'.implode('/', array_slice($t_url, 0, count($t_url)-1)) : '';
				$item_url = $t_url[count($t_url)-1];
				$res = false;
				if($sct_url == ''){
					$res = $db->query('select * from '.$entity.' where '.(is_numeric($item_url) ? 'id='.intval($item_url) : 'url="'.mysql_escape_string($item_url).'"').' limit 1')->getNext();
				}else{
					$res = $db->query('
						select I.* 
						from '.$entity.' as I,folders as F 
						where I.folder_id=F.id and F.entity="'.mysql_escape_string($entity).'" 
						and '.(false && is_numeric($item_url) ? 'I.id='.intval($item_url) : 'I.url="'.mysql_escape_string($item_url).'"').' and F.url="'.mysql_escape_string($sct_url).'" limit 1')->getNext();
				}
				if($res){
					$data['type'] = 'item';
					$data['data'] = $res;
				}
			}
			
		}
		
		return $data;
		
	}
	
}

?>