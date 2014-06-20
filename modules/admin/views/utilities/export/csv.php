<?php

	echo implode( ',', $fields ) . "\n";


	for ( $i=0; $i< count( $data ); $i++ ) :

		$_data = array_values($data[$i]);
		for ( $x=0; $x < count( $_data ); $x++ ) :

			echo $x == 0 ? '' : ',';
			echo '"' . str_replace( '"', '""', $_data[$x] ) . '"';

		endfor;

		echo "\n";

	endfor;