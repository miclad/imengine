<?

$list = $db->query('select * from brands where 1 order by name'.($this->getConfig('limit') ? ' limit '.intval($this->getConfig('limit')) : ''));

?>