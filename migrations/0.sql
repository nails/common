# INITIAL NAILS DB
# This is the schema of the Nails database as of 09/01/2015
DROP TABLE IF EXISTS `{{NAILS_DB_PREFIX}}app_notification`;
CREATE TABLE `{{NAILS_DB_PREFIX}}app_notification` (`id` int(11) unsigned NOT NULL AUTO_INCREMENT, `grouping` varchar(100) NOT NULL, `key` varchar(50) DEFAULT NULL, `value` text, PRIMARY KEY (`id`), KEY `grouping` (`grouping`), KEY `grouping_2` (`grouping`,`key`)) ENGINE=InnoDB DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `{{NAILS_DB_PREFIX}}app_setting`;
CREATE TABLE `{{NAILS_DB_PREFIX}}app_setting` (`id` int(11) unsigned NOT NULL AUTO_INCREMENT, `grouping` varchar(100) NOT NULL, `key` varchar(50) DEFAULT NULL, `value` text, `is_encrypted` tinyint(1) unsigned NOT NULL DEFAULT '0', PRIMARY KEY (`id`), KEY `grouping` (`grouping`), KEY `grouping_2` (`grouping`,`key`)) ENGINE=InnoDB DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `{{NAILS_DB_PREFIX}}session`;
CREATE TABLE `{{NAILS_DB_PREFIX}}session` (`session_id` varchar(40) NOT NULL DEFAULT '0', `ip_address` varchar(45) NOT NULL DEFAULT '0', `user_agent` varchar(120) NOT NULL, `last_activity` int(10) unsigned NOT NULL DEFAULT '0', `user_data` text NOT NULL, PRIMARY KEY (`session_id`), KEY `last_activity_idx` (`last_activity`)) ENGINE=InnoDB DEFAULT CHARSET=utf8;