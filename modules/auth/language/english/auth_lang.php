<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Name:		Authentication - LANGUAGE
 *
 * Description:	English language file for Auth module
 *
 **/


	//	Page Titles
	$lang['auth_title_login']									= 'Please Log In';
	$lang['auth_title_register']								= 'Register';
	$lang['auth_title_forgotten_password']						= 'Forgotten your password?';
	$lang['auth_title_forgotten_password_security_question']	= 'Please answer this security question';
	$lang['auth_title_reset']									= 'Reset your Password';


	// --------------------------------------------------------------------------


	//	Generic errors
	$lang['auth_no_db']									= 'No database is configured.';
	$lang['auth_no_access_already_logged_in']			= '<strong>Sorry,</strong> you can\'t access that page while logged in (currently logged in as <strong>%s</strong>).';
	$lang['auth_no_access']								= '<strong>Sorry,</strong> you do not have permission to access that content.';


	// --------------------------------------------------------------------------


	//	Login lang strings
	$lang['auth_login_message']							= 'Sign in to your %s account using your email address and password. Not got an account? <a href="%s">Click here to register</a>.';
	$lang['auth_login_message_no_register']				= 'Sign in to your %s account using your email address and password.';
	$lang['auth_login_email_placeholder']				= 'Your registered email ';
	$lang['auth_login_username_placeholder']			= 'Your registered username';
	$lang['auth_login_both']							= 'Email/Username';
	$lang['auth_login_both_placeholder']				= 'Your registered email or username';
	$lang['auth_login_password_placeholder']			= 'Your password';
	$lang['auth_login_label_remember_me']				= 'Remember Me';
	$lang['auth_login_action_login']					= 'Log In';
	$lang['auth_login_forgot']							= 'Forgotten your password?';
	$lang['auth_login_social_message']					= 'Or, sign in using your preferred social network.';
	$lang['auth_login_social_signin']					= 'Sign in with %s';

	//	Messages
	$lang['auth_login_ok_welcome']						= '<strong>Welcome, %s!</strong> You last logged in %s.';
	$lang['auth_login_ok_welcome_with_ip']				= '<strong>Welcome, %s!</strong> You last logged in %s from IP address %s.';
	$lang['auth_login_ok_welcome_notime']				= '<strong>Welcome, %s!</strong> Nice to see you again.';

	//	with_hashes() lang strings
	$lang['auth_with_hashes_incomplete_creds']			= 'Incomplete login credentials.';
	$lang['auth_with_hashes_autologin_fail']			= 'Auto-login failed.';

	//	Override lang strings
	$lang['auth_override_invalid']						= 'Sorry, the supplied credentials failed validation.';
	$lang['auth_override_ok']							= 'You were successfully logged in as <strong>%s</strong>';
	$lang['auth_override_return']						= '<strong>Welcome back!</strong> You successfully logged back in as <strong>%s</strong>';
	$lang['auth_override_fail_nopermission']			= '<strong>Sorry,</strong> you do not have permission to sign in as other users.';
	$lang['auth_override_fail_cloning']					= 'You cannot sign in as this person. For security we do not allow users to sign in as themselves for a second time; doing so will cause a break in the space-time continuum. I don\'t believe you want to be responsible for that now, do you?';
	$lang['auth_override_fail_superuser']				= 'You cannot sign in as this person. For security we do not allow users to sign in as superusers; doing so will cause a break in the space-time continuum. I don\'t believe you want to be responsible for that now, do you?';

	//	Auth_model lang strings
	$lang['auth_login_fail_missing_field']				= '<strong>Sorry,</strong> a required field was missing.';
	$lang['auth_login_fail_general']					= '<strong>Sorry,</strong> your log in has not been successful, please try again.';
	$lang['auth_login_fail_suspended']					= '<strong>This account has been suspended;</strong> if you feel you have received this message in error then please contact us.';
	$lang['auth_login_fail_blocked']					= '<strong>This account has been temporarily blocked due to repeated failed logins.</strong><br />Please wait %s minutes before trying again (each failed login resets the block). ';
	$lang['auth_login_fail_social']						= '<strong>This account was created using a social network</strong><br />Either login via the appropriate button or <a href="%s">click here to set a password</a> using the Forgotten Password tool.';
	$lang['auth_login_fail_social_fb']					= '<strong>This account was created using Facebook.</strong><br />Either login via the Facebook button or <a href="%s">click here to set a password</a> using the Forgotten Password tool.';
	$lang['auth_login_fail_social_tw']					= '<strong>This account was created using Twitter.</strong><br />Either login via the Twitter button or <a href="%s">click here to set a password</a> using the Forgotten Password tool.';
	$lang['auth_login_fail_social_li']					= '<strong>This account was created using LinkedIn.</strong><br />Either login via the LinkedIn button or <a href="%s">click here to set a password</a> using the Forgotten Password tool.';

	//	Two-factor auth strings
	$lang['auth_twofactor_token_could_not_generate']	= 'Unable to generate two factor auth token.';
	$lang['auth_twofactor_token_invalid']				= 'Invalid Token.';
	$lang['auth_twofactor_token_expired']				= 'Token has expired.';
	$lang['auth_twofactor_token_bad_ip']				= 'Invalid IP address.';
	$lang['auth_twofactor_token_unverified']			= '<strong>Sorry,</strong> there was a problem verifying your login session. As a precaution we have logged you out.';

	$lang['auth_twofactor_question_set_title']			= 'Set Your Security Questions';
	$lang['auth_twofactor_question_set_body']			= 'This website offers enhanced security for your account, please specify a few security questions which we\'ll use to verify your identity when you log in to the system.';
	$lang['auth_twofactor_question_set_system_body']	= 'The following questions are generated by the system, please choose your preferred question and provide an answer.';
	$lang['auth_twofactor_question_set_system_legend']	= 'System Questions';
	$lang['auth_twofactor_question_set_custom_body']	= 'Specify your own security question and answer combination. Remember to make questions hard for an attacker to guess or research (avoid information which can easily be found on public mediums, such as social networks).';
	$lang['auth_twofactor_question_set_custom_legend']	= 'Custom Questions';
	$lang['auth_twofactor_question_set_fail']			= '<strong>Sorry,</strong> there was a problem saving your security questions.';
	$lang['auth_twofactor_question_unique']				= '<strong>Sorry,</strong> questions must be unique. Please don\'t specify the same question more than once.';

	$lang['auth_twofactor_answer_title']				= 'Security Question';
	$lang['auth_twofactor_answer_body']					= 'Please answer the following security question.';
	$lang['auth_twofactor_answer_incorrect']			= '<strong>Sorry,</strong> your answer was incorrect.';


	// --------------------------------------------------------------------------

	//	Social network connect
	$lang['auth_social_already_linked']					= '<strong>Woah there!</strong> You have already linked your %s account.';
	$lang['auth_social_no_access_token']				= '<strong>There was a problem.</strong> We could not validate your account with %s, you may be able to try again.';
	$lang['auth_social_account_in_use']					= '<strong>Sorry</strong>, the %s account you\'re currently logged into is already linked with another %s account.';
	$lang['auth_social_email_in_use']					= '<strong>You\'ve been here before?</strong> We noticed that the email associated with your %1$s account is already registered with %2$s. In order to use %1$s to sign in you\'ll need to link your accounts via your Settings page. Log in below using your email address and we\'ll get you started.';
	$lang['auth_social_email_in_use_no_settings']		= '<strong>You\'ve been here before?</strong> We noticed that the email associated with your %1$s account is already registered with %2$s. Please sign in using your email address and password. <a href="%3$s">Forgotten your password</a>?';
	$lang['auth_social_linked_ok']						= '<strong>Success</strong>, your %s account is now linked.';
	$lang['auth_social_register_ok']					= '<strong>Hi, %s!</strong> Your account has been set up and is ready to be used.';
	$lang['auth_social_register_disabled']				= '<strong>Sorry,</strong> new registrations are not permitted.';
	$lang['auth_social_disconnect_ok']					= '<strong>Success!</strong> Your %s account was successfully disconnected.';
	$lang['auth_social_no_disconnect_fail']				= '<strong>Sorry,</strong> there was a problem disconnecting your %s account.';
	$lang['auth_social_no_disconnect_not_linked']		= '<strong>Sorry,</strong> your account is not currently linked to a %s account.';
	$lang['auth_social_no_disconnect_not_logged_in']	= '<strong>Sorry,</strong> you must be logged in to disconnect your %s account.';

	// --------------------------------------------------------------------------


	//	Logout lang strings
	$lang['auth_logout_successful']						= '<strong>Goodbye, %s!</strong> You have been logged out successfully.';


	// --------------------------------------------------------------------------


	//	Register lang strings
	$lang['auth_register_email_is_unique']				= 'This email is already registered. Have you <a href="%s">forgotten your password</a>?';
	$lang['auth_register_username_is_unique']			= 'This username is already registered. Have you <a href="%s">forgotten your password</a>?';
	$lang['auth_register_identity_is_unique']			= 'This is already registered. Have you <a href="%s">forgotten your password</a>?';
	$lang['auth_register_flashdata_welcome']			= '<strong>Welcome, %s!</strong>';
	$lang['auth_register_message']						= 'To register with %s simply complete the following form.';
	$lang['auth_register_first_name_placeholder']		= 'Your first name';
	$lang['auth_register_last_name_placeholder']		= 'Your Surname';
	$lang['auth_register_email_placeholder']			= 'A valid email address';
	$lang['auth_register_username_placeholder']			= 'Your desired username';
	$lang['auth_register_password_placeholder']			= 'Choose a password';
	$lang['auth_register_label_accept_tc']				= 'I accept the <a href="%s">T&amp;C\'s</a>';

	$lang['auth_register_social_message']				= 'Or, to save time, register using your preferred social network.';
	$lang['auth_register_social_signin']				= 'Sign in with %s';
	$lang['auth_register_social_register']				= 'Register with %s';

	//	Extra info
	$lang['auth_register_extra_message']				= 'In order to complete setting up your account we need a little more information from you.';

	//	Wait for activation email
	$lang['auth_register_wait_message']					= 'An email with a link to verify your email address has been sent to:';
	$lang['auth_register_wait_next_title']				= 'What to do next';
	$lang['auth_register_wait_next_message']			= 'Check your e-mail (including spam folders) and click on the link to activate your account! It can take up to 60 seconds to receive your activation e-mail. If you have not received it, use the link below.';
	$lang['auth_register_wait_help_title']				= 'Help! I Didn\'t Receive an E-mail';
	$lang['auth_register_wait_help_message']			= 'If you haven\'t received your activation e-mail after a few moments, you can <a href="%s">send it again</a>.';

	//	Resend activation
	$lang['auth_register_resend_invalid']				= '<strong>Sorry,</strong> invalid credentials were supplied. Unable to resend activation email.';
	$lang['auth_register_resend_already_active']		= 'Account already active, please <a href="%s">try logging in</a>.';
	$lang['auth_register_resend_message']				= 'An email with a link to verify your email has been re-sent to: <strong>%s</strong>';
	$lang['auth_register_resend_next_title']			= 'What to do next';
	$lang['auth_register_resend_next_message']			= 'Check your email (including spam folders) and click on the link to verify your email address. It can sometimes take a while to receive your verification email.';

	//	Extra info lang strings
	$lang['auth_register_extra_title']					= 'Almost there!';


	// --------------------------------------------------------------------------


	//	Forgotten Password
	$lang['auth_forgot_message']						= 'Please enter your registered email address so we can send you an email with a link which you can use to reset your password.';
	$lang['auth_forgot_temp_message']					= '<strong>A password reset has been requested for this account</strong><br />Please choose a new password and click "Change Password &amp; Log In"';
	$lang['auth_forgot_new_pass_placeholder']			= 'Type a new password';
	$lang['auth_forgot_new_pass_confirm_placeholder']	= 'Confirm your new password';
	$lang['auth_forgot_email_placeholder']				= 'Your registered email address';
	$lang['auth_forgot_username_placeholder']			= 'Your registered username';
	$lang['auth_forgot_both']							= 'Email or Username';
	$lang['auth_forgot_both_placeholder']				= 'Your registered email or username';
	$lang['auth_forgot_action_reset']					= 'Reset Password';
	$lang['auth_forgot_action_reset_continue']			= 'Change Password & Log In';
	$lang['auth_forgot_success']						= '<strong>Reset token sent!</strong> Please check your email remembering to look in junk and spam folders.';
	$lang['auth_forgot_email_fail']						= '<strong>Sorry,</strong> there was a problem sending the email with your reset link. Please try again.';
	$lang['auth_forgot_code_not_set_email']				= '<strong>Sorry,</strong> we were unable to generate a token for the email address <strong>%s</strong>.';
	$lang['auth_forgot_code_not_set_username']			= '<strong>Sorry,</strong> we were unable to generate a token for the username <strong>%s</strong>.';
	$lang['auth_forgot_code_not_set']					= '<strong>Sorry,</strong> we were unable to generate a token for that account.';
	$lang['auth_forgot_expired_code']					= '<strong>Sorry,</strong> the reset token you are using has expired. You will need to resubmit a password reset request.';
	$lang['auth_forgot_invalid_code']					= '<strong>Sorry,</strong> Invalid or expired password reset token.';
	$lang['auth_forgot_reset_badlogin']					= '<strong>Sorry,</strong> something when wrong and you were not logged in. Please try <a href="%s">logging in with your new password</a>.';
	$lang['auth_forgot_reset_badupdate']				= '<strong>Sorry,</strong> there were errors. %s';
	$lang['auth_forgot_reminder']						= '<strong>In case you forgot,</strong> your temporary password is <strong>%s</strong>. You won\'t be shown this message again.';
	$lang['auth_forgot_reset_ok']						= 'Please log in using this temporary password:';
	$lang['auth_forgot_action_proceed']					= 'Proceed to Log In';

