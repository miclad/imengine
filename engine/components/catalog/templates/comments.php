<?if($comments->selectedRowsCount()){?>

	<div class="comments">

		<?/*
		<div class="comments__title"><i class="fa fa-comment-o"></i> Отзывы</div>
		*/?>

		<?while($v = $comments->getNext()){?>
		<div class="comments__item">
			<div class="comments__item__picture">			
			</div>
			<div class="comments__item__head">
				<div class="comments__item__author">
					<?=$v['author_name']?><?if(!$this->getConfig('product_id')){?> <span>про <a href="/catalog/<?=$v['url']?>/"><?=$v['model']?></a></span><?}?>
				</div>
				<div class="comments__item__date">
					<?=Site::formatDBDate($v['created_at'], false, true)?><?=$v['author_city'] ? ', '.$v['author_city'] : ''?>
				</div>
			</div>
			<div class="comments__item__text">
				<?=nl2br($v['comment'])?>
			</div>		
			<div class="comments__item__vote">
				<a href="#">
					<i class="fa fa-thumbs-o-up"></i> <span><?=($v['rate_good'] > 0 ? '+' : '').$v['rate_good']?></span>
				</a>
				<a href="#">
					<i class="fa fa-thumbs-o-down"></i> <span><?=$v['rate_bad']?></span>
				</a>
			</div>
		</div>
		<?}?>

	</div>

<?}?>