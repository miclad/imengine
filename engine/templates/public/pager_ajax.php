<div class="pagination<?if(isset($params['no_pagination'])){?> no-pagination<?}?>" data-pages_num="<?=intval($this->totalPages)?>" data-items_num="<?=intval($this->itemsNum)?>" data-page_size="<?=intval($this->pageSize)?>" data-page_size_first="<?=intval($this->pageSizeFirst)?>" data-base_url="<?=Common::editUrl(array('page', 'return'))?>">
	<div class="pagination__more">
		<a href="#" class="btn">Показать ещё <span><?=$this->itemsNum - $this->pageSize > $this->pageSize ? $this->pageSize : $this->itemsNum - $this->pageSize?></span></a>
	</div>
	<div class="pagination__all">
		<a href="#" class="dotted">Показать все <?=$this->itemsNum?></a>
	</div>
</div>