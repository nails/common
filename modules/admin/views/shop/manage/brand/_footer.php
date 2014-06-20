<?php

	//	Build the options, requires an ID and a Label
	$_options = array();

	foreach ( $brands AS $brand ) :

		$_temp			= new stdClass();
		$_temp->id		= $brand->id;
		$_temp->label	= $brand->label;

		$_options[] = $_temp;

	endforeach;

	//	Set _DATA
	echo '<script type="text/javascript">';
	echo 'var _DATA = ' . json_encode( $_options ) . ';';
	echo '</script>';