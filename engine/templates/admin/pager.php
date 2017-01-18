<ul class="pagination">
	<?if($this->currentPage > 1){?>
		<li><a href="<?=$this->baseUrl?>page=<?=($this->currentPage-1)?>">&laquo; Назад</a></li>
	<?}else{?>
		<li class="disabled"><span>&laquo; Назад</span></span>
	<?}?>
	<?if($this->firstPage > 1){?>
		<li><a href="<?=Common::editUrl(array('page'), array('page' => 1))?>">1</a></li>
		<?if($this->firstPage > 2){?>
		<li><a href="<?=Common::editUrl(array('page'), array('page' => $this->firstPage-1))?>">...</a></li>
		<?}?>
	<?}?>
	<?for($i=$this->firstPage; $i<=$this->lastPage; $i++){?>
		<li<?if($i == $this->currentPage){?> class="active"<?}?>><a href="<?=Common::editUrl(array('page'), array('page' => $i))?>"><?=$i?></a></li>
	<?}?>
	<?if($this->totalPages > $this->lastPage){?>
		<?if($this->lastPage < $this->totalPages-1){?>
		<li><a href="<?=Common::editUrl(array('page'), array('page' => $this->lastPage+1))?>">...</a></li>
		<?}?>
		<li><a href="<?=Common::editUrl(array('page'), array('page' => $this->totalPages))?>"><?=$this->totalPages?></a></li>
	<?}?>
	<?if($this->currentPage < $this->totalPages){?>
		<li><a href="<?=$this->baseUrl?>page=<?=($this->currentPage+1)?>">Вперед &raquo;</a></li>
	<?}else{?>
		<li class="disabled"><span>Вперед &raquo;</span></li>
	<?}?>
</ul>