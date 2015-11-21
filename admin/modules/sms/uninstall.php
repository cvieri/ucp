<?php
//	License for all code of this FreePBX module can be found in the license file inside the module directory
//	Copyright 2013 Schmooze Com Inc.
//
$sql = "DROP TABLE sms_messages";
FreePBX::Database()->query($sql);

$sql = "DROP TABLE sms_routing";
FreePBX::Database()->query($sql);
