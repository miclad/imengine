<?

if(!AuthUser::getInstance()->isAdmin())
	throw new NeedAuthException();
	
$entity = isset($_GET['entity']) ? $_GET['entity'] : '';

if(!$entity)
	throw new PageNotFoundException();
	
$dt = new DataTable($entity);

if($dt->entity != ''){
	
	$import_settings = isset($dt->dataDesc['import_settings']) ? $dt->dataDesc['import_settings'] : false;
	
	if(isset($import_settings['fields']) && is_array($import_settings['fields']) && count($import_settings['fields'])){

			require_once B_DIR.'engine/lib/PHPExcel/PHPExcel.php';
			require_once B_DIR.'engine/lib/PHPExcel/PHPExcel/IOFactory.php';
			
			$folder_id = 0;
			$filename = $dt->entity;
			
			/* 
			Build filename 
			*/
			
			if(isset($_GET['folder_id'])){
				$tree = new Tree('folders', 'entity="'.$dt->entity.'"');
				$tp = $tree->getPath('M.*', intval($_GET['folder_id']));
				if(count($tp)){
					$filename = '';
					$folder_id = $tp[count($tp)-1]['id'];
					foreach($tp as $k => $v){
						$filename .= str_replace(' ','_',$v['name']).($k < count($tp)-1 ? '-' : '');
					}
				}
			}
			
			/*
			Init Entity Description
			*/
			
			$t_fields = $import_settings['fields'];
			$section_columns = isset($import_settings['section_columns_num']) ? intval($import_settings['section_columns_num']) : 0;
			$data_desc = $dt->fields;
			$fields = array();
			
			foreach($t_fields as $k => $v){
				$fields[$k]['name'] = $v;
				$fields[$k]['is_multilang'] = false;
				$fields[$k]['title'] = isset($data_desc[$v][0]) ? $data_desc[$v][0] : $v;
				$fields[$k]['description'] = isset($data_desc[$v][1]) ? $data_desc[$v][1] : $v;
			}	
			
			$data = array();
			
			/*
			Build Title
			*/
			
			if(isset($dt->dataDesc['export_settings']['show_title']) && $dt->dataDesc['export_settings']['show_title']){
				$data[0][] = 'Product ID';
				foreach($fields as $f){
					if(!$f['is_multilang']){		
						$data[0][] = $f['title'];
					}else{
						foreach(SiteLang::getLangs() as $l){
							$data[0][] = $f['title']." (".$l['name'].")";
						}
					}
				}
			}
			
			/*
			Build Elements
			*/
			
			$foreign_cache = array();
			
			$folders = array();
			$cur_folder_path = array();
			$item_folder_path = array();
			
			if($dt->withFolders){
				
				$tree = new Tree('folders', 'entity="'.$dt->entity.'"');
				
				$folders = $tree->getFullTree($folder_id);
					
				if($folder_id){
					$cur_folder_path = $tree->getPath('*', $folder_id);
				}
				
			}
			
			$folders = array_merge(array(array('id' => $folder_id)), $folders);
				
			foreach($folders as $k => $fld){
				
				if($dt->withFolders){
					
					$dt->setFilter(array('folder_id' => $fld['id']), false);
					
					if(isset($fld['_tree_level'])){
						
						if($k > 0 && $fld['_tree_level'] <= $folders[$k-1]['_tree_level']){
							for($i=0; $i<=$folders[$k-1]['_tree_level']-$fld['_tree_level']; $i++){
								array_pop($item_folder_path);
							}
						}
						
						$item_folder_path[] = $fld;
						
					}else{
						$item_folder_path = array();
					}
					
				}
					
				$elements = $dt->getGrid();
				
				foreach($elements['elements'] as $item){
					
					$index = count($data);
					
					$data[$index] = array();
					
					// build sections structure
					
					if($dt->withFolders){
						$sc = 0;
						for($i=0; $i<count($cur_folder_path); $i++){
							$data[$index][] = $cur_folder_path[$i]['name'];
							$sc++;
						}
						foreach($item_folder_path as $v){
							$data[$index][] = $v['name'];
							$sc++;
						}
						if($sc < $section_columns){
							for($i=0; $i<$section_columns-$sc; $i++){
								$data[$index][] = '';
							}
						}
					}
					
					// build data array
					
					foreach($fields as $f){
						
						if(!$f['is_multilang']){
							
							$val = '';
							
							if($f['description'][0] == 'foreign'){	
											
								if($item[$f['name']] != ''){
									if(isset($foreign_cache[$f['name']]['id'.$item[$f['name']]])){
										$val = $foreign_cache[$f['name']]['id'.$item[$f['name']]];
									}else{
										$res = $db->query('select name from '.$f['description'][1].' where id='.intval($item[$f['name']]))->getNext();
										if($res){
											$val = $res['name'];
											$foreign_cache[$f['name']]['id'.$item[$f['name']]] = $val;
										}
									}
								}							
								
							}else{
								$val = $item[$f['name']];
							}
							
							$data[$index][] = $val;	
								
						}else{		
											
							$res = $db->query('select C.* from langs as L left join catalog_lang as C on C.entry_id='.$v['id'].' and C.lang=L.id order by L.pos');
							while($l = $res->getNext()){
								$data[$index][] = $l[$f['name']];			 
							}
							
						}
						
					}							
					
				}
			
			}
			
			/*
			echo '<pre>';
			var_dump($data);
			echo '</pre>';
			exit();
			*/
			
			$objPHPExcel = new PHPExcel();
			$objPHPExcel->getProperties()->setCreator("ImEngine");
			$objPHPExcel->setActiveSheetIndex(0);
			$objPHPExcel->getActiveSheet()->setTitle($dt->entity);
			foreach($data as $row_num => $row){
				foreach($row as $column_num => $value){
					$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($column_num, $row_num+1, $value);
				}
			}
			
			header('Content-Type: application/vnd.ms-excel');
			header('Content-Disposition: attachment;filename="'.$filename.'.xls"');
			header('Cache-Control: max-age=0');
			
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
			$objWriter->save('php://output'); 
			
			exit();
			
	}
			
}
			
?>