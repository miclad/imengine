<?

if(!AuthUser::getInstance()->isAdmin())
	throw new NeedAuthException();
	
$entity = isset($_GET['entity']) ? $_GET['entity'] : '';

if(!$entity)
	throw new PageNotFoundException();
	
if(isset($GLOBALS['entities'][$entity][1]['url']))
	$GLOBALS['entities'][$entity][1]['url'][2] = 0;
	
$dt = new DataTable($entity);

if($dt->entity != ''){
	
	$import_settings = isset($dt->dataDesc['import_settings']) ? $dt->dataDesc['import_settings'] : false;
	
	if(isset($import_settings['fields']) && is_array($import_settings['fields']) && count($import_settings['fields'])){
	
			set_time_limit(0);
			require_once B_DIR.'engine/lib/PHPExcel/PHPExcel.php';
			require_once B_DIR.'engine/lib/PHPExcel/PHPExcel/IOFactory.php';
			
			if(isset($_GET['archive'])){
				
				
			}else{				
				
				if(isset($_FILES['data_import']) && file_exists($_FILES['data_import']['tmp_name'])){
					
					if(!in_array(Site::getFileExtension($_FILES['data_import']['name']), array('xls', 'xlsx'))){
						
						$errors[] = 'Неверный тип файла';
						
					}else{						
						
						$counter = array('updated' => 0, 'new' => 0, 'deleted' => 0);						
						$fields = $import_settings['fields'];
						$unique_field = isset($import_settings['unique']) ? $import_settings['unique'] : 'id';
						$section_columns = isset($import_settings['section_columns_num']) ? intval($import_settings['section_columns_num']) : 0;
						$data_desc = $dt->fields;
						
						$folder_id = $dt->withFolders && isset($_GET['folder_id']) ? intval($_GET['folder_id']) : 0;
						
						/* get section properties */						
						/*
						$properties = Catalog::getSectionProperties($_GET['folder_id']);
						$tags_index = count($fields) + count($properties);
						$purposes_index = $tags_index+1;
						*/
						$properties = array();
						
						$foreign_cache = array();
						$processed_ids = array();
						
						$time_start = microtime(true);						
						
						$fname = B_DIR.'tmp/import_'.$dt->entity.'_'.date('d-m-Y_h-i-s').'.'.Site::getFileExtension($_FILES['data_import']['name']);
						copy($_FILES['data_import']['tmp_name'], $fname);
						$objPHPExcel = PHPExcel_IOFactory::load($fname);
						$objSheet = $objPHPExcel->getActiveSheet();
						$rows_num = $objSheet->getHighestRow();
												
						for($row = 1; $row <= $rows_num; $row++){
							
							$column = 0;
							
							$update_tags = false;
							
							// build fields array
							
							$data_array = array();			
							for($di=$section_columns; $di<count($fields)+$section_columns; $di++){
								
								$fn = $fields[$di-$section_columns];
								
								$is_ml = false;
								if(!isset($data_desc[$fn])){
									continue;
								}					
								
								if(!$is_ml){
									
									$val = trim($objSheet->getCellByColumnAndRow($di, $row)->getValue());									
									
									if($data_desc[$fn][1][0] == 'foreign'){
										
										$t_val = $val;
										
										if(is_numeric($t_val)){
											$val = intval($t_val);
										}else{
											$ent = $data_desc[$fn][1][1];
											if(isset($foreign_cache[$ent][$t_val])){
												$val = $foreign_cache[$ent][$t_val];
											}else{
												$res = $db->query('select id from '.$ent.' where lower(name)="'.mysql_escape_string(Common::toLowerCase($t_val)).'"')->getNext();
												if($res){
													$foreign_cache[$ent][$t_val] = $res['id'];
													$val = $res['id'];
												}else{
													$res = $db->query('insert into '.$ent.' set name="'.mysql_escape_string($t_val).'"');													
													$val = $res->lastID();
													$foreign_cache[$ent][$t_val] = $val;
												}
											}
										}
										
									}
									
									$data_array[$fn] = $data_desc[$fn][1][1] == 'number' ? intval($val) : $val;
									
								}else{
									
									/*
									$ml_num++;
									
									foreach(SiteLang::getLangs() as $l){	
										$t_val = trim($objSheet->getCellByColumnAndRow($di, $row)->getValue());							
										if(!isset($old_data_ml[$l['name']][$fn]) || $old_data_ml[$l['name']][$fn] != $t_val){
											$new_data_ml[$l['name']]['entry_id'] = $t_id;
											$new_data_ml[$l['name']]['lang'] = $l['id'];
											$new_data_ml[$l['name']][$fn] = $t_val;
											if(isset($old_data_ml[$l['name']]['id'])){
												$new_data_ml[$l['name']]['id'] = $old_data_ml[$l['name']]['id'];
											}
										}
										$di++;
									}
									*/
									
								}							
								
							}
							
							//var_dump($data_array);
							//exit();
								
							// detect section / build structure
							
							if($dt->withFolders && $section_columns > 0){
								
								$f_tree = array();
								$cur_parent = 0;
								
								for($j=0; $j<$section_columns; $j++){
									
									$nm = trim($objSheet->getCellByColumnAndRow($j, $row)->getValue());
									
									if($nm == '')
										break;
									
									$f_tree[] = $nm;
									$md = md5(serialize($f_tree));
									
									if(!isset($foreign_cache['folders'][$md])){
										$check = $db->query('select * from folders where entity="'.$dt->entity.'" and name="'.mysql_escape_string($nm).'" and parent='.$cur_parent)->getNext();
										if(!$check){
											$res = $db->query('insert into folders set name="'.mysql_escape_string($nm).'",parent='.$cur_parent.',entity="'.$dt->entity.'", pos='.DBCommon::getMaxPos('folders', 'entity="'.$dt->entity.'" and parent='.$cur_parent));
											if($res->lastID()){
												$foreign_cache['folders'][$md] = $res->lastID();
											}
										}else{
											$foreign_cache['folders'][$md] = $check['id'];
										}
									}
									
									if(isset($foreign_cache['folders'][$md])){
										$cur_parent = $foreign_cache['folders'][$md];
									}
									
								}

								if($cur_parent)
									$folder_id = $cur_parent;
									
								$data_array['folder_id'] = $folder_id;
															
							}				
							
							
							// detect id by unique field
							
							$record_id = 0;
							$record = array();
							$unique_query = array();
							
							if(!is_array($unique_field))
								$unique_query[] = $unique_field.'="'.mysql_escape_string($data_array[$unique_field]).'"';
								
							if(is_array($unique_field)){
								for($i=0; $i<count($unique_field);$i++){
									$unique_field[] = $unique_field[$i].'="'.mysql_escape_string($data_array[$unique_field[$i]]).'"';
								}
							}
							
							if(count($unique_query)){
								$record = $db->query('select * from '.$dt->entity.' where '.implode(' and ', $unique_query))->getNext();
								if($record)
									$record_id = $record['id'];
							}
							
							if($record_id)	
								$processed_ids[] = $t_id;
							
							if($data_array && count($data_array)){
								
								if($record){
									
									$update_fields = array();
									foreach($data_array as $k => $v){
										if(isset($record[$k]) && $record[$k] != $v){
											$update_fields[$k] = $v;
										}
									}
									
									if(count($update_fields)){
										$counter['updated']++;
										$update_fields['id'] = $record['id'];
										$dt->updateRow($update_fields);
									}
									
								}else{
									
									$ins_id = $dt->insertRow($data_array);
									if($ins_id){									
										$counter['new']++;									
										$ids_array[] = $ins_id;									
									}
									
								}
								
							}
							
						}	
						
						/*
						if(count($ids_array)){			
							mysql_query('update catalog set deleted=1 where folder_id='.$folder_id.' and id not in('.implode($ids_array,',').') and deleted=0');
							$counter['deleted'] = mysql_affected_rows();
						}
						*/
						
						$time_end = microtime(true);
						$counter['execution_time'] = round($time_end - $time_start,2);
						
						$_SESSION['import_result_stat'][$dt->entity] = $counter;
						
						unlink($fname);
						
					}
					
				}else{
				
					$errors[] = 'Файл не найден';
						
				}
				
			}
			
			require B_DIR.'make_urls.php';

			Header('Location: index.php?module=datatable&entity='.$dt->entity.(isset($_GET['folder_id']) ? '&folder='.intval($_GET['folder_id']) : ''));
			exit();
			
	}
			
}
			
?>