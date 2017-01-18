<?

$type_groups = array();
$res = $db->query('select * from folders where entity="payment_types" order by pos')->fetchArray();
if(!count($res))
	$res[] = array('id' => '0', 'name' => '');
	
foreach($res as $t){	
	$t['types'] = $db->query('select * from payment_types where show_on_page=1 and folder_id='.$t['id'].' order by pos')->fetchArray();
	$type_groups[] = $t;	
}

?>