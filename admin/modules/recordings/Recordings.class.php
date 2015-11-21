<?php
// vim: set ai ts=4 sw=4 ft=php:

class Recordings implements BMO {
	private $initialized = false;
	private $full_list = null;
	private $filter_list = array();

	public function __construct($freepbx = null) {
		if ($freepbx == null) {
			throw new Exception("Not given a FreePBX Object");
		}

		$this->FreePBX = $freepbx;
		$this->db = $freepbx->Database;
	}

	public function doConfigPageInit($page) {

	}

	public function install() {

	}
	public function uninstall() {

	}
	public function backup(){

	}
	public function restore($backup){

	}
	public function genConfig() {

	}

	public function getRecordingsById($id) {
		$sql = "SELECT * FROM recordings where id= ?";
		$sth = $this->db->prepare($sql);
		$sth->execute(array($id));
		return $sth->fetch(\PDO::FETCH_ASSOC);
	}

	public function getFilenameById($id) {
		$res = $this->getRecordingsById($id);
		if (empty($res)) {
			return '';
		}
		return $res['filename'];
	}

	public function getAllRecordings($compound = true) {
		if ($this->initialized) {
			return ($compound ? $this->full_list : $this->filter_list);
		}
		$this->initialized = true;

		$sql = "SELECT * FROM recordings where displayname <> '__invalid' ORDER BY displayname";
		$sth = $this->db->prepare($sql);
		$sth->execute();
		$this->full_list = $sth->fetchAll(\PDO::FETCH_ASSOC);
		foreach($this->full_list as &$item) {
			//TODO: Find instances of this and remove it!
			// Make array backward compatible, put first 4 columns as numeric
			$item[0] = $item['id'];
			$item[1] = $item['displayname'];
			$item[2] = $item['filename'];
			$item[3] = $item['description'];
			if (strstr($item['filename'],'&') === false) {
				$this->filter_list[] = $item;
			}
		}
		return ($compound ? $this->full_list : $this->filter_list);
	}
        
        
        public function ucp_getAllRecordings($userid, $compound = true) 
        {
            $sql = "SELECT r.* FROM recordings as r
            LEFT JOIN recording_user as ru ON (r.id = ru.fk_recording_id) WHERE ru.fk_user_id = :userid ORDER BY r.displayname";                
            $sth = $this->db->prepare($sql);
            $sth->execute(array(':userid' => $userid));                
            $full_list = $sth->fetchAll(\PDO::FETCH_ASSOC);
            foreach($full_list as &$item)
            {
                $item[0] = $item['id'];
                $item[1] = $item['displayname'];
                $item[2] = $item['filename'];
                $item[3] = $item['description'];
                if (strstr($item['filename'],'&') === false) {
                        $filter_list[] = $item;
                }
            }
            return ($compound ? $full_list : $filter_list);
	}
        
        public function ucp_getActiveCode() 
        {
            $fcc = new featurecode('recordings', 'record_save');
            $fc_save = $fcc->getCodeActive();
            unset($fcc);
            
            $fcc = new featurecode('recordings', 'record_check');
            $fc_check = $fcc->getCodeActive();
            unset($fcc);
            
            $ReturnArray['fc_save'] = ($fc_save!='')?$fc_save:'** MISSING FEATURE CODE **';
            $ReturnArray['fc_check'] = ($fc_check!='')?$fc_check:'** MISSING FEATURE CODE **';
            
            return $ReturnArray;
        }
        
        public function ucp_recordings_add($displayname, $filename, $description='', $userid ='') 
        {
            require 'functions.inc.php';

            // Check to make sure we can actually read the file if it has an extension (if it doesn't,
            // it was put here by system recordings, so we know it's there.
            if (recordings_has_valid_exten($filename)) 
            {
                if (!is_readable($recordings_astsnd_path.$filename)) 
                {
                    //print "<p>Unable to add ".$recordings_astsnd_path.$filename." - Can not read file!</p>";
                    return false;
		}
		$fname = recordings_remove_exten($filename);
            }
            else 
            {
		$fname = $filename;
            }
            $description = ($description != '') ? htmlentities($description, ENT_QUOTES, "UTF-8", false) : _("No long description available");
            $displayname = htmlentities($displayname, ENT_QUOTES, "UTF-8", false);
            if ($fname !== htmlentities($fname, ENT_QUOTES, "UTF-8", false)) 
            {
                //print "<p>Invalid file name supplied. Please rename.</p>";
                return false;
            }
            
            $sql = "INSERT INTO recordings (displayname, filename, description) VALUES (:displayname, :filename, :description)";
            $sth = $this->db->prepare($sql);
            $sth->execute(array(
            ':displayname' => $displayname,
            ':filename' => $fname,
            ':description' => $description,
            ));
            
            $id = $this->db->lastInsertId();
            $this->addRecordingUser($id, $userid);
            
            return true;
        }
        
        public function addRecordingUser($RecordingID = '', $userid = '')
        {
            if(!empty($RecordingID))
            {
                $sql = "INSERT INTO recording_user (fk_user_id, fk_recording_id) VALUES (:userid, :recordingid)";
                $sth = $this->db->prepare($sql);
                $sth->execute(array(
                ':userid' => $userid,
                ':recordingid' => $RecordingID,
                ));
            }
        }
        
