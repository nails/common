<?php

/*
| -------------------------------------------------------------------
| Email Settings
| -------------------------------------------------------------------
| This file contains the configuration to be used by the email classes
|
*/

$config['mailpath']		= '/usr/sbin/sendmail';
$config['charset']		= 'utf-8';
$config['wordwrap']		= true;
$config['validate']		= true;
$config['mailtype']		= 'html';
$config['protocol']		= 'smtp';
$config['newline']		= "\r\n";
$config['smtp_host']	= defined('EMAIL_HOST') ? EMAIL_HOST : '127.0.0.1';
$config['smtp_pass']	= defined('EMAIL_PASSWORD') ? EMAIL_PASSWORD : '';
$config['smtp_user']	= defined('EMAIL_USERNAME') ? EMAIL_USERNAME : '';
$config['smtp_port']	= defined('EMAIL_PORT') ? EMAIL_PORT : '25';
