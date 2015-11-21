<?php
namespace FreePBX\modules\Sipstation;

class Oobe {

	private $url = "https://katanafpbx.schmoozecom.com";
	#private $url = "http://203.4.240.10";

	public static function genUuid() {
		return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
			mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),
			mt_rand( 0, 0xffff ),
			mt_rand( 0, 0x0fff ) | 0x4000,
			mt_rand( 0, 0x3fff ) | 0x8000,
			mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
		);
	}

	public function kPost($section, $vars = array()) {
		$pest = new \Pest($this->url);
		$pest->curl_opts[\CURLOPT_CONNECTTIMEOUT] = 3;
		$pest->curl_opts[\CURLOPT_SSL_VERIFYPEER] = true;
		// TODO: Set proxy stuff here
		if ($section[0] != '/') {
			$section = "/$section";
		}
		$vars['sysid'] = json_encode($this->getSysVars($vars));

		$resp = $pest->post($section, $vars);
		$output = @json_decode($resp, true);
		if (!is_array($output)) {
			throw new \Exception("I received '$resp', I sent ".json_encode($vars)." to $url/$section");
		}
		return $output;
	}

	private function getSysVars() {
		$vars = array();
		if (file_exists("/etc/schmooze/pbxid")) {
			$vars["pbxid"] = file_get_contents("/etc/schmooze/pbxid");
		} else {
			$vars['pbxid'] = false;
		}
		if (function_exists('zend_get_id')) {
			$vars['zend'] = zend_get_id();
		} else {
			$vars['zend'] = "nozend";
		}
		$vars['modules'] = array();
		$allmods = \FreePBX::Modules()->getActiveModules();
		foreach ($allmods as $name => $arr) {
			$vars['modules'][$name] = $arr['version'];
		}

		return $vars;
	}

	public function getOobePage($vars) {
		$res = $this->kPost("/oobe/sipstation", $vars);
		return $res;
	}

	public function showOobe() {
		$vars = $_REQUEST;

		// If there's a keycode then don't make an OOBE offer.
		//
		$ss = new \sipstation();
		if (($key = $ss->get_key()) !== false) {
			return true;
		}

		// Grab our persistent variables
		$p = \FreePBX::OOBE()->getConfig("persist");
		$vars['persist'] = json_encode($p);

		// Now ask Katana for the page
		$page = $this->getOobePage($vars);
		//print "<pre>\n";print_r($page);print"</pre>\n";

		// Handle commands passed back
		// 'update' = array(
		//     "ampconf" => array("FREEPBX_AMP_VAR" => "blurble", "OTHER_AMPCONF_VAR" => "blech"),
		//     "persist" => array("whatever...")
		//  );
		if (isset($page['update'])) {
			$u = $page['update'];
			if (is_array($u['ampconf'])) {
				 \FreePBX::Config()->set_conf_values($u['ampconf'], true, true);
			}
			if (is_array($u['persist'])) {
				if (!is_array($p)) {
					// Creating the persist var
					\FreePBX::OOBE()->setConfig("persist", $u['persist']);
				} else {
					// Overwrite the current with the new
					\FreePBX::OOBE()->setConfig("persist", array_merge($p, $u['persist']));
				}
			}
		}

		// Now, is there anything left to do?
		if (!$page['count']) {
			// Nothing more to do. Mark this OOBE as complete.
			return true;
		}

		$guid = $page['guid'];
		$html = "\n<form method='post' id='oobepost'>\n  <input type='hidden' name='oobesubmit' value='true'>\n";
		$html .= "  <input type='hidden' name='guid' value='$guid'>\n";
		$html .= "  <input type='hidden' name='thisoffer' value='".$page['thisoffer']."'>\n";
		// Replace any %%VARS%% returned with our config variable.
		if (preg_match_all('/%%([\w_]+)%%/m', $page['offer']['html'], $out)) {
			$search = array();
			$replace = array();
			foreach ($out[1] as $match) {
				$search[] = "%%$match%%";
				$replace[] = \FreePBX::Config()->get($match);
			}
			$html .= str_replace($search, $replace, $page['offer']['html']);
		} else {
			$html .= $page['offer']['html'];
		}
		$html .= "</form>";
		if (isset($page['offer']['js'])) {
			$html .= "\n<script type='text/javascript'>\n".$page['offer']['js']."</script>";
		}
		$html .= "<script type='text/javascript'> window.sentvars = ".json_encode($vars)."; </script>\n";

		print $html;

		return false;
	}

	public function restartOobe() {
		$o = \FreePBX::OOBE();
		$c = $o->getConfig('completed');
		unset($c['sipstation']);
		$o->setConfig('completed', $c);
		$p = $o->getConfig('persist');
		if (!is_array($p)) {
			$p = array('restartoobe' => true);
		} else {
			$p['restartoobe'] = true;
		}
		$o->setConfig('persist', $p);

		return $c;
	}
}
