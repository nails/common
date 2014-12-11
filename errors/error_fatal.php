<!DOCTYPE html>
<html>
	<head>
		<title>Error</title>
		<meta charset="utf-8">
		<style type="text/css">

			body {
			   font-family: "HelveticaNeue-Light", "Helvetica Neue Light", "Helvetica Neue", Helvetica, Arial, "Lucida Grande", sans-serif;
			   font-weight: 300;
			   text-align: center;
			   font-size:14px;
			   color:#444;
			   margin:100px;
			}

			h1
			{
				margin:auto;
				width:400px;
				margin-bottom:30px;
			}

			p
			{
				margin:auto;
				width:400px;
			}

			p small
			{
				font-size:10px;
				display:block;
				margin-top:40px;
			}

			code
			{
				margin-top: 2em;
				display: block;
				border: 1px solid #CCC;
				background: #EFEFEF;
				padding: 1em;
			}

			code strong
			{
				font-weight: bold;
				color: red;
			}

		</style>
	</head>
	<body>
		<h1>Sorry, an error occurred ☹</h1>
		<p>
			An error occurred which we couldn't recover from. The technical team have
			been informed, we apologise for the inconvenience.
		</p>
		<?php

			if (!empty($subject) || !empty($message)) {

				if (empty($subject)) {

					$subject = 'Error:';
				}

				if (empty($message)) {

					$message = '';
				}

				echo '<code>';
					echo '<strong>' . $subject . ':</strong> ' . $message;
				echo '</code>';
			}

		?>
		<p>

		</p>
		<p>
			<small>Powered by <a href="http://nailsapp.co.uk">Nails</a></small>
		</p>
	</body>
</html>