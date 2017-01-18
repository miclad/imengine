<div class="row row--cellspace">	
	<?if(count($pages)){
		$size = ceil(12/count($pages));
	?>
		
			<?foreach($pages as $k => $v){?><?
				$cl = array();
				if($PAGE->getRootId() == $v['id'] || $PAGE->getId() == $v['id'])
					$cl[] = 'cur';
			?>
				<div class="col-<?=$size?>">
					<ul>
						<li>

							<h3><?=$v['name']?></h3>

							<?$children = DBCommon::getMenu($v['id'])?>
							<?if(count($children)){?>
								<ul>
									<?foreach($children as $ch){?>
									<li>
										<a href="<?=$ch['link']?>"><?=$ch['name']?></a>
									</li>
									<?}?>
								</ul>
							<?}?>

						</li>
					</ul>
				</div>
			<?}?>
	<?}?>
</div>