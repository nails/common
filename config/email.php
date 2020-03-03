<?php

use Nails\Config;

/*
| -------------------------------------------------------------------
| Email Settings
| -------------------------------------------------------------------
| This file contains the configuration to be used by the email classes
|
*/

$config['mailpath']  = '/usr/sbin/sendmail';
$config['charset']   = 'utf-8';
$config['wordwrap']  = true;
$config['validate']  = true;
$config['mailtype']  = 'html';
$config['protocol']  = 'smtp';
$config['newline']   = "\r\n";
$config['smtp_host'] = Config::get('EMAIL_HOST');
$config['smtp_pass'] = Config::get('EMAIL_PASSWORD');
$config['smtp_user'] = Config::get('EMAIL_USERNAME');
$config['smtp_port'] = Config::get('EMAIL_PORT');
