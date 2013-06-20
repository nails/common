An order has been placed at <?=APP_NAME?>.

Order reference <strong><?=$order->ref?></strong>, placed on the <?=date( 'jS F Y, \a\t H:i:s', strtotime( $order->created ) )?> by <?=$order->user->email?>.

---------------

<?php

	$this->load->view( 'email/shop/utilities/order_table_plaintext', array( 'type' => 'notification' ) );

?>