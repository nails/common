<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class NAILS_CMS_Widget_html extends NAILS_CMS_Widget
{
	static function details()
	{
		$_d					= parent::details();
		$_d->label			= 'Plain Text';
		$_d->description	= 'Plain, completely unformatted text. Perfect for custom HTML.';
		$_d->keywords		= 'text,html,code,plaintext,plain text';

		return $_d;
	}
}