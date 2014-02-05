<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class NAILS_CMS_Widget_table extends NAILS_CMS_Widget
{
	static function details()
	{
		$_d					= parent::details();
		$_d->label			= 'Table';
		$_d->description	= 'Easily build a table';
		$_d->keywords		= 'table,tabular data,data';

		return $_d;
	}
}