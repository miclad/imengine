<?

if(!defined(IN_CONTEXT)){
	require '../../conf/init.php';
}

$result = false;

if(isset($_POST['address']) && trim($_POST['address']) != ''){
	
	$result = GMaps::getLatLng(urldecode($_POST['address']));
	
}

echo json_encode($result);

?>