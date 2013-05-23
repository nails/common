<p>
	Thanks for your order at <?=APP_NAME?>, here's your email receipt for your records.
</p>
<p>
	Order reference <strong><?=$order->ref?></strong>, placed on the <?=date( 'jS F Y, \a\t H:i:s', strtotime( $order->created ) )?>.
</p>
<?php

	$this->load->view( 'email/shop/utilities/order_table', array( 'type' => 'receipt' ) );

	if ( shop_setting( 'invoice_company' ) || shop_setting( 'invoice_vat_no' ) || shop_setting( 'invoice_address' ) || shop_setting( 'invoice_company_no' ) ) :

		echo '<hr />';
		echo '<p><small>';

		if ( shop_setting( 'invoice_company' ) ) :

			echo '<div>Company: ' . shop_setting( 'invoice_company' ) . '</div>';

		endif;

		if ( shop_setting( 'invoice_vat_no' ) ) :

			echo '<div>VAT: ' . shop_setting( 'invoice_vat_no' ) . '</div>';

		endif;

		if ( shop_setting( 'invoice_address' ) ) :

			echo '<div>Address: ' . shop_setting( 'invoice_address' ) . '</div>';

		endif;

		if ( shop_setting( 'invoice_company_no' ) ) :

			echo '<div>Company No.: ' . shop_setting( 'invoice_company_no' ) . '</div>';

		endif;

		echo '</small></p>';

	endif;

?>