<div class="pager">
<table>
<tr>
<?if(!$this->is_all_pages){?>
	<td>Страницы:</td>
	<?if($this->currentPage > 1){?>
	<td><a href="<?=Common::editUrl(array('page'), array('page' => $this->currentPage-1))?>">предыдущая</a> |</td>
	<?}?>
	<?if($this->firstPage > 1){?>
	<td><a href="<?=Common::editUrl(array('page'), array('page' => $this->firstPage-1))?>">...</a></td>
	<?}?>
	<?for($i=$this->firstPage; $i<=$this->lastPage; $i++){?>
	<td>
	<a href="<?=Common::editUrl(array('page'), array('page' => $i))?>"<?if($i == $this->currentPage){?> class="cur nondec"<?}?>><?=$i?></a>
	</td>
	<?}?>
	<?if($this->totalPages > $this->lastPage){?>
	<td><a href="<?=Common::editUrl(array('page'), array('page' => $this->lastPage+1))?>">...</a></td>
	<?}?>
	<?if($this->currentPage < $this->totalPages){?>
	<td>|</td><td><a href="<?=Common::editUrl(array('page'), array('page' => $this->currentPage+1))?>">следующая</a></td>
	<?}?>
	<td>|</td><td><a href="<?=Common::editUrl(array('page'), array('show_all_pages' => 1))?>">показать все</a></td>
<?}else{?>
	<td><a href="<?=Common::editUrl(array('page', 'show_all_pages'), array('page' => 1))?>">вернуться к постраничному просмотру</a></td>
<?}?>
</tr>
</table>
</div>