<!DOCTYPE html>
<html lang="en">
<head>
<title>Error</title>
<style type="text/css">

::selection{ background-color: #E13300; color: white; }
::moz-selection{ background-color: #E13300; color: white; }
::webkit-selection{ background-color: #E13300; color: white; }

body {
	background-color: #fff;
	margin: 40px;
	font: 13px/20px normal Helvetica, Arial, sans-serif;
	color: #4F5155;
}

a {
	color: #003399;
	background-color: transparent;
	font-weight: normal;
}

h1 {
	color: #444;
	border-bottom: 1px solid #D0D0D0;
	font-size: 19px;
	font-weight: normal;
	margin: 0 0 14px 0;
	padding: 14px 15px 10px 15px;
}

h1 img
{
	vertical-align:middle;
	position:relative;
	top:-10px;
	float:right;
	-webkit-opacity:0.2;
	-moz-opacity:0.2;
	opacity:0.2;
	-ms-filter: "progid:DXImageTransform.Microsoft.Alpha(Opacity=20)";
	filter: alpha(opacity=20);
}

small {
	color: #444;
	background-color: #f0f0f0;
	border-top: 1px solid #D0D0D0;
	border-bottom: 1px solid #D0D0D0;
	font-size: 10px;
	font-weight: normal;
	margin: 0 0 14px 0;
	padding: 14px 15px 10px 15px;
	display:block;
}

code {
	font-family: Consolas, Monaco, Courier New, Courier, monospace;
	font-size: 12px;
	background-color: #f9f9f9;
	border: 1px solid #D0D0D0;
	color: #002166;
	display: block;
	margin: 14px 0 14px 0;
	padding: 12px 10px 12px 10px;
}

#container {
	margin: 10px;
	border: 1px solid #D0D0D0;
	-webkit-box-shadow: 0 0 8px #D0D0D0;
	border-radius: 6px;
	width: 650px;
	margin: 10px auto;
}

p {
	margin: 12px 15px 12px 15px;
}
</style>
</head>
<body>
	<div id="container">
		<h1>
			<?=img( array( 'src' => NAILS_URL . 'img/icons/fail-whale.png', 'height' => 35 ) )?>
			<?php echo $heading; ?>
		</h1>
		<?php echo $message; ?>
		<small><?=lang( 'error_footer', 'hello@shedcollective.org' )?></small>
	</div>
</body>
</html>