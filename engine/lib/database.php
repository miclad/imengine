<?
class DataBase{
	
	var $conn;
	
	function DataBase($host, $user, $pass, $db){
		$this->conn = @mysql_connect($host, $user, $pass, true) or die('Невозможно подключиться к базе данных');
		@mysql_select_db($db, $this->conn) or die("База данных не найдена");
		$this->query("set names 'utf8'");
	}
	
	/**
	 * returns result identifier
	 *
	 * @param string $q
	 * @return DBResult
	 */
	function query($q){
		$res = mysql_query($q, $this->conn);			
		if(!mysql_errno($this->conn)){
			return new DBResult($res, $this->conn);				
		}
		$this->catchError($q);
	}
		
	function catchError($text = ''){
		echo mysql_error($this->conn).'<br>'.$text;
		exit();
	}
	
	function disconnect(){
		mysql_close($this->conn);
	}
	
}
?>