<?php

/**
 * English language strings for Nails
 *
 * @package     Nails
 * @subpackage  common
 * @category    Language
 * @author      Nails Dev Team
 * @link
 */

//  Default header/footer
$lang['nails_footer_powered_by'] = 'Powered by <a href="%s">%s</a>';

// --------------------------------------------------------------------------

//  Verbs / common actions
$lang['action_save']            = 'Save';
$lang['action_create']          = 'Create';
$lang['action_publish']         = 'Publish';
$lang['action_publish_changes'] = 'Publish Changes';
$lang['action_edit']            = 'Edit';
$lang['action_save_changes']    = 'Save Changes';
$lang['action_update']          = 'Update';
$lang['action_upgrade']         = 'Upgrade';
$lang['action_search']          = 'Search';
$lang['action_cancel']          = 'Cancel';
$lang['action_reset']           = 'Reset';
$lang['action_continue']        = 'Continue';
$lang['action_delete']          = 'Delete';
$lang['action_remove']          = 'Remove';
$lang['action_close']           = 'Close';
$lang['action_open']            = 'Open';
$lang['action_view']            = 'View';
$lang['action_preview']         = 'Preview';
$lang['action_clear']           = 'Clear';
$lang['action_next']            = 'Next';
$lang['action_previous']        = 'Previous';
$lang['action_back']            = 'Back';
$lang['action_go_back']         = 'Go Back';
$lang['action_forward']         = 'Forward';
$lang['action_go_forward']      = 'Go Forward';
$lang['action_first']           = 'First';
$lang['action_last']            = 'Last';
$lang['action_register']        = 'Register';
$lang['action_login']           = 'Log In';
$lang['action_logout']          = 'Log Out';
$lang['action_suspend']         = 'Suspend';
$lang['action_unsuspend']       = 'Unsuspend';
$lang['action_verify']          = 'Verify';
$lang['action_unverify']        = 'Unverify';
$lang['action_choose']          = 'Choose';
$lang['action_hide']            = 'Hide';
$lang['action_download']        = 'Download';

// --------------------------------------------------------------------------

