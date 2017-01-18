<?

class Search{
	

	function search($string){
		//vars
		$stype=2;
		$arr=array();
		$case="";
		$str_arr=array();
		$offset=80;
		
		//Getting type of search
		if(isset($_GET["stype"])&&$_GET["stype"]>-1)
			$stype=$_GET["stype"];
		
		//preparing string
		$cmp_str=$string;
		if(get_magic_quotes_gpc())
			$cmp_str=stripslashes($cmp_str);
		$cmp_str=preg_replace("/[()\[\].,;*+\"'\/\\\{}]/"," ",$cmp_str);
		$cmp_str=htmlspecialchars($cmp_str);	
		$cmp_str=preg_replace("/\s{2,}/"," ",trim($cmp_str));
		$cmp_str=mysql_escape_string($cmp_str);	
		
		//main block
		if($cmp_str!=""){
			$what = "structure.name,structure.id as page_id,structure.content";
			$from = "structure";
			$where = "(";
			if($stype==0){
				$str_arr[]=htmlspecialchars(trim($cmp_str));
			}
			else{
				$str_arr=explode(" ",$cmp_str);
				if($stype==1)
					$case="and";
				else
					$case="or";
			}
			$already=false;
			foreach($str_arr as $v){
				if($already)	
					$where.=" ".$case." ";
				$already=true;
				$where.="structure.content regexp '>[^<]*".$v."|^[^<]*".$v."'";
			}
			$where.=")";      		
			$order = "id";
			$res=mysql_query("select ".$what." from ".$from." where ".$where." order by ".$order);
			while($tmp=mysql_fetch_assoc($res)){
				$tmp["content"]=strip_tags($tmp["content"]);
				//getting announce
				preg_match("/(?i)((\b[^.,:;][^.]{0,$offset}|^)(?:".implode("|",$str_arr).")[^.]{0,$offset}\b\.?)/",$tmp["content"],$tc);
				$tmp["content"]=$tc[1];
				//mark found words
				$tmp["content"]=preg_replace("/(?i)(".implode("|",$str_arr).")/","<b>\\1</b>",$tmp["content"]);
				$arr[]=$tmp;
			}
		}
		return $arr;
	}
	
	function search_in_coll($string, $entity, $tit_field, $cont_field){
		//vars
		$stype = 1;
		$arr = array();
		$case = "";
		$str_arr = array();
		$offset = 80;
		
		//Getting type of search
		if(isset($_GET["stype"])&&$_GET["stype"]>-1)
			$stype = $_GET["stype"];
		
		//preparing string
		$cmp_str=$string;
		if(get_magic_quotes_gpc())
			$cmp_str=stripslashes($cmp_str);
		$cmp_str = preg_replace("/[()\[\].,;*+\"'\/\\\{}]/"," ",$cmp_str);
		$cmp_str = htmlspecialchars($cmp_str);	
		$cmp_str = preg_replace("/\s{2,}/"," ",trim($cmp_str));
		$cmp_str = mysql_escape_string($cmp_str);	
		
		//main block
		if($cmp_str!=""){
			$what = '*';
			$from = $entity;
			$where = '1 and ';//'lang="'.LANG.'" and ';
			if($stype==0){
				$str_arr[] = htmlspecialchars(trim($cmp_str));
			}
			else{
				$str_arr=explode(" ",$cmp_str);
				if($stype==1)
					$case="and";
				else
					$case="or";
			}
			$already=false;
			foreach($str_arr as $v){
				if($already)	
					$where .= " ".$case." ";
				$already = true;
				$where .= $cont_field." regexp '>[^<]*".$v."|^[^<]*".$v."'";
			} 
			if($entity == 'news')   		
				$order = 'date desc';
			else
				$order = 'id';
			$res=mysql_query("select ".$what." from ".$from." where ".$where." order by ".$order);
			//echo "select ".$what." from ".$from." where ".$where." order by ".$order;

			if($res){
			while($tmp=mysql_fetch_assoc($res)){
				$tmp['title'] = $tmp[$tit_field];
				$tmp["content"]=strip_tags($tmp["content"]);
				$tmp['link'] = '';
				if($entity == 'structure'){
					$tmp['link'] = $tmp['url'];
				}elseif(isset($tmp['folder_id']) && $tmp['folder_id']){
					$tr = mysql_query('select page_url from folders where id='.$tmp['folder_id']);
					if($tpu = mysql_fetch_assoc($tr))
						$tmp['link'] = $tpu['page_url'].'/'.$tmp['id'].'/';
				}
				//getting announce
				preg_match("/(?i)((\b[^.,:;][^.]{0,$offset}|^)(?:".implode("|",$str_arr).")[^.]{0,$offset}\b\.?)/",$tmp["content"],$tc);
				if(count($tc) > 1)
					$tmp["content"]=$tc[1];
				//mark found words
				$tmp["content"]=preg_replace("/(?i)(".implode("|",$str_arr).")/","<b>\\1</b>",$tmp["content"]);
				$arr[]=$tmp;
			}
			}
		}
		return $arr;
	}
	
}
?>