<?if(!defined("IN_CONTEXT")) exit(0);?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>	
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">

		<title>Вход в систему управления сайтом</title>
		
		<link href="/engine/templates/admin/css/main.css?<?=filemtime(B_DIR.'engine/templates/admin/css/main.css')?>" rel="stylesheet"/>
		
		<script src="/engine/tools/jquery-1.10.2.min.js"></script>		
		<script type="text/javascript" src="/engine/templates/admin/js/plugins.js"></script>
		<script type="text/javascript" src="/engine/templates/admin/js/functions.js"></script>
		<script type="text/javascript" src="/engine/templates/admin/js/adminpanel.js"></script>				
		<script type="text/javascript" src="/engine/templates/admin/js/ui.js?<?=filemtime(B_DIR.'engine/templates/admin/js/ui.js')?>"></script>

	</head>
	<body class="login">
		
		<div class="wrap">
		
			<div class="login__form">
				
				<form action="<?=$_SERVER['REQUEST_URI']?>" method="post">

					<h1><i>im.</i>Engine</h1>

					<input type="hidden" name="do_login" value="1"/>				

					<?if($login_error != ''){?>
						<div class="alert alert-danger">
							<?=$login_error?>
						</div>
					<?}?>

					<div class="login__form__item">
						<input name="login" type="text" value="<?if(isset($_POST['login']) && isset($_POST['do_login'])){echo htmlspecialchars($_POST['login']);}?>" placeholder="Логин" required autofocus>
					</div>
					<div class="login__form__item">
						<input name="password" type="password" class="form-control" placeholder="Пароль" required>
					</div>
					<div class="login__form__item checkbox">
						<label>
							<input type="checkbox" name="remember" value="1"> запомнить меня
						</label>
					</div>
					<div class="login__form__submit">
						<button class="btn" type="submit">Войти</button>
					</div>
				</form>
				
			</div>
			
		</div>
		
	</body>
</html>