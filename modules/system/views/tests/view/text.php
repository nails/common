
<?=strtoupper( $page->title )?>


The following tests are configured to run on this application. To execute these tests run `index.php system tests run`.

<?php

	$_counter = 1;
	foreach ( $tests AS $test ) :


		echo '   ' . $_counter . '. ' . strtoupper( $test->label ) . "\n";
		echo '   ' . $test->description . "\n";
		echo '   TESTING: ' . $test->testing . "\n";
		echo '   EXPECTING: ' . $test->expecting . "\n";
		echo  "\n";

		$_counter++;

	endforeach;

?>