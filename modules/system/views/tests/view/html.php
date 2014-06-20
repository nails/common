<style type="text/css">

	#tests li
	{
		border:1px solid #CCC;
		-webkit-border-radius:3px;
		-moz-border-radius:3px;
		-o-border-radius:3px;
		border-radius:3px;
		padding:10px 20px;
		background:#FFF;
	}

	#tests li h2
	{
		font-size:1em;
	}

	#tests li h2 input
	{
		margin-right:10px;
	}

	#tests li p
	{
		margin-bottom:0.5em;
	}

	#tests li p.testing span,
	#tests li p.expecting span
	{
		display:inline-block;
		font-weight:bold;
		width:80px;
		margin-right:5px;
	}

</style>

<div class="container">

	<h1 class="row"><?=$page->title?></h1>
	<p class="row">
		The following tests are configured to run on this application. Select which tests you'd like to execute and hit the 'Run Tests' button below to begin testing.
	</p>
	<p class="row system-alert message">
		<strong>Please Remember: </strong> Tests can sometimes take a while to execute. If you're finding page timeouts occur or out of memory limits please try running these tests from the command line.
	</p>

	<?php

		$_query_string = isset( $_SERVER['QUERY_STRING'] ) && $_SERVER['QUERY_STRING'] ? '?' . $_SERVER['QUERY_STRING'] : '';
		echo form_open( 'system/test/run' . $_query_string );
	?>
	<ol class="row" id="tests">
		<?php

			foreach ( $tests AS $test ) :


				echo '<li>';
				echo '<h2>';
				echo form_checkbox( 'test[]', $test->test, TRUE );
				echo $test->label;
				echo '</h2>';
				echo '<p class="description">' . $test->description . '</p>';
				echo '<p class="testing"><span>Testing:</span>' . $test->testing . '</p>';
				echo '<p class="expecting"><span>Expecting:</span>' . $test->expecting . '</p>';
				echo '</li>';

			endforeach;

		?>
	</ol>
	<p class="row" style="text-align:center;padding-bottom:2em;">
		<?=form_submit( 'submit', 'Run Tests', 'class="awesome green huge"' )?>
	</p>
	<?=form_close()?>

</div>