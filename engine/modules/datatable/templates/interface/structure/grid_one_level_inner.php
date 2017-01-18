<table class="table table-hover">
		<?while($v = $list->getNext()){?>
			<?
			$url = $dt->entityNameVin != '' ? $dt->baseUrl.'&show=element_form&id='.$v["id"] : '';
			$is_parent = $db->query('select id from structure where parent='.$v['id'].' limit 1')->selectedRowsCount() > 0;
			?>
			<tr class="datarow<?=$url ? ' with_url' : ''?>" <?if($url){?> data-url="<?=$url?>"<?}?> data-id="<?=$v['id']?>" data-tree-level="<?=isset($v['_tree_level']) ? $v['_tree_level'] : ''?>">
				<?/*
				<td class="home_field<?if(!$v["main"]){?> inactive<?}?>">
					<?if($v["main"]){?>
						<span class="glyphicon glyphicon-home"></span>
					<?}else{?>
						<a href="<?=$dt->baseUrl?>&act=set_main&id=<?=$v["id"]?>" class="glyphicon glyphicon-home" title="Сделать стартовой"></a>
					<?}?>
				</td>
				 * 
				 */?>

				<td class="sort">
					<?if($is_parent){?>
						<a href="#" class="children-toggle fa <?if(!isset($_COOKIE['tree_structure_open_'.$v['id']])){?>fa-folder<?}else{?>fa-folder-open<?}?>" data-is-control="1" data-id="<?=$v['id']?>" data-level="<?=$level?>"><i></i></a>
					<?}else{?>
						<i class="fa fa-file-o"></i>
					<?}?>
					<?if(isset($dt->fields["pos"])){?>
						<input type="text" autocomplete="off" name="update[<?=$v['id']?>][pos]" value="<?=$v["pos"]?>">
					<?}?>
				</td>
				<td>
					
					<?=$v["name"]?>					

				</td>
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
			<?if($is_parent){?>				
				<?echo showPages($v['id'], '', $level+1, $dt)?>		
			<?}?>
		<?}?>
	</table>