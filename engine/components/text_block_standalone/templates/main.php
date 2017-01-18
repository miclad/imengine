<div<?if($this->getConfig('anchor')){?> id="<?=$this->getConfig('anchor')?>"<?}?> class="static-content-wrap content-wrap"<?if($this->getConfig('bg')){?> style="background: <?=$this->getConfig('bg')?>"<?}?>>
	<div class="container">
		<div class="row">
			<?if($this->getConfig('sidebar')){?>
				<div class="col-3">
					<?=$this->getConfig('sidebar')?>
				</div>
			<?}?>
			<div class="<?if(!$this->getConfig('sidebar')){?>offset-3 <?}?>col-6">
				<?if($this->getConfig('title')){?>
					<h2 class="static-content-wrap__title"><?=$this->getConfig('title')?></h2>
				<?}?>
				<?=$this->getConfig('text')?>
			</div>
		</div>
		<?if($this->getConfig('rubbish_inside')){?>
			<div class="rubbish"><?=$this->getConfig('rubbish_inside')?></div>
		<?}?>
	</div>
	<?if($this->getConfig('rubbish_outside')){?>
			<div class="rubbish"><?=$this->getConfig('rubbish_outside')?></div>
		<?}?>
</div>