<!DOCTYPE html>
<html lang="en">
	<head>
	
		<!--	META	-->
		<meta charset="utf-8">
		<title><?=isset( $page->title ) ? $page->title . ' - ' : NULL?><?=APP_NAME?></title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="description" content="">
		<meta name="author" content="">
		
		<!--	JS GLOBALS	-->
		<script tyle="text/javascript">
			window.NAILS_URL = '<?=NAILS_URL?>';
		</script>
		
		<!--	STYLES	-->
		<link href="<?=NAILS_URL?>css/twitter-bootstrap/bootstrap.min.css" rel="stylesheet">
		<link href="<?=NAILS_URL?>css/twitter-bootstrap/bootstrap-responsive.min.css" rel="stylesheet">
		<link href="<?=NAILS_URL?>css/nails.default.css" rel="stylesheet">
		
		<!--	JAVASCRIPT	-->
		<script type="text/javascript" src="https://www.google.com/jsapi"></script>
		<script type="text/javascript">
			google.load( "jquery" );
			google.load( "jqueryui" );
		</script>
		
		<!--	HTML5 shim, for IE6-8 support of HTML5 elements	-->
		<!--[if lt IE 9]>
			<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
		<![endif]-->
		
	</head>
	<body>
	
	<!--	NAILS MASTHEAD	-->
	<div class="navbar navbar-fixed-top" id="masthead">
		<div class="navbar-inner">
			<div class="container">
				<h1><a href="<?=site_url()?>" class="brand"><?=APP_NAME?></a></h1>
			</div>
		</div>
	</div>
	
	
	<!--	PAGE CONTENT	-->
	<div class="container" id="page-content">
		
		<h2><?=isset( $page->title ) ? $page->title: NULL?></h2>
		<hr />
		
		<?=( $message )	? '<div class="alert">' . $message . '</div>'				: NULL ?>
		<?=( $notice )	? '<div class="alert alert-info">' . $notice . '</div>'		: NULL ?>
		<?=( $error )	? '<div class="alert alert-error">' . $error . '</div>'		: NULL ?>
		<?=( $success )	? '<div class="alert alert-success">' . $success . '</div>'	: NULL ?>