        public function ucp_recordings_update($DataArray) 
        {
            $fcode_pass = preg_replace("/[^0-9*]/" ,"", trim($DataArray['fcode_pass']));
            
            $sql = "UPDATE recordings SET `displayname` = :displayname, `description` = :description, `fcode` = :fcode, `fcode_pass` = :fcode_pass WHERE `id` = :id";
            $sth = $this->db->prepare($sql);
            $sth->execute(array(
            ':displayname' => $DataArray['rname'],
            ':description' => $DataArray['notes'],
            ':fcode' => $DataArray['fcode'],
            ':fcode_pass' => $fcode_pass,
            ':id' => $DataArray['id'],
            ));
            
            $Message = "System Recording ".$DataArray['rname']." updated";
            $return = array("status" => true, "message" => $Message);
            return $return;
        }
        
        public function ucp_recordings_delete($RecordingID) 
        {
            $sql = "DELETE FROM recordings where id= ?";
            $sth = $this->db->prepare($sql);
            $sth->execute(array($RecordingID));
            
            // delete the feature code if it existed
            $fcc = new featurecode('recordings', 'edit-recording-'.$RecordingID);
            $fcc->delete();
            unset($fcc);
            
            $sql = "DELETE FROM recording_user WHERE `fk_recording_id` = :id";
            $sth = $this->db->prepare($sql);
            $sth->execute(array(':id' => $RecordingID));
            
            $Message = "System Recording deleted";
            $return = array("status" => true, "message" => $Message);
            return $return;
        }
        
        
        public function ucp_getRecordingsById($id) {
		$sql = "SELECT * FROM recordings where id= ?";
		$sth = $this->db->prepare($sql);
		$sth->execute(array($id));
		return $sth->fetch(\PDO::FETCH_ASSOC);
	}
        
        public function ucp_recordings_list_usage($id)
        {
            $full_usage_arr = array();
            
            $AnnUsageArr = $this->ucp_announcement_recordings_usage($id);            
            $IvrUsageArr = $this->ucp_ivr_recordings_usage($id);
            
            
            if(!empty($AnnUsageArr))
            {
                $full_usage_arr = array_merge($full_usage_arr, $AnnUsageArr);
                
            }
            if(!empty($IvrUsageArr))
            {
                $full_usage_arr = array_merge($full_usage_arr, $IvrUsageArr);
            }
            return $full_usage_arr;
            
            /*global $amp_conf;
            global $active_modules;
            $modpath = $amp_conf['AMPWEBROOT'] . '/admin/modules/';
            
            foreach ($active_modules as $mod) 
            {
                $file = $modpath . strtolower($mod) .'/functions.inc.php';
                $file_exists = is_file($file);
                
                if ($file_exists)
                {
                    require_once($file);
                }
            }
            
            $full_usage_arr = array();

            foreach($active_modules as $mod) {
                    $function = strtolower($mod)."_recordings_usage";
                    if (function_exists($function)) {
                            modgettext::push_textdomain($mod);
                            $recordings_usage = $function($id);
                            modgettext::pop_textdomain();
                            if (!empty($recordings_usage)) {
                                    $full_usage_arr = array_merge($full_usage_arr, $recordings_usage);
                            }
                    }
            }
            echo "<pre>";print_r($full_usage_arr);exit;
            return $full_usage_arr;
            
            $full_usage_arr = recordings_list_usage($id);
            
            return $full_usage_arr;*/
        }
        
        public function ucp_announcement_recordings_usage($id) {
            $Annsql = "SELECT announcement_id, description FROM announcement WHERE `recording_id` = :recording_id";
            $sth = $this->db->prepare($Annsql);
            $sth->execute(array(
            ':recording_id' => $id,
            ));
            $ann_usage_arr = $sth->fetchAll(\PDO::FETCH_ASSOC);
            
            if(!empty($ann_usage_arr))
            {
                
                foreach ($ann_usage_arr as $result)
                {
                    $usage_arr[] = array(                                                
                        "url_query" => "index.php?display=dashboard&mod=announcement&view=editannouncement&id=".$result['announcement_id']."&mode=edit",
                        "description" => "Announcement: ".$result['description']
                    );
                }
                return $usage_arr;
            }
            else
            {
                return array();
            }
        }
        
        public function ucp_ivr_recordings_usage($id)
        {
            $Ivrsql = "SELECT id, name FROM ivr_details WHERE announcement = '$id' OR "
                    . "invalid_retry_recording = '$id' OR invalid_recording = '$id' OR "
                    . "timeout_recording = '$id' OR timeout_retry_recording = '$id'";
            $sth = $this->db->prepare($Ivrsql);
            $sth->execute();
            $ivr_usage_arr = $sth->fetchAll(\PDO::FETCH_ASSOC);
            if(!empty($ivr_usage_arr))
            {
                foreach ($ivr_usage_arr as $result)
                {
                    $usage_arr[] = array(
                        "url_query" => "index.php?display=dashboard&mod=ivr&view=editivr&id=".$result['id']."&mode=edit",
                        "description" => "IVR: ".$result['name']
                    );
                }
                return $usage_arr;
            }
            else
            {
                return array();
            }
        }
}
