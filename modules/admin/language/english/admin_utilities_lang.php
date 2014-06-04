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
	$lang['utilities_nav_languages']		= 'Manage Languages';
	$lang['utilities_nav_export']			= 'Export Data';

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


	// --------------------------------------------------------------------------

	//	Export data
	$lang['utilities_export_title']				= 'Export Data';
	$lang['utilities_export_intro']				= 'Export data stored in the site\'s database in a variety of formats.';
	$lang['utilities_export_warn']				= '<strong>Please note:</strong> Exporting may take some time when executing on large databases. Please be patient.';
	$lang['utilities_export_legend_source']		= 'Data Source';
	$lang['utilities_export_field_source']		= 'Source';
	$lang['utilities_export_legend_format']		= 'Export Format';
	$lang['utilities_export_field_format']		= 'Format';

	$lang['utilities_export_error_source']			= '<strong>Sorry,</strong> invalid data source.';
	$lang['utilities_export_error_format']			= '<strong>Sorry,</strong> invalid format type.';
	$lang['utilities_export_error_source_notexist']	= '<strong>Sorry,</strong> that data source is not available.';
	$lang['utilities_export_error_format_notexist']	= '<strong>Sorry,</strong> that format type is not available.';
