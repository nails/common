<div class="group-shop vouchers browse">
	<p>
		Browse all vouchers (including gift cards) which are associated with the shop.
		<?php

			if ( $user->has_permission( 'admin.shop.vouchers_create' ) ) :

				echo anchor( 'admin/shop/vouchers/create', 'Create Voucher', 'class="awesome small green right"' );

			endif;

		?>
	</p>

	<?php
	
		$this->load->view( 'admin/shop/vouchers/utilities/search' );
		$this->load->view( 'admin/shop/vouchers/utilities/pagination' );
	
	?>

	<table>
		<thead>
			<tr>
				<th class="code">Code</th>
				<th class="type">Details</th>
				<th class="user">Created By</th>
				<th class="value">Discount</th>
				<th class="valid_from">Valid From</th>
				<th class="expires">Expires</th>
				<th class="uses">Uses</th>
				<th class="actions">Actions</th>
			</tr>
		</thead>
		<tbody>
			<?php
			
				if ( $vouchers->data ) :
				
					foreach ( $vouchers->data AS $voucher ) :
					
						?>
						<tr id="order-<?=number_format( $voucher->id )?>">
							<td class="code"><?=$voucher->code?></td>
							<td class="type">
							<?php

								echo $voucher->label;

								switch( $voucher->type ) :

									case 'NORMAL' :

										echo '<small>Type: Normal</small>';

									break;

									// --------------------------------------------------------------------------

									case 'LIMITED_USE' :

										echo '<small>Type: Limited Use</small>';
										echo '<small>Limited to ' . $voucher->limited_use_limit . ' uses; used ' . $voucher->use_count . ' times</small>';


									break;

									// --------------------------------------------------------------------------

									case 'GIFT_CARD' :

										echo '<small>Type: Gift card</small>';
										echo '<small>Remaining Balance:' . SHOP_BASE_CURRENCY_SYMBOL . number_format( $voucher->gift_card_balance, SHOP_BASE_CURRENCY_PRECISION ). '</small>';

									break;

								endswitch;


								// --------------------------------------------------------------------------


								echo '<small>Applies to: ';
								switch( $voucher->discount_application ) :

									case 'PRODUCTS' :

										echo 'Purchases only';

									break;

									case 'SHIPPING' :

										echo 'Shipping only';

									break;

									case 'PRODUCT_TYPES' :

										echo 'Certain product types only &rsaquo; ' . $voucher->product->label;

									break;

									case 'ALL' :

										echo 'Both Products and Shipping';

									break;

								endswitch;
								echo '</small>';
							?>
							</td>
							<?php

								$this->load->view( 'admin/_utilities/table-cell-user',		$voucher->creator );

							?>
							<td class="value">
								<?php

								switch( $voucher->discount_type ) :

									case 'AMOUNT' :

										echo SHOP_BASE_CURRENCY_SYMBOL . number_format( $voucher->discount_value, SHOP_BASE_CURRENCY_PRECISION );

									break;

									// --------------------------------------------------------------------------

									case 'PERCENTAGE' :

										echo $voucher->discount_value . '%';

									break;

								endswitch;

								?>
							</td>
							<td class="valid_from">
								<?php

									$_format_d = active_user( 'date_setting' )->format->date->format;
									$_format_t = active_user( 'date_setting' )->format->time->format;

									echo date( $_format_d . ' ' . $_format_t, strtotime( $voucher->valid_from ) );

								?>
							</td>
							<td class="expires">
								<?php

									if ( $voucher->valid_to ) :

										$_format_d = active_user( 'date_setting' )->format->date->format;
										$_format_t = active_user( 'date_setting' )->format->time->format;

										echo date( $_format_d . ' ' . $_format_t, strtotime( $voucher->valid_from ) );

									else :

										echo '<span class="blank">Does not expire</span>';

									endif;

								?>
							</td>
							<td class="uses"><?=number_format( $voucher->use_count )?></td>
							<td class="actions">
								<?php
								
									$_buttons = array();

									// --------------------------------------------------------------------------

									if ( $voucher->is_active ) :

										if ( $user->has_permission( 'admin.shop.vouchers_deactivate' ) ) :

											$_buttons[] = anchor( 'admin/shop/vouchers/deactivate/' . $voucher->id, 'Suspend', 'class="awesome small red confirm"' );

										endif;

									else :

										if ( $user->has_permission( 'admin.shop.vouchers_activate' ) ) :

											$_buttons[] = anchor( 'admin/shop/vouchers/activate/' . $voucher->id, 'Activate', 'class="awesome small green"' );

										endif;

									endif;

									// --------------------------------------------------------------------------

									if ( $_buttons ) :

										foreach ( $_buttons AS $button ) :

											echo $button;

										endforeach;

									else :

										echo '<span class="blank">There are no actions you can do on this item.</span>';

									endif;

								?>
							</td>
						</tr>
						<?php
						
					endforeach;
					
				else :
					?>
					<tr>
						<td colspan="7" class="no-data">
							<p>No Vouchers found</p>
						</td>
					</tr>
					<?php
				endif;
			
			?>
		</tbody>
	</table>
	<?php
	
		$this->load->view( 'admin/shop/vouchers/utilities/pagination' );
	
	?>
</div>