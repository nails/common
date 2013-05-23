Thanks for your order at <?=APP_NAME?>, here's your email receipt for your records.

Order reference <?=$order->ref?>, placed on the <?=date( 'jS F Y, \a\t H:i:s', strtotime( $order->created ) )?>.

---------------

<?php

	$this->load->view( 'email/shop/utilities/order_table_plaintext', array( 'type' => 'receipt' ) );

	if ( shop_setting( 'invoice_company' ) || shop_setting( 'invoice_vat_no' ) || shop_setting( 'vat_address' ) || shop_setting( 'invoice_company_no' ) ) :

		echo '---------------' . "\n\n";

		if ( shop_setting( 'invoice_company' ) ) :

			echo 'Company: ' . shop_setting( 'invoice_company' ) . "\n";

		endif;

		if ( shop_setting( 'invoice_vat_no' ) ) :

			echo 'VAT: ' . shop_setting( 'invoice_vat_no' ) . "\n";

		endif;

		if ( shop_setting( 'invoice_address' ) ) :

			echo 'Address: ' . shop_setting( 'invoice_address' ) . "\n";

		endif;

		if ( shop_setting( 'invoice_company_no' ) ) :

			echo 'Company No.: ' . shop_setting( 'invoice_company_no' ) . "\n";

		endif;

	endif;

?>