<div class="group-shop vouchers browse">
	<p>
		Browse all vouchers (including gift cards) which are associated with the shop.
		<?=anchor( 'admin/shop/vouchers/create', 'Create Voucher', 'class="awesome small green right"' )?>
	</p>

	<?php
	
		$this->load->view( 'admin/shop/vouchers/utilities/search' );
		$this->load->view( 'admin/shop/vouchers/utilities/pagination' );
	
	?>

	<table>
		<thead>
			<tr>
				<th class="code">Code</th>
				<th class="type">Type</th>
				<th class="value">Discount</th>
				<th class="valid_from">Valid From</th>
				<th class="expires">Expires</th>
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

								switch( $voucher->type ) :

									case 'NORMAL' :

										echo 'Normal';

									break;

									// --------------------------------------------------------------------------

									case 'LIMITED_USE' :

										echo 'Limited Use';
										echo '<small>Limited to ' . $voucher->limited_use_limit . ' uses; used ' . $voucher->use_count . ' times</small>';


									break;

									// --------------------------------------------------------------------------

									case 'GIFT_CARD' :

										echo 'Gift card';
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

										echo 'Certain product types only &ndash; PRODUCT TYPE';

									break;

									case 'ALL' :

										echo 'Both Products and Shipping';

									break;

								endswitch;
								echo '</small>';
							?>
							</td>
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
							<td class="actions">
								<?=anchor( 'admin/shop/vouchers/view/' . $voucher->id, lang( 'action_edit' ), 'class="awesome small fancybox" data-fancybox-type="iframe"' )?>
								<?=anchor( 'admin/shop/vouchers/delete/' . $voucher->id, lang( 'action_delete' ), 'class="awesome small red confirm" data-confirm="Are you sure?"' )?>
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