

	<?if($this->getConfig('is_search')){?>
		<?if($items->selectedRowsCount()){?>
			<p>Найдено товаров: <?=$items->selectedRowsCount()?></p>
		<?}?>
	<?}elseif(!$this->is_inner){?>

		<?if(isset($section_data) && $section_data['short_desc']){?>
			<p class="section_descr"><?=nl2br($section_data['short_desc'])?></p>
		<?}?>

		<?if($PAGE->getUrl() == '/catalog' && isset($section_data) && $section_data){?>

			<?/*
			<?$brands = Catalog::getSectionBrands($section_data['id'], false)?>
			<?if(count($brands)){?>
				<div class="catalog_filter_brands">
					Производители:
					<?if($this->getInitParam('brand')){?>
						<a href="/catalog<?=$section_data['url']?>/"<?if(!$this->getInitParam('brand')){?> class="cur"<?}?>>Все</a>
					<?}?>
					<?foreach($brands as $v){?>
						<a href="/catalog<?=$section_data['url']?>/?brand=<?=$v['id']?>"<?if($v['id'] == $this->getInitParam('brand')){?> class="cur"<?}?>><?=$v['name']?></a>
					<?}?>
				</div>
			<?}?>
			*/?>

		<?}elseif($PAGE->getUrl() == '/brands' && $PAGE->getAttribute('current_brand')){?>

			<?
			$b_sections = $db->query('select F.* from folders as F,catalog as C where C.folder_id=F.id and C.brand='.intval($PAGE->getAttribute('current_brand')).' and C.active=1 and C.deleted=0 group by F.id order by F.name');
			if(count($b_sections)){
			?>
				<div class="rnd_box catalog_filter">
					<?if(true || isset($filter['folder_id'])){?>
						<a href="/brands/<?=$brand_data['url'] ? $brand_data['url'] : $brand_data['id']?>/"<?if(!isset($filter['folder_id'])){?> class="cur"<?}?>>Все категории</a>
					<?}?>
					<?while($v = $b_sections->getNext()){
						//if($v['parent'])
						//	$v = $db->query('select * from folders where id='.$v['parent'])->getNext();
					?>
						<a href="/brands/<?=$brand_data['url'] ? $brand_data['url'] : $brand_data['id']?>/?category=<?=$v['id']?>"<?if($v['id'] == (isset($filter['folder_id']) ? $filter['folder_id'] : 0)){?> class="cur"<?}?>><?=$v['name']?></a>
					<?$i++;}?>
				</div>
			<?}?>

		<?}?>

	<?}?>

	<?if($items->selectedRowsCount()){?>
			
		<div id="content">
			<?require 'items_gallery.php'?>
		</div>
				
		<?=$items->printPager('pager_ajax.php')?>

		<?if(isset($section_data) && $section_data['description']){?>
			<p><?=$section_data['description']?></p>
		<?}?>	

	<?}?>

	<?if(!$this->is_inner && !$items->selectedRowsCount()){?>
		<?if($this->getConfig('is_search')){?>
			<?=$this->getConfig('search_nothing_found')?>
			<?=Page::includeComponent('catalog:items', array('order_by' => 'rating desc', 'limit' => 4, 'items_per_row' => 4), array('folder_id' => 0))?>
		<?}else{?>
			Товары не найдены
		<?}?>
	<?}?>

	<?if($this->getConfig('favorites')){?>
		<div id="favorites-empty"<?if($items->selectedRowsCount()){?> style="display: none;"<?}?>>
			<?=$this->getConfig('favorites_empty_text')?>
		</div>
	<?}?>
