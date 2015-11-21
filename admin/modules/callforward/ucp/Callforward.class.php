<?php
/**
 * This is the User Control Panel Object.
 *
 * Copyright (C) 2013 Schmooze Com, INC
 * Copyright (C) 2013 Andrew Nagy <andrew.nagy@schmoozecom.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package   FreePBX UCP BMO
 * @author   Andrew Nagy <andrew.nagy@schmoozecom.com>
 * @license   AGPL v3
 */
namespace UCP\Modules;
use \UCP\Modules as Modules;

class Callforward extends Modules{
	protected $module = 'Callforward';

	function __construct($Modules) {
		$this->Modules = $Modules;
	}

	public function getSettingsDisplay($ext) {
		$displayvars = array(
			"ringtime" => $this->UCP->FreePBX->Callforward->getRingtimerByExtension($ext),
			"CFU" => $this->UCP->FreePBX->Callforward->getNumberByExtension($ext,'CFU'),
			"CFB" => $this->UCP->FreePBX->Callforward->getNumberByExtension($ext,'CFB'),
			"CF" => $this->UCP->FreePBX->Callforward->getNumberByExtension($ext,'CF'),
		);
		for($i = 1;$i<=120;$i++) {
			$displayvars['cfringtimes'][$i] = $i;
		}
		$out = array(
			array(
				"title" => _('Call Forwarding'),
				"content" => $this->load_view(__DIR__.'/views/settings.php',$displayvars),
				"size" => 6
			)
		);
		return $out;
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
		if(!$this->_checkExtension($_POST['ext'])) {
			return false;
		}
		switch($command) {
			case 'settings':
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
	function ajaxHandler() {
		$return = array("status" => false, "message" => "");
		switch($_REQUEST['command']) {
			case 'settings':
				if(isset($_POST['ringtimer'])) {
					$this->UCP->FreePBX->Callforward->setRingtimerByExtension($_POST['ext'],$_POST['ringtimer']);
				}
				if(isset($_POST['type'])) {
					if(!empty($_POST['number'])) {
						$this->UCP->FreePBX->Callforward->setNumberByExtension($_POST['ext'],$_POST['number'],$_POST['type']);
					} else {
						$this->UCP->FreePBX->Callforward->delNumberByExtension($_POST['ext'],$_POST['type']);
					}
				}
				return array("status" => true, "alert" => "success", "message" => _('Call Forwarding Has Been Updated!'));
				break;
			default:
				return $return;
			break;
		}
	}

	private function _checkExtension($extension) {
		$user = $this->UCP->User->getUser();
		return in_array($extension,$user['assigned']);
	}
}
