<?
class DBCommon{
	
	private static $configs = array();
	
	function getMaxPos($table, $where="1"){
		$pos=0;
		$res=$GLOBALS['db']->query("select max(pos) as maxpos from ".$table." where ".$where)->getNext();
		if(is_numeric($res['maxpos']))
			$pos = $res['maxpos'] + 10;
		return $pos;
	}
	
	function getById($table,$fields,$id){
		$res=$GLOBALS['db']->query("select ".$fields." from ".$table." where id='".$id."'");
		if($res->selectedRowsCount())
			return $res->getNext();
		return null;
	}
	
	function getFromBase($what,$from,$where,$order="id",$assoc=true){
		$arr = array();
		if($where=="")
			$where="1";
		if(trim($what)!=""&&trim($from)!=""&&trim($where)!=""&&trim($order)!=""){			
			$result = $GLOBALS['db']->query("select ".$what." from ".$from." where ".$where." order by ".$order);
			if($result->SelectedRowsCount()>0){
				while($tmp = $result->getNext())
					$arr[] = $assoc ? $tmp : array_values($tmp);
			}
		}
		return $arr;
	}

	function getConfig($name){
		if(!isset(self::$configs[$name])){
			$val = '';
			$d = $GLOBALS['db']->query("select value from configs where name='".mysql_escape_string($name)."'");
			if($t = $d->getNext()){
				$val = $t['value'];
			}
			self::$configs[$name] = $val;
		}
		return self::$configs[$name];
	}

	function showBanner($ban){
		if($ban["code"]!="")
			return $ban["code"];
		$link = "";
		if($ban["link"]!="")
			$link = $ban["link"];
		return DBCommon::show_image("/img/ab/",$ban["file"],$ban["width"],$ban["height"],$link,$ban['new_window']);
	}

	function show_image($path,$name,$width=0,$height=0,$link="", $new_window = false){
		$str = "";
		if(preg_match("/.+?\.swf/",$name)){
			if($width == 0 || $height == 0){
				$is = getimagesize(B_DIR.$path.$name);
				$width = $is[0];
				$height = $is[1];
			}
			$str = '
				<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0" width="'.$width.'" height="'.$height.'" align="middle">
									<param name="allowScriptAccess" value="sameDomain" />
									<param name="movie" value="'.$path.$name.'" />
									<param name="quality" value="high" />
									<param name="wmode" value="transparent" />
									<embed src="'.$path.$name.'" quality="high" wmode="transparent" bgcolor="#ffffff" width="'.$width.'" height="'.$height.'" align="middle" allowScriptAccess="sameDomain" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" />
									</object>
			';
		}else{
			if($link!="")
				$str.="<a href='".$link."' ".($new_window ? "target='_blank'" : '').">";
			$str.="<img src='".$path.$name."' align='absmiddle' ".($width ? " style='max-width: ".$width."px;'" : "")."/>";
			if($link!="")
				$str.="</a>";
		}
		return $str;
	}

	function inPath($id){
		foreach($GLOBALS["path"] as $v)
			if($v["id"] == $id)
				return true;
		return false;
	}

	function getMenu($parent = 0, $navi_type = ""){
		$PAGE = Page::getInstance();
		$w = "parent=".$parent;
		if($parent == 0 && $navi_type != "")
			$w .= " and navi_type='" .$navi_type."'";
		$arr = DBCommon::getFromBase("pict,pict_hover,id,name,hard_link,url,main,IF((hard_link = ''), CONCAT(url, '/'), hard_link) as link","structure",$w." and visible=1","pos");
		foreach($arr as $k => $v){
			$arr[$k]['is_current'] = $PAGE->getRootId() == $v['id'] || $PAGE->getId() == $v['id'];
		}
		return $arr;
	}
	
	function getPageAddr($id){
		if(isset($GLOBALS["pages_addr"]["page".$id]))
			return $GLOBALS["pages_addr"]["page".$id];
		$tree = new Tree("structure");
		$path = $tree->getPath("u_name",$id);
		$str = "/";
		foreach($path as $v)
			$str .= $v["u_name"]."/";
		$GLOBALS["pages_addr"]["page".$id] = $str;
		return $str;
	}
	
