<div id="variation-{{counter}}" class="variation" data-counter="{{counter}}">
	<div class="not-applicable">
		<p>
			<strong>The specified product type has a limited number of variations it can support.</strong>
			This variation will be deleted when you submit this form.
		</p>
	</div>
	<ul class="tabs" data-tabgroup="variation-{{counter}}">
		<li class="tab active">
			<a href="#" data-tab="tab-varitation-{{counter}}-details">Details</a>
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
				$_field['required']		= TRUE;
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
			<p>
				The following meta information is dependant on the product type.
			</p>
			<?php

				foreach ( $product_types_meta AS $id => $fields ) :

					echo '<div class="meta-fields meta-fields-' . $id . '" style="display:none;">';

					if ( $fields ) :

						//	TODO: use the form builder library
						foreach ( $fields AS $field ) :

							$_field					= array();
							$_field['key']			= 'meta[' . $field->key . ']';
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
					<tr>
						<td class="currency">
							<?=SHOP_BASE_CURRENCY_CODE?>
							<?=form_hidden( 'variation[{{counter}}][pricing][0][currency_id]', SHOP_BASE_CURRENCY_ID, 'placeholder="Calculate automatically"' )?>
						</td>
						<td class="price">
							<?=form_input( 'variation[{{counter}}][pricing][0][price]', NULL, 'data-prefix="' . SHOP_BASE_CURRENCY_SYMBOL . '" placeholder="Price"' )?>
						</td>
						<td class="price-sale">
							<?=form_input( 'variation[{{counter}}][pricing][0][sale_price]', NULL, 'data-prefix="' . SHOP_BASE_CURRENCY_SYMBOL . '" placeholder="Sale price"' )?>
						</td>
					</tr>

					<!--	OTHER CURRENCIES	-->
					<?php

						$_counter = 1;
						foreach ( $currencies AS $currency ) :

							if ( $currency->id != SHOP_BASE_CURRENCY_ID ) :

								?>
								<tr>
									<td class="currency">
										<?=$currency->code?>
										<?=form_hidden( 'variation[{{counter}}][pricing][' . $_counter . '][currency_id]', $currency->id, 'placeholder="Calculate automatically"' )?>
									</td>
									<td class="price">
										<?=form_input( 'variation[{{counter}}][pricing][' . $_counter . '][price]', NULL, 'data-prefix="' . $currency->symbol . '" placeholder="Calculate automatically"' )?>
									</td>
									<td class="price-sale">
										<?=form_input( 'variation[{{counter}}][pricing][' . $_counter . '][sale_price]', NULL, 'data-prefix="' . $currency->symbol . '" placeholder="Calculate automatically"' )?>
									</td>
								</tr>
								<?php

								$_counter++;

							endif;

						endforeach;

					?>

				</tbody>
			</table>
		</div>

		<div class="tab page" id="tab-varitation-{{counter}}-gallery" style="display:none">
			<p>
				Specify which, if any, of the uploaded gallery images feature this product variation.
			</p>
			<ul class="gallery-associations empty">
				<li class="empty">No images have been uploaded; upload some using the <a href="#">Gallery tab</a></li>
			</ul>
		</div>

		<div class="tab page" id="tab-varitation-{{counter}}-shipping" style="display:none">
			<p>
				Define the shipping options available for this variant. Shipping options do not have to be the same between variations.
			</p>
			<table class="shipping-options empty">
				<thead>
					<tr>
						<th class="courier">Courier &amp; Method</th>
						<th class="price">Price</th>
						<th class="price-additional">Additonal</th>
						<th class="delete">&nbsp;</th>
					</tr>
				</thead>
				<tbody>
					<tr class="empty">
						<td colspan="4" class="no-data">No shipping methods defined</td>
					</tr>
				</tbody>
			</table>
			<p>
				<a href="#" id="add-shipping-option" data-variation-counter="{{counter}}" class="awesome small green">Add Shipping Option</a>
			</p>
		</div>
	</section>
</div>