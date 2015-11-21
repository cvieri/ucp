<div class="col-md-6 col-md-offset-2">
    <div class="inboundsettings-container">
        <div class="alert" role="alert" style="display:none"></div>
        <form role="form" id="<?php echo ($add) ? 'add' : 'edit'?>did">
            <div class="form-group">
                <label for="did"><?php echo _('DID')?></label>
                <input type="text" class="form-control" id="did" placeholder="DID" value="<?php echo $did['did']?>">
            </div>
            <div class="form-group">
                <label for="forwardnumber"><?php echo _('Forwarding Number')?></label>
                <input type="text" class="form-control" id="forwardnumber" placeholder="Forwarding Number" value="<?php echo $did['forwardnumber']?>">
            </div>
            <div class="form-group">
                <label for="divertion"><?php echo _('IVR')?></label>
                <select class="form-control" name="ivr" id="ivr">
                    <option value=""><?php echo _('Select Divertion')?></option>
                    <option value="IVR" <?php echo ($did['ivr']=='IVR')?'selected="selected"':'';?>><?php echo _('IVR')?></option>
                    <option value="FORWARD" <?php echo ($did['ivr']=='FORWARD')?'selected="selected"':'';?>><?php echo _('FORWARD')?></option>
                    <option value="SIP NUMBER" <?php echo ($did['ivr']=='SIP NUMBER')?'selected="selected"':'';?>><?php echo _('SIP NUMBER')?></option>
                    <option value="VM" <?php echo ($did['ivr']=='VM')?'selected="selected"':'';?>><?php echo _('VM')?></option>
                </select>
            </div>
            <input type="hidden" id="mode" name="mode" value="<?php echo ($add) ? 'add' : 'edit'?>">
            <?php if($add) {?>
                    <button id="adddidbutton" class="btn btn-default"><?php echo _('Add DID')?></button>
            <?php } else { ?>
                    <input type="hidden" id="id" name="id" value="<?php echo $did['id']?>">
                    <button id="editdidbutton" class="btn btn-default"><?php echo _('Edit DID')?></button>
                    <!--<button id="deletedid" class="btn btn-default"><i class="fa fa-trash-o"></i> <?php //echo _('Delete DID')?></button>-->
            <?php } ?>
        </form>
    </div>
</div>
