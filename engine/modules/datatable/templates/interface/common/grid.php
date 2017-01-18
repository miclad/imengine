<?require MODULES_DIR.'datatable/templates/interface/common/filter.php'?>
<?require MODULES_DIR.'datatable/templates/interface/common/multi_insert.php'?>
<?require MODULES_DIR.'datatable/templates/interface/common/multi_update.php'?>
<?require MODULES_DIR.'datatable/templates/interface/common/multi_upload.php'?>

<?
$edit_fields = isset($dt->dataDesc['edit_in_list']) ? $dt->dataDesc['edit_in_list'] : array();
?>



	<?
	$grid = $dt->getGrid($dt->dataDesc[2][1]);
	?>
	
	<?if(count($grid["folders"]) || count($grid["elements"])){?>
	
		<?if($dt->filter->isEmpty()){?>
			<?if(count($grid["folders"])){?>
				<div class="grid">
					<form action="<?=$dt->baseUrl?>&act=update_all<?=isset($_GET['page']) ? '&page='.intval($_GET['page']) : ''?>" method="post">
						<table class="table table-hover">
							<thead>
								<th class="sort">
									<a href="#"><i class="fa fa-sort"></i></a>
								</th>
								<th colspan="2">Категория</th>
							</thead>
							<?foreach($grid["folders"] as $v){?>
							<?
							$url = $dt->baseUrl.'&folder='.$v["id"];
							?>
							<tr class="datarow<?=$url ? ' with_url' : ''?>"<?if($url){?> data-url="<?=$url?>"<?}?>>
								<td class="sort">
									<i class="fa fa-folder"></i>
									<input autocomplete="off" type="text" name="pos[f][<?=$v["id"]?>]" value="<?=$v["pos"]?>">
								</td>
								<td class="folder-name">
									<a href="<?=$dt->baseUrl?>&folder=<?=$v["id"]?>"><?=$v["name"]?></a>
								</td>							
								<td class="controls">

									<div class="controls__wrap">
										<div class="controls__body">
											<a href="#" class="controls__open"><i></i></a>
											<div class="controls__items">
												<?if(AuthUser::getInstance()->hasAccess('datatable_'.$check_access_entity, 'w')){?>
													<a href="<?=$dt->baseUrl?>&act=delete&id=<?=$v["id"]?>&sp=folder">
														<i class="fa fa-remove red"></i> Удалить
													</a>
												<?}?>
												<?if($dt->entityNameVin != ''){?>
													<a href="<?=$dt->baseUrl?>&show=folder_form&id=<?=$v["id"]?>">
														<i class="fa fa-cog"></i> Редактировать
													</a>
												<?}?>											
											</div>
										</div>
									</div>

								</td>
							</tr>
							<?}?>
						</table>
						
						<div class="grid__submit">		
							<button type="submit" class="btn"><span class="fa fa-save"></span> Сохранить</button>
						</div>
						
					</form>
				</div>
			<?}?>			
		<?}?>
		
		<?if(count($grid["elements"])){?>
			<div class="grid">
				<form action="<?=$dt->baseUrl?>&act=update_all<?=isset($_GET['page']) ? '&page='.intval($_GET['page']) : ''?>" method="post">
					<table class="table table-hover">				
						<thead>
							<th class="sort">
								<?if(isset($dt->fields["pos"])){?>
									<a href="#"><i class="fa fa-sort"></i></a>
								<?}?>
							</th>
							<?foreach($dt->getGridHead() as $k => $v){?>
								<th><?=$v?></th>
							<?}?>
							<?foreach($edit_fields as $v){?>
								<th>
									<?=$dt->fields[$v][0]?>
								</th>
							<?}?>
							<th></th>
						</thead>
						<?foreach($grid["elements"] as $v){?>
						<?
						$url = $dt->entityNameVin != '' ? $dt->baseUrl.'&show=element_form&id='.$v["id"].(isset($_GET['page']) ? '&page='.intval($_GET['page']) : '') : ''; 
						?>
						<tr class="datarow<?=$url ? ' with_url' : ''?>"<?if($url){?> data-url="<?=$url?>"<?}?>>

							<td class="sort">
								<i class="fa fa-file-o"></i>
								<?if(isset($dt->fields["pos"])){?>
									<input autocomplete="off" type="text" name="update[<?=$v['id']?>][pos]" value="<?=$v["pos"]?>">
								<?}?>
							</td>
							<?$title_fields = $dt->getTitleFields();?>
							<?$i=0;foreach($title_fields as $fn => $fd){?>
							<td <?if(is_array($fd) && ($fd[1][0]=="date" || $fd[1][0] == 'timestamp_add' || isset($fd[3][0]) && $fd[3][0] == 'select') && count($title_fields) > 1){?> class="date" nowrap<?}?><?if($i == 0 && isset($v["_tree_level"])){?> style="padding-left:<?=(10+$v["_tree_level"]*20)?>px;"<?}?>>
								<?if(isset($v[$fn.'_real'])){?>
									<?=$v[$fn.'_real']?>
								<?
								}elseif(!is_array($fd)){
									echo $v[$fn];
								}else{
									switch($fd[1][0]){
										case 'foreign':
											$val = DataTable::getRecordTitle($fd[1][1], $v[$fn], isset($fd['foreign_title_fields']) ? $fd['foreign_title_fields'] : false);
											if(!$val)
												$val = '-';
											echo $val;
										break;
										case "date":
										case "timestamp_add":
											if($v[$fn]){
												if(isset($fd[2]) || !is_numeric($v[$fn])){
													echo Site::formatDBDate($v[$fn], true, false);
												}else
													echo date("d.m.Y H:i:s", $v[$fn]);
											}
											else echo '-';
										break;
										case "file": 
											if($fd[1][1]=="image")
												echo "<img src='/".$fd[1][2].$v[$fn]."'/>";
											else
												echo "http://".$_SERVER["SERVER_NAME"]."/".$fd[1][2].$v[$fn]; 
										break;
										case "image_to_resize":
											echo "<img src='/".$fd[1][1][0][2].$fd[1][1][0][1].$v["id"].".jpg'/>";
										break;
										default: 
											if(isset($fd[3][0]) && $fd[3][0] == 'select' && !isset($fd[3]['range'])){
												$s_val = '-';
												if($v[$fn] != ''){
													$data = is_array($fd[3][1]) ? $fd[3][1] : $GLOBALS[$fd[3][1]];
													foreach($data as $st){
														if($st[0] == $v[$fn]){
															$s_val = $st[1];
															break;
														}
													}
												}			
												echo $s_val;							
											}else
												echo mb_substr(strip_tags($v[$fn]), 0, 100, 'utf8').(mb_strlen($v[$fn], 'utf8') > 100 ? '...' : '');
										break;
									}	
								}?>
							</td>
							<?$i++;}?>
							<?foreach($edit_fields as $f){?>
								<td>
									<?if($dt->fields[$f][3][0] == 'checkbox'){?>
										<input type="hidden" name="update[<?=$v['id']?>][<?=$f?>]" value="0"/>
										<input type="checkbox" name="update[<?=$v['id']?>][<?=$f?>]"<?if($v[$f]){?> checked<?}?> value="1"/>
									<?}?>
								</td>
							<?}?>
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

					<?if(count($edit_fields) || (isset($dt->fields["pos"]) && count($grid['elements']) || $dt->withFolders && count($grid["folders"]) && $dt->filter->isEmpty())){?>
						<div class="grid__submit">		
							<button type="submit" class="btn"><span class="fa fa-save"></span> Сохранить</button>
						</div>
					<?}?>
					
				</form>
				
			</div>
		<?}?>
		
		
	
	<?}?>
	
	<?if($dt->pager_ob){?>
		<div class="clear"></div>
		<?=$dt->pager_ob->printPager()?>
	<?}?>

</form>
