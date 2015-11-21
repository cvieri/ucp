<?php
/**
 * This is the User Control Panel Object.
 *
 * Copyright (C) 2014 Schmooze Com, INC
 */
namespace UCP\Modules;
use \UCP\Modules as Modules;

class Ivr extends Modules
{
    protected $module = 'Ivr';
    private $ext = 0;
    private $limit = 15;
    private $break = 5;

    public function __construct($Modules) {
        $this->Modules = $Modules;
        $this->ivr = $this->UCP->FreePBX->Ivr;
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
    function ajaxRequest($command, $settings) {
        switch($command)
        {
            case 'updateivr':
            case 'deleteivr':
            case 'enable':
                return true;
            case 'addivr':
            //case 'bindcmb':
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
            case 'updateivr':
                $CheckIvr = $this->ivr->checkIvrByUser($this->user['id'],$_REQUEST['id']);
                if(!empty($CheckIvr))
                {
                    $ivr = $_REQUEST['ivr'];
                    $return = $this->ivr->updateIvr($_REQUEST['id'], $ivr,$this->user);
                    break;
                }
                
                $return = array("status" => false, "message" => _("Unauthorized"));
            break;
            case 'deleteivr':
                
                $return = $this->ivr->deleteEntryByID($_REQUEST['id']);
                //$return = array("status" => false, "message" => _("Unauthorized"));
            break;
            case 'addivr':
                $ivr = $_REQUEST['ivr'];  
                $return = $this->ivr->addIvrByUserID($this->user, $ivr);
            break;
            case 'enable':
                if($_POST['enable'] == 'true') {    
                    $this->ivr->setuseivrByExtension($_POST['ext'],"1");
                } else {
                    $this->ivr->setuseivrByExtension($_POST['ext'],"0");
                }
                return array("status" => true, "alert" => "success", "message" => _('IVR Has Been Updated!'));
            break;
            /*case 'bindcmb':
                $result = $this->ivr->BindSelectionBox($_REQUEST['value'],$_REQUEST['name']);
                $return = array("returndata" => $result);
            break;*/
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
            if(!empty($validKeys) && !in_array($key, $validKeys)) 
            {
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
        $displayvars['activeList'] = "myivr";
        $displayvars['total'] = 0;
        $displayvars['orderby'] = 'name';
        $displayvars['order'] = 'desc';
        $displayvars['readonly'] = true;
        $displayvars['add'] = false;
        $displayvars['editable'] = false;
        $IvrsArray = array();
        $IvrDataArray = $this->ivr->getIvrByUserID($this->user['id']);

        $displayvars['total'] = $displayvars['total'] + count($IvrDataArray);
        $displayvars['ivrs'] = $IvrDataArray;
                
        /*$c = 1;
        foreach($displayvars['groups'] as &$group) {
                $group['readonly'] = ($group['owner'] == -1);
                $group['contacts'] = $this->ivr->getEntriesByGroupID($this->user['id']);
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

        usort($IvrDataArray, function($a, $b) {
            return strnatcmp($a['name'], $b['name']);
        });

        switch($view)
        {
            case "addivr":
                $displayvars['add'] = true;	
                $displayvars['edit'] = false;
                $displayvars['allRecordings'] = $this->ivr->getAllRecordings($this->user['id']);
                $displayvars['InvalidDestinationArray'] = $this->ivr->getDestinationArray('invalid','',$this->user);
                $displayvars['TimeoutDestinationArray'] = $this->ivr->getDestinationArray('timeout','',$this->user);
                $displayvars['IVREntriesArray'] = $this->ivr->getDestinationArray('DESTID','',$this->user);
                
                $mainDisplay = $this->load_view(__DIR__.'/views/ivrcreate.php',$displayvars);
            break;
            case "editivr":
                $CheckIvr = $this->ivr->checkIvrByUser($this->user['id'],$_REQUEST['id']);
                if(!empty($CheckIvr))
                {
                    $displayvars['edit'] = true;
                    $IVRDetail = $this->ivr->getIvrByID($_REQUEST['id']);
                    $IVREntries = $this->ivr->getIvrEntries($_REQUEST['id']);
                    //$DIDIvr = $this->ivr->getDIDIvr($_REQUEST['id']);
                    
                    $displayvars['ivr'] = $IVRDetail;
                    $displayvars['InvalidDestinationArray'] = $this->ivr->getDestinationArray('invalid',$IVRDetail['invalid_destination'],$this->user);
                    $displayvars['TimeoutDestinationArray'] = $this->ivr->getDestinationArray('timeout',$IVRDetail['timeout_destination'],$this->user);
                    $displayvars['IVREntries'] = $IVREntries;
                    //$displayvars['DIDIvr'] = $DIDIvr;
                    $displayvars['allRecordings'] = $this->ivr->getAllRecordings($this->user['id']);
                    
                    
                    if(count($IVREntries) > 0)
                    {
                        $EntriesArray = array();
                        foreach ($IVREntries as $EntryValue) 
                        {
                            $DaraArray['ivr_id'] = $EntryValue['ivr_id'];
                            $DaraArray['selection'] = $EntryValue['selection'];
                            $DaraArray['ivr_ret'] = $EntryValue['ivr_ret'];
                            $DaraArray['dest'] = $this->ivr->getDestinationArray('DESTID',$EntryValue['dest'],$this->user);
                            $FinalDaraArray[] = $DaraArray;
                            //$EntriesArray[] = $this->ivr->getDestinationArray('DESTID',$EntryValue['dest']);
                        }
                        $IVREntriesArray = $FinalDaraArray;
                    }
                    else
                    {
                        $IVREntriesArray = $this->ivr->getDestinationArray('DESTID');
                    }
                    $displayvars['IVREntriesArray'] = $IVREntriesArray;
                    
                    if(!empty($_REQUEST['mode']) && $_REQUEST['mode'] == 'edit') 
                    {
                        $mainDisplay = $this->load_view(__DIR__.'/views/ivrcreate.php',$displayvars);
                    }
                }
                else 
                {
                    $mainDisplay = _("Not Authorized");
                }

            break;
                default:
                    $ivrs = !empty($displayvars['ivrs']) || !empty($_REQUEST['view'])  ? $displayvars['ivrs'] : $IvrsArray;
                    $displayvars['search'] = $search =  $_REQUEST['search'] ? $_REQUEST['search'] : '';
                    if(!empty($search))
                    {
                        $temp = $ivrs;
                        $ivrs = array();
                        foreach($temp as $c) 
                        {
                            if($this->pregRecursiveArraySearch($search,$c, array('name','description')) !== false) 
                            {
                                    $ivrs[] = $c;
                            }
                        }
                    }
                    $orderby = $_REQUEST['orderby'] ? $_REQUEST['orderby'] : 'name';
                    $order = $_REQUEST['order'] ? $_REQUEST['order'] : 'desc';
                    switch($orderby)
                    {
                        case 'description':
                            uasort($ivrs, function($a, $b) {
                                    return strcasecmp($a['description'],$b['description']);
                            });
                            $displayvars['orderby'] = 'description';
                        break;
                        case 'name':
                        default:
                            uasort($ivrs, function($a, $b) {
                                    return strcasecmp($a['name'],$b['name']);
                            });
                            $displayvars['orderby'] = 'name';
                        break;
                    }

                    if($order == 'asc') {
                        $ivrs = array_reverse($ivrs);
                        $ivrs = array_values($ivrs);
                        $displayvars['order'] = 'asc';
                    } else {
                        $displayvars['order'] = 'desc';
                    }

                    $view = !empty($_REQUEST['view']) ? $_REQUEST['view'] : 'all';
                    $id = !empty($_REQUEST['id']) ? '&id='.$_REQUEST['id'] : '';
                    $page = !empty($_REQUEST['page']) ? $_REQUEST['page'] : 1;
                    $total = ceil(count($ivrs) / $this->limit);
                    $displayvars['pagnation'] = $this->UCP->Template->generatePagnation($total,$page,'?display=dashboard&mod=ivr&view='.$view.'&orderby='.$displayvars['orderby'].'&order='.$displayvars['order'].$id,$this->break);
                    $ivrs = array_slice($ivrs,($this->limit * ($page - 1)),$this->limit);
                    $displayvars['ivrs'] = $ivrs;
                    $mainDisplay = $this->load_view(__DIR__.'/views/ivr.php',$displayvars);
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
			"rawname" => "ivr",
			"name" => _("Ivr")
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
        
        /*public function getSettingsDisplay($ext) {
		$displayvars = array(
                    "useivr" => $this->ivr->getuseivrByExtension($ext)
		);
		$out = array(
                    array(
                        "title" => _('IVR'),
                        "content" => $this->load_view(__DIR__.'/views/settings.php',$displayvars),
                        "size" => 6
                    )
		);
		return $out;
	}*/
}
