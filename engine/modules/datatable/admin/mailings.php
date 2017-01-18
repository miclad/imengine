<?
	
if(isset($_GET['show']) && $_GET['show'] == 'element_form'){
	$res = $db->query('select type,count(*) as cnt from mailing_list group by type order by type');
	$GLOBALS['mailing_groups'] = array();
	while($v = $res->getNext()){
		$GLOBALS['mailing_groups'][] = array($v['type'], $v['type'].' ('.$v['cnt'].')');
	}
}

$entity = isset($_GET['entity']) && $_GET['entity'] != '' ? $_GET['entity'] : '';

if(true){
	
	if(isset($_GET['send_emails']) && isset($_GET['id']) && AuthUser::getInstance()->hasAccess('datatable_mailings', 'w')){
				
		$mailing_data = $db->query('select * from mailings where id='.intval($_GET['id']))->getNext();
		if($mailing_data){					
				
			//$mail_cnt = Page::includeComponent('mailings', array(), array('id' => $_GET['id']), 'main', '', true);
			$mail_cnt = $mailing_data['top_description'];
			if(trim($mail_cnt) != ''){
					
				$db->query('update mailings set sent_at=NOW(),complete_at=null,last_sent_id=0 where id='.$mailing_data['id']);
				
				$addresses = $mailing_data['direct_emails'] ? explode(',', $mailing_data['direct_emails']) : $db->query('select * from mailing_list where event="news"'.($mailing_data['address_group'] ? ' and type="'.mysql_escape_string($mailing_data['address_group']).'"' : ''))->fetchArray();
				
				$counter = intval($mailing_data['counter']);
				$mail_subject = $mailing_data['email_subject'];
				$mail_from = str_replace('www.', '', DBCommon::getConfig('mailing_from'));
				$last_sent_id = 0;
				foreach($addresses as $v){
					if(!is_array($v)){
						$v = array('name' => $v, 'email' => $v);
					}
					$cnt = str_replace(array('{to_name}', '{unsubscribe_link}'), array(($v['name'] ? ', '.$v['name'] : ''), $v['email']), $mail_cnt);
					$mail_to = $v['email'];							
					Email::sendMail($mail_to, $mail_subject, $cnt, $mail_from, '', '', 'text/html');
					$counter++;
					$last_sent_id = $v['id'];
				}
				
				$db->query('update mailings set counter='.$counter.',complete_at=NOW(),last_sent_id='.$last_sent_id.' where id='.$mailing_data['id']);
				Header('Location: index.php?module=datatable&entity=mailings');
				exit();
				
			}
			
		}
				
	}
	
	$dt = new DataTable($entity);			
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
		
		if($page)
			require $page;
			
		SiteNotices::addNotice($dt->renderErrors(), 'error');
		
	}else{	
		throw new Exception('Ошибка инициализации');	
	}

}
			
?>