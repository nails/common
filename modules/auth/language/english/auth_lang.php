<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* Name:			Authentication - LANGUAGE
*
* Docs:			-
*
* Created:		19/11/2010
* Modified:		04/01/2012
*
* Description:  English language file for Authentication messages and errors (outside Ion Auth)
* 
*/

/**
 * General
 * 
 **/

$lang['login_ok_welcome']					= '<strong>Welcome, %s!</strong>&nbsp; You last logged in %s.';
$lang['no_access_already_logged_in']		= '<strong>Sorry,</strong> you can\'t access that page while logged in (currently logged in as <strong>%s</strong>).';
$lang['no_access_bad_data']					= '<strong>Sorry,</strong> the link you clicked on seems to be invalid. Please contact technical support.';

/**
 * Login
 * 
 **/
 
$lang['login_link']							= 'Already Registered? <a href="%s">Login here</a>';
$lang['login_title']						= 'Please Log In';
$lang['login_link_forgotten_password']		= 'Forgotten your Password?';
$lang['login_link_register']				= 'Register';
$lang['login_remember_me']					= 'Remember Me';
$lang['login_error']						= '<strong>Sorry,</strong> there were errors. Please see below for details.';


/**
 * Forgotten password
 * 
 **/
 
//	Resetting
$lang['forgotten_password_title']			= 'Reset your password';
$lang['forgotten_password_blurb']			= 'Please enter your registered email address so we can send you a password reset token.';
$lang['forgotten_password_success']			= '<strong>Reset token sent!</strong> Please check your email remembering to look in junk and spam folders.';
$lang['forgotten_password_email_fail']		= '<strong>Sorry,</strong> there was a problem sending the email with your reset link. Please try again.';
$lang['forgotten_password_code_not_set']	= '<strong>Sorry,</strong> we were unable to generate a token for the email address <strong>%s</strong>.';

//	Validating
$lang['forgotten_password_expired_code']	= '<strong>Sorry,</strong> the reset token you are using has expired. You will need to resubmit a password reset request.';
$lang['forgotten_password_invalid_code']	= 'Invalid or expired password reset token.';
$lang['forgotten_password_reset_blurb_1']	= 'Your password has been reset and is shown below.';
$lang['forgotten_password_reset_blurb_2']	= 'Copy this password and use it to log in, it will <strong>not</strong> be shown again.';
$lang['forgotten_password_reset_blurb_3']	= 'You will be prompted to change your password upon log in.';



/**
 * Temporary password
 * 
 **/
 
$lang['reset_temp_title']					= 'Update your password';
$lang['reset_temp_blurb']					= 'Please update your password to continue.';


/**
 * Registration
 * 
 **/
 
$lang['register_title']						= 'Register';
$lang['register_disabled']					= 'New registrations have been disabled by the administrator.';
$lang['register_email_subjects_welcome']	= 'Welcome';
$lang['register_read_terms']				= 'I have read and accept the <a href="%s">Terms and Conditions</a>.';
$lang['register_marketing']					= 'We would like to send you information and offers from carefully selected partners. Check the box if you do not want to receive these emails';
$lang['register_terms_error']				= 'You must accept the Terms and conditions in order to register.';
$lang['await_activation_title']				= 'Activate your Account';
$lang['await_activation_1']					= 'An activation email has been sent to <strong>%s</strong>.';
$lang['await_activation_2']					= 'Check your mail (including spam folders) and click on the link to activate your account and begin using Intern Avenue!';

//	Errors
$lang['register_error']						= 'There were errors. Please see below for details.';
$lang['register_error_email_in_use']		= 'This email is already in use.';


//	Register core fields
$lang['register_field_name_email']				= 'Email';
$lang['register_field_placeholder_email']		= 'email';
$lang['register_field_name_password']			= 'Password';
$lang['register_field_placeholder_password']	= 'password';
$lang['register_field_name_username']			= 'Username';
$lang['register_field_placeholder_username']	= 'username';


/**
 * Activation
 * 
 **/
 
$lang['activate_title']						= 'Activate your account';
$lang['activate_fail']						= 'Unable to activate account.<br />Invalid or expired code.';


