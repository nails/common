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
$config['smtp_host']	= DEPLOY_SMTP_HOST;
$config['smtp_pass']	= DEPLOY_SMTP_PASSWORD;
$config['smtp_user']	= DEPLOY_SMTP_USERNAME;
$config['smtp_port']	= DEPLOY_SMTP_PORT;