<div class="message alert" style="display:none;"></div>
<form role="form">
	<div class="form-group">
		<label for="cfringtimer" class="help"><?php echo _('CallForward Ringtimer') ?> <i class="fa fa-question-circle"></i></label><br/>
		<select name="cfringtimer" id="cfringtimer" class="form-control">
			<option value="0">Default</option>
			<option value="-1" <?php echo ($ringtime == -1) ? 'selected' : ''?>>Always</option>
			<?php foreach($cfringtimes as $key => $value) { ?>
				<option value="<?php echo $key?>" <?php echo ($ringtime == $key) ? 'selected' : ''?>><?php echo $value?> <?php echo _('Seconds')?></option>
			<?php } ?>
		</select>
		<span class="help-block help-hidden" data-for="cfringtimer"><?php echo _('Number of seconds to ring prior to going to voicemail or other fail over destinations that may be setup by an administrator on this account. The Always setting will ring the call forward destinaiton until answered or the caller hangs up. The Default setting will use the value set in Ring Time. Your setting here will be forced to Always if there is no Voicemail or alternartive fail over destination for a call to go to.')?></span>
	</div>
	<div class="form-group">
		<label for="call_forward" class="help"><?php echo _('Unconditional')?> <i class="fa fa-question-circle"></i></label></br>
		<div class="input-group cfnumber">
			<span class="input-group-addon">
				<input type="checkbox" id="call_forward_number_enable" name="call_forward_number_enable" data-el="call_forward" <?php echo !empty($CF) ? 'checked' : ''?>>
			</span>
			<input type="text" class="form-control" id="call_forward" name="call_forward" data-type="CF" value="<?php echo $CF?>">
		</div>
		<span class="help-block help-hidden" data-for="call_forward"><?php echo _('Forward immediately regardless of current state of line/PBX')?></span>
	</div>
	<div class="form-group">
		<label for="call_forward_unavailable" class="help"><?php echo _('Unavailable')?> <i class="fa fa-question-circle"></i></label></br>
		<div class="input-group cfnumber">
			<span class="input-group-addon">
				<input type="checkbox" id="call_forward_unavailable_number_enable" name="call_forward_unavailable_number_enable" data-el="call_forward_unavailable" <?php echo !empty($CFU) ? 'checked' : ''?>>
			</span>
			<input type="text" class="form-control" id="call_forward_unavailable" name="call_forward_unavailable" data-type="CFU" value="<?php echo $CFU?>">
		</div>
		<span class="help-block help-hidden" data-for="call_forward_unavailable"><?php echo _('Preconfigured number to which calls are forwarded if the customer endpoint becomes unresponsive due to an Internet outage or software/configuration failure of endpoint')?></span>
	</div>
	<div class="form-group">
		<label for="call_forward_busy" class="help"><?php echo _('Busy')?> <i class="fa fa-question-circle"></i></label></br>
		<div class="input-group cfnumber">
			<span class="input-group-addon">
				<input type="checkbox" id="call_forward_busy_number_enable" name="call_forward_busy_number_enable" data-el="call_forward_busy" <?php echo !empty($CFB) ? 'checked' : ''?>>
			</span>
			<input type="text" class="form-control" id="call_forward_busy" name="call_forward_busy" data-type="CFB" value="<?php echo $CFB?>">
		</div>
		<span class="help-block help-hidden" data-for="call_forward_busy"><?php echo _('Preconfigured number to which calls are forwarded if the customer endpoint is busy, usually due to being on an active call')?></span>
	</div>
</form>
