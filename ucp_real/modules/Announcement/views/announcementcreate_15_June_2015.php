<div class="col-md-12">
    <div class="contact-container">
        <div class="alert" role="alert" style="display:none"></div>
        <form role="form" id="<?php echo ($add) ? 'add' : 'edit'?>Announcement">
            <h3>Add Announcement</h3>
            <div class="form-group">
                <label for="description" class="help"><?php echo _('Description')?>&nbsp;<i class="fa fa-question-circle"></i></label>
                <input type="text" class="form-control" id="description" placeholder="Announcement Description" value="<?php echo $announcement['description']?>">
                <span data-for="description" class="help-block help-hidden">The name of this announcement.</span>
            </div>
            
            <?php $validRecording = ($add)?'default':$announcement['recording_id'];?>
            <div class="form-group">
                <label for="recording_id" class="help"><?php echo _('Recording')?>&nbsp;<i class="fa fa-question-circle"></i></label>
                <select class="form-control" id="recording_id" name="recording_id">
                    <option value="">None</option>
                    <?php                    
                    if(count($allRecordings) > 0)
                    {
                        foreach ($allRecordings as $Recordingvalue) 
                        {
                            $RecordingSelected = ($Recordingvalue['id']==$validRecording)?'selected':'';
                            echo '<option value="'.$Recordingvalue['id'].'" '.$RecordingSelected.' >'.$Recordingvalue['displayname'].'</option>';
                        }
                    }
                    ?>                    
                </select>	
                <span data-for="recording_id" class="help-block help-hidden">Message to be played.<br/>To add additional recordings use the "System Recordings" MENU to the left</span>
            </div>
            
            <?php 
            $InvalidLoops = ($add)?'disabled':$announcement['repeat_msg'];
            $DisableSelected = ($InvalidLoops=='disabled')?'selected':'';
            $StartSelecteded = ($announcement['repeat_msg']=='*')?'selected':'';
            $HashSelecteded = ($announcement['repeat_msg']=='#')?'selected':'';
            ?>
            <div class="form-group">
                <label for="repeat_msg" class="help"><?php echo _('Repeat')?>&nbsp;<i class="fa fa-question-circle"></i></label>
                <select class="form-control" id="repeat_msg" name="repeat_msg">
                    <option value="disabled" <?php echo $DisableSelected;?> >Disabled</option>
                    <?php
                    for($i=0; $i <10; $i++)
                    {
                        $InvalidLoopsSelected = ($InvalidLoops==$i && $DisableSelected=='')?'selected':'';
                        echo '<option value="'.$i.'" '.$InvalidLoopsSelected.' >'.$i.'</option>';
                    }
                    ?>
                    <option value="*" <?php echo $StartSelecteded; ?>>*</option>
                    <option value="#" <?php echo $HashSelecteded; ?>>#</option>
                </select>
                <span data-for="repeat_msg" class="help-block help-hidden">Key to press that will allow for the message to be replayed. If you choose this option there will be a short delay inserted after the message. If a longer delay is needed it should be incorporated into the recording.</span>
            </div>
            
            <div class="form-group">
                <label for="allow_skip" class="help"><?php echo _('Allow Skip')?>&nbsp;<i class="fa fa-question-circle"></i></label>
                <input type="checkbox" name="allow_skip" id="allow_skip" value="on" <?php echo ($announcement['allow_skip']=='1')?'checked':'';?> >
                <div class="clearfix"></div>
                <span data-for="allow_skip" class="help-block help-hidden">If the caller is allowed to press a key to skip the message.</span>
            </div>
            
            <div class="form-group">
                <label for="return_ivr" class="help"><?php echo _('Return to IVR')?>&nbsp;<i class="fa fa-question-circle"></i></label>
                <input type="checkbox" name="return_ivr" id="return_ivr" value="on" <?php echo ($announcement['return_ivr']=='1')?'checked':'';?> >
                <div class="clearfix"></div>
                <span data-for="return_ivr" class="help-block help-hidden">If this announcement came from an IVR and this box is checked, the destination below will be ignored and instead it will return to the calling IVR. Otherwise, the destination below will be taken. Don't check if not using in this mode. <br>The IVR return location will be to the last IVR in the call chain that was called so be careful to only check when needed. For example, if an IVR directs a call to another destination which eventually calls this announcement and this box is checked, it will return to that IVR which may not be the expected behavior.</span>
            </div>
            
            <div class="form-group">
                <label for="noanswer" class="help"><?php echo _('Don\'t Answer Channel')?>&nbsp;<i class="fa fa-question-circle"></i></label>
                <input type="checkbox" name="noanswer" id="noanswer" value="on" <?php echo ($announcement['noanswer']=='1')?'checked':'';?> >
                <div class="clearfix"></div>
                <span data-for="noanswer" class="help-block help-hidden">Check this to keep the channel from explicitly being answered. When checked, the message will be played and if the channel is not already answered it will be delivered as early media if the channel supports that. When not checked, the channel is answered followed by a 1 second delay. When using an announcement from an IVR or other sources that have already answered the channel, that 1 second delay may not be desired.</span>
            </div>
            
            <input type="hidden" value="gotoinvalid" id="destination" name="destination">
            <input type="hidden" id="mode" name="mode" value="<?php echo ($add) ? 'add' : 'edit'?>">                        
            <?php if($add) { ?>
                <button id="addannouncement" class="btn btn-default"><?php echo _('Add Announcement')?></button>
            <?php } if($edit) {?>
                <input type="hidden" id="id" name="id" value="<?php echo $announcement['announcement_id']?>">
                <button id="editannouncement" class="btn btn-default"><?php echo _('Edit Announcement')?></button>
                <button id="deleteannouncement" class="btn btn-default"><i class="fa fa-trash-o"></i> <?php echo _('Delete Announcement')?></button>
            <?php } ?>
                
            <?php /*?>
            <input type="hidden" id="id" name="id" value="<?php echo $announcement['id']?>">
            <button id="deleteivr" class="btn btn-default"><i class="fa fa-trash-o"></i> <?php echo _('Delete IVR')?></button>
            <?php */?>
			
        </form>
    </div>
</div>
