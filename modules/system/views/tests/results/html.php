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

	#tests li ul
	{
		margin:0;
	}

	#tests li ul li:last-of-type
	{
		margin-bottom:0;
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
		See below the results of running <?=$summary->total?> tests.
	</p>
	<?php

		if ( $summary->fail ) :

			echo '<p class="system-alert message"><strong>Oh no!</strong> Unfortunately ' . $summary->fail . ' test(s) failed. See below for specific errors.</p>';

		else :

			echo '<p class="system-alert success"><strong>Good News Everyone!</strong> All tests passed.</p>';

		endif;
	?>
	<ol class="row" id="tests">
		<?php

			foreach ( $summary->results AS $result ) :

				echo $result->pass ? '<li class="pass">' : '<li class="fail">';
				echo '<h2>';
				echo $result->info->label;
				echo '</h2>';
				echo '<p class="description">' . $result->info->description . '</p>';
				echo '<p class="testing"><span>Testing:</span>' . $result->info->testing . '</p>';
				echo '<p class="expecting"><span>Expecting:</span>' . $result->info->expecting . '</p>';
				if ( $result->pass ) :

					echo '<p class="system-alert success">';
					echo '<strong>Success:</strong> Test passed.';
					echo '</p>';

				else :

					echo '<div class="system-alert error">';
					echo '<p>The following errors were reported by this test:</p>';
					echo '<ul>';
					foreach ( $result->errors AS $error ) :

						echo '<li>' . $error . '</li>';

					endforeach;
					echo '</ul>';

					echo '</div>';

				endif;
				echo '</li>';

			endforeach;

		?>
	</ol>

</div>