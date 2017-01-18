<?

if(get_magic_quotes_gpc()){
    $_POST = array_map(array('Common', 'stripslashes_rec'), $_POST);
    $_GET = array_map(array('Common', 'stripslashes_rec'), $_GET);
}

?>