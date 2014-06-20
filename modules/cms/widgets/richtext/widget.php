<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class NAILS_CMS_Widget_richtext extends NAILS_CMS_Widget
{
	static function details()
	{
		$_d					= parent::details();
		$_d->label			= 'Rich Text';
		$_d->description	= 'Build beautiful pages using the rich text editor; embed images, links and more.';
		$_d->keywords		= 'rich text,formatted text,formatted,wysiwyg,embed';

		$_d->assets_editor[]	= array( 'ckeditor/ckeditor.js', 		'BOWER' );
		$_d->assets_editor[]	= array( 'ckeditor/adapters/jquery.js', 'BOWER' );

		return $_d;
	}
}