<!DOCTYPE html>
<html>
	<head>
		<title></title>
		<meta charset="utf-8">
		<style type="text/css">
		
			body,
			#BodyImposter
			{
				margin:0;
				padding:0;
				font-size:12px;
				font-family:helvetica,arial,sans-serif;
				line-height:1.3em;
			}
			
			body .padder,
			#BodyImposter .padder
			{
				padding: 20px;
			}
			
			body p,
			#BodyImposter p
			{
				margin-bottom:1em;
			}
			
			body small,
			#BodyImposter small
			{
				font-size:0.8em;
			}
		
		</style>
	</head>
	<body>
	<div id="BodyImposter">
	<div class="padder">
	<?php
	
		echo '<h1>' . $email_subject . '</h1>';
	
		if ( isset( $first_name ) ) :
		
			echo '<p>Hi ' . $first_name . ',</p>';
		
		endif;
	
	?>