<?php

/* Load the base config file from the CodeIgniter package */
$mimes = require FCPATH . 'vendor/codeigniter/framework/application/config/mimes.php';

/**
 * Additional mimes
 * @todo - move this into the services file
 */

//  Video
$mimes['mp4']  = 'video/mp4';
$mimes['ogv']  = ['video/ogg', 'application/ogg'];
$mimes['webm'] = 'video/webm';

//  MS Office
//  http://stackoverflow.com/a/4212908/789224
$mimes['doc'] = ['application/msword', 'application/vnd.ms-office'];
$mimes['dot'] = 'application/msword';

$mimes['docx'] = ['application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/zip'];
$mimes['dotx'] = 'application/vnd.openxmlformats-officedocument.wordprocessingml.template';
$mimes['docm'] = 'application/vnd.ms-word.document.macroEnabled.12';
$mimes['dotm'] = 'application/vnd.ms-word.template.macroEnabled.12';

$mimes['xls']  = ['application/excel', 'application/vnd.ms-excel', 'application/msexcel'];
$mimes['xlt']  = 'application/vnd.ms-excel';
$mimes['xla']  = 'application/vnd.ms-excel';

$mimes['xlsx'] = ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/zip'];
$mimes['xltx'] = 'application/vnd.openxmlformats-officedocument.spreadsheetml.template';
$mimes['xlsm'] = 'application/vnd.ms-excel.sheet.macroEnabled.12';
$mimes['xltm'] = 'application/vnd.ms-excel.template.macroEnabled.12';
$mimes['xlam'] = 'application/vnd.ms-excel.addin.macroEnabled.12';
$mimes['xlsb'] = 'application/vnd.ms-excel.sheet.binary.macroEnabled.12';

$mimes['ppt']  = ['application/powerpoint', 'application/vnd.ms-powerpoint'];
$mimes['pot']  = 'application/vnd.ms-powerpoint';
$mimes['pps']  = 'application/vnd.ms-powerpoint';
$mimes['ppa']  = 'application/vnd.ms-powerpoint';

$mimes['pptx'] = ['application/vnd.openxmlformats-officedocument.presentationml.presentation', 'application/zip'];
$mimes['potx'] = 'application/vnd.openxmlformats-officedocument.presentationml.template';
$mimes['ppsx'] = 'application/vnd.openxmlformats-officedocument.presentationml.slideshow';
$mimes['ppam'] = 'application/vnd.ms-powerpoint.addin.macroEnabled.12';
$mimes['pptm'] = 'application/vnd.ms-powerpoint.presentation.macroEnabled.12';
$mimes['potm'] = 'application/vnd.ms-powerpoint.presentation.macroEnabled.12';
$mimes['ppsm'] = 'application/vnd.ms-powerpoint.slideshow.macroEnabled.12';

// --------------------------------------------------------------------------

//  Adobe Creative Suite/Cloud
//  Illustrator files are PDF files with some additional meta data
$mimes['ai'] = array_merge([$mimes['ai']], $mimes['pdf'], ['application/illustrator']);

//  PhotoShop files also have some aliases
$mimes['psd'] = array_merge([$mimes['psd']], ['application/psd', 'image/photoshop', 'image/psd', 'image/x-psd']);

//  InDesign Files
$mimes['indd'] = 'application/x-indesign';

// Adobe Swatch Exchange Files
$mimes['ase'] = 'application/octet-stream';
