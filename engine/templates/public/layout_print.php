<?if(!defined("IN_CONTEXT")) exit(0);?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<meta http-equiv="Content-type" content="text/html; charset=utf-8;"/>
		<title><?=$PAGE->getTitle()?></title>
		<meta name="keywords" content="<?=$PAGE->getMetaKeywords()?>"/>
		<meta name="description" content="<?=$PAGE->getMetaDescription()?>"/>
		<link rel="stylesheet" type="text/css" href="/css/main.css"/>
		<link rel="stylesheet" type="text/css" href="/css/print.css"/>
		<link rel="icon" href="/favicon.ico" type="image/x-icon">
		<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
	</head>
	<body>
	
		<div style="padding:10px;">
		
			<h1><?=$PAGE->getTitle()?></h1>
			<?=$PAGE->getContent()?>
		
		</div>	
		
		<script type="text/javascript">
			window.print();
		</script>
		
		
	</body>
</html>