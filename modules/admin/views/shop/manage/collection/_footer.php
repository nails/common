<?php

	//	Build the options, requires an ID and a Label
	$_options = array();

	foreach ( $collections AS $collection ) :

		$_temp			= new stdClass();
		$_temp->id		= $collection->id;
		$_temp->label	= $collection->label;

		$_options[] = $_temp;

	endforeach;

	//	Set _DATA
	echo '<script type="text/javascript">';
	echo 'var _DATA = ' . json_encode( $_options ) . ';';
	echo '</script>';