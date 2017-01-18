<?
class DataTable{
	
	var $baseUrl;
	var $dataDesc;
	var $curRow = array();
	var $gridCond = array();
	var $boundedEntities = array();
	var $isOk = true;
	var $entity = "";
	var $entityName = "";
	var $entityNameVin = "";
	var $fields;
	
	var $withFolders = false;
	var $saveAuthorId = false;
	var $curFolder = 0;
	
	var $foreignField = "";
	var $foreignValue = 0;
	var $ent;
	var $mpTree = false;
	
	private static $event_handlers = array();
	
	var $errors = array();
	var $isMultilang = false;
	var $multilang_ob = null;
	
	var $post_array = null;
	var $files_in_array = array();
	var $field_name_template = '';
	var $filter_fields =array();
	var $filter = null;
	var $use_pager = null;
	var $pager_ob = null;
	
	private $before_update_data = array();
	private $last_id = 0;

	private $interface_dir = '';
	private $fields_dir = '';
	
	private $escape_mode = false;
	private $fk_text_mode = false;
	
	public $gridResultObject;
	
	
	function DataTable($entity, $gc = '', $init = array(), $files_in_array = array(), $base_url = ''){
		
		if(isset($GLOBALS["entities"][$entity])){
			
			if(!$base_url)
				$base_url = 'index.php?module=datatable&entity=';
			
			$this->interface_dir = MODULES_DIR.'datatable/templates/interface/';
			$this->fields_dir = MODULES_DIR.'datatable/templates/fields/';
			
			$this->files_in_array = $files_in_array;
			$this->dataDesc = &$GLOBALS["entities"][$entity];
			$this->entity=$entity;
			$this->baseUrl = $base_url.$entity;
			$this->entityName = $this->dataDesc[0][0];
			$this->entityNameVin = isset($this->dataDesc[0][1]) ? $this->dataDesc[0][1] : '';
			$this->fields = $this->dataDesc[1];			
			
			$tree_field = $this->isTree();
			$this->mpTree = isset($this->dataDesc["mp_tree"]);
			$this->ent = &$this;
			
			$this->post_array = $_POST;
			
			$init_by = false;
			
			if(isset($GLOBALS['entInits'][$entity])){				
				$init_by = $GLOBALS["entInits"][$entity][0];				
			}
			
			if(isset($this->dataDesc['init_by'])){
				$init_by = $this->dataDesc['init_by'];
			}
			
			if(!is_array($init_by)){
				if($init_by)
					$init_by = array($init_by);
				else
					$init_by = array();
			}
			
			if(isset($this->dataDesc[4]) && $this->dataDesc[4] == 1){
				$this->withFolders = true;
			}
			
			if(count($init_by)){
				
				if(!count($init))
					$init = $_GET;
				
				foreach($init_by as $i){
					
					if(isset($init[$i])){
						$this->foreignField = $i;
						$this->foreignValue = $init[$i];
						break;
					}				
				
				}
				
				if(!$this->foreignField && isset($_GET['id']) && isset($_GET['show']) && in_array($_GET['show'], array('element_form'))){
					$this->foreignField = $init_by[0];
				}
					
			}elseif($this->isTree() && $this->mpTree){
				
				/* if the entity is tree */				
				$this->foreignField = $tree_field;
				$this->foreignValue = isset($_GET[$tree_field]) ? intval($_GET[$tree_field]) : 0;			
				//$this->gridCond[] = $this->foreignField."='".$this->foreignValue."'";
				//$this->baseUrl .= "&".$this->foreignField."=".$this->foreignValue;	
				
				/* get tree level */			
				$tree = new Tree($this->entity);
				$tp = $tree->getPath("id",$this->foreignValue);
				if(isset($this->dataDesc["tree_titles"][count($tp)]))
					$this->entityNameVin = $this->dataDesc["tree_titles"][count($tp)][1];
				
			}
			
			if(isset($_GET['id']) && isset($_GET['entity']) && $_GET['entity'] == $this->entity && isset($_GET['show']) && in_array($_GET['show'], array('element_form'))){
				$this->initCurRow('id='.intval($_GET['id']));
			}
			
			if($this->foreignField){
			
				if($this->foreignValue){
					$this->gridCond[] = $this->foreignField."='".mysql_escape_string($this->foreignValue)."'";
					$this->baseUrl .= "&".$this->foreignField."=".$this->foreignValue;
				}elseif(!$this->isTree() || !$this->mpTree){
					if(!isset($this->dataDesc['allow_no_init']))
						$this->isOk = false;
				}
			
			}
			
			$this->filter_fields = isset($this->dataDesc['filter']) ? $this->dataDesc['filter'] : array();
			$this->filter = new DBFilter($entity, $this);
			if(isset($_GET['_clear_filter']) && $_GET['_clear_filter'] == $this->entity)
				$this->clearFilter();
			$this->use_pager = isset($this->dataDesc['use_pager']) ? $this->dataDesc['use_pager'] : 40;

			$this->_baseUrl = $this->baseUrl;
			
			if($gc!="")
				$this->gridCond[] = $gc;
				
			if($this->withFolders){
				if(isset($_GET['show']) && $_GET['show'] == 'folder_form'){
					$this->ent = new DataTable('folders');				
				}
				if(!$this->curFolder && isset($_GET['folder']) && is_numeric($_GET['folder'])){
					$this->curFolder = $_GET['folder'];
				}			
				$this->gridCond[] = 'folder_id='.intval($this->curFolder);		
				if($this->curFolder){
					$this->baseUrl .= '&folder='.$this->curFolder;
				}				
			}
			
			if(isset($this->dataDesc['save_author_id']) && $this->dataDesc['save_author_id']){
				$this->saveAuthorId = true;
			}
			
			if(isset($this->dataDesc['multilang_entity'])){
				$this->isMultilang = true;
			}
			
		}else
			$this->isOk = false;
	}
	
	function clearFilter(){
		$this->filter->clearFilter();
	}
	
	function setFilesInArray($arr){
		$this->files_in_array = $arr;
	}
	
	function setPostArray($arr){
		$this->post_array = $arr; 
	}
	
	function getContents(){	
		
		if(isset($_GET['show']) && in_array($_GET['show'], array('element_form', 'folder_form'))){
			
			$this->setOutputEscapeMode(true);
			
			if($_GET['show'] == 'folder_form' && $this->ent->entity != 'folders')
				$this->ent = new DataTable('folders');

			if(isset($this->ent->dataDesc[3][1]) && $this->ent->dataDesc[3][1] && file_exists($this->interface_dir.$this->ent->entity.'/add.php'))
				$folder = $this->ent->entity;
			else
				$folder = 'common';

			$page = $this->interface_dir.$folder.'/add.php';
			if(isset($_GET['id']) && is_numeric($_GET['id'])){
				$this->ent->initCurRow('id='.$_GET['id']);
				$path[] = array('Изменить '.$this->ent->entityNameVin, '');
			}else{
				if(isset($_GET['copy_from'])){
					$this->ent->curRow = DBCommon::getById($this->ent->entity, '*', $_GET['copy_from']);
					if(count($this->ent->curRow))
						unset($this->ent->curRow['id']);
				}
				$path[]=array('Добавить '.$this->ent->entityNameVin);
			}
		}else{

			if(isset($this->dataDesc[3][0]) && $this->dataDesc[3][0] && file_exists($this->interface_dir.$this->ent->entity.'/grid.php'))
				$folder = $this->entity;
			else
				$folder = 'common';

			$page = $this->interface_dir.$folder.'/grid.php';
		}
		
		/*
		$content = '';		
		if($page != ''){
			ob_start();
			require $page;
			$content = ob_get_contents();
			ob_end_clean();
		}
		*/		
		
		return $page;
		
	}
	
