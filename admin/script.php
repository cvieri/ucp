<?php
//$FilePath = '/etc/asterisk/voicemail.conf';
/*$_REQUEST['extdisplay'] = $_POST['extdisplay'];
$_REQUEST['name'] = $_POST['name'];
$_REQUEST['email'] = $_POST['email'];
$_REQUEST['pwd'] = $_POST['pwd'];
$_REQUEST['action'] = 'add';
$_REQUEST['vm'] = 'enabled';*/

$_REQUEST['extension'] = $_POST['extension'];
$_REQUEST['name'] = $_POST['name'];
$_REQUEST['vmpwd'] = $_POST['vmpwd'];
$_REQUEST['vm'] = 'enabled';
$_REQUEST['action'] = 'add';
$_REQUEST['passlogin'] = 'passlogin=yes';
$_REQUEST['attach'] = 'attach=no';
$_REQUEST['saycid'] = 'saycid=no';
$_REQUEST['envelope'] = 'envelope=no';
$_REQUEST['delete'] = 'delete=no';
$_REQUEST['vmcontext'] = 'default';

require_once(dirname(__FILE__).'/libraries/ampuser.class.php');

$amp_conf['AMPDBUSER']	= 'asteriskuser';
$amp_conf['AMPDBPASS']	= 'amp109';
$amp_conf['AMPDBHOST']	= 'localhost';
$amp_conf['AMPDBNAME']	= 'asterisk';
$amp_conf['AMPDBENGINE'] = 'mysql';
$amp_conf['datasource']	= '';

//include_once('/etc/asterisk/freepbx.conf');

define('FREEPBX_IS_AUTH', 'TRUE');
require_once('/var/www/html/admin/bootstrap.php');

$modpath = $amp_conf['AMPWEBROOT'] . '/admin/modules/';
$file = $modpath .'voicemail/functions.inc.php';
require_once($file);
voicemail_configprocess();

$file1 = $modpath .'core/functions.inc.php';
require_once($file1);
core_users_configprocess();

//voicemail_mailbox_del($extdisplay);
//voicemail_mailbox_add($extdisplay, $RequestArray);

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

