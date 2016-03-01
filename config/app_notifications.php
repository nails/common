<?php

/**
 * This config file defines app notifications for this module.
 *
 * @package     Nails
 * @subpackage  common
 * @category    Config
 * @author      Nails Dev Team
 * @link
 */

$config['notification_definitions'] = array(
    'app' => (object) array(
        'slug'        => 'app',
        'label'       => 'Site',
        'description' => 'General site notifications',
        'options'     => array(
            'orders' => (object) array(
                'slug'          => 'default',
                'label'         => 'Generic',
                'sub_label'     => '',
                'tip'           => '',
                'email_subject' => '',
                'email_tpl'     => '',
                'email_message' => ''
            )
        )
    )
);