	function getPath(){
		
		/*
		
		$path = array();
		
		if($this->isTree() && $this->mpTree){
			
			$path[] = array($this->entityName, $this->baseUrl);
			$tree = new Tree($this->entity);
			$t_path = $tree->getPath('name,id', $this->foreignValue);
			for($i=0; $i<count($t_path); $i++)
				$path[] = array($t_path[$i]['name'], $this->baseUrl.'&parent='.$t_path[$i]['id']);
			
		}else{

			$f_ent = isset($GLOBALS['entInits'][$this->entity]) ? $GLOBALS['entInits'][$this->entity][1] : '';
			$path = array_reverse(DataTable::getPathToEntity($this->entity, $f_ent, $this->foreignField, $this->foreignValue, $this->curFolder));
			
		}	
		
		if(isset($_GET['show']) && in_array($_GET['show'], array('element_form', 'folder_form'))){

			if(isset($_GET['id']) && is_numeric($_GET['id'])){
				$path[] = array('Редактировать '.$this->ent->entityNameVin, '');
			}else{
				$path[]=array('Добавить '.$this->ent->entityNameVin, );
			}
		}
		
		return $path;
		
		*/
		
		return array_reverse($this->getCurrentPath(true));
		
	}
	
	function getLinks(){
		
		$links = array();
		
		if(!isset($_GET['show']) || !in_array($_GET['show'], array('element_form', 'folder_form'))){			
			if($this->withFolders && $this->curFolder){
				$res = $GLOBALS['db']->query('select parent from folders where id='.intval($this->curFolder))->getNext();
				if($res){
					$lnk = Common::editQueryString($this->baseUrl, array('folder')).($res['parent'] ? '&folder='.$res['parent'] : '');
					$links[] = array('Назад', $lnk, 'reply');
				}
			}
			if(AuthUser::getInstance()->hasAccess('datatable_'.self::getCheckAccessEntity($this->entity), 'w')){
				if($this->entityNameVin != '')
					$links[] = array('Добавить '.$this->entityNameVin, $this->baseUrl.'&show=element_form', 'plus');					
				if($this->withFolders)
					$links[] = array('Добавить категорию', $this->baseUrl.'&show=folder_form', 'folder-open');				
			}
		}		
		return $links;
		
	}
	
	function getEntitiesList(){
		
		global $db;
		
		$result = array();
		$parent = $this->getParentEntity(false);
		$is_element_form = isset($_GET['show']) && in_array($_GET['show'], array('element_form', 'folder_form')) && $this->ent->getRowValue('id');
		$current_children = $this->getNextEntity();
		
		$target_entity = $this->ent->entity;
		$target_id = 0;
		
		if($is_element_form){
			
			if(count($current_children)){

				$target_id = $this->ent->getRowValue('id');
				
			}else{
				
				$target_entity = $parent;
				$target_id = $this->ent->foreignValue;
				
			}
			
		}else{
			
			$target_entity = $parent;
			$target_id = $this->ent->foreignValue;
			
		}
		
		if($target_entity == $parent && !$this->ent->foreignValue)
			$target_entity = '';
		
		if($target_entity){
			
			$dt = new DataTable($target_entity);
			$list = $dt->getNextEntity();
			if($target_entity == 'folders'){
				$folder_data = $db->query('select * from folders where id='.intval($target_id))->getNext();
				$result[] = array('name' => '_datatable_'.$folder_data['entity'], 'title' => 'Описание', 'url' => 'index.php?module=datatable&entity='.$folder_data['entity'].($folder_data['parent'] ? '&folder='.$folder_data['parent'] : '').'&show=folder_form&id='.$target_id);
			}else{
				if(count($list)){
					$result[] = array('name' => '_datatable_'.$target_entity, 'title' => 'Описание', 'url' => 'index.php?module=datatable&entity='.$target_entity.'&show=element_form&id='.$target_id);
				}
			}
			foreach($list as $v){
				$result[] = array(
					'name' => 'datatable_'.$v[0],
					'title' => $v[2],
					'url' => 'index.php?module=datatable&entity='.$v[0].'&'.$v[1].'='.$target_id
				);
			}
			
		}
		
		/*
		if($parent){
			$dt = new DataTable($parent);
			$l = $dt->getNextEntity();
			if(count($l))
				$list[] = array('name' => '_datatable_'.$parent, 'title' => 'Описание', 'url' => 'index.php?module=datatable&entity='.$parent.'&show=element_form&id='.$this->foreignValue);
			foreach($l as $v){
				$list[] = array(
					'name' => 'datatable_'.$v[0],
					'title' => $v[2],
					'url' => 'index.php?module=datatable&entity='.$v[0].'&'.$v[1].'='.$this->foreignValue
				);
			}
		}else{			
			if($is_element_form){				
				$l = $this->getNextEntity();
				if(count($l))
					$list[] = array('name' => '_datatable_'.$this->entity, 'title' => 'Описание','url' => $_SERVER['REQUEST_URI']);
				foreach($l as $v){
					$list[] = array(
						'name' => 'datatable_'.$v[0],
						'title' => $v[2],
						'url' => 'index.php?module=datatable&entity='.$v[0].'&'.$v[1].'='.$this->getRowValue('id')
					);
				}
			}			
		}
		*/
		
		return $result;
		
	}
	
	function setFilter($arr, $use_session = true){
		$this->filter->setFilter($arr, $use_session);
	}
	
	function setFilterFolderRecursive($val){
		$this->filter->setFolderRecursive($val);
	}
	
	function execute($redir = ''){
		
		$fld = false;
		if(isset($_GET["sp"])){
			$this->ent = new DataTable("folders");
			$fld = true;
		}
		else
			$this->ent = &$this;
		
		$check_access_entity = self::getCheckAccessEntity($this->entity);
			
		if(isset($_GET["act"])){
			
			if(!AuthUser::getInstance()->hasAccess('datatable_'.$check_access_entity, 'w')){
				SiteNotices::addNotice('Недостаточно прав для выполнения этого действия', 'error');
				Header('Location: '.'/engine/admin/'.$this->baseUrl);
				exit(0);
			}
			
			$done = in_array($_GET["act"],array("add","update","delete","setpos","update_all"));
			switch($_GET["act"]){
				case "add":
					if($fld){
						$this->ent->setRowValue("entity", $this->entity);
						$this->ent->setRowValue("entity_foreign_id", intval($this->foreignValue));
						$this->ent->setRowValue("parent", isset($_POST["folder_id"]) ? $_POST["folder_id"] : $this->curFolder);
					}elseif($this->curFolder)
						if(!isset($_POST["folder_id"]))
							$this->ent->setRowValue("folder_id", $this->curFolder);
					//return 
					$res = $this->ent->insertRow();
					
				break;
				case "update":
					if($fld)
						$this->ent->setRowValue("parent", isset($_POST["folder_id"])?$_POST["folder_id"]:$this->curFolder);
					$res = $this->ent->updateRow();
				break;
				case "delete":
					$this->ent->deleteRow();
					$redir = Common::editUrl(array('act', 'id'));
				break;
				case "update_all":
					if(isset($_POST['pos']))
						$this->setPos();
					$this->updateAll();	
				break;
				case "setpos":
					$this->setPos();
				break;
				case 'copy':
					if(isset($this->dataDesc['copy']) && isset($_GET['id'])){
						DataTable::copyElements($this->entity, 'id='.intval($_GET['id']));
						$done = true;
					}
				break;
				case 'multi_insert':
					$res = $this->multiInsert();
					$done = true;
					SiteNotices::addNotice('Создано записей: '.$res, 'success');
				break;
				case 'multi_update':
					$counter = 0;
					if(isset($_POST['multi_update'])){
						$data = array();
						foreach($_POST['multi_update'] as $k => $v){
							if($v !== ''){
								$data[$k] = $v;
							}
						}
						
						if(count($data)){
							$this->use_pager = 0;
							$items = $this->getGrid();
							foreach($items['elements'] as $v){
								$this->setPostArray(array_merge($data, array('id' => $v['id'])));
								$this->updateRow();
								$counter++;
							}
						}
						
					}
					$done = true;
					SiteNotices::addNotice('Обновлено записей: '.$counter, 'success');
				break;
			}
			if($done && !count($this->ent->errors)){
				
				$cookie_name = 'admin_'.($this->ent->entity != $this->entity ? $this->ent->entity.'_' : '').$this->entity.'_'.$_GET['act'].'_back_to_edit';
				
				if(isset($_POST['__back_to_edit']) && $_POST['__back_to_edit'] == 1){
					if($_GET['act'] == 'add')
						SiteNotices::addNotice('Запись добавлена', 'success');
					else
						SiteNotices::addNotice('Изменения сохранены', 'success');
					$url_remove = array('act');
					$url_add = array();
					if($_GET['act'] == 'add'){
						$url_remove[] = 'show';
						$url_add['show'] = $fld ? 'folder_form' : 'element_form';
						$url_add['id'] = $res;
					}
					$redir = Common::editUrl($url_remove, $url_add);
					if(!isset($_COOKIE[$cookie_name])){
						setcookie($cookie_name, 1, time() + 86400*365, '/');
					}
				}elseif(isset($_COOKIE[$cookie_name])){
					setcookie($cookie_name, "", time() - 3600, '/');
				}
				
				if(!Site::isAjaxRequest() && !isset($_POST['multi_upload'])){
					Header('Location: '.($redir ? $redir : '/engine/admin/'.$this->baseUrl.(isset($_GET['page']) ? '&page='.intval($_GET['page']) : '')));
				}
				exit(0);
			}
		}
	}
	
