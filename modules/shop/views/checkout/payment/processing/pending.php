<?php

	//	Manager? Or managers?
	$_managers	= explode( ',', app_setting( 'notify_order', 'shop' ) );
	$_count		= count( $_managers );
	$_manager	= $_count > 1 ? 'managers' : 'manager';

	if ( $_count > 1 ) :

		foreach ( $_managers AS &$manager ) :

			$manager = mailto( trim( $manager ) );

		endforeach;

		$_managers = implode( ', ', $_managers );

		$_managers = substr_replace($_managers, ' or ', strrpos( $_managers, ', ' ), 2 );

	else :

		$_managers = mailto( $_managers[0] );

	endif;

?>
<div class="container shop processing paid">
	<p>
		Many thanks for your order, it has been assigned reference <strong><?=$order->ref?></strong>,
		please quote this reference in any correspondence regarding this order.
	</p>

	<p class="system-alert message no-close">
		<strong>Please note:</strong> The payment system has placed your payment under review. We'll finish processing the order when we receive notification of completed payment at which point you'll receive your receipt.
	</p>
	<p>
		If you have any questions please contact the store <?=$_manager?> at <?=$_managers?>.
	</p>
</div>