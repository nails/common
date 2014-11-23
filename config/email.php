<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------
| Email Settings
| -------------------------------------------------------------------
| This file contains the configuration to be used by the email classes
|
*/

$config['mailpath']		= '/usr/sbin/sendmail';
$config['charset']		= 'utf-8';
$config['wordwrap']		= TRUE;
$config['validate']		= TRUE;
$config['mailtype']		= 'html';
$config['protocol']		= 'smtp';
$config['newline']		= "\r\n";
$config['smtp_host']	= defined('DEPLOY_EMAIL_HOST') ? DEPLOY_EMAIL_HOST : '127.0.0.1';
$config['smtp_pass']	= defined('DEPLOY_EMAIL_PASS') ? DEPLOY_EMAIL_PASS : '';
$config['smtp_user']	= defined('DEPLOY_EMAIL_USER') ? DEPLOY_EMAIL_USER : '';
$config['smtp_port']	= defined('DEPLOY_EMAIL_PORT') ? DEPLOY_EMAIL_PORT : '25';