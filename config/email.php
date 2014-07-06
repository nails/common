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
$config['smtp_host']	= '';
$config['smtp_pass']	= '';
$config['smtp_user']	= '';
$config['smtp_port']	= '';