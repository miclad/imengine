<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-type" content="text/html; charset=utf-8;"/>
		<title><?=$PAGE->getHeadTitle()?></title>
		<meta name="keywords" content="<?=$PAGE->getMetaKeywords()?>"/>
		<meta name="description" content="<?=$PAGE->getMetaDescription()?>"/>
		
		<meta name='yandex-verification' content='6f237973bff37e06' />
		
		<link rel="stylesheet" href="/engine/tools/bootstrap/css/glyphicon.css" type="text/css"/>
		<link rel="stylesheet" type="text/css" href="/css/main.css?<?=filemtime(B_DIR.'css/main.css')?>"/>
		
		<meta name="viewport" content="width=1300">
		
		<script type="text/javascript" src="/js/jquery-1.10.1.min.js"></script>
		<script type="text/javascript" src="/js/jquery-ui.min.js?<?=filemtime(B_DIR.'js/jquery-ui.min.js')?>"></script>
		
		<script type="text/javascript" src="/js/magnific/magnific.js"></script>
		<link rel="stylesheet" type="text/css" href="/js/magnific/magnific-popup.css"/>
		<script type="text/javascript" src="/js/jquery.maskedinput.min.js"></script>
		
		<script type="text/javascript" src="/js/typewatch.js"></script>
		<script type="text/javascript" src="/js/functions.js?<?=filemtime(B_DIR.'js/functions.js')?>"></script>
		<script type="text/javascript" src="/js/share.js"></script>
		<script type="text/javascript" src="/js/iform.js"></script>
		<script type="text/javascript" src="/js/ajaxpager.js?<?=filemtime(B_DIR.'js/ajaxpager.js')?>"></script>
		<script type="text/javascript" src="/js/cart.js?<?=filemtime(B_DIR.'js/cart.js')?>"></script>
		<script type="text/javascript" src="/js/catalog.js?<?=filemtime(B_DIR.'js/catalog.js')?>"></script>
		<script type="text/javascript" src="/js/geo.js?<?=filemtime(B_DIR.'js/geo.js')?>"></script>
		<script type="text/javascript" src="/js/animatenumber.js"></script>
		<script type="text/javascript" src="/js/auth.js"></script>
		<script type="text/javascript" src="/js/ui.js?<?=filemtime(B_DIR.'js/ui.js')?>"></script>
		
		<script type="text/javascript" src="/js/plugins/royalslider.js"></script>
		
		<link rel="icon" href="/favicon.ico" type="image/x-icon">
		<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
		
		<script type="text/javascript" src="//vk.com/js/api/openapi.js?96"></script>
		
		<?if(DBCommon::getConfig('vkontakte_id')){?>
		<script type="text/javascript">
			var is_authorized = <?=$USER->isAuthorized() ? 1 : 0?>;
			var vk_id = '<?=DBCommon::getConfig('vkontakte_id')?>';
		</script>
		
		<script type="text/javascript">
			VK.init({apiId: vk_id, onlyWidgets: true});
		</script>
		<?}?>
		
	</head>
	<body>
		
		<?=Page::includeComponent('banners:main', array('placing' => 'codes_top', 'no_tags' => true))?>
	
		<?if(DBCommon::getConfig('facebook_id')){?>
		<div id="fb-root"></div>
		<script>(function(d, s, id) {
		  var js, fjs = d.getElementsByTagName(s)[0];
		  if (d.getElementById(id)) return;
		  js = d.createElement(s); js.id = id;
		  js.src = "//connect.facebook.net/ru_RU/all.js#xfbml=1&appId=<?=DBCommon::getConfig('facebook_id')?>";
		  fjs.parentNode.insertBefore(js, fjs);
		}(document, 'script', 'facebook-jssdk'));</script>
		<?}?>
		
		<div class="wrap">
			
			<div class="top-nav">
					<div class="container">
						
						<div class="top-nav__city">
							<i class="fa fa-map-marker"></i> <a href="/geo/?a=choose_region" class="popup-ajax dotted" data-role="city-name"><?=Region::getInstance()->getName()?></a>
							<?/*if($USER->isAuthorized()){?>
								<a href="/personal/"><i class="glyphicon glyphicon-user"></i> Личный кабинет</a>
								&nbsp;&nbsp; <a href="?logout"><i class="glyphicon glyphicon-log-out"></i> Выход</a>
							<?}else{?>
								<a href="/login/"><i class="glyphicon glyphicon-log-in"></i> &nbsp;Войти на сайт</a>
							<?}*/?>
						</div>
						
						<div class="top-nav__pages">
							<?=Page::includeComponent('menu:main', array('navi_type' => 'top'))?>
						</div>
						
						<div class="top-nav__user">
							
								<?
								$comp_count = count(Catalog::getCompare());
								?>
								<span class="icon-bubble">
									<a href="/catalog/compare/"><i class="fa fa-balance-scale"></i></a>
									<span id="compare-count" class="icon-bubble__bubble"<?if(!$comp_count){?> style="display: none;"<?}?>>
										<?=intval($comp_count)?>
									</span>
								</span>
							
								<?
								$fav_count = count(Catalog::getFavorites());
								?>
								<span class="icon-bubble">
									<a href="/catalog/favorites/"><i class="fa fa-heart"></i></a>
									<span id="favorites-count" class="icon-bubble__bubble"<?if(!$fav_count){?> style="display: none;"<?}?>>
										<?=intval($fav_count)?>
									</span>
								</span>								
							
								<?
								$stat = $cart_o->getWishlistStat($cart_o->getDefaultList(), true);
								?>
				
								<span class="icon-bubble">
									<a href="/cart/"><i class="fa fa-shopping-cart"></i></a>
									<span id="cart-mini-count" class="icon-bubble__bubble"<?if(!$stat['items_num']){?> style="display: none;"<?}?>>
										<?=intval($stat['items_num'])?>
									</span>
									<div class="browse__popup<?if(!$stat['items_num']){?> hidden<?}?>" id="cart-mini-popup">
										<ul>
											<li id="cart-mini-text">
												<?=$stat['text']?>
											</li>
											<li>
					 							<a href="/cart/" class="btn btn--mini">Оформить заказ</a>
											</li>
										</ul>
									</div>
								</span>

								<?if($USER->isAuthorized()){?>
									<span class="browse icon-bubble">
										<a href="/personal/" class="browse__open"><i class="fa fa-user"></i></a>
										<span class="icon-bubble__bubble"<?if(!$USER->getField('bonus')){?> style="display: none;"<?}?>>
											<?=$USER->getField('bonus').' '.Common::makeEnding($USER->getField('bonus'), 'балл', '', 'а', 'ов')?>
										</span>
										<div class="browse__popup">
											<ul>
												<li>
													<a href="/personal/">Профиль</a>
												</li>
												<li>
													<a href="/personal/orders/">История заказов</a>
												</li>
												<li>
													<a href="/personal/bonus/">Бонусы</a>
												</li>
												<li>
													<a href="?logout">Выход <i class="fa fa-sign-out"></i></a>
												</li>
											</ul>
										</div>
									</span>	
								<?}else{?>
									<span class="browse icon-bubble">
										<a href="/login" class="browse__open"><i class="fa fa-user"></i></a>
										<?=Page::includeComponent('login:main:popup')?>
									</span>
									
								<?}?>
						
						</div>
					</div>
			</div>
			
			<div class="wrap-inner">			

				<div class="header">

					<div class="container">	
						
						<div class="header__left">
							
							<div class="header__working-time">
								Интернет-магазин<br>
								работает круглосуточно
							</div>
							
							<div class="header__ask">
								<i class="fa fa-vk"></i> <a href="https://vk.com/im?sel=196418983" class="" target="_blank">задать вопрос</a>
							</div>							
							
						</div>
						
						<div class="header__logo">
							<a href="/"><img src="/img/logo.png"/></a>
						</div>
						
						<div class="header__right">
							
							<div class="header__phone">
								<?=DBCommon::getConfig('phone')?>
							</div>
							
							<div class="header__working-time">
								<?=DBCommon::getConfig('working_time')?>
							</div>
							
							<div class="header__callback">
								<i class="fa fa-phone"></i> <a href="/callback/" class="dotted popup-ajax" target="_blank">обратный звонок</a>								
							</div>							

						</div>						

					</div>

				</div>

				<div class="navbar">
					<div class="container">
						<div class="navbar__items">
							<?=Page::includeComponent('catalog:navbar')?>
						</div>
						<div class="navbar__search">
							
							<form action="/catalog/">
								<input type="hidden" name="a" value="search"/>
								<input type="text" name="search" placeholder="Введите название товара или категории"/>
								<button type="submit"><i class="fa fa-search"></i></button>
							</form>
						
						</div>
					</div>
				</div>

				<div class="page">
					
					<?if(!$PAGE->getAttribute('no_page_wrap')){?>
						<div class="content-wrap content-wrap--nobg">
						<div class="container">		
					<?}?>

						<?if(!$PAGE->getAttribute('hide_title')){?>
							<div class="page__head">
								<div class="breadcrumbs">
									<?=$PAGE->printPath()?>
								</div>
								<h1><?=$PAGE->getTitle()?></h1>
							</div>
						<?}?>

						<div class="page__body">
							<div class="row">
								<?if($PAGE->getAttribute('sidebar')){?>
									<div class="col-3">
										<?=$PAGE->getAttribute('sidebar')?>
									</div>
								<?}?>
								<div class="col-<?=$PAGE->getAttribute('sidebar') ? 9 : 12?>">
									<?if(SiteNotices::getList()){?>
										<div class="page__alert">
											<?=SiteNotices::getList()?>
										</div>
									<?}?>
									<?=$PAGE->getContent()?>
								</div>
							</div>
						</div>

					<?if(!$PAGE->getAttribute('no_page_wrap')){?>
						</div></div>		
					<?}?>

				</div>

			</div>	
			
			<div class="footer">
				<div class="container">
					<div class="row row--cellspace">
						<div class="col-3">
							<div class="footer__info">
								<div class="footer__logo">
									<a href="/"><img src="/img/logo-footer.png"/></a>
								</div>
								<div class="footer__contacts">
									<div>
										<i class="fa fa-phone"></i>
										<?=nl2br(DBCommon::getConfig('phone_footer'))?>
									</div>
									<div>
										<i class="fa fa-envelope-o"></i> <a href="mailto:<?=DBCommon::getConfig('email')?>"><?=DBCommon::getConfig('email')?></a>
									</div>
								</div>
								<div class="footer__disclaimer">
									<a href="<?=DBCommon::getConfig('link_dogovor_offerta')?>">Договор публичной оферты</a>
								</div>
								<div class="footer__socnet">
									<?if(DBCommon::getConfig('vk_link')){?>
										<a href="<?=DBCommon::getConfig('vk_link')?>" target="_blank"><i class="fa fa-vk"></i></a>
									<?}if(DBCommon::getConfig('facebook_link')){?>
										<a href="<?=DBCommon::getConfig('facebook_link')?>" target="_blank"><i class="fa fa-facebook"></i></a>
									<?}if(DBCommon::getConfig('twitter_link')){?>
										<a href="<?=DBCommon::getConfig('twitter_link')?>" target="_blank"><i class="fa fa-twitter"></i></a>
									<?}if(DBCommon::getConfig('instagram_link')){?>
										<a href="<?=DBCommon::getConfig('instagram_link')?>" target="_blank"><i class="fa fa-instagram"></i></a>
									<?}?>
								</div>
							</div>
						</div>
						<div class="col-9">
							<div class="footer__navbar">
								<?=Page::includeComponent('menu:main:footer', array('navi_type' => 'footer'))?>
							</div>							
						</div>
					</div>
				</div>
			</div>
			
		</div>
			
		
			
		
	<?=Page::includeComponent('banners:main', array('placing' => 'codes_bottom', 'no_tags' => true))?>		
		
		
	</body>
</html>