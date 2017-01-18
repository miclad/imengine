<?php

class FileTempUpload{
	
	private $var_name;
	private $record_id;
	private $formats;
	private static $path = 'uploads/tmp/';
	
	public function __construct($var_name, $record_id, $formats, $main_thumb = 'sq'){
		
		$this->var_name = $var_name;
		$this->record_id = $record_id;
		$this->formats = $formats;
		$this->main_thumb = $main_thumb;
		
		$this->savePhotos();
		
		if(isset($_POST['delete_photo']) && is_array($_POST['delete_photo'])){
			foreach($_POST['delete_photo'] as $v){
				$pi = pathinfo($v);
				$mfile = B_DIR.self::$path.$this->record_id.'/'.$v;
				if(file_exists($mfile)){
					unlink($mfile);
					$dir = B_DIR.self::$path.$this->record_id.'/'.$pi['filename'];
					if(file_exists($dir))
						Common::unlinkRecursive($dir);
				}
			}
			exit();
		}
		
	}
	
	public function savePhotos(){
		
		if(!isset($_POST['async_file_upload']))
			return false;
		
		if(isset($_FILES[$this->var_name]) && count($_FILES[$this->var_name]['name'])){
			
			$dir = B_DIR.'uploads/tmp/'.$this->record_id;
			if(!file_exists($dir) || !is_dir($dir)){
				mkdir($dir);
			}
			
			for($f=0; $f<count($_FILES[$this->var_name]['name']); $f++){
					
				if($_FILES[$this->var_name]['tmp_name'][$f] != '' && file_exists($_FILES[$this->var_name]['tmp_name'][$f])){
					
					$pi = pathinfo($_FILES[$this->var_name]['name'][$f]);
					$fname = md5(microtime().'_'.$_FILES[$this->var_name]['tmp_name'][$f].'_'.$_FILES[$this->var_name]['name'][$f]);
					$fpath = $dir.'/'.$fname.'.'.$pi['extension'];
					copy($_FILES[$this->var_name]['tmp_name'][$f], $fpath);
					
					$dir_thumbs = $dir.'/'.$fname;
					if(!file_exists($dir_thumbs) || !is_dir($dir_thumbs))
						mkdir($dir_thumbs);
					
					if(count($this->formats)){
						foreach($this->formats as $frm){
							$sz = explode(',', $frm[0]);
							Images::imageResizeUnified($fpath, $sz[0], $sz[1], $dir_thumbs.'/'.($frm[1] ? $frm[1] : '_').'.jpg', 2, $frm[3]);
						}
					}
					
					if(isset($_POST['async_file_upload'])){
						$rsp = array(
							'image' => '/'.self::$path.$this->record_id.'/'.$fname.'/'.$this->main_thumb.'.jpg',
							'id' => $fname.'.'.$pi['extension']
						);
						echo json_encode($rsp);
						exit();
					}

				}
				
			}
		}
		
	}
	
	public function getPhotos(){
		
		$result = array();
		$path = B_DIR.self::$path.$this->record_id;
		if(file_exists($path) && is_dir($path)){
			$dir = opendir(B_DIR.self::$path.$this->record_id);
			while(($f = readdir($dir)) !== false){

				if(is_dir($path.'/'.$f) || $f == '..' || $f == '.') continue;

				$item = array(
					'main_file' => $path.'/'.$f,
					'thumbs' => array()
				);

				$pi = pathinfo($f);

				foreach($this->formats as $frm){
					$item['thumbs'][$frm[1]] = $path.'/'.$pi['filename'].'/'.$frm[1].'.jpg';
				}

				$result[] = $item;

			}
		}
		
		return $result;
		
	}
	
	public function clear(){
		
		$dir = B_DIR.self::$path.$this->record_id;
		if(file_exists($dir)){
			Common::unlinkRecursive($dir);
		}
		
	}
	
}