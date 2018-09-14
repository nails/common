<?php

/**
 * This config file defines email types for this module.
 *
 * @package     Nails
 * @subpackage  common
 * @category    Config
 * @author      Nails Dev Team
 * @link
 */

$config['email_types'] = array(
    (object) array(
        'slug'             => 'app_notification',
        'name'             => 'Generic: App Notification',
        'description'      => 'Email template used by the App Notification system.',
        'template_header'  => '',
        'template_body'    => 'email/app_notification',
        'template_footer'  => '',
        'default_subject'  => 'App Notification'
    )
);
