<div class="col-md-10">
    <div class="recording-container">
        <div class="alert" role="alert" style="display:none"></div>
        <form name="xtnprompt" action="<?php $_SERVER['PHP_SELF'] ?>" method="post" class="form-inline">
            <fieldset><legend>Step 1: Record or upload</legend></fieldset>
            <?php if(!empty($usersnum)) { ?>
            <div class="form-group">                
                <p class="form-control-static">Using your phone, dial <?php echo $FeaturedCodeArray['fc_save'];?><a href="javascript:;" title="Start speaking at the tone. Press # when finished."><i class="fa fa-question-circle"></i></a>and speak the message you wish to record. Press # when finished.</p>                
            </div>
            <?php } else { ?>
            <!--<div class="form-group">                
                <p class="form-control-static">If you wish to make and verify recordings from your phone, please enter your extension number here:</p>
            </div>-->
            <div class="form-group">                
                <p class="form-control-static">To record from the phone press Go </p>
            </div>
            <div class="form-group">                
                <input type="text" name="usersnum" id="usersnum" value="<?php echo $UserName;?>">
            </div>
            <div class="form-group">                
                <button type="submit" class="btn btn-default" style="padding: 2px 10px;"><?php echo _('Go')?></button>
            </div>
            <?php } ?>
            <p></p>
        </form>
        
        <form enctype="multipart/form-data" name="upload" action="<?php $_SERVER['PHP_SELF'] ?>" method="POST">
            <input type="hidden" name="usersnum" id="usersnum" value="<?php echo $usersnum ?>">
            <div class="form-group">                
                <p class="form-control-static">Alternatively, upload a recording in any supported asterisk format. Note that if you're using .wav, (eg, recorded with Microsoft Recorder) the file <b>must</b> be PCM Encoded, 16 Bits, at 8000Hz:</p>
            </div>
            <div class="form-group">   
                <input type="file" name="ivrfile">
            </div>
            <div class="form-group">                
                <button type="button" class="btn btn-default" style="padding: 2px 10px;" onclick="document.upload.submit(upload);alert('<?php echo addslashes(_("Please wait until the page reloads."))?>');"><?php echo _('Upload')?></button>
            </div>
        </form>
        <?php
        if (isset($_FILES['ivrfile']['tmp_name']) && is_uploaded_file($_FILES['ivrfile']['tmp_name']))
        {
            if (empty($usersnum) || !ctype_digit($usersnum))
            {
                $dest = "unnumbered-";
            }
            else
            {
                $dest = "{$usersnum}-";
            }
            $suffix = preg_replace('/[^0-9a-zA-Z]/','',substr(strrchr($_FILES['ivrfile']['name'], "."), 1));
            $destfilename = $recordings_save_path.$dest."ivrrecording.".$suffix;
            move_uploaded_file($_FILES['ivrfile']['tmp_name'], $destfilename);
            chmod($destfilename, 0666);
            echo '<h4 class="text-success">Successfully uploaded</h4>';
            $rname = rtrim(basename($_FILES['ivrfile']['name'], $suffix), '.');
        }
        ?>
        <form name="prompt" role="form">
            <input type="hidden" name="usersnum" id="usersnum" value="<?php echo $usersnum ?>">
            <input type="hidden" name="suffix" id="suffix" value="<?php echo $suffix;?>">
            <?php
            if (!empty($usersnum))
            {
                ?>
                <fieldset><legend>Step 2: Verify</legend></fieldset>
                <div class="form-group">                
                    <p class="form-control-static">After recording or uploading, dial <?php echo $FeaturedCodeArray['fc_check'];?> to listen to your recording. </p>
                </div>
                <div class="form-group">                
                    <p class="form-control-static">If you wish to re-record your message, dial <?php echo $FeaturedCodeArray['fc_save'];?></p>
                </div>
                <fieldset><legend>Step 3: Name</legend></fieldset>
                <?php
            }
            else
            {
                ?>
                <fieldset><legend>Step 2: Name</legend></fieldset>
                <?php
            }
            ?>
            <div class="form-group">
                <label for="rname" class="col-md-3 control-label">Name this Recording</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control input-sm" name="rname" id="rname" value="<?php echo $rname; ?>">
                    <p></p>
                </div>
            </div>
                
            <div class="form-group">                
                <p class="form-control-static">Click "SAVE" when you are satisfied with your recording</p>
            </div>
                
            <div class="form-group">                
                <button type="submit" id="save_button" class="btn btn-default" ><?php echo _('Save')?></button>
            </div>
                
        </form>
        
        
	</div>
</div>
