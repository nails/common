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
				font-weight:bold;
				margin-bottom:1em;
				padding-bottom:1em;
				border-bottom:1px solid #ececec;
			}
			
			h2
			{
				font-size:1.2em;
				font-weight:bold;
				margin-top:3em;
				border-bottom:1px solid #ececec;
				padding-bottom:0.5em;
			}
			
			h3,h4,h5,h6
			{
				font-size:1em;
				font-weight:bold;
				margin-bototm:1em;
			}
			
			p
			{
				margin-bottom:1em;
			}
			
			small
			{
				font-size:0.8em;
			}
			
			blockquote
			{
				border-left: 4px solid #EFEFEF;
				padding-left: 10px;
				margin-left: 0px;
				font-style: italic;
				font-size: 1.3em;
				font-weight: lighter;
				color: #777;
			}
			
			ul
			{
				margin:0;
				margin-bottom:1em;
				padding:0;
			}
			
			ul li
			{
				margin:0;
				padding:0;
				list-style-type: none;
			}
			
			hr
			{
				border:none;
				border-top:1px dotted #CCC;
				margin: 30px 0;
			}
			
			.footer
			{
				border-top:1px solid #ececec;
				margin-top:2em;
			}
			
			table.default-style
			{
				border:1px solid #ccc;
				width:100%;
			}
			
			table.default-style th
			{
				background:#EFEFEF;
				border-bottom:1px dotted #CCC;
			}
			
			table.default-style td
			{
				padding:10px;
			}
			
			table.default-style td.left-header-cell
			{
				width:125px;
				font-weight:bold;
				background:#ececec;
				border-right:1px solid #ccc;
			}
			
			table.default-style th.center,
			table.default-style td.center
			{
				text-align: center;
			}
			
			table.default-style th.right,
			table.default-style td.right
			{
				text-align: right;
			}
			
			table.default-style tr.line-bottom td
			{
				border-bottom:1px dotted #CCC;
			}
			
			table.default-style td small
			{
				display:block;
			}
			
			.heads-up
			{
				padding:10px;
				border:1px solid #CCC;
				background:#EFEFEF;
				-webkit-border-radius:3px;
				-moz-border-radius:3px;
				-o-border-radius:3px;
				border-radius:3px;
				
				-moz-box-shadow: 0px 1px 1px #888;
				-webkit-box-shadow: 0px 1px 1px #888;
				box-shadow: 0px 1px 1px #888;
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