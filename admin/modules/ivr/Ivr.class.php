<?php
// vim: set ai ts=4 sw=4 ft=php:
//	License for all code of this FreePBX module can be found in the license file inside the module directory
//	Copyright 2014 Schmooze Com Inc.
//
namespace FreePBX\modules;
class Ivr extends \FreePBX_Helpers implements \BMO {
    private $message = '';
    private $lookupCache = array();
    private $contactsCache = array();

    public function __construct($freepbx = null) {
            $this->db = $freepbx->Database;
            $this->freepbx = $freepbx;
    }

    public function install() {

    }
    public function uninstall() {

    }
    public function backup(){

    }
    public function restore($backup){

    }


    /**
     * Get all IVR by User ID
     * @param {int} $userid The user ID
     */
    public function getIvrByUserID($userid) {
            $fields = array(
            'i.id','i.id as ivrid','i.name','i.description',
            );                

            $sql = "SELECT " . implode(', ', $fields) . " FROM ivr_details as i
            LEFT JOIN ivr_user_relation_mst as iu ON (i.id = iu.fk_ivr_id) WHERE iu.fk_user_id = :userid ORDER BY i.id";                
            $sth = $this->db->prepare($sql);
            $sth->execute(array(':userid' => $userid));                
            $e = $sth->fetchAll(\PDO::FETCH_ASSOC | \PDO::FETCH_UNIQUE);
            $entries = $e;                
            return $entries;
    }

    /**
     * Add Entry to Group
     * @param {int} $userid The user ID
     * @param {array} $entry   Array of Entry information
     */
    public function addIvrByUserID($userdata, $entry)
    {
        $userid = $userdata['id'];
        $UserName = $userdata['username'];
        /*$validation = true;
        if(empty($entry['name']))
        {
            $validation = false;
            $Message = 'IVR Name missing';
        }
        if(!$validation)
        {
            return array("status" => false, "type" => "danger", "message" => _($Message));
        }*/

        $sql = "INSERT INTO ivr_details (`id`, `name`, `description`, `announcement`, `directdial`, `invalid_loops`, `invalid_retry_recording`, `invalid_destination`, `invalid_recording`, `retvm`, `timeout_time`, `timeout_recording`, `timeout_retry_recording`, `timeout_destination`, `timeout_loops`, `timeout_append_announce`, `invalid_append_announce`, `timeout_ivr_ret`, `invalid_ivr_ret`) VALUES (:id, :name, :description, :announcement, :directdial, :invalid_loops, :invalid_retry_recording, :invalid_destination, :invalid_recording, :retvm, :timeout_time, :timeout_recording, :timeout_retry_recording, :timeout_destination, :timeout_loops, :timeout_append_announce, :invalid_append_announce, :timeout_ivr_ret, :invalid_ivr_ret)";
        $sth = $this->db->prepare($sql);
        $sth->execute(array(
            ':id' => $UserName,
            ':name' => $UserName,
            ':description' => $UserName,
            ':announcement' => $entry['announcement'],
            ':directdial' => $entry['directdial'],
            ':invalid_loops' => $entry['invalid_loops'],
            ':invalid_retry_recording' => $entry['invalid_retry_recording'],
            ':invalid_destination' => $entry['invalid_destination'],
            ':invalid_recording' => $entry['invalid_recording'],
            ':retvm' => $entry['retvm'],
            ':timeout_time' => $entry['timeout_time'],
            ':timeout_recording' => $entry['timeout_recording'],
            ':timeout_retry_recording' => $entry['timeout_retry_recording'],
            ':timeout_destination' => $entry['timeout_destination'],
            ':timeout_loops' => $entry['timeout_loops'],
            ':timeout_append_announce' => empty($entry['timeout_append_announce'])?'0':'1',
            ':invalid_append_announce' => empty($entry['invalid_append_announce'])?'0':'1',
            ':timeout_ivr_ret' => empty($entry['timeout_ivr_ret'])?'0':'1',
            ':invalid_ivr_ret' => empty($entry['invalid_ivr_ret'])?'0':'1',
        ));
        $id = $this->db->lastInsertId();
        $this->addIvrUserByEntryID($id, $userid);
        $this->addIvrEntries($id,$entry);
        //$this->UpdateDIDIvr($entry,$userdata);
        
        return array("status" => true, "type" => "success", "message" => _("IVR successfully added"), "id" => $id);
    }

