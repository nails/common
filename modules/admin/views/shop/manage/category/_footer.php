<?php

	//	Build the options, requires an ID and a Label
	$_options = array();

	foreach ( $categories AS $category ) :

		$_temp			= new stdClass();
		$_temp->id		= $category->id;
		$_temp->label	= array();

		foreach ( $category->breadcrumbs AS $crumb ) :

			$_temp->label[] = $crumb->label;

		endforeach;

		$_temp->label = implode( ' &rsaquo; ', $_temp->label );

		$_options[] = $_temp;

	endforeach;

	//	Set _DATA
	echo '<script type="text/javascript">';
	echo 'var _DATA = ' . json_encode( $_options ) . ';';
	echo '</script>';