/**
 * Form placeholders
 * 
 **/
 
$lang['placeholder_email']					= 'email';
$lang['placeholder_email_confirm']			= 're-type email';
$lang['placeholder_username']				= 'username';
$lang['placeholder_password']				= 'password';
$lang['placeholder_password_confirm']		= 're-type password';
$lang['placeholder_new_password']			= 'new password';
$lang['placeholder_confirm_pass']			= 'confirm';
$lang['placeholder_first_name']				= 'first name';
$lang['placeholder_last_name']				= 'surname';



/**
 * Form validation
 * 
 **/
 
$lang['required']							= 'This field is required.';
$lang['required_reset']						= 'Both fields are required.';
$lang['valid_email']						= 'You must provide a valid email.';
$lang['matches']							= 'The fields do not match';
$lang['min_length']							= 'Password must be at least %2$s characters long.';
$lang['max_length']							= 'Password cannot exceed %2$s characters.';
$lang['min_length_change_temp']				= 'Your new password cannot be shorter than %2$s characters.';


/**
 * Form Errors
 * 
 **/
 
$lang['required_field']						= 'This field is required';
$lang['valid_email']						= 'This must be a valid email';
$lang['alpha_dash']							= 'Invalid characters (only a-z, 0-9, dashes and underscores)';
$lang['alpha_dash_space_accent']			= 'Invalid characters (only a-z, 0-9, spaces, dashes and underscores)';
$lang['integer']							= 'This field must be an integer';
$lang['matches']							= 'This field does not match the %2$s field.';
$lang['is_unique']							= 'The email address you entered is already in use. <a href="%s">Forgotten your password?</a>';
$lang['integer']							= 'This field must be an integer';
$lang['max_length']							= 'This field cannot exceed %2$s characters in length';
$lang['min_length']							= 'This field cannot be less than %2$s characters in length';


/**
 * Form Actions
 * 
 **/
 
$lang['action_log_in']						= 'Log In';
$lang['action_reset_password']				= 'Reset Password';
$lang['action_reset_continue']				= 'Change Password & Log In';
$lang['action_proceed_login']				= 'Proceed to Log In';
$lang['action_register']					= 'Sign Up Now';


/**
 * Error messages
 * 
 **/
 
$lang['login_fail_general']						= 'Your log in has not been successful, please try again.';
$lang['login_fail_inactive']					= 'Your log in failed, you must activate your account first.<br />If you did not receive a code, have lost your activation email or have deactivated your account then <a href="%s">click here to resend</a>.';
$lang['login_fail_banned']						= 'This account has been banned; if you feel you have received this message in error then please contact us.';
$lang['login_fail_blocked']						= 'This account has been temporarily blocked due to repeated failed logins. Please wait %s minutes before trying again (each failed login resets the block). ';
$lang['login_fail_social']						= 'This account was created using a social network; either login via the appropriate button or <a href="%s">click here to set a password</a> using the Forgotten Password tool.';
$lang['login_fail_social_fb']					= 'This account was created using Facebook; either login via the Facebook button or <a href="%s">click here to set a password</a> using the Forgotten Password tool.';
$lang['login_fail_social_in']					= 'This account was created using LinkedIn; either login via the LinkedIn button or <a href="%s">click here to set a password</a> using the Forgotten Password tool.';
$lang['logout_successful']						= '<strong>Goodbye!</strong> You have been logged out successfully.';
$lang['forgot_password_unsuccessful']			= 'That email address was not recognised, please try again.';
$lang['forgot_password_email_unsuccessful']		= 'Your email was recognised but there were problems issuing a reset token. You may be able to try again. If the problem continues please contact the site administrator.';
$lang['password_change_successful']				= 'Your password was changed successfully.';
$lang['password_change_unsuccessful']			= 'Your password was not changed.';
$lang['activate_successful']					= 'Account was activated successfully.';
$lang['update_successful']						= 'Account was updated successfully.';
$lang['update_unsuccessful']					= 'Account was not updated.';
$lang['account_creation_unsuccessful']			= 'Account was not created.';
$lang['activate_unsuccessful']					= 'Account was not activated.';