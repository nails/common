<?php

	//	Build the options, requires an ID and a Label
	$_options = array();

	foreach ( $ranges AS $range ) :

		$_temp			= new stdClass();
		$_temp->id		= $range->id;
		$_temp->label	= $range->label;

		$_options[] = $_temp;

	endforeach;

	//	Set _DATA
	echo '<script type="text/javascript">';
	echo 'var _DATA = ' . json_encode( $_options ) . ';';
	echo '</script>';