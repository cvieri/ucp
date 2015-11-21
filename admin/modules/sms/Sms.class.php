<?php
// vim: set ai ts=4 sw=4 ft=php:
//	License for all code of this FreePBX module can be found in the license file inside the module directory
//	Copyright 2013 Schmooze Com Inc.
//
namespace FreePBX\modules;
class Sms implements \BMO {
	public function __construct($freepbx = null) {
		if ($freepbx == null) {
			throw new \Exception("Not given a FreePBX Object");
		}
		$this->FreePBX = $freepbx;
		$this->db = $freepbx->Database;
		if(!class_exists("Emojione")) {
			include(__DIR__."/includes/Emojione.class.php");
		}
	}

	public function install() {

	}
	public function uninstall() {

	}
	public function backup(){

	}
	public function restore($backup){

	}
	public function doConfigPageInit($page){
		return true;
	}

	public function myDialplanHooks() {
		return true;
	}

	/**
	 * Add DID to the Routing Table
	 * @param {int} $did           The DID
	 * @param {array} $users=array() The assigned user(s)
	 */
	public function addDIDRouting($did,$users=array()) {
		$did = strlen($did) == 10 ? '1'.$did : $did;
		$sql = "DELETE FROM sms_routing WHERE did = ?";
		$sth = $this->db->prepare($sql);
		$sth->execute(array($did));
		foreach($users as $user) {
			$sql = "INSERT INTO sms_routing (`did`, `uid`, `accepter`, `adaptor`) VALUES (?,?,?,?)";
			$sth = $this->db->prepare($sql);
			$sth->execute(array($did,$user,'UCP','Sipstation'));
		}
	}

	/**
	 * Add User to the DID Routing Table
	 * @param {int} $user         The User Man User ID
	 * @param {array} $dids=array() Array of DIDs to add to said user
	 */
	public function addUserRouting($user,$dids=array()) {
		$sql = "DELETE FROM sms_routing WHERE uid = ?";
		$sth = $this->db->prepare($sql);
		$sth->execute(array($user));
		foreach($dids as $did) {
			$did = strlen($did) == 10 ? '1'.$did : $did;
			$sql = "INSERT INTO sms_routing (`did`, `uid`, `accepter`, `adaptor`) VALUES (?,?,?,?)";
			$sth = $this->db->prepare($sql);
			$sth->execute(array($did,$user,'UCP','Sipstation'));
		}
	}

	/**
	 * Get Routing information from said DID
	 * @param {int} $did The DID
	 */
	public function getDIDRouting($did) {
		$did = strlen($did) == 10 ? '1'.$did : $did;
		$sql = "SELECT * FROM sms_routing WHERE did = ?";
		$sth = $this->db->prepare($sql);
		$sth->execute(array($did));
		$dids = $sth->fetchAll(\PDO::FETCH_ASSOC);
		return $dids;
	}

	/**
	 * Get Assigned user for said did
	 * @param {int} $did The DID
	 */
	public function getAssignedUsers($did) {
		$routing = $this->getDIDRouting($did);
		$final = array();
		foreach($routing as $r) {
			$final[] = $r['uid'];
		}
		return $final;
	}

	/**
	 * Get DIDs that are Assigned for this User
	 * @param {[type]} $user [description]
	 */
	public function getAssignedDIDs($user) {
		$routing = $this->getUserRouting($user);
		$final = array();
		foreach($routing as $r) {
			$final[] = $r['did'];
		}
		return $final;
	}

	/**
	 * Get all routing information for said user
	 * @param {int} $user The User ID
	 */
	public function getUserRouting($user) {
		$sql = "SELECT * FROM sms_routing WHERE uid = ?";
		$sth = $this->db->prepare($sql);
		$sth->execute(array($user));
		$user = $sth->fetchAll(\PDO::FETCH_ASSOC);
		return $user;
	}

	/**
	 * Get all messages less than said ID
	 * @param {int} $uid   The User ID
	 * @param {int} $id    The Message ID
	 * @param {int} $from  The DID from
	 * @param {int} $to    The DID to
	 * @param {int} $limit = 1 How many results to return
	 */
	public function getMessagesOlderThanID($uid,$id,$from,$to,$limit = 1) {
		$sql = "SELECT m.* FROM sms_messages m, sms_routing r WHERE r.uid = ? AND ((m.from = ? AND m.to = ?) OR (m.from = ? AND m.to = ?)) AND (m.from = r.did OR m.to = r.did) AND m.id < ? ORDER BY UNIX_TIMESTAMP(m.tx_rx_datetime) DESC LIMIT ".$limit;
		$sth = $this->db->prepare($sql);
		$sth->execute(array($uid,$from,$to,$to,$from,$id));
		$messages = $sth->fetchAll(\PDO::FETCH_ASSOC);
		return $messages;
	}

