<?php

	/**
	 *	THE MAINTENANCE PAGE
	 *
	 *	This view contains the fallback 'down for maintenance' page. The app will
	 *	render this page if it can't find a file in the main application folder.
	 *
	 *	You can completely overload this view by creating a view at:
	 *
	 *	application/views/maintenance/maintenance
	 *
	 **/


	// --------------------------------------------------------------------------

	//	Write the HTML for the activation failed page
?>
<!DOCTYPE html>
<html>
	<head>
		<title><?=APP_NAME?> is down for maintenance</title>
		<meta charset="utf-8">

		<!--	STYLES	-->
		<link href="<?=NAILS_ASSETS_URL?>css/nails.default.css" rel="stylesheet">

		<style type="text/css">

			#main-col
			{
				text-align:center;
				margin-top:100px;
			}

		</style>

	</head>
	<body>
		<div class="container row">
			<div class="six columns first last offset-by-five" id="main-col">
				<h1>down for maintenance</h1>
				<hr />
				<p>Please bear with us while we pull back the curtains and under go some scheduled maintenance.</p>
			</div>
		</div>
	</body>
</html>