<?php

	/**
	 *	THE 404 PAGE
	 *	
	 *	This view contains only the basic 404 status page. The controller
	 *	will look for an app version of the file  first and load that up. It will fall back
	 *	to the empty Nails view if not available (which includes some basic styling so
	 *	as not to look totally rubbish).
	 *	
	 *	You can completely overload this view by creating a view at:
	 *	
	 *	application/views/system/404
	 *	
	 **/
?>

	<p>
		<?=lang( '404_text' )?>
	</p>