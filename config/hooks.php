<?php

/**
 *  "Hook" into CodeIgniter's core so that we can run some early, pre-system configurations.
 */

$hook['pre_system'] = array(
    'class'     => '\Nails\Startup',
    'function'  => 'init',
    'filename'  => 'Startup.php',
    'filepath'  => NAILS_PATH . 'common/src/',
);
