<?php
//	License for all code of this FreePBX module can be found in the license file inside the module directory
//	Copyright 2013 Schmooze Com Inc.
//
namespace UCP\Modules;
use \UCP\Modules as Modules;

class Sms extends Modules{
	protected $module = 'Sms';

	private $userID = null;
	private $limit = 25;

	function __construct($Modules) {
		$this->Modules = $Modules;
		$this->user = $this->UCP->User->getUser();
		$this->userID = $this->user['id'];
		$this->sms = $this->UCP->FreePBX->Sms;
		$this->dids = $this->sms->getDIDs($this->user['id']);
	}

	function getDisplay() {
		if(empty($this->dids)) {
			return '';
		}
		$displayvars = array();
		$displayvars['orderby'] = !empty($_REQUEST['orderby']) ? $_REQUEST['orderby'] : 'date';
		$displayvars['order'] = !empty($_REQUEST['order']) ? $_REQUEST['order'] : 'desc';
		$displayvars['search'] = !empty($_REQUEST['search']) ? $_REQUEST['search'] : '';
		$page = !empty($_REQUEST['page']) ? $_REQUEST['page'] : 1;
		$displayvars['pagnation'] = $this->UCP->Template->generatePagnation($this->sms->getPages($this->userID,$displayvars['search'],$this->limit),$page,'?display=dashboard&mod=sms',5);
		$displayvars['messages'] = $this->sms->getAllMessagesHistory($this->userID,$displayvars['search'],$displayvars['order'],$displayvars['orderby'],$page,$this->limit);
		foreach($displayvars['messages'] as &$conversation) {
			foreach($conversation['messages'] as &$message) {
				$message['body'] = \Emojione::toImage($message['body']);
			}
		}
		$html = $this->load_view(__DIR__.'/views/history.php',$displayvars);
		return $html;
	}

	function poll($data,$mdata=array()) {
		if(empty($this->dids)) {
			return array('status' => false);
		}
		$messages = array();
		//see if there are any new messages since the last checked time
		$mdata['lastchecked'] = !empty($mdata['lastchecked']) ? $mdata['lastchecked'] : null;
		$newmessages = $this->sms->getMessagesSinceTime($this->userID,$mdata['lastchecked']);
		if(!empty($newmessages)) {
			foreach($newmessages as $messageb) {
				$mid = $messageb['id'];
				if(in_array($messageb['from'],$this->dids)) {
					$messageb['did'] = $messageb['from'];
					$messageb['recp'] = $messageb['to'];
					$from = $messageb['from'];
					$to = $messageb['to'];
				} else {
					$messageb['did'] = $messageb['to'];
					$messageb['recp'] = $messageb['from'];
					$from = $messageb['to'];
					$to = $messageb['from'];
				}
				$wid = $from.$to;
				$messageb['cnam'] = !empty($messageb['cnam']) ? $messageb['cnam'] : $messageb['from'];
				$messages[$wid][$mid] = $messageb;

			}
		}

		//get all messages from open windows that weren't picked up from lastcheck
		if(!empty($mdata['messageWindows'])) {
			foreach($mdata['messageWindows'] as $window) {
				$msgs = $this->sms->getAllMessagesAfterID($this->userID,$window['from'],$window['to'],$window['last']);
				$wid = $window['windowid'];
				foreach($msgs as $messageb) {
					$mid = $messageb['id'];
					if(in_array($messageb['from'],$this->dids)) {
						$messageb['did'] = $messageb['from'];
						$messageb['recp'] = $messageb['to'];
						$from = $messageb['from'];
						$to = $messageb['to'];
					} else {
						$messageb['did'] = $messageb['to'];
						$messageb['recp'] = $messageb['from'];
						$from = $messageb['to'];
						$to = $messageb['from'];
					}
					$messageb['cnam'] = !empty($messageb['cnam']) ? $messageb['cnam'] : $messageb['from'];
					if(!isset($messages[$wid][$mid])) {
						$messages[$wid][$mid] = $messageb;
					}
				}
			}
		}

		//reset array keys so they don't get out of control
		foreach($messages as $windowid => &$m) {
			$m = array_values($m);
			$m = array_reverse($m);
		}
		return array('status' => true, 'messages' => $messages);
	}

	function getChatHistory($from, $to, $newWindow) {
		$start = ($newWindow == 'true') ? 0 : 1;
		$messages = $this->sms->getAllDeliveredMessages($this->userID,$from,$to,$start,10);
		$final = array();
		if(!empty($messages)) {
			foreach($messages as $m) {
				$final['messages'][] = array(
					'id' => $m['id'],
					'from' => in_array($m['from'],$this->dids) ? _('Me') : $this->replaceDIDwithDisplay($m['from']),
					'message' => trim(\Emojione::toImage($m['body'])),
					'date' => strtotime($m['tx_rx_datetime'])
				);
			}
			$final['lastMessage'] = $final['messages'][0];
			$final['messages'] = array_reverse($final['messages']);
		} else {
			$final = array('messages' => array(), 'lastMessage' => '');
		}
		return $final;
	}

