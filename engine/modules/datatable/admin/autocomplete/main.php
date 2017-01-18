<?

if(!AuthUser::getInstance()->hasAccess('datatable_'.$_GET['entity']))
	throw new NeedAuthException();
	
$dt = new DataTable(isset($_GET['entity']) ? $_GET['entity'] : '');
$results = array();

$results = $dt->autocomplete(isset($_GET['field']) ? $_GET['field'] : '', isset($_GET['term']) ? $_GET['term'] : '');

echo json_encode($results);

exit();

?>