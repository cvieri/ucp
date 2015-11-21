<?php
// vim: set ai ts=4 sw=4 ft=php:
//	License for all code of this FreePBX module can be found in the license file inside the module directory
//	Copyright 2014 Schmooze Com Inc.
//
namespace FreePBX\modules;
class Announcement extends \FreePBX_Helpers implements \BMO {
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
    public function getAnnouncementByUserID($userid) {
        
            $fields = array(
                'a.announcement_id','a.announcement_id as announcementid','a.description','a.allow_skip','a.return_ivr','a.noanswer','a.repeat_msg'
            );                

            $sql = "SELECT " . implode(', ', $fields) . " FROM announcement as a
            LEFT JOIN announcement_user as au ON (a.announcement_id = au.fk_announcement_id) WHERE au.fk_user_id = :userid ORDER BY a.description";                
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
    public function addAnnouncementByUserID($userid, $entry)
    {
        $validation = true;
        if(empty($entry['description']))
        {
            $validation = false;
            $Message = 'Announcement Description missing';
        }
        if(!$validation)
        {
            return array("status" => false, "type" => "danger", "message" => _($Message));
        }
        
        $sql = "INSERT INTO announcement (description, recording_id, allow_skip, return_ivr, noanswer, repeat_msg) VALUES (:description, :recording_id, :allow_skip, :return_ivr, :noanswer, :repeat_msg)";
            $sth = $this->db->prepare($sql);
            $sth->execute(array(
            ':description' => $entry['description'],
            ':recording_id' => $entry['recording_id'],
            ':allow_skip' => ($entry['allow_skip'] ? 1 : 0),
            ':return_ivr' => ($entry['return_ivr'] ? 1 : 0),
            ':noanswer' => ($entry['noanswer'] ? 1 : 0),
            ':repeat_msg' => $entry['repeat_msg'],
            ));
        $id = $this->db->lastInsertId();
        
//        $sql = "INSERT INTO announcement (description, recording_id, allow_skip, return_ivr, noanswer, repeat_msg) VALUES (".
//		"".$this->db->escapeSimple($entry['description']).", ".
//		"".$entry['recording_id'].", ".
//		"".($entry['allow_skip'] ? 1 : 0).", ".
//		"".($entry['return_ivr'] ? 1 : 0).", ".
//		"".($entry['noanswer'] ? 1 : 0).", ".
//		"".$this->db->escapeSimple($entry['repeat_msg']).")";
//	$result = $this->db->query($sql);
//        $id = $this->db->lastInsertId();
        $this->addannouncementUserByEntryID($id, $userid);
        return array("status" => true, "type" => "success", "message" => _("ANNOUNCEMENT successfully added"), "id" => $id);
    }

    /**
     * Add IVR User by Entry ID
     * @param {int} $entryid The entry ID
     * @param {array} $userid The user ID
     */
    public function addannouncementUserByEntryID($entryid, $userid) 
    {
        if(!empty($entryid) && !empty($userid))
        {
            $sql = "INSERT INTO announcement_user (fk_user_id, fk_announcement_id) VALUES (:userid, :entryid)";
            $sth = $this->db->prepare($sql);
            $sth->execute(array(
            ':userid' => $userid,
            ':entryid' => $entryid,
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
    public function checkAnnouncementByUser($userid,$id) {
            $sql = "SELECT * FROM announcement_user WHERE `fk_announcement_id` = :announcementid AND `fk_user_id` = :userid";
            $sth = $this->db->prepare($sql);
            $sth->execute(array(
            ':announcementid' => $id,
            ':userid' => $userid,
            ));
            $ivruser = $sth->fetch(\PDO::FETCH_ASSOC);
            return $ivruser;
    }


    /**
     * Get all information about an IVR
     * @param {int} $id The IVR ID
     */
    public function getAnnouncementByID($id)
    {
        $sql = "SELECT * FROM announcement WHERE announcement_id = :id";
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


    /**
     * Update IVR Information
     * @param {int} $id The IVR ID
     * @param {array} $entry   Array of Ivr information
     */
    public function updateAnnouncement($id,$entry)
    {      
        $sql = "UPDATE announcement SET ".
		"description = ".$this->db->escapeSimple($entry['description']).",".
		"recording_id = '".$entry['recording_id']."', ".
		"allow_skip = '".($entry['allow_skip'] ? 1 : 0)."', ".
		"return_ivr = '".($entry['return_ivr'] ? 1 : 0)."', ".
		"noanswer = '".($entry['noanswer'] ? 1 : 0)."', ".
		"repeat_msg = ".$this->db->escapeSimple($entry['repeat_msg'])."".
		"WHERE announcement_id = ".$this->db->escapeSimple($id);
        $sth = $this->db->query($sql);
        return array("status" => true, "type" => "success", "message" => _("Announcement successfully updated"), "id" => $id);
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
    
    /**
	 * Delete Entry by ID
	 * @param {int} $id The entry ID
    */
    public function deleteEntryByID($id) {
        
        $ret = $this->deleteannouncementuserByEntryID($id);
        if (!$ret['status']) {
                return $ret;
        }

        $sql = "DELETE FROM announcement WHERE `announcement_id` = :id";
        $sth = $this->db->prepare($sql);
        $sth->execute(array(':id' => $id));

        return array("status" => true, "type" => "success", "message" => _("Announcement entry successfully deleted"));
    }
       
    function deleteannouncementuserByEntryID($id)
    {
        $sql = "DELETE FROM announcement_user WHERE `fk_announcement_id` = :id";
        $sth = $this->db->prepare($sql);
        $sth->execute(array(':id' => $id));
        return array("status" => true, "type" => "success", "message" => _("Announcement entry successfully deleted"));
        
    }
    
//    function getDestinationArray($BoxID = '', $goto = '')
//    {
//        //$goto = 'app-announcement-1,s,1';
//        $DataArray = array();
//        $DataArray = array(
//            "Announcements" => "Announcements",
//            "Extensions" => "Extensions",
//            "IVR" => "IVR",
//            "Terminate_Call" => "Terminate Call",
//            "Voicemail" => "Voicemail"
//        );
//        
//        $MainStyle = '';
//        if($BoxID=='DESTID')
//        {
//            $MainStyle = 'width:200px;margin-right:10px;float:left;';
//        }
//        $DestinationCmbStr = '';
//        $DestinationCmbStr .= '<select data-id="'.$BoxID.'" class="form-control destdropdown" id="goto'.$BoxID.'" name="goto'.$BoxID.'" style="'.$MainStyle.'">';
//        $DestinationCmbStr .= '<option style="" value="">== choose one ==</option>';
//        foreach ($DataArray as $mod => $value) 
//        {
//            $funct = strtolower('ucp_'.$mod.'_destinations');
//            $destArray = $this->$funct();
//            
//            foreach($destArray as $dest)
//            {
//                if($goto==$dest['destination'])
//                {
//                    $destmod=$mod;
//                }
//                $drawselect_destinations[$mod][] = $dest;
//            }
//            
//            $CmbSelected = ($destmod==$mod)?'selected':'';
//            $DestinationCmbStr .= '<option style="" value="'.$mod.'" '.$CmbSelected.' >'.$value.'</option>';
//        }
//        $DestinationCmbStr .= '</select>';
//        
//        
//        $BindStr = '';
//        foreach($drawselect_destinations as $cat=>$destination)
//        {        
//            $Style = ($destmod==$cat)?'display:block;':'display:none;';
//            $BindStr .= '<select data-id="'.$BoxID.'" class="form-control destdropdown2" style="'.$Style.'width:200px;" id="'.$cat.$BoxID.'" name="'.$cat.$BoxID.'">';
//            foreach ($destination as $key => $dest)
//            {
//                $Selected = ($dest['destination']==$goto)?'selected':'';
//                $BindStr .= '<option value="'.$dest['destination'].'" '.$Selected.' >'.$dest['description'].'</option>';
//            }
//            $BindStr .= '</select>';
//        }
//        
//        $ReturnArray['destinationarray'] = $DestinationCmbStr;
//        $ReturnArray['destinationdataarray'] = $BindStr;
//        
//        return $ReturnArray;
//        
//    }
//    
//    function ucp_announcements_destinations($name) 
//    {
//        $sql = "SELECT * FROM announcement ORDER BY description ";
//        $sth = $this->db->prepare($sql);
//        $sth->execute();
//        $results = $sth->fetchAll(\PDO::FETCH_ASSOC);
//        
//        foreach ($results as $resultvalue)
//        {
//            $extens[] = array('destination' => 'app-announcement-'.$resultvalue['announcement_id'].',s,1', 'description' => $resultvalue['description']);
//        }
//        return $extens;
//        
//        
//        /*$BindStr = '';
//        $BindStr .= '<select class="form-control" id="Announcements'.$name.'" name="Announcements'.$name.'">';
//        foreach ($results as $resultvalue) 
//        {
//            $BindStr .= '<option value="app-announcement-'.$resultvalue['announcement_id'].',s,1">'.$resultvalue['description'].'</option>';
//        }
//        $BindStr .= '</select>';
//        
//        return $BindStr;*/
//    }
//    
//    function ucp_terminate_call_destinations($name)
//    {
//        $extens = array();
//        $extens[] = array('destination' => 'app-blackhole,hangup,1', 'description' => 'Hangup');
//        $extens[] = array('destination' => 'app-blackhole,congestion,1', 'description' => 'Congestion');
//        $extens[] = array('destination' => 'app-blackhole,busy,1', 'description' => 'Busy');
//        $extens[] = array('destination' => 'app-blackhole,zapateller,1', 'description' => 'Play SIT Tone (Zapateller)');
//        $extens[] = array('destination' => 'app-blackhole,musiconhold,1', 'description' => 'Put caller on hold forever');
//        $extens[] = array('destination' => 'app-blackhole,ring,1', 'description' => 'Play ringtones to caller until they hangup');
//        return $extens;
//        
//        /*$BindStr = '';
//        $BindStr .= '<select class="form-control" id="Terminate_Call'.$name.'" name="Terminate_Call'.$name.'">';
//        $BindStr .= '<option value="app-blackhole,hangup,1">Hangup</option>';
//        $BindStr .= '<option value="app-blackhole,congestion,1">Congestion</option>';
//        $BindStr .= '<option value="app-blackhole,busy,1">Busy</option>';
//        $BindStr .= '<option value="app-blackhole,zapateller,1">Play SIT Tone (Zapateller)</option>';
//        $BindStr .= '<option value="app-blackhole,musiconhold,1">Put caller on hold forever</option>';
//        $BindStr .= '<option value="app-blackhole,ring,1">Play ringtones to caller until they hangup</option>';
//        $BindStr .= '</select>';
//        
//        return $BindStr;*/
//    }
//    
//    function ucp_ivr_destinations($name)
//    {
//        $sql = "SELECT * FROM ivr_details ORDER BY name ";
//        $sth = $this->db->prepare($sql);
//        $sth->execute();
//        $results = $sth->fetchAll(\PDO::FETCH_ASSOC);
//        
//        foreach ($results as $resultvalue)
//        {
//            $extens[] = array('destination' => 'ivr-'.$resultvalue['id'].',s,1', 'description' => $resultvalue['name']);
//        }
//        return $extens;
//        
//        /*$BindStr = '';
//        $BindStr .= '<select class="form-control" id="IVR'.$name.'" name="IVR'.$name.'">';
//        foreach ($results as $resultvalue) 
//        {
//            $BindStr .= '<option value="ivr-'.$resultvalue['id'].',s,1">'.$resultvalue['name'].'</option>';
//        }
//        $BindStr .= '</select>';
//        
//        return $BindStr;*/
//    }
//    
//    function ucp_voicemail_destinations($name)
//    {
//        $extens = array();
//        $extens[] = array('destination' => 'app-blackhole,hangup,1', 'description' => 'Hangup');
//        $extens[] = array('destination' => 'app-blackhole,congestion,1', 'description' => 'Congestion');
//        $extens[] = array('destination' => 'app-blackhole,busy,1', 'description' => 'Busy');
//        $extens[] = array('destination' => 'app-blackhole,zapateller,1', 'description' => 'Play SIT Tone (Zapateller)');
//        $extens[] = array('destination' => 'app-blackhole,musiconhold,1', 'description' => 'Put caller on hold forever');
//        $extens[] = array('destination' => 'app-blackhole,ring,1', 'description' => 'Play ringtones to caller until they hangup');
//        return $extens;
//        
//        /*$BindStr = '';
//        $BindStr .= '<select class="form-control" id="Terminate_Call'.$name.'" name="Terminate_Call'.$name.'">';
//        $BindStr .= '<option value="app-blackhole,hangup,1">Hangup</option>';
//        $BindStr .= '<option value="app-blackhole,congestion,1">Congestion</option>';
//        $BindStr .= '<option value="app-blackhole,busy,1">Busy</option>';
//        $BindStr .= '<option value="app-blackhole,zapateller,1">Play SIT Tone (Zapateller)</option>';
//        $BindStr .= '<option value="app-blackhole,musiconhold,1">Put caller on hold forever</option>';
//        $BindStr .= '<option value="app-blackhole,ring,1">Play ringtones to caller until they hangup</option>';
//        $BindStr .= '</select>';
//        
//        return $BindStr;*/
//    }
//    
//    function ucp_extensions_destinations($name)
//    {
//        $extens = array();
//        $extens[] = array('destination' => 'app-blackhole,hangup,1', 'description' => 'Hangup');
//        $extens[] = array('destination' => 'app-blackhole,congestion,1', 'description' => 'Congestion');
//        $extens[] = array('destination' => 'app-blackhole,busy,1', 'description' => 'Busy');
//        $extens[] = array('destination' => 'app-blackhole,zapateller,1', 'description' => 'Play SIT Tone (Zapateller)');
//        $extens[] = array('destination' => 'app-blackhole,musiconhold,1', 'description' => 'Put caller on hold forever');
//        $extens[] = array('destination' => 'app-blackhole,ring,1', 'description' => 'Play ringtones to caller until they hangup');
//        return $extens;
//        
//        /*$BindStr = '';
//        $BindStr .= '<select class="form-control" id="Terminate_Call'.$name.'" name="Terminate_Call'.$name.'">';
//        $BindStr .= '<option value="app-blackhole,hangup,1">Hangup</option>';
//        $BindStr .= '<option value="app-blackhole,congestion,1">Congestion</option>';
//        $BindStr .= '<option value="app-blackhole,busy,1">Busy</option>';
//        $BindStr .= '<option value="app-blackhole,zapateller,1">Play SIT Tone (Zapateller)</option>';
//        $BindStr .= '<option value="app-blackhole,musiconhold,1">Put caller on hold forever</option>';
//        $BindStr .= '<option value="app-blackhole,ring,1">Play ringtones to caller until they hangup</option>';
//        $BindStr .= '</select>';
//        
//        return $BindStr;*/
//    }
    
        
        
        


}
