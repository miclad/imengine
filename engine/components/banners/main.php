<?

$b_tables = 'banners as B';
$b_fields = 'B.*';
$b_where = 'B.visible=1';
$b_group_by = 'B.id';
$b_order_by = $this->getConfig('order_by') ? $this->getConfig('order_by') : 'B.pos';

$tag = $this->getConfig('tag') != '' ? $this->getConfig('tag') : 'div';

if($this->getConfig('placing') != ''){
	$b_where .= ' and B.placing="'.mysql_escape_string($this->getConfig('placing')).'"';
}

/* 
	Use cases 
*/


if(!in_array($this->getConfig('placing'), array('_slider'))){
	
	if(false && $PAGE->isIndex()){
		$b_where .= ' and B.show_case like "% main %"';
	}elseif(false && $PAGE->getUrl() == '/catalog'){
		if(!$PAGE->getAttribute('current_section')){
			/* catalog main page */
			$b_where .= ' and B.show_case like "% catalog_main %"';
		}else{
			/* catalog sections */
			$b_tables .= ' left join banner_sections as S on S.banner_id=B.id';
			$b_where .= ' and B.show_case like "% catalog %" and (B.show_case not like "% only_sections %" or S.section_id='.$PAGE->getAttribute('current_section').' and B.id=S.banner_id)';
		}
	}elseif(false && $PAGE->getUrl() == '/brands'){
		if(!$PAGE->getAttribute('current_brand')){
			/* catalog main page */
			$b_where .= ' and B.show_case like "% brands_main %"';
		}else{
			/* catalog sections */
			$b_tables .= ' left join banner_brands as S on S.banner_id=B.id';
			$b_where .= ' and B.show_case like "% brands %" and (B.show_case not like "% only_brands %" or S.brand_id='.$PAGE->getAttribute('current_brand').' and B.id=S.banner_id)';
		}
	}else{
		//$b_tables .= ' left join banner_pages as P on P.banner_id=B.id';
		$b_where .= ' and (B.show_case not like "% selected_pages %" or (select count(*) from banner_pages as P where P.page_id='.$PAGE->getId().' and B.id=P.banner_id) > 0)';
	} 
}

$banners = $db->query('select '.$b_fields.' from '.$b_tables.' where '.$b_where.' group by '.$b_group_by.' order by '.$b_order_by.($this->getConfig('limit') ? ' limit '.intval($this->getConfig('limit')) : ''))->fetchArray();

$template = '';

$tpl_file = $this->getConfig('placing') ? COMPONENTS_DIR.'banners/templates/'.$this->getConfig('placing').'.php' : '';
if($tpl_file && file_exists($tpl_file))
	$template = $this->getConfig('placing');
	
if($this->getConfig('template'))
	$template = $this->getConfig('template');

if($template)
	$this->setActionTemplate($template);

?>