	function getGrid($order="", $case="1", $forselect=false, $add_root = true, $left_join = '', $extra_fields = ''){
		
		if(isset($this->dataDesc['default_filter'])){
			$this->filter->addFilter($this->dataDesc['default_filter']);
		}
		$filter_query = $this->filter->getQuery();
		if($forselect){
			$filter_query['where'] = 1;
		}
			
		if($filter_query['where'] && $filter_query['where'] != '1')
			$case .=' and ('.$filter_query['where'].')';
		
		if($order == "")
			$order = $this->dataDesc[2][1];

		if($order == "")
			$order="id";
			
		if(!$this->filter->isEmpty())
			$this->gridCond = array();

		$this->gridCond[] = $case;

		$arr = array("folders"=>array(), "elements"=>array());
		if($this->withFolders){
			$t = SiteLang::getLangs();
			$lng = 0;
			if(count($t))
				$lng = $t[0]['id'];
			$arr["folders"] = DBCommon::getFromBase("M.*","folders as M","M.entity='".$this->entity."' and M.parent=".$this->curFolder,"M.pos");
		}

		$arr["elements"] = array();

		if(!$this->isTree() || $this->mpTree){
			
			if($this->isMultilang){
				$ml_ob = $this->getMultilangObject();
				$m_f = $ml_ob->getTitleFields();
				if(count($m_f)){
					/* get main language */
					if($main_lang = DataTable::getMainLang()){
						foreach($m_f as $fn => $fd)
							$filter_query['fields'] .= ',L.'.$fn;
						$filter_query['tables'] .= ' left join '.$ml_ob->entity.' as L on L.entry_id=T.id and L.lang='.$main_lang['id'];
					}
				}
			}
			
			foreach(array_keys($this->getTitleFields()) as $f){
				$tit_fld = $f;
				break;
			}
			
			if($forselect){
				foreach($this->gridCond as $k=>$v){
					if(strpos($v, 'folder_id=') === 0){
						unset($this->gridCond[$k]);
						break;
					}
				}
			}
			
			//echo 'select '.$filter_query['fields'].($extra_fields ? ','.$extra_fields : '').' from ('.$filter_query['tables'].')'.($left_join ? ' left join '.$left_join : '').' where '.implode(" and ",$this->gridCond).($filter_query['group_by'] ? ' group by '.$filter_query['group_by'] : '').' order by '.$order;

			$this->gridResultObject = $GLOBALS['db']->query('select '.$filter_query['fields'].($extra_fields ? ','.$extra_fields : '').' from ('.$filter_query['tables'].')'.($left_join ? ' left join '.$left_join : '').' where '.implode(" and ",$this->gridCond).($filter_query['group_by'] ? ' group by '.$filter_query['group_by'] : '').' order by '.$order);
			if($this->use_pager && !$forselect){		
				$this->gridResultObject->startPager($this->use_pager);
				$this->pager_ob = $this->gridResultObject->pager;
				$this->pager_ob->setTemplate('admin');
			}
			$arr["elements"] = $this->gridResultObject->fetchArray();

		}else{
			if($this->foreignValue)
				$case = ($case != '' ? $case.' and ' : '').' ('.$this->foreignField.'='.$this->foreignValue.')';
			if(!$forselect && $this->withFolders)
				$case = ($case != '' ? $case.' and ' : '').' folder_id='.$this->curFolder;
			$case = str_replace('T.', 'M.', $case);
			$tree = new Tree($this->entity, $case, $left_join);
			$arr["elements"] = $tree->getFullTree(0, 0, -1, $order, $extra_fields ? $extra_fields : '*');
		}
		
		if($forselect){
			$s_arr = array();
			if($this->isTree() && $add_root)
				$s_arr[] = array("__value" => 0, "__title" => "Корень");
			foreach($arr["elements"] as $v){
				$s_arr[] = array_merge($v,array("__value"=>$v["id"], "__title" => DataTable::getRecordTitle($this->entity, $v)));//isset($v[$tit_fld."_real"]) ? $v[$tit_fld."_real"]:$v[$tit_fld]);
				if(isset($v["_tree_level"]))
					$s_arr[count($s_arr)-1]["_tree_level"] = $v["_tree_level"];
			}
			
			return $s_arr;
		}else{
			return $arr;
		}
	}
	
	function getElementsNum(){
		$query = $this->filter->getQuery();
		$res = $GLOBALS['db']->query('select * from '.$query['tables'].' where '.$query['where'].($query['group_by'] ? ' group by '.$query['group_by'] : ''));
		//$t = $res->getNext();
		return $res->selectedRowsCount();
	}

	function isTree(){
		foreach($this->dataDesc[1] as $k=>$v){
			if($v[1][0] == "foreign" && $v[1][1] == $this->entity)
				return $k;
		}
		return false;
	}
	
	function initCurRow($q){
		
		$t = DBCommon::getFromBase('*', $this->entity, $q, 'id');
		if(count($t)){
			$this->curRow = $t[0];
			/* try to find multiple foreign rows */
			foreach($this->fields as $k=>$v){
				if($v[1][0] == 'multiple_foreign'){
					$table = isset($v[1]['table']) ? $v[1]['table'] : $k;
					$mq = $v[1][2].'='.$t[0]['id'];
					if(isset($v[1]['additional_field']) && isset($v[1]['additional_value'])){
						$mq .= ' and '.$v[1]['additional_field'].'="'.mysql_escape_string($v[1]['additional_value']).'"';
					}
					$res = $GLOBALS['db']->query('select '.$v[1][3].' from '.$table.' where '.$mq);
					while($tf = $res->getNext())
						$this->curRow[$k][] = $tf[$v[1][3]];
				}
			}
			
			if($this->withFolders){
				$this->curFolder = $this->curRow['folder_id'];
			}
			
			if($this->foreignField && isset($this->curRow[$this->foreignField]))
				$this->foreignValue = $this->curRow[$this->foreignField];
			
		}
		
	}
	
	function clearCurRow(){
		$this->curRow = array();
	}
	
	function getRowValue($field, $search_in_post = true, $to_show = false){
		
		$val = '';
		
		if($this->isFkTextMode() && isset($this->fields[$field][1]) && $this->fields[$field][1][0] == 'foreign'){
			
			if($search_in_post && isset($this->post_array['_'.$field.'_fk_text'])){
				$val = htmlspecialchars($this->post_array['_'.$field.'_fk_text']);
			}elseif(isset($this->curRow[$field])){
				$dt_fk = new DataTable($this->fields[$field][1][1]);
				$tf = array_keys($dt_fk->getTitleFields());
				$dt_fk->initCurRow('id='.intval($this->curRow[$field]));
				$val = $dt_fk->getRowValue($tf[0]);
			}
			
		}else{
			
			$found_in_post = false;
		
			if($search_in_post){// && !isset($this->errors[$field]))

				if(isset($this->post_array[$field])){
					$found_in_post = true;
					$val = $this->isOutputEscapeMode() && !is_array($this->post_array[$field]) ? htmlspecialchars($this->post_array[$field]) : $this->post_array[$field];
				}else{
					
					if(isset($this->fields[$field]) && $this->fields[$field][1][0] == 'multiple_foreign' && isset($this->fields[$field][1]['table'])){
						
						if(isset($this->fields[$field][1]['additional_value']) && isset($this->post_array[$this->fields[$field][1]['table']][$this->fields[$field][1]['additional_value']])){
							
							$found_in_post = true;
							$val = $this->post_array[$this->fields[$field][1]['table']][$this->fields[$field][1]['additional_value']];
							
						}
						
					}
					
				}
			}
			
			if(isset($this->curRow[$field]) && !$found_in_post){
				$data_escaped = self::getFieldEscapeMode($this->entity, $field) == 'escape';
				$do_escape = $this->isOutputEscapeMode() && !$data_escaped && !is_array($this->curRow[$field]);
				$val = $do_escape ? htmlspecialchars($this->curRow[$field]) : $this->curRow[$field];
			}			
				
			if($to_show && isset($this->fields[$field][1][0]) && $this->fields[$field][1][0] == 'date' && isset($this->fields[$field][1][2])){
				$ex = explode('.', $val);
				if(count($ex) == 3){
					$val = $ex[2].'-'.$ex[1].'-'.$ex[0];
				}
			}
		
		}
		
		return $val;
	}
	
