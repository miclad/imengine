<?

DataTable::setEventHandler('user_groups', 'onAfterInsert', array('UserGroups', 'afterSave'));
DataTable::setEventHandler('user_groups', 'onAfterUpdate', array('UserGroups', 'afterSave'));

class UserGroups{
	
	function afterSave(DataTable $dt_ob){
		
		global $db;
		
		$group_id = intval($dt_ob->getLastId());
		
		if($group_id){
			
			$cur_rules = array();
			$new_rules = array();
			$res = $db->query('
				select 
				*
				from user_groups_access_rules 
				where group_id='.$group_id
			);
			while($v = $res->getNext()){
				$cur_rules[$v['target']] = $v;
			}
			
			if(isset($_POST['access']) && is_array($_POST['access'])){
				foreach($_POST['access'] as $k => $v){
					if(is_array($v) && count($v) && $v[0] != '')
					$new_rules[$k] = $v;
				}
			}
			
			$c_ind = array_keys($cur_rules);
			$n_ind = array_keys($new_rules);
			
			$to_delete = array_diff($c_ind, $n_ind);
			if(count($to_delete)){
				$db->query('
					delete 
					from 
					user_groups_access_rules
					where group_id='.$group_id.' and target in ("'.implode('","', array_map('mysql_escape_string', $to_delete)).'")
				');
			}
			
			foreach($new_rules as $k => $v){
				
				$access = implode('', $v);
				
				if(isset($cur_rules[$k])){
					if($cur_rules[$k]['access'] != $access){
						$db->query('
							update 
							user_groups_access_rules
							set access="'.mysql_escape_string($access).'"
							where id='.$cur_rules[$k]['id']
						);
					}
				}else{
					$db->query('
						insert 
						into 
						user_groups_access_rules
						set target="'.mysql_escape_string($k).'",
						group_id='.$group_id.',
						access="'.mysql_escape_string($access).'"
					');
				}
				
			}
			
			
		}
		
	}
	
}

if(!AuthUser::getInstance()->isAdmin() && AuthUser::getInstance()->getField('level') != 'manager')
	throw new NeedAuthException();
	
$dt = new DataTable('user_groups');			
if($dt->isOk){				
		
	$dt->execute();
	$page = $dt->getContents();
		
	$path = $dt->getPath();
	foreach($path as $v){
		NaviPath::addItem($v[0], (isset($v[1]) ? $v[1] : ''));
	}		
	
	$ents = $dt->getEntitiesList();
	if(count($ents)){
		$par = $dt->getParentEntity();
		foreach($ents as $k => $v){
			MenuTree::getInstance()->addItem($v['name'], $v['title'], $v['url'], array('_controls_tabs', 'datatable_'.($par ? $par : $entity)));
		}
		MenuTree::getInstance()->rebuildPath();
	}
		
	$links = $dt->getLinks();
	foreach($links as $v){	
		MenuTree::getInstance()->addItem('', $v[0], $v[1], array('_controls_buttons', 'datatable_'.$entity), '', (isset($v[2]) ? $v[2] : ''));
	}
	
	if(isset($_GET['show'])){
		
		$access_rules = array();
		$modules = Modules::getList();
		foreach($modules as $v){
			$access_rules = array_merge($access_rules, Modules::getAccessRules($v));
		}
		
		$selected_rules = array();
		
		if($dt->getRowValue('id')){
			$res = $db->query('
				select 
				*
				from
				user_groups_access_rules
				where group_id='.intval($dt->getRowValue('id'))
			);
			while($v = $res->getNext()){
				$selected_rules[$v['target']] = $v;
			}
		}
		
	}
	
	if($page)
		require $page;
		
	SiteNotices::addNotice($dt->renderErrors(), 'error');
		
}else{	
	throw new Exception('Ошибка инициализации');	
}
			
?>