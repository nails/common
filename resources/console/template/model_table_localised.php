<?php

/**
 * This file is used for the query used to generate new model tables
 * Used by the console command when creating models.
 */

return <<<'EOD'
CREATE TABLE `{{TABLE_WITH_PREFIX}}` (
    `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE `{{TABLE_WITH_PREFIX}}_localised` (
    `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
    `language` varchar(150) DEFAULT NULL,
    `region` varchar(150) DEFAULT NULL,
    `slug` varchar(150) DEFAULT NULL,
    `label` varchar(150) DEFAULT NULL,
    `is_deleted` tinyint(1) unsigned NOT NULL DEFAULT '0',
    `created` datetime NOT NULL,
    `created_by` int(11) unsigned DEFAULT NULL,
    `modified` datetime NOT NULL,
    `modified_by` int(11) unsigned DEFAULT NULL,
    UNIQUE KEY (`id`, `language`, `region`),
    KEY `created_by` (`created_by`),
    KEY `modified_by` (`modified_by`),
    CONSTRAINT `{{TABLE_WITH_PREFIX}}_localised_ibfk_1` FOREIGN KEY (`id`) REFERENCES `{{TABLE_WITH_PREFIX}}` (`id`) ON DELETE CASCADE,
    CONSTRAINT `{{TABLE_WITH_PREFIX}}_localised_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `{{NAILS_DB_PREFIX}}user` (`id`) ON DELETE SET NULL,
    CONSTRAINT `{{TABLE_WITH_PREFIX}}_localised_ibfk_3` FOREIGN KEY (`modified_by`) REFERENCES `{{NAILS_DB_PREFIX}}user` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
EOD;