	function getOldMessages($id,$from,$to) {
		$messages = $this->sms->getMessagesOlderThanID($this->userID,$id,$from,$to,10);
		$final = array();
		if(!empty($messages)) {
			foreach($messages as $m) {
				$final[] = array(
					'id' => $m['id'],
					'from' => in_array($m['from'],$this->dids) ? _('Me') : $this->replaceDIDwithDisplay($m['from']),
					'message' => \Emojione::toImage($m['body']),
					'date' => strtotime($m['tx_rx_datetime'])
				);
			}
			$final = array_reverse($final);
		} else {
			$final = array();
		}
		return $final;
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
		switch($command) {
			case 'history':
			case 'delivered':
			case 'read':
			case 'send':
			case 'dids':
			case 'delete':
			case 'contacts':
				return true;
			break;
			default:
				return false;
			break;
		}
	}

	public function getNavItems() {
		if(empty($this->dids)) {
			return false;
		}
		$dlist = "";
		$count = 1;
		foreach($this->sms->getAllMessagesHistory($this->userID) as $did) {
			if($count > 5) {
				break;
			}
			$dlist .= "<li><a class='did' data-did='" . $did['to'] . "'>" . $did['prettyto'] . "</a></li>";
			$count++;
		}
		$out = array();
		$out[] = array(
			"rawname" => "sms",
			"badge" => false,
			"icon" => "fa-comments-o",
			"menu" => array(
				"html" => '<li><a class="new">'._("New SMS").'</a></li><li><hr></li>' . $dlist
			)
		);
		return $out;
	}

	/**
	 * The Handler for all ajax events releated to this class
	 *
	 * Used by Ajax Class to process commands
	 *
	 * @return mixed Output if success, otherwise false will generate a 500 error serverside
	 */
	function ajaxHandler() {
		$return = array("status" => false, "message" => "");
		switch($_REQUEST['command']) {
			case 'contacts':
				$return = array();
				if($this->Modules->moduleHasMethod('Contactmanager','lookupMultiple')) {
					$search = !empty($_REQUEST['search']) ? $_REQUEST['search'] : "";
					$results = $this->Modules->Contactmanager->lookupMultiple($search);
					if(!empty($results)) {
						foreach($results as $res) {
							foreach($res['numbers'] as $type => $num) {
								if(!empty($num)) {
									$return[] = array(
										"value" => $num,
										"text" => $res['displayname'] . " (".$type.")"
									);
								}
							}
						}
					}
				}
				break;
			case 'delete':
				$this->sms->deleteConversations($this->userID, $_REQUEST['from'], $_REQUEST['to']);
				$return['status'] = true;
			break;
			case 'history':
				$messages = $this->getOldMessages($_POST['id'],$_POST['from'],$_POST['to']);
				$return['status'] = true;
				$return['messages'] = $messages;
			break;
			case 'dids':
				$return['status'] = true;
				$return['dids'] = $this->dids;
			break;
			case 'read':
				$this->sms->markMessageRead($_POST['id']);
			break;
			case 'delivered':
				foreach($_POST['ids'] as $id) {
					$this->sms->markMessageDelivered($id);
				}
			break;
			case 'send':
				$did = $_POST['from'];
				$adaptor = $this->sms->getAdaptor($did);
				$name = !empty($this->user['fname']) ? $this->user['fname'] : $this->user['username'];
				$o = $adaptor->sendMessage($_POST['to'],$this->formatNumber($did),$name,$_POST['message']);
				if($o['status']) {
					$return['status'] = true;
					$return['id'] = $o['id'];
				} else {
					$return['message'] = 'Message could not be delivered';
				}
			break;
			default:
				return false;
			break;
		}
		return $return;
	}

	private function formatNumber($number) {
		if(strlen($number) == 10) {
			$number = '1'.$number;
		}
		return $number;
	}

	public function getBadge() {
		return false;
	}

	public function getMenuItems() {
		if(empty($this->dids)) {
			return array();
		}
		$menu = array(
			"rawname" => "sms",
			"name" => "Sms",
			"badge" => false
		);
		return $menu;
	}

	/**
	* Send settings to UCP upon initalization
	*/
	public function getStaticSettings() {
		if(!empty($this->dids)) {
			return array(
				'enabled' => true,
				'dids' => $this->dids
			);
		} else {
			return array('enabled' => false);
		}
	}

	public function replaceDIDwithDisplay($did) {
		return $this->UCP->FreePBX->Sms->replaceDIDwithDisplay($this->userID,$did);
	}
}
