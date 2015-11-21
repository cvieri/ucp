<?php
global $amp_conf;
global $_item_sort;

$out = '';
$out .= '<div id="header">';
$out .= '<div class="menubar ui-widget-header ui-corner-all">';
//left hand logo
$out .= '<img src="' . $amp_conf['BRAND_IMAGE_TANGO_LEFT']
        . '" alt="' . $amp_conf['BRAND_FREEPBX_ALT_LEFT']
        . '" title="' . $amp_conf['BRAND_FREEPBX_ALT_LEFT']
        . '" id="MENU_BRAND_IMAGE_TANGO_LEFT" '
		. 'data-BRAND_IMAGE_FREEPBX_LINK_LEFT="' . $amp_conf['BRAND_IMAGE_FREEPBX_LINK_LEFT'] . '"/>';

// If freepbx_menu.conf exists then use it to define/redefine categories
//
if ($amp_conf['USE_FREEPBX_MENU_CONF']) {
  $fd = $amp_conf['ASTETCDIR'].'/freepbx_menu.conf';
  if (file_exists($fd)) {
    $favorites = @parse_ini_file($fd,true);
    if ($favorites !== false) foreach ($favorites as $menuitem => $setting) {
      if (isset($fpbx_menu[$menuitem])) {
        foreach($setting as $key => $value) {
          switch ($key) {
            case 'category':
            case 'name':
              $fpbx_menu[$menuitem][$key] = htmlspecialchars($value);
            break;
            case 'type':
              // this is really deprecated but ???
              if (strtolower($value)=='setup' || strtolower($value)=='tool') {
                $fpbx_menu[$menuitem][$key] = strtolower($value);
              }
            break;
            case 'sort':
              if (is_numeric($value) && $value > -10 && $value < 10) {
                $fpbx_menu[$menuitem][$key] = $value;
              }
            break;
            case 'remove':
              // parse_ini_file sets all forms of yes/true to 1 and no/false to nothing
              if ($value == '1') {
                unset($fpbx_menu[$menuitem]);
              }
            break;
          }
        }
      }
		} else {
			freepbx_log('FPBX_LOG_ERROR', _("Syntax error in your freepbx_menu.conf file"));
		}
  }
}


