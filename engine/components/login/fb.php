<?

$ok = false;

if(isset($_GET['code'])){
	$request_str = 'https://graph.facebook.com/oauth/access_token?client_id='.DBCommon::getConfig('facebook_id').'&redirect_uri='.urlencode('http://'.$_SERVER['SERVER_NAME'].'/login/?a=fb').'&client_secret='.DBCommon::getConfig('facebook_secret').'&code='.$_GET['code'];
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $request_str);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	$response = curl_exec($ch);
	curl_close($ch); 
	
	if($response != ''){
		$rsp = explode('&', $response);
		if(count($rsp) > 1){
			$token_array = explode('=', $rsp[0]);
			if(count($token_array)){
				
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, 'https://graph.facebook.com/me?access_token='.$token_array[1]);
				curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
				curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
				$response = curl_exec($ch);
				curl_close($ch);
				
				$user_data = (array)json_decode($response);
				
				if(isset($user_data['id'])){
					
					if($USER->isAuthorized())
						Auth::logout();
					
					$res = $db->query('select * from users where auth_type="fb" and socnet_user_id='.$user_data['id'])->getNext();
					if($res){
						
						Auth::login($res['login'], '', true);
						$ok = true;
						
					}else{
						
						$data['login'] = md5(Site::generatePassword()).'_'.$user_data['username'];
						$data['password'] = Site::generatePassword();
						$data['email'] = $user_data['email'];
						$data['name'] = $user_data['name'];
						$data['level'] = 'user';
						$data['socnet_user_id'] = $user_data['id'];
						$data['socnet_user_page'] = $user_data['link'];
						$data['socnet_user_domain'] = $user_data['username'];
						$data['auth_type'] = 'fb';
						$data['activated'] = 1;
						$data['phone'] = '';
						
						$GLOBALS['entities']['users'][1]['phone'][2] = 0;
						
						//file_put_contents(B_DIR.'fb.txt', print_r($data, true));
						
						$dt_o = new DataTable('users');
						$ins_id = $dt_o->insertRow($data);
						if($ins_id){
							
							Auth::login($data['login'], '', true);
							
							$ok = true;
				
							$ch = curl_init();
							curl_setopt($ch, CURLOPT_URL, 'http://graph.facebook.com/'.$user_data['username'].'/picture?type=large');
							curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
							curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
							$photo = curl_exec($ch);
							curl_close($ch);
							
							$path = B_DIR.'tmp/photos/'.md5($data['login']).'.jpg';
							file_put_contents($path, $photo);
							
							foreach($GLOBALS['entities']['users'][1]['image'][1][1] as $frm){
								$sz = explode(',', $frm[0]);
								Images::imageResizeUnified($path, $sz[0], $sz[1], B_DIR.$frm[2].$frm[1].$ins_id.'.jpg', 2, $frm[3]);
							}
							
							copy($path, B_DIR.'img/users/'.$ins_id.'.jpg');
							unlink($path);
							$db->query('update users set orig_ext="jpg" where id='.$ins_id)->getNext();
						
						}
					
					}
				
				}
				
			}
		}		
	}
	
	
}

?>

<script type="text/javascript">
	<?if($ok){?>
		window.close();
		window.opener.location.reload();
	<?}elseif(isset($_GET['error_reason']) && $_GET['error_reason'] == 'user_denied'){?>
		window.close();
	<?}?>
</script>

<?exit();?>