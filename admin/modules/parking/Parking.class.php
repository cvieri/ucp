<?php
// vim: set ai ts=4 sw=4 ft=php:

class Parking implements BMO {
	public function __construct($freepbx = null) {
		if ($freepbx == null)
			throw new Exception("Not given a FreePBX Object");

		$this->FreePBX = $freepbx;
		$this->db = $freepbx->Database;
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
	public function genConfig() {
		global $version;

		if (function_exists('parkpro_get_config')) {
			return null;
		}

		if(version_compare($version, '12', 'ge')) {
			$lot = parking_get();
			$parkpos1	= $lot['parkpos'];
			$parkpos2	= $parkpos1 + $lot['numslots'] - 1;
			$park_context = 'default';
			$hint_context = 'parkedcalls';
			$conf['res_parking.conf'][$park_context] = array(
				'parkext' => $lot['parkext'],
				'parkpos' => $parkpos1."-".$parkpos2,
				'context' => $hint_context,
				'parkingtime' => $lot['parkingtime'],
				'comebacktoorigin' => 'no',
				'parkedplay' => $lot['parkedplay'],
				'courtesytone' => 'beep',
				'parkedcalltransfers' => $lot['parkedcalltransfers'],
				'parkedcallreparking' => $lot['parkedcallreparking'],
				'parkedmusicclass' => $lot['parkedmusicclass'],
				'findslot' => $lot['findslot']
			);
			return $conf;
		}
	}
	public function writeConfig($conf){
		$this->FreePBX->WriteConfig($conf);
	}
}
