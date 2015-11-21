<?php
// vim: set ai ts=4 sw=4 ft=php:
//	License for all code of this FreePBX module can be found in the license file inside the module directory
//	Copyright 2013 Schmooze Com Inc.
//
namespace FreePBX\modules;

// Default setting array passed to ajaxRequest
$setting = array('authenticate' => true, 'allowremote' => false);

class Sipstation implements \BMO {

	public $ss = null;
	private $tollfree = "/(^888)|(^877)|(^866)|(^855)|(^844)|(^800)/";

	private static $oobeobj = false;

	public function __construct($freepbx = null) {
		if ($freepbx == null) {
			throw new \Exception("Not given a FreePBX Object");
		}
		if(!class_exists('sipstation')){
			include(__DIR__.'/functions.inc/sipstation.inc.php');
		}
		$this->ss = new \sipstation();
		$this->freepbx = $freepbx;
		$path = \FreePBX::Config()->get('AMPBIN',true);
		$fullpath = $path.'/freepbx_sipstation_check';
		$cron = '@daily [ -x '.$fullpath.' ] && '.$fullpath;
		\FreePBX::Cron()->addLine($cron);
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

	public function ajaxRequest($req, &$setting) {
		if ($req == "setupKeyCode") {
			$setting['authenticate'] = true;
			$setting['allowremote'] = false;
			return true;
		}
		return false; // Returning false, or anything APART from (bool) true will abort the request
	}

	public function ajaxHandler() {
		$req = $_REQUEST;
		$ret = array("status" => false, "message" => "");
		if ($req['command'] == "setupKeyCode") {
			$path = \FreePBX::Config()->get('AMPWEBROOT') . '/admin/modules/core/functions.inc.php';

			if (\file_exists($path)) {
				$ret["message"] = _("Unable to complete your request because Core is not being found");
			}

			include_once($path);
			// TOOD: provide feedback if they give blank blank key, maybe just js validation?
			$account_key = $req["account_key"];
			if (empty($account_key)) {
				$ret["message"] = _("Invalid Account Key Code provided");
				return $ret;
			}

			$set_key_status = $this->ss->set_key($account_key);
			$data = $this->ss->get_config($account_key);
			$this->ss->add_routes($data['sip_username'],false);

			$ret["status"] = true;
			return $ret;
			return;
		}
		echo $ret;
	}

	/**
	 * Process the UCP Admin Page on submission
	 * @param {array} $user An array of submitted user data
	 */
	public function processUCPAdminDisplay($user) {
		if(!empty($_REQUEST['ucp|sipstation-sms-did']) && $this->smsEnabled() && $this->freepbx->Modules->checkStatus('sms')) {
			$this->freepbx->Sms->addUserRouting($user['id'],$_REQUEST['ucp|sipstation-sms-did']);
		} elseif($this->freepbx->Modules->checkStatus('sms')) {
			$this->freepbx->Sms->addUserRouting($user['id'],array());
		}
	}

	/**
	 * Get all Active DIDs for this Account
	 * @param {bool} $online       = true  Whether to force an online check
	 * @param {bool} $skiptollfree = false Whether to skip tollfree numbers
	 */
	public function getDIDs($online = true, $skiptollfree = false) {
		$key = $this->ss->get_key();
		if(!empty($key)) {
			$c = $this->ss->get_config($key, $online);
			if(!empty($c['dids'])) {
				if($skiptollfree) {
					$final = array();
					foreach($c['dids'] as $did) {
						if(!preg_match($this->tollfree,$did['did'])) {
							$final[] = $did;
						}
					}
				} else {
					$final = $c['dids'];
				}
				return $final;
			}
		}
		return array();
	}
	public static function myDialplanHooks() {
		return true;
	}

	public function doDialplanHook(&$ext, $engine, $priority) {
		$ssapp = 'sipstation-welcome';
		$ext->add($ssapp, '_X.', '', new \ext_set('ISNUM','${REGEX("[0-9]" ${CALLERID(number)})}'));
		$ext->add($ssapp, '_X.', '', new \ext_answer(''));
		$ext->add($ssapp, '_X.', '', new \ext_wait(1));
		$ext->add($ssapp, '_X.', '', new \ext_playback('you-have-reached-a-test-number&silence/1'));
		$ext->add($ssapp, '_X.', '', new \ext_saydigits('${EXTEN}'));
		$ext->add($ssapp, '_X.', '', new \ext_playback('&your&calling&from&silence/1'));
		$ext->add($ssapp, '_X.', '', new \ext_gotoif('$["${ISNUM}" = "1"]','valid','notvalid'));
		$ext->add($ssapp, '_X.', 'valid', new \ext_saydigits('${CALLERID(number)}'));
		$ext->add($ssapp, '_X.', '', new \ext_hangup());
		$ext->add($ssapp, '_X.', 'notvalid', new \ext_playback('unavailable&number'));
		$ext->add($ssapp, '_X.', '', new \ext_hangup());
	}

	/**
	 * Get the UCP Display page
	 * @param {array} $user An Array containing user information
	 */
	public function getUCPAdminDisplay($user) {
		if($this->smsEnabled() && $this->freepbx->Modules->checkStatus('sms')) {
			$dids = $this->getDIDs(false,true);
			if(!empty($dids)) {
				$html['description'] = '<a href="#" class="info">'._("SIPStation SMS DIDs").':<span>'._("These are the SMS DIDs that are assigned to this user for use in UCP").'</span></a>';
				$html['content'] = load_view(dirname(__FILE__)."/views/ucp_config.php",array("dids" => $dids, "assigned" => $this->freepbx->Sms->getAssignedDIDs($user['id'])));
			}
		}
		return $html;
	}

	/**
	 * Send the adaptor if needed
	 */
	public function smsAdaptor() {
		include(__DIR__.'/functions.inc/SipstationSMS.class.php');
		return \FreePBX\modules\Sms\Adaptor\Sipstation::Create();
	}

	/**
	 * Check if SMS is enabled on the SIPStation Servers
	 */
	private function smsEnabled() {
		$key = $this->ss->get_key();
		if(!empty($key)) {
			$c = $this->ss->get_config($key, false);
			if(!empty($c['server_settings']['sms'])) {
				return true;
			}
		}
		return false;
	}

	public function O() {
		if (!self::$oobeobj) {
			if (!class_exists('FreePBX\\modules\\Sipstation\\Oobe')) {
				include __DIR__."/Oobe.class.php";
			}
			self::$oobeobj  = new \FreePBX\modules\Sipstation\Oobe();
		}
		return self::$oobeobj;
	}

	public function oobeHook() {
		try {
			return $this->O()->showOobe();
		} catch (\Exception $e) {
			// Woah. It broke. Mark it as broken, so it gets reset later.
			$o = \FreePBX::OOBE()->getConfig('crashed');
			if (!is_array($o)) {
				$o = array("sipstation" => array("time" => time()));
			} else {
				$o['sipstation'] = array("time" => time());
			}
			\FreePBX::OOBE()->setConfig('crashed', $o);
			return true;
		}
	}

}
