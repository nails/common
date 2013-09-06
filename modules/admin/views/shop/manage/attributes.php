<?php

	dump($_GET);



	echo '<script type="text/javascript">';

	//	This variable holds the current state of objects and is read
	//	by the calling script when closing the fancybox, feel free to
	//	update this during the lietime of the fancybox.

	$_data					= array();				//	An array of objects in the format {id,label}

	$_data[0]				= new stdClass();
	$_data[0]->id			= 123;
	$_data[0]->label		= 'something or other';

	$_data[1]				= new stdClass();
	$_data[1]->id			= 456;
	$_data[1]->label		= 'Something else';

	$_data[2]				= new stdClass();
	$_data[2]->id			= 789;
	$_data[2]->label		= 'Another thing';

	$_data[3]				= new stdClass();
	$_data[3]->id			= 888;
	$_data[3]->label		= 'Newly Added!';

	echo 'var _DATA = ' . json_encode( $_data ) . ';';
	echo '</script>';

	// --------------------------------------------------------------------------