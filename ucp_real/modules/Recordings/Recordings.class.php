<?php
/**
 * This is the User Control Panel Object.
 *
 * Copyright (C) 2014 Schmooze Com, INC
 */
namespace UCP\Modules;
use \UCP\Modules as Modules;

class Recordings extends Modules
{
    protected $module = 'Recordings';
    private $ext = 0;
    private $limit = 15;
    private $break = 5;

    public function __construct($Modules) 
    {
        $this->Modules = $Modules;
        $this->rec = $this->UCP->FreePBX->Recordings;
        $this->user = $this->UCP->User->getUser();
    }

    /**
    * Determine what commands are allowed
    *
    * Used by Ajax Class to determine what commands are allowed by this class
    *
    * @param string $command The command something is trying to perform
    * @param string $settings The Settings being passed through $_POST or $_PUT
    * @return bool True if pass
    */
    function ajaxRequest($command, $settings) 
    {
        switch($command) 
        {
            case 'updaterecording':
            case 'deleterecording':
            case 'addrecording':
                    return true;
            default:
                    return false;
            break;
        }
    }

    /**
    * The Handler for all ajax events releated to this class
    *
    * Used by Ajax Class to process commands
    *
    * @return mixed Output if success, otherwise false will generate a 500 error serverside
    */
    function ajaxHandler()
    {
        $return = array("status" => false, "message" => "");
        switch($_REQUEST['command']) 
        {
            case 'updaterecording':
                $PostDataArray = $_REQUEST['recording'];
                $return = $this->rec->ucp_recordings_update($PostDataArray);
                //$return = array("status" => false, "message" => _("Unauthorized"));
            break;
            case 'deleterecording':
                $return = $this->rec->ucp_recordings_delete($_REQUEST['id']);
                //$return = array("status" => false, "message" => _("Unauthorized"));
            break;
            case 'addrecording':
                global $amp_conf;
                $PostDataArray = $_REQUEST['recording'];                                
                
                $usersnum = isset($PostDataArray['usersnum'])?$PostDataArray['usersnum']:'';
                $rname = isset($PostDataArray['rname'])?$PostDataArray['rname']:'';
                $suffix = isset($PostDataArray['suffix']) && $PostDataArray['suffix']!=''?$PostDataArray['suffix']:'wav';
                
                $recordings_save_path = $amp_conf['ASTSPOOLDIR']."/tmp/";
                $filename = escapeshellcmd(strtr($rname, '/ ', '__'));
                $suffix = escapeshellcmd(strtr($suffix, '/ ', '__'));
                $astsnd = isset($amp_conf['ASTVARLIBDIR'])?$amp_conf['ASTVARLIBDIR']:'/var/lib/asterisk';
                $astsnd .= "/sounds/";                                
                
                if (empty($usersnum) || !ctype_digit($usersnum)) {
                    $dest = "unnumbered-";
                } else {
                    $dest = "{$usersnum}-";
                }
                
                if (!file_exists($astsnd."custom")) 
                {
                    if (!mkdir($astsnd."custom", 0775))
                    {
                        $return = array("status" => false, "message" => _("Failed to create").' '.$astsnd.'custom');
                    }
		}
                else
                {
                    if (!file_exists($recordings_save_path."{$dest}ivrrecording.$suffix"))
                    {
                        $Message = '[ERROR] The Recorded File Does Not exists:'.$recordings_save_path.$dest.'ivrrecording.'.$suffix.'make sure you uploaded or recorded a file with the entered extension';
                        $return = array("status" => false, "message" => $Message);
                    }
                    else
                    {
                        $Message = '';
                        exec("cp " . $recordings_save_path . "{$dest}ivrrecording.$suffix " . $astsnd."custom/{$filename}.$suffix 2>&1", $outarray, $ret);
                        if (!$ret) 
                        {
                            $isok = $this->rec->ucp_recordings_add($rname,"custom/{$filename}.$suffix",'',$this->user['id']);
                            //$isok = recordings_add($rname, "custom/{$filename}.$suffix");
                        } 
                        else
                        {
                            $Message .= '[ERROR] SAVING RECORDING:';
                            foreach ($outarray as $line)
                            {
                                $Message .= $line;
                                $Message .= "<br>";
                            }
                            $Message .= 'Make sure you have entered a proper name';
                            $Message .= "<br>";
                        }
                        exec("rm " . $recordings_save_path . "{$dest}ivrrecording.$suffix ", $outarray, $ret);
                        if ($ret)
                        {
                            $Message .= '[ERROR] REMOVING TEMPORARY RECORDING:';
                            foreach ($outarray as $line) {
                                $Message .= $line;
                                $Message .= "<br>";
                            }
                            $Message .= 'Make sure Asterisk is not running as root';
                            $Message .= "<br>";
                        }
                    }
                        
                    if ($isok)
                    {
                        $ReturnMessage = 'System Recording '.$rname.' saved';
                        $return = array("status" => true, "message" => $ReturnMessage);
                    }
                    else
                    {
                        $return = array("status" => false, "message" => $Message);
                    }
                    
                }
                
            break;
			
            default:
                return false;
            break;
        }
        return $return;
    }

