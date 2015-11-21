<?php echo _("This module is used to configure Parking Lot(s)") ?>
<br/><br/>
<div class="messageb">
	<?php echo sprintf(_("You can transfer a call to the Parking Lot Extension (%d by default), the call will then be placed into a lot (%d-%d by default) and the lot number will be announced to you"),70,71,78) ?>
	<br/>
	<?php echo sprintf(_("You can also transfer directly to a lot number (%d through %d) and if that lot is empty, your call will be parked there"),71,78)?>
</div>
<br/>
<table width="50%">
	<tr>
		<td colspan="2"><?php echo _("Example usage") ?>:</td>
	</tr>
	<tr>
		<td><?php echo "*270:" ?></td>
		<td><?php echo _("Attended Transfer call to the Parking Lot Extension. The lot number will be announced to the parker") ?></td>
	</tr>
	<tr>
		<td><?php echo "*275:" ?></td>
		<td><?php echo sprintf(_("Attended transfer to lot %d"),75) ?></td>
	</tr>
	<tr>
		<td><?php echo "*2"._("nn").":" ?></td>
		<td><?php echo _("Attended Transfer call into Park lot nn") ?></td>
	</tr>
	<tr>
		<td><?php echo "70:" ?></td>
		<td><?php echo _("Park Yourself. The lot number will be announced to you") ?></td>
	</tr>
	<tr>
		<td><?php echo "75:" ?></td>
		<td><?php echo sprintf(_("Park Yourself into lot %d"),75) ?></td>
	</tr>
	<tr>
		<td><?php echo _("nn").":" ?></td>
		<td><?php echo _("Park Yourself into lot nn") ?></td>
	</tr>
</table>
<?php echo _("The Parking Lot Extension and lot numbers can be changed using this module") ?>
<br/>
<?php if(function_exists('parking_overview_display')) { echo parking_overview_display(); }?>
