<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* Name:			Accounts Langfile
*
* Description:  Generic language file for Nails. Admin Accounts
*
*/

	//	Generic for module
	$lang['accounts_module_name']		= 'Members';
	$lang['accounts_sort_id']			= 'User ID';
	$lang['accounts_sort_group_id']		= 'Group ID';
	$lang['accounts_sort_first']		= 'First Name, Surname';
	$lang['accounts_sort_last']			= 'Surname, First Name';
	$lang['accounts_sort_email']		= 'Email';

	// --------------------------------------------------------------------------

	//	Nav
	$lang['accounts_nav_index']			= 'View All Members';
	$lang['accounts_nav_create']		= 'Create New User';

	// --------------------------------------------------------------------------

	//	Overview
	$lang['accounts_index_title']				= 'View All Members';
	$lang['accounts_index_search_results']		= 'search for "%s" returned %s results';
	$lang['accounts_index_intro']				= 'This section lists all users registered on site. You can browse or search this list using the search facility below.';

	//	Listings
	$lang['accounts_index_th_id']				= 'User ID';
	$lang['accounts_index_th_user']				= 'User';
	$lang['accounts_index_th_group']			= 'Group';
	$lang['accounts_index_th_actions']			= 'Actions';
	$lang['accounts_index_no_users']			= 'No users found';
	$lang['accounts_index_verified']			= 'Verified email address';
	$lang['accounts_index_last_login']			= 'Last login: <span class="nice-time">%s</span> (%s logins)';
	$lang['accounts_index_last_nologins']		= 'Last login: Never Logged In';

	$lang['accounts_index_noteditable']			= 'You do not have permission to perform manipulations on this user.';
	$lang['accounts_index_noactions']			= 'There are no actions you can perform on this user.';

	// --------------------------------------------------------------------------

	//	Create new user
	$lang['accounts_create_title']						= 'Create New User';
	$lang['accounts_create_intro']						= 'Create a new user by completing the following basic information and clicking \'Create User\' below. You will be given the opportunity to edit the user once the basic account has been created.';
	$lang['accounts_create_basic_legend']				= 'Basic Information';
	$lang['accounts_create_field_group_label']			= 'User\'s Group';
	$lang['accounts_create_field_group_tip']			= 'Specify to which group this user belongs';
	$lang['accounts_create_field_password_tip']			= 'Leave the password field blank to have the system auto-generate a 6 character password.';
	$lang['accounts_create_field_password_placeholder']	= 'The user\'s password, leave blank to auto-generate';
	$lang['accounts_create_field_send_welcome_label']	= 'Send Welcome Email';
	$lang['accounts_create_field_send_welcome_yes']		= '<strong>Yes</strong>, send user welcome email containing their password.';
	$lang['accounts_create_field_send_welcome_no']		= '<strong>No</strong>, do not send welcome email.';
	$lang['accounts_create_field_temp_pw_label']		= 'Update on log in';
	$lang['accounts_create_field_temp_pw_yes']			= '<strong>Yes</strong>, require user to update password on first log in.';
	$lang['accounts_create_field_temp_pw_no']			= '<Strong>No</strong>, do not require user to update password on first log in.';
	$lang['accounts_create_field_first_placeholder']	= 'The user\'s first name';
	$lang['accounts_create_field_last_placeholder']		= 'The user\'s surname';
	$lang['accounts_create_field_email_placeholder']	= 'The user\'s email address';
	$lang['accounts_create_field_username_placeholder']	= 'The user\'s username';
	$lang['accounts_create_submit']						= 'Create User';

	// --------------------------------------------------------------------------

	//	Edit user
	$lang['accounts_edit_title']			= 'Edit User (%s)';

	//	Errors
	$lang['accounts_edit_error_unknown_id']		= 'Unknown User ID';
	$lang['accounts_edit_error_profile_img']	= '<strong>Update Failed:</strong> There was a problem uploading the Profile Image.';
	$lang['accounts_edit_error_upload']			= '<strong>Update failed:</strong> The file "%s" failed to upload.';
	$lang['accounts_edit_error_noteditable']	= '<strong>Sorry,</strong> you do not have permission to perform manipulations on that user.';


	$lang['accounts_edit_ok']					= '<strong>Success!</strong> Updated user %s';
	$lang['accounts_edit_fail']					= '<strong>Sorry,</strong> failed to update user: %s';
	$lang['accounts_edit_editing_self_m']		= '<strong>Hey there handsome!</strong> You are currently editing your own account.';
	$lang['accounts_edit_editing_self_f']		= '<strong>Hey there beautiful!</strong> You are currently editing your own account.';
	$lang['accounts_edit_editing_self_u']		= '<strong>Hello there!</strong> You are currently editing your own account.';

	$lang['accounts_edit_actions_legend']	= 'Actions';

	$lang['accounts_edit_password_legend']							= 'Password';
	$lang['accounts_edit_password_field_password_label']			= 'Reset Password';
	$lang['accounts_edit_password_field_password_placeholder']		= 'Reset the user\'s password by specifying a new one here';
	$lang['accounts_edit_password_field_password_tip']				= 'The user will NOT be informed of any password changes';
	$lang['accounts_edit_password_field_temp_pw_label']				= 'Update on next log in';
	$lang['accounts_edit_password_field_temp_pw_yes']				= '<strong>Yes</strong>, require user to update password on next log in.';
	$lang['accounts_edit_password_field_temp_pw_no']				= '<strong>No</strong>, do not require user to update password on next log in.';

	$lang['accounts_edit_security_questions_legend']			= 'Security Questions';
	$lang['accounts_edit_security_questions_field_reset_label']	= 'Set new qustions on next log in';
	$lang['accounts_edit_security_questions_field_reset_yes']	= '<strong>Yes</strong>, require user to set new security questions on next log in.';
	$lang['accounts_edit_security_questions_field_reset_no']	= '<strong>No</strong>, do not require user to set new security questions on next log in.';

	$lang['accounts_edit_basic_legend']							= 'Basic Information';
	$lang['accounts_edit_basic_field_first_placeholder']		= 'The user\'s first name';
	$lang['accounts_edit_basic_field_last_placeholder']			= 'The user\'s surname';
	$lang['accounts_edit_basic_field_email_placeholder']		= 'The user\'s email address';
	$lang['accounts_edit_basic_field_verified_label']			= 'Email verified';
	$lang['accounts_edit_basic_field_username_label']			= 'Username';
	$lang['accounts_edit_basic_field_username_placeholder']		= 'The user\'s username';
	$lang['accounts_edit_basic_field_gender_label']				= 'Gender';
	$lang['accounts_edit_basic_field_timezone_label']			= 'Timezone';
	$lang['accounts_edit_basic_field_timezone_tip']				= 'This gives the user the ability to specify their local timezone so that dates &amp; times are correct.';
	$lang['accounts_edit_basic_field_timezone_placeholder']		= 'The user\'s timezone, coming soon!';
	$lang['accounts_edit_basic_field_date_format_label']		= 'Date Format';
	$lang['accounts_edit_basic_field_date_format_tip']			= 'Allows the user to choose how dates are rendered.';
	$lang['accounts_edit_basic_field_time_format_label']		= 'Time Format';
	$lang['accounts_edit_basic_field_time_format_tip']			= 'Allows the user to choose how times are rendered.';
	$lang['accounts_edit_basic_field_language_label']			= 'Language';
	$lang['accounts_edit_basic_field_language_tip']				= 'This gives the user the ability to translate the site into their own language (where supported).';
	$lang['accounts_edit_basic_field_language_placeholder']		= 'The user\'s preferred language, coming soon!';
	$lang['accounts_edit_basic_field_register_ip_label']		= 'Registration IP';
	$lang['accounts_edit_basic_field_last_ip_label']			= 'Last IP';
	$lang['accounts_edit_basic_field_created_label']			= 'Created';
	$lang['accounts_edit_basic_field_modified_label']			= 'Modified';
	$lang['accounts_edit_basic_field_logincount_label']			= 'Log in counter';
	$lang['accounts_edit_basic_field_last_login_label']			= 'Last Login';
	$lang['accounts_edit_basic_field_not_logged_in']			= 'Never Logged In';
	$lang['accounts_edit_basic_field_referral_label']			= 'Referral Code';
	$lang['accounts_edit_basic_field_referred_by_label']		= 'Referred By';
	$lang['accounts_edit_basic_field_referred_by_placeholder']	= 'The user who referred this user, if any';

	$lang['accounts_edit_emails_legend']			= 'Email Addresses';
	$lang['accounts_edit_emails_th_email']			= 'Email Address';
	$lang['accounts_edit_emails_th_primary']		= 'Primary';
	$lang['accounts_edit_emails_th_verified']		= 'Verified';
	$lang['accounts_edit_emails_th_date_added']		= 'Date Added';
	$lang['accounts_edit_emails_th_date_verified']	= 'Date Verified';
	$lang['accounts_edit_emails_td_not_verified']	= 'Not Verified';

	$lang['accounts_edit_meta_legend']		= 'Meta Information';
	$lang['accounts_edit_meta_noeditable']	= 'There is no editable meta information for this user.';

	$lang['accounts_edit_img_legend']		= 'Profile Image';

	$lang['accounts_edit_social_legend']	= 'Social Media';
	$lang['accounts_edit_social_connected']	= 'Connected to %s';
	$lang['accounts_edit_social_none']		= 'This user is not currently connected to any social media network';

	$lang['accounts_edit_upload_legend']		= 'User Uploads';
	$lang['accounts_edit_upload_nofile']		= 'No files found';
	$lang['accounts_edit_upload_type_image']	= 'Images';
	$lang['accounts_edit_upload_type_file']		= 'Files';

	//	TODO: Errors

	// --------------------------------------------------------------------------

	//	Suspending/Unsuspending
	$lang['accounts_suspend_success']		= '<strong>Success!</strong> %s was suspended.';
	$lang['accounts_suspend_error']			= '<strong>Sorry,</strong> there was a problem suspending %s.';
	$lang['accounts_unsuspend_success']		= '<strong>Success!</strong> %s was unsuspended.';
	$lang['accounts_unsuspend_error']		= '<strong>Sorry,</strong> there was a problem unsuspending %s.';

	// --------------------------------------------------------------------------

	//	Deleting
	$lang['accounts_delete_success']		= '<strong>See ya!</strong> User %s was deleted successfully.';
	$lang['accounts_delete_error']			= '<strong>Sorry,</strong> there was a problem deleting %s.';

	// --------------------------------------------------------------------------

	//	Deleting profile image
	$lang['accounts_delete_img_success']		= '<strong>Success!</strong> Profile image was deleted.';
	$lang['accounts_delete_img_error']			= '<strong>Sorry,</strong> I was unable delete this user\'s profile image. The server said: "%s"';
	$lang['accounts_delete_img_error_noid']		= '<strong>Sorry,</strong> I was unable to find a user by that ID.';
	$lang['accounts_delete_img_error_noimg']	= '<strong>Hey!</strong> This user doesn\'t have a profile image to delete.';

	// --------------------------------------------------------------------------

	//	Manage User Groups
	$lang['accounts_groups_index_title']				= 'Manage User Access';
	$lang['accounts_groups_index_intro']				= 'Manage how groups of user\'s can interact with the site.';
	$lang['accounts_groups_index_th_name']				= 'Name and Description';
	$lang['accounts_groups_index_th_homepage']			= 'Homepage';
	$lang['accounts_groups_index_th_default']			= 'Default';
	$lang['accounts_groups_index_th_actions']			= 'Actions';
	$lang['accounts_groups_index_action_set_default']	= 'Set As Default Group';

	//	Edit group
	$lang['accounts_groups_edit_title']				= 'Edit Group (%s)';
	$lang['accounts_groups_edit_warning']			= '<strong>Please note:</strong> while the system will do its best to validate the content you set ' .
													  'sometimes a valid combination can render an entire group useless (including your own). Please be ' .
													  'extra careful and only change things when you know what you\'re doing. Remember that you won\'t see ' .
													  'the effect of changing the permissions of a group other than your own, check that your changes ' .
													  'have worked before considering the job done!';

	$lang['accounts_groups_edit_basic_legend']							= 'Basics';
	$lang['accounts_groups_edit_basic_field_label_label']				= 'Label';
	$lang['accounts_groups_edit_basic_field_placeholder_label']			= 'Type the group\'s label name here.';
	$lang['accounts_groups_edit_basic_field_label_slug']				= 'Slug';
	$lang['accounts_groups_edit_basic_field_placeholder_slug']			= 'Type the group\'s slug here.';
	$lang['accounts_groups_edit_basic_field_label_description']			= 'Description';
	$lang['accounts_groups_edit_basic_field_placeholder_description']	= 'Type the group\'s description here.';
	$lang['accounts_groups_edit_basic_field_label_homepage']			= 'Default Homepage';
	$lang['accounts_groups_edit_basic_field_placeholder_homepage']		= 'Type the group\'s homepage here.';
	$lang['accounts_groups_edit_basic_field_label_registration']		= 'Registration Redirect';
	$lang['accounts_groups_edit_basic_field_placeholder_registration']	= 'Redirect new registrants of this group here.';
	$lang['accounts_groups_edit_basic_field_tip_registration']			= 'If not defined new registrants will be redirected to the group\'s homepage.';

	$lang['accounts_groups_edit_permission_legend']						= 'Permissions';
	$lang['accounts_groups_edit_permission_warn']						= '<strong>Please note:</strong> Superusers have full, unrestricted access to admin, regardless of what extra permissions are set.';
	$lang['accounts_groups_edit_permission_intro']						= 'For non-superuser groups you may also grant a access to the administration area by selecting which admin modules they have permission to access. <strong>It goes without saying that you should be careful with these options.</strong>';
	$lang['accounts_groups_edit_permissions_field_label_superuser']		= 'Is Super User';
	$lang['accounts_groups_edit_permissions_toggle_all']				= 'Toggle All';
	$lang['accounts_groups_edit_permissions_dashboard_warn']			= 'If any admin method is selected then this must also be selected.';

