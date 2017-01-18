<?if(!defined('IN_CONTEXT'))exit();?>
<?=str_replace('#ORDER_ID#', $order_data['id'], DBCommon::getConfig('payment_ok'))?>