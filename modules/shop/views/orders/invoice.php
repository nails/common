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
				vertical-align:top;
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
			
			td.total,
			td.tax
			{
				font-weight:bold;
			}
			
			td.tax.label,
			td.total.label
			{
				text-align:right;
			}
			
			td.tax.spacer,
			td.total.spacer
			{
				border-right:0px;
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
						<strong><?=shop_setting( 'invoice_company' )?></strong>
						<br /><?=nl2br( shop_setting( 'invoice_address' ) )?>
						<br /><?=nl2br( shop_setting( 'invoice_vat_no' ) )?>
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
					<td><?=date ( 'jS M Y, H:i:s', strtotime( $order->created ) )?></td>
				</tr>
				<tr>
					<td class="th">Due</td>
					<td>On Receipt</td>
				</tr>
				<tr>
					<td class="th">Status</td>
					<?php
					
						if ( $order->status == 'VERIFIED' ) :
						
							echo '<td class="status paid">PAID</td>';
						
						elseif ( $order->status == 'CANCELLED' ) :
						
							echo '<td class="status">CANCELLED</td>';
							
						else :
						
							echo '<td class="status">UNPAID</td>';
						
						endif;
					
					?>
				</tr>
			</tbody>
		</table>
		
		<table>
			<thead>
				<tr>
					<th class="quantity">Quantity</th>
					<th class="item">Details</th>
					<th class="unit">Unit Cost</th>
					<th class="vat">Tax</th>
					<th class="shipping">Shipping</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( $order->items AS $item ) :?>
				<tr>
					<td class="quantity"><?=$item->quantity?></td>
					<td class="item">
						<?=$item->title?>
						<small><?=$item->type->label?>; Product ID: <?=$item->product_id?></small>
					</td>
					<td class="unit"><?=$order->currency->order->symbol . number_format( $item->price, $order->currency->order->precision )?></td>
					<td class="vat"><?=$order->currency->order->symbol . number_format( $item->tax, $order->currency->order->precision )?></td>
					<td class="shipping"><?=$order->currency->order->symbol . number_format( $item->shipping, $order->currency->order->precision )?></td>
				</tr>
				<?php endforeach; ?>
				
				<tr>
					<td class="tax label spacer">&nbsp;</td>
					<td class="tax label spacer">&nbsp;</td>
					<td class="tax label spacer">&nbsp;</td>
					<td class="tax label">Sub Total</td>
					<td class="tax value"><?=$order->currency->order->symbol . number_format( $order->totals->sub, $order->currency->order->precision )?></td>
				</tr>
				<tr>
					<td class="tax label spacer">&nbsp;</td>
					<td class="tax label spacer">&nbsp;</td>
					<td class="tax label spacer">&nbsp;</td>
					<td class="tax label">Tax</td>
					<td class="tax value"><?=$order->currency->order->symbol . number_format( $order->totals->tax, $order->currency->order->precision )?></td>
				</tr>
				<tr>
					<td class="tax label spacer">&nbsp;</td>
					<td class="tax label spacer">&nbsp;</td>
					<td class="tax label spacer">&nbsp;</td>
					<td class="total label">Grand Total</td>
					<td class="total value"><?=$order->currency->order->symbol . number_format( $order->totals->grand, $order->currency->order->precision )?></td>
				</tr>
			</tbody>
		</table>
	</div>
	</body>
</html>
