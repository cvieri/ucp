<?php
namespace FreePBX\modules\Sms\Adaptor;
class Sipstation extends \FreePBX\modules\Sms\AdaptorBase {
	//	License for all code of this FreePBX module can be found in the license file inside the module directory
	//	Copyright 2013 Schmooze Com Inc.
	//
	/**
	* Create the Notification Class statically while checking to make sure the class hasn't already been loaded
	*
	* @param object Database Object
	* @return object Notification object
	*/
	function &create() {
		static $obj;
		if (!isset($obj)) {
			$obj = new Sipstation();
		}
		return $obj;
	}


	public function sendMessage($to,$from,$cnam,$message) {
		$id = parent::sendMessage($to,$from,$cnam,$message);
		$astman = \FreePBX::create()->astman;
		$cnam = !empty($cnam) ? '"'.utf8_decode($cnam).'"' : '';
		if(class_exists("Emojione")) {
			$message = \Emojione::shortnameToAscii($message);
		}
		$res = $astman->MessageSend('sip:'.$to.'@trunk1.freepbx.com', $cnam.' <sip:+'.$from.'@trunk1.freepbx.com>', $message);
		$final = array();
		if(!empty($res['Response']) && ($res['Response'] == 'Success')) {
			return $final = array("status" => true, "id" => $id);
		} else {
			return $final['status'] = false;
		}
	}

	public function getMessage($to,$from,$cnam,$message) {
		parent::getMessage($to,$from,$cnam,$message);
	}

	public function dialPlanHooks(&$ext, $engine, $priority) {
		global $core_conf;

		//need to get the list of trunks we control.
		//\FreePBX::PJSip()->addEndpoint('test','message_context','sms-incoming');

		$c = 'sms-incoming';
		$ext->add($c, '_.', '', new \ext_noop('SMS came in with DID: ${EXTEN}'));
		$ext->add($c, '_.', '', new \ext_goto('1', 's'));
		$ext->add($c, 's', '', new \ext_agi('sipstation_sms.php, RECEIVE'));
		$ext->add($c, 's', '', new \ext_hangup());

		$core_conf->addSipGeneral('accept_outofcall_message','yes');
	}
}