    /**
     * Add IVR User by Entry ID
     * @param {int} $entryid The entry ID
     * @param {array} $userid The user ID
     */
    public function addIvrUserByEntryID($entryid, $userid) 
    {
        if(!empty($entryid) && !empty($userid))
        {
            $sql = "INSERT INTO ivr_user_relation_mst (fk_user_id, fk_ivr_id) VALUES (:userid, :entryid)";
            $sth = $this->db->prepare($sql);
            $sth->execute(array(
            ':userid' => $userid,
            ':entryid' => $entryid,
            ));
        }
    }

    public function addIvrEntries($entryid,$dataarray) 
    {
        $LoopCount = count($dataarray['entryext']);
        $sql = "DELETE FROM ivr_entries WHERE ivr_id = :ivr_id ";
        $sth = $this->db->prepare($sql);
        $sth->execute(array(
            ':ivr_id' => $entryid,
        ));
        
        $sql = "INSERT INTO  ivr_entries (ivr_id, selection, dest, ivr_ret) VALUES (:ivr_id, :selection, :dest, :ivr_ret)";
        $sth = $this->db->prepare($sql);
        
        for($i=0;$i<$LoopCount;$i++)
        {
            $sth->execute(array(
                ':ivr_id' => $entryid,
                ':selection' => $dataarray['entryext'][$i],
                ':dest' => $dataarray['entrydest'][$i],
                ':ivr_ret' => $dataarray['entryivrret'][$i],
            ));
        }
        
    }

    /**
     * Check IVR User ID
     *
     * This gets ivr user id information
     *
     * @param string $id The ID of IVR
     * @param string $userid The ID of User
     * @return array
     */
    public function checkIvrByUser($userid,$id) {
            $sql = "SELECT * FROM ivr_user_relation_mst WHERE `fk_ivr_id` = :ivrid AND `fk_user_id` = :userid";
            $sth = $this->db->prepare($sql);
            $sth->execute(array(
            ':ivrid' => $id,
            ':userid' => $userid,
            ));
            $ivruser = $sth->fetch(\PDO::FETCH_ASSOC);
            return $ivruser;
    }


    /**
     * Get all information about an IVR
     * @param {int} $id The IVR ID
     */
    public function getIvrByID($id)
    {
        $sql = "SELECT * FROM ivr_details WHERE id = :id";
        $sth = $this->db->prepare($sql);
        $sth->execute(array(':id' => $id));
        $ivrdetail = $sth->fetch(\PDO::FETCH_ASSOC);
        return $ivrdetail;
    }
    
    public function getIvrEntries($id)
    {
        $sql = "SELECT * FROM ivr_entries WHERE ivr_id = :ivr_id";
        $sth = $this->db->prepare($sql);
        $sth->execute(array(':ivr_id' => $id));
        //$ivrdetail = $sth->fetch(\PDO::FETCH_ASSOC);
        $ivrdetail = $sth->fetchAll(\PDO::FETCH_ASSOC);
        return $ivrdetail;
    }

    public function deleteEntryByID($id) {
        
        $ret = $this->deleteivruserByEntryID($id);
        if (!$ret['status']) {
                return $ret;
        }

        $ret = $this->deleteivrentriesByEntryID($id);
        if (!$ret['status']) {
                return $ret;
        }
        
        $sql = "DELETE FROM ivr_details WHERE `id` = :id";
        $sth = $this->db->prepare($sql);
        $sth->execute(array(':id' => $id));

        return array("status" => true, "type" => "success", "message" => _("IVR entry successfully deleted"));
    }
       