	function getBeforeUpdateData($field){
		return isset($this->before_update_data[$field]) ? $this->before_update_data[$field] : '';
	}
	
	function setRowValue($row, $value){
		$this->post_array[$row] = $value;
	}
	
	function getBoundedEntities(){
		foreach($GLOBALS["entities"] as $ent=>$desc){
			foreach($desc[1] as $field=>$fd)
				if($fd[1][0]=="foreign" && $fd[1][1]==$this->entity && !isset($fd['ignore'])){
					$this->boundedEntities[] = array($ent, $field, $fd[1][2]);
				}
		}
	}
	
	function insertRow($data = null, $ignore_events = false){
		
		if(!$ignore_events)
			$this->callEventHandlers('onBeforeSave', array(&$this));
		
		if(!$data)
			$data = &$this->post_array;
		$row = new DataRow($this->entity, $this->files_in_array);
		$row->read("add", $data);
		$res=0;
		if(!$row->haveErrors()){
			$iq = $row->getInsertQuery();
			if($this->saveAuthorId && (!isset($data['created_by']) || !$data['created_by'])){
				$iq[0].=",created_by";
				$iq[1].=",".AuthUser::getInstance()->getId();
			}
			if(isset($data['id'])){
				$iq[0] .= ',id';
				$iq[1] .= ','.intval($data['id']);
			}
			$ins_res = $GLOBALS['db']->query("insert into ".$this->entity."(".$iq[0].") values(".$iq[1].")");
			if(!mysql_errno()){
				$res = $ins_res->lastID();
				if(count($row->filesQueue))
					$this->copyFiles($row->filesQueue,$res);
				if(count($row->imageResizeQueue))
					$this->createImages($row->imageResizeQueue,$res);
				foreach($this->dataDesc[1] as $k=>$v){

					if($v[1][0] == "multiple_foreign"){
						
						$arr_ins = array();
						
						$table = isset($v[1]['table']) ? $v[1]['table'] : $k;
						$values = array();
						if(isset($v[1]['additional_field']) && isset($v[1]['additional_value'])){
							if(isset($data[$table][$v[1]['additional_value']])){
								$values = $data[$table][$v[1]['additional_value']];
							}
						}else{
							if(isset($data[$table]))
								$values = $data[$table];
						}
						
						if(is_array($values)){
							foreach($values as $fid){
								if(!$fid) continue;
								$ind = $res.','.$fid.(isset($v[1]['additional_value']) ? ',"'.$v[1]['additional_value'].'"' : '');
								if(is_numeric($fid) && !in_array($ind, $arr_ins)){
									$GLOBALS['db']->query('insert into '.$table.'('.$v[1][2].','.$v[1][3].(isset($v[1]['additional_field']) ? ','.$v[1]['additional_field'] : '').') values('.$res.','.$fid.(isset($v[1]['additional_value']) ? ',"'.$v[1]['additional_value'].'"' : '').')');
									$arr_ins[] = $ind;
								}
							}					
						}
					}
				}
				
				/* insert multilang content */
				if($this->isMultilang){
					$langs = DBCommon::getFromBase('*', 'langs', '1', 'pos');
					$ml_ob = $this->getMultilangObject();
					$def_lang = '';
					foreach($langs as $k => $l){
						/* fill empty values with defaults */
						if($k > 0){
							foreach($data['langs'][$l['name']] as $k => $v){
								if(!is_array($v) && $v == ''){
									$data['langs'][$l['name']][$k] = isset($data['langs'][$def_lang][$k]) ? $data['langs'][$def_lang][$k] : '';
								}
							}
						}else{
							$def_lang = $l['name'];
						}
						
						$ml_ob->clearCurRow();
						$ml_ob->setPostArray($data['langs'][$l['name']]);
						$ml_ob->setRowValue('entry_id', $res);	
						$ml_ob->setRowValue('lang', $l['id']);
						$ml_ob->setFilesInArray(array('langs', $l['name']));
						$ml_ob->insertRow();
					}
				}
				
			}else{
				echo mysql_error();
				exit();
			}
		}else{
			$this->errors = $row->errors;
		}
		if($res){
			$this->last_id = $res;
			if(!$ignore_events)
				$this->callEventHandlers('onAfterInsert', array(&$this));
		}
		return $res;
	}
	