	/**
	 * Get all messages that have been marked as delivered
	 * @param {int} $uid   The User ID
	 * @param {int} $from  The DID from
	 * @param {int} $to    The DID to
	 * @param {int} $start =             0 The starting position
	 * @param {int} $limit =             1 How many results to return
	 */
	public function getAllDeliveredMessages($uid,$from,$to,$start = 0, $limit = 1) {
		$sql = "SELECT m.* FROM sms_messages m, sms_routing r WHERE r.uid = ? AND ((m.from = ? AND m.to = ?) OR (m.from = ? AND m.to = ?)) AND (m.from = r.did OR m.to = r.did) ORDER BY UNIX_TIMESTAMP(m.tx_rx_datetime) DESC LIMIT ".$start.",".$limit;
		$sth = $this->db->prepare($sql);
		$sth->execute(array($uid,$from,$to,$to,$from));
		$messages = $sth->fetchAll(\PDO::FETCH_ASSOC);
		return $messages;
	}

	/**
	 * Get all Undelivered Messages
	 * @param {int} $uid The User ID
	 */
	public function getAllUndeliveredMessages($uid) {
		$sql = "SELECT m.* FROM sms_messages m, sms_routing r WHERE r.uid = ? AND (m.from = r.did OR m.to = r.did) AND direction = 'in' AND delivered = 0";
		$sth = $this->db->prepare($sql);
		$sth->execute(array($uid));
		$messages = $sth->fetchAll(\PDO::FETCH_ASSOC);
		return $messages;
	}

	/**
	 * Get All Messages
	 * @param {int} $uid       The User ID
	 * @param {int} $from      The DID from
	 * @param {int} $to        The DID to
	 * @param {string} $search='' The search phrase to look for
	 */
	public function getAllMessages($uid,$from,$to,$search='') {
		if(empty($search)) {
			$sql = "SELECT m.*, UNIX_TIMESTAMP(m.tx_rx_datetime) as utime FROM sms_messages m, sms_routing r WHERE r.uid = ? AND ((m.from = ? AND m.to = ?) OR (m.from = ? AND m.to = ?)) AND (m.from = r.did OR m.to = r.did) ORDER BY UNIX_TIMESTAMP(m.tx_rx_datetime) DESC";
			$sth = $this->db->prepare($sql);
			$sth->execute(array($uid,$from,$to,$to,$from));
		} else {
			$sql = "SELECT m.*, UNIX_TIMESTAMP(m.tx_rx_datetime) as utime FROM sms_messages m, sms_routing r WHERE r.uid = ? AND ((m.from = ? AND m.to = ?) OR (m.from = ? AND m.to = ?)) AND (m.from = r.did OR m.to = r.did) AND body LIKE ? ORDER BY UNIX_TIMESTAMP(m.tx_rx_datetime) DESC";
			$sth = $this->db->prepare($sql);
			$sth->execute(array($uid,$from,$to,$to,$from,'%'.$search.'%'));
		}
		$messages = $sth->fetchAll(\PDO::FETCH_ASSOC);
		return $messages;
	}

	/**
	 * Get All Messages after ID
	 * @param {int} $uid   The User ID
	 * @param {int} $from  The DID from
	 * @param {int} $to    The DID to
	 * @param {int} $msgId The message ID to check after
	 */
	public function getAllMessagesAfterID($uid,$from,$to,$msgId) {
		$sql = "SELECT m.* FROM sms_messages m, sms_routing r WHERE r.uid = ? AND (m.from = r.did OR m.to = r.did) AND ((m.from = ? AND m.to = ?) OR (m.from = ? AND m.to = ?)) AND m.id > ? ORDER BY UNIX_TIMESTAMP(m.tx_rx_datetime) DESC";
		$sth = $this->db->prepare($sql);
		$sth->execute(array($uid,$from,$to,$to,$from,$msgId));
		$messages = $sth->fetchAll(\PDO::FETCH_ASSOC);
		return $messages;
	}

	/**
	 * Get Messages since last time stamp
	 * @param {int} $uid  The User ID
	 * @param {int} $time Unix timestamp
	 */
	public function getMessagesSinceTime($uid,$time) {
		$sql = "SELECT m.* FROM sms_messages m, sms_routing r WHERE r.uid = ? AND (m.from = r.did OR m.to = r.did) AND UNIX_TIMESTAMP(m.tx_rx_datetime) > ?";
		$sth = $this->db->prepare($sql);
		$sth->execute(array($uid,$time));
		$messages = $sth->fetchAll(\PDO::FETCH_ASSOC);
		return $messages;
	}

