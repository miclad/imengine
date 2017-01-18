<?if(!defined("IN_CONTEXT")) exit(0);?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<meta http-equiv="Content-type" content="text/html; charset=utf-8;"/>
		<title>Квитанция</title>
		<meta name="keywords" content="<?=$PAGE->getMetaKeywords()?>"/>
		<meta name="description" content="<?=$PAGE->getMetaDescription()?>"/>
		<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
		<link rel="stylesheet" type="text/css" href="/css/main.css"/>		
		<style type="text/css">
			body{background:#fff;margin:20px;}
			li{list-style-type:none; padding-left:20px;}
			td{line-height:1.6em;}
		</style>
	</head>
	<body>
	
	
	<table width="720" bordercolor="#000000" style="border:#000000 1px solid;" cellpadding="0" cellspacing="0">
  <tr>

    <td width="220" valign="top" height="250" align="center" style="border-bottom:#000000 1px solid; border-right:#000000 1px solid;">&nbsp;<strong>Извещение</strong></td>
    <td valign="top" style="border-bottom:#000000 1px solid; border-right:#000000 1px solid; padding-top:10px; padding-right:10px;">
       <li><strong>Получатель: </strong> <?=$this->getConfig('company_name')?>&nbsp;&nbsp;&nbsp;<br />  
      <li><strong>КПП:</strong> <?=$this->getConfig('kpp')?>&nbsp;&nbsp;&nbsp;&nbsp; <strong>ИНН:</strong> <?=$this->getConfig('inn')?>&nbsp;&nbsp;<font style="font-size:11px"> &nbsp;</font> <br />

     <li><strong>P/сч.:</strong> <?=$this->getConfig('r_schet')?>&nbsp;&nbsp;
     &nbsp;     <li> <strong>в:</strong> <?=$this->getConfig('bank_name')?><br /> 
     <li><strong>БИК:</strong> <?=$this->getConfig('bik')?>&nbsp; <strong>К/сч.:</strong> <?=$this->getConfig('kor_schet')?><br />
     <li><strong>Платеж:</strong> <?if(isset($order_data)){?>оплата заказа №<?=$order_data['id']?>.<?}else{?>_____________________________<?}?><br />
     <li><strong>Плательщик:</strong>  __________________________________________________<br />
     <li><strong>Адрес плательщика:</strong> ____________________________________________<br />
	<?/*
     <li><strong>ИНН плательщика:</strong> ____________&nbsp;&nbsp;&nbsp;&nbsp; <strong>№ л/сч. плательщика:</strong> ______________       
     */?>
     <li><strong>Сумма:</strong> <?if(isset($order_data)){?><?=$order_data['sum_discount']+$order_data['delivery_price']?> руб. 00 коп. <?}else{?>____________________<?}?><br /><br /> 
    Подпись:________________________        Дата: __________________ <br /><br /> 
    </td>
  </tr>
  <tr>
    <td width="220" valign="top" height="250" align="center" style="border-bottom:#000000 1px solid; border-right:#000000 1px solid;">&nbsp;<strong>Квитанция</strong></td>
  	<td valign="top" style="border-bottom:#000000 1px solid; border-right:#000000 1px solid; padding-top:10px; padding-right:10px;">
       <li><strong>Получатель: </strong> <?=$this->getConfig('company_name')?>&nbsp;&nbsp;&nbsp;<br />  
      <li><strong>КПП:</strong> <?=$this->getConfig('kpp')?>&nbsp;&nbsp;&nbsp;&nbsp; <strong>ИНН:</strong> <?=$this->getConfig('inn')?>&nbsp;&nbsp;<font style="font-size:11px"> &nbsp;</font> <br />

     <li><strong>P/сч.:</strong> <?=$this->getConfig('r_schet')?>&nbsp;&nbsp;
     &nbsp;     <li> <strong>в:</strong> <?=$this->getConfig('bank_name')?><br /> 
     <li><strong>БИК:</strong> <?=$this->getConfig('bik')?>&nbsp; <strong>К/сч.:</strong> <?=$this->getConfig('kor_schet')?><br />
     <li><strong>Платеж:</strong> <?if(isset($order_data)){?>оплата заказа №<?=$order_data['id']?>.<?}else{?>_____________________________<?}?><br />
     <li><strong>Плательщик:</strong>  __________________________________________________<br />
     <li><strong>Адрес плательщика:</strong> ____________________________________________<br />
	<?/*
     <li><strong>ИНН плательщика:</strong> ____________&nbsp;&nbsp;&nbsp;&nbsp; <strong>№ л/сч. плательщика:</strong> ______________       
     */?>
     <li><strong>Сумма:</strong> <?if(isset($order_data)){?><?=$order_data['sum_discount']+$order_data['delivery_price']?> руб. 00 коп. <?}else{?>____________________<?}?><br /><br /> 
    Подпись:________________________        Дата: __________________ <br /><br /> 
    </td>
  </tr>
</table>
		

	</body>
</html>