	function updateRow($data = null, $ignore_events = false){
		
		if(!$ignore_events)
			$this->callEventHandlers('onBeforeSave', array(&$this));
			
		if(!$data)
			$data = &$this->post_array;
		$res=0;
		
		if(isset($data["id"]) && is_numeric($data["id"])){
			$row = new DataRow($this->entity, $this->files_in_array);
			$row->read("update", $data);
			if(!$row->haveErrors()){
				$od = DBCommon::getById($this->entity,"*",$data["id"]);
				$this->before_update_data = $od;
				$GLOBALS['db']->query("update ".$this->entity." set ".$row->getUpdateQuery()." where id=".$data["id"]);
				if(!mysql_errno()){
					if(count($row->filesQueue)){
						foreach(array_keys($row->values) as $k){
							if($this->dataDesc[1][$k][1][0]=="file" && $od[$k]!="" && file_exists(B_DIR.$this->dataDesc[1][$k][1][2].$od[$k]))
								unlink(B_DIR.$this->dataDesc[1][$k][1][2].$od[$k]);
						}
						$this->copyFiles($row->filesQueue, $data['id']);
					}
					
					if(count($row->imageResizeQueue)){					
						$orig_ext_field = isset($this->fields[$row->imageResizeQueue[0]][1]['orig_ext_field']) ? $this->fields[$row->imageResizeQueue[0]][1]['orig_ext_field'] : 'orig_ext';
						if(isset($this->fields[$row->imageResizeQueue[0]][1]['save_orig'])){							
							$tf = B_DIR.$this->fields[$row->imageResizeQueue[0]][1]['save_orig'].$od['id'].'.'.$od[$orig_ext_field];
							if(file_exists($tf))
								unlink($tf);
						}
						$formats = $this->fields[$row->imageResizeQueue[0]][1][1];
						foreach($formats as $f){
							$fn = B_DIR.$f[2].$f[1].$od['id'].(isset($f['orig_ext']) && $f['orig_ext'] ? '.'.$od[$orig_ext_field] : '.jpg');
							if(file_exists($fn))
								unlink($fn);
						}
						$this->createImages($row->imageResizeQueue, $od['id']);
					}
					
					foreach($row->filesDelQ as $v){
						if(isset($this->dataDesc[1][$v])){
							if($this->dataDesc[1][$v][1][0] == 'file'){
								if($od[$v]!="" && file_exists(B_DIR.$this->dataDesc[1][$v][1][2].$od[$v]))
									unlink(B_DIR.$this->dataDesc[1][$v][1][2].$od[$v]);
							}elseif($this->dataDesc[1][$v][1][0] == 'image_to_resize'){
								if($od['orig_ext'] != ''){
									foreach($this->dataDesc[1][$v][1][1] as $frm){
										$fn = B_DIR.$frm[2].$frm[1].$od['id'].'.jpg';
										if(file_exists($fn))
											unlink($fn);								
									}
									if(isset($this->dataDesc[1][$v][1][2])){
										$fn = B_DIR.$this->dataDesc[1][$v][1][2].$od['id'].'.'.$od['orig_ext'];
										if(file_exists($fn)){
											unlink($fn);
										}
									}
									$pif = '';
									if(isset($this->dataDesc[1][$v][1]['put_in_fields']))
										$pif = ','.implode('="",', $this->dataDesc[1][$v][1]['put_in_fields']).'=""';
									$GLOBALS['db']->query('update '.$this->entity.' set orig_ext=""'.$pif.' where id='.$od['id']);
								}
							}
						}
					}
					$res = $data["id"];
					
					foreach($this->dataDesc[1] as $k => $v){
						if($v[0] != '' && $v[1][0] == "multiple_foreign"){						
						
							$arr_ins = array();

							$table = isset($v[1]['table']) ? $v[1]['table'] : $k;
							$values = array();
							if(isset($v[1]['additional_field']) && isset($v[1]['additional_value'])){
								if(isset($data[$table][$v[1]['additional_value']])){
									$values = $data[$table][$v[1]['additional_value']];
								}
							}else{
								if(isset($data[$table]))
									$values = $data[$table];
							}
							
							$GLOBALS['db']->query('delete from '.$table.' where '.$v[1][2].'='.$res.(isset($v[1]['additional_field']) ? ' and '.$v[1]['additional_field'].'="'.mysql_escape_string($v[1]['additional_value']).'"' : ''));

							if(is_array($values)){
								foreach($values as $fid){
									if(!$fid) continue;
									$ind = $res.','.$fid.(isset($v[1]['additional_value']) ? ',"'.$v[1]['additional_value'].'"' : '');
									if(is_numeric($fid) && !in_array($ind, $arr_ins)){
										$GLOBALS['db']->query('insert into '.$table.'('.$v[1][2].','.$v[1][3].(isset($v[1]['additional_field']) ? ','.$v[1]['additional_field'] : '').') values('.$res.','.$fid.(isset($v[1]['additional_value']) ? ',"'.$v[1]['additional_value'].'"' : '').')');
										$arr_ins[] = $ind;
									}
								}					
							}
							
						}
					}				
					
				}
				else{
					
				}
			}else{
				$this->errors = $row->errors;
			}
			
			/* update multilang content */
			if($this->isMultilang){
				$ml_ob = $this->getMultilangObject();
				foreach(SiteLang::getLangs() as $l){
					$ml_ob->clearCurRow();
					$ml_ob->setFilesInArray(array('langs', $l['name']));
					$ml_ob->setPostArray(isset($data['langs'][$l['name']]) ? $data['langs'][$l['name']] : array());					
					$ml_ob->initCurRow('entry_id='.$data['id'].' and lang="'.$l['id'].'"');
					$ml_ob->setRowValue('entry_id', $data['id']);	
					$ml_ob->setRowValue('lang', $l['id']);
					if($ml_ob->getRowValue('id', false) != ''){
						$ml_ob->setRowValue('id', $ml_ob->getRowValue('id'));
						$ml_ob->updateRow();
					}else{						
						$ml_ob->insertRow();
					}
				}
			}
			
		}
		if($res){	
			$this->last_id = $res;		
			if(!$ignore_events)
				$this->callEventHandlers('onAfterUpdate', array(&$this));
		}
		return $res;
	}
	
	function deleteRow($id=0){
		if($id==0&&isset($_GET["id"]))
			$id = $_GET["id"];
		$this->initCurRow('id='.intval($id));
		if(is_numeric($id)&&$id>0){
			$this->getBoundedEntities();
			foreach($this->boundedEntities as $ent){
				if($ent[2]){
					if($ent[0]!=$this->entity)
						$be = new DataTable($ent[0]);
					else
						$be = &$this;
					$rows = DBCommon::getFromBase("id",$ent[0],$ent[1]."=".$id);
					foreach($rows as $r)
						$be->deleteRow($r["id"]);
					unset($be);
				}else{
					$GLOBALS['db']->query("update ".$ent[0]." set ".$ent[1]."=0 where ".$ent[1]."=".$id);
				}
			}
			
			foreach($this->dataDesc[1] as $k => $v){
				if($v[1][0] == "multiple_foreign"){
					$table = isset($v[1]['table']) ? $v[1]['table'] : $k;
					$GLOBALS['db']->query('delete from '.$table.' where '.$v[1][2].'='.$id.(isset($v[1]['additional_field']) ? ' and '.$v[1]['additional_field'].'="'.mysql_escape_string($v[1]['additional_value']).'"' : ''));
				}
			}

			$od = DBCommon::getById($this->entity, "*", $id);
			$this->before_update_data = $od;
			$GLOBALS['db']->query("delete from ".$this->entity." where id=".$id);
			if($od)
				foreach($this->fields as $k=>$v){
					if($v[1][0] == "file" && $od[$k] != "" && file_exists(B_DIR.$v[1][2].$od[$k]))
						unlink(B_DIR.$v[1][2].$od[$k]);
					elseif($v[1][0] == "image_to_resize"){
						$orig_ext_field = isset($v[1]['orig_ext_field']) ? $v[1]['orig_ext_field'] : 'orig_ext';
						for($i=0; $i<count($v[1][1]); $i++){
							$fn = B_DIR.$v[1][1][$i][2].$v[1][1][$i][1].$od["id"].(isset($v[1][1][$i]['orig_ext']) && $v[1][1][$i]['orig_ext'] ? '.'.$od[$orig_ext_field] : '.jpg');
							if(file_exists($fn))
								unlink($fn);
						}
						if(isset($v[1]['save_orig'])){
							$fn = B_DIR.$v[1]['save_orig'].$od['id'].'.'.$od[$orig_ext_field];
							if(file_exists($fn))
								unlink($fn);
						}
					}
				}				
		}
		$this->callEventHandlers('onAfterDelete', array(&$this));
	}
	
	function setPos(){
		
		global $db;
		
		$upd = array();

		if(isset($_POST["pos"]["e"]) && is_array($_POST["pos"]["e"])){
			foreach($_POST["pos"]["e"] as $id=>$pos){
				$upd = array();
				if(is_numeric($id) && is_numeric($pos)){	
					$upd[] = 'pos='.$pos;
				}
				if(count($upd)){
					$db->query("update ".$this->entity." set ".implode(',', $upd)." where id=".$id);
				}
			}
		}
					
		if(isset($_POST["pos"]["f"])&&is_array($_POST["pos"]["f"])){
			foreach($_POST["pos"]["f"] as $id=>$pos){
				if(is_numeric($id)&&is_numeric($pos)){
					mysql_query("update folders set pos=".$pos." where id=".$id);				
				}
			}
		}
		
		// multi update
		if(isset($_POST['grid_update']) && is_array($_POST['grid_update'])){
			foreach($_POST['grid_update'] as $id => $data){
				$this->updateRow(array_merge(array('id' => $id), $data));
			}
		}
		
		return 0;
	}
	
	function updateAll(){
		
		if(isset($_POST['update']) && is_array($_POST['update'])){
			foreach($_POST['update'] as $id => $data){
				$this->updateRow(array_merge(array('id' => $id), $data), true);
			}
		}
		
	}
	
	function copyFiles($fq, $id=0){
		foreach($fq as $v){
			//$fn = $id;
			$file_data = Common::getPostFileData($v, $this->files_in_array);
			if($file_data){
				$pi = pathinfo($file_data["name"]);
				if(!isset($this->fields[$v][1]['orig_filename']))
					$fn = md5($file_data['name'].'_'.time().'_'.$id).'.'.$pi['extension'];//$id.'_'.str_replace(' ','_',$file_data["name"]);
				else
					$fn = $file_data['name'];
				copy($file_data["tmp_name"], B_DIR.$this->fields[$v][1][2].$fn);
				$GLOBALS['db']->query('update '.$this->entity.' set '.$v.'="'.mysql_escape_string($fn).'" where id='.$id);
				$this->curRow[$v] = $fn;
			}
		}
	}
	
