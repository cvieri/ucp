<div class="col-md-12">
	<?php if(!empty($message)) { ?>
		<div class="alert alert-<?php echo $message['type']?>"><?php echo $message['message']?></div>
	<?php } ?>
	<div class="row">
		<div class="col-sm-8">
			<?php echo $pagnation;?>
		</div>
		<div class="col-sm-4">
			<div class="input-group">
				<input type="text" class="form-control" id="search-text" placeholder="<?php echo _('Search')?>" value="<?php echo $search?>">
				<span class="input-group-btn">
					<button class="btn btn-default" type="button" id="search-btn"><?php echo _("Go")?>!</button>
				</span>
			</div>
		</div>
	</div>
	<div class="table-responsive">
		<table id="sms-table" class="table table-hover table-bordered message-table message-list">
			<thead>
				<tr class="message-header">
					<th class="noclick"></th>
					<th data-type="date"><?php echo _('Last Message Date')?> <i class="fa fa-chevron-<?php echo ($order == 'desc' && $orderby == 'date') ? 'down' : 'up'?> <?php echo ($orderby == 'date') ? '' : 'hidden'?>"></i></th>
					<th data-type="from"><?php echo _('DID')?> <i class="fa fa-chevron-<?php echo ($order == 'desc' && $orderby == 'from') ? 'down' : 'up'?> <?php echo ($orderby == 'from') ? '' : 'hidden'?>"></i></th>
					<th data-type="to"><?php echo _('To')?> <i class="fa fa-chevron-<?php echo ($order == 'desc' && $orderby == 'to') ? 'down' : 'up'?> <?php echo ($orderby == 'to') ? '' : 'hidden'?>"></i></th>
					<th class="noclick"><?php echo _('Controls')?></th>
				</tr>
			</thead>
			<tbody>
			<?php if(!empty($messages)) {?>
				<?php foreach($messages as $conversation){?>
						<?php $c = count($conversation['messages']); $last = $conversation['messages'][0]; $prev = 0;?>
						<tr class="sms-message" data-from="<?php echo $conversation['from']?>" data-to="<?php echo $conversation['to']?>">
							<td class=""></td>
							<td class=""><?php echo $last['tx_rx_datetime']?></td>
							<td class=""><?php echo $conversation['from']?></td>
							<td class=""><a onclick="UCP.Modules.Sms.startChat('<?php echo $conversation['from']?>','<?php echo $conversation['to']?>')"><?php echo $conversation['prettyto'] ?></a></td>
							<td class="actions"><a><i class="fa fa-eye" data-id="<?php echo $conversation['from']?><?php echo $conversation['to']?>"></i></a><a><i class="fa fa-trash-o"></i></a></td>
						</tr>
						<tr class="sms-message-body-container" id="<?php echo $conversation['from']?><?php echo $conversation['to']?>-messages">
							<td class=""></td>
							<td class="" colspan="4">
								<div class="sms-message-body">
									<?php foreach($conversation['messages'] as $message) { ?>
										<?php if($prev != date('j',$message['utime'])) {?>
											<div class="full-date"><?php echo date('m/d/Y',$message['utime'])?></div>
											<hr>
										<?php } ?>
										<span class="date">[<?php echo date('H:i',$message['utime'])?>]</span> <span class="name"><?php echo ($conversation['from'] == $message['from']) ? _('Me') : \UCP\UCP::create()->Modules->Sms->replaceDIDwithDisplay($message['from'])?></span>: <?php echo $message['body']?><br/>
										<?php $prev = date('j',$message['utime']);?>
									<?php } ?>
								</div>
							</td>
						</tr>
				<?php }?>
			<?php } else { ?>
				<tr id="no-messages" class="sms-message">
					<td colspan="8"><?php echo _('No Messages')?></td>
				</tr>
			<?php } ?>
			</tbody>
		</table>
	</div>
	<?php echo $pagnation;?>
</div>
