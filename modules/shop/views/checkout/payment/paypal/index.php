<html>
<head>
	<title>Please Wait...</title>
	<meta http-equiv="content-type" content="text/html;charset=utf-8" />
	<link rel="stylesheet" href="<?=NAILS_ASSETS_URL?>css/nails.default.css" type="text/css" media="screen" charset="utf-8"
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
	<?=img( NAILS_ASSETS_URL . '/img/modules/shop/payment-gateway/lock-locked-icon.png' )?> All transactions are secure.
</h2>
<?php

	echo img( NAILS_ASSETS_URL . '/img/loader/20px-TRANS.gif' );

	echo form_open( $paypal->url );

	// --------------------------------------------------------------------------

	//	Basics for transaction
	echo form_hidden( 'cmd',			'_cart' );
	echo form_hidden( 'charset',		'utf-8' );
	echo form_hidden( 'upload',			FALSE );
	echo form_hidden( 'business',		$paypal->business );

	// --------------------------------------------------------------------------

	//	Tell PayPal not to prompt for shipping details
	echo form_hidden( 'no_shipping',	TRUE );

	// --------------------------------------------------------------------------

	//	Items
	$_counter = 1;
	foreach ( $basket->items AS $item ) :

		echo form_hidden( 'item_name_' . $_counter,		$item->title );
		echo form_hidden( 'item_number_' . $_counter,	$item->id );

		if ( $item->is_on_sale ) :

			echo form_hidden( 'amount_' . $_counter,	shop_format_price( $item->sale_price_render, FALSE ) );

		else :

			echo form_hidden( 'amount_' . $_counter,	shop_format_price( $item->price_render, FALSE ) );

		endif;

		echo form_hidden( 'quantity_' . $_counter,		$item->quantity );
		echo form_hidden( 'shipping_' . $_counter,		shop_format_price( ( $item->shipping_render + $item->shipping_tax ), FALSE ) );


	$_counter++;
	endforeach;

	// --------------------------------------------------------------------------

	//	Shipping, Taxes
	echo form_hidden( 'tax_cart',		shop_format_price( $basket->totals->tax_items_render, FALSE ) );

	// --------------------------------------------------------------------------

	//	Voucher
	if ( $basket->discount->shipping || $basket->discount->items ) :

		echo form_hidden( 'discount_amount_cart',	shop_format_price( $basket->discount->items_render + $basket->discount->shipping_render, FALSE ) );

	endif;

	// --------------------------------------------------------------------------

	//	Verifiers
	echo form_hidden( 'invoice',		$order->ref );
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

				echo '<p>';
				echo anchor( app_setting( 'url', 'shop' ) . 'checkout/notify/' . $order->payment_gateway->slug . '?testing=true&ref='. $order->ref, 'Testing: Simulate Successful Payment', 'class="awesome small"' );
				echo '<br /><small>Auto advance disabled on non-production servers</small>';
				echo '</p>';

			break;

		endswitch;

	else :

		//	On production so auto advance
		echo '<script type="text/javascript">';
		echo 'var t = setTimeout( "document.forms[0].submit();",2000 );';
		echo '</script>';

	endif;


?>
</body>
</html>