    function deleteivruserByEntryID($id)
    {
        $sql = "DELETE FROM ivr_user_relation_mst WHERE `fk_ivr_id` = :id";
        $sth = $this->db->prepare($sql);
        $sth->execute(array(':id' => $id));
        return array("status" => true, "type" => "success", "message" => _("IVR entry successfully deleted"));
        
    }
    
    function deleteivrentriesByEntryID($id)
    {
        $sql = "DELETE FROM ivr_entries WHERE `ivr_id` = :id";
        $sth = $this->db->prepare($sql);
        $sth->execute(array(':id' => $id));
        return array("status" => true, "type" => "success", "message" => _("IVR entry successfully deleted"));
        
    }
    
    /**
     * Update IVR Information
     * @param {int} $id The IVR ID
     * @param {array} $entry   Array of Ivr information
     */
    public function updateIvr($id,$entry,$userdata)
    {        
        $sql = "UPDATE ivr_details SET `announcement` = :announcement, `directdial` = :directdial, `invalid_loops` = :invalid_loops, `invalid_retry_recording` = :invalid_retry_recording, `invalid_destination` = :invalid_destination, `invalid_recording` = :invalid_recording, `retvm` = :retvm, `timeout_time` = :timeout_time, `timeout_recording` = :timeout_recording, `timeout_retry_recording` = :timeout_retry_recording, `timeout_destination` = :timeout_destination, `timeout_loops` = :timeout_loops, `timeout_append_announce` = :timeout_append_announce, `invalid_append_announce` = :invalid_append_announce, `timeout_ivr_ret` = :timeout_ivr_ret, `invalid_ivr_ret` = :invalid_ivr_ret WHERE `id` = :id";//`name` = :name, `description` = :description, 
        $sth = $this->db->prepare($sql);
        $sth->execute(array(
        //':name' => $entry['name'],
        //':description' => $entry['description'],
        ':announcement' => $entry['announcement'],
        ':directdial' => $entry['directdial'],
        ':invalid_loops' => $entry['invalid_loops'],
        ':invalid_retry_recording' => $entry['invalid_retry_recording'],
        ':invalid_destination' => $entry['invalid_destination'],
        ':invalid_recording' => $entry['invalid_recording'],
        ':retvm' => $entry['retvm'],
        ':timeout_time' => $entry['timeout_time'],
        ':timeout_recording' => $entry['timeout_recording'],
        ':timeout_retry_recording' => $entry['timeout_retry_recording'],
        ':timeout_destination' => $entry['timeout_destination'],
        ':timeout_loops' => $entry['timeout_loops'],
        ':timeout_append_announce' => empty($entry['timeout_append_announce'])?'0':'1',
        ':invalid_append_announce' => empty($entry['invalid_append_announce'])?'0':'1',
        ':timeout_ivr_ret' => empty($entry['timeout_ivr_ret'])?'0':'1',
        ':invalid_ivr_ret' => empty($entry['invalid_ivr_ret'])?'0':'1',
        ':id' => $id,
        ));
        $this->addIvrEntries($id,$entry);
        //$this->UpdateDIDIvr($entry,$userdata);

        return array("status" => true, "type" => "success", "message" => _("IVR successfully updated"), "id" => $id);
    }
    
    function UpdateDIDIvr($entry,$userdata)
    {
        $UserName = $userdata['username'];
      
        $sql = "SELECT * FROM DID where username = '".$UserName."' ";
        $sth = $this->db->prepare($sql);
        $sth->execute();
        $results = $sth->fetchAll(\PDO::FETCH_ASSOC);
        
        if(count($results) == 0)
        {
            $sql = "INSERT INTO  DID (did, username, divertion,ivr) VALUES ('','".$UserName."',NULL,'".$entry['useivr']."')";
            $sth = $this->db->prepare($sql);
            $sth->execute();
        }
        else
        {
            $sql = "UPDATE DID SET `ivr` = :useivr WHERE `username` = :username";
            $sth = $this->db->prepare($sql);
            $sth->execute(array(
            ':useivr' => $entry['useivr'],
            ':username' => $UserName,
            ));
        }
      
//        if(count($results) == 1)
//        {
//            $sql = "UPDATE DID SET `ivr` = :useivr WHERE `username` = :username";
//            $sth = $this->db->prepare($sql);
//            $sth->execute(array(
//            ':useivr' => $entry['useivr'],
//            ':username' => $UserName,
//            ));
//        }
    }
    
