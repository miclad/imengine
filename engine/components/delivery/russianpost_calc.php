<?

if(!defined('IN_CONTEXT'))
	require '../../conf/init.php';

$rsp = array(
	'price' => 0,
	'days' => '',
	'error' => ''
);

if(isset($_GET['to']) && isset($_GET['weight'])){
	
	$_GET['weight'] = intval($_GET['weight']/1000);

	header('Content-Type: text/html; charset=windows-1251');
	$Request = 'http://api.postcalc.ru/?st=eyescolor.ru&ml=powar@mail.ru&Person=Michael&f=190000&c=RU&t='.$_GET['to'].'&w='.$_GET['weight'].'&v=1000&Step=0&d='.date('d.m.Y').'&o=php';
	$Response = file_get_contents($Request) or die('Could not connect!');
	if ( substr($Response,0,3) == "\x1f\x8b\x08" ) $Response=gzinflate(substr($Response,10,-8));
	$t_arrResponse = unserialize($Response);
	$arrResponse = array();
	
	foreach($t_arrResponse['Отправления'] as $k => $v){
		$arrResponse[$k] = $v;
	}	
	
	if(isset($arrResponse['ЦеннаяПосылка'])){
		$rsp['price'] = $arrResponse['ЦеннаяПосылка']['Доставка'] > 0 ? $arrResponse['ЦеннаяПосылка']['Доставка'] : $arrResponse['ЦеннаяАвиаПосылка']['Доставка'];
		$rsp['days'] = $arrResponse['ЦеннаяПосылка']['Доставка'] > 0 ? $arrResponse['ЦеннаяПосылка']['СрокДоставки'] : $arrResponse['ЦеннаяАвиаПосылка']['СрокДоставки'];
		if($rsp['days']){
			$rsp['days'] .= ' дн.';
		}
	}

}

echo json_encode($rsp);
exit();
