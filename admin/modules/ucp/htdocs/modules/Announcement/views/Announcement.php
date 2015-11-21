<div class="col-md-12">
    <div class="row nav-container">
        <div class="col-sm-8">
            <?php echo $pagnation; ?>
        </div>
        <div class="col-sm-4">
            <div class="input-group">
                <input type="text" class="form-control" id="search-text" placeholder="<?php echo _('Search') ?>" value="<?php echo $search ?>">
                <span class="input-group-btn">
                    <button class="btn btn-default" type="button" id="search-btn"><?php echo _('Go!') ?>'</button>
                </span>
            </div>
        </div>
    </div>
    <a cm-pjax href="?display=dashboard&amp;mod=announcement&amp;view=addannouncement" class="btn btn-default btn-sm"><i class="fa fa-plus"></i> <?php echo _('Add New Announcement') ?></a>
    <div class="table-responsive">
        <table class="table table-hover table-bordered announcement-table">
            <thead class="announcement-header">
                <tr>
                    <th data-type="description"><?php echo _('Announcement Name') ?><i class="fa fa-chevron-<?php echo ($order == 'desc' && $orderby == 'description') ? 'down' : 'up' ?> <?php echo ($orderby == 'description') ? '' : 'hidden' ?>"></i></th>
                    <th data-type="allow_skip"><?php echo _('Allow Skip ') ?><i class="fa fa-chevron-<?php echo ($order == 'desc' && $orderby == 'allow_skip') ? 'down' : 'up' ?> <?php echo ($orderby == 'allow_skip') ? '' : 'hidden' ?>"></i></th>
                    <th data-type="return_ivr"><?php echo _('Return to IVR ') ?><i class="fa fa-chevron-<?php echo ($order == 'desc' && $orderby == 'return_ivr') ? 'down' : 'up' ?> <?php echo ($orderby == 'return_ivr') ? '' : 'hidden' ?>"></i></th>
                    <th data-type="noanswer"><?php echo _('Don\'t Answer Channel ') ?><i class="fa fa-chevron-<?php echo ($order == 'desc' && $orderby == 'noanswer') ? 'down' : 'up' ?> <?php echo ($orderby == 'noanswer') ? '' : 'hidden' ?>"></i></th>
                </tr>
            </thead>
            <?php foreach ($announcements as $announcement) { ?>
                <tr class="announcement-item" data-announcement="<?php echo $announcement['announcementid']?>">
                    <td><?php echo $announcement['description']; ?></td>
                    <td><?php echo ($announcement['allow_skip']=='1') ? 'ON' : 'OFF'; ?></td>
                    <td><?php echo ($announcement['return_ivr']=='1') ? 'ON' : 'OFF'; ?></td>
                    <td><?php echo ($announcement['noanswer']=='1') ? 'ON' : 'OFF'; ?></td>
                </tr>
            <?php } ?>
        </table>
    </div>
    <?php echo $pagnation; ?>
</div>