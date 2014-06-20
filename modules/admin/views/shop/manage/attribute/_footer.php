<?php

	//	Build the options, requires an ID and a Label
	$_options = array();

	foreach ( $attributes AS $attribute ) :

		$_temp			= new stdClass();
		$_temp->id		= $attribute->id;
		$_temp->label	= $attribute->label;

		$_options[] = $_temp;

	endforeach;

	//	Set _DATA
	echo '<script type="text/javascript">';
	echo 'var _DATA = ' . json_encode( $_options ) . ';';
	echo '</script>';