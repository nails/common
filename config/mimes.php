<?php

/* Load the base config file from the CodeIgniter package */
require FCPATH . 'vendor/rogeriopradoj/codeigniter/application/config/mimes.php';

/**
 * Additional mimes
 */

//  Video
$mimes['mp4']  = 'video/mp4';
$mimes['ogv']  = array('video/ogg', 'application/ogg');
$mimes['webm'] = 'video/webm';

//  MS Office
//  http://stackoverflow.com/a/4212908/789224
$mimes['doc'] = array('application/msword', 'application/vnd.ms-office');
$mimes['dot'] = 'application/msword';

$mimes['docx'] = array('application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/zip');
$mimes['dotx'] = 'application/vnd.openxmlformats-officedocument.wordprocessingml.template';
$mimes['docm'] = 'application/vnd.ms-word.document.macroEnabled.12';
$mimes['dotm'] = 'application/vnd.ms-word.template.macroEnabled.12';

$mimes['xls']  = array('application/excel', 'application/vnd.ms-excel', 'application/msexcel');
$mimes['xlt']  = 'application/vnd.ms-excel';
$mimes['xla']  = 'application/vnd.ms-excel';

$mimes['xlsx'] = array('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/zip');
$mimes['xltx'] = 'application/vnd.openxmlformats-officedocument.spreadsheetml.template';
$mimes['xlsm'] = 'application/vnd.ms-excel.sheet.macroEnabled.12';
$mimes['xltm'] = 'application/vnd.ms-excel.template.macroEnabled.12';
$mimes['xlam'] = 'application/vnd.ms-excel.addin.macroEnabled.12';
$mimes['xlsb'] = 'application/vnd.ms-excel.sheet.binary.macroEnabled.12';

$mimes['ppt']  = array('application/powerpoint', 'application/vnd.ms-powerpoint');
$mimes['pot']  = 'application/vnd.ms-powerpoint';
$mimes['pps']  = 'application/vnd.ms-powerpoint';
$mimes['ppa']  = 'application/vnd.ms-powerpoint';

$mimes['pptx'] = array('application/vnd.openxmlformats-officedocument.presentationml.presentation', 'application/zip');
$mimes['potx'] = 'application/vnd.openxmlformats-officedocument.presentationml.template';
$mimes['ppsx'] = 'application/vnd.openxmlformats-officedocument.presentationml.slideshow';
$mimes['ppam'] = 'application/vnd.ms-powerpoint.addin.macroEnabled.12';
$mimes['pptm'] = 'application/vnd.ms-powerpoint.presentation.macroEnabled.12';
$mimes['potm'] = 'application/vnd.ms-powerpoint.presentation.macroEnabled.12';
$mimes['ppsm'] = 'application/vnd.ms-powerpoint.slideshow.macroEnabled.12';
