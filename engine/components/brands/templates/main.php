<?if($brand_data['photogallery_id']){
	echo Page::includeComponent('photogallery:main', array('folder' => $brand_data['photogallery_id']));
}?>
<?=$brand_data['description'] ? $brand_data['description'] : ''?>	