<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* Name:			Utilities Langfile
*
* Description:  Generic language file for Nails. Admin Utilities
* 
*/

	//	Generic for module
	$lang['utilities_module_name']		= 'Utilities';
	
	// --------------------------------------------------------------------------
	
	//	Nav
	$lang['utilities_nav_test_email']		= 'Send Test Email';
	$lang['utilities_nav_user_access']		= 'Manage User Access';
	$lang['utilities_nav_languages']		= 'Manage Languages';
	
	// --------------------------------------------------------------------------
	
	//	Send Test email
	$lang['utilities_test_email_title']				= 'Send a Test Email';
	$lang['utilities_test_email_intro']				= 'Use this form to send a test email, useful for testing that emails being sent are received by the end user.';
	$lang['utilities_test_email_field_legend']		= 'Recipient';
	$lang['utilities_test_email_field_name']		= 'Email';
	$lang['utilities_test_email_field_placeholder']	= 'Type recipient\'s email address';
	$lang['utilities_test_email_submit']			= 'Send Test Email';
	$lang['utilities_test_email_success']			= '<strong>Done!</strong> Test email successfully sent to <strong>%s</strong> at %s';
	$lang['utilities_test_email_error']				= 'Sending Failed, debugging data below:';
	
	// --------------------------------------------------------------------------
	
	//	User Access
	$lang['utilities_user_access_title']			= 'Manage User Access';
	$lang['utilities_user_access_intro']			= 'Manage how groups of user\'s can interface with the site, e.g: grant a specific group access to admin and specify which parts of admin they can view.';
	$lang['utilities_user_access_th_name']			= 'Name and Description';
	$lang['utilities_user_access_th_homepage']		= 'Default Homepage';
	$lang['utilities_user_access_th_actions']		= 'Actions';

	//	Edit group
	$lang['utilities_edit_group_title']				= 'Edit Group (%s)';
	$lang['utilities_edit_group_warning']			= '<strong>Please note:</strong> while the system will do its best to validate the content you set ' .
													  'sometimes a valid combination can render an entire group useless (including your own). Please be ' .
													  'extra careful and only change things when you know what you\'re doing. Remember that you won\'t see ' .
													  'the effect of changing the permissions of a group other than your own, check that your changes ' .
													  'have worked before considering the job done!';
	
	$lang['utilities_edit_group_basic_legend']							= 'Basics';
	$lang['utilities_edit_group_basic_field_label_display']				= 'Display Name';
	$lang['utilities_edit_group_basic_field_placeholder_display']		= 'Type the group\'s display name here.';
	$lang['utilities_edit_group_basic_field_label_name']				= 'Slug';
	$lang['utilities_edit_group_basic_field_placeholder_name']			= 'Type the group\'s slug here.';
	$lang['utilities_edit_group_basic_field_label_description']			= 'Description';
	$lang['utilities_edit_group_basic_field_placeholder_description']	= 'Type the group\'s description here.';
	$lang['utilities_edit_group_basic_field_label_homepage']			= 'Default Homepage';
	$lang['utilities_edit_group_basic_field_placeholder_homepage']		= 'Type the group\'s homepage here.';
	$lang['utilities_edit_group_basic_field_label_registration']		= 'Registration Redirect';
	$lang['utilities_edit_group_basic_field_placeholder_registration']	= 'Redirect new registrants of this group here.';
	$lang['utilities_edit_group_basic_field_tip_registration']			= 'If not defined new registrants will be redirected to the group\'s homepage.';

	$lang['utilities_edit_group_permission_legend']						= 'Permissions';
	$lang['utilities_edit_group_permission_warn']						= '<strong>Please note:</strong> Superusers have full, unrestricted access to admin, regardless of what extra permissions are set.';
	$lang['utilities_edit_group_permission_intro']						= 'For non-superuser groups you may also grant a access to the administration area by selecting which admin modules they have permission to access. <strong>It goes without saying that you should be careful with these options.</strong>';
	$lang['utilities_edit_group_permissions_field_label_superuser']		= 'Is Super User';
	$lang['utilities_edit_group_permissions_toggle_all']				= 'Toggle All';
	$lang['utilities_edit_group_permissions_dashboard_warn']			= 'If any admin method is selected then this must also be selected.';

	// --------------------------------------------------------------------------

	//	Manage Languages
	$lang['utilities_languages_title']				= 'Manage Site Languages';
	$lang['utilities_languages_intro']				= 'Manage which languages are supported by the site.';
	$lang['utilities_languages_multilang_on']		= 'This site supports multiple languages, where appropriate you will be able to specify content in langauges which are marked here as \'supported\'.';
	$lang['utilities_languages_multilang_off']		= 'This site does not support multiple languages, changes made here will affect non-language specific items (e.g dropdowns where the user can choose a language).';

	$lang['utilities_languages_th_lang']			= 'Language';
	$lang['utilities_languages_th_actions']			= 'Actions';

	$lang['utilities_languages_action_mark_supported']		= 'Mark As Supported';
	$lang['utilities_languages_action_mark_unsupported']	= 'Mark As Unsupported';

	$lang['utilities_languages_mark_supported_ok']		= '<strong>Success!</strong> Language marked as supported.';
	$lang['utilities_languages_mark_supported_fail']	= '<strong>Sorry,</strong> there was a problem marking this language as supported.';

	$lang['utilities_languages_mark_unsupported_ok']	= '<strong>Success!</strong> Language marked as unsupported.';
	$lang['utilities_languages_mark_unsupported_fail']	= '<strong>Sorry,</strong> there was a problem marking this language as unsupported.';

