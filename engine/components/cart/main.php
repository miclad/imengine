<?

global $cart_o;

//file_put_contents(B_DIR.'cart_request.txt', print_r($_REQUEST, true));

// generate delivery type string

$delivery_type;
$post_data = array();

$PAGE->setAttribute('sidebar', Page::includeComponent('cart:navbar'));
$PAGE->setAttribute('hide_title');

if(true || $USER->isAuthorized()){	
	
	$PAGE->setAttribute('is_personal', true);
	$PAGE->setAttribute('cur_personal_page', 'cart');
	
	if(!count($_POST) && isset($_GET['edit']) && isset($_SESSION['cart_post_data']) && is_array($_SESSION['cart_post_data']))
		$post_data = $_SESSION['cart_post_data'];
	else
		$post_data = $_POST;
	
	/* init order form */
	$f_desc = array(		
		array('name' => 'delivery_ok', 'title' => 'Доставка', 'datatype' => 'text', 'req' => 1, 'errors' => array('error1' => isset($post_data['delivery_error']) && $post_data['delivery_error'] ? $post_data['delivery_error'] : 'Необходимо выбрать способ доставки')),
		array('name' => 'payment', 'title' => 'Способ оплаты', 'datatype' => 'number', 'req' => 1, 'errors' => array('error1' => 'Необходимо выбрать способ оплаты')),
		array('name' => 'surname', 'title' => 'Фамилия', 'datatype' => 'text', 'req' => 1, 'maxlength' => 255),
		array('name' => 'name', 'title' => 'Имя', 'datatype' => 'text', 'req' => 1, 'maxlength' => 255),
		array('name' => 'p_name', 'title' => 'Отчество', 'datatype' => 'text', 'req' => 1, 'maxlength' => 255),
		array('name' => 'passport', 'title' => 'Паспорт', 'datatype' => 'text', 'req' => 1, 'maxlength' => 255),
		array('name' => 'phone', 'title' => 'Телефон', 'datatype' => 'phone', 'req' => 1, 'maxlength' => 255),
		array('name' => 'email', 'title' => 'Электронная почта', 'datatype' => 'mail', 'req' => 1, 'maxlength' => 255),	
		array('name' => 'address_post_index', 'title' => 'Почтовый индекс', 'datatype' => 'text', 'req' => 1, 'maxlength' => 255),
		array('name' => 'address_region', 'title' => 'Область', 'datatype' => 'text', 'req' => 1, 'maxlength' => 255),
		array('name' => 'address_city', 'title' => 'Город', 'datatype' => 'text', 'req' => 1, 'maxlength' => 255),
		array('name' => 'address_street', 'title' => 'Улица', 'datatype' => 'text', 'req' => 1, 'maxlength' => 255),
		array('name' => 'address_house', 'title' => 'Номер дома', 'datatype' => 'text', 'req' => 1, 'maxlength' => 255),
		array('name' => 'address_corp', 'title' => 'Корпус', 'datatype' => 'text', 'req' => 0, 'maxlength' => 255),
		array('name' => 'address_flat', 'title' => 'Квартира/офис', 'datatype' => 'text', 'req' => 1, 'maxlength' => 255),
		array('name' => 'comments', 'title' => 'Комментарий', 'datatype' => 'text', 'req' => 0, 'maxlength' => 255)		
	);
	
	// Поля формы, зависящие от способа доставки
	
	$delivery_type = Delivery::buildString($post_data);
	$dt_expl = explode('|', $delivery_type);
	
	$delivery_type_data = false;
	if(count($dt_expl) > 1){
		$delivery_type_data = $db->query('select * from delivery_types where id='.intval($dt_expl[0]))->getNext();
		if($delivery_type_data){
			
			$no_req_fields = array();
			$d_order_fields = array('p_name', 'passport', 'address', 'address_city', 'address_region', 'address_post_index');
			
			foreach($d_order_fields as $f){
				
				if($f == 'address'){
					
					if(!$delivery_type_data['need_'.$f]){
						$no_req_fields[] = 'address_street';
						$no_req_fields[] = 'address_house';
						$no_req_fields[] = 'address_flat';
					}
					
				}else{
					
					if(!$delivery_type_data['need_'.$f]){
						$no_req_fields[] = $f;
					}
					
				}
				
			}
			
			foreach($f_desc as $k => $f){
				if(in_array($f['name'], $no_req_fields)){
					$f_desc[$k]['req'] = 0;
				}
			}
			
		}
	}

	/*
	if(!$USER->isAuthorized() && (!isset($post_data['order_type']) || $post_data['order_type'] == 'new_user')){
		$add_fields = array(
			array('name' => 'login', 'title' => 'E-mail', 'datatype' => 'mail', 'req' => 1, 'unique' => 'users'),
			array('name' => 'password', 'title' => 'Пароль', 'datatype' => 'password', 'req' => 1)
		);
		$f_desc = array_merge($add_fields, $f_desc);		
	}
	*/
	
	$status_res = $db->query('select id from order_statuses where 1 order by pos limit 1')->getNext();
	$def_status = $status_res ? $status_res['id'] : 0;
	
	$form = new UForm($f_desc, 'orders', '', array('status' => $def_status, 'user_id' => $USER->getID(), 'wishlist_id' => $GLOBALS['cart_o']->getDefaultList()), '', false);
	if($USER->isAuthorized()){
		$form->setValues(
			array(
				'name' => $USER->getField('name'),
				'surname' => $USER->getField('surname'),
				'email' => $USER->getField('email'),
				'phone' => $USER->getField('phone'),
				'address' => $USER->getField('address'),
				'discount_card' => intval($USER->getField('discount_card'))
			)
		);	
	}
	
	if(isset($_GET['edit']) && !count($_POST) && isset($_SESSION['cart_post_data']) && is_array($_SESSION['cart_post_data'])){
		foreach($_SESSION['cart_post_data'] as $k => $v){
			if(!is_array($v)){
				$form->setValue($k, $v);
			}
		}
	}
	
	if(isset($_GET['action'])){
		
		switch($_GET['action']){
			case 'repeat_order':
				
				if(isset($_GET['id'])){
					
					$order_data = $db->query('select * from orders where id='.intval($_GET['id']))->getNext();
					if(!$order_data || !$order_data['user_id'] || $order_data['user_id'] != $USER->getId()){
						throw new Exception('Заказ не найден');
					}
					
					$order_items = Orders::getItems($order_data['id']);
					
					if(!count($order_items)){
						throw new Exception('Заказ пуст');
					}
					
					if(!$cart_o->getUserId()){
						$cart_o->createUser();
					}
					
					$wl_id = $cart_o->getDefaultList();					
					if(!$wl_id){
						$wl_id = $cart_o->createWishlist('Корзина заказов', true, 1);
					}
					
					$cart_o->clearWishlist($wl_id);
					
					foreach($order_items as $v){
						$cart_o->addItem($v['id'], $v['v_id'], $v['items_num'], $wl_id);
					}
					
					Header('Location: /cart/order/?repeat='.$order_data['id'], true, 307);
					exit();
					
				}
				
			break;
			case 'add_item':
				
				if(isset($_GET['id'])){
					
					if(!$cart_o->getUserId()){
						$cart_o->createUser();
					}
					
					$wl_id = $cart_o->getDefaultList();					
					if(!$wl_id){ // create new
						$wl_id = $cart_o->createWishlist('Корзина заказов', true, 1);
					}
					
					if($wl_id){
						
						$ids_to_add = array();
						
						if(is_numeric($_GET['id']))
							$ids_to_add[] = intval($_GET['id']);
						else
							$ids_to_add = explode(',', $_GET['id']);
						
						if(isset($_GET['variants'])){
							
							foreach($ids_to_add as $product_id){
								
								foreach($_GET['variants'] as $variant){
									
									$quantity = isset($variant['quantity']) ? intval($variant['quantity']) : 0;
									if($quantity < 1)
										continue;
									
									$variant_id = Catalog::findProductVariant($product_id, $variant['params']);
									if($variant_id){
										
										$res = $cart_o->addItem($product_id, $variant_id, $quantity, $wl_id);
										
									}
									
								}
								
							}
							
						}else{
							
							foreach($ids_to_add as $v){

								$item_id = 0;

								if(is_numeric($v)){
									$item_id = $v;
									$item_variant_id = isset($_GET['item_variant']) ? intval($_GET['item_variant']) : 0;
									$items_num = isset($_GET['num']) && is_numeric($_GET['num']) ? intval($_GET['num']) : 1;
								}else{
									$iv_id = explode('-', $v);
									if(count($iv_id) == 2){ // ITEM_ID|ITEM_VARIANT-ITEMS_NUM,...
										$iiid = explode('|', $iv_id[0]);
										if(count($iiid) == 2){
											$item_id = $iiid[0];
											$item_variant_id = $iiid[1];
											$items_num = $iv_id[1];
										}
									}
								}

								if($item_id){
									if(!$item_variant_id){
										// get default variant
									}
									$res = $cart_o->addItem($item_id, $item_variant_id, $items_num, $wl_id);
								}

							}
							
						}
						
						if(!Site::isAjaxRequest()){
							Header('Location: '.Site::getReferer('/cart/'));
							exit();
						}
						
					}
					
				}				
				
				
			break;
			case 'change_item_variant':
				
				if($cart_o->getDefaultList() && isset($_GET['item_id']) && is_numeric($_GET['item_id']) && isset($_GET['item_variant_id']) && is_numeric($_GET['item_variant_id'])){
					$db->query('update wishlist_items set item_variant='.intval($_GET['item_variant_id']).' where wishlist_id='.$cart_o->getDefaultList().' and id='.intval($_GET['item_id']));
				}
				
			break;
			case 'move_item':
				
				if(isset($_GET['from']) && isset($_GET['to']) && isset($_GET['id'])){
					$cart_o->moveItem($_GET['id'], $_GET['from'], $_GET['to'], isset($_GET['item_variant']) ? $_GET['item_variant'] : 0);
					if(Site::isAjaxRequest()){
						header("Content-Type: text/xml; charset=utf-8");							
						$wl_data = $cart_o->getWishlistStat($_GET['from'], true);
						require 'templates/xml_response.php';
					}
				}
				
			break;
			case 'delete_item':
				
				if(isset($_GET['item'])){
					$cart_o->deleteItem($_GET['item'], $cart_o->getDefaultList());
				}
				
			break;
			case 'change_items_num':
				
				if(isset($_GET['num']) && isset($_GET['item'])){
					$cart_o->changeItemsNum($cart_o->getDefaultList(), $_GET['item'], $_GET['num'], isset($_GET['item_variant']) ? $_GET['item_variant'] : 0);
				}elseif(isset($_POST['change_num']) && is_array($_POST['change_num'])){
					foreach($_POST['change_num'] as $id => $num){
						$cart_o->changeItemsNum($cart_o->getDefaultList(), $id, $num);
					}
					if(!Site::isAjaxRequest()){
						Header('Location: /cart/');
					}
				}
				
			break;
			case 'create_list':
				
				if(isset($_POST['name']) && trim($_POST['name']) != ''){
					$cart_o->createWishlist($_POST['name'], true);
				}
				
			break;
			case 'delete_list':
				
				if(isset($_GET['id'])){
					$res = $cart_o->deleteWishlist($_GET['id']);
					if($res){
						SiteNotices::addNotice('Список покупок удален');
						Header('Location: /cart/');
						exit();
					}
				}
				
			break;
			case 'clear_list':
				
				if(isset($_GET['id'])){
					$res = $cart_o->clearWishlist($_GET['id']);
					if($res){
						SiteNotices::addNotice('Список очищен');
						Header('Location: /cart/');
						exit();
					}
				}
				
			break;
			case 'rename_list':
				
				if(isset($_POST['id']) && isset($_POST['name'])){
					$res = $cart_o->renameWishlist($_POST['id'], $_POST['name']);
					if($res){
						SiteNotices::addNotice('Список переименован');
					}
					Header('Location: /cart/');
					exit();
				}
				
			break;
			case 'set_list_note':
				
				if(isset($_POST['id']) && isset($_POST['note'])){
					if(isset($_POST['delete'])){
						$_POST['note'] = '';
					}
					$res = $cart_o->setWishlistNote($_POST['id'], $_POST['note']);
					if($res){
						SiteNotices::addNotice(isset($_POST['delete']) ? 'Заметка удалена' : 'Заметка сохранена');
					}
					Header('Location: /cart/');
					exit();
				}
				
			break;
			case 'set_default_list':
				
				if(isset($_GET['id'])){
					$cart_o->setDefaultList($_GET['id']);
					if(!Site::isAjaxRequest()){
						Header('Location: /cart/');
						exit();
					}
				}
				
			break;
			case 'do_order':
				
				$_SESSION['cart_post_data'] = $_POST;
				
				if(isset($_POST['change_num']) && is_array($_POST['change_num'])){
					foreach($_POST['change_num'] as $id => $num){
						$cart_o->changeItemsNum($cart_o->getDefaultList(), $id, $num);
					}
				}
				
				if(isset($_POST['id'])){
					
					/*
					$_POST['login'] = isset($_POST['new_login']) ? $_POST['new_login'] : '';
					$_POST['password'] = isset($_POST['new_password']) ? $_POST['new_password'] : '';
					if(!isset($_POST['order_type']) && !$USER->isAuthorized() || isset($_POST['order_type']) && $_POST['order_type'] == 'new_user')
						$_POST['email'] = $_POST['login'];
					*/
					
					$wl_data = $cart_o->getListById($_POST['id']);
					if($wl_data){
						
						$wl_items = $cart_o->getWishlistItems($wl_data['id']);
						if(isset($_POST['discount_card']))
							$_POST['discount_card'] = intval($_POST['discount_card']);
							
						$form->read();
						if(!$form->haveErrors()){						
							
							if(isset($_POST['confirm']) && $_POST['confirm'] == 1){			
								
								if(!$USER->isAuthorized()){
									
									$login = $form->getValue('email');
									$password = Site::generatePassword();
									
									$check_user = $db->query('select * from users where login="'.mysql_escape_string($login).'"')->getNext();
									if(!$check_user){
								
										$db->query('
											insert into users 
											set 
											is_admin=0,
											group_id=0,
											name="'.mysql_escape_string($form->getValue('name')).'",
											surname="'.mysql_escape_string($form->getValue('surname')).'",
											p_name="'.mysql_escape_string($form->getValue('p_name')).'",
											phone="'.mysql_escape_string($form->getValue('phone')).'",
											login="'.mysql_escape_string($login).'",
											password="'.md5($password).'",
											email="'.mysql_escape_string($form->getValue('email')).'"
										');

										Auth::login($login, '', true, true);
										AuthUser::getInstance()->reAuth();
										$USER = AuthUser::getInstance();
										$form->setValue('user_id', $USER->getId());	
									
									}else{
										$form->setValue('user_id', $check_user['id']);
									}
									
								}
								
								if(isset($_POST['subscribe'])){
									$r = $db->query('select * from mailing_list where email="'.mysql_escape_string($form->getValue('email')).'"');
									if(!$r->selectedRowsCount())
										$db->query('insert into mailing_list set name="'.mysql_escape_string($form->getValue('name')).'",email="'.mysql_escape_string($form->getValue('email')).'",event="news"');
								}
								
								/* save in database */
								$oid = $form->writeInDB(array('password', 'login', 'delivery_ok'));
								$t_sum = 0;
								$items_num_t = 0;
								if($oid){									
									$_SESSION['order_sent'] = $oid;									
									foreach($wl_items as $v){										
										$db->query('insert into order_items 
										            set order_id='.$oid.', item_id='.$v['id'].',item_variant='.$v['item_variant'].',
										            items_num='.$v['items_num'].',
										            price='.intval($v['price']).',
										            discount='.intval($v['discount']));
										
										/* increase rating */
										$db->query('update catalog set rating=rating+1 where id='.$v['id']);
										$t_sum += round($v['price'])*$v['items_num'];
										$items_num_t += $v['items_num'];
									}
									/* save as regular */
									if(isset($_POST['is_regular']) && isset($_POST['regular_settings']['rate'])){
										Orders::saveAsRegular($oid);
									}
								}
								
								/* update user discount card number */
															
								
								$delivery_price = 0;
								if(isset($_POST['delivery']['price']) && is_numeric($_POST['delivery']['price'])){
									$delivery_price = $_POST['delivery']['price'];
								}								
								
								$discount = Catalog::getDiscount($t_sum, $form->getValue('discount_card'), $wl_items);								
								$db->query('update orders set items_num='.intval($items_num_t).',sum='.intval($t_sum).',sum_discount='.intval($discount['total_sum']).',discount_value='.$discount['perc'].',discount_by_card='.intval($discount['by_card']).',delivery_type="'.mysql_escape_string($delivery_type).'",delivery_price='.intval($delivery_price).' where id='.$oid);
								
								$db->query('delete from wishlist_items where wishlist_id='.$wl_data['id']);
								
								/* update users orders_sum */
								if(AuthUser::getInstance()->isAuthorized()){
									$user_upd['orders_sum'] = 'orders_sum+'.$discount['total_sum'];									
									if($form->getValue('discount_card') && $form->getValue('discount_card') != $USER->getField('discount_card')){
										$user_upd['discount_card'] = intval($form->getValue('discount_card'));										
									}
									$q = '';
									$upd = array();
									foreach($user_upd as $k => $v){
										$upd[] = $k.'='.$v;
									}
									$db->query('update users set '.implode(',', $upd).' where id='.AuthUser::getInstance()->getId());
								}
								
								/* send email to customer */
								ob_start();
								require 'templates/email_user.php';
								$email_content = ob_get_contents();
								ob_end_clean();
								Email::sendMail($_POST['email'], DBCommon::getConfig('order_email_subj'), $email_content, '<order@'.str_replace('www.','',$_SERVER['SERVER_NAME']).'>');
								
								/* send email */
								ob_start();
								require 'templates/email.php';
								$email_content = ob_get_contents();
								ob_end_clean();
								Email::sendMail($this->getConfig('email'), 'Новый заказ', $email_content, '<order@'.str_replace('www.','',$_SERVER['SERVER_NAME']).'>');
								
								if(isset($_SESSION['cart_post_data']))
									unset($_SESSION['cart_post_data']);
	
								Header('Location: /cart/success/');
								exit();
								
							}else{
								
								$PAGE->setAttribute('is_personal', false);								
								$PAGE->setAttribute('cur_personal_page', '');	
								
								$PAGE->addItemToPath('Подтверждение заказа', '');
								
								$payment = false;
								if($form->getValue('payment')){
									$payment = $db->query('select * from payment_types where id='.intval($form->getValue('payment')))->getNext();
								}
								
								$delivery_data = isset($_POST['delivery_type_str']) && $_POST['delivery_type_str'] != '' ? Delivery::parseDeliveryType($_POST['delivery_type_str'], 'arr') : false;
								$delivery_price = isset($_POST['delivery']['price']) ? intval($_POST['delivery']['price']) : 0;								
								
								$content_template = 'templates/confirm.php';
							}
								
						}else{
							SiteNotices::addNotice($form->getErrors());
						}
					}
				}
				
			break;
		}
	}
	
	if(!Site::isAjaxRequest()){
		
		/* get left content */
		$user_wishlists = $cart_o->getUserLists();
			
		/* get default wishlist data and items list */
		$current_wishlist = $cart_o->getListById($cart_o->getDefaultList());
		if($current_wishlist){			
			$items = $cart_o->getWishlistItems($current_wishlist['id']);		
			require 'templates/items.php';
		}else{
			throw new Exception('Корзина пуста');
		}

	}
		
}elseif(!Site::isAjaxRequest()){
	throw new NeedAuthException();
}

if(Site::isAjaxRequest()){	

	$stat = $cart_o->getWishlistStat($cart_o->getDefaultList(), true);
	
	$resp = array(
		'sum' => $stat['sum'],
		'sum_no_discount' => $stat['sum_no_discount'],
		'items_num' => $stat['items_num'],
		'weight' => $cart_o->getTotalWeight($cart_o->getDefaultList()),
		'short_text' => $stat['text']
	);
	
	echo json_encode($resp);
	
	exit();
	
}
	
?>