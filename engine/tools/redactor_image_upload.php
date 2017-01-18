<?

$dir = '/img/lib/';
$fname = "";

if(isset($_FILES["file"]) && file_exists($_FILES['file']['tmp_name'])){
	
	if(preg_match("/^(?i).+?\.(jpg|jpeg|png|gif|bmp)$/", $_FILES["file"]["name"], $pock)){
		
		$fname = $dir.md5(basename($_FILES["file"]["name"]).'_'.time()).'.'.strtolower($pock[1]);
		copy($_FILES["file"]["tmp_name"], $_SERVER["DOCUMENT_ROOT"].$fname);
		
		$result = array(
			'filelink' => $fname
		);
		
		echo stripslashes(json_encode($result));
		
	}
	
}

?>