<div class="container shop processing verified">
	<p>
		Many thanks for your order, it has been assigned reference <strong><?=$order->ref?></strong>,
		please quote this reference in any correspondence regarding this order.
	</p>
	
	<p>
		We have also sent an email receipt to <strong><?=$order->user->email?></strong> for your records.
	</p>
	<p>
		Thanks again, we really appreciate your business!
	</p>
	<p>
		<?=anchor( '/', lang( 'action_continue' ), 'class="awesome"' )?>
	</p>
</div>