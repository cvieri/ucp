<div class="col-md-10">
    <div class="recording-container">
        <div class="alert" role="alert" style="display:none"></div>
        <form name="prompt"  role="form">
            <input type="hidden" name="id" id="id" value="<?php echo $id ?>">
            <fieldset><legend>System Recordings</legend></fieldset>
            <?php
            if(count($usage_list) > 0)
            {
                echo "<ul>";
                foreach ($usage_list as $link)
                {
                    echo '<li><a href="'.$link['url_query'].'" title="'.$link['description'].'">'.$link['description'].'</a></li>';
                }
                echo "</ul>";

            }
            ?>
            <div class="form-group">
                <label for="rname" class="help"><?php echo _('Change Name')?>&nbsp;<i class="fa fa-question-circle"></i></label>
                <input type="text" class="form-control" name="rname" id="rname" value="<?php echo $recordingdata['displayname']; ?>">
                <span data-for="rname" class="help-block help-hidden">This changes the short name, visible on the right, of this recording</span>
            </div>
            <div class="form-group">
                <label for="notes" class="help"><?php echo _('Descriptive Name')?>&nbsp;<i class="fa fa-question-circle"></i></label>
                <textarea class="form-control" rows="5" name="notes" id="notes"><?php echo $recordingdata['description']; ?></textarea>
                <span data-for="notes" class="help-block help-hidden">This is displayed, as a hint, when selecting this recording in Queues, Digital Receptionist, etc</span>
            </div>
            
            <?php
            $rec = $recordingdata;
            $fn = $rec['filename'];
            $files = explode('&', $fn);
            $counter = 0;
            $arraymax = count($files)-1;
            $sndfile_html = "";
            $jq_autofill = "";
            
            if ($arraymax == 0 && isset($files[0]) && substr($files[0],0,7) == 'custom/')
            {
                if ($rec['fcode'])
                {
                    $fcc = new featurecode("recordings", 'edit-recording-'.$id);
                    $rec_code = $fcc->getCode();
                    unset($fcc);
                    if ($rec_code == '')
                    {
                        $rec_code = $fcbase.$id;
                    }
                }
                else
                {
                    $rec_code = $fcbase.$id;
                }
                ?>
            
                <div class="form-group">
                    <label style="padding: 0px;" for="fcode" class="col-md-4 help"><?php echo _('Link to Feature Code')?>&nbsp;<i class="fa fa-question-circle"></i></label>
                    <div class="col-md-8">
                        <input type="checkbox" name="fcode" id="fcode" <?php echo ($rec['fcode']=="1")?'checked':'';?> > Optional Feature Code <?php echo $rec_code;?>
                        <div class="clearfix"></div>
                        <span data-for="fcode" class="help-block help-hidden">Check this box to create an options feature code that will allow this recording to be changed directly.</span>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="form-group">
                    <label for="fcode_pass" class="help"><?php echo _('Feature Code Password')?>&nbsp;<i class="fa fa-question-circle"></i></label>
                    <input type="text" class="form-control" name="fcode_pass" id="fcode_pass" value="<?php echo $rec['fcode_pass']; ?>">
                    <span data-for="fcode_pass" class="help-block help-hidden">Optional password to protect access to this feature code which allows a user to re-record it.</span>
                </div>
                <?php
            }
            else
            {
                ?>
                <div class="form-group">
                    <p class="form-control-static">Direct Access Feature Codes for recordings are not available for built in system recordings or compound recordings made of multiple individual ones.</p>                
                </div>
                <?php
            }
            ?>
            
            <div class="form-group">                
                <button type="submit" id="edit_button" class="btn btn-default" ><?php echo _('Save')?></button>
                <?php if(count($usage_list) == 0) { ?>
                <button id="delete_recording" class="btn btn-default" type="button">Remove Recording</button>
                <p>Note, does not delete file from computer</p>
                <?php } ?>
            </div>
        </form>
        
        
        
        
	</div>
</div>
