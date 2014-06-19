<?php

	//	Set _DATA
	echo '<script type="text/javascript">';
	echo 'var _DATA = ' . json_encode( $tags ) . ';';
	echo '</script>';