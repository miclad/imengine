<?if(!defined('IN_CONTEXT'))exit();?>
<?='<?xml version="1.0" encoding="UTF-8"?>'?>
<result>
<code><?=$code?></code>
<pay_for><?=$order_data['id']?></pay_for>
<?if($_POST['type'] == 'pay'){?>
<onpay_id><?=$_POST['onpay_id']?></onpay_id>
<order_id><?=$order_data['id']?></order_id>
<?}?>
<md5><?=$response_md5?></md5>
</result>