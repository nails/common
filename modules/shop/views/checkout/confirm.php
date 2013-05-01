<div class="container shop checkout basket confirm">

	<!--	BASKET	-->
	<?php $this->load->view( 'shop/basket/basket-table', array( 'no_changes' => TRUE ) );?>
	
	<!--	SHIPPING	-->
	<?php if ( $basket->requires_shipping ) : ?>
	
		<h2>Shipping Options</h2>
		<p class="system-alert message no-close">
			<strong>TODO: </strong> Render shipping details.
		</p>
	
	<?php endif; ?>
	
</div>