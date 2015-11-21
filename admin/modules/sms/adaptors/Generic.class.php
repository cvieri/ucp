<?php
//	License for all code of this FreePBX module can be found in the license file inside the module directory
//	Copyright 2013 Schmooze Com Inc.
//
namespace FreePBX\modules\Sms\Adaptor;
class Generic extends \FreePBX\modules\Sms\AdaptorBase {

	/**
	* Create the Notification Class statically while checking to make sure the class hasn't already been loaded
	*
	* @param object Database Object
	* @return object Notification object
	*/
	function &create() {
		static $obj;
		if (!isset($obj)) {
			$obj = new Generic();
		}
		return $obj;
	}
}
