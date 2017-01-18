<?

require '../../../../conf/init.php';

if(isset($_GET['parent']) && is_numeric($_GET['parent']) && $_GET['parent'] && isset($_GET['table']) && in_array($_GET['table'], array('structure', 'folders'))){
	$res = $db->query('select * from '.$_GET['table'].' where id='.intval($_GET['parent']))->getNext();
	if($res){
		echo $res['url'];
	}
}

exit();

?>