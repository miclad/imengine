<?if(!Site::isAjaxRequest()){?>
<script type="text/javascript" src="/js/idelivery.js?<?=filemtime(B_DIR.'js/idelivery.js')?>"></script>
<script type="text/javascript" src="http://pickpoint.ru/select/postamat.js"/></script>
<script type="text/javascript">

	var oDelivery = new iDelivery(
		<?=intval($this->getInitParam('order_sum'))?>,// order sum
		<?=intval($this->getInitParam('order_sum_full'))?> // order sum without discount
	);
	
	oDelivery.setParam('weight', <?=str_replace(',', '.', floatval($this->getConfig('weight') ? $this->getConfig('weight') : 1))?>);
	
	sumChangeHandler = oDelivery;
	
	$(document).ready(
		function(){
			oDelivery.init();
			<?if(isset($current_type[0]) && $current_type[0]){?>
			oDelivery.typeChanged(<?=$current_type[0]?>);
			<?}?>
		}
	);
	

</script>
<?}?>

<div>
	<div class="error-message"></div>
	<input type="hidden" name="delivery_ok" value="" data-required="1" data-error-text-empty="Пожалуйста, выберите способ доставки"/>
</div>
<div>
	<input type="hidden" name="delivery_error" value=""/>
	<input type="hidden" name="delivery_type_str" value="<?=$this->getConfig('current_type')?>"/>
	<input type="hidden" name="delivery[price]" value="0"/>
</div>

<div id="delivery-types-container">
	<?require 'types_list.php'?>
</div>