    public function getDIDIvr($username)
    {
        $sql = "SELECT * FROM DID WHERE username = :username";
        $sth = $this->db->prepare($sql);
        $sth->execute(array(':username' => $username));
        $ivrdetail = $sth->fetch(\PDO::FETCH_ASSOC);
        return $ivrdetail;
    }

    function getAllRecordings($UserID) 
    {
        //$sql = "SELECT * FROM recordings where displayname <> '__invalid' ORDER BY displayname";
        $sql = "SELECT r.* FROM recordings as r
            LEFT JOIN recording_user as ru ON (r.id = ru.fk_recording_id) WHERE ru.fk_user_id = '".$UserID."' ORDER BY r.displayname"; 
        $sth = $this->db->prepare($sql);
        $sth->execute();
        $results = $sth->fetchAll(\PDO::FETCH_ASSOC);
        return $results;
    }
    
    function getDestinationArray($BoxID = '', $goto = '', $UserData = '')
    {
        //$goto = 'app-announcement-1,s,1';
        $UserID = $UserData['id'];
        $UserName = $UserData['username'];
        $DataArray = array();
        $DataArray = array(
            "Announcements" => "Announcements",
            "Extensions" => "Extensions",
            "IVR" => "IVR",
            "Terminate_Call" => "Terminate Call",
            "Voicemail" => "Voicemail"
        );
        
        $MainStyle = '';
        if($BoxID=='DESTID')
        {
            $MainStyle = 'width:165px;margin-right:10px;float:left;';
        }
        $DestinationCmbStr = '';
        $DestinationCmbStr .= '<select data-id="'.$BoxID.'" class="form-control destdropdown" id="goto'.$BoxID.'" name="goto'.$BoxID.'" style="'.$MainStyle.'">';
        $DestinationCmbStr .= '<option style="" value="">== choose one ==</option>';
        foreach ($DataArray as $mod => $value) 
        {
            $funct = strtolower('ucp_'.$mod.'_destinations');
            $destArray = $this->$funct($UserID,$UserName);
            
            foreach($destArray as $dest)
            {
                if($goto==$dest['destination'])
                {
                    $destmod=$mod;
                }
                $drawselect_destinations[$mod][] = $dest;
            }
            
            $CmbSelected = ($destmod==$mod)?'selected':'';
            $DestinationCmbStr .= '<option style="" value="'.$mod.'" '.$CmbSelected.' >'.$value.'</option>';
        }
        $DestinationCmbStr .= '</select>';
        
        
        $SubStyle = '';
        if($BoxID=='DESTID')
        {
            $SubStyle = 'width:165px;';
        }
        
        $BindStr = '';
        foreach($drawselect_destinations as $cat=>$destination)
        {        
            $Style = ($destmod==$cat)?'display:block;':'display:none;';
            $BindStr .= '<select data-id="'.$BoxID.'" class="form-control destdropdown2" style="'.$Style.$SubStyle.'" id="'.$cat.$BoxID.'" name="'.$cat.$BoxID.'">';
            foreach ($destination as $key => $dest)
            {
                $Selected = ($dest['destination']==$goto)?'selected':'';
                $BindStr .= '<option value="'.$dest['destination'].'" '.$Selected.' >'.$dest['description'].'</option>';
            }
            $BindStr .= '</select>';
        }
        
        $ReturnArray['destinationarray'] = $DestinationCmbStr;
        $ReturnArray['destinationdataarray'] = $BindStr;
        
        return $ReturnArray;
        
    }
    
