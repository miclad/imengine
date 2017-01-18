<?

$data = false;

if(isset($_GET['id']) && is_numeric($_GET['id']))
	$data = $db->query('select * from payment_types where id='.intval($_GET['id']))->getNext();
	
if(!$data)
	throw new PageNotFoundException();
	
$PAGE->addItemToPath($data['name'], '');

?>