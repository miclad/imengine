<?

if(!defined('IN_CONTEXT')) exit();

$q = '';
$result = '';

if(isset($_POST['email'])){
	$_POST['email'] = substr($_POST['email'], 0, 255);
	$q = 'login="'.mysql_escape_string($_POST['email']).'"';
}elseif(isset($_POST['phone'])){
	$_POST['phone'] = preg_replace('/[^0-9]/', '', $_POST['phone']);
	if(strlen($_POST['phone']) >= 10 && strlen($_POST['phone']) < 15){
		if(strlen($_POST['phone']) > 10)
			$_POST['phone'] = substr($_POST['phone'], 0, 10);
		$q = 'login="'.mysql_escape_string($_POST['phone']).'"';
	}
}

if($q != ''){
	$res = $db->query('select * from users where '.$q);
	if($res->selectedRowsCount()){
		$data = $res->getNext();
		$result = md5($data['email'].'_'.$data['id']);
	}
}

echo $result;
exit();

?>