	function getNextEntity(){
		
		$res = array();
		
		/* to to the same entity if tree */
		$tree_field = $this->isTree();
		if($tree_field && $this->mpTree){
			$tree = new Tree($this->entity);
			$tp = $tree->getPath("id",$this->foreignValue);
			if(isset($this->dataDesc["tree_titles"][count($tp)+1]))
				$res[] = array($this->entity,$tree_field,$this->dataDesc["tree_titles"][count($tp)+1][0]);
		}
		
		if(isset($GLOBALS["entInits"])){
			foreach($GLOBALS["entInits"] as $k => $v){
				if($v[1] == $this->ent->entity)
					$res[] = array($k, $v[0], $GLOBALS["entities"][$k][0][0]);
			}
		}
		
		foreach($GLOBALS["entities"] as $entity => $data){
			if(isset($data['init_by'])){
				if(!is_array($data['init_by'])){
					$data['init_by'] = $data['init_by'] ? array($data['init_by']) : array();
				}
				foreach($data['init_by'] as $i){
					$field = isset($data[1][$i]) ? $data[1][$i] : false;
					if($field && $field[1][0] == 'foreign' && $field[1][1] == $this->ent->entity){
						$res[] = array($entity, $i, $data[0][0]);
						continue;
					}
				}
			}
		}
		
		return $res;
	}
	
	function getParentEntity($ignore_folders = true){
		
		global $db;
		
		$entity = self::foundParentEntity($this->entity, $this->foreignField);
		
		if($entity == 'folders' && $this->foreignValue && $ignore_folders){
			$folder_data = $db->query('select * from folders where id='.intval($this->foreignValue))->getNext();
			if($folder_data){
				$entity = $folder_data['entity'];
			}
		}		
		
		return $entity;

	}
	
	function foundParentEntity($entity, $foreignField = false){
		
		$parent = '';
		
		if(isset($GLOBALS["entInits"])){ // Deprecated
			foreach($GLOBALS["entInits"] as $k => $v){
				if($k == $entity){
					$parent = $v[1];
					break;
				}
			}
		}
		
		$dataDesc = $GLOBALS['entities'][$entity];
		
		if(!$parent && isset($dataDesc['init_by'])){
			$init = $dataDesc['init_by'];
			if(!is_array($init))
				$init = array($init);
			$field = '';
			foreach($init as $i){
				if(!$foreignField || $foreignField == $i || isset($_GET[$i])){
					$field = $i;
					break;
				}
			}
			if(count($init) && !$field){
				$field = $init[0];
			}
			if($field){
				$data = $dataDesc[1][$field];
				if($data[1][0] == 'foreign'){
					$parent = $data[1][1];
				}
			}
		}
		
		return $parent;
		
	}
	
	function getRootEntity($entity){
		$par = '';
		if(isset($GLOBALS['entInits'][$entity])){
			$par = $GLOBALS['entInits'][$entity][1];
		}else{
			$par = self::foundParentEntity($entity);
		}
		if($par){
			return self::getRootEntity($par);
		}
		return $entity;
	}
	
	function renderErrors(){
		$err = array();
		foreach($this->ent->errors as $field=>$code){
			$e_s = isset($GLOBALS["error_codes"][$code]) ? $GLOBALS["error_codes"][$code] : '';
			$err[] = sprintf($e_s, $this->ent->fields[$field][0]);
		}
		$res = implode((Site::isApp() ? "\n": '<br>'), $err);		
		return $res;
	}

	function createImages($fields, $id){
		
		$mess_string = "";//Users::generatePassword(6);
		//mysql_query("update ".$this->entity." set mess_string='".$mess_string."' where id=".$id);
		
		if($id){
			
			foreach($fields as $v){
				
				$file_data = Common::getPostFileData($v, $this->files_in_array);
				
				$s_is = getimagesize($file_data["tmp_name"]);
				$formats = $this->fields[$v][1][1];
				
				/* rename tmp file to original format */
				$pi = pathinfo($file_data['name']);
				$pi['extension'] = strtolower($pi['extension']);
				$ext = $pi['extension'];
				$renamed = $file_data['tmp_name'].md5($file_data['name']).'.'.$pi['extension'];
				rename($file_data['tmp_name'], $renamed);
				
				$orig_ext_field = isset($this->fields[$v][1]['orig_ext_field']) ? $this->fields[$v][1]['orig_ext_field'] : 'orig_ext';
				$GLOBALS['db']->query('update '.$this->entity.' set '.$orig_ext_field.'="'.$ext.'" where id='.$id);
				
				if(isset($this->fields[$v][1]['put_in_fields'])){
					$upd = array();
					foreach($this->fields[$v][1]['put_in_fields'] as $f_k => $f_v){
						if(isset($formats[$f_k])){
							$upd[] = $f_v.'="'.$formats[$f_k][1].$id.'.jpg"';
						}
					}
					if(count($upd)){
						$GLOBALS['db']->query('update '.$this->entity.' set '.implode(',', $upd).' where id='.$id);
					}
				}
				
				/* copy original image*/
				if(isset($this->fields[$v][1]['save_orig'])){
					
					copy($renamed, B_DIR.$this->fields[$v][1]['save_orig'].$id.'.'.$ext);
					
					/* add watermark if needed */
					if(isset($this->fields[$v][1]['watermark'])){
						Images::addWatermark(B_DIR.$this->fields[$v][1]['save_orig'].$id.'.'.$ext, B_DIR.$this->fields[$v][1]['watermark']);
					}
					
				}				
				
				foreach($formats as $f){
					$fname = $f[1].$id.(isset($f['orig_ext']) && $f['orig_ext'] ? '.'.$ext : '.jpg');
					$sz = explode(',', $f[0]);
					Images::imageResizeUnified($renamed, $sz[0], $sz[1], B_DIR.$f[2].$fname, 2, (isset($f[3]) ? $f[3] : 0));
					if(isset($f[4])){
						Images::addWatermark(B_DIR.$f[2].$fname, B_DIR.$f[4]);
					}
				}
				
			}
			
		}
		
	}
	
	function callEventHandlers($event, $params = array()){
		if(isset(self::$event_handlers[$this->entity][$event]) && is_array(self::$event_handlers[$this->entity][$event]))
			foreach(self::$event_handlers[$this->entity][$event] as $v){				
				call_user_func_array($v, $params);
			}
	}
	
	static function getRecordTitle($entity, $data, $title_fields = false){
		
		$title = '';
		
		if(!$data)
			return false;
		
		if(isset($GLOBALS['entities'][$entity])){			
			
			$ent = new DataTable($entity);			
			
			if(is_numeric($data)){
				$tables = $ent->entity.' as M';
				$fields = 'M.*';
				$where = 'M.id = '.$data;
				if($ent->isMultilang){
					$ml_ob = $ent->getMultilangObject();
					$title_fields = $ml_ob->getTitleFields();
					$tables .= ' left join '.$ml_ob->entity.' as L on L.entry_id=M.id and L.lang='.DataTable::getMainLang();
					foreach(array_keys($ml_ob->getTitleFields()) as $v)
						$fields .= ',L.'.$v;
				}
				$t_res = DBCommon::getFromBase($fields, $tables, $where);
				if(count($t_res))
					$data = $t_res[0];
			}
			
			if(count($data)){
				
				$fields = array();
				
				if($title_fields){
					$t = is_array($title_fields) ? $title_fields : array($title_fields);
					foreach($t as $field){
						if(isset($GLOBALS['entities'][$entity][1][$field])){
							$fields[$field] = $GLOBALS['entities'][$entity][1][$field];
						}elseif($field == 'id'){
							$fields[$field] = 'ID';
						}
					}
				}else{
					$fields = $ent->getTitleFields();
				}
			
				foreach($fields as $fn => $fd){
					if(!is_array($fd) || $fd[1][0] != 'foreign'){
						$do_escape = self::getFieldEscapeMode($entity, $fn) != 'escape';
						$title .= (isset($data[$fn]) ? ($do_escape ? strip_tags($data[$fn]) : $data[$fn]).' ' : '');
					}else{
						$f_ent = $fd[1][1];		
						$f_title_fields = false;
						if(isset($fd['foreign_title_fields'])){
							$f_title_fields = $fd['foreign_title_fields'];
						}else{
							if(isset($GLOBALS['entities'][$fd[1][1]]['title_fields'])){
								$f_title_fields = $GLOBALS['entities'][$fd[1][1]]['title_fields'];
							}
						}
						$title .= DataTable::getRecordTitle($fd[1][1], $data[$fn], $f_title_fields).' ';
					}
					if(!$title_fields)
						break;
				}
			
			}
			
		}
		
		return Common::cropText(trim($title), 100, false);
		
	}
	