//  Common form validation
$lang['fv_there_were_errors']           = 'Please check highlighted fields.';
$lang['fv_required']                    = 'This field is required.';
$lang['fv_required_field']              = 'The {field} field is required.';
$lang['fv_valid_email']                 = 'This must be a valid email.';
$lang['fv_valid_email_field']           = 'The {field} field is must be a valid email.';
$lang['fv_valid_emails']                = 'This must contain only valid email addresses.';
$lang['fv_valid_emails_field']          = 'The {field} field must contain only valid email addresses.';
$lang['fv_email_already_registered']    = 'This email is already registered.<br /><small><a href="%s">Forgotten your password?</a></small>';
$lang['fv_username_already_registered'] = 'This username is already registered.<br /><small><a href="%s">Forgotten your password?</a></small>';
$lang['fv_identity_already_registered'] = 'This identity is already registered.<br /><small><a href="%s">Forgotten your password?</a></small>';
$lang['fv_matches']                     = 'This field does not match the {param} field.';
$lang['fv_matches_field']               = 'The {field} field does not match the {param} field.';
$lang['fv_is_unique']                   = 'This field must be unique.';
$lang['fv_is_unique_field']             = 'The {field} field is not unique.';
$lang['fv_unique_if_diff']              = 'This field must be unique.';
$lang['fv_unique_if_diff_field']        = 'The {field} field is not unique.';
$lang['fv_valid_postcode']              = 'This field must be a valid UK postcode.';
$lang['fv_valid_postcode_field']        = 'The {field} field is not a valid UK postcode.';
$lang['fv_valid_date']                  = 'This field must be a valid date.';
$lang['fv_valid_date_field']            = 'The {field} field is not a valid date.';
$lang['fv_date_future']                 = 'This field must be in the future.';
$lang['fv_date_future_field']           = 'The {field} field must be in the future.';
$lang['fv_date_today']                  = 'This field must be the same as today\'s date.';
$lang['fv_date_today_field']            = 'The {field} field must be today\'s date.';
$lang['fv_date_past']                   = 'This field must be in the past.';
$lang['fv_date_past_field']             = 'The {field} field must be in the past.';
$lang['fv_date_before']                 = 'This must be before the {param} field.';
$lang['fv_date_before_field']           = 'The {field} field must be before the {param} field.';
$lang['fv_date_after']                  = 'This must be after the {param} field.';
$lang['fv_date_after_field']            = 'The {field} field must be before the {param} field.';
$lang['fv_valid_datetime']              = 'This field must be a valid datetime.';
$lang['fv_valid_datetime_field']        = 'The {field} field is not a valid datetime.';
$lang['fv_datetime_future']             = 'This field must be in the future.';
$lang['fv_datetime_future_field']       = 'The {field} field must be in the future.';
$lang['fv_datetime_past']               = 'This field must be in the past.';
$lang['fv_datetime_past_field']         = 'The {field} field must be in the past.';
$lang['fv_datetime_before']             = 'This must be before the {param} field.';
$lang['fv_datetime_before_field']       = 'The {field} field must be before the {param} field.';
$lang['fv_datetime_after']              = 'This must be after the {param} field.';
$lang['fv_datetime_after_field']        = 'The {field} field must be before the {param} field.';
$lang['fv_valid_time']                  = 'This field must be a valid time.';
$lang['fv_valid_time_field']            = 'The {field} field is not a valid time.';
$lang['fv_time_future']                 = 'This field must be in the future.';
$lang['fv_time_future_field']           = 'The {field} field must be in the future.';
$lang['fv_time_past']                   = 'This field must be in the past.';
$lang['fv_time_past_field']             = 'The {field} field must be in the past.';
$lang['fv_time_before']                 = 'This must be before the {param} field.';
$lang['fv_time_before_field']           = 'The {field} field must be before the {param} field.';
$lang['fv_time_after']                  = 'This must be after the {param} field.';
$lang['fv_time_after_field']            = 'The {field} field must be before the {param} field.';
$lang['fv_numeric']                     = 'This field must be numeric.';
$lang['fv_numeric_field']               = 'The {field} field must be numeric.';
$lang['fv_is_natural']                  = 'This field must be a natural number (0, 1, 2, 3, etc).';
$lang['fv_is_natural_field']            = 'The {field} field must be a natural number (0, 1, 2, 3, etc).';
$lang['fv_is_natural_no_zero']          = 'This field must be a natural number greater than zero (1, 2, 3, etc).';
$lang['fv_is_natural_no_zero_field']    = 'The {field} field must contain be a natural number greater than zero (1, 2, 3, etc).';
$lang['fv_in_range']                    = 'This field must be within the range {param}.';
$lang['fv_in_range_field']              = 'The {field} field must be within the range {param}.';
$lang['fv_min_length']                  = 'This field is too short, minimum length is {param} characters.';
$lang['fv_min_length_field']            = 'The {field} field is too short, minimum length is {param} characters.';
$lang['fv_max_length']                  = 'This field is too long, maximum length is {param} characters.';
$lang['fv_max_length_field']            = 'The {field} field is too long, maximum length is {param} characters.';
$lang['fv_alpha_dash']                  = 'This field may only contain alpha-numeric characters, underscores, and dashes.';
$lang['fv_alpha_dash_field']            = 'The {field} field may only contain alpha-numeric characters, underscores, and dashes.';
$lang['fv_alpha_dash_period']           = 'This field may only contain alpha-numeric characters, underscores, periods, and dashes.';
$lang['fv_alpha_dash_period_field']     = 'The {field} field may only contain alpha-numeric characters, underscores, periods, and dashes.';
$lang['fv_count_floor']                 = 'This field requires a minimum selection.'; // @todo include minimum item requirement in text
$lang['fv_count_floor_field']           = 'The {field} field requires a minimum selection.'; // @todo include minimum item requirement in text
$lang['fv_count_ceiling']               = 'This field accepts a maximum selection.'; // @todo include maximum item requirement in text
$lang['fv_count_ceiling_field']         = 'The {field} field accepts a maximum selection.'; // @todo include maximum item requirement in text
$lang['fv_greater_than']                = 'This field must be greater than {param}';
$lang['fv_greater_than_field']          = 'The {field} field must be greater than {param}';
$lang['fv_greater_than_equal_to']       = 'This field must be greater than or equal to {param}';
$lang['fv_greater_than_equal_to_field'] = 'The {field} field must be greater than or equal to {param}';
$lang['fv_less_than']                   = 'This field must be less than {param}';
$lang['fv_less_than_field']             = 'The {field} field must be less than {param}';
$lang['fv_less_than_equal_to']          = 'This field must be less than or equal to {param}';
$lang['fv_less_than_equal_to_field']    = 'The {field} field must be less than or equal to {param}';
$lang['fv_is_bool']                     = 'This field must be a boolean';
$lang['fv_is_bool_field']               = 'The {field} field must be a boolean';
$lang['fv_is_id']                       = 'This field must be a valid ID';
$lang['fv_is_id_field']                 = 'The {field} field must be a valid ID';
$lang['fv_in_list']                     = 'This field must be one of: {param}';
$lang['fv_in_list_field']               = 'The {field} field must be one of: {param}.';
$lang['fv_supportedLocale']             = 'This field is not a supported locale';
$lang['fv_supportedLocale_field']       = 'The {field} field is not a supported locale';
$lang['fv_is']                          = 'This field must be exactly "{param}"';
$lang['fv_is_field']                    = 'The {field} field must be exactly "{param}"';
$lang['fv_integer']                     = 'This field must be an integer';
$lang['fv_integer_field']               = 'The {field} field must be an integr';

