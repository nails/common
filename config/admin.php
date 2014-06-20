<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| Admin Variables
| -------------------------------------------------------------------------
|
| Control aspects of admin at the app level with this config file.
|
| Full details of configurable options are available at
| TODO: Link to docs
|
*/

	$config = array();

/*
	User Meta Editing
	=================

	Specify which user meta cols to render in the edit user view (for
	that group). Can also specify the columns datatype and label.

	Basic Prototype:

	$config['user_meta_cols'][GROUP_ID]	= array();

	$config['user_meta_cols'][GROUP_ID]['COL_NAME'] = array(
		'datatype'		=> 'string|bool|id|date',
		'label'			=> 'Label to render',
		'required'		=> TRUE|FALSE,
		'validation'	=> 'form_validation|rules|',
		'join'			=> array(
			'table'		=> 'table_name',
			'id'		=> 'id_column',
			'name'		=> 'name_column',
			'order_col'	=> 'order_column',
			'order_dir'	-> 'order_dir_column'
		)
	);

	Note: the 'join' field only applies to the 'id' datatype


	---

*/