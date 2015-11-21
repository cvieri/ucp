<div class="col-md-10 col-md-offset-2">
    <div class="alert" role="alert" style="display:none"></div>
    <form role="form" id="<?php echo ($add) ? 'add' : 'edit'?>Ivr">
        <?php /*?>
        <div class="ivr-container">
            <h3>IVR General Options</h3>
            <div class="form-group">
                <label for="name" class="help"><?php echo _('IVR Name')?>&nbsp;<i class="fa fa-question-circle"></i></label>
                <input type="text" class="form-control" id="name" placeholder="IVR Name" value="<?php echo $ivr['name']?>">
                <span data-for="name" class="help-block help-hidden">Name of this IVR.</span>
            </div>
            <div class="form-group">
                <label for="description" class="help"><?php echo _('IVR Description')?>&nbsp;<i class="fa fa-question-circle"></i></label>
                <input type="text" class="form-control" id="description" placeholder="IVR Description" value="<?php echo $ivr['description']?>">
                <span data-for="description" class="help-block help-hidden">Description of this ivr.</span>
            </div>
        </div>
        <?php */?>
        <div class="ivr-container">
            <h3>IVR Options (DTMF)</h3>
            <div class="form-group">
                <label for="announcement" class="help"><?php echo _('Announcement')?>&nbsp;<i class="fa fa-question-circle"></i></label>
                <select class="form-control" id="announcement" name="announcement">
                    <option value="">None</option>
                    <?php
                    if(count($allRecordings) > 0)
                    {
                        foreach ($allRecordings as $Recordingvalue) 
                        {
                            $RecordingSelected = ($Recordingvalue['id']==$ivr['announcement'])?'selected':'';
                            echo '<option value="'.$Recordingvalue['id'].'" '.$RecordingSelected.' >'.$Recordingvalue['displayname'].'</option>';
                        }
                    }
                    ?>
                </select>	
                <span data-for="announcement" class="help-block help-hidden">Greeting to be played on entry to the Ivr.</span>
            </div>
            <?php /*?>
            <div class="form-group">
                <label for="directdial" class="help"><?php echo _('Direct Dial')?>&nbsp;<i class="fa fa-question-circle"></i></label>
                <select class="form-control" id="directdial" name="directdial">
                    <option value="">Disabled</option>
                    <option value="ext-local" <?php echo ($ivr['directdial']=='ext-local')?'selected':'';?>>Extensions</option>
                </select>
                <span data-for="directdial" class="help-block help-hidden">Provides options for callers to direct dial an extension. Direct dialing can be:
                    <ul>
                        <li>Tied to a Directory allowing all entries in that directory to be dialed directly, as they appear in the directory</li>
                        <li>Completely disabled</li>
                        <li>Enabled for all extensions on a system</li>
                    </ul>
                </span>
            </div>
            <?php */ ?>
            <input type="hidden" name="directdial" id="directdial" value="">
            <div class="form-group">
                <label for="timeout_time" class="help"><?php echo _('Timeout')?>&nbsp;<i class="fa fa-question-circle"></i></label>
                <input type="number" class="form-control" id="timeout_time" value="<?php echo (!empty($ivr['timeout_time']))?$ivr['timeout_time']:'10';?>">
                <span data-for="timeout_time" class="help-block help-hidden">Amount of time to be considered a timeout</span>
            </div>
            <?php 
            $InvalidLoops = ($add)?'3':$ivr['invalid_loops'];
            $DisableSelected = ($InvalidLoops=='disabled')?'selected':'';
            ?>
            <div class="form-group">
                <label for="invalid_loops" class="help"><?php echo _('Invalid Retries')?>&nbsp;<i class="fa fa-question-circle"></i></label>
                <select class="form-control" id="invalid_loops" name="invalid_loops">
                    <option value="disabled" <?php echo $DisableSelected;?> >Disabled</option>
                    <?php
                    for($i=0; $i <11; $i++)
                    {
                        $InvalidLoopsSelected = ($InvalidLoops==$i && $DisableSelected=='')?'selected':'';
                        echo '<option value="'.$i.'" '.$InvalidLoopsSelected.' >'.$i.'</option>';
                    }
                    ?>
                </select>
                <span data-for="invalid_loops" class="help-block help-hidden">Number of times to retry when receiving an invalid/unmatched response from the caller</span>
            </div>
            <?php $InvalidRetryRecording = ($add)?'default':$ivr['invalid_retry_recording'];?>
            <div class="form-group">
                <label for="invalid_retry_recording" class="help"><?php echo _('Invalid Retry Recording')?>&nbsp;<i class="fa fa-question-circle"></i></label>
                <select class="form-control" id="invalid_retry_recording" name="invalid_retry_recording">
                    <option value="">None</option>
                    <?php                    
                    if(count($allRecordings) > 0)
                    {
                        foreach ($allRecordings as $Recordingvalue) 
                        {
                            $RecordingSelected = ($Recordingvalue['id']==$InvalidRetryRecording)?'selected':'';
                            echo '<option value="'.$Recordingvalue['id'].'" '.$RecordingSelected.' >'.$Recordingvalue['displayname'].'</option>';
                        }
                    }
                    ?>
                    <option value="default" <?php echo ($InvalidRetryRecording=='default')?'selected':'';?>>Default</option>
                </select>
                <span data-for="invalid_retry_recording" class="help-block help-hidden">Prompt to be played when an invalid/unmatched response is received, before prompting the caller to try again</span>
            </div>
            <div class="form-group">
                <label style="padding: 0px;" for="invalid_append_announce" class="col-md-4 help"><?php echo _('Append Announcement on Invalid')?>&nbsp;<i class="fa fa-question-circle"></i></label>
                <div class="col-md-8">
                    <input type="checkbox" name="invalid_append_announce" id="invalid_append_announce" value="on" <?php echo ($ivr['invalid_append_announce']=='1')?'checked':'';?> >
                    <div class="clearfix"></div>
                    <span data-for="invalid_append_announce" class="help-block help-hidden">After playing the Invalid Retry Recording the system will replay the main IVR Announcement</span>
                </div>
                <div class="clearfix"></div>
                <?php /*?>
                <label for="invalid_append_announce" class="help"><?php echo _('Append Announcement on Invalid')?>&nbsp;<i class="fa fa-question-circle"></i></label>
                <input type="checkbox" name="invalid_append_announce" id="invalid_append_announce" value="on" <?php echo ($ivr['invalid_append_announce']=='1')?'checked':'';?> >
                <div class="clearfix"></div>
                <span data-for="invalid_append_announce" class="help-block help-hidden">After playing the Invalid Retry Recording the system will replay the main IVR Announcement</span>
                <?php */?>
            </div>
                    
            <div class="form-group">
                <label style="padding: 0px;" for="invalid_ivr_ret" class="col-md-4 help"><?php echo _('Return on Invalid')?>&nbsp;<i class="fa fa-question-circle"></i></label>
                <div class="col-md-8">
                    <input type="checkbox" name="invalid_ivr_ret" id="invalid_ivr_ret" value="on" <?php echo ($ivr['invalid_ivr_ret']=='1')?'checked':'';?> >
                    <div class="clearfix"></div>
                    <span data-for="invalid_ivr_ret" class="help-block help-hidden">Check this box to have this option return to a parent IVR if it was called from a parent IVR. If not, it will go to the chosen destination.<br>The return path will be to any IVR that was in the call path prior to this IVR which could lead to strange results if there was an IVR called in the call path but not immediately before this</span>
                </div>
                <div class="clearfix"></div>
                <?php /*?>
                <label for="invalid_ivr_ret" class="help"><?php echo _('Return on Invalid')?>&nbsp;<i class="fa fa-question-circle"></i></label>
                <input type="checkbox" name="invalid_ivr_ret" id="invalid_ivr_ret" value="on" <?php echo ($ivr['invalid_ivr_ret']=='1')?'checked':'';?> >
                <div class="clearfix"></div>
                <span data-for="invalid_ivr_ret" class="help-block help-hidden">Check this box to have this option return to a parent IVR if it was called from a parent IVR. If not, it will go to the chosen destination.<br>The return path will be to any IVR that was in the call path prior to this IVR which could lead to strange results if there was an IVR called in the call path but not immediately before this</span>
                <?php */?>
            </div>
            
            <?php $InvalidRecording = ($add)?'default':$ivr['invalid_recording'];?>
            <div class="form-group">
                <label for="invalid_recording" class="help"><?php echo _('Invalid Recording')?>&nbsp;<i class="fa fa-question-circle"></i></label>
                <select class="form-control" id="invalid_recording" name="invalid_recording">
                    <option value="">None</option>
                    <?php                    
                    if(count($allRecordings) > 0)
                    {
                        foreach ($allRecordings as $Recordingvalue) 
                        {
                            $RecordingSelected = ($Recordingvalue['id']==$InvalidRecording)?'selected':'';
                            echo '<option value="'.$Recordingvalue['id'].'" '.$RecordingSelected.' >'.$Recordingvalue['displayname'].'</option>';
                        }
                    }
                    ?>
                    <option value="default" <?php echo ($InvalidRecording=='default')?'selected':'';?>>Default</option>
                </select>	
                <span data-for="invalid_recording" class="help-block help-hidden">Prompt to be played before sending the caller to an alternate destination due to the caller pressing 0 or receiving the maximum amount of invalid/unmatched responses (as determined by Invalid Retries)</span>
            </div>
            
            <div class="form-group">
                <label for="gotoinvalid" class="help"><?php echo _('Invalid Destination')?>&nbsp;<i class="fa fa-question-circle"></i></label>
                <?php echo $InvalidDestinationArray['destinationarray'];?> 
                <span data-for="gotoinvalid" class="help-block help-hidden">Destination to send the call to after Invalid Recording is played.</span>
            </div>
            <div class="form-group" id="invalid_destination_change">
                <?php echo $InvalidDestinationArray['destinationdataarray'];?>
            </div>
            
            <?php 
            $TimeOutLoops = ($add)?'3':$ivr['timeout_loops'];
            $TimeoutDisableSel = ($TimeOutLoops=='disabled')?'selected':'';
            ?>
            <div class="form-group">
                <label for="timeout_loops" class="help"><?php echo _('Timeout Retries')?>&nbsp;<i class="fa fa-question-circle"></i></label>
                <select class="form-control" id="timeout_loops" name="timeout_loops">
                    <option value="disabled" <?php echo $TimeoutDisableSel;?> >Disabled</option>
                    <?php
                    for($i=0; $i <11; $i++)
                    {
                        $TimeOutLoopsSelected = ($TimeOutLoops==$i && $TimeoutDisableSel=='')?'selected':'';
                        echo '<option value="'.$i.'" '.$TimeOutLoopsSelected.' >'.$i.'</option>';
                    }
                    ?>
                </select>
                <span data-for="timeout_loops" class="help-block help-hidden">Number of times to retry when no DTMF is heard and the IVR choice times out.</span>
            </div>
            
            <?php $TimeoutRetryRecording = ($add)?'default':$ivr['timeout_retry_recording'];?>
            <div class="form-group">
                <label for="timeout_retry_recording" class="help"><?php echo _('Timeout Retry Recording')?>&nbsp;<i class="fa fa-question-circle"></i></label>
                <select class="form-control" id="timeout_retry_recording" name="timeout_retry_recording">
                    <option value="">None</option>
                    <?php                    
                    if(count($allRecordings) > 0)
                    {
                        foreach ($allRecordings as $Recordingvalue) 
                        {
                            $RecordingSelected = ($Recordingvalue['id']==$TimeoutRetryRecording)?'selected':'';
                            echo '<option value="'.$Recordingvalue['id'].'" '.$RecordingSelected.' >'.$Recordingvalue['displayname'].'</option>';
                        }
                    }
                    ?>
                    <option value="default" <?php echo ($TimeoutRetryRecording=='default')?'selected':'';?>>Default</option>
                </select>
                <span data-for="timeout_retry_recording" class="help-block help-hidden">Prompt to be played when a timeout occurs, before prompting the caller to try again</span>
            </div>
            <div class="form-group">
                <label style="padding: 0px;" for="timeout_append_announce" class="col-md-4 help"><?php echo _('Append Announcement on Timeout')?>&nbsp;<i class="fa fa-question-circle"></i></label>
                <div class="col-md-8">
                    <input type="checkbox" name="timeout_append_announce" id="timeout_append_announce" value="on" <?php echo ($ivr['timeout_append_announce']=='1')?'checked':'';?> >
                    <div class="clearfix"></div>
                    <span data-for="timeout_append_announce" class="help-block help-hidden">After playing the Timeout Retry Recording the system will replay the main IVR Announcement</span>
                </div>
                <div class="clearfix"></div>
                <?php /*?>
                <label for="timeout_append_announce" class="help"><?php echo _('Append Announcement on Timeout')?>&nbsp;<i class="fa fa-question-circle"></i></label>
                <input type="checkbox" name="timeout_append_announce" id="timeout_append_announce" value="on" <?php echo ($ivr['timeout_append_announce']=='1')?'checked':'';?> >
                <div class="clearfix"></div>
                <span data-for="timeout_append_announce" class="help-block help-hidden">After playing the Timeout Retry Recording the system will replay the main IVR Announcement</span>
                <?php */ ?>
            </div>

            <div class="form-group">
                <label for="timeout_ivr_ret" style="padding: 0px;" class="col-md-4 help"><?php echo _('Return on Timeout')?>&nbsp;<i class="fa fa-question-circle"></i></label>
                <div class="col-md-8">
                    <input type="checkbox" name="timeout_ivr_ret" id="timeout_ivr_ret" value="on" <?php echo ($ivr['timeout_ivr_ret']=='1')?'checked':'';?> >
                    <div class="clearfix"></div>
                    <span data-for="timeout_ivr_ret" class="help-block help-hidden">Check this box to have this option return to a parent IVR if it was called from a parent IVR. If not, it will go to the chosen destination.<br>The return path will be to any IVR that was in the call path prior to this IVR which could lead to strange results if there was an IVR called in the call path but not immediately before this</span>
                </div>
                <div class="clearfix"></div>
                <?php /*?>
                <label for="timeout_ivr_ret" class="help"><?php echo _('Return on Timeout')?>&nbsp;<i class="fa fa-question-circle"></i></label>
                <input type="checkbox" name="timeout_ivr_ret" id="timeout_ivr_ret" value="on" <?php echo ($ivr['timeout_ivr_ret']=='1')?'checked':'';?> >
                <div class="clearfix"></div>
                <span data-for="timeout_ivr_ret" class="help-block help-hidden">Check this box to have this option return to a parent IVR if it was called from a parent IVR. If not, it will go to the chosen destination.<br>The return path will be to any IVR that was in the call path prior to this IVR which could lead to strange results if there was an IVR called in the call path but not immediately before this</span>
                <?php */ ?>
            </div>
                  
            <?php $TimeoutRecording = ($add)?'default':$ivr['timeout_recording'];?>
            <div class="form-group">
                <label for="timeout_recording" class="help"><?php echo _('Timeout Recording')?>&nbsp;<i class="fa fa-question-circle"></i></label>
                <select class="form-control" id="timeout_recording" name="timeout_recording">
                    <option value="">None</option>
                    <?php                    
                    if(count($allRecordings) > 0)
                    {
                        foreach ($allRecordings as $Recordingvalue) 
                        {
                            $RecordingSelected = ($Recordingvalue['id']==$TimeoutRecording)?'selected':'';
                            echo '<option value="'.$Recordingvalue['id'].'" '.$RecordingSelected.' >'.$Recordingvalue['displayname'].'</option>';
                        }
                    }
                    ?>
                    <option value="default" <?php echo ($TimeoutRecording=='default')?'selected':'';?>>Default</option>
                </select>
                <span data-for="timeout_recording" class="help-block help-hidden">Prompt to be played before sending the caller to an alternate destination due to the caller pressing 0 or receiving the maximum amount of invalid/unmatched responses (as determined by Invalid Retries)</span>
            </div>
                   
            <div class="form-group">
                <label for="gototimeout" class="help"><?php echo _('Timeout Destination')?>&nbsp;<i class="fa fa-question-circle"></i></label>
                <?php echo $TimeoutDestinationArray['destinationarray'];?>  
                <span data-for="gototimeout" class="help-block help-hidden">Destination to send the call to after Timeout Recording is played.</span>                
            </div>
            <div class="form-group" id="timeout_destination_change">
                <?php echo $TimeoutDestinationArray['destinationdataarray'];?>
            </div>
                    
            <div class="form-group">                
                <label for="retvm" style="padding: 0px;" class="col-md-4 help"><?php echo _('Return to IVR after VM')?>&nbsp;<i class="fa fa-question-circle"></i></label>
                <div class="col-md-8">
                    <input type="checkbox" value="on" name="retvm" id="retvm" <?php echo ($ivr['retvm']=='on')?'checked':'';?> >
                    <div class="clearfix"></div>
                    <span data-for="retvm" class="help-block help-hidden">If checked, upon exiting voicemail a caller will be returned to this IVR if they got a users voicemail</span>
                </div>
                <div class="clearfix"></div>
                <?php /*?>
                <label for="retvm" class="help"><?php echo _('Return to IVR after VM')?>&nbsp;<i class="fa fa-question-circle"></i></label>
                <input type="checkbox" value="on" name="retvm" id="retvm" <?php echo ($ivr['retvm']=='on')?'checked':'';?> >
                <div class="clearfix"></div>
                <span data-for="retvm" class="help-block help-hidden">If checked, upon exiting voicemail a caller will be returned to this IVR if they got a users voicemail</span>
                <?php */ ?>
            </div>
            <?php /*?>
            <div class="form-group">               
                <label for="useivr" style="padding: 0px;" class="col-md-4 help"><?php echo _('Use IVR')?></label>
                <div class="col-md-8">
                    <input type="checkbox" name="useivr" id="useivr" <?php echo ($DIDIvr['ivr']=='1')?'checked':'';?> >
                    <div class="clearfix"></div>
                    <!--<span data-for="retvm" class="help-block help-hidden">If checked, upon exiting voicemail a caller will be returned to this IVR if they got a users voicemail</span>-->
                </div>
                <div class="clearfix"></div>
            </div>
            <?php */ ?>
            
        </div>
        
        <div class="ivr-container">
            <h3>IVR Entries</h3>
            <div class="form-group table-responsive">
                <table id="ivr_entries" class="table">
                    <tr>
                        <th>Ext</th>
                        <th>Destination</th>
                        <th>Return</th>
                        <th>Delete</th>
                        
                    </tr>
                    <?php
                    if($add || count($IVREntries)==0)
                    {
                        ?>
                        <tr>
                            <td width="10%">
                                <input style="width: 150px;" type="text" name="entries[ext][]" value="" class="special entryext">
                            </td>
                            <td>
                                <?php 
                                echo $IVREntriesArray['destinationarray'];
                                echo $IVREntriesArray['destinationdataarray'];

                                ?>
                                <input type="hidden" value="" name="entries[dest][]" class="special entrydest">
                            </td>
                            <td>
                                <input type="checkbox" name="entries[ivr_ret][]" class="special entryivrret">
                            </td>
                            <td>
                                <a href="javascript:;" class="delete_entrie"><i class="fa fa-trash-o"></i></a>
                            </td>
                            
                        </tr>
                        <?php
                    }
                    else
                    {
                        foreach ($IVREntriesArray as $Enkey => $EnValue) 
                        {
                            ?>
                            <tr>
                                <td width="10%">
                                    <input type="text" name="entries[ext][]" value="<?php echo $EnValue['selection'];?>" class="special entryext">
                                </td>
                                <td>
                                    <?php 
                                    echo $EnValue['dest']['destinationarray'].$EnValue['dest']['destinationdataarray'];
                                    //echo $EnValue['dest']['destinationdataarray'];
                                    ?>
                                    <input type="hidden" value="" name="entries[dest][]" class="special entrydest">
                                </td>
                                <td>
                                    <input type="checkbox" <?php echo ($EnValue['ivr_ret']=='1')?'checked':'';?> name="entries[ivr_ret][]" class="special entryivrret">
                                </td>
                                <td>
                                    <a href="javascript:;" class="delete_entrie"><i class="fa fa-trash-o"></i></a>
                                </td>
                            </tr>
                            <?php
                        }
                    }
                    
                    ?>
                    
                </table>
                
            </div>
            <div class="form-group">	
                <a id="add_entrie" href="javascript:;" class="btn btn-default btn-xs add-additional"><i class="fa fa-plus fa-fw"></i>Add New</a>
            </div>
        </div>
        
        <div class="ivr-container">
            
            <input type="hidden" value="gotoinvalid" id="invalid_destination" name="invalid_destination">
            <input type="hidden" value="gotoinvalid" id="timeout_destination" name="timeout_destination">
            <input type="hidden" id="mode" name="mode" value="<?php echo ($add) ? 'add' : 'edit'?>">                        
            <?php if($add) { ?>
                <button id="addivr" class="btn btn-default"><?php echo _('Add IVR')?></button>
            <?php } if($edit) {?>
                <input type="hidden" id="id" name="id" value="<?php echo $ivr['id']?>">
                <button id="editivr" class="btn btn-default"><?php echo _('Edit IVR')?></button>
                <button id="deleteivr" class="btn btn-default"><i class="fa fa-trash-o"></i> <?php echo _('Delete IVR')?></button>
            <?php } ?>
            <?php /*?>
            <input type="hidden" id="id" name="id" value="<?php echo $ivr['id']?>">
            <button id="deleteivr" class="btn btn-default"><i class="fa fa-trash-o"></i> <?php echo _('Delete IVR')?></button>
            <?php */?>
        </div>
        
    </form>
</div>