<?php
/**
 * This is the User Control Panel Object.
 *
 * Copyright (C) 2014 Schmooze Com, INC
 */
namespace UCP\Modules;
use \UCP\Modules as Modules;

class Inboundsettings extends Modules
{
    protected $module = 'Inboundsettings';
    private $ext = 0;
    private $limit = 15;
    private $break = 5;

    public function __construct($Modules) 
    {
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
    function ajaxRequest($command, $settings) 
    {
        switch($command) 
        {
            case 'updatedid':
            case 'deletedid':
            case 'adddid':
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
            case 'adddid':
                $did = $_REQUEST['did'];  
                $return = $this->ivr->addDIDByUserID($this->user['username'], $did);
            break;
            case 'updatedid':
                $CheckDID = $this->ivr->checkDIDByUser($this->user['username'],$_REQUEST['id']);
                if(!empty($CheckDID))
                {
                    $did = $_REQUEST['did'];
                    $return = $this->ivr->updateDID($_REQUEST['id'],$did);
                    break;
                }                
                $return = array("status" => false, "message" => _("Unauthorized"));
            break;
            case 'deletedid':
                $CheckDID = $this->ivr->checkDIDByUser($this->user['username'],$_REQUEST['id']);
                if(!empty($CheckDID)) 
                {
                    $return = $this->ivr->deleteDIDByID($_REQUEST['id']);
                    break;
                }
                $return = array("status" => false, "message" => _("Unauthorized"));
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
        $displayvars['activeList'] = "mydids";
        $displayvars['total'] = 0;
        $displayvars['orderby'] = 'id';
        $displayvars['order'] = 'desc';
        $displayvars['readonly'] = true;
        $displayvars['add'] = true;
        $displayvars['editable'] = true;
        
        $allDids = array();
        $DIDDataArray = $this->ivr->getDIDByUsername($this->user['username']);
        $displayvars['total'] = $displayvars['total'] + count($DIDDataArray);
        $displayvars['dids'] = $DIDDataArray;

        usort($DIDDataArray, function($a, $b) {
                return strnatcmp($a['id'], $b['id']);
        });

        switch($view) 
        {
            case "adddid":
                $displayvars['add'] = true;	
                $displayvars['edit'] = false;  
                
                $mainDisplay = $this->load_view(__DIR__.'/views/adddid.php',$displayvars);
            break;
            case "editdid":
                $displayvars['add'] = false;
                $displayvars['edit'] = true;
                
                $DidDetail = $this->ivr->getDIDByID($_REQUEST['id']);
                $displayvars['did'] = $DidDetail;
                
                if(!empty($DidDetail)) 
                {
                    $mainDisplay = $this->load_view(__DIR__.'/views/adddid.php',$displayvars);
                    break;
                }
                $displayvars['activeList'] = '';
                $mainDisplay = _("Not Authorized");
            break;
            default:
                $dids = !empty($displayvars['dids']) || !empty($_REQUEST['view']) ? $displayvars['dids'] : $allDids;
                $displayvars['search'] = $search =  $_REQUEST['search'] ? $_REQUEST['search'] : '';
                if(!empty($search)) 
                {
                    $temp = $dids;
                    $dids = array();
                    foreach($temp as $c) 
                    {
                        if($this->pregRecursiveArraySearch($search,$c, array('did','username','divertion','ivr')) !== false) 
                        {
                            $dids[] = $c;
                        }
                    }
                }
                $orderby = $_REQUEST['orderby'] ? $_REQUEST['orderby'] : 'id';
                $order = $_REQUEST['order'] ? $_REQUEST['order'] : 'desc';
                switch($orderby) 
                {
                    case 'did':
                        uasort($dids, function($a, $b) {
                                return strcasecmp($a['did'],$b['did']);
                        });
                        $displayvars['orderby'] = 'did';
                    break;
                    case 'username':
                        uasort($dids, function($a, $b) {
                            return strcasecmp($a['username'],$b['username']);
                        });
                        $displayvars['orderby'] = 'username';
                    break;
                    case 'divertion':
                        uasort($dids, function($a, $b) {
                                return strcasecmp($a['divertion'],$b['divertion']);
                        });
                        $displayvars['orderby'] = 'divertion';
                    break;
                    case 'ivr':
                        uasort($dids, function($a, $b) {
                                return strcasecmp($a['ivr'],$b['ivr']);
                        });
                        $displayvars['orderby'] = 'ivr';
                    break;
                    case 'forwardnumber':
                        uasort($dids, function($a, $b) {
                                return strcasecmp($a['forwardnumber'],$b['forwardnumber']);
                        });
                        $displayvars['orderby'] = 'forwardnumber';
                    break;
                    case 'id':
                    default:
                        uasort($dids, function($a, $b) {
                                return strcasecmp($a['id'],$b['id']);
                        });
                        $displayvars['orderby'] = 'id';
                    break;
                }

                if($order == 'asc') 
                {
                    $dids = array_reverse($dids);
                    $dids = array_values($dids);
                    $displayvars['order'] = 'asc';
                } else {
                    $displayvars['order'] = 'desc';
                }

                $view = !empty($_REQUEST['view']) ? $_REQUEST['view'] : 'all';
                $id = !empty($_REQUEST['id']) ? '&id='.$_REQUEST['id'] : '';
                $page = !empty($_REQUEST['page']) ? $_REQUEST['page'] : 1;
                $total = ceil(count($dids) / $this->limit);
                $displayvars['pagnation'] = $this->UCP->Template->generatePagnation($total,$page,'?display=dashboard&mod=inboundsettings&view='.$view.'&orderby='.$displayvars['orderby'].'&order='.$displayvars['order'].$id,$this->break);
                $dids = array_slice($dids,($this->limit * ($page - 1)),$this->limit);
                $displayvars['dids'] = $dids;                
                $mainDisplay = $this->load_view(__DIR__.'/views/dids.php',$displayvars);
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
			"rawname" => "inboundsettings",
			"name" => _("DID Management")
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
