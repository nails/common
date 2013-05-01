<html>
<head>
	<title>Please Wait...</title>
	<meta http-equiv="refresh" content="2">
	<meta http-equiv="content-type" content="text/html;charset=utf-8" />
	<link rel="stylesheet" href="<?=NAILS_URL?>css/nails.default.css" type="text/css" media="screen" charset="utf-8"
</head>
<style type="text/css">

	body
	{
		text-align:center;
		padding:30px;
	}
	img
	{
		margin:30px;
		width:20px;
		height: 20px;
	}

</style>
<body>
	<h1>Please wait while we process your payment</h1>
	<p>
		This can take up to 60 seconds but is usually much quicker.
	</p>
	<p>
		<?=img( NAILS_URL . '/img/loader/20px-000000-TRANS.gif' )?>
	</p>
	<?php
	
		if ( ENVIRONMENT != 'production' ) :
		
			//	Not on production so offer a button to test the notification
			switch( $order->payment_gateway->slug ) :
			
				case 'paypal' :
				
					echo '<p>' . anchor( 'shop/checkout/notify/' . $order->payment_gateway->slug . '?testing=true&ref='. $order->ref, 'Testing: Simulate Successful Payment', 'class="awesome small"' ) . '</p>';
				
				break;
			
			endswitch;
		
		endif;
	
	?>
</body>
</html>