<?php
/**
 * This is the User Control Panel Object.
 *
 * Copyright (C) 2014 Schmooze Com, INC
 */
namespace UCP\Modules;
use \UCP\Modules as Modules;

class Announcement extends Modules
{
    protected $module = 'Announcement';
    private $ext = 0;
    private $limit = 15;
    private $break = 5;

    public function __construct($Modules) 
    {
        $this->Modules = $Modules;
        $this->announcement = $this->UCP->FreePBX->Announcement;
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
            switch($command) {
                    case 'addannouncement':
                          return true;
                    case 'updateannouncement':
                          return true;
                    case 'deleteannouncement':
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
		switch($_REQUEST['command']) {
			case 'updateannouncement':
                            $CheckAnnouncement = $this->announcement->checkAnnouncementByUser($this->user['id'],$_REQUEST['id']);
                            if(!empty($CheckAnnouncement))
                            {
                                $announcement = $_REQUEST['announcement'];
                                $return = $this->announcement->updateAnnouncement($_REQUEST['id'], $announcement);
                                break;
                            }
                            $return = array("status" => false, "message" => _("Unauthorized"));
                        break;
			case 'deleteannouncement':
				$entry = $this->announcement->checkAnnouncementByUser($this->user['id'],$_REQUEST['id']);
				if(!empty($entry)) {
                                    $return = $this->announcement->deleteEntryByID($_REQUEST['id']);
                                    break;
				}
				$return = array("status" => false, "message" => _("Unauthorized"));
			break;
			case 'addannouncement':
                            $announcement = $_REQUEST['announcement'];  
                            $return = $this->announcement->addAnnouncementByUserID($this->user['id'], $announcement);
			break;
			default:
				return false;
			break;
		}
		return $return;
	}

	function pregRecursiveArraySearch($needle,$haystack,$validKeys=array()) {
		foreach($haystack as $key=>$value) {
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
        $displayvars['activeList'] = "myannouncements";
        $displayvars['total'] = 0;
        $displayvars['orderby'] = 'description';
        $displayvars['order'] = 'desc';
        $displayvars['readonly'] = true;
        $displayvars['add'] = false;
        $displayvars['editable'] = false;
        $AnnouncementsArray = array();
        
        $AnnouncementDataArray = $this->announcement->getAnnouncementByUserID($this->user['id']);
        $displayvars['total'] = $displayvars['total'] + count($AnnouncementDataArray);
        $displayvars['announcements'] = $AnnouncementDataArray;

        usort($AnnouncementDataArray, function($a, $b) {
            return strnatcmp($a['description'], $b['description']);
        });

        switch($view) 
        {
            case "addannouncement":
                $displayvars['activeList'] = $g['name'];
                $displayvars['add'] = true;
                $displayvars['allRecordings'] = $this->announcement->getAllRecordings($this->user['id']);
                $mainDisplay = $this->load_view(__DIR__.'/views/announcementcreate.php',$displayvars);
                break;
            break;
            case "editannouncement":
                $CheckIvr = $this->announcement->checkAnnouncementByUser($this->user['id'],$_REQUEST['id']);
                if(!empty($CheckIvr))
                {
                    $displayvars['edit'] = true;
                    $ANNOUNCEMENTDetail = $this->announcement->getAnnouncementByID($_REQUEST['id']);
                    
                    $displayvars['announcement'] = $ANNOUNCEMENTDetail;
                    $displayvars['allRecordings'] = $this->announcement->getAllRecordings($this->user['id']);
                    
                    //$displayvars['InvalidDestinationArray'] = $this->ivr->getDestinationArray('invalid',$IVRDetail['invalid_destination']);
                    //$displayvars['TimeoutDestinationArray'] = $this->ivr->getDestinationArray('timeout',$IVRDetail['timeout_destination']);
                    
                    if(!empty($_REQUEST['mode']) && $_REQUEST['mode'] == 'edit') 
                    {
                        $mainDisplay = $this->load_view(__DIR__.'/views/announcementcreate.php',$displayvars);
                    }
                }
                else 
                {
                    $mainDisplay = _("Not Authorized");
                }

            break;
            default:
                $announcements = !empty($displayvars['announcements']) || !empty($_REQUEST['view'])  ? $displayvars['announcements'] : $AnnouncementDataArray;
                $displayvars['search'] = $search =  $_REQUEST['search'] ? $_REQUEST['search'] : '';
                if(!empty($search))
                {
                    $temp = $announcements;
                    $announcements = array();
                    foreach($temp as $c) 
                    {
                        if($this->pregRecursiveArraySearch($search,$c, array('name','description')) !== false) 
                        {
                                $announcements[] = $c;
                        }
                    }
                }
                $orderby = $_REQUEST['orderby'] ? $_REQUEST['orderby'] : 'description';
                $order = $_REQUEST['order'] ? $_REQUEST['order'] : 'desc';
                switch($orderby)
                {
                    case 'description':
                        uasort($announcements, function($a, $b) {
                                return strcasecmp($a['description'],$b['description']);
                        });
                        $displayvars['orderby'] = 'description';
                    break;
                    case 'allow_skip':
                    default:
                        uasort($announcements, function($a, $b) {
                                return strcasecmp($a['allow_skip'],$b['allow_skip']);
                        });
                        $displayvars['orderby'] = 'allow_skip';
                    break;
                    case 'return_ivr':
                    default:
                        uasort($announcements, function($a, $b) {
                                return strcasecmp($a['return_ivr'],$b['return_ivr']);
                        });
                        $displayvars['orderby'] = 'return_ivr';
                    break;
                    case 'noanswer':
                    default:
                        uasort($announcements, function($a, $b) {
                                return strcasecmp($a['noanswer'],$b['noanswer']);
                        });
                        $displayvars['orderby'] = 'noanswer';
                    break;
                }

                if($order == 'asc') {
                    $announcements = array_reverse($announcements);
                    $announcements = array_values($announcements);
                    $displayvars['order'] = 'asc';
                } else {
                    $displayvars['order'] = 'desc';
                }

                $view = !empty($_REQUEST['view']) ? $_REQUEST['view'] : 'all';
                $id = !empty($_REQUEST['id']) ? '&id='.$_REQUEST['id'] : '';
                $page = !empty($_REQUEST['page']) ? $_REQUEST['page'] : 1;
                $total = ceil(count($announcements) / $this->limit);
                $displayvars['pagnation'] = $this->UCP->Template->generatePagnation($total,$page,'?display=dashboard&mod=announcement&view='.$view.'&orderby='.$displayvars['orderby'].'&order='.$displayvars['order'].$id,$this->break);
                $announcements = array_slice($announcements,($this->limit * ($page - 1)),$this->limit);
                $displayvars['announcements'] = $announcements;
                $mainDisplay = $this->load_view(__DIR__.'/views/Announcement.php',$displayvars);
                
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
			"rawname" => "announcement",
			"name" => _("Announcement")
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
