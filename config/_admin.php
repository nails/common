<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| Admin Variables
| -------------------------------------------------------------------------
| 
| Control aspects of admin at the app level with this config file. To use this
| file make sure it is named 'admin.php'.
| 
| Docs available at http://nails.shedcollective.org/docs/admin/config
| 
*/
	
	/**
	 *	USER_META_COLS
	 *	
	 *	Specify which user meta cols to render in the edit user view (for
	 *	that group). Can also specify the columns datatype and label.
	 *	
	 *	Basic Prototype:
	 *	
	 *	$config['user_meta_cols'][GROUP_ID]	= array();
	 *	
	 *	$config['user_meta_cols'][GROUP_ID]['COL_NAME'] = array(
	 *		'datatype'		=> 'string|bool|join|date',
	 *		'label'			=> 'Label to render',
	 *		'required'		=> TRUE|FALSE,
	 *		'validation'	=> 'form_validation|rules|',
	 *		'join'			=> array(
	 *			'table'		=> 'table_name',
	 *			'id'		=> 'id_column',
	 *			'name'		=> 'name_column',
	 *			'order_col'	=> 'order_column',
	 *			'order_dir'	-> 'order_dir_column'
	 *		)
	 *	);
	 *	
	 *	Note: the 'join' field only applies to the 'id' datatype
	 *	
	 **/
	 
	 
	 
/* End of file admin.php */
/* Location: ./application/config/admin.php */