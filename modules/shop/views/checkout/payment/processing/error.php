<?php

	//	Manager? Or managers?
	$_managers	= explode( ',', shop_setting( 'notify_order' ) );
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
<div class="container shop processing error">
	<p>
		An error occurred during order processing which we couldn't recover from. This may be due to an error during payment.
	</p>
	<p>
		Please contact the store <?=$_manager?> at <?=$_managers?>; quoting order reference <strong><?=$order->ref?></strong> and we'll assist as best we can.
	</p>
	<p>
		Unfortunately we can't confirm wether or not your card has been charged at this moment.
	</p>
</div>