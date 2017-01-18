<?require MODULES_DIR.'datatable/templates/interface/common/filter.php'?>

<form action="<?=$dt->baseUrl?>&act=setpos" method="post">

	<?
	$grid = $dt->getGrid($dt->dataDesc[2][1]);
	?>
	
	<?if(count($grid["folders"]) || count($grid["elements"])){?>
	
		<?if($dt->filter->isEmpty()){?>
			<?if(count($grid["folders"])){?>
				<div class="grid">
					<table class="table table-hover">
						<?foreach($grid["folders"] as $v){?>
						<?
						$url = $dt->baseUrl.'&show=folder_form&id='.$v["id"];
						?>
						<tr class="datarow<?=$url ? ' with_url' : ''?>"<?if($url){?> data-url="<?=$url?>"<?}?>>
							<td class="sort"><input type="text" name="pos[f][<?=$v["id"]?>]" value="<?=$v["pos"]?>" class="short"></td>
							<td class="folder_name">
								<span class="glyphicon glyphicon-folder-close"></span><a href="<?=$dt->baseUrl?>&folder=<?=$v["id"]?>"><?=$v["name"]?></a>
							</td>
							<td class="control">
								<a href="<?=$dt->baseUrl?>&show=folder_form&id=<?=$v["id"]?>" class="glyphicon glyphicon-pencil" title="Редактировать"></a>
								<?if(AuthUser::getInstance()->hasAccess('datatable_'.$check_access_entity, 'w')){?>
									<a href="<?=$dt->baseUrl?>&act=delete&id=<?=$v["id"]?>&sp=folder" class="glyphicon glyphicon-remove" title="Удалить"></a>
								<?}?>
							</td>
						</tr>
						<?}?>
					</table>
				</div>
				<?if(count($grid["elements"])){?>
					<div class="space sm"></div>
				<?}?>
			<?}?>			
		<?}?>
		
		<?if(count($grid["elements"])){?>
		<div class="grid">
			<table class="table table-hover">				
				<?foreach($grid["elements"] as $v){?>
				<?
				$url = $dt->entityNameVin != '' ? $dt->baseUrl.'&show=element_form&id='.$v["id"] : ''; 
				?>
				<tr class="datarow<?=$url ? ' with_url' : ''?>"<?if($url){?> data-url="<?=$url?>"<?}?>>
					<?if(isset($dt->fields["pos"])){?>
					<td class="sort"><input type="text" name="pos[e][<?=$v["id"]?>]" value="<?=$v["pos"]?>" class="short"></td>
					<?}?>
					<td>
						<?if(trim($v["code"])){echo htmlspecialchars($v["code"]);}elseif($v["file"]!=""){echo DBCommon::show_image("/img/ab/", $v["file"], 400);}?>
					</td>
					<td class="control">
						<?if(isset($dt->dataDesc['copy']) && $dt->dataDesc['copy']){?>
							<?if(AuthUser::getInstance()->hasAccess('datatable_'.$check_access_entity, 'w')){?>
								<a href="<?=$dt->baseUrl?>&act=copy&id=<?=$v["id"]?>">Копировать</a>
							<?}?>
						<?}?>
						<?if(false && $dt->entityNameVin != ''){?>
							<a href="<?=$dt->baseUrl?>&show=element_form&id=<?=$v["id"]?>"><img src="/engine/templates/admin/img/edit_hdn.gif" title="Редактировать"/></a>
						<?}?>
						<?if(AuthUser::getInstance()->hasAccess('datatable_'.$check_access_entity, 'w')){?>
							<a href="<?=$dt->baseUrl?>&act=delete&id=<?=$v["id"]?><?=isset($_GET['page']) ? '&page='.intval($_GET['page']) : ''?>" class="glyphicon glyphicon-remove" title="Удалить"></a>
						<?}?>
					</td>
				</tr>	
				<?}?>	
			</table>
		</div>
		<?}?>
		
		<?if(isset($dt->fields["pos"]) && count($grid['elements']) || $dt->withFolders && count($grid["folders"]) && $dt->filter->isEmpty()){?>
			<button type="submit" class="btn">Сохранить</button>
		<?}?>
	
	<?}?>
	
	<?if($dt->pager_ob){?>
		<?=$dt->pager_ob->printPager()?>
	<?}?>

</form>
