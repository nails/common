<html>
<head>
	<title>Please Wait...</title>
	<meta http-equiv="content-type" content="text/html;charset=utf-8" />
	<link rel="stylesheet" href="<?=NAILS_URL?>css/nails.default.css" type="text/css" media="screen" charset="utf-8"
</head>
<style type="text/css">

	body
	{
		text-align:center;
		padding:30px;
	}
	
	h2
	{
		line-height:1em;
		padding:15px;
		padding-bottom:20px;
		font-size:12px;
	}
	
	h2 img
	{
		margin:0;
		position:relative;
		top:3px;
		left:-5px;
	}
	img
	{
		margin:30px;
		margin-bottom:50px;
	}

</style>
<body>

<h1>
	Please wait while we redirect you to PayPal
</h1>
<h2>
	<?=img( NAILS_URL . '/img/modules/shop/payment-gateway/lock-locked-icon.png' )?> All transactions are secure.
</h2>
<?php

	echo img( NAILS_URL . '/img/loader/20px-000000-TRANS.gif' );
	
	echo form_open( $paypal->url );
	
	// --------------------------------------------------------------------------
	
	//	Basics for transaction
	echo form_hidden( 'cmd',			'_cart' );
	echo form_hidden( 'charset',		'utf-8' );
	echo form_hidden( 'upload',			FALSE );
	echo form_hidden( 'business',		$paypal->business );
	
	// --------------------------------------------------------------------------
	
	//	Shipping
	echo form_hidden( 'no_shipping',	TRUE );
	
	// --------------------------------------------------------------------------
	
	//	Items
	$_counter = 1;
	foreach ( $basket->items AS $item ) :
	
		echo form_hidden( 'item_name_' . $_counter,		$item->title );
		echo form_hidden( 'item_number_' . $_counter,	$item->id );
		
		if ( $item->is_on_sale ) :
		
			echo form_hidden( 'amount_' . $_counter,	$item->sale_price );
			
		else :
		
			echo form_hidden( 'amount_' . $_counter,	$item->price );
		
		endif;
		
		echo form_hidden( 'tax_' . $_counter,			$item->tax );
		echo form_hidden( 'quantity_' . $_counter,		$item->quantity );
		
		
	$_counter++;
	endforeach;
	
	// --------------------------------------------------------------------------
	
	//	Verifiers
	echo form_hidden( 'invoice',		$order->id );
	echo form_hidden( 'custom',			$this->encrypt->encode( md5( $order->ref . ':' . $order->code ), APP_PRIVATE_KEY ) );
	
	// --------------------------------------------------------------------------
	
	//	URLS
	echo form_hidden( 'notify_url',		$paypal->notify );
	echo form_hidden( 'cancel_return',	$paypal->cancel . '?ref=' . $order->ref );
	echo form_hidden( 'return',			$paypal->processing . '?ref=' . $order->ref );
	
	// --------------------------------------------------------------------------
	
	//	Misc
	echo form_hidden( 'no_note',		TRUE );
	echo form_hidden( 'currency_code',	SHOP_USER_CURRENCY_CODE );
	
	// --------------------------------------------------------------------------
	
	echo form_submit( 'go', 'If you have not been redirected within 5 seconds, please click here' );
	
	echo form_close();
	
	// --------------------------------------------------------------------------
	
	if ( ENVIRONMENT != 'production' ) :
	
		//	Not on production so offer a button to test the notification
		switch( $order->payment_gateway->slug ) :
		
			case 'paypal' :
			
				echo '<p>' . anchor( 'shop/checkout/notify/' . $order->payment_gateway->slug . '?testing=true&ref='. $order->ref, 'Testing: Simulate Successful Payment', 'class="awesome small"' ) . '</p>';
			
			break;
		
		endswitch;
	
	endif;


?>

<script type="text/javascript">

	//var t = setTimeout( "document.forms[0].submit();",2000 );

</script>

</body>
</html>