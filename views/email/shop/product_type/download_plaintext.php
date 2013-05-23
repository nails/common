<?php

	echo 'Thanks for ordering this downloadable content from ' . APP_NAME . '. Please find below your download links. The order reference for these files is ' . $order->ref . '.' . "\n\n";

	$_plural = count( $urls ) > 1 ? TRUE : FALSE;

	if ( $_plural ) :

		echo 'PLEASE NOTE: these links are only valid for ' . ( $expires/60/60 ) . ' hours after which the links will stop working';

	else :

		echo 'PLEASE NOTE: this link is only valid for ' . ( $expires/60/60 ) . ' hours after which it will stop working';

	endif;
	
	if ( $sent_to->id ) :
	
		echo '; however you can always log in to your ' . APP_NAME . ' account to access your downloads any time.' . "\n\n";
	
	else :
	
		echo '. If you register an account with us using this email (' . $sent_to->email . ') then we\'ll automatically associate your previous orders so you can always access your files.' . "\n\n";
		
	endif;
	
	
	echo '---------------' . "\n\n";
	
	foreach ( $urls AS $url ) :
	
		echo $url->title . "\n";
		echo '{unwrap}' . $url->url . "{/unwrap}\n\n";
	
	endforeach;
	
	echo '---------------' . "\n\n";
	echo 'Thanks again for shopping with ' . APP_NAME . ', your business is very much appreciated!';