<div class="space"></div>
<table class="like_buttons"<?if($this->getConfig('name')){?> id="likes_<?=$this->getConfig('name')?>"<?}?><?if($this->getConfig('align')){?> align="<?=$this->getConfig('align')?>"<?}?>>
	<tr>
		<td class="i">
			<div id="vk_like_<?=$this->getConfig('name')?>"></div>
			<script type="text/javascript">
				VK.Widgets.Like("vk_like_<?=$this->getConfig('name')?>", {type: "mini", pageUrl: '<?='http://'.$_SERVER['SERVER_NAME'].'/'.$this->getConfig('url')?>'}<?if($this->getConfig('id')){?>, <?=intval($this->getConfig('id'))?><?}?>);
			</script>
		</td>
		<td class="i">
			<div class="fb-like" data-href="<?='http://'.$_SERVER['SERVER_NAME'].'/'.$this->getConfig('url')?>" data-send="false" data-layout="button_count" data-width="450" data-show-faces="false"></div>
		</td>
		<td class="i">
			<a href="https://twitter.com/share" class="twitter-share-button" data-via="<?=DBCommon::getConfig('twitter_name')?>" data-related="<?=DBCommon::getConfig('twitter_name')?>">Tweet</a>
			<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
		</td>
	</tr>
</table>