//  @todo (Pablo - 2019-12-16) - Deprecate/remove/move these
$lang['cdnObjectPickerMultiObjectRequired']         = 'All items must have a file set.';
$lang['cdnObjectPickerMultiObjectRequired_field']   = 'All items must have a file set.';
$lang['fv_cdnObjectPickerMultiLabelRequired']       = 'All items must have a label set.';
$lang['fv_cdnObjectPickerMultiLabelRequired_field'] = 'All items must have a label set.';
$lang['fv_cdnObjectPickerMultiAllRequired']         = 'All items must have a file and a label set.';
$lang['fv_cdnObjectPickerMultiAllRequired_field']   = 'All items must have a file and a label set.';

// --------------------------------------------------------------------------

//  Common form field labels
$lang['form_label_email']            = 'Email';
$lang['form_label_username']         = 'Username';
$lang['form_label_password']         = 'Password';
$lang['form_label_password_confirm'] = 'Confirm Password';
$lang['form_label_first_name']       = 'First Name';
$lang['form_label_last_name']        = 'Surname';
$lang['form_label_title']            = 'Title';
$lang['form_label_body']             = 'Body';

// --------------------------------------------------------------------------

//  Common strings
$lang['yes']              = 'Yes';
$lang['no']               = 'No';
$lang['on']               = 'On';
$lang['off']              = 'Off';
$lang['no_records_found'] = 'No Records found';

// --------------------------------------------------------------------------

//  Days and Months
$lang['day_mon']  = 'Monday';
$lang['day_tue']  = 'Tuesday';
$lang['day_wed']  = 'Wednesday';
$lang['day_thur'] = 'Thursday';
$lang['day_fri']  = 'Friday';
$lang['day_sat']  = 'Saturday';
$lang['day_dun']  = 'Sunday';

$lang['month_jan'] = 'January';
$lang['month_feb'] = 'February';
$lang['month_mar'] = 'March';
$lang['month_apr'] = 'April';
$lang['month_may'] = 'May';
$lang['month_jun'] = 'June';
$lang['month_jul'] = 'July';
$lang['month_aug'] = 'August';
$lang['month_sep'] = 'September';
$lang['month_oct'] = 'October';
$lang['month_nov'] = 'November';
$lang['month_dec'] = 'December';
