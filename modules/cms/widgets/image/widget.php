<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class NAILS_CMS_Widget_image extends NAILS_CMS_Widget
{
	static function details()
	{
		$_d					= parent::details();
		$_d->label			= 'Image';
		$_d->description	= 'A single image.';
		$_d->keywords		= 'image,images,photo,photos';

		return $_d;
	}
}