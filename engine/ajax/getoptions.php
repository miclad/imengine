<?

require '../conf/init.php';

header("Content-type: text/xml; charset=utf-8");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
ob_start();
echo '<?xml version="1.0" encoding="utf-8"?>';
echo "<selectChoice>\n";
if(isset($_POST["id"])){
	
	if($_POST['master'] == 'navi_type'){
		$ignore_id = isset($_POST['ignore_id']) ? intval($_POST['ignore_id']) : 0;
		$tree = new Tree('structure', 'navi_type="'.$_POST['id'].'"');
		$arr = $tree->getFullTree(0, 0, -1, 'pos', 'M.name,M.id', true, $ignore_id);
		for($i=0; $i<count($arr); $i++){
			$arr[$i]['prefix'] = '---';
			for($t=0; $t<$arr[$i]['level']; $t++)
				$arr[$i]['prefix'] .= "---";
		}
		echo '<entry><optionText>Корень</optionText><optionValue>0</optionValue></entry>';
		foreach($arr as $v){
			echo "<entry>\n";
			echo "<optionText>".htmlspecialchars($v['prefix']." ".$v['name'])."</optionText>\n";
			echo "<optionValue>".$v['id']."</optionValue>\n";
			echo "</entry>\n";
		}
	}elseif($_POST['master'] == 'item_id'){
		/* get variants */
		$res = $db->query('select V.id,V.name from catalog_item_variants as V where V.item_id='.intval($_POST['id']).' order by V.name');
		echo "<entry><optionText>--</optionText><optionValue></optionValue></entry>";
		while($v = $res->getNext()){
			echo "<entry>\n";
			echo "<optionText>".htmlspecialchars($v['name'])."</optionText>\n";
			echo "<optionValue>".$v['id']."</optionValue>\n";
			echo "</entry>\n";
		}
	}elseif($_POST['master'] == 'catalog_folder'){
		$arr = DBCommon::getFromBase('C.*,B.name as brand_name,C.model as name', 'catalog as C left join brands as B on C.brand=B.id', 'C.folder_id='.$_POST['id'].' and C.id != '.intval($_POST['ignore_id']), 'model,price');
		echo "<entry><optionText>--</optionText><optionValue></optionValue></entry>";
		foreach($arr as $v){
			echo "<entry>\n";
			echo "<optionText>".htmlspecialchars($v['brand_name'].' '.$v['name'].' '.$v['package'])."</optionText>\n";
			echo "<optionValue>".$v['id']."</optionValue>\n";
			echo "</entry>\n";
		}
	}elseif($_POST['master'] == 'article_folder'){
		$arr = DBCommon::getFromBase('A.*', 'articles as A', 'A.folder_id='.$_POST['id'].' and A.id != '.intval($_POST['ignore_id']), 'date desc');
		echo "<entry><optionText>--</optionText><optionValue></optionValue></entry>";
		foreach($arr as $v){
			echo "<entry>\n";
			echo "<optionText>".htmlspecialchars($v['title'])."</optionText>\n";
			echo "<optionValue>".$v['id']."</optionValue>\n";
			echo "</entry>\n";
		}
	}
		
	
}

echo "</selectChoice>";
$string = ob_get_contents();
ob_end_clean();
echo $string;
?>