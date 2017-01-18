<?
class Images{
	
	
	/*
	return width and height due to new image format conception
	Params:
	$is - source image info
	$v - format info
	*/
	function getNewImageSize($is,$v){
		$nw = $nh = 0;
	    if($v[2] == 0){
	    	$ratio = $is[0]>$is[1]?3/2:2/3;
	    }elseif($v[2] == 1){
	    	$ratio = $is[0]/$is[1];
	    }elseif($v[2] == 2){
	    	$nw = $v[0];
	    	$nh = $v[1];
	    }
	    if($v[2]!=2 && ($v[0] || $v[1])){
	    	/*only one dimension given*/
	    	if(!($v[0]*$v[1])){
	    		if($v[0]){
	    			$nw = $v[0];
	    			$nh = $nw/$ratio;
	    		}else{
	    			$nh = $v[1];
	    			$nw = $nh*$ratio;
	    		}
	    	}else{
	    		if($is[0]>$is[1]){
	    			$nw = $v[0];
	    			$nh = $nw/$ratio;
	    			
	    			if($nh > $v[1]){
	    				$nh = $v[1];
	    				$nw = $nh*$ratio;
	    			}
	    			
	    		}else{
	    			$nh = $v[1];
	    			$nw = $nh*$ratio;
	    			
	    			if($nw > $v[0]){
	    				$nw = $v[0];
	    				$nh = $nw/$ratio;
	    			}
	    		}
	    	}
	    }
	    return array($nw,$nh);
	}
	
function imageResizeUnified($src, $nw, $nh, $dst, $img_type = 2, $resize_type = 0, $filter = false, $filter_param = false, $save_proportions = true, $save_extension = false){
		
		/* check if file exists */
		if(file_exists($src)){
			if(is_numeric($nw) && $nw > 0 || is_numeric($nh) && $nh > 0){
				
				$real_nw = $real_nh = 0;
				list($src_w,$src_h) = getimagesize($src);
				$ratioSrc = $src_w/$src_h;
				
				if(!$save_proportions){
					$real_nw = $nw;
					$real_nh = $nh;
				}else{

					if($nw && $nh && $nw >= $src_w && $nh >= $src_h){
						$real_nw = $src_w;	
						$real_nh = $src_h;
					}else{				
						
						if(!$nw){
							$nw = $ratioSrc*$nh;
						}elseif(!$nh){
							$nh = $nw/$ratioSrc;
						}
					
						$ratioNew = $nw/$nh;
						if($resize_type == 0 || $resize_type == 1){
							if($ratioSrc > $ratioNew){
								$real_nw = $nw;
								$real_nh = $nw/$ratioSrc;
							}else{
								$real_nh = $nh;
								$real_nw = $nh*$ratioSrc;
							}
						}else{
							if($ratioSrc > $ratioNew){
								$real_nh = $nh;
								$real_nw = $nh*$ratioSrc;
							}else{
								$real_nw = $nw;
								$real_nh = $nw/$ratioSrc;
							}
						}
					
					}
				
				}
				
				preg_match("/^.+?\.([A-z]+)$/",$src,$ext);
				$s_ext = "";
				if(isset($ext[1]))
					$s_ext = $ext[1];
				else
					$s_ext = $img_type;
				switch(strtolower($s_ext)){
					case "1":
					case "gif":
						$s_im = imagecreatefromstring(file_get_contents($src));
					break;
					case "2":
					case "jpeg":
					case "jpg":
						$s_im = imagecreatefromjpeg($src);
					break;
					case "3":
					case "png":
						$s_im = imagecreatefrompng($src);
						imagesavealpha($s_im, true);
					break;
				}
				
				if($resize_type == 0){
					$im_width = $real_nw;
					$im_height = $real_nh;
				}else{
					$im_width = $nw;
					$im_height = $nh;
				}
				
				$im_left = $im_top = 0;
				
				if($im_width != $real_nw)
					$im_left = ($im_width - $real_nw) / 2;
				if($im_height != $real_nh)
					$im_top = ($im_height - $real_nh) / 2;
				
				$pi = pathinfo($dst);
					
				$d1_img = imagecreatetruecolor($im_width,$im_height);
				if($pi['extension'] != 'png'){
					$cl = imagecolorallocate($d1_img, 255, 255, 255);
					imagefill($d1_img, 0, 0, $cl);				
				}else{
					imagealphablending($d1_img, false);
				}
				imagecopyresampled($d1_img, $s_im, $im_left, $im_top, 0, 0, $real_nw, $real_nh, $src_w, $src_h);
				
				if($filter){
					if($filter == IMG_FILTER_GAUSSIAN_BLUR){
						for($i=0; $i<$filter_param; $i++)
							imagefilter($d1_img, $filter);
					}else
						imagefilter($d1_img, $filter, $filter_param ? $filter_param : null);
				}
				
				if(!$save_extension){
					imagejpeg($d1_img,$dst,95);
					imagedestroy($d1_img);			
					imagedestroy($s_im);
				}else{	
					if($pi['extension']){
						switch($pi['extension']){
							case 'gif':
								imagegif($d1_img, $dst);
								break;
							case 'jpg':
							case 'jpeg':
								imagejpeg($d1_img, $dst, 90);
								break;
							case 'png':
								imagesavealpha($d1_img, true);
								imagepng($d1_img,$dst);
								break;
						}
					}
				}
				
				return true;
				
			}
		}	
		
		return false;
	}
	
	function addWatermark($src,$stamp_path){
	
		if(file_exists($src)){	
			
			preg_match("/^.+?\.([A-z]+)$/",$src,$pock);
			if(isset($pock[1]) && in_array($pock[1],array("jpg","jpeg","png","gif"))){			
				
				$src_size = getimagesize($src);
				$stamp_size = getimagesize($stamp_path);
				$snw = $stamp_size[0]; // stamp new width
				$snh = $stamp_size[1]; // stamp new height
				
				preg_match("/^.+?\.([A-z]+)$/",$stamp_path,$st_ext);
				switch($st_ext[1]){
					case "jpeg":
					case "jpg":
						$stamp_im = imagecreatefromjpeg($stamp_path);
					break;
					case "png":
						$stamp_im = imagecreatefrompng($stamp_path);
						imagesavealpha($stamp_im, true);
					break;
					case "gif":
						$stamp_im = imagecreatefromgif($stamp_path);
					break;	
				}
				
				switch($pock[1]){
					case "jpeg":
					case "jpg":
						$source_im = imagecreatefromjpeg($src);
					break;
					case "png":
						$source_im = imagecreatefrompng($src);
					break;
					case "gif":
						$source_im = imagecreatefromgif($src);
					break;				
				}				
				
				/* set stamp */
				imagecopy($source_im,$stamp_im,$src_size[0]-$snw-10,$src_size[1]-$snh-10,0,0,$snw,$snh);			
				switch($pock[1]){
					case "jpeg":
					case "jpg":
						imagejpeg($source_im,$src,100);
					break;
					case "png":
						imagepng($source_im,$src);
					break;
					case "gif":
						imagegif($source_im,$src);
					break;
				}
				
			}
			
		}
	
	}

}

?>