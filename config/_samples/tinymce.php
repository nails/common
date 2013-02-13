<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| TinyMCE Library
| -------------------------------------------------------------------------
| 
|
| -------------------------------------------------------------------------
| DEFAULT SETTINGS
| -------------------------------------------------------------------------
| 
| Override the default settings of TinyMCE
| 
| Prototype: $config['setting']['setting_name'] = 'setting_value';
|
*/


	$config['setting']['script_url']						= site_url('assets/core/js/system/_tiny_mce/tiny_mce.js');
	$config['setting']['theme']								= 'advanced';
	$config['setting']['plugins']							= 'fullscreen,paste';
	$config['setting']['document_base_url']					= site_url();
	$config['setting']['relative_urls']						= FALSE;
	
	//	Extend valid elements
	$config['setting']['extended_valid_elements'][]			= 'div[*]';
	$config['setting']['extended_valid_elements'][]			= 'h1[*]';
	$config['setting']['extended_valid_elements'][]			= 'h2[*]';
	$config['setting']['extended_valid_elements'][]			= 'h3[*]';
	$config['setting']['extended_valid_elements'][]			= 'h4[*]';
	$config['setting']['extended_valid_elements'][]			= 'h5[*]';
	$config['setting']['extended_valid_elements'][]			= 'h6[*]';
	$config['setting']['extended_valid_elements'][]			= 'p[*]';
	$config['setting']['extended_valid_elements'][]			= 'iframe[*]';
	$config['setting']['extended_valid_elements'][]			= 'script[*]';
	
	//	Theme Options
	$config['setting']['theme_advanced_buttons1'][]			= 'bold';
	$config['setting']['theme_advanced_buttons1'][]			= 'italic';
	$config['setting']['theme_advanced_buttons1'][]			= 'underline';
	$config['setting']['theme_advanced_buttons1'][]			= 'strikethrough';
	$config['setting']['theme_advanced_buttons1'][]			= '|';
	$config['setting']['theme_advanced_buttons1'][]			= 'justifyleft';
	$config['setting']['theme_advanced_buttons1'][]			= 'justifycenter';
	$config['setting']['theme_advanced_buttons1'][]			= 'justifyright';
	$config['setting']['theme_advanced_buttons1'][]			= 'formatselect';
	$config['setting']['theme_advanced_buttons1'][]			= '|';
	$config['setting']['theme_advanced_buttons1'][]			= 'link';
	$config['setting']['theme_advanced_buttons1'][]			= 'unlink';
	$config['setting']['theme_advanced_buttons1'][]			= 'image';
	$config['setting']['theme_advanced_buttons1'][]			= '|';
	$config['setting']['theme_advanced_buttons1'][]			= 'fullscreen';
	$config['setting']['theme_advanced_buttons1'][]			= 'code';
	
	
	$config['setting']['theme_advanced_buttons2'][]			= '';
	$config['setting']['theme_advanced_buttons3'][]			= '';
	$config['setting']['theme_advanced_toolbar_location']	= 'top';
	$config['setting']['theme_advanced_toolbar_align']		= 'left';
	$config['setting']['theme_advanced_statusbar_location']	= 'none';
	$config['setting']['theme_advanced_resizing']			= TRUE;
	
	//	Example content CSS
	$config['setting']['content_css']						= site_url('assets/core/css/system/_rte_editor.css');
	
	//	Drop lists for link/image/media/template dialogs
	//$config['setting']['template_external_list_url']		= 'lists/template_list.js';
	//$config['setting']['external_link_list_url']			= 'lists/link_list.js';
	//$config['setting']['external_image_list_url']			= 'lists/image_list.js';
	//$config['setting']['medNAILS_external_list_url']			= 'lists/medNAILS_list.js';
	
	//	Paste settings
	$config['setting']['paste_preprocess']					= 'function(pl, o) {'.
															  '	o.content = o.content.replace( /<style[\s\S]*?style>/igs, \'\' );'.
															  '	o.content = strip_tags( o.content,\'<p><ul><li>\' );'.
															  '	o.content = o.content.replace( /<p.*?>\s?<\/p>/ig, \'\');'.
															  '}';

	//	Skin
	$config['setting']['skin']								= 'cirkuit';
	
	//	File Uploader
	$config['setting']['relative_urls']						= FALSE;
	$config['setting']['file_browser_callback']				= '

function(field_name, url, type, win) {
	  tinyMCE.activeEditor.windowManager.open({
	      file : "madfilebrowser/mfm.php?field=" + field_name + "&url=" + url + "",
	      title : \'File Manager\',
	      width : 640,
	      height : 450,
	      resizable : "no",
	      inline : "yes",
	      close_previous : "no"
	  }, {
	      window : win,
	      input : field_name
	  });
	  return false;
	}
	
	';

/* End of file tinymce.php */
/* Location: ./application/config/tinymce.php */