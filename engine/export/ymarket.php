<?
define('NO_CONTENT_TYPE_HEADER', true);
header("Content-Type: text/xml; charset=utf-8");
require '../conf/init.php';
mysql_set_charset('utf8');
?>
<?
echo '<?xml version="1.0" encoding="utf-8"?>';
?>
<!DOCTYPE yml_catalog SYSTEM "shops.dtd">
<yml_catalog date="<?=date('Y-m-d H:i')?>">
	<shop>
		<name><?=$_SERVER['SERVER_NAME']?></name>
		<company><?=$_SERVER['SERVER_NAME']?></company>
		<url>http://<?=$_SERVER['SERVER_NAME']?>/</url>		
		<currencies>
			<currency id="RUR" rate="1"/>
		</currencies>
		<categories>
			<?
			$tree = new Tree('folders', 'entity="catalog"');
			$sections = $tree->getFullTree(0);
			?>
			<?foreach($sections as $v){?>
			<category id="<?=$v['id']?>"<?if($v['parent']){?> parentId="<?=$v['parent']?>"<?}?>><?=htmlspecialchars($v['name'])?></category>
			<?}?>
		</categories>
		<offers>
			<?
			
			$filter = array(
				'active' => 1,
				//'avail' => 1,
				//'yandex_market' => 1
			);
				
			$order_by = 'B.name,T.model,T.price';
			$res = Catalog::getElements($filter, '', $order_by);
			while($v = $res->getNext()){
			?>
			<offer id="<?=$v['id']?>"<?if($v['brand_name'] != ''){?> type="vendor.model"<?}?> available="<?=($v['avail'] ? 'true' : 'false')?>">
				<url>http://<?=$_SERVER['SERVER_NAME']?>/catalog/<?=urlencode($v['url']).'/'?></url>
				<price><?=round($v['price'])?></price>
				<currencyId>RUR</currencyId>
				<categoryId><?=$v['folder_id']?></categoryId>
				<?if($v['photo_id'] != ''){?>
				<picture>http://<?=$_SERVER['SERVER_NAME']?>/img/catalog/more/big<?=$v['photo_id']?>.jpg</picture>
				<?}?>
				<delivery>true</delivery>
				<?if($v['section_name'] != '' && $v['brand_name'] != ''){?>
				<typePrefix><?=htmlspecialchars($v['section_name'])?></typePrefix>
				<?}?>
				<?if($v['brand_name'] != ''){?>
					<vendor><?=htmlspecialchars($v['brand_name'])?></vendor>
					<model><?=trim(htmlspecialchars($v['_model']))?></model>
  				<?}else{?>
					<name><?=trim(htmlspecialchars($v['_model']))?></name>
				<?}?>		
				<?if($v['package']){?>
					<param name="Упаковка"><?=$v['package']?></param>
				<?}?>
				<?if($v['color']){?>
					<param name="Цвет"><?=$v['color']?></param>
				<?}?>
				<?if($v['short_desc']){?>
					<description><![CDATA[<?=preg_replace('/[^0-9)(A-zА-я _,:"]/u', '', $v['short_desc'])?>]]></description>
				<?}?>
				<?/*
				<sales_notes></sales_notes>
				*/?>
			</offer>
			<?}?>
		</offers>
	</shop>
</yml_catalog>
