<div class="col-md-12">
	<div class="row nav-container">
		<div class="col-sm-8">
			<?php echo $pagnation;?>
		</div>
		<div class="col-sm-4">
			<div class="input-group">
				<input type="text" class="form-control" id="search-text" placeholder="<?php echo _('Search')?>" value="<?php echo $search?>">
				<span class="input-group-btn">
					<button class="btn btn-default" type="button" id="search-btn"><?php echo _('Go!')?>'</button>
				</span>
			</div>
		</div>
	</div>
	<?php if(count($ivrs)==0) { ?>
        <a cm-pjax href="?display=dashboard&amp;mod=ivr&amp;view=addivr" class="btn btn-default btn-sm"><i class="fa fa-plus"></i> <?php echo _('Add New Ivr')?></a>
        <?php } ?>
        <?php /*?>
        <button id="deleteivr" class="btn btn-default btn-sm pull-right" data-id="<?php echo $_REQUEST['id']?>"><i class="fa fa-trash-o"></i> <?php echo _('Delete Ivr')?></button>
        <?php */?>
	
	<div class="table-responsive">
		<table class="table table-hover table-bordered ivr-table">
			<thead class="ivr-header">
				<tr>
					<th data-type="name"><?php echo _('Name')?><i class="fa fa-chevron-<?php echo ($order == 'desc' && $orderby == 'name') ? 'down' : 'up'?> <?php echo ($orderby == 'name') ? '' : 'hidden'?>"></i></th>
					<th data-type="description"><?php echo _('Description')?><i class="fa fa-chevron-<?php echo ($order == 'desc' && $orderby == 'description') ? 'down' : 'up'?> <?php echo ($orderby == 'description') ? '' : 'hidden'?>"></i></th>					
				</tr>
			</thead>
			<?php foreach($ivrs as $ivr) {?>
				<tr class="ivr-item" data-ivr="<?php echo $ivr['ivrid']?>">
					<td><?php echo $ivr['name'];?></td>
					<td><?php echo $ivr['description'];?></td>
					
				</tr>
			<?php } ?>
		</table>
	</div>
	<?php echo $pagnation;?>
</div>
