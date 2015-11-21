
<div class="container-fluid">
	<h1><?php echo _('SIPSTATION')?></h1>

	<div class="well well-info">
		<?php echo sprintf(_("This module requires %s trunking service available at %s or use the window below.
				Once you have service a key will be available in the portal. Enter it below to use this module.
				The key is very long, use \"Copy\" &amp; \"Paste\" to copy it here. The key will be stored securely and can be removed at any time to stop access.
				If the key is compromised, you can contact us at our %s and have a new one re-generated."),
				'<a href="https://store.freepbx.com" target="_sipstation" title="FreePBX SIP Store and Portal">SIPStation</a>',
				'<a href="https://store.freepbx.com" target="_sipstation" title="FreePBX SIP Store and Portal">https://store.freepbx.com</a>',
				'<a href="http://support.schmoozecom.com" target="_sipstationsupport" title="Schmoozecom Support">Support Center</a>')?>
				<br/>
				<br/>
		<label for="account_key"><strong><?php echo _("Account Key")?></strong></label>
		<input type="text" size="100" id="account_key" name="account_key" tabindex="<?php echo ++$tabindex;?>">
		<input type="submit" name="add_key" id="add_key" value="<?php echo _("Add Key")?>" tabindex="<?php echo ++$tabindex;?>">
	</div>
	<div class = "display full-border">
		<div class="row">
			<div class="col-sm-12">
				<div class="fpbx-container">
					<ul class="nav nav-tabs" role="tablist">
						<li role="presentation" data-name="ssstore" class="change-tab">
							<a href="#ssstore" aria-controls="ssstore" role="tab" data-toggle="tab">
								<?php echo _("SIPSTATION Store")?>
							</a>
						</li>
						<li role="presentation" data-name="sstrial" class="change-tab sstrialtab">
							<a href="#sstrial" id="sstrial" aria-controls="sstrial" role="tab" data-toggle="tab">
								<?php echo _("SIPSTATION Free Trial")?>
							</a>
						</li>

					</ul>
					<div class="tab-content display">
						<div role="tabpanel" id="ssstore" class="tab-pane active">
							<iframe src="https://sipstation.schmoozecom.com" seamless width='100%' height="700px" frameborder="0"></iframe>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
