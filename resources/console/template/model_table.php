<?php

/**
 * This file is used for the query used to generate new model tables
 * Used by the console command when creating models.
 */

return <<<'EOD'
CREATE TABLE `{{TABLE_WITH_PREFIX}}` (
    `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
    `slug` varchar(150) DEFAULT NULL,
    `label` varchar(150) DEFAULT NULL,
    `is_deleted` tinyint(1) unsigned NOT NULL DEFAULT '0',
    `created` datetime NOT NULL,
    `created_by` int(11) unsigned DEFAULT NULL,
    `modified` datetime NOT NULL,
    `modified_by` int(11) unsigned DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `created_by` (`created_by`),
    KEY `modified_by` (`modified_by`),
    CONSTRAINT `{{TABLE_WITH_PREFIX}}_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `{{NAILS_DB_PREFIX}}user` (`id`) ON DELETE SET NULL,
    CONSTRAINT `{{TABLE_WITH_PREFIX}}_ibfk_2` FOREIGN KEY (`modified_by`) REFERENCES `{{NAILS_DB_PREFIX}}user` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
EOD;
