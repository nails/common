<?=strtoupper( $email_subject )?>

---------------

<?php

	if ( isset( $sent_to->first ) && $sent_to->first ) :
	
		echo 'Hi ' . $sent_to->first . ',' . "\n\n";
	
	endif;

?>