//For Localization pickup
if(false) {
  _("Admin");
  _("Applications");
  _("Connectivity");
  _("Reports");
  _("Settings");
  _("User Panel");
  _("Other");
}
if (isset($fpbx_menu) && is_array($fpbx_menu)) {	// && freepbx_menu.conf not defined
	if (empty($favorites)) foreach ($fpbx_menu as $mod => $deets) {
		switch(strtolower($deets['category'])) {
			case 'admin':
			case 'applications':
			case 'connectivity':
			case 'reports':
			case 'settings':
			case 'user panel':
				$menu[strtolower($deets['category'])][] = $deets;
				break;
			default:
				$menu['other'][] = $deets;
				break;
		}
  } else {
	  foreach ($fpbx_menu as $mod => $deets) {
			$menu[$deets['category']][] = $deets;
		}
  }

	$count = 0;
	foreach($menu as $t => $cat) { //catagories
    if (count($cat) == 1) {
			if (isset($cat[0]['hidden']) && $cat[0]['hidden'] == 'true') {
				continue;
			}
      $href = isset($cat[0]['href']) ? $cat[0]['href'] : 'config.php?display=' . $cat[0]['display'];
      $target = isset($cat[0]['target']) ? ' target="' . $cat[0]['target'] . '"'  : '';
      $class = $cat[0]['display'] == $display ? 'class="ui-state-highlight"' : '';
      $mods[$t] = '<a href="' . $href . '" ' . $target . $class . '>' . modgettext::_(ucwords($cat[0]['name']),$cat[0]['module']['rawname']) . '</a>';
      continue;
    }
    //Reverse lookup here, first look in amp, then the module, then amp again.
    //This allows us to check special modules that are not defined in Framework
    $catname = _(ucwords($t));
    $catname = ($catname != ucwords($t)) ? $catname : modgettext::_(ucwords($t),$cat[0]['module']['rawname']);
		$mods[$t] = '<a href="#" class="module_menu_button ui-button ui-widget ui-state-default ui-corner-all">'
				. $catname
				. '</a><ul>';
		foreach ($cat as $c => $mod) { //modules
			if (isset($mod['hidden']) && $mod['hidden'] == 'true') {
				continue;
			}
			$classes = array();

			//build defualt module url
			$href = isset($mod['href'])
					? $mod['href']
					: "config.php?display=" . $mod['display'];

      $target = isset($mod['target'])
          ? ' target="' . $mod['target'] . '" '  : '';

			//highlight currently in-use module
			if ($display == $mod['display']) {
				$classes[] = 'ui-state-highlight';
				$classes[] = 'ui-corner-all';
			}

			//highlight disabled modules
			if (isset($mod['disabled']) && $mod['disabled']) {
				$classes[] = 'ui-state-disabled';
				$classes[] = 'ui-corner-all';
			}

			// try the module's translation domain first
			$items[$mod['name']] = '<li><a href="' . $href . '"'
          . $target
					. (!empty($classes) ? ' class="' . implode(' ', $classes) . '">' : '>')
					. modgettext::_($mod['name'], $mod['module']['rawname'])
					. '</a></li>';

       $_item_sort[$mod['name']] = $mod['sort'];
		}
		uksort($items,'_item_sort');
		$mods[$t] .= implode($items) . '</ul>';
		unset($items);
		unset($_item_sort);
	}
	uksort($mods,'_menu_sort');
	$out .= implode($mods);
}
if($amp_conf['SHOWLANGUAGE']) {
	$out .= '<a id="language-menu-button" '
		. 'class="button-right ui-widget-content ui-state-default">' . _('Language') . '</a>';
	$out .= '<ul id="fpbx_lang" style="display:none;">';
	$out .= '<li data-lang="en_US"><a href="#">'. _('English') . '</a></li>';
	$out .= '<li data-lang="bg_BG"><a href="#">' . _('Bulgarian') . '</a></li>';
	$out .= '<li data-lang="zh_CN"><a href="#">' . _('Chinese') . '</a></li>';
	$out .= '<li data-lang="de_DE"><a href="#">' . _('German') . '</a></li>';
	$out .= '<li data-lang="fr_FR"><a href="#">' . _('French') . '</a></li>';
	$out .= '<li data-lang="he_IL"><a href="#">' . _('Hebrew') . '</a></li>';
	$out .= '<li data-lang="hu_HU"><a href="#">' . _('Hungarian') . '</a></li>';
	$out .= '<li data-lang="it_IT"><a href="#">' . _('Italian') . '</a></li>';
	$out .= '<li data-lang="pt_PT"><a href="#">' . _('Portuguese') . '</a></li>';
	$out .= '<li data-lang="pt_BR"><a href="#">' . _('Portuguese (Brasil)') . '</a></li>';
	$out .= '<li data-lang="ru_RU"><a href="#">' . _('Russian') . '</a></li>';
	$out .= '<li data-lang="sv_SE"><a href="#">' . _('Swedish') . '</a></li>';
	$out .= '<li data-lang="es_ES"><a href="#">' . _('Spanish') . '</a></li>';
	$out .= '<li data-lang="ja_JP"><a href="#">' . _('Japanese') . '</a></li>';
	$out .= '</ul>';
}

if ( isset($_SESSION['AMP_user']) && ($authtype != 'none')) {
	$out .= '<a id="user_logout" href="#"'
			. ' class="button-right ui-widget-content ui-state-default" title="logout">'
			. _('Logout') . ': ' . (isset($_SESSION['AMP_user']->username) ? $_SESSION['AMP_user']->username : 'ERROR')
			. '</a>';
}
$out .= '<progress class="button-right" id="ajax_spinner"></progress>';

$out .= '<a id="button_reload" data-button-icon-primary="ui-icon-gear" class="ui-state-error ">'
		. _("Apply Config") .'</a>';

$out .= '</div>';
$out .= '</div>';//header

$out .= '<div id="page_body">';

echo $out;

// key sort but keep Favorites on the far left, Other on the far right
//
function _menu_sort($a, $b) {
  $a = strtolower($a);
  $b = strtolower($b);
  if ($a == 'favorites')
    return false;
  else if ($b == 'favorites')
    return true;
  else if ($a == 'other')
    return true;
  else if ($b == 'other')
    return false;
  else
    return $a > $b;
}

function _item_sort($a, $b) {
  global $_item_sort;

  if (!empty($_item_sort[$a]) && !empty($_item_sort[$a]) && $_item_sort[$a] != $_item_sort[$b])
    return $_item_sort[$a] > $_item_sort[$b];
  else
    return $a > $b;
}
?>