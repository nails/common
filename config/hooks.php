<?php

/* Load the base config file from the CodeIgniter package */
require FCPATH . 'vendor/codeigniter/framework/application/config/hooks.php';

//  Set Nails up as early as possible
$hook['pre_system'] = array(
    'class'    => '\Nails\Startup',
    'function' => 'init',
    'filename' => 'Startup.php',
    'filepath' => NAILS_PATH . 'common/src/'
);
