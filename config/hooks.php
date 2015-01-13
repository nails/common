<?php

/*
| -------------------------------------------------------------------------
| Hooks
| -------------------------------------------------------------------------
| This file lets you define "hooks" to extend CI without hacking the core
| files. Please see the user guide for info:
|
| http://codeigniter.com/user_guide/general/hooks.html
|
*/

    if (is_file(FCPATH . APPPATH . '/hooks/')) {

        $path = FCPATH . APPPATH . '/hooks/';

    } else {

        $path = NAILS_PATH . 'common/hooks/';
    }

    $hook['pre_system'] = array(
        'class'     => 'System_startup',
        'function'  => 'init',
        'filename'  => 'System_startup.php',
        'filepath'  => $path,
    );
