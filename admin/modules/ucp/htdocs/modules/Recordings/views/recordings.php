<div class="col-md-10">
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
	
    <a cm-pjax href="?display=dashboard&amp;mod=recordings&amp;view=addrecording" class="btn btn-default btn-sm"><i class="fa fa-plus"></i> <?php echo _('Add New Recording')?></a>        
	
    <div class="table-responsive">
        <table class="table table-hover table-bordered recording-table">
            <thead class="recording-header">
                <tr>
                    <th data-type="displayname"><?php echo _('Display Name')?><i class="fa fa-chevron-<?php echo ($order == 'desc' && $orderby == 'displayname') ? 'down' : 'up'?> <?php echo ($orderby == 'displayname') ? '' : 'hidden'?>"></i></th>                            
                </tr>
            </thead>
            <?php foreach($recordings as $recording) {?>
            <tr class="recording-item" data-recording="<?php echo $recording['id']?>">
                <td><?php echo $recording['displayname'];?></td>
            </tr>
            <?php } ?>
        </table>
    </div>
    <?php echo $pagnation;?>
</div>
