<?

$PAGE->clearContent();

SiteCurrency::getInstance()->setCurrency(1);

$product = false;

if($this->getConfig('id')){	
	$product = Catalog::getById($this->getConfig('id'), $this->getConfig('collective_buy') ? array('price_type' => intval(DBCommon::getConfig('collective_price_type'))) : null);	
}

if(!$product || !$product['active']){
	throw new PageNotFoundException();
}

$tree = new Tree('folders', 'entity="catalog"');
$path = $tree->getPath('*', $product['folder_id']);
//$PAGE->clearPath();
$section_default_pict = '';
foreach($path as $k => $v){
	$PAGE->addItemToPath($v['name'], '/catalog'.$v['url'].'/');
	if($v['orig_ext'] != '')
		$section_default_pict = '/img/folders/m'.$v['id'].'.jpg';
}
$PAGE->addItemToPath($product['brand_name'].' '.$product['model'], '/catalog/'.$product['url'].'/');
$PAGE->setTitle(count($path) > 2 ? $path[1]['name'] : $path[count($path)-1]['name']);

$PAGE->setAttribute('current_section_root', $path[0]['id']);
$PAGE->setAttribute('current_section_data_root', $path[0]);
$PAGE->setAttribute('current_section', $product['folder_id']);
$PAGE->setAttribute('current_product', $product['id']);

$product['properties'] = Catalog::getElementProperties($product['id'], true, true, true);
$product['photos'] = $db->query('select * from catalog_photos where item_id='.$product['id'].' order by pos')->fetchArray();

if(count($product['photos']))
	$PAGE->setMeta('image', 'http://'.$_SERVER['SERVER_NAME'].'/img/catalog/b'.$product['photos'][0]['id'].'.jpg');

$related_products = Page::includeComponent('catalog', array('related_to' => $product['id'], 'items_per_row' => 4, 'limit' => 4), array(), 'items', '', true);

$brand_data = $db->query('select * from brands where id='.intval($product['brand']))->getNext();

$certificates = Catalog::getCertificates($product['id']);

if($this->getInitParam('show')){
	
	$this->setActionTemplate('dummy');
	
	switch($this->getInitParam('show')){
		
		case 'comments':
			
			$PAGE->setTitle('Отзывы о '.$product['model']);
			
			echo Page::includeComponent('catalog', array('product_id' => $product['id']), array(), 'comments');
			
		break;
		
		case 'comment_added':
			
			$comment = isset($_SESSION['catalog_comment_added']) ? $db->query('select * from catalog_comments where id='.intval($_SESSION['catalog_comment_added']))->getNext() : false;
			if(!$comment)
				throw new PageNotFoundException();
				
			$this->setActionTemplate('comments_add_success');
			$PAGE->setTitle('Ваш отзыв о '.$product['model']);
			
		break;
		
		case 'new_comment':

			$dt_ob = new DataTable('catalog_comments');
			$this->setActionTemplate('comments_add');
			$PAGE->setTitle('Ваш отзыв о '.$product['model']);
			
			if(isset($_POST) && count($_POST)){
				
				$dt_ob->setRowValue('product_id', $product['id']);
				$dt_ob->setRowValue('user_id', $USER->getField('id'));
				$dt_ob->setRowValue('active', 0);
				
				$errors = array();
				if(!intval($dt_ob->getRowValue('mark'))){
					// $errors[] = 'Необходимо оценить товар!';
				}
				
				if(!count($errors)){
					$ins_id = $dt_ob->insertRow();
					if($ins_id){
						
						/* inform admin */
						$mail_cnt = "Добавлен новый отзыв к товару ".$product['brand_name'].' '.$product['model']."\n";
						$mail_cnt .= "Для модерации перейдите по ссылке http://".$_SERVER['SERVER_NAME']."/engine/admin/?module=datatable&entity=catalog_comments&show=element_form&id=".$ins_id;
						$mail_subject = "Новый отзыв к товару";
						Email::sendMail($this->getConfig('comments_email') ? $this->getConfig('comments_email') : DBCommon::getConfig('email'), $mail_subject, $mail_cnt, 'comments@'.str_replace('www.', '', $_SERVER['SERVER_NAME']));
						
						$_SESSION['catalog_comment_added'] = $ins_id;
						
						//SiteNotices::addNotice('Ваш отзыв будет опубликован через некоторое время после проверки модератором.');
						header('Location: /catalog/'.$product['url'].'/?show=comment_added');
						exit();
						
					}
					//echo $dt_ob->renderErrors();
				}else{
					SiteNotices::addNotice('<div>'.implode('</div><div>', $errors).'</div>');
				}
				
			}
			
			$dt_ob->setRowValue('author_name', $USER->getField('name'));
			$dt_ob->setRowValue('author_email', $USER->getField('email'));
			$dt_ob->setRowValue('author_city', $USER->getField('address_city'));
			
		break;
		
	}
	
}
	
/* SEO */
if($product['html_title'])
	$PAGE->setHeadTitle(trim($product['html_title']));
else
	$PAGE->setHeadTitle($product['model']);
if($product['html_keywords'])
	$PAGE->setMetaKeywords($product['html_keywords']);
if($product['html_description'])
	$PAGE->setMetaDescription($product['html_description']);

?>