	static function getPathToEntity($entity, $foreign_entity = '', $foreign_field = '', $foreign_value = 0, $folder_id = 0){
		
		if(isset($GLOBALS['entities'][$entity])){
			
			$path = array();
			
			$desc = $GLOBALS['entities'][$entity];			
			// check if has parent entity
			if($foreign_entity != ''){
				
				$p_table = $foreign_entity;				
				$res = $GLOBALS['db']->query('select * from '.$foreign_entity.' where id='.$foreign_value);
				if($t = $res->getNext()){
					$p_title = DataTable::getRecordTitle($foreign_entity, $t['id']);
					$path[] = array($p_title, 'index.php?module=datatable&entity='.$entity.'&'.$foreign_field.'='.$foreign_value);
					
					$p_desc = $GLOBALS['entities'][$foreign_entity];					
					$p_f_e = isset($GLOBALS['entInits'][$foreign_entity]) ? $GLOBALS['entInits'][$foreign_entity][1] : '';
					
					if(!$p_f_e){
						$p_f_e = self::foundParentEntity($foreign_entity);
					}					
					
					$p_f_f = '';
					if($p_f_e){
						if(isset($GLOBALS['entInits'][$foreign_entity][0])){
							$p_f_f = $GLOBALS['entInits'][$foreign_entity][0];
						}else{
							foreach($p_desc[1] as $fn => $f){
								if($f[1][0] == 'foreign' && $f[1][1] == $p_f_e){
									$p_f_f = $fn;
								}
							}
						}
					}
					
					$p_f_v = $p_f_e != '' ? $t[$p_f_f] : '';
					$path = array_merge($path, DataTable::getPathToEntity($foreign_entity, $p_f_e, $p_f_f, $p_f_v, (isset($t['folder_id']) ? $t['folder_id'] : 0)));
				}				
				
			}else{
				$path[] = array($desc[0][0], 'index.php?module=datatable&entity='.$entity);
				// if not with folders
				if(isset($desc[4]) && $folder_id){	
					$t = SiteLang::getLangs();
					$lng = 0;
					if(count($t))
						$lng = $t[0]['id'];
					$tree = new Tree('folders','M.entity="'.$entity.'"');
					$t_path = $tree->getPath('M.name,M.id', $folder_id);			
					foreach($t_path as $p){
						$path[] = array($p['name'], 'index.php?module=datatable&entity='.$entity.'&folder='.$p['id']);
					}
					$path = array_reverse($path);
				}	
							
			}
			
			return $path;
			
		}
		
	}
	
	
	function getCurrentPath($first_call = false){
		
		global $db;
		
		$path = array();
		$parent = $this->getParentEntity(false);
		
		if($first_call && isset($_GET['show']) && in_array($_GET['show'], array('element_form', 'folder_form'))){
			if(!$this->ent->getRowValue('id')){
				$path[] = array(
					'Добавить '.$this->ent->entityNameVin,
					''
				);
			}elseif($_GET['show'] == 'folder_form'){
				$path[] = array(
					'Редактировать категорию',
					''
				);
			}
		}
		
		if($this->entity == 'folders'){
			
			$folder_data = $db->query('select * from folders where id='.intval($this->getRowValue('id', false)))->getNext();
			if($folder_data){
				$tree = new Tree('folders');
				$t_p = $tree->getPath('*', $folder_data['id']);
				for($i = count($t_p)-1; $i >= 0; $i--){
					if($i == count($t_p)-1){
						$link = 'index.php?module=datatable&entity='.$folder_data['entity'].'&show=folder_form&id='.$t_p[$i]['id'].($t_p[$i]['parent'] ? '&folder='.$t_p[$i]['parent'] : '');
					}else{
						$link = 'index.php?module=datatable&entity='.$folder_data['entity'].'&folder='.$t_p[$i]['id'];
					}
					$path[] = array(
						$t_p[$i]['name'],
						$link
					);
				}
				$dt = new DataTable($folder_data['entity']);
				$path = array_merge($path, $dt->getCurrentPath());
			}
			
		}else{
			
			if($this->getRowValue('id', false)){
			
				$ttl = isset($this->dataDesc['common_edit_title']) ? 'Редактировать '.$this->entityNameVin : Common::cropText(strip_tags(DataTable::getRecordTitle($this->entity, $this->getRowValue('id', false), isset($this->dataDesc['title_fields']) ? $this->dataDesc['title_fields'] : false )), 60, true, '...');
				$path[] = array(
					$ttl,
					'index.php?module=datatable&entity='.$this->entity.'&show=element_form&id='.$this->getRowValue('id')
				);
			}

			if($this->withFolders && $this->curFolder){
				$tree = new Tree('folders', 'entity="'.$this->entity.'"'.($this->foreignValue ? ' and entity_foreign_id='.intval($this->foreignValue) : ''));
				$p = array_reverse($tree->getPath('*', $this->curFolder));
				foreach($p as $v){
					$path[] = array(
						$v['name'],
						'index.php?module=datatable&entity='.$this->entity.($this->foreignValue ? '&'.$this->foreignField.'='.$this->foreignValue : '').'&folder='.$v['id']
					);
				}
			}
			
			if(!$parent || !$this->foreignValue || isset($_GET['show'])){
				$path[] = array(
					$this->entityName,
					'index.php?module=datatable&entity='.$this->entity.($this->foreignField ? '&'.$this->foreignField.'='.$this->foreignValue : '')
				);
			}
			
			if($parent && $this->foreignValue){			
			
				$dt = new DataTable($parent);
				$dt->initCurRow('id='.intval($this->foreignValue));
				$path = array_merge($path, $dt->getCurrentPath());

			}

		}
		
		return $path;
		
	}
	
	
	function getFormFields($field_name_template = ''){
		
		$fields = array();
		
		foreach($this->fields as $k => $v){
			if(isset($v[0]) && $v[0] != ''){
				$t = array('title' => '', 'html' => '', 'type' => isset($v[3][0]) ? $v[3][0] : '');
				$t['title'] = $v[0];
				$t['html'] = $this->showFormField($k, $field_name_template);
				$fields[$k] = $t;
			}
		}
		
		return $fields;
		
	}
	
	function setFieldNameTemplate($fnt){
		$this->field_name_template = $fnt;		
	}
	
	function parseFieldName($fn){
		if($this->field_name_template != '')
			return str_replace('{field_name}', $fn, $this->field_name_template);
		return $fn;
	}
	
	function showFormField($field){
		
		$cnt = '';
		
		if(isset($this->fields[$field])){
			$field_name = $this->parseFieldName($field);
			$f_desc = $this->fields[$field];
			
			/* get template name */
			if(isset($f_desc[4])){
				$template = 'readonly';
			}else{
				$template = $f_desc[1][0];
				if($f_desc[1][0] == 'image_to_resize')
					$template = 'file';
				elseif($f_desc[1][0] == 'input'){
					if(isset($f_desc[3][0]))
						$template = $f_desc[3][0];
				}
			}
			ob_start();
			require $this->fields_dir.$template.'.php';
			$cnt = ob_get_contents();
			ob_end_clean();
		}
		
		return $cnt;
		
	}
	
	function getTitleFields(){
		
		$arr = array();
		
		if(isset($this->dataDesc[2][0])){
			$flds = explode(',', $this->dataDesc[2][0]);
			foreach($flds as $v){
				if(isset($this->fields[$v])){
					$arr[$v] = $this->fields[$v];
				}elseif($v == 'id'){
					$arr['id'] = 'ID';
				}
			}
		}
		
		if($this->isMultilang)
			$arr = array_merge($arr, $this->getMultilangObject()->getTitleFields());
		
		return $arr;
		
	}
	
	/* multilang functions */
	
	/**
	 * Returns multilang object
	 *
	 * @return DataTable
	 */
	function getMultilangObject(){
		if($this->isMultilang){
			if(!$this->multilang_ob)
				$this->multilang_ob = new DataTable($this->dataDesc['multilang_entity']);
			return $this->multilang_ob;
		}
		return null;
	}
	
