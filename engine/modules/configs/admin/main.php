<?

if(!AuthUser::getInstance()->hasAccess('configs'))
	throw new NeedAuthException();
	
require dirname(__FILE__).'/../conf/params.php';

if(AuthUser::getInstance()->hasAccess('configs', 'w') && isset($_POST) && count($_POST)){
	foreach($params as $k => $v){
		$val='';
		$do = true;
		switch($v[1][0]){
			case 'file':
				if(isset($_POST['del'][$k])){
					$res = $db->query('select value from configs where name="'.$k.'"');
					if($t = $res->getNext()){
						$tfl = $t['value'];
						if($tfl!='' && file_exists('../configs/'.$tfl))
							unlink('../configs/'.$tfl);
					}
				}elseif(isset($_FILES[$k]) && file_exists($_FILES[$k]['tmp_name'])){
					$val = basename($_FILES[$k]['name']);
					copy($_FILES[$k]['tmp_name'],'../configs/'.$val);
				}else
					$do=false;
			break;
			default:
				if(isset($_POST[$k]))
					$val = trim($_POST[$k]);
			break;
		}
		if($do)
			$db->query('replace into configs set name="'.$k.'",value="'.mysql_escape_string($val).'"');
	}
	SiteNotices::addNotice('Изменения сохранены');
	header('Location: '.$_SERVER['REQUEST_URI']);
	exit(0);
}

$data = array();
$res=$db->query('select * from configs where 1');
while($t = $res->getNext())
	$data[$t['name']] = $t['value'];

require dirname(__FILE__).'/../templates/main.php';
	
NaviPath::addItem('Настройки');

?>