	function getMultilangRows($table, $main_fields = '*', $lang_fields = '*', $q = '', $order = '', $limit = 0, $left_join = '', $extra_fields = '', $group_by = ''){
		
		$fields = '';
		
		if($main_fields == '*')
			$fields .= 'M.*';
		elseif($main_fields != ''){
			$tf = explode(',',$main_fields);
			foreach($tf as $f)
				$fields .= ($fields != '' ? ',' : '').'M.'.$f;
		}
		if($lang_fields == '*')
			$fields .= 'L.*';
		elseif($lang_fields != ''){
			$tf = explode(',',$lang_fields);
			foreach($tf as $f)
				$fields .= ($fields != '' ? ',' : '').'L.'.$f;
		}
		
		$arr = array();
		
		if($fields != ''){
			$tables = $table.' as M';
			$where = '';
			if($lang_fields != ''){
				$tables .= ' left join '.$table.'_lang as L on M.id=L.entry_id and L.lang='.LANG;
			}
			
			if($q != '')
				$where .= ($where != '' ? ' and ' : '').'('.$q.')';
				
			if($left_join != ''){
				$tables .= ' left join '.$left_join;				
			}
			if($extra_fields != ''){
				$fields .= ','.$extra_fields;
			}
			if($group_by != ''){
				$where .= ' group by '.$group_by;
			}
			
			$res = $GLOBALS['db']->query('select '.$fields.' from '.$tables.' where '.$where.($order != '' ? ' order by '.$order : '').($limit ? ' limit '.$limit : ''));
			//echo 'select '.$fields.' from '.$tables.' where '.$where.($order != '' ? ' order by '.$order : '').($limit ? ' limit '.$limit : '').'<br>';
			//echo mysql_error().'<br><br>';
			while($t = $res->getNext()){
				$arr[] = $t;
			}
		}
		
		return $arr;
		
	}
	
	function getPreviousItem($table, $field, $cur_value, $cur_id, $q = '1', $is_text = false){
		
		global $db;
		
		$id = 0;
		if($is_text){
			$cur_value = '"'.$cur_value.'"';
		}
		$res = $db->query('select id from '.$table.' where '.$field.'<='.$cur_value.' and id != '.$cur_id.' and '.$q.' order by '.$field.' desc,id desc limit 1')->getNext();
		if($res){
			$id = $res['id'];
		}
		
		return $id;
	}
	
	function getNextItem($table, $field, $cur_value, $cur_id, $q = '1', $is_text = false){
		
		global $db;
		
		$id = 0;
		if($is_text){
			$cur_value = '"'.$cur_value.'"';
		}
		$res = $db->query('select id from '.$table.' where '.$field.'>='.$cur_value.' and id != '.$cur_id.' and '.$q.' order by '.$field.',id limit 1')->getNext();
		if($res){
			$id = $res['id'];
		}
		
		return $id;
		
	}
	
	static function getFoldersByElementsFilter($entity, $filter = array(), $parent = 0, $level_limit = -1){
		
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
		
		$tree = new Tree('folders', 'entity = "'.$entity.'"');
		$sections = $tree->getFullTree($parent, 0, $level_limit);
		for($i=0; $i<count($sections); $i++){
			if(!$use_filter || self::hasElementsInFolder($entity, $sections[$i]['id'], $filter)){
				$sections_res[] = $sections[$i];
			}
		}
		return $sections_res;
		
	}
	
	function hasElementsInFolder($entity, $section_id, $filter){		
		
		$db = $GLOBALS['db'];
		$dt_ob = new DataTable($entity);
		$dt_ob->setFilter(array_merge($filter, array('folder_id' => $section_id)), false);
		$e_num = $dt_ob->getElementsNum();
		if($e_num){
			return true;
		}else{
			$res = $db->query('select * from folders where entity="'.$entity.'" and parent='.$section_id);	
			while($t = $res->getNext()){
				$n = self::hasElementsInFolder($entity, $t['id'], $filter);
				if($n)
					return true;
			}
		}	
		return false;				
	}

}