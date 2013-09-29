<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* Name:			Email Langfile
*
* Description:  Generic language file for Nails. Admin Email
*
*/

	//	Generic for module
	$lang['email_module_name']			= 'Email';
	$lang['email_method_unfinished']	= '<strong>Not quite ready!</strong><br />This part of the module is still under development and will be made available soon.';

	// --------------------------------------------------------------------------

	//	Nav
	$lang['email_nav_index']	= 'Message Archive';
	$lang['email_nav_compose']	= 'Compose Message';
	$lang['email_nav_campaign']	= 'Campaigns';

	// --------------------------------------------------------------------------

	//	Archive browser
	$lang['email_index_title']			= 'Message Archive';
	$lang['email_index_intro']			= 'This page shows you all the mail which has been sent by the system (including messages which are still queued for sending).';
	$lang['email_index_thead_id']		= 'ID';
	$lang['email_index_thead_ref']		= 'Ref';
	$lang['email_index_thead_to']		= 'To';
	$lang['email_index_thead_sent']		= 'Sent';
	$lang['email_index_thead_type']		= 'Type';
	$lang['email_index_thead_reads']	= 'Opens';
	$lang['email_index_thead_clicks']	= 'Clicks';
	$lang['email_index_thead_actions']	= 'Actions';
	$lang['email_index_subject']		= 'Subject: %s';
	$lang['email_index_noemail']		= 'No Emails Found';

	// --------------------------------------------------------------------------

	//	Compose message
	$lang['email_compose_title']	= 'Compose New Message';
	$lang['email_compose_intro']	= 'This page allows you to compose, and send, branded email from the site to a particular set of users.';

	// --------------------------------------------------------------------------

	//	Campaign Manager
	$lang['email_campaign_title']	= 'Campaigns';
	$lang['email_campaign_intro']	= 'Use campaigns to send an email to your users. You can choose to send to everyone or just to a particular sub-set.';