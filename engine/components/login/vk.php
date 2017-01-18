<?

$ok = false;

//file_put_contents(B_DIR.'auth_vk.txt', print_r($_POST, true));

if((true || !$USER->isAuthorized()) && isset($_POST['vk_session']) && isset($_POST['user_data'])){
	
	$post_data = array_merge($_POST['vk_session'], $_POST['user_data']);
	
	// check secure
	$secure_ok = false;
	if(DBCommon::getConfig('vkontakte_secret')){
		$valid_keys = array('expire', 'mid', 'secret', 'sid');
		$str = '';
		foreach($valid_keys as $k){
			$str .= $k.'='.(isset($post_data[$k]) ? $post_data[$k] : '');
		}
		$str .= DBCommon::getConfig('vkontakte_secret');
		if(isset($post_data['sig']) && md5($str) == $post_data['sig']) 
			$secure_ok = true;
	}
	
	if($secure_ok){
	
		$data = array();
		$data['socnet_user_id']	= isset($post_data['uid']) ? $post_data['uid'] : '';
		$data['socnet_user_page'] = isset($post_data['href']) ? $post_data['href'] : '';
		$data['socnet_user_domain'] = isset($post_data['domain']) ? $post_data['domain'] : '';
		$data['name'] = isset($post_data['first_name']) && isset($post_data['last_name']) ? $post_data['first_name'].' '.$post_data['last_name'] : '';
		$data['phone'] = isset($post_data['mobile_phone']) && ($post_data['mobile_phone']) ? $post_data['mobile_phone'] : (isset($post_data['home_phone']) && ($post_data['home_phone']) ? $post_data['home_phone'] : '');
		
		if($data['socnet_user_id']){
			
			if($USER->isAuthorized())
				Auth::logout();
			
			$res = $db->query('select * from users where auth_type="vk" and socnet_user_id='.intval($data['socnet_user_id']))->getNext();
			if($res){
				Auth::login($res['login'], '', true, true);
				$ok = true;
			}else{
			
				$data['login'] = md5(Site::generatePassword()).'_'.($data['socnet_user_domain'] ? $data['socnet_user_domain'] : $data['socnet_user_id']);
				$data['password'] = Site::generatePassword();		
				$data['auth_type'] = 'vk';
				$data['level'] = 'user';
				$data['activated'] = 1;
				
				//file_put_contents(B_DIR.'auth_vk_p.txt', print_r($data, true));
				
				$GLOBALS['entities']['users'][1]['phone'][2] = 0;
				
				$dt_o = new DataTable('users');
				$res = $dt_o->insertRow($data);
				if($res){
					
					$ok = true;
					Auth::login($data['login'], '', true, true);
					
					if($post_data['photo_big'] != ''){
						$fn = substr($post_data['photo_big'], strrpos($post_data['photo_big'], '/'));
						copy($post_data['photo_big'], B_DIR.'tmp/photos/'.$fn);
						foreach($GLOBALS['entities']['users'][1]['image'][1][1] as $frm){
							$sz = explode(',', $frm[0]);
							Images::imageResizeUnified(B_DIR.'tmp/photos/'.$fn, $sz[0], $sz[1], B_DIR.$frm[2].$frm[1].$res.'.jpg', 2, $frm[3]);
						}
						$pi = pathinfo($fn);
						copy(B_DIR.'tmp/photos/'.$fn, B_DIR.'img/users/'.$res.'.'.$pi['extension']);
						unlink(B_DIR.'tmp/photos/'.$fn);
						$db->query('update users set orig_ext="'.$pi['extension'].'" where id='.$res)->getNext();
					}
					
				}
			
			}
			
		}
		
	}
	
	
}

echo $ok ? 1 : 0;

if(Site::isAjaxRequest())
	exit();

?>