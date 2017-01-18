<?

function __autoload($cname){
	$cname = strtolower($cname);
	if(file_exists(LIB_DIR.$cname.'.php'))
		require LIB_DIR.$cname.'.php';
}

?>