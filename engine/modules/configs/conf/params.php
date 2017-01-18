<?
if(!defined('IN_CONTEXT')) exit(0);

$articles = $db->query('select * from folders where entity="articles" and parent=0 order by pos')->fetchArray();
$price_types = $db->query('select * from catalog_price_types order by pos')->fetchArray();
$statuses = $db->query('select * from order_statuses where 1 order by pos')->fetchArray();
	
$params = array(	
	'email' => array('E-mail', array('field')),
	'mailing_from' => array('From в рассылках', array('field')),
	'phone' => array('Телефон в шапке', array('field')),
	'phone_footer' => array('Телефоны в футере', array('textarea')),
	'working_time' => array('Часы работы', array('field')),
	//'contacts' => array('Контактная информация', array('textarea')),
	'order_success' => array('Сообщение об успешном заказе', array('textarea')),
	'order_email_subj' => array('Тема письма о заказе', array('field')),
	'order_email_before' => array('Текст письма о заказе (до списка товаров)', array('textarea')),
	'order_email_after' => array('Текст письма о заказе (после списка товаров)', array('textarea')),
	//'qiwi_login' => array('QIWI Login', array('field')),
	//'qiwi_password' => array('QIWI Password', array('field')),
	//'news_folder' => array('Новости', array('select', $articles, 'id', 'name')),
	'vkontakte_id' => array('Vkontakte AppID', array('field')),
	'vkontakte_secret' => array('Vkontakte Secret', array('field')),
	'facebook_id' => array('Facebook AppID', array('field')),
	'facebook_secret' => array('Facebook Secret', array('field')),
	'facebook_link' => array('Facebook Page', array('field')),
	//'fb_code' => array('Группа Facebook', array('textarea')),
	'vk_link' => array('Vkontakte Page', array('field')),
	//'vk_code' => array('Группа VK', array('textarea')),
	'twitter_name' => array('Twitter Name', array('field')),
	'twitter_link' => array('Twitter Page', array('field')),
	'twitter_code' => array('Группа Twitter', array('textarea')),
	'instagram_link' => array('Instagram Page', array('field')),
	/*
	'robokassa_login' => array('Robokassa Login', array('field')),
	'robokassa_password1' => array('Robokassa Password 1', array('field')),
	'robokassa_password2' => array('Robokassa Password 2', array('field'))
	*/
	'link_dogovor_offerta' => array('Договор публичной офферты', array('field')),
	//'link_registration_doc' => array('Регистрационное удостоверение', array('field')),
	//'link_certificate' => array('Сертификат соответствия', array('field')),
	'collective_price_type' => array('Цена коллективной закупки', array('select', $price_types, 'id', 'name')),
	'copyright' => array('Copyright', array('field')),
	'yandex_shop_id' => array('Yandex ShopId', array('field')),
	'yandex_shop_password' => array('Yandex ShopPassword', array('field')),
	'yandex_scid' => array('Yandex Номер витрины', array('field')),
	'payment_ok' => array('Оплата прошла успешно', array('textarea')),
	'payment_fail' => array('Оплата отклонена', array('textarea')),
	'payment_ok_status' => array('Статус после оплаты', array('select', $statuses, 'id', 'name')),
);
			
?>