    function ucp_announcements_destinations($UserID,$UserName) 
    {
        //$sql = "SELECT * FROM announcement ORDER BY description ";
        $sql = "SELECT * FROM announcement as a LEFT JOIN announcement_user as au ON (a.announcement_id = au.fk_announcement_id) WHERE au.fk_user_id = '".$UserID."' ORDER BY a.description";
        $sth = $this->db->prepare($sql);
        $sth->execute();
        $results = $sth->fetchAll(\PDO::FETCH_ASSOC);
        
        foreach ($results as $resultvalue)
        {
            $extens[] = array('destination' => 'app-announcement-'.$resultvalue['announcement_id'].',s,1', 'description' => $resultvalue['description']);
        }
        return $extens;
        
        
        /*$BindStr = '';
        $BindStr .= '<select class="form-control" id="Announcements'.$name.'" name="Announcements'.$name.'">';
        foreach ($results as $resultvalue) 
        {
            $BindStr .= '<option value="app-announcement-'.$resultvalue['announcement_id'].',s,1">'.$resultvalue['description'].'</option>';
        }
        $BindStr .= '</select>';
        
        return $BindStr;*/
    }
    
    function ucp_terminate_call_destinations($UserID,$UserName)
    {
        $extens = array();
        $extens[] = array('destination' => 'app-blackhole,hangup,1', 'description' => 'Hangup');
        $extens[] = array('destination' => 'app-blackhole,congestion,1', 'description' => 'Congestion');
        $extens[] = array('destination' => 'app-blackhole,busy,1', 'description' => 'Busy');
        $extens[] = array('destination' => 'app-blackhole,zapateller,1', 'description' => 'Play SIT Tone (Zapateller)');
        $extens[] = array('destination' => 'app-blackhole,musiconhold,1', 'description' => 'Put caller on hold forever');
        $extens[] = array('destination' => 'app-blackhole,ring,1', 'description' => 'Play ringtones to caller until they hangup');
        return $extens;
        
        /*$BindStr = '';
        $BindStr .= '<select class="form-control" id="Terminate_Call'.$name.'" name="Terminate_Call'.$name.'">';
        $BindStr .= '<option value="app-blackhole,hangup,1">Hangup</option>';
        $BindStr .= '<option value="app-blackhole,congestion,1">Congestion</option>';
        $BindStr .= '<option value="app-blackhole,busy,1">Busy</option>';
        $BindStr .= '<option value="app-blackhole,zapateller,1">Play SIT Tone (Zapateller)</option>';
        $BindStr .= '<option value="app-blackhole,musiconhold,1">Put caller on hold forever</option>';
        $BindStr .= '<option value="app-blackhole,ring,1">Play ringtones to caller until they hangup</option>';
        $BindStr .= '</select>';
        
        return $BindStr;*/
    }
    
    function ucp_ivr_destinations($UserID,$UserName)
    {
        $Whereclause = '';
        if(!empty($_REQUEST['id']))
        {
            //$Whereclause = ' where id!= '.$_REQUEST['id'].' ';
        }
        //$sql = "SELECT * FROM ivr_details $Whereclause ORDER BY name ";
        $sql = "SELECT * FROM ivr_details as i LEFT JOIN ivr_user_relation_mst as iu ON (i.id = iu.fk_ivr_id) WHERE iu.fk_user_id = '".$UserID."' ORDER BY i.id";
        $sth = $this->db->prepare($sql);
        $sth->execute();
        $results = $sth->fetchAll(\PDO::FETCH_ASSOC);
        
        foreach ($results as $resultvalue)
        {
            $extens[] = array('destination' => 'ivr-'.$resultvalue['id'].',s,1', 'description' => $resultvalue['name']);
        }
        return $extens;
    }
    
