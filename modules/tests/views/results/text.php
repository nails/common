
<?=strtoupper( $page->title )?>


See below the results of running <?=$summary->total?> tests.

<?php

	if ( $summary->fail ) :

		echo 'Oh no! Unfortunately ' . $summary->fail . ' test(s) failed. See below for specific errors.' . "\n\n";

	else :

		echo 'Good News Everyone! All tests passed' . "\n\n";

	endif;

	$_counter = 1;
	foreach ( $summary->results AS $result ) :

		echo '   ' . $_counter . '. ' . strtoupper( $result->info->label )  . "\n";
		echo '   ' . $result->info->description . "\n";
		echo '   TESTING: ' . $result->info->testing . "\n";
		echo '   EXPECTING: ' . $result->info->expecting . "\n";
		echo  "\n";

		if ( $result->pass ) :

			echo '   SUCCESS: Test passed.' . "\n\n\n";

		else :

			echo '   ERROR: The following errors were reported by this test:' . "\n\n";

			foreach ( $result->errors AS $error ) :


				echo '    - ' . $error . "\n\n";

			endforeach;

			echo "\n";

		endif;

		$_counter++;

	endforeach;