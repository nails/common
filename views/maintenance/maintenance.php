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
	</head>
	<body>
		<h1>Down for Maintenance</h1>
		<p><?=APP_NAME?> will be back soon</p>
	</body>
</html>