    function ucp_voicemail_destinations($UserID,$UserName)
    {
        $uservm = $this->ucp_voicemail_getVoicemail();
        $vmcontexts = array_keys($uservm);
        
        //$sql = "SELECT extension,name,voicemail FROM users ORDER BY extension ";
        $sql = "SELECT extension,name,voicemail FROM users where extension = '".$UserName."' ORDER BY extension ";
        $sth = $this->db->prepare($sql);
        $sth->execute();
        $results = $sth->fetchAll(\PDO::FETCH_ASSOC);
        
        foreach ($results as $thisext)
        {
            $extnum = $thisext['extension'];
            foreach ($vmcontexts as $vmcontext) 
            {
                if(isset($uservm[$vmcontext][$extnum]))
                {
                    $vmboxes[$extnum] = true;
                }
            }
        }
        
        foreach($results as $result) 
        {
            if(isset($vmboxes[$result['extension']]))
            {
                if($UserName==$result['extension'])
                {
                    $extens[] = array('destination' => 'ext-local,vmb'.$result['extension'].',1', 'description' => '<'.$result['extension'].'> '.$result['name'].' (busy)');
                    $extens[] = array('destination' => 'ext-local,vmu'.$result['extension'].',1', 'description' => '<'.$result['extension'].'> '.$result['name'].' (unavail)');
                    $extens[] = array('destination' => 'ext-local,vms'.$result['extension'].',1', 'description' => '<'.$result['extension'].'> '.$result['name'].' (no-msg)');
                    $extens[] = array('destination' => 'ext-local,vmi'.$result['extension'].',1', 'description' => '<'.$result['extension'].'> '.$result['name'].' (instructions-only)');
                }
            }
        }
        return $extens;

        /*$extens = array();
        $extens[] = array('destination' => 'app-blackhole,hangup,1', 'description' => 'Hangup');
        $extens[] = array('destination' => 'app-blackhole,congestion,1', 'description' => 'Congestion');
        $extens[] = array('destination' => 'app-blackhole,busy,1', 'description' => 'Busy');
        $extens[] = array('destination' => 'app-blackhole,zapateller,1', 'description' => 'Play SIT Tone (Zapateller)');
        $extens[] = array('destination' => 'app-blackhole,musiconhold,1', 'description' => 'Put caller on hold forever');
        $extens[] = array('destination' => 'app-blackhole,ring,1', 'description' => 'Play ringtones to caller until they hangup');
        return $extens;*/
        
        /*$BindStr = '';
        $BindStr .= '<select class="form-control" id="Terminate_Call'.$name.'" name="Terminate_Call'.$name.'">';
        $BindStr .= '<option value="app-blackhole,hangup,1">Hangup</option>';
        $BindStr .= '<option value="app-blackhole,congestion,1">Congestion</option>';
        $BindStr .= '<option value="app-blackhole,busy,1">Busy</option>';
        $BindStr .= '<option value="app-blackhole,zapateller,1">Play SIT Tone (Zapateller)</option>';
        $BindStr .= '<option value="app-blackhole,musiconhold,1">Put caller on hold forever</option>';
        $BindStr .= '<option value="app-blackhole,ring,1">Play ringtones to caller until they hangup</option>';
        $BindStr .= '</select>';
        
        return $BindStr;*/
    }
    
    function ucp_extensions_destinations($UserID,$UserName)
    {
        //$sql = "SELECT extension,name,voicemail FROM users ORDER BY extension ";
        $sql = "SELECT extension,name,voicemail FROM users where extension = '".$UserName."' ORDER BY extension ";
        $sth = $this->db->prepare($sql);
        $sth->execute();
        $results = $sth->fetchAll(\PDO::FETCH_ASSOC);
        foreach ($results as $resultvalue)
        {
            $extens[] = array('destination' => 'from-did-direct,'.$resultvalue['extension'].',1', 'description' => ' <'.$resultvalue['extension'].'> '.$resultvalue['name']);
        }
        return $extens;
    }
    
