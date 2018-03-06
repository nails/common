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
$lang['fv_identity_already_registered'] = 'This %s is already registered.<br /><small><a href="%s">Forgotten your password?</a></small>';
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
$lang['fv_is_natural']                  = 'This field must be a positive number.';
$lang['fv_is_natural_field']            = 'The {field} field must contain only positive numbers.';
$lang['fv_is_natural_no_zero']          = 'This field must be a positive number greater than zero.';
$lang['fv_is_natural_no_zero_field']    = 'The {field} field must contain only positive numbers greater than zero.';
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

// --------------------------------------------------------------------------

/**
 * Stop Words - common words which bring little search value to a string
 * This list is as used by MySQL's FULLTEXT feature
 */

$lang['string_helper_stop_words']  = 'a\'s,a,able,about,above,according,accordingly,across,actually,after,afterwards,';
$lang['string_helper_stop_words'] .= 'again,against,ain\'t,all,allow,allows,almost,alone,along,already,also,although,';
$lang['string_helper_stop_words'] .= 'always,am,among,amongst,an,and,another,any,anybody,anyhow,anyone,anything,';
$lang['string_helper_stop_words'] .= 'anyway,anyways,anywhere,apart,appear,appreciate,appropriate,aren\'t,are,around,';
$lang['string_helper_stop_words'] .= 'as,aside,ask,asking,associated,at,available,away,awfully,be,became,because,';
$lang['string_helper_stop_words'] .= 'become,becomes,becoming,been,before,beforehand,behind,being,believe,below,';
$lang['string_helper_stop_words'] .= 'beside,besides,best,better,between,beyond,both,brief,but,by,c\'mon,c\'s,came,';
$lang['string_helper_stop_words'] .= 'can\'t,can,cannot,cant,cause,causes,certain,certainly,changes,clearly,co,com,';
$lang['string_helper_stop_words'] .= 'come,comes,concerning,consequently,consider,considering,contain,containing,';
$lang['string_helper_stop_words'] .= 'contains,corresponding,couldn\'t,could,course,currently,definitely,described,';
$lang['string_helper_stop_words'] .= 'despite,did,didn\'t,different,do,doesn\'t,does,doing,don\'t,done,down,';
$lang['string_helper_stop_words'] .= 'downwards,during,each,edu,eg,eight,either,else,elsewhere,enough,entirely,';
$lang['string_helper_stop_words'] .= 'especially,et,etc,even,ever,every,everybody,everyone,everything,everywhere,';
$lang['string_helper_stop_words'] .= 'ex,exactly,example,except,far,few,fifth,first,five,followed,following,';
$lang['string_helper_stop_words'] .= 'follows,for,former,formerly,forth,four,from,further,furthermore,get,gets,';
$lang['string_helper_stop_words'] .= 'getting,given,gives,go,goes,going,gone,got,gotten,greetings,had,hadn\'t,';
$lang['string_helper_stop_words'] .= 'happens,hardly,hasn\'t,has,haven\'t,have,having,he\'s,he,hello,help,hence,';
$lang['string_helper_stop_words'] .= 'her,here\'s,here,hereafter,hereby,herein,hereupon,hers,herself,hi,him,himself,';
$lang['string_helper_stop_words'] .= 'his,hither,hopefully,how,howbeit,however,i\'d,i\'ll,i\'m,i\'ve,ie,if,ignored,';
$lang['string_helper_stop_words'] .= 'immediate,in,inasmuch,inc,indeed,indicate,indicated,indicates,inner,insofar,';
$lang['string_helper_stop_words'] .= 'instead,into,inward,isn\'t,is,it\'d,it\'ll,it\'s,its,it,itself,just,keep,keeps,';
$lang['string_helper_stop_words'] .= 'kept,know,knows,known,last,lately,later,latter,latterly,least,less,lest,let\'s,';
$lang['string_helper_stop_words'] .= 'let,like,liked,likely,little,look,looking,looks,ltd,mainly,many,may,maybe,';
$lang['string_helper_stop_words'] .= 'me,mean,meanwhile,merely,might,more,moreover,most,mostly,much,must,my,myself,';
$lang['string_helper_stop_words'] .= 'name,namely,nd,near,nearly,necessary,need,needs,neither,never,nevertheless,new,';
$lang['string_helper_stop_words'] .= 'next,nine,no,nobody,non,none,noone,nor,normally,not,nothing,novel,now,nowhere,';
$lang['string_helper_stop_words'] .= 'obviously,of,off,often,oh,ok,okay,old,on,once,one,ones,only,onto,or,other,';
$lang['string_helper_stop_words'] .= 'others,otherwise,ought,our,ours,ourselves,out,outside,over,overall,own,';
$lang['string_helper_stop_words'] .= 'particular,particularly,per,perhaps,placed,please,plus,possible,presumably,';
$lang['string_helper_stop_words'] .= 'probably,provides,que,quite,qv,rather,rd,re,really,reasonably,regarding,';
$lang['string_helper_stop_words'] .= 'regardless,regards,relatively,respectively,right,said,same,saw,say,saying,';
$lang['string_helper_stop_words'] .= 'says,second,secondly,see,seeing,seem,seemed,seeming,seems,seen,self,selves,';
$lang['string_helper_stop_words'] .= 'sensible,sent,serious,seriously,seven,several,shall,she,should,shouldn\'t,';
$lang['string_helper_stop_words'] .= 'since,six,so,some,somebody,somehow,someone,something,sometime,sometimes,';
$lang['string_helper_stop_words'] .= 'somewhat,somewhere,soon,sorry,specified,specify,specifying,still,sub,such,';
$lang['string_helper_stop_words'] .= 'sup,sure,t\'s,take,taken,tell,tends,th,than,thank,thanks,thanx,that\'s,';
$lang['string_helper_stop_words'] .= 'thats,that,the,their,theirs,them,themselves,then,thence,there\'s,there,';
$lang['string_helper_stop_words'] .= 'thereafter,thereby,therefore,therein,theres,thereupon,these,they\'d,they\'ll,';
$lang['string_helper_stop_words'] .= 'they\'re,they\'ve,they,think,third,this,thorough,thoroughly,those,though,three,';
$lang['string_helper_stop_words'] .= 'through,throughout,thru,thus,to,together,too,took,toward,towards,tried,';
$lang['string_helper_stop_words'] .= 'tries,truly,try,trying,twice,two,un,under,unfortunately,unless,unlikely,';
$lang['string_helper_stop_words'] .= 'until,unto,up,upon,us,use,used,useful,uses,using,usually,value,various,';
$lang['string_helper_stop_words'] .= 'very,via,viz,vs,want,wants,wasn\'t,was,way,we\'d,we\'ll,we\'re,we\'ve,we,';
$lang['string_helper_stop_words'] .= 'welcome,well,went,weren\'t,were,what\'s,what,whatever,when,whence,whenever,';
$lang['string_helper_stop_words'] .= 'where\'s,where,whereafter,whereas,whereby,wherein,whereupon,wherever,whether,';
$lang['string_helper_stop_words'] .= 'which,while,whither,who\'s,who,whoever,whole,whom,whose,why,will,willing,wish,';
$lang['string_helper_stop_words'] .= 'with,within,without,won\'t,wonder,wouldn\'t,would,yes,yet,you\'d,';
$lang['string_helper_stop_words'] .= 'you\'ll,you\'re,you\'ve,you,yours,your,yourself,yourselves,zero,';

//  And a few others which we feel don't add much to a sentence's context either
$lang['string_helper_stop_words'] .= 'let\'s,lets,cm,mm,m';
