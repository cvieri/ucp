<?php
//	License for all code of this FreePBX module can be found in the license file inside the module directory
//	Copyright 2013 Schmooze Com Inc.
//
out('Creating SMS Message Table');
$sql = "CREATE TABLE IF NOT EXISTS `sms_messages` (
	`id`int(11) NOT NULL AUTO_INCREMENT,
	`from` varchar(20) NOT NULL,
	`to` varchar(20) NOT NULL,
	`cnam` VARCHAR(40) NULL,
	`direction` enum('in','out'),
	`tx_rx_datetime` datetime,
	`body` text NOT NULL,
	`delivered` int(1) DEFAULT '0',
	`read` int(1) DEFAULT '0',
	PRIMARY KEY (`id`),
	FULLTEXT KEY `TEXT` (`body`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;";

FreePBX::Database()->query($sql);

out('Creating SMS Routing Table');
$sql = "CREATE TABLE IF NOT EXISTS `sms_routing` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `did` varchar(45) NOT NULL,
  `uid` int(11) NOT NULL,
  `accepter` varchar(45) DEFAULT NULL,
  `adaptor` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
";
FreePBX::Database()->query($sql);
