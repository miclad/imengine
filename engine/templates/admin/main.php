<!DOCTYPE html>
<html lang="en">
	<head>	
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">

		<title><?=NaviPath::getHeadTitle('Система управления сайтом')?></title>
		
		<script src="/engine/tools/jquery-1.10.2.min.js"></script>
		<?/*
		<script src="/engine/tools/bootstrap/js/bootstrap.min.js"></script>
		*/?>

		<script src="/engine/tools/jqueryui/js/jquery.ui.datepicker-ru.js"></script>
		<script src="/engine/tools/jqueryui/js/jquery-ui-1.10.3.custom.min.js"></script>
		<link href="/engine/tools/jqueryui/css/smoothness/jquery-ui-1.10.3.custom.min.css" rel="stylesheet">
		
		<link href="/engine/templates/admin/css/main.css?<?=filemtime(B_DIR.'engine/templates/admin/css/main.css')?>" rel="stylesheet"/>
		
		<script language="javascript" type="text/javascript" src="/engine/ajax/net.js"></script>
		<script language="javascript" type="text/javascript" src="/engine/ajax/comboBox.js"></script>
		
		<script type="text/javascript" src="/engine/templates/admin/js/plugins.js"></script>
		<script type="text/javascript" src="/engine/templates/admin/js/functions.js"></script>
		<script type="text/javascript" src="/engine/templates/admin/js/adminpanel.js"></script>
				
		<script type="text/javascript" src="/engine/templates/admin/js/ui.js?<?=filemtime(B_DIR.'engine/templates/admin/js/ui.js')?>"></script>
		
		
		<link href="/engine/tools/redactor/redactor.css" rel="stylesheet"/>
		<script type="text/javascript" src="/engine/tools/redactor/redactor.min.js"></script>
		<script language="javascript" type="text/javascript">
			var redactor_plugins = [];
			function initRedactor(){
				$('.ta_big, .ta_short').redactor({
					lang: 'ru',
					formattingTags: ['p', 'blockquote', 'pre', 'h1', 'h2'],
					imageFloatMargin: '20px',
					minHeight: 300,
					fixed: true,
					toolbarFixedBox: true,
					convertDivs: false,
					deniedTags: ['html', 'head', 'link', 'body', 'applet'],
					imageUpload: '/engine/tools/redactor_image_upload.php',
					cleanSpaces: true,
					plugins: redactor_plugins
				});
			};			
		</script>

	</head>
	<body>

		<div class="header">
			<div class="container">
				<div class="row row--nopad">
					<div class="col-2">
						<a class="header__logo" href="/engine/admin/">
							<i>im.</i>Engine
						</a>
					</div>					
					<div class="col-10">
						<div class="header__body">
							<div class="header__project-name">
								
							</div>	
							<div class="header__links">
								<ul>
									<li>
										<a href="/" target="_blank"><i class="fa fa-external-link"></i> <?=$_SERVER['SERVER_NAME']?></a>
									</li>
									<?if(AuthUser::getInstance()->isAuthorized()){?>
										<li><i class="fa fa-user"></i> <?=$USER->GetField('login')?></li>
										<li><a href="/engine/admin/?logout"><i class="fa fa-sign-out"></i> выйти</a></li>
									<?}else{?>
										<li><a href="/engine/admin/"><i class="fa fa-sign-in"></i> войти</a></li>
									<?}?>
								</ul>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="body">
			<div class="container">
			
				<div class="row row--nopad">			
					<div class="col-2 sidebar">
									          
								<ul class="nav">
									<?$menu = MenuTree::getInstance()->getItems()?>
									<?foreach($menu as $v){?>
										<?
										$ch = MenuTree::getInstance()->getItems($v['name']);
										if(!count($ch)) continue;
										?>
										<li <?if(MenuTree::getInstance()->isActive($v['name'])){?>class="s-active"<?}?>>
											<?if(!isset($v['icon']) || !$v['icon']) $v['icon'] = 'list';?>
											<a class="nav__group-title" href="<?=isset($v['url']) && $v['url'] ? $v['url'] : (isset($v['items']) && count($v['items']) ? $v['items'][0]['url'] : '')?>">
												<?if($v['icon']){?><span class="fa fa-<?=$v['icon']?>"></span><?}?>
												<?=$v['title']?>
											</a>
											<ul class="nav__group-items">
												<?foreach($ch as $child){?>
													<li <?if(MenuTree::getInstance()->isActive($child['name'])){?>class="s-active"<?}?>>
														<a href="<?=isset($child['url']) && $child['url'] ? $child['url'] : (isset($child['items']) && count($child['items']) ? $child['items'][0]['url'] : '')?>"><?=$child['title']?></a>
													</li>
												<?}?>
											</ul>
										</li>
									<?}?>
								</ul>
					
					</div>
					<div class="col-10 mainbar">
						
						<div class="page">					

							<ul class="breadcrumbs">
								<?$navi = NaviPath::getNavi();?>
								<?if($navi != ''){?>
									<li>
										<a href="/engine/admin/">Главная</a>
									</li>
									<?=$navi?>
								<?}?>
							</ul>
							
							<?if(SiteNotices::getList('success') != ''){?>
								<div class="alert alert-success alert-dismissable" role="alert">
									<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
									<?=SiteNotices::getList('success')?>
								</div>
							<?}?>

							<?if(SiteNotices::getList('error') != ''){?>
								<div class="alert alert-danger alert-dismissable" role="alert">
									<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
									<?=SiteNotices::getList('error')?>
								</div>
							<?}?>
							
							<div class="content-box">

								<?$mpath = MenuTree::getInstance()->getPath();?>
								
								<?
								$panels_buttons = AdminPanel::getInstance()->getSwitchablePanels('title');
								?>

								<?if(count($panels_buttons) || count($mpath) > 0){?>
									<div class="page__title">
										<?if(count($mpath)){?>
											<h1><?=NaviPath::getPageTitle()?></h1>
										<?}?>
										<?if(count($panels_buttons)){?>
											<div class="page__title__controls" data-role="sw-panels-controls">
												<ul>
													<?foreach($panels_buttons as $v){?>
														<li<?if($v['id'] == AdminPanel::getInstance()->getActivePanel()){?> class="s-active"<?}?>>
															<a href="#" data-panel-id="<?=$v['id']?>">
																<i class="fa fa-<?=$v['font_icon']?>" title="<?=$v['title']?>"></i>
															</a>
														</li>
													<?}?>
												</ul>												
											</div>
										<?}?>
									</div>
								<?}?>
								
								<?
								$tabs = MenuTree::getInstance()->getItems('_controls_tabs');
								if(count($tabs)){
								?>
									<div class="page__tabs">
										<ul>
											<?foreach($tabs as $k => $v){
												$cl = array();
												if(in_array($v['name'], $mpath) || $v['name'] == '_'.MenuTree::getInstance()->getCurrentItem())
													$cl[] = 's-active';											
											?>
												<li<?if(count($cl)){echo ' class="'.implode(' ', $cl).'"';}?>>
													<a href="<?=$v['url']?>">
														<?if(isset($v['icon']) && $v['icon']){?><span class="fa fa-<?=$v['icon']?>"></span><?}?> <?=$v['title']?>
													</a>
												</li>
											<?}?>
										</ul>
									</div>
									</div><div class="content-box">
								<?}?>
						
								<?
								$buttons = MenuTree::getInstance()->getItems('_controls_buttons');
								if(count($buttons)){
								?>
									<div class="page__buttons">
										<ul>
											<?foreach($buttons as $k => $v){
												$cl = array();
											?>
												<li>
													<a href="<?=$v['url']?>" class="btn<?if(count($cl)){echo ' '.implode(' ', $cl);}?>"<?if($v['new_window']){?> target="_blank"<?}?>>
														<?if(isset($v['icon']) && $v['icon']){?><span class="fa fa-<?=$v['icon']?>"></span><?}?> <?=$v['title']?>
													</a>
												</li>
											<?}?>
										</ul>
									</div>
								<?}?>
										
								<?
								$panels = AdminPanel::getInstance()->getSwitchablePanels();
								$panels_tabs = AdminPanel::getInstance()->getSwitchablePanels('links');
								if(count($panels)){
								?>
										
									<div class="switchable-panels">
										
										<?if(count($panels_tabs)){?>
										<div class="switchable-panels__tabs" data-role="sw-panels-controls">
											<ul>
												<?foreach($panels_tabs as $v){?>
													<li>
														<a href="#" data-panel-id="<?=$v['id']?>">
															<i class="fa fa-<?=$v['font_icon']?>"></i> <?=$v['title']?>
														</a>
													</li>
												<?}?>												
											</ul>
										</div>
										<?}?>
											
										<div class="switchable-panels__body">
											<?foreach($panels as $v){?>
												<div id="swp-<?=$v['id']?>"<?if($v['id'] == AdminPanel::getInstance()->getActivePanel()){?> style="display: block;"<?}?>>
													<div>
														<?=$v['content']?>
													</div>
												</div>
											<?}?>
										</div>
											
									</div>
										
								<?}?>
										
								<?=$content?>
								
							</div>
							
						</div>
						
					</div>
				</div>
			</div>
		</div>
		
		<script type="text/javascript">
			
			$(function(){
				initRedactor();
			});
			
		</script>
		
	</body>
</html>