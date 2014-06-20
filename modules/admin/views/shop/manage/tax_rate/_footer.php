<?php

	//	Build the options, requires an ID and a Label
	$_options = array();

	$_temp			= new stdClass();
	$_temp->id		= '';
	$_temp->label	= 'No Tax';

	$_options[] = $_temp;

	foreach ( $tax_rates AS $tax ) :

		$_temp			= new stdClass();
		$_temp->id		= $tax->id;
		$_temp->label	= $tax->label;

		$_options[] = $_temp;

	endforeach;

	//	Set _DATA
	echo '<script type="text/javascript">';
	echo 'var _DATA = ' . json_encode( $_options ) . ';';
	echo '</script>';