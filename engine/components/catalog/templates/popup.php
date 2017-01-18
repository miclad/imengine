<div class="sct_col">
<?
$ipc = ceil($sections->selectedRowsCount()/4);
$i=0;
while($v = $sections->getNext()){
?>

	<div>
		<a href="/catalog<?=$v['url']?>/"<?if($v['icon']){?> rel="<?=$v['icon']?>"<?}?>><?=Common::cropText($v['name'], 20, false, '...')?></a>
	</div>
	<?if(($i+1)%$ipc == 0 && $i < $sections->selectedRowsCount()-1){?>
		</div><div class="sct_col">
	<?}?>

<?$i++;}?>
</div>

<div class="clearer"></div>