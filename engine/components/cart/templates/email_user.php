<?

echo "Здравствуйте, ".$_POST['name']."!\n\n";

echo "Номер вашего заказа: ".$oid."\n\n";
	
echo DBCommon::getConfig('order_email_before') ? DBCommon::getConfig('order_email_before')."\n\n" : "";

$total_sum = 0;
$total_num = 0;
$k = 1;
foreach($wl_items as $v){
	echo $k.". ".$v['model'].($v['v_name'] != "" ? ', '.$v['v_name'] : "").", ".$v['items_num']." шт., ".Site::formatPrice($v['items_num']*$v['price'])." руб.\n";
	$total_sum += $v['items_num']*$v['price'];
	$k++;
	$total_num += $v['items_num'];
}

echo "\nИтого: ".$total_num." шт. на сумму ".Site::formatPrice(round($discount['total_sum'],2))." руб.".($discount['perc'] ? " (с учетом скидки ".$discount['perc']."%)" : "")."\n\n";

$deliv = Delivery::parseDeliveryType($delivery_type);

echo "E-mail: ";
echo ($_POST['email'] ? $_POST['email'] : "не указан")."\n";
echo "Телефон: ";
echo ($_POST['phone'] ? $_POST['phone'] : "не указан")."\n";
echo "Тип доставки: ";
echo ($deliv ? $deliv : " не выбран")."\n";
echo "Адрес доставки: ";
echo $_POST['address']."\n";
if($delivery_price){
	echo "Стоимость доставки: ";
	echo $delivery_price." руб.\n";
}

echo "\n\n".DBCommon::getConfig('order_email_after');

?>