    function pregRecursiveArraySearch($needle,$haystack,$validKeys=array()) 
    {
        foreach($haystack as $key=>$value)
        {
            if(!empty($validKeys) && !in_array($key, $validKeys)) {
                    continue;
            }
            $current_key = $key;
            if(preg_match('/'.$needle.'/i',$value) OR (is_array($value) && $this->pregRecursiveArraySearch($needle,$value) !== false)) {
                return $current_key;
            }
        }
        return false;
    }

    /**
    * Generate the display in UCP
    */
    public function getDisplay() 
    {
        $view = !empty($_REQUEST['view']) ? $_REQUEST['view'] : '';
        $displayvars = array();		
        //$displayvars['groups'] = $this->cm->getGroupsByOwner($this->user['id']);
        //$displayvars['activeList'] = "myrecordings";
        $displayvars['total'] = 0;
        $displayvars['orderby'] = 'displayname';
        $displayvars['order'] = 'asc';
        //$displayvars['readonly'] = true;
        //$displayvars['add'] = false;
        //$displayvars['editable'] = false;
        $allRecordings = array();
        $Recordings = $this->rec->ucp_getAllRecordings($this->user['id']);
        $displayvars['total'] = $displayvars['total'] + count($Recordings);        
        $allRecordings = array_merge($allRecordings,$Recordings);
        $displayvars['recordings'] = $Recordings;
        
		/*$c = 1;
		foreach($displayvars['groups'] as &$group) {
			$group['readonly'] = ($group['owner'] == -1);
			$group['contacts'] = $this->cm->getEntriesByGroupID($group['id']);
			$group['count'] = count($group['contacts']);
			$displayvars['total'] = $displayvars['total'] + $group['count'];
			$allContacts = array_merge($allContacts,$group['contacts']);
			if(!empty($_REQUEST['view']) && $_REQUEST['view'] == "group" && $_REQUEST['id'] == $group['id']) {
				$displayvars['activeList'] = $group['name'];
				$displayvars['contacts'] = $group['contacts'];
				$displayvars['readonly'] = $group['readonly'];
			}else if(!empty($_REQUEST['view']) && $_REQUEST['view'] == "contact" && $_REQUEST['group'] == $group['id']) {
				$displayvars['activeList'] = $group['name'];
				$displayvars['readonly'] = $group['readonly'];
			}
		}*/

        usort($allRecordings, function($a, $b) {
            return strnatcmp($a['displayname'], $b['displayname']);
        });

        switch($view) 
        {
            case "addrecording":
                global $amp_conf;
                $displayvars['recordings_save_path'] = $amp_conf['ASTSPOOLDIR']."/tmp/";
                
                //$usersnum = isset($_REQUEST['usersnum'])?$_REQUEST['usersnum']:'';
                //$displayvars['usersnum'] = $usersnum;
                $displayvars['usersnum'] = $this->user['username'];
                
                $displayvars['UserName'] = $this->user['username'];
                
                $FeaturedCodeArray = $this->rec->ucp_getActiveCode();
                $displayvars['FeaturedCodeArray'] = $FeaturedCodeArray;
                
                
                $displayvars['add'] = true;
                $mainDisplay = $this->load_view(__DIR__.'/views/addrecording.php',$displayvars);
               
            break;
            case "editrecording":
                $id = $_REQUEST['id'];
                $usersnum = $_REQUEST['usersnum'];
                
                $displayvars['id'] = $id;
                $displayvars['usersnum'] = $usersnum;
                
                $fcbase = '*29';
                $displayvars['fcbase'] = $fcbase;
                
                $recordingdata = $this->rec->ucp_getRecordingsById($id);
                $displayvars['recordingdata'] = $recordingdata;
                
                $usage_list = $this->rec->ucp_recordings_list_usage($id);
                $displayvars['usage_list'] = $usage_list;
                
                $displayvars['edit'] = true;
                $mainDisplay = $this->load_view(__DIR__.'/views/editrecording.php',$displayvars);
            break;
			
            case "contact":
                    $g = $this->cm->getGroupByID($_REQUEST['group']);
                    if(!empty($g)) {
                            $displayvars['contact'] = $this->cm->getEntryByID($_REQUEST['id']);
                            if($g['owner'] == -1) {
                                    $mainDisplay = $this->load_view(__DIR__.'/views/contactro.php',$displayvars);
                            } else {
                                    if(!empty($_REQUEST['mode']) && $_REQUEST['mode'] == 'edit') {
                                            $mainDisplay = $this->load_view(__DIR__.'/views/contact.php',$displayvars);
                                    } else {
                                            $displayvars['editable'] = true;
                                            $mainDisplay = $this->load_view(__DIR__.'/views/contactro.php',$displayvars);
                                    }
                            }
                    } else {
                            $mainDisplay = _("Not Authorized");
                    }
            break;
            default:
                $recordings = !empty($displayvars['recordings']) || !empty($_REQUEST['view']) ? $displayvars['recordings'] : $allRecordings;                
                $displayvars['search'] = $search =  $_REQUEST['search'] ? $_REQUEST['search'] : '';
                if(!empty($search)) {
                    $temp = $recordings;
                    $recordings = array();
                    foreach($temp as $c) {
                        if($this->pregRecursiveArraySearch($search,$c, array('displayname')) !== false) {
                                $recordings[] = $c;
                        }
                    }
                }
                $orderby = $_REQUEST['orderby'] ? $_REQUEST['orderby'] : 'displayname';
                $order = $_REQUEST['order'] ? $_REQUEST['order'] : 'desc';
                switch($orderby) {
                    case 'displayname':
                    default:
                        uasort($recordings, function($a, $b) {
                                return strcasecmp($a['displayname'],$b['displayname']);
                        });
                        $displayvars['orderby'] = 'displayname';
                    break;
                }

                if($order == 'asc') {
                    $recordings = array_reverse($recordings);
                    $recordings = array_values($recordings);
                    $displayvars['order'] = 'asc';
                } else {
                    $displayvars['order'] = 'desc';
                }

                $view = !empty($_REQUEST['view']) ? $_REQUEST['view'] : 'all';
                $id = !empty($_REQUEST['id']) ? '&id='.$_REQUEST['id'] : '';
                $page = !empty($_REQUEST['page']) ? $_REQUEST['page'] : 1;
                $total = ceil(count($recordings) / $this->limit);
                $displayvars['pagnation'] = $this->UCP->Template->generatePagnation($total,$page,'?display=dashboard&mod=recordings&view='.$view.'&orderby='.$displayvars['orderby'].'&order='.$displayvars['order'].$id,$this->break);
                $recordings = array_slice($recordings,($this->limit * ($page - 1)),$this->limit);
                $displayvars['recordings'] = $recordings;
                $mainDisplay = $this->load_view(__DIR__.'/views/recordings.php',$displayvars);
            break;
        }


        //$html = $this->load_view(__DIR__.'/views/nav.php',$displayvars);
        $html .= $mainDisplay;
        return $html;
    }

	public function lookupMultiple($search) {
		$entry = $this->cm->lookupMultipleByUserID($this->user['id'],$search);
		return $entry;
	}

	public function lookup($search) {
		$entry = $this->cm->lookupByUserID($this->user['id'],$search);
		return $entry;
	}

	/**
	* Setup Menu Items for display in UCP
	*/
	public function getMenuItems() {
		$menu = array(
			"rawname" => "recordings",
			"name" => _("Recordings")
		);
		return $menu;
	}

	public function poll() {
		$contacts = $this->cm->getContactsByUserID($this->user['id']);
		if(!empty($contacts)) {
			return array(
				'enabled' => true,
				'contacts' => $contacts
			);
		} else {
			return array('enabled' => false);
		}
	}

	/**
	* Send settings to UCP upon initalization
	*/
	public function getStaticSettings() {
		$contacts = $this->cm->getContactsByUserID($this->user['id']);
		if(!empty($contacts)) {
			return array(
				'enabled' => true,
				'contacts' => $contacts
			);
		} else {
			return array('enabled' => false);
		}
	}
}
