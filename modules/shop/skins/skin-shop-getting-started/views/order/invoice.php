<!DOCTYPE html>
<html>
	<head>
		<title><?=APP_NAME?> Invoice #<?=$order->ref?></title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<style type="text/css">

			#container
			{
				width:700px;
				padding:25px;
				margin:25px auto;
				border:1px solid #CCC;
				background:#EFEFEF;
				font-family: "HelveticaNeue-Light", "Helvetica Neue Light", "Helvetica Neue", Helvetica, Arial, "Lucida Grande", sans-serif;
				font-weight: 300;
				position:relative;
			}


			#title
			{
				text-align:center;
			}

			hr
			{
				margin:20px 0;
				clear:both;
				border-top:1px dotted #CCC;
			}

			table
			{
				width:100%;
				border:1px solid #CCC;
				border-collapse: collapse;
				margin-bottom:20px;
			}

			th,td
			{
				padding:10px;
				text-align: left;
				border-right:1px dotted #CCC;
				border-bottom:1px dotted #CCC;
				vertical-align:middle;
			}

			td
			{
				background:#FEFEFE;
			}

			td.th
			{
				width:150px;
			}

			td small
			{
				display:block;
				color:#777;
				margin-top:0.5em;
			}

			td.status
			{
				font-weight:bold;
				color:red;
			}

			td.status.paid
			{
				color:green;
			}

			th.quantity,
			th.unit,
			th.vat,
			th.shipping,
			th.total,
			td.quantity,
			td.unit,
			td.vat,
			td.shipping,
			td.total
			{
				text-align:center;
			}

			td.sub
			{
				border-top:1px solid #CCC;
			}

			td.sub,
			td.tax,
			td.discount,
			td.grand
			{
				font-weight:bold;
				text-align:center;
			}

			td.sub.label,
			td.tax.label,
			td.discount.label,
			td.grand.label
			{
				text-align:right;
			}

			td.sub.spacer,
			td.tax.spacer,
			td.discount.spacer,
			td.grand.spacer
			{
				border-right:0px;
			}

			p small
			{
				font-size:0.75em;
			}

		</style>
	</head>
	<body>
	<div id="container">

		<h1 id="title"><?=APP_NAME?> Invoice</h1>
		<table>
			<tbody>
				<tr>
					<td class="th">Recipient</td>
					<td>
						<strong><?=$order->shipping_details->addressee?></strong>
						<?php

							if ( $order->shipping_details->addressee ) :

								echo '<strong>' . $order->shipping_details->addressee . '</strong>';

							elseif ( $order->user->first_name ) :

								echo '<strong>' . $order->user->first_name . ' ' . $order->user->last_name . '</strong>';
								echo '<br />' . $order->user->email;

							else :

								echo '<strong>' . $order->user->email . '</strong>';

							endif;

							// --------------------------------------------------------------------------

							echo $order->shipping_details->line_1	? '<br />' . $order->shipping_details->line_1 : '';
							echo $order->shipping_details->line_2	? '<br />' . $order->shipping_details->line_2 : '';
							echo $order->shipping_details->town		? '<br />' . $order->shipping_details->town : '';
							echo $order->shipping_details->postcode	? '<br />' . $order->shipping_details->postcode : '';

						?>
					</td>
				</tr>
				<tr>
					<td class="th">Sender</td>
					<td>
						<strong>
						<?php

							if ( app_setting( 'invoice_company', 'shop' ) ) :

								echo app_setting( 'invoice_company', 'shop' );

							elseif ( APP_NAME ) :

								echo APP_NAME;

							else :

								echo site_url();

							endif;

						?>
						</strong>
						<?=app_setting( 'invoice_address', 'shop' ) ? '<br />' . nl2br( app_setting( 'invoice_address', 'shop' ) ) : ''?>
						<?=app_setting( 'invoice_vat_no', 'shop' ) ? '<small>VAT No.: ' . nl2br( app_setting( 'invoice_vat_no', 'shop' ) ) . '</small>' : ''?>
						<?=app_setting( 'invoice_company_no', 'shop' ) ? '<small>Company No.: ' . nl2br( app_setting( 'invoice_company_no', 'shop' ) ) . '</small>' : ''?>

					</td>
				</tr>
			</tbody>
		</table>
		<table>
			<tbody>
				<tr>
					<td class="th">Invoice</td>
					<td>#<?=$order->ref?></td>
				</tr>
				<tr>
					<td class="th">Dated</td>
					<td><?=user_datetime( $order->created )?></td>
				</tr>
				<tr>
					<td class="th">Due</td>
					<td>On Receipt</td>
				</tr>
				<tr>
					<td class="th">Status</td>
					<?='<td class="status ' . strtolower(  $order->status ) . '">' .  $order->status . '</td>'?>
				</tr>
			</tbody>
		</table>

		<table>
			<thead>
				<tr>
					<th class="quantity">Quantity</th>
					<th class="item">Details</th>
					<th class="unit">Unit Cost</th>
					<th class="vat">Tax Rate</th>
					<?php if ( $order->requires_shipping ) : ?>
					<th class="shipping">Shipping</th>
					<?php endif; ?>
					<th class="total">Total</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( $order->items AS $item ) :?>
				<tr>
					<td class="quantity"><?=$item->quantity?></td>
					<td class="item">
					<?php

						//	Load the 'details' view; in a separate view so apps can easily customise the layout/content
						//	of this part of the view without having to duplicate the entire invoice view.

						$this->load->view( 'shop/' . $skin->dir . '/orders/invoice-item-cell', array( 'item' => &$item ) );

					?>
					</td>
					<td class="unit"><?=shop_format_price( $item->price_render, TRUE, TRUE )?></td>
					<td class="vat"><?=$item->tax_rate->rate*100?>%</td>

					<?php if ( $order->requires_shipping ) : ?>
					<td class="shipping"><?=shop_format_price( $item->shipping_render, TRUE, TRUE )?></td>
					<?php endif; ?>

					<td class="total"><?=shop_format_price( $item->total_render, TRUE, TRUE )?></td>
				</tr>
				<?php endforeach; ?>

				<tr>
					<td class="sub label" colspan="4">Sub Total</td>
					<?php if ( $order->requires_shipping ) : ?>
					<td class="sub value"><?=shop_format_price( $order->totals->shipping_render, TRUE, TRUE )?></td>
					<?php endif;?>
					<td class="sub value"><?=shop_format_price( $order->totals->sub_render, TRUE, TRUE )?></td>
				</tr>
				<tr>
					<td class="tax label" colspan="4">Tax</td>
					<?php if ( $order->requires_shipping ) : ?>
					<td class="tax value"><?=shop_format_price( $order->totals->tax_shipping_render, TRUE, TRUE )?></td>
					<?php endif;?>
					<td class="tax value"><?=shop_format_price( $order->totals->tax_items_render, TRUE, TRUE )?></td>
				</tr>
				<?php if ( $order->discount->shipping || $order->discount->items ) : ?>
				<tr>
					<td class="tax label" colspan="4">Discount</td>
					<?php

						if ( $order->requires_shipping && $order->discount->shipping  ) :

							echo '<td class="discount value">';
							echo shop_format_price( $order->discount->shipping_render, TRUE, TRUE );
							echo '</td>';

						elseif ( $order->requires_shipping ) :

							echo '<td class="discount value">';
							echo '&mdash;';
							echo '</td>';

						endif;


						if ( $order->discount->items  ) :

							echo '<td class="discount value">';
							echo shop_format_price( $order->discount->items_render, TRUE, TRUE );
							echo '</td>';

						else :

							echo '<td class="discount value">';
							echo '&mdash;';
							echo '</td>';

						endif;
					?>
				</tr>
			<?php endif; ?>
				<tr>
					<td class="grand label" colspan="4">Grand Total</td>
					<?php if ( $order->requires_shipping ) : ?>
					<td class="grand label spacer">&nbsp;</td>
					<?php endif;?>
					<td class="grand value"><?=shop_format_price( $order->totals->grand_render, TRUE, TRUE )?></td>
				</tr>
			</tbody>
		</table>
		<?php

			if ( $order->currency->order->id != $order->currency->base->id ) :

				echo '<p><small>';
				echo '<strong>Please Note:</strong> Currency conversions are estimates, exchange rate used at the time of ordering: ';
				echo '1 ' . $order->currency->base->code  . ' = ' . $order->currency->exchange_rate . ' ' . $order->currency->order->code;
				echo '</small></p>';

			endif;

		?>
	</div>
	</body>
</html>
