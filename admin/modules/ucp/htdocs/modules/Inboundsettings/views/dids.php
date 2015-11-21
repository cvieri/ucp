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
    <?php /*?>
    <a cm-pjax href="?display=dashboard&amp;mod=inboundsettings&amp;view=adddid" class="btn btn-default btn-sm"><i class="fa fa-plus"></i> <?php echo _('Add New DID')?></a>    
    <?php */ ?>
    <div class="table-responsive">
        <table class="table table-hover table-bordered did-table inboundsettings-table">
            <thead class="did-header inboundsettings-header">
                <tr>
                    <th data-type="did"><?php echo _('DID')?><i class="fa fa-chevron-<?php echo ($order == 'desc' && $orderby == 'did') ? 'down' : 'up'?> <?php echo ($orderby == 'did') ? '' : 'hidden'?>"></i></th>
                    <th data-type="forwardnumber"><?php echo _('Forward Number')?><i class="fa fa-chevron-<?php echo ($order == 'desc' && $orderby == 'forwardnumber') ? 'down' : 'up'?> <?php echo ($orderby == 'forwardnumber') ? '' : 'hidden'?>"></i></th>
                    <th data-type="ivr"><?php echo _('IVR')?><i class="fa fa-chevron-<?php echo ($order == 'desc' && $orderby == 'ivr') ? 'down' : 'up'?> <?php echo ($orderby == 'ivr') ? '' : 'hidden'?>"></i></th>                    
                </tr>
            </thead>
            <?php foreach($dids as $did) {?>
                <tr class="did-item inboundsettings-item" data-did="<?php echo $did['id']?>">
                    <td><?php echo $did['did'];?></td>
                    <td><?php echo $did['forwardnumber'];?></td>
                    <td><?php echo $did['ivr'];?></td>
                </tr>
            <?php } ?>
        </table>
    </div>
    <?php echo $pagnation;?>
</div>
