<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| Auth Variables
| -------------------------------------------------------------------------
|
| Control aspects of auth at the app level with this config file.
|
| Full details of configurable options are available at
| TODO: Link to docs
|
*/


$config['auth_two_factor_num_questions']		= 1;
$config['auth_two_factor_num_custom_question']	= 0;
$config['auth_two_factor_questions']			= array();
$config['auth_two_factor_questions'][]			= 'What was your childhood nickname? ';
$config['auth_two_factor_questions'][]			= 'In what city did you meet your spouse/significant other?';
$config['auth_two_factor_questions'][]			= 'What is the name of your favorite childhood friend? ';
$config['auth_two_factor_questions'][]			= 'What is the middle name of your oldest child?';
$config['auth_two_factor_questions'][]			= 'What is your oldest sibling\'s middle name?';
$config['auth_two_factor_questions'][]			= 'What was your childhood phone number including area code?';
$config['auth_two_factor_questions'][]			= 'What is your oldest cousin\'s first and last name?';
$config['auth_two_factor_questions'][]			= 'What was the name of your first stuffed animal?';
$config['auth_two_factor_questions'][]			= 'In what city or town did your mother and father meet? ';
$config['auth_two_factor_questions'][]			= 'Where were you when you had your first kiss? ';
$config['auth_two_factor_questions'][]			= 'What is the first name of the boy or girl that you first kissed?';
$config['auth_two_factor_questions'][]			= 'In what city does your nearest sibling live? ';
$config['auth_two_factor_questions'][]			= 'What is your oldest sibling\'s birthday month and year? (e.g., January 1900) ';
$config['auth_two_factor_questions'][]			= 'What is your oldest brother\'s birthday month and year? (e.g., January 1900) ';
$config['auth_two_factor_questions'][]			= 'What is your oldest sister\'s birthday month and year? (e.g., January 1900) ';
$config['auth_two_factor_questions'][]			= 'What is your maternal grandmother\'s maiden name?';
$config['auth_two_factor_questions'][]			= 'In what city or town was your first job?';
$config['auth_two_factor_questions'][]			= 'What is the name of the place your wedding reception was held?';
$config['auth_two_factor_questions'][]			= 'What is the name of a college or university you applied to but didn\'t attend?';
$config['auth_two_factor_questions'][]			= 'Where were you when you first heard about 9/11?';