	/**
	 * Get page count
	 * @param {int} $uid       The User ID
	 * @param {string} $search='' The Search Phrase
	 * @param {int} $limit=100 The limit of results to return per page
	 */
	public function getPages($uid,$search='',$limit=100) {
		$conversations = $this->getAllMessagesHistory($uid,$search);
		$total = count($conversations);
		if(!empty($total)) {
			return ceil($total/$limit);
		} else {
			return false;
		}
	}

	/**
	 * Get All History of All Messages
	 * @param {int} $uid            The User ID
	 * @param {string} $search=''      The search phrase
	 * @param {string} $order='asc'    Sorting order
	 * @param {string} $orderby='date' Sorting by
	 * @param {int} $start=0        The Starting position
	 * @param {int} $limit=100      The limit of results to return
	 */
	public function getAllMessagesHistory($uid,$search='',$order='asc',$orderby='date',$start=0,$limit=100) {
		$dids = $this->getDIDs($uid);
		$grouped = array();
		$messages = array();
		foreach($dids as $did) {
			$conversations = $this->getConversations($uid, $did);
			foreach($conversations as $convo) {
				if(in_array($convo['from'],$dids)) {
					$mdid = $convo['from'];
					$tdid = $convo['to'];
				} elseif(in_array($convo['to'],$dids)) {
					$mdid = $convo['to'];
					$tdid = $convo['from'];
				}
				if(!isset($messages[$mdid."|".$tdid])) {
					//search first for the 'word'
					$msgs = $this->getAllMessages($uid,$mdid,$tdid,$search);
					if(!empty($msgs)) {
						//ok good now get all messages
						$messages[$mdid."|".$tdid]['messages'] = $this->getAllMessages($uid,$mdid,$tdid);
						$messages[$mdid."|".$tdid]['from'] = $mdid;
						$messages[$mdid."|".$tdid]['to'] = $tdid;
						$messages[$mdid."|".$tdid]['prettyto'] = $this->replaceDIDwithDisplay($uid,$tdid);
					}
				}
			}
		}
		switch($orderby) {
			case 'from':
				uasort($messages, function($a, $b) {
					return strcmp($a['from'], $b['from']);
				});
				if($order == 'desc') {
					$messages = array_reverse($messages, true);
				}
			break;
			case 'to':
				uasort($messages, function($a, $b) {
					return strcmp($a['prettyto'], $b['prettyto']);
				});
				if($order == 'desc') {
					$messages = array_reverse($messages, true);
				}
			break;
			case 'date':
			default:
				uasort($messages, function($a, $b) {
					return ($a['messages'][0]['utime'] < $b['messages'][0]['utime']) ? -1 : 1;
				});
				if($order == 'desc') {
					$messages = array_reverse($messages, true);
				}
			break;
		}
		$all = $messages;
		$start = ($start > 0) ? ($start-1) : 0;

		return array_slice($all,$start,$limit,true);
	}

	/**
	 * Get All Conversations for said DID
	 * @param {int} $did The DID
	 */
	public function getConversations($uid, $did) {
		$sql = "SELECT DISTINCT a.`from`, a.`to` FROM `sms_messages` a, sms_routing b WHERE (a.`from` = :did OR a.`to` = :did) AND (b.did = :did OR b.did = :did) AND b.uid = :uid ORDER BY UNIX_TIMESTAMP(tx_rx_datetime);";
		$sth = $this->db->prepare($sql);
		$sth->execute(array(":did" => $did, ":uid" => $uid));
		$conversations = $sth->fetchAll(\PDO::FETCH_ASSOC);
		return $conversations;
	}

	public function deleteConversations($uid, $did1, $did2) {
		try {
			$sql = "DELETE a FROM `sms_messages` a, `sms_routing` b WHERE ((a.`from` = :did1 OR a.`to` = :did1) OR (a.`from` = :did2 OR a.`to` = :did2)) AND (b.did = :did1 OR b.did = :did2) AND b.uid = :uid";
			$sth = $this->db->prepare($sql);
			$sth->execute(array(":did1" => $did1, ":did2" => $did2, ":uid" => $uid));
			return true;
		} catch(\Exception $e) {
			return false;
		}
	}

