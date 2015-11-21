<div id="sipstation-sms-did-list" class="extensions-list">
<?php foreach($dids as $did) {?>
		<label>
			<input type="checkbox" name="ucp|sipstation-sms-did[]" value="<?php echo '1'.$did['did']?>" <?php echo (in_array('1'.$did['did'], $assigned) ? 'checked' : '')?>> <?php echo '1'.$did['did']?>
		</label>
		<br />
<?php } ?>
</div>
