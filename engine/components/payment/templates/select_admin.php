<?if(isset($list)){?>

	<div class="form-group" id="order-payment">
		<label class="control-label col-lg-3">Способ оплаты</label>
		<div class="col-lg-9">
			
			<select name="payment" class="form-control">
				<option value="">--</option>
				<?while($v = $list->getNext()){?>
					<option value="<?=$v['id']?>"<?if(isset($_POST['current_type']) && $_POST['current_type'] == $v['id']){?> selected<?}?>><?=$v['name']?></option>
				<?}?>
			</select>

		</div>
	</div>

<?}?>