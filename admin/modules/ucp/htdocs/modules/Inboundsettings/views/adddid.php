<script type="text/javascript">
    function KeycheckOnlyPhonenumber(c) {
        var d = 0;
        d = document.all ? 3 : (document.getElementById ? 1 : (document.layers ? 2 : 0));
        if (document.all) {
            c = window.event
        }
        var b = "";
        var a = "";
        if (d == 2) {
            if (c.which > 0) {
                b = "(" + String.fromCharCode(c.which) + ")"
            }
            a = c.which
        } else {
            if (d == 3) {
                a = (window.event) ? event.keyCode : c.which
            } else {
                if (c.charCode > 0) {
                    b = "(" + String.fromCharCode(c.charCode) + ")"
                }
                a = c.charCode
            }
        }
        
        if ((a >= 65 && a <= 90) || (a >= 97 && a <= 122) || (a >= 33 && a <= 39) || (a==42) || (a >= 44 && a <= 45) || (a >= 47 && a <= 47) || (a >= 58 && a <= 64) || (a >= 91 && a <= 96) || (a >= 123 && a <= 126)) {
            return false
        }
        return true
    }
</script>    
<div class="col-md-6 col-md-offset-2">
    <div class="inboundsettings-container">
        <div class="alert" role="alert" style="display:none"></div>
        <form role="form" id="<?php echo ($add) ? 'add' : 'edit'?>did">
            <div class="form-group">
                <label for="did"><?php echo _('DID')?></label>
                <input type="text" class="form-control" id="did" placeholder="DID" value="<?php echo $did['did']?>" readonly="readonly">
            </div>
            <div class="form-group">
                <label for="forwardnumber"><?php echo _('Forwarding Number')?></label>
                <input type="text" class="form-control" id="forwardnumber" placeholder="Forwarding Number" value="<?php echo $did['forwardnumber']?>" onkeypress="return KeycheckOnlyPhonenumber(event);" maxlength="25">
            </div>
            <div class="form-group">
                <label for="ivr"><?php echo _('IVR')?></label>
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
