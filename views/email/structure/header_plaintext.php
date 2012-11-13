<?=strtoupper( $email_subject )?>

---------------

<?php

	if ( isset( $first_name ) ) :
	
		echo 'Hi ' . $first_name . ',' . "\n\n";
	
	endif;

?>
