<?

class DiscountCards{
	
	static function cardExists($number){		
		global $db;	
		$res = $db->query('select * from discount_cards where number='.intval($number));
		return $res->selectedRowsCount();
	}
	
}

?>