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
				font-size:13px;
				font-family:"HelveticaNeue-Light", "Helvetica Neue Light", "Helvetica Neue", Helvetica, Arial, "Lucida Grande", sans-serif; 
				line-height:1.75em;
				max-width:600px;
				margin:auto;
				color:#333;
			}
			
			.padder
			{
				padding: 20px;
			}
			
			h1
			{
				line-height:1.5em;
				font-style:italic;
				margin-bottom:1em;
				padding-bottom:1em;
				border-bottom:1px solid #ececec;
			}
			
			p
			{
				margin-bottom:1em;
			}
			
			small
			{
				font-size:0.8em;
			}
			
			.footer
			{
				border-top:1px solid #ececec;
				margin-top:2em;
			}
		
		</style>
	</head>
	<body>
	<div id="BodyImposter">
	<div class="padder">
	<?php
	
		echo '<h1>' . $email_subject . '</h1>';
	
		if ( isset( $sent_to->first ) && $sent_to->first ) :
		
			echo '<p>Hi ' . $sent_to->first . ',</p>';
		
		endif;
	
	?>