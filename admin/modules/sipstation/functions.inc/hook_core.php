<?php
if (!defined('FREEPBX_IS_AUTH')) { die('No direct script access allowed'); }
//	License for all code of this FreePBX module can be found in the license file inside the module directory
//	Copyright 2013 Schmooze Com Inc.
//
function sipstation_hook_core($viewing_itemid, $target_menuid) {
	if ($target_menuid == 'did' && FreePBX::Modules()->checkStatus('userman'))	{
		$users = FreePBX::Userman()->getAllUsers();
		if(empty($users)) {
			return '';
		}
		$display = isset($_REQUEST['display'])?$_REQUEST['display']:null;
		$action = isset($_REQUEST['action'])?$_REQUEST['action']:null;
		$vdid = isset($_REQUEST['extdisplay'])?$_REQUEST['extdisplay']:null;
		$ss = FreePBX::Sipstation();
		$good = false;
		$d = explode("/",$vdid);
		$vdid = isset($d[0]) ? $d[0] : '';
		if(empty($vdid)) {
			return '';
		}
		foreach($ss->getDIDs(false,true) as $did) {
			if($vdid == $did['did']) {
				$good = true;
				break;
			}
		}
		$html = '';
		if($good && FreePBX::Modules()->checkStatus('sms')) {
			$routing = FreePBX::Sms()->getAssignedUsers($vdid);
			$html = '<tr><td colspan="2"><h5>';
			$html .= _("SIPStation");
			$html .= '<hr></h5></td></tr>';
			$html .= '<tr>';
			$html .= '<td><a href="#" class="info">';
			$html .= _("SMS/User Assignment").'<span>'._("Assign which users will get SMSes routed through this inbound route").'.</span></a>:</td>';
			$html .= '<td><div class="users-list">';
			foreach($users as $user) {
				$checked = in_array($user['id'],$routing) ? 'checked' : '';
				$html .= '<label><input class="sms-user-checkbox" type="checkbox" name="smsusercheckbox[]" value="'.$user['id'].'" '.$checked.'>'.$user['username'].'</label><br>';
			}
			$html .= '</div><style>.users-list { border: 1px black dotted;padding: 2px;overflow: auto;height: 150px;font-size: 85%;min-width: 200px; }</style></td>';
		}
		return $html;
	}
}

function sipstation_hookProcess_core($viewing_itemid, $request) {
	$did = isset($request['extension'])?$request['extension']:null;
	$users = isset($request['smsusercheckbox'])?$request['smsusercheckbox']:array();
	if($_REQUEST['display'] == 'did' && isset($request['Submit']) && FreePBX::Modules()->checkStatus('sms') && !empty($did)) {
		FreePBX::Sms()->addDIDRouting($did,$users);
	}
}

function sipstation_destinations(){
	$extens[] = array(
		'destination' => 'sipstation-welcome,${EXTEN},1',
		'description' => _("DID Verification"),
		'category' => _('Sipstation'),
	);
	return $extens;
}
