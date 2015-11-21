<div class="container-fluid">
	<h1><?php echo _('SIPSTATION')?></h1>
	<div class = "display full-border">
		<div class="row">
			<div class="col-sm-12">
				<div class="fpbx-container">
					<ul class="nav nav-tabs" role="tablist">
						<li role="presentation" data-name="localset" class="active">
							<a href="#localset" aria-controls="localset" role="tab" data-toggle="tab">
								<?php echo _("Local Settings")?>
							</a>
						</li>
						<li role="presentation" data-name="ssstore" class="change-tab">
							<a href="#ssstore" aria-controls="ssstore" role="tab" data-toggle="tab">
								<?php echo _("SIPSTATION Store")?>
							</a>
						</li>
						<li role="presentation" data-name="sstrial" class="change-tab <?php echo empty($trialcontent)?'hidden':'' ?>">
							<a href="#" aria-controls="sstrial" role="tab" data-toggle="tab" onClick="sstrial_launch()">
								<?php echo _("SIPSTATION Free Trial")?>
							</a>
						</li>

					</ul>
					<div class="tab-content display">
						<div role="tabpanel" id="localset" class="tab-pane active">
							<table>
								<?php echo $localcontent ?>
							</table>
						</div>
						<div role="tabpanel" id="sstrial" class="tab-pane <?php echo empty($trialcontent)?'hidden':'' ?>">
							<table>
								<?php echo $trialcontent ?>
							</table>
						</div>
						<div role="tabpanel" id="ssstore" class="tab-pane">
							<iframe src="https://sipstation.schmoozecom.com/" seamless width='100%' height="600px"></iframe>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>