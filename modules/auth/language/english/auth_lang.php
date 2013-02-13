<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Name:		Authentication - LANGUAGE
 *
 * Description:	English language file for Authentication messages and errors (outside Ion Auth)
 * 
 **/

/**
 * General
 * 
 **/

$lang['login_ok_welcome']					= '<strong>Welcome, %s!</strong>&nbsp; You last logged in %s.';
$lang['no_access_already_logged_in']		= '<strong>Sorry,</strong> you can\'t access that page while logged in (currently logged in as <strong>%s</strong>).';
$lang['no_access_bad_data']					= '<strong>Sorry,</strong> the link you clicked on seems to be invalid. Please contact technical support.';


/**
 * Forgotten password
 * 
 **/
 
//	Resetting
$lang['forgotten_password_success']			= '<strong>Reset token sent!</strong> Please check your email remembering to look in junk and spam folders.';
$lang['forgotten_password_email_fail']		= '<strong>Sorry,</strong> there was a problem sending the email with your reset link. Please try again.';
$lang['forgotten_password_code_not_set']	= '<strong>Sorry,</strong> we were unable to generate a token for the email address <strong>%s</strong>.';

//	Validating
$lang['forgotten_password_expired_code']	= '<strong>Sorry,</strong> the reset token you are using has expired. You will need to resubmit a password reset request.';
$lang['forgotten_password_invalid_code']	= 'Invalid or expired password reset token.';


//	Errors
$lang['register_error']						= 'There were errors. Please see below for details.';



/**
 * Form validation
 * 
 **/
 
$lang['required']							= 'This field is required.';
$lang['required_reset']						= 'Both fields are required.';
$lang['valid_email']						= 'You must provide a valid email.';
$lang['min_length']							= 'Password must be at least %2$s characters long.';
$lang['min_length_change_temp']				= 'Your new password cannot be shorter than %2$s characters.';


/**
 * Form Errors
 * 
 **/
 
$lang['required_field']						= 'This field is required';
$lang['alpha_dash_space_accent']			= 'Invalid characters (only a-z, 0-9, spaces, dashes and underscores)';
$lang['matches']							= 'This field does not match the %2$s field.';
$lang['is_unique']							= 'The email address you entered is already in use. <a href="%s">Forgotten your password?</a>';
$lang['integer']							= 'This field must be an integer';
$lang['max_length']							= 'This field cannot exceed %2$s characters in length';
$lang['min_length']							= 'This field cannot be less than %2$s characters in length';


/**
 * Form Actions
 * 
 **/
 
$lang['action_reset_continue']				= 'Change Password & Log In';
$lang['action_proceed_login']				= 'Proceed to Log In';


/**
 * Error messages
 * 
 **/
 
$lang['login_fail_missing_field']				= '<strong>Sorry,</strong> a required field was missing.';
$lang['login_fail_general']						= '<strong>Sorry,</strong> your log in has not been successful, please try again.';
$lang['login_fail_banned']						= '<strong>This account has been banned;</strong> if you feel you have received this message in error then please contact us.';
$lang['login_fail_blocked']						= '<strong>This account has been temporarily blocked due to repeated failed logins.<strong> Please wait %s minutes before trying again (each failed login resets the block). ';
$lang['login_fail_social']						= 'This account was created using a social network; either login via the appropriate button or <a href="%s">click here to set a password</a> using the Forgotten Password tool.';
$lang['login_fail_social_fb']					= 'This account was created using Facebook; either login via the Facebook button or <a href="%s">click here to set a password</a> using the Forgotten Password tool.';
$lang['login_fail_social_in']					= 'This account was created using LinkedIn; either login via the LinkedIn button or <a href="%s">click here to set a password</a> using the Forgotten Password tool.';
$lang['logout_successful']						= '<strong>Goodbye!</strong> You have been logged out successfully.';