<?
if(isset($_GET['logout'])){
	Auth::logout();
	Header('Location: /');
	exit(0);
}

if(AuthUser::getInstance()->isAuthorized() && !Site::isAjaxRequest()){
	header('Location: /personal/');
	exit();
}

$page_errors = array();

if(isset($_POST['login']) && isset($_POST['password'])){
	
	if($_POST['login'] != 'admin')
		$_POST['login'] = CheckData::trimPhone($_POST['login']);
	
	$temp_id = 0;
	if($USER->getField('is_tmp')){
		$temp_id = $USER->getID();
	}
	
	$res = Auth::login($_POST['login'], $_POST['password']);
	if($res > 0){
		AuthUser::getInstance()->reAuth();
		if(isset($_SESSION['cart_user_id']) && $_SESSION['cart_user_id'] != AuthUser::getInstance()->getCartUserId()){
			$wl = $db->query('select * from wishlist where user_id='.intval(AuthUser::getInstance()->getCartUserId()));
			while($t = $wl->getNext()){
				$db->query('delete from wishlist where id='.$t['id']);
				$db->query('delete from wishlist_items where wishlist_id='.$t['id']);
			}
			$db->query('update wishlist_users set user_id='.AuthUser::getInstance()->getId().' where id='.intval($_SESSION['cart_user_id']));
			$db->query('delete from wishlist_users where id='.intval(AuthUser::getInstance()->getCartUserId()));
		}
		if(!Site::isAjaxRequest()){
			Header('Location: /');
			exit();
		}
	}elseif($res == 0){
		$page_errors[] = 'Неверный логин/пароль';
	}else{
		$page_errors[] = 'Аккаунт заблокирован из соображений безопасности. Свяжитесь, пожалуйста, с администрацией ресурса';
	}
}

if(Site::isAjaxRequest()){
	
	$return_mode = isset($_POST['_return_json']) ? 'json' : 'html';

	if($return_mode == 'json'){
		
		$result = array('error' => '', 'succes' => '', 'data' => array());
		if(count($page_errors))
			$result['error'] = implode('<br>', $page_errors);
		else{
			$data = $db->query('select * from users where id='.intval($res))->getNext();
			if($data){
				$result['data'] = array(
					'name' => $data['name'],
					'surname' => $data['surname'],
					'p_name' => $data['p_name'],
					'address' => $data['address'],
					'email' => $data['email'],
					'phone' => $data['phone']
				);
				$result['success'] = 'Рады видеть вас, '.$data['name'].'!';
			}
		}
		
		echo json_encode($result);
		
	}else{
		if(count($page_errors)){
			echo implode('<br>', $page_errors);
		}
	}
	exit();
}else{
	if(count($page_errors)){
		foreach($page_errors as $v){
			//SiteNotices::addNotice($v);
		}
	}
}

?>