# INITIAL NAILS DB
# This is the schema of the Nails database as of 09/01/2015
DROP TABLE IF EXISTS `nails_session`;
CREATE TABLE `nails_session` (`session_id` varchar(40) NOT NULL DEFAULT '0', `ip_address` varchar(45) NOT NULL DEFAULT '0', `user_agent` varchar(120) NOT NULL, `last_activity` int(10) unsigned NOT NULL DEFAULT '0', `user_data` text NOT NULL, PRIMARY KEY (`session_id`), KEY `last_activity_idx` (`last_activity`)) ENGINE=InnoDB DEFAULT CHARSET=utf8;