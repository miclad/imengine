<?

$event = isset($_GET['event']) ? substr($_GET['event'], 0, 255) : 'news';
		
$form_desc = array(
	array('title' => 'Имя', 'name' => 'name', 'datatype' => 'simple', 'req' => 1),
	array('title' => 'Фамилия', 'name' => 'surname', 'datatype' => 'simple', 'req' => 0),
	array('title' => 'E-mail', 'name' => 'email', 'datatype' => 'mail', 'req' => 1, 'unique' => array(1, 'event="'.mysql_escape_string($event).'"'), 'error555' => 'Введенный e-mail уже используется в этой рассылке')
);
		
$form = new UForm($form_desc, 'mailing_list', '', array('event' => $event), 'Ваш адрес добавлен в список рассылки', !$USER->isAuthorized() && !Site::isAjaxRequest());

$form->setValue('email', $USER->getField('email') ? $USER->getField('email') : $USER->getField('login'));
$form->setValue('name', $USER->getField('name'));

$json_result = array('error' => '', 'result' => '');
$message = '';
		
if(isset($_POST) && count($_POST)){
	$form->read();
	if(!$form->haveErrors()){
		$form->writeInDB();
		
		if($event != 'news'){
			$evt_array = explode('|', $event);
			if(count($evt_array)){
				$message = $evt_array[0] == 'avail' ? 'Мы оповестим вас, когда товар поступит в продажу' : 'Мы оповестим вас, когда цена на товар будет снижена';
			}			
		}else{
			$message = 'Ваш адрес добавлен в список рассылки';
		}
		
		if(!Site::isAjaxRequest()){
			Header('Location: '.Common::editUrl(array('a'), array('a' => 'done')));
			exit(0);
		}
		
	}else{
		if(!Site::isAjaxRequest()){
			//SiteNotices::addNotice($form->getErrors());
		}else{
			$json_result['error'] = $form->getErrors();
		}
	}
	
	if(Site::isAjaxRequest()){
		$json_result['result'] = $message;
		echo json_encode($json_result);
		exit();
	}
	
}
		
if(Site::isAjaxRequest())
	require 'templates/ajax_pre.php';
echo $form->printForm();
if(Site::isAjaxRequest())
	require 'templates/ajax_post.php';

?>