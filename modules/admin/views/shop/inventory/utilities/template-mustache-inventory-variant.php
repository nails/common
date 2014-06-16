<?php

	//	Counter string
	//	If the $variation var is passed then we're loading this in PHP and we want
	//	to prefill the fields. The form_helper functions don't pick up the fields
	//	automatically because of the Mustache ' . $_counter . ' variable.

	//	Additionally, make sure it's not === FALSE as the value can persist when
	//	this view is loaded multiple times.

	$_counter = isset( $variation ) && $counter !== FALSE ? $counter : '{{counter}}';


	//	Pass the vraiation ID along for the ride too
	if ( ! empty( $variation->id ) ) :

		echo form_hidden( 'variation[' . $_counter . '][id]', $variation->id );

	elseif ( ! empty( $variation['id'] ) ) :

		echo form_hidden( 'variation[' . $_counter . '][id]', $variation['id'] );

	endif;

?>
<div id="variation-<?=$_counter?>" class="variation" data-counter="<?=$_counter?>">
	<div class="not-applicable">
		<p>
			<strong>The specified product type has a limited number of variations it can support.</strong>
			This variation will be deleted when you submit this form.
		</p>
	</div>
	<ul class="tabs" data-tabgroup="variation-<?=$_counter?>">
		<li class="tab active">
			<a href="#" class="tabber-variation-details" data-tab="tab-variation-<?=$_counter?>-details">Details</a>
		</li>
		<li class="tab">
			<a href="#" data-tab="tab-variation-<?=$_counter?>-meta">Meta</a>
		</li>
		<li class="tab">
			<a href="#" data-tab="tab-variation-<?=$_counter?>-pricing">Pricing</a>
		</li>
		<li class="tab">
			<a href="#" data-tab="tab-variation-<?=$_counter?>-gallery">Gallery</a>
		</li>
		<li class="tab">
			<a href="#" class="tabber-variation-shipping" data-tab="tab-variation-<?=$_counter?>-shipping">Shipping</a>
		</li>
		{{^is_first}}
		<li class="action">
			<a href="#" class="delete">Delete</a>
		</li>
		{{/is_first}}
	</ul>
	<section class="tabs pages variation-<?=$_counter?>">
		<div class="tab page active fieldset" id="tab-variation-<?=$_counter?>-details">
			<?php

				$_field					= array();
				$_field['key']			= 'variation[' . $_counter . '][label]';
				$_field['label']		= 'Label';
				$_field['required']		= TRUE;
				$_field['placeholder']	= 'Give this variation a title';
				$_field['default']		= ! empty( $variation->label ) ? $variation->label : '';

				echo form_field( $_field );

				// --------------------------------------------------------------------------

				$_field					= array();
				$_field['key']			= 'variation[' . $_counter . '][sku]';
				$_field['label']		= 'SKU';
				$_field['placeholder']	= 'This variation\'s Stock Keeping Unit; a unique offline identifier (e.g for POS or warehouses)';
				$_field['default']		= ! empty( $variation->sku ) ? $variation->sku : '';

				echo form_field( $_field );

				// --------------------------------------------------------------------------

				$_field					= array();
				$_field['key']			= 'variation[' . $_counter . '][stock_status]';
				$_field['label']		= 'Stock Status';
				$_field['class']		= 'select2 stock-status';
				$_field['required']		= TRUE;
				$_field['default']		= ! empty( $variation->stock_status ) ? $variation->stock_status : 'IN_STOCK';

				$_options					= array();
				$_options['IN_STOCK']		= 'In Stock';
				$_options['TO_ORDER']		= 'To Order';
				$_options['OUT_OF_STOCK']	= 'Out of Stock';

				echo form_field_dropdown( $_field, $_options );

				// --------------------------------------------------------------------------

				$_status	= set_value( 'variation[' . $_counter . '][stock_status]', $_field['default'] );
				$_display	= $_status == 'IN_STOCK' ? 'block' : 'none';

				echo '<div class="stock-status-field IN_STOCK" style="display:' . $_display . '">';

					$_field					= array();
					$_field['key']			= 'variation[' . $_counter . '][quantity_available]';
					$_field['label']		= 'Quantity Available';
					$_field['required']		= TRUE;
					$_field['placeholder']	= 'How many units of this variation are available? Leave blank for unlimited';
					$_field['default']		= ! empty( $variation->quantity_available ) ? $variation->quantity_available : '';

					echo form_field( $_field );

				echo '</div>';

				// --------------------------------------------------------------------------

				$_display = $_status == 'TO_ORDER' ? 'block' : 'none';
				echo '<div class="stock-status-field TO_ORDER" style="display:' . $_display . '">';

					$_field					= array();
					$_field['key']			= 'variation[' . $_counter . '][lead_time]';
					$_field['label']		= 'Lead Time (days)';
					$_field['required']		= TRUE;
					$_field['placeholder']	= 'How long is the lead time on orders for this product?';
					$_field['default']		= ! empty( $variation->lead_time ) ? $variation->lead_time : '';

					echo form_field( $_field );

				echo '</div>';

			?>
		</div>

		<div class="tab page fieldset" id="tab-variation-<?=$_counter?>-meta">
			<?php

				foreach ( $product_types_meta AS $id => $fields ) :

					echo '<div class="meta-fields meta-fields-' . $id . '" style="display:none;">';

					if ( $fields ) :

						//	TODO: use the form builder library
						foreach ( $fields AS $field ) :

							$_field					= array();
							$_field['key']			= 'variation[' . $_counter . '][meta][' . $field->key . ']';
							$_field['label']		= ! empty( $field->label )			? $field->label : '';
							$_field['placeholder']	= ! empty( $field->placeholder )	? $field->placeholder : '';
							$_field['required']		= array_search( 'required', explode( '|', $field->validation ) ) ? TRUE : FALSE;
							$_field['default']		= ! empty( $variation->meta->{$field->key} ) ? $variation->meta->{$field->key} : '';

							switch( $field->type ) :

								case 'cdn_object' :

									$_field['bucket'] = $field->bucket;

									$_field_out = form_field_mm( $_field, $field->tip );

								break;

								// --------------------------------------------------------------------------

								case 'text' :
								default :

									$_field_out = form_field( $_field, $field->tip );

								break;

							endswitch;

							echo $_field_out;

						endforeach;

					else :

						echo '<p>There are no extra fields for this product type.</p>';

					endif;

					echo '</div>';

				endforeach;

			?>
		</div>

		<div class="tab page" id="tab-variation-<?=$_counter?>-pricing">
			<?php if ( count( $currencies ) > 1 ) : ?>
			<p>
				Define the price points for this variation. If you'd like to set a specific price for a certain
				currency then define that also otherwise the system will calculate automatically using current
				exchange rates.
			</p>
			<?php endif; ?>
			<table class="pricing-options">
				<thead>
					<tr>
						<th>Currency</th>
						<th>Price</th>
						<th>Sale Price</th>
					</tr>
				</thead>
				<tbody>

					<!--	BASE CURRENCY	-->
					<?php

						if ( $is_first ) :

							$_attr_price		= 'data-code="' . SHOP_BASE_CURRENCY_CODE . '"';
							$_attr_price_sale	= 'data-code="' . SHOP_BASE_CURRENCY_CODE . '"';

							$_class_price		= array( 'base-price' );
							$_class_price_sale	= array( 'base-price-sale' );

						else :

							$_attr_price		= '';
							$_attr_price_sale	= '';

							$_class_price		= array( 'variation-price', SHOP_BASE_CURRENCY_CODE );
							$_class_price_sale	= array( 'variation-price-sale', SHOP_BASE_CURRENCY_CODE );

						endif;

						// --------------------------------------------------------------------------

						//	Prep the prices into an easy to access array
						$_price			= array();
						$_sale_price	= array();

						if ( ! empty( $variation->price ) ) :

							foreach( $variation->price AS $price ) :

								$_price[$price->id]			= $price->price;
								$_sale_price[$price->id]	= $price->sale_price;

							endforeach;

						endif;

					?>
					<tr>
						<td class="currency">
							<?php

								echo SHOP_BASE_CURRENCY_CODE;

								$_key = 'variation[' . $_counter . '][pricing][0][currency_id]';
								echo form_hidden( $_key, SHOP_BASE_CURRENCY_ID );

							?>
						</td>
						<td class="price">
							<?php

								$_key		= 'variation[' . $_counter . '][pricing][0][price]';
								$_error		= form_error( $_key, '<span class="error show-in-tabs">', '</span>' );
								$_class		= $_class_price;
								$_default	= ! empty( $_price[SHOP_BASE_CURRENCY_ID] ) ? $_price[SHOP_BASE_CURRENCY_ID] : '';

								if ( $_error ) :

									$_class[] = 'error';

								endif;

								$_class = $_class ? ' class="' . implode( ' ', $_class ) . '"' : '';

								echo form_input( $_key, set_value( $_key, $_default ), 'data-prefix="' . SHOP_BASE_CURRENCY_SYMBOL . '" ' . $_attr_price . $_class . ' placeholder="Price"' );
								echo $_error;

							?>
						</td>
						<td class="price-sale">
							<?php

								$_key		= 'variation[' . $_counter . '][pricing][0][sale_price]';
								$_error		= form_error( $_key, '<span class="error show-in-tabs">', '</span>' );
								$_class		= $_class_price_sale;
								$_default	= ! empty( $_sale_price[SHOP_BASE_CURRENCY_ID] ) ? $_sale_price[SHOP_BASE_CURRENCY_ID] : '';

								if ( $_error ) :

									$_class[] = 'error';

								endif;

								$_class = $_class ? ' class="' . implode( ' ', $_class ) . '"' : '';

								echo form_input( $_key, set_value( $_key, $_default ), 'data-prefix="' . SHOP_BASE_CURRENCY_SYMBOL . '" ' . $_attr_price_sale . $_class . ' placeholder="Sale Price"' );
								echo $_error;

							?>
						</td>
					</tr>

					<!--	OTHER CURRENCIES	-->
					<?php

						$_counter_inside = 1;
						foreach ( $currencies AS $currency ) :

							if ( $currency->id != SHOP_BASE_CURRENCY_ID ) :

								if ( $is_first ) :

									$_attr_price		= 'data-code="' . $currency->code . '"';
									$_attr_price_sale	= 'data-code="' . $currency->code . '"';

									$_class_price		= array( 'base-price' );
									$_class_price_sale	= array( 'base-price-sale' );

								else :

									$_attr_price		= '';
									$_attr_price_sale	= '';

									$_class_price		= array( 'variation-price', $currency->code );
									$_class_price_sale	= array( 'variation-price-sale', $currency->code );

								endif;

								?>
								<tr>
									<td class="currency">
										<?php

											echo $currency->code;

											$_key = 'variation[' . $_counter . '][pricing][' . $_counter_inside . '][currency_id]';
											echo form_hidden( $_key, $currency->id );

										?>
									</td>
									<td class="price">
										<?php

											$_key		= 'variation[' . $_counter . '][pricing][' . $_counter_inside . '][price]';
											$_error		= form_error( $_key, '<span class="error show-in-tabs">', '</span>' );
											$_class		= $_class_price;
											$_default	= ! empty( $_price[$currency->id] ) ? $_price[$currency->id] : '';

											if ( $_error ) :

												$_class[] = 'error';

											endif;

											$_class = $_class ? ' class="' . implode( ' ', $_class ) . '"' : '';

											echo form_input( $_key, set_value( $_key, $_default ), 'data-prefix="' . $currency->symbol . '" ' . $_attr_price . $_class . ' placeholder="Calculate automatically from ' . SHOP_BASE_CURRENCY_CODE . '"' );
											echo $_error;

										?>
									</td>
									<td class="price-sale">
										<?php

											$_key		= 'variation[' . $_counter . '][pricing][' . $_counter_inside . '][sale_price]';
											$_error		= form_error( $_key, '<span class="error show-in-tabs">', '</span>' );
											$_class		= $_class_price_sale;
											$_default	= ! empty( $_sale_price[$currency->id] ) ? $_sale_price[$currency->id] : '';

											if ( $_error ) :

												$_class[] = 'error';

											endif;

											$_class = $_class ? ' class="' . implode( ' ', $_class ) . '"' : '';
											echo form_input( $_key, set_value( $_key, $_default ), 'data-prefix="' . $currency->symbol . '" ' . $_attr_price_sale . $_class . ' placeholder="Calculate automatically from ' . SHOP_BASE_CURRENCY_CODE . '"' );
											echo $_error;

										?>
									</td>
								</tr>
								<?php

								$_counter_inside++;

							endif;

						endforeach;

					?>

				</tbody>
			</table>
			<?php

				if ( $is_first ) :

					$_display = empty( $num_variants ) || $num_variants == 1 ? 'none' : 'block';
					echo '<p id="variation-sync-prices" style="display:' . $_display . '">';
					echo '<a href="#" class="awesome small orange">Sync Prices</a>';
					echo '</p>';

				endif;

			?>
		</div>

		<div class="tab page" id="tab-variation-<?=$_counter?>-gallery">
			<p>
				Specify which, if any, of the uploaded gallery images feature this product variation.
			</p>
			<?php

				//	Render, if there's POST then make sure we render it enough times
				//	Otherwise check to see if there's $item data

				if ( $this->input->post( 'gallery' ) ) :

					$_gallery	= $this->input->post( 'gallery' );
					$_selected	= isset( $_POST['variation'][$_counter]['gallery'] ) ? $_POST['variation'][$_counter]['gallery'] : array();

				elseif( ! empty( $item->gallery ) ) :

					$_gallery	= $item->gallery;
					$_selected	= ! empty( $variation->gallery ) ? $variation->gallery : array();

				else :

					$_gallery	= array();
					$_selected	= array();

				endif;

			?>
			<ul class="gallery-associations <?=! empty( $_gallery ) ? '' : 'empty' ?>">
				<li class="empty">No images have been uploaded; upload some using the <a href="#">Gallery tab</a></li>
				<?php

					if ( ! empty( $_gallery ) ) :

						foreach( $_gallery AS $image ) :

							//	Is this item selected for this variation?
							$_checked = array_search( $image, $_selected ) !== FALSE ? 'selected' : FALSE;

							echo '<li class="image object-id-' . $image . ' ' . $_checked . '">';
								echo form_checkbox( 'variation[' . $_counter . '][gallery][]', $image, (bool) $_checked );
								echo img( cdn_thumb( $image, 34, 34 ) );
							echo '</li>';

						endforeach;

					endif;

				?>
				<li class="actions">
					<a href="#" data-function="all" class="action awesome small orange">Select All</a>
					<a href="#" data-function="none" class="action awesome small orange">Select None</a>
					<a href="#" data-function="toggle" class="action awesome small orange">Toggle</a>
				</li>
			</ul>
		</div>

		<div class="tab page fieldset" id="tab-variation-<?=$_counter?>-shipping">
			<p>
				Define the following information for shipping. The system will use this information to
				calculate a shipping quote for the user, where appropriate.
			</p>
			<p class="system-alert message">
				<strong>Please note:</strong> specify dimensions of the item as it'll be shipped, not assembled.
			</p>
			<?php

				$_field					= array();
				$_field['key']			= 'variation[' . $_counter . '][shipping][measurement_unit]';
				$_field['label']		= 'L/W/H Unit of measurement';
				$_field['class']		= 'select2 measurement-unit';
				$_field['required']		= TRUE;
				$_field['default']		= ! empty( $variation->shipping->measurement_unit ) ? $variation->shipping->measurement_unit : 'MM';

				$_options				= array();
				$_options['MM']			= 'Millimeter';
				$_options['CM']			= 'Centimetre';
				$_options['M']			= 'Metre';

				echo form_field_dropdown( $_field, $_options );

				// --------------------------------------------------------------------------

				$_field					= array();
				$_field['key']			= 'variation[' . $_counter . '][shipping][length]';
				$_field['label']		= 'Boxed Length';
				$_field['placeholder']	= 'The length of the item';
				$_field['class']		= 'length';
				$_field['required']		= TRUE;
				$_field['default']		= isset( $variation->shipping->length ) ? $variation->shipping->length : '';

				echo form_field( $_field );

				// --------------------------------------------------------------------------

				$_field					= array();
				$_field['key']			= 'variation[' . $_counter . '][shipping][width]';
				$_field['label']		= 'Boxed Width';
				$_field['placeholder']	= 'The width of the item';
				$_field['class']		= 'width';
				$_field['required']		= TRUE;
				$_field['default']		= isset( $variation->shipping->width ) ? $variation->shipping->width : '';

				echo form_field( $_field );

				// --------------------------------------------------------------------------

				$_field					= array();
				$_field['key']			= 'variation[' . $_counter . '][shipping][height]';
				$_field['label']		= 'Boxed Height';
				$_field['placeholder']	= 'The height of the item';
				$_field['class']		= 'height';
				$_field['required']		= TRUE;
				$_field['default']		= isset( $variation->shipping->height ) ? $variation->shipping->height : '';

				echo form_field( $_field );

				// --------------------------------------------------------------------------

				$_field					= array();
				$_field['key']			= 'variation[' . $_counter . '][shipping][weight_unit]';
				$_field['label']		= 'Weight unit of measurement';
				$_field['class']		= 'select2 weight-unit';
				$_field['required']		= TRUE;
				$_field['default']		= ! empty( $variation->shipping->weight_unit ) ? $variation->shipping->weight_unit : 'G';

				$_options				= array();
				$_options['G']			= 'Gram';
				$_options['KG']			= 'Kilogram';

				echo form_field_dropdown( $_field, $_options );

				// --------------------------------------------------------------------------

				$_field					= array();
				$_field['key']			= 'variation[' . $_counter . '][shipping][weight]';
				$_field['label']		= 'Boxed Weight';
				$_field['placeholder']	= 'The weight of the item';
				$_field['class']		= 'weight';
				$_field['required']		= TRUE;
				$_field['default']		= isset( $variation->shipping->weight ) ? $variation->shipping->weight : '';

				echo form_field( $_field, 'cock and balls' );

				// --------------------------------------------------------------------------

				$_field					= array();
				$_field['key']			= 'variation[' . $_counter . '][shipping][collection_only]';
				$_field['label']		= 'Collection Only';
				$_field['readonly']		= ! app_setting( 'warehouse_collection_enabled', 'shop' );
				$_field['info']			= ! app_setting( 'warehouse_collection_enabled', 'shop' ) ? '<strong>Warehouse Collection is disabled</strong>' : '';
				$_field['info']			.= ! app_setting( 'warehouse_collection_enabled', 'shop' ) && user_has_permission( 'admin[settings]' ) ? '<br />If you wish to allow customers to collect from your warehouse you must enable it in ' . anchor( 'admin/settings/shop', 'settings', 'class="confirm" data-title="Stop Editing?" data-body="Any unsaved changes will be lost."' ) . '.' : '';
				$_field['class']		= 'collection-only';
				$_field['default']		= isset( $variation->shipping->collection_only ) ? (bool) $variation->shipping->collection_only : FALSE;
				$_tip					= 'Items marked as collection only will be handled differently in checkout and reporting. They also dont contribute to the overall dimensions and weight of the order when calculating shipping costs.';

				echo form_field_boolean( $_field, $_tip );

				// --------------------------------------------------------------------------

				if ( $is_first ) :

					$_display = empty( $num_variants ) || $num_variants == 1 ? 'none' : 'block';
					echo '<p id="variation-sync-shipping" style="margin-top:1em;display:' . $_display . '">';
					echo '<a href="#" class="awesome small orange">Sync Shipping Details</a>';
					echo '</p>';

				endif;

			?>
		</div>
	</section>
</div>