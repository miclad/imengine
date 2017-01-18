<?

set_time_limit(0);

require '../conf/init.php';

$dirname = 'img/articles/';
$dir = opendir(B_DIR.$dirname);
$formats = $entities['articles'][1]['image'][1][1];

$counter = 0;

while(($f = readdir($dir)) !== false){
	
	if($f == '.' || $f == '..') continue;
	
	$preg = preg_match('/^([0-9]+)\.[A-z]+$/', $f, $pock);
	if($preg){
		
		foreach($formats as $v){
			if($v[1] != 'sm')
				continue;
			$sizes = explode(',', $v[0]);
			if(count($sizes) == 2){
				$res = Images::imageResizeUnified(B_DIR.$dirname.$f, $sizes[0], $sizes[1], B_DIR.$dirname.$v[1].$pock[1].'.jpg', 0, $v[3]);
				if($res){
					echo 'Resized: '.$f.' ('.B_DIR.$dirname.$f.', '.$sizes[0].'x'.$sizes[1].', '.$pock[1].', '.B_DIR.$dirname.$v[1].$pock[1].'.jpg'.')<br>';
					$counter++;
				}
			}
		}
		
		
	}
	
}

echo '<br><br>';
echo 'Total: '.$counter;

closedir($dir);

?>