    function ucp_voicemail_getVoicemail()
    {
        global $amp_conf;
        $vmconf = null;
	$section = null;                        
        parse_voicemailconf(rtrim($amp_conf["ASTETCDIR"],"/")."/voicemail.conf", $vmconf, $section);
        return $vmconf;
    }
        
    function setuseivrByExtension($ext,$value)
    {
        $sql = "SELECT * FROM DID where username = '".$ext."' ";
        $sth = $this->db->prepare($sql);
        $sth->execute();
        $results = $sth->fetchAll(\PDO::FETCH_ASSOC);
      
        if(count($results) == 0)
        {
            $sql = "INSERT INTO  DID (did, username, divertion,ivr) VALUES ('','".$ext."',NULL,'".$value."')";
            $sth = $this->db->prepare($sql);
            $sth->execute();
        }
        else
        {
            $sql = "UPDATE DID SET `ivr` = :useivr WHERE `username` = :username";
            $sth = $this->db->prepare($sql);
            $sth->execute(array(
            ':useivr' => $value,
            ':username' => $ext,
            ));
        }
    }
    
    public function getuseivrByExtension($ext)
    {
        $sql = "SELECT * FROM DID WHERE username = :username";
        $sth = $this->db->prepare($sql);
        $sth->execute(array(':username' => $ext));
        $ivrdetail = $sth->fetch(\PDO::FETCH_ASSOC);
        return $ivrdetail['ivr'];
    }
    
    public function getDIDByUsername($username) 
    {                        
        $sql = "SELECT * FROM DID as d WHERE d.username = :userid ORDER BY d.id desc";                
        $sth = $this->db->prepare($sql);
        $sth->execute(array(':userid' => $username));      
        $e = $sth->fetchAll(\PDO::FETCH_ASSOC);
        $entries = $e;                
        return $entries;
    }
    public function getDIDByID($id)
    {
        $sql = "SELECT * FROM DID WHERE id = :id";
        $sth = $this->db->prepare($sql);
        $sth->execute(array(':id' => $id));
        $DIDdetail = $sth->fetch(\PDO::FETCH_ASSOC);
        return $DIDdetail;
    }
    public function checkDIDByUser($username,$id)
    {
        $sql = "SELECT * FROM DID WHERE `id` = :id AND `username` = :username";
        $sth = $this->db->prepare($sql);
        $sth->execute(array(
        ':id' => $id,
        ':username' => $username,
        ));
        $ivruser = $sth->fetch(\PDO::FETCH_ASSOC);
        return $ivruser;
    }
    public function addDIDByUserID($username, $entry)
    {
        $sql = "INSERT INTO DID (`did`,`username`, `ivr`, `forwardnumber`) VALUES (:did, :username, :ivr, :forwardnumber)";
        $sth = $this->db->prepare($sql);
        $sth->execute(array(
            ':did' => $entry['did'],
            ':username' => $username,
            ':ivr' => $entry['ivr'],
            ':forwardnumber' => $entry['forwardnumber'],
        ));
        $id = $this->db->lastInsertId();
        return array("status" => true, "type" => "success", "message" => _("DID successfully added"), "id" => $id);
    }
    public function updateDID($id,$entry)
    {        
        $sql = "UPDATE DID SET `did` = :did, `ivr` = :ivr, `forwardnumber` = :forwardnumber WHERE `id` = :id";//`name` = :name, `description` = :description, 
        $sth = $this->db->prepare($sql);
        $sth->execute(array(
            ':did' => $entry['did'],
            ':ivr' => $entry['ivr'],
            ':forwardnumber' => $entry['forwardnumber'],
            ':id' => $id,
        ));
        return array("status" => true, "type" => "success", "message" => _("DID successfully updated"), "id" => $id);
    }
    public function deleteDIDByID($id)
    {
        $sql = "DELETE FROM DID WHERE `id` = :id";
        $sth = $this->db->prepare($sql);
        $sth->execute(array(':id' => $id));

        return array("status" => true, "type" => "success", "message" => _("DID entry successfully deleted"));
    }   



}
