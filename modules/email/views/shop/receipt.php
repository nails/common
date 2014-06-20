<p>
	Thanks for your order at <?=APP_NAME?>, here's your email receipt for your records.
</p>
<p>
	Order reference <strong><?=$order->ref?></strong>, placed on the <?=date( 'jS F Y, \a\t H:i:s', strtotime( $order->created ) )?>.
</p>
<?php

	$this->load->view( 'email/shop/utilities/order_table', array( 'type' => 'receipt' ) );

	if ( app_setting( 'invoice_company', 'shop' ) || app_setting( 'invoice_vat_no', 'shop' ) || app_setting( 'invoice_address', 'shop' ) || app_setting( 'invoice_company_no', 'shop' ) ) :

		echo '<hr />';
		echo '<p><small>';

		if ( app_setting( 'invoice_company', 'shop' ) ) :

			echo '<div>Company: ' . app_setting( 'invoice_company', 'shop' ) . '</div>';

		endif;

		if ( app_setting( 'invoice_vat_no', 'shop' ) ) :

			echo '<div>VAT: ' . app_setting( 'invoice_vat_no', 'shop' ) . '</div>';

		endif;

		if ( app_setting( 'invoice_address', 'shop' ) ) :

			echo '<div>Address: ' . app_setting( 'invoice_address', 'shop' ) . '</div>';

		endif;

		if ( app_setting( 'invoice_company_no', 'shop' ) ) :

			echo '<div>Company No.: ' . app_setting( 'invoice_company_no', 'shop' ) . '</div>';

		endif;

		echo '</small></p>';

	endif;

?>