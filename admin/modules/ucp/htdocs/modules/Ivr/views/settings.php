<div class="message alert" style="display:none;"></div>
<form role="form">
    <div class="form-group">
        <label for="useivr" class="help"><?php echo _('Use IVR')?> <i class="fa fa-question-circle"></i></label>
        <div class="onoffswitch">
            <input type="checkbox" name="useivr" class="onoffswitch-checkbox" id="useivr" <?php echo ($useivr=='1') ? 'checked' : ''?>>
            <label class="onoffswitch-label" for="useivr">
                <div class="onoffswitch-inner"></div>
                <div class="onoffswitch-switch"></div>
            </label>
        </div>
        <span class="help-block help-hidden" data-for="useivr-h"><?php echo _('A service whereby someone making a telephone call is notified of an incoming call and is able to place the first call on hold while answering the second.')?></span>
    </div>
</form>