	/**
	 * Get all DIDs assigned to user
	 * @param {int} $uid the user ID
	 */
	public function getDIDs($uid) {
		$sql = "SELECT DID,Adaptor FROM sms_routing WHERE uid = ?";
		$sth = $this->db->prepare($sql);
		$sth->execute(array($uid));
		$dids = $sth->fetchAll(\PDO::FETCH_ASSOC);
		$final = array();
		foreach($dids as $did) {
			//make sure we can load the adaptor, if not then the DID isnt valid for now
			try{
				$final[] = $did['DID'];
				$this->loadAdaptor($did['Adaptor']);
			}catch(\Exception $e) {}
		}
		return $final;
	}

	/**
	 * Mark a Message Read
	 * @param {int} $msgId The message ID
	 */
	public function markMessageRead($msgId) {
		$sql = "UPDATE sms_messages SET `read` = 1 WHERE direction = 'in' AND `read` = 0 AND id = ?";
		$sth = $this->db->prepare($sql);
		$sth->execute(array($msgId));
	}

	/**
	 * Mark a message delivered
	 * @param {int} $msgId The message ID
	 */
	public function markMessageDelivered($msgId) {
		$sql = "UPDATE sms_messages SET delivered = 1 WHERE direction = 'in' AND delivered = 0 AND id = ?";
		$sth = $this->db->prepare($sql);
		$sth->execute(array($msgId));
	}

	/**
	 * Do all the dialplan hooking from other hooks
	 * @param {object} &$ext     The Extension object
	 * @param {string} $engine   The engine (Asterisk)
	 * @param {string} $priority The Priority
	 */
	public function doDialplanHook(&$ext, $engine, $priority) {
		foreach($this->getAllAdaptors() as $adaptor) {
			$adaptor = $this->loadAdaptor($adaptor['adaptor']);
			$adaptor->dialPlanHooks($ext, $engine, $priority);
		}
	}

	/**
	 * Try to load the adaptor from a provided DID
	 * @param {int} $did The DID
	 */
	public function getAdaptor($did) {
		$sql = 'SELECT adaptor FROM sms_routing WHERE did = ?';
		try {
			$sth = $this->db->prepare($sql);
			$sth->execute(array($did));
			$a = $sth->fetch(\PDO::FETCH_ASSOC);
			$adaptor = $a['adaptor'];
		} catch(\Exception $e) {
			$adaptor = 'Generic';
		}
		if(empty($a['adaptor'])) {
			$adaptor = 'Generic';
		}

		return $this->loadAdaptor($adaptor);
	}

	/**
	 * Load the Adaptor from the Adaptor Name
	 * @param {string} $adaptor The adaptor name
	 */
	public function loadAdaptor($adaptor) {
		$adaptor = ucfirst(strtolower($adaptor));
		if(!class_exists('\FreePBX\modules\Sms\AdaptorBase')) {
			include(__DIR__.('/includes/AdaptorBase.class.php'));
		}

		$classname = '\FreePBX\modules\Sms\Adaptor\\'.$adaptor;

		if(!class_exists($classname)) {
			$class = $this->FreePBX->Hooks->processHooks($adaptor);
			if(!empty($class[$adaptor]) && is_object($class[$adaptor]) && is_a($class[$adaptor],$classname)) {
				return $class[$adaptor];
			} elseif(empty($class)) {
				//TODO: Should we load the generic class here?
				throw new \Exception('I could not find '.$adaptor);
			} else {
				throw new \Exception('I was passed something I did not expect!');
			}
		} else {
			return $classname::Create();
		}
	}

	/**
	 * Get all Adaptors that have a routing assignment
	 */
	public function getAllAdaptors() {
		$sql = "SELECT DISTINCT adaptor FROM asterisk.sms_routing";
		$sth = $this->db->prepare($sql);
		$sth->execute();
		$adaptors = $sth->fetchAll(\PDO::FETCH_ASSOC);
		return $adaptors;
	}

	public function replaceDIDwithDisplay($id, $did) {
		if($this->FreePBX->Modules->checkStatus("contactmanager")) {
			$sdid = strlen($did) == 11 ? substr($did, 1) : $did;
			try {
				$user = $this->FreePBX->Contactmanager->lookupByUserID($id, $sdid, '/\D/');
				if(!empty($user)) {
					return $user['displayname'];
				}
				$user = $this->FreePBX->Contactmanager->lookupByUserID($id, $did, '/\D/');
				if(!empty($user)) {
					return $user['displayname'];
				}
			} catch(\Exception $e) {
				return $did;
			}
		}
		return $did;
	}
}
