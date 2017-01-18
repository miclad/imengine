<?

$items = $PAGE->getChildren();

if(!count($items)){
	$items[] = array('name' => 'На главную', 'link' => '/');
}

?>