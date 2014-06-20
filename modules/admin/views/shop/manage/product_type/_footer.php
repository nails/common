<?php

	//	Build the options, requires an ID and a Label
	$_options = array();

	foreach ( $product_types AS $product_type ) :

		$_temp			= new stdClass();
		$_temp->id		= $product_type->id;
		$_temp->label	= $product_type->label;

		$_options[] = $_temp;

	endforeach;

	//	Set _DATA
	echo '<script type="text/javascript">';
	echo 'var _DATA = ' . json_encode( $_options ) . ';';
	echo '</script>';