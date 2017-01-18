<?
echo "-----\nИнформация о заказчике\n-----\n\n";

echo "Имя: ".$form->getValue('name')."\n";
echo "Телефон: ";
echo ($form->getValue('phone') ? $form->getValue('phone') : "не указан")."\n";
echo "E-mail: ";
echo ($form->getValue('email') ? $form->getValue('email') : "не указан")."\n";
echo "Адрес доставки: ";
echo $form->getValue('address')."\n";
if($form->getValue('comments') != '')
	echo "Комментарии: ".$form->getValue('comments')."\n";

	
echo "\n-----\nЗаказ\n-----\n\n";

$total_sum = $total_num = 0;
foreach($wl_items as $v){
	echo $v['model'].($v['variant_name'] != "" ? ', '.$v['variant_name'] : "").", ".$v['items_num']." шт., цена ".round($v['price'],2)." руб.\n";
	$total_sum += $v['items_num']*$v['price'];
	$total_num += $v['items_num'];
}

$discount = Catalog::getDiscount($total_sum);
echo "\nИтого: ".$total_num." шт. на сумму ".Site::formatPrice(round($discount['total_sum'],2))." руб.".($discount['perc'] ? " (с учетом скидки ".$discount['perc']."%)" : "")."\n\n";
?>