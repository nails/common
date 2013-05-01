<div class="container shop processing error">
	<p>
		An error occurred during order processing which we couldn't recover from. This may be due to an error during payment.
	</p>
	<p>
		Please contact us directly at <strong><?=mailto( shop_setting( 'notify_order' ) )?></strong> quoting order reference <strong><?=$order->ref?></strong> and we'll assist as best we can.
	</p>
	<p>
		Unfortunately we can't confirm wether or not your card has been charged at this moment.
	</p>
</div>