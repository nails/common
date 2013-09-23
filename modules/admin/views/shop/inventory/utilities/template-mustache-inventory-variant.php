<div id="variation-{{counter}}" class="variation" data-counter="{{counter}}">
	<div class="not-applicable">
		<p>
			<strong>The specified product type has a limited number of variations it can support.</strong>
			This variation will be deleted when you submit this form.
		</p>]
	</div>
	<ul class="tabs" data-tabgroup="variation-{{counter}}">
		<li class="tab active">
			<a href="#" class="tabber-variation-details" data-tab="tab-varitation-{{counter}}-details">Details</a>
		</li>
		<li class="tab">
			<a href="#" data-tab="tab-varitation-{{counter}}-meta">Meta</a>
		</li>
		<li class="tab">
			<a href="#" data-tab="tab-varitation-{{counter}}-pricing">Pricing</a>
		</li>
		<li class="tab">
			<a href="#" data-tab="tab-varitation-{{counter}}-gallery">Gallery</a>
		</li>
		<li class="tab">
			<a href="#" class="tabber-variation-shipping" data-tab="tab-varitation-{{counter}}-shipping">Shipping</a>
		</li>
		{{^is_first}}
		<li class="action">
			<a href="#" class="delete">Delete</a>
		</li>
		{{/is_first}}
	</ul>
	<section class="tabs pages variation-{{counter}}">
		<div class="tab page fieldset" id="tab-varitation-{{counter}}-details" style="display:block">
			<?php

				$_field					= array();
				$_field['key']			= 'variation[{{counter}}][label]';
				$_field['label']		= 'Label';
				$_field['required']		= TRUE;
				$_field['placeholder']	= 'Give this variation a title';

				echo form_field( $_field );

				// --------------------------------------------------------------------------

				$_field					= array();
				$_field['key']			= 'variation[{{counter}}][sku]';
				$_field['label']		= 'SKU';
				$_field['placeholder']	= 'This variation\'s Stock Keeping Unit; a unique offline identifier (e.g for POS or warehouses)';

				echo form_field( $_field );

				// --------------------------------------------------------------------------

				$_field					= array();
				$_field['key']			= 'variation[{{counter}}][quantity_available]';
				$_field['label']		= 'Quantity Available';
				$_field['required']		= TRUE;
				$_field['placeholder']	= 'How many units of this variation are available? Leave blank for unlimited';

				echo form_field( $_field );

				// --------------------------------------------------------------------------

				$_field					= array();
				$_field['key']			= 'variation[{{counter}}][quantity_sold]';
				$_field['label']		= 'Quantity Sold';
				$_field['required']		= TRUE;
				$_field['placeholder']	= 'How many units have been sold (offline, in store or otherwise)';

				echo form_field( $_field );

			?>
		</div>

		<div class="tab page fieldset" id="tab-varitation-{{counter}}-meta" style="display:none">
			<div class="fields-is-physical">
				<fieldset>
					<legend>Physical Dimensions</legend>
					<div class="physical-fields">
						<?php

							$_field					= array();
							$_field['key']			= 'variation[{{counter}}][meta][length]';
							$_field['label']		= 'Length';
							$_field['placeholder']	= 'The length of the item';

							echo form_field( $_field );

							// --------------------------------------------------------------------------

							$_field					= array();
							$_field['key']			= 'variation[{{counter}}][meta][width]';
							$_field['label']		= 'Width';
							$_field['placeholder']	= 'The width of the item';

							echo form_field( $_field );

							// --------------------------------------------------------------------------

							$_field					= array();
							$_field['key']			= 'variation[{{counter}}][meta][height]';
							$_field['label']		= 'Height';
							$_field['placeholder']	= 'The height of the item';

							echo form_field( $_field );

							// --------------------------------------------------------------------------

							$_field					= array();
							$_field['key']			= 'variation[{{counter}}][meta][measurement_unit]';
							$_field['label']		= 'L/W/H Unit of measurement';
							$_field['class']		= 'chosen';

							$_options				= array();
							$_options['mm']			= 'Millimeter';
							$_options['cm']			= 'Centimetre';
							$_options['m']			= 'Metre';

							echo form_field_dropdown( $_field, $_options );

							// --------------------------------------------------------------------------

							$_field					= array();
							$_field['key']			= 'variation[{{counter}}][meta][weight]';
							$_field['label']		= 'Weight';
							$_field['placeholder']	= 'The weight of the item';

							echo form_field( $_field );

							// --------------------------------------------------------------------------

							$_field					= array();
							$_field['key']			= 'variation[{{counter}}][meta][weight_unit]';
							$_field['label']		= 'Weight unit of measurement';
							$_field['class']		= 'chosen';

							$_options				= array();
							$_options['g']			= 'Gram';
							$_options['kg']			= 'Kilogram';

							echo form_field_dropdown( $_field, $_options );

						?>
					</div>
					<p class="no-dimensions" style="display:none;">
						This product has no dimensions.
					</p>
				</fieldset>
			</div>

			<fieldset>
				<legend>Other Meta Data</legend>
				<?php

					foreach ( $product_types_meta AS $id => $fields ) :

						echo '<div class="meta-fields meta-fields-' . $id . '" style="display:none;">';

						if ( $fields ) :

							//	TODO: use the form builder library
							foreach ( $fields AS $field ) :

								$_field					= array();
								$_field['key']			= 'variation[{{counter}}][meta][' . $field->key . ']';
								$_field['label']		= $field->label;
								$_field['required']		= array_search( 'required', explode( '|', $field->validation ) ) ? TRUE : FALSE;

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


								//	Don't do this for the first iteration as it's being done in PHP.
								if ( ! isset( $is_first ) || ! $is_first ) :

									//	Replace any reference to </script> with <!--/script--> which will be parsed by the JS
									//	Otherwise it prematurely closes the template.

									$_field_out = str_replace( '<script type="text/javascript">', '<!--script type="text/javascript"-->', $_field_out );
									$_field_out = str_replace( '</script>', '<!--/script-->', $_field_out );

								endif;

								echo $_field_out;

							endforeach;

						else :

							echo '<p>There are no extra fields for this product type.</p>';

						endif;

						echo '</div>';

					endforeach;

				?>
			</fieldset>
		</div>

		<div class="tab page" id="tab-varitation-{{counter}}-pricing" style="display:none">
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

							$_attr_price		= 'data-code="' . SHOP_BASE_CURRENCY_CODE . '" class="base-price"';
							$_attr_price_sale	= 'data-code="' . SHOP_BASE_CURRENCY_CODE . '" class="base-price-sale"';

						else :

							$_attr_price		= 'class="variation-price ' . SHOP_BASE_CURRENCY_CODE . '"';
							$_attr_price_sale	= 'class="variation-price-sale ' . SHOP_BASE_CURRENCY_CODE . '"';

						endif;

					?>
					<tr>
						<td class="currency">
							<?=SHOP_BASE_CURRENCY_CODE?>
							<?=form_hidden( 'variation[{{counter}}][pricing][0][currency_id]', SHOP_BASE_CURRENCY_ID )?>
						</td>
						<td class="price">
							<?=form_input( 'variation[{{counter}}][pricing][0][price]', NULL, 'data-prefix="' . SHOP_BASE_CURRENCY_SYMBOL . '" ' . $_attr_price . ' placeholder="Price"' )?>
						</td>
						<td class="price-sale">
							<?=form_input( 'variation[{{counter}}][pricing][0][sale_price]', NULL, 'data-prefix="' . SHOP_BASE_CURRENCY_SYMBOL . '" ' . $_attr_price_sale . ' placeholder="Sale price"' )?>
						</td>
					</tr>

					<!--	OTHER CURRENCIES	-->
					<?php

						$_counter = 1;
						foreach ( $currencies AS $currency ) :

							if ( $currency->id != SHOP_BASE_CURRENCY_ID ) :

								if ( $is_first ) :

									$_attr_price		= 'data-code="' . $currency->code . '" class="base-price"';
									$_attr_price_sale	= 'data-code="' . $currency->code . '" class="base-price-sale"';

								else :

									$_attr_price		= 'class="variation-price ' . $currency->code . '"';
									$_attr_price_sale	= 'class="variation-price-sale ' . $currency->code . '"';

								endif;

								?>
								<tr>
									<td class="currency">
										<?=$currency->code?>
										<?=form_hidden( 'variation[{{counter}}][pricing][' . $_counter . '][currency_id]', $currency->id )?>
									</td>
									<td class="price">
										<?=form_input( 'variation[{{counter}}][pricing][' . $_counter . '][price]', NULL, 'data-prefix="' . $currency->symbol . '" ' . $_attr_price . ' placeholder="Calculate automatically"' )?>
									</td>
									<td class="price-sale">
										<?=form_input( 'variation[{{counter}}][pricing][' . $_counter . '][sale_price]', NULL, 'data-prefix="' . $currency->symbol . '" ' . $_attr_price_sale . ' placeholder="Calculate automatically"' )?>
									</td>
								</tr>
								<?php

								$_counter++;

							endif;

						endforeach;

					?>

				</tbody>
			</table>
			<?php

				if ( $is_first ) :

					echo '<p id="variation-sync-prices" style="display:none">';
					echo '<a href="#" class="awesome small orange">Sync Prices</a>';
					echo '</p>';

				endif;

			?>
		</div>

		<div class="tab page" id="tab-varitation-{{counter}}-gallery" style="display:none">
			<p>
				Specify which, if any, of the uploaded gallery images feature this product variation.
			</p>
			<ul class="gallery-associations empty">
				<li class="empty">No images have been uploaded; upload some using the <a href="#">Gallery tab</a></li>
				<li class="actions">
					<a href="#" data-function="all" class="action awesome small orange">Select All</a>
					<a href="#" data-function="none" class="action awesome small orange">Select None</a>
					<a href="#" data-function="toggle" class="action awesome small orange">Toggle</a>
				</li>
			</ul>
		</div>

		<div class="tab page" id="tab-varitation-{{counter}}-shipping" style="display:none">
			<p>
				Define the shipping options available for this variant. Shipping options do not have to be the same between variations.
			</p>
			<?php

				$_field					= array();
				$_field['key']			= 'variation[{{counter}}][shipping][collection_only]';
				$_field['label']		= 'Collection Only';
				$_field['readonly']		= ! shop_setting( 'warehouse_collection_enabled' );
				$_tip					= 'Items marked as collection only will be handled differently in checkout and reporting. They also dont contribute to the overall dimensions and weight of the order when calculating shipping costs.';

				echo form_field_boolean( $_field, $_tip );

			?>
		</div>
	</section>
</div>