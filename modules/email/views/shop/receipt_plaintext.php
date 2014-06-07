Thanks for your order at <?=APP_NAME?>, here's your email receipt for your records.

Order reference <?=$order->ref?>, placed on the <?=date( 'jS F Y, \a\t H:i:s', strtotime( $order->created ) )?>.

---------------

<?php

	$this->load->view( 'email/shop/utilities/order_table_plaintext', array( 'type' => 'receipt' ) );

	if ( app_setting( 'invoice_company', 'shop' ) || app_setting( 'invoice_vat_no', 'shop' ) || app_setting( 'vat_address', 'blog' ) || app_setting( 'invoice_company_no', 'shop' ) ) :

		echo '---------------' . "\n\n";

		if ( app_setting( 'invoice_company', 'shop' ) ) :

			echo 'Company: ' . app_setting( 'invoice_company', 'shop' ) . "\n";

		endif;

		if ( app_setting( 'invoice_vat_no', 'shop' ) ) :

			echo 'VAT: ' . app_setting( 'invoice_vat_no', 'shop' ) . "\n";

		endif;

		if ( app_setting( 'invoice_address', 'shop' ) ) :

			echo 'Address: ' . app_setting( 'invoice_address', 'shop' ) . "\n";

		endif;

		if ( app_setting( 'invoice_company_no', 'shop' ) ) :

			echo 'Company No.: ' . app_setting( 'invoice_company_no', 'shop' ) . "\n";

		endif;

	endif;

?>