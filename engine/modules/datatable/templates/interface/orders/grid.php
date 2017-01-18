<?require MODULES_DIR.'datatable/templates/interface/common/filter.php'?>
<?require MODULES_DIR.'datatable/templates/interface/common/multi_upload.php'?>

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
						$url = $dt->baseUrl.'&folder='.$v["id"];
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
				<thead>					
					<th>ID</th>
					<th>
						Время
					</th>
					<th>
						ФИО
					</th>
					<th>
						Сумма
					</th>
					<th>
						Статус
					</th>
					<?/*
					<th>
						Статус оплаты
					</th>
					*/?>
					<th></th>
				</thead>
				<?foreach($grid['elements'] as $k => $v){?>
				<?
				$date_t = explode(' ', $v['date']);
				if($k > 0){
					$date_t_prev = explode(' ', $grid['elements'][$k-1]['date']);
				}
				?>
				<?if($k == 0 || $date_t[0] != $date_t_prev[0]){?>
					<td colspan="7" class="grid__separator">
						<?=Site::formatDBDate($date_t[0], false, true)?>
					</td>
				<?}?>
				<tr class="datarow with_url" data-url="index.php?module=datatable&entity=order_items&order_id=<?=$v['id']?>">
					<td class="id">
						<b><?=$v['id']?></b>
					</td>
					<td class="date" nowrap>
						<?=substr($v['date'], 11, 5)?>
					</td>
					<td>
						<?=$v['name'].' '.$v['surname']?>
					</td>
					<td>
						<?=$v['sum_discount'] ? $v['sum_discount'] : $v['sum']?> руб.
					</td>
					<td>
					
						<?
						if($v['status']){
							$st = $db->query('select name from order_statuses where id='.$v['status'])->getNext();
							if($st){
								echo $st['name'];
							}				
						}
						?>
					
					</td>
					<?/*
					<td>
					
						<?
						if($v['status_payment']){
							$st = $db->query('select name from order_statuses where id='.$v['status_payment'])->getNext();
							if($st){
								echo $st['name'];
							}				
						}
						?>
					
					</td>
					*/?>
					<td class="controls">
						<div class="controls__wrap">
									<div class="controls__body">
										<a href="#" class="controls__open"><i></i></a>
										<div class="controls__items">
											<?if(AuthUser::getInstance()->hasAccess('datatable_'.$check_access_entity, 'w')){?>
												<a href="<?=$dt->baseUrl?>&act=delete&id=<?=$v["id"]?><?=isset($_GET['page']) ? '&page='.intval($_GET['page']) : ''?>">
													<i class="fa fa-remove red"></i> Удалить
												</a>
											<?}?>
											<?if(isset($dt->dataDesc['copy']) && $dt->dataDesc['copy']){?>
												<?if(AuthUser::getInstance()->hasAccess('datatable_'.$check_access_entity, 'w')){?>
													<a href="<?=$dt->baseUrl?>&act=copy&id=<?=$v["id"]?>">
														<i class="fa fa-copy"></i> Копировать
													</a>
												<?}?>
											<?}?>
											<?if(false && $dt->entityNameVin != ''){?>
												<a href="<?=$dt->baseUrl?>&show=element_form&id=<?=$v["id"]?><?=isset($_GET['page']) ? '&page='.intval($_GET['page']) : ''?>"></a>
											<?}?>

										</div>
									</div>
						</div>
					</td>
				</tr>	
				<?}?>	
			</table>
		</div>
		<?}?>
		
		<?if(isset($dt->fields["pos"]) && count($grid['elements']) || $dt->withFolders && count($grid["folders"]) && $dt->filter->isEmpty()){?>
			<button type="submit" class="btn"><span class="glyphicon glyphicon-ok grey"></span> Сохранить</button>
		<?}?>
	
	<?}?>
	
	<?if($dt->pager_ob){?>
		<?=$dt->pager_ob->printPager()?>
	<?}?>

</form>

<?if(!count($grid['elements'])){?>
	Нет заказов
<?}?>