	function getMainLang(){
		$res = $GLOBALS['db']->query('select * from langs where 1 order by pos limit 1');
		if($t = $res->getNext())
			return $t['id'];
		return 0;
	}
	
	function getGridHead(){
		
		$arr = array();
		
		$ob_arr = array(&$this);
		if($this->isMultilang)
			$ob_arr[] = $this->getMultilangObject();
		foreach($ob_arr as $o){
			$gf = explode(',',$o->dataDesc[2][0]);
			foreach($gf as $t){
				if(isset($o->fields[$t]))
					$arr[] = $o->fields[$t][0];
				elseif($t == 'id'){
					$arr[] = 'ID';
				}
			}
		}
		return $arr;
		
	}
	
	function copyElements($entity, $cond, $replace = array()){
		
		global $db;
		
		$ob = new DataTable($entity);
		$ob->use_pager = 0;
		
		/* get multiple foreign fields */
		$multiple_foreign = array();
		foreach($ob->fields as $k => $v){
			if($v[1][0] == 'multiple_foreign'){
				$multiple_foreign[$k] = $v;
			}
		}
		
		/* get bounded entities */
		$ob->getBoundedEntities();
		
		/* get elements list */
		$elements = $ob->getGrid('', $cond, false, false);
		foreach($elements['elements'] as $v){			
			
			/* rename text field if needed */
			if(isset($ob->dataDesc['change_before_copy']) && $ob->dataDesc['change_before_copy'] != ''){
				$v[$ob->dataDesc['change_before_copy']] = $v[$ob->dataDesc['change_before_copy']].'__COPY';
			}
			/* replace falues */
			if(count($replace)){
				foreach($replace as $ri => $rv){
					$v[$ri] = $rv;
				}
			}
			
			$v_noid = $v;
			unset($v_noid['id']);
			
			/* insert new row */
			$ob->setPostArray($v_noid);
			$new_id = $ob->insertRow();
			
			if($new_id){
				/* copy multiple foreign values */
				foreach($multiple_foreign as $mi => $mv){
					$GLOBALS['db']->query('insert into '.$mi.'('.$mv[1][3].','.$mv[1][2].') select '.$mv[1][3].','.$new_id.' from '.$mi.' where '.$mv[1][2].'='.$v['id']);
				}
				foreach($ob->boundedEntities as $be){
					if(!isset($ob->dataDesc['copy_ignore_entities']) || !in_array($be[0], $ob->dataDesc['copy_ignore_entities'])){
						DataTable::copyElements($be[0], $be[1].'='.$v['id'], array($be[1] => $new_id));
					}
				}
				foreach($ob->fields as $f){
					if($f[1][0] == 'image_to_resize' && $v['orig_ext']){
						foreach($f[1][1] as $ph){
							$of = B_DIR.$ph[2].$ph[1].$v['id'].'.jpg';							
							if(file_exists($of)){
								copy($of, B_DIR.$ph[2].$ph[1].$new_id.'.jpg');
							}
						}
						if(isset($f[1]['save_orig'])){
							copy(B_DIR.$f[1]['save_orig'].$v['id'].'.'.$v['orig_ext'], B_DIR.$f[1]['save_orig'].$new_id.'.'.$v['orig_ext']);
						}
						$db->query('update '.$this->entity.' set orig_ext="'.$v['orig_ext'].'" where id='.$new_id);
					}
				}
			}
			
		}
		
	}
	
	function getLastId(){
		return $this->last_id;
	}
	
	function checkData(){
		$row = new DataRow($this->entity);
		$row->read('add', $this->post_array);
		$this->errors = $row->errors;
		return $this->renderErrors();
	}
	
	static function setEventHandler($entity, $event, $handler){		
		self::$event_handlers[$entity][$event][] = $handler;		
	}
	
	function getElementProperties($item_id, $in_card = false){
		
		global $db;
		
		$arr = array();
		
		$res = $db->query(
			'select P.id,PV.value,P.name from items_properties as P,items_property_values as PV
			where 
			PV.property_id=P.id'.($in_card ? ' and P.in_card=1' : '' ).'
			and PV.value != "" and PV.value is not null
			and PV.item_id='.intval($item_id).' order by P.pos'
		);
		
		while($v = $res->getNext()){
			$arr[$v['id']] = $v;
		}
		
		return $arr;
		
	}
	
	function autocomplete($field, $value){
		
		global $db;
		
		$value = Common::prepareGetString($value);
		
		$results = array();
		if(isset($this->fields[$field]) && trim($value) != ''){			
			$results_res = $db->query('select id,'.$field.' from '.$this->entity.' where '.$field.' like "%'.mysql_escape_string($value).'%" group by '.$field.' order by '.$field.' not like "'.mysql_escape_string($value).'%",'.$field.' limit 20');
			while($v = $results_res->getNext()){
				$results[] = array('id' => $v['id'], 'value' => Common::cropText(htmlspecialchars($v[$field]), 80, false, '...'));
			}
		}
		
		return $results;
		
	}
	
	function setOutputEscapeMode($val = true){
		$this->escape_mode = $val;
	}
	
	function isOutputEscapeMode(){
		return (bool) $this->escape_mode;
	}
	
	static function getFieldEscapeMode($entity, $field){
		if(isset($GLOBALS['entities'][$entity][1][$field][1]['escape_mode']))
			return $GLOBALS['entities'][$entity][1][$field][1]['escape_mode'];
		elseif(isset($GLOBALS['entities'][$entity]['escape_mode'])){
			return $GLOBALS['entities'][$entity]['escape_mode'];
		}else
			return '';		
	}
	
	function setFkTextMode($val = true){
		$this->fk_text_mode = $val;
	}
	
	function isFkTextMode(){
		return (bool) $this->fk_text_mode;
	}
	
	public static function getCheckAccessEntity($entity){
		return isset($GLOBALS['entities'][$entity]['force_access_rules']) ? $entity : self::getRootEntity($entity);
	}
	
	public function multiInsert($data = null){
		
		$counter = 0;
		
		if(isset($this->dataDesc['multi_insert']) && count($this->dataDesc['multi_insert'])){
		
			if(!$data && isset($_POST['multi_insert'])){
				$data = $_POST['multi_insert'];
			}
			
			$rows = array();			
			$values = array();
			
			$add_data = array();
			$allow_indexes = array('%FIELD%', '_%FIELD%_fk_text');
			
			foreach($this->dataDesc['multi_insert'] as $f){
				
				if(isset($data[$f]['min']) && isset($data[$f]['max']) && isset($data[$f]['step'])){
					
					if($data[$f]['min'] === '' || $data[$f]['max'] === '' || !$data[$f]['step']) continue;
				
					$min = $data[$f]['min'] < $data[$f]['max'] ? $data[$f]['min'] : $data[$f]['max'];
					$max = $data[$f]['max'] > $data[$f]['min'] ? $data[$f]['max'] : $data[$f]['min'];
					$step = $data[$f]['step'];

					$values[$f] = array();

					for($i = $min; $i <= $max; $i+= $step){

						$values[$f][] = $i;

					}
				
				}else{
					
					foreach($allow_indexes as $ind){
						$check_field = str_replace('%FIELD%', $f, $ind);
						if(isset($data[$check_field]) && !is_array($data[$check_field]) && trim($data[$check_field]) != ''){
							$add_data[$check_field] = $data[$check_field];
						}
					}
					
				}
				
			}
			
			if(count($values)){
			
				$_row = array();
				$_result = array();

				$rows = self::cartesian($values, -1, $_row, $_result);
				
				foreach($rows as $r){
					$r = array_merge($r, $add_data);
					if($this->foreignField)
						$r[$this->foreignField] = $this->foreignValue;
					$res = $this->insertRow($r, true);
					if($res){
						$counter++;
					}
				}

			
			}	
			
			
		}
		
		return $counter;
		
	}
	
	function cartesian($array, $key, $row, $result){
		
		$key++;
		$keys = array_keys($array);
		
		if($key < count($keys)){
			$key_index = $keys[$key];
			foreach($array[$key_index] as $v){			
				$row[$key_index] = $v;
				$result = self::cartesian($array, $key, $row, $result);		
			}			
		}else{			
			$result[] = $row;				
		}
		
		return $result;
		
	}
	
}
?>