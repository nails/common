<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class NAILS_CMS_Widget_slider extends NAILS_CMS_Widget
{
	static function details()
	{
		$_d					= parent::details();
		$_d->label			= 'Slider';
		$_d->description	= 'Embed easily configurable photo sliders into your page.';
		$_d->keywords		= 'gallery,slider,image gallery,images';

		return $_d;
	}
}