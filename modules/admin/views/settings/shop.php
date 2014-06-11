<div class="group-shop settings">
	<p>
		Configure various aspects of the shop.
	</p>

	<hr />

		<ul class="tabs">
			<?php $_active = $this->input->post( 'update' ) == 'settings' || ! $this->input->post() ? 'active' : ''?>
			<li class="tab <?=$_active?>">
				<a href="#" data-tab="tab-general">General</a>
			</li>

			<?php $_active = $this->input->post( 'update' ) == 'skin' ? 'active' : ''?>
			<li class="tab <?=$_active?>">
				<a href="#" data-tab="tab-skin">Skin</a>
			</li>

			<?php $_active = $this->input->post( 'update' ) == 'paymentgateways' ? 'active' : ''?>
			<li class="tab <?=$_active?>">
				<a href="#" data-tab="tab-paymentgateway">Payment Gateways</a>
			</li>

			<?php $_active = $this->input->post( 'update' ) == 'currencies' ? 'active' : ''?>
			<li class="tab <?=$_active?>">
				<a href="#" data-tab="tab-currencies">Currencies</a>
			</li>

			<?php $_active = $this->input->post( 'update' ) == 'shipping_methods' ? 'active' : ''?>
			<li class="tab <?=$_active?>">
				<a href="#" data-tab="tab-shipping-methods">Shipping Methods</a>
			</li>

			<?php $_active = $this->input->post( 'update' ) == 'tax_rates' ? 'active' : ''?>
			<li class="tab <?=$_active?>">
				<a href="#" data-tab="tab-tax-rates">Tax Rates</a>
			</li>
		</ul>

		<section class="tabs pages">

			<?php $_display = $this->input->post( 'update' ) == 'settings' || ! $this->input->post() ? 'active' : ''?>
			<div id="tab-general" class="tab page <?=$_display?> general">
				<?=form_open( NULL, 'style="margin-bottom:0;"' )?>
				<?=form_hidden( 'update', 'settings' )?>
				<p>
					Generic store settings. Use these to control some store behaviours.
				</p>
				<hr />
				<fieldset id="shop-settings-free-shipping">
					<legend>Free Shipping</legend>
					<p>
						If you wish to offer your customers an incentive to spend more you can offer free shipping when they spend a certain amount. Define what that figure should be here.
						If you don't want to offer this service set the threshold to 0.
					</p>
					<p class="system-alert message no-close">
						<strong>Please note:</strong> This is calculated on the cost of items <em>before</em> any tax is applied.
					</p>
					<?php

						//	Order Notifications
						$_field					= array();
						$_field['key']			= 'free_shipping_threshold';
						$_field['label']		= 'Threshold';
						$_field['default']		= app_setting( $_field['key'], 'shop' );

						echo form_field( $_field );

					?>
				</fieldset>

				<fieldset id="shop-settings-warehouse-collection">
					<legend>Warehouse Collection</legend>
					<p>
						If you wish to give your customers the option of collecting from your warehouse or store then turn this feature on here. Also provide the address of the warehouse or store so we can let the customer know where to go.
					</p>
					<?php

						//	Order Notifications
						$_field					= array();
						$_field['key']			= 'warehouse_collection_enabled';
						$_field['label']		= 'Enabled';
						$_field['default']		= app_setting( $_field['key'], 'shop' );
						$_field['id']			= 'warehouse_collection_enabled';

						echo form_field_boolean( $_field );

						// --------------------------------------------------------------------------

						if ( $this->input->post( $_field['key'] ) ) :

							$_display = $this->input->post( $_field['key'] ) ? 'block' : 'none';

						else :

							$_display = app_setting( $_field['key'], 'shop' ) ? 'block' : 'none';

						endif;

						echo '<div id="warehouse-collection-address" style="display:' . $_display . '">';

							$_field					= array();
							$_field['key']			= 'warehouse_addr_addressee';
							$_field['label']		= 'Addressee';
							$_field['default']		= app_setting( $_field['key'], 'shop' );

							echo form_field( $_field );

							// --------------------------------------------------------------------------

							$_field					= array();
							$_field['key']			= 'warehouse_addr_line1';
							$_field['label']		= 'Address Line 1';
							$_field['default']		= app_setting( $_field['key'], 'shop' );

							echo form_field( $_field );

							// --------------------------------------------------------------------------

							$_field					= array();
							$_field['key']			= 'warehouse_addr_line2';
							$_field['label']		= 'Address Line 2';
							$_field['default']		= app_setting( $_field['key'], 'shop' );

							echo form_field( $_field );

							// --------------------------------------------------------------------------

							$_field					= array();
							$_field['key']			= 'warehouse_addr_town';
							$_field['label']		= 'Town';
							$_field['default']		= app_setting( $_field['key'], 'shop' );

							echo form_field( $_field );

							// --------------------------------------------------------------------------

							$_field					= array();
							$_field['key']			= 'warehouse_addr_postcode';
							$_field['label']		= 'Postcode';
							$_field['default']		= app_setting( $_field['key'], 'shop' );

							echo form_field( $_field );

							// --------------------------------------------------------------------------

							$_field					= array();
							$_field['key']			= 'warehouse_addr_state';
							$_field['label']		= 'State/County';
							$_field['default']		= app_setting( $_field['key'], 'shop' );

							echo form_field( $_field );

							// --------------------------------------------------------------------------

							$_field					= array();
							$_field['key']			= 'warehouse_addr_country';
							$_field['label']		= 'Country';
							$_field['default']		= app_setting( $_field['key'], 'shop' );

							echo form_field( $_field );

						echo '</div>';

					?>
				</fieldset>

				<fieldset id="shop-settings-url">
					<legend>URL</legend>
					<p>
						Customise the shop's URL by specifying it here.
					</p>
					<?php

						//	Blog URL
						$_field					= array();
						$_field['key']			= 'url';
						$_field['label']		= 'Shop URL';
						$_field['default']		= app_setting( $_field['key'], 'shop' );
						$_field['placeholder']	= 'Customise the Shop\'s URL (include trialing slash)';

						echo form_field( $_field );

					?>
				</fieldset>

				<fieldset id="shop-settings-invoice">
					<legend>Invoice details</legend>
					<p>
						These details will be visible on invoices and email receipts.
					</p>
					<?php

						//	Company Name
						$_field					= array();
						$_field['key']			= 'invoice_company';
						$_field['label']		= 'Company Name';
						$_field['default']		= app_setting( $_field['key'], 'shop' );
						$_field['placeholder']	= 'The registered company name.';

						echo form_field( $_field );

						// --------------------------------------------------------------------------

						//	Address
						$_field					= array();
						$_field['key']			= 'invoice_address';
						$_field['label']		= 'Company Address';
						$_field['type']			= 'textarea';
						$_field['default']		= app_setting( $_field['key'], 'shop' );
						$_field['placeholder']	= 'The address to show on the invoice.';

						echo form_field( $_field );

						// --------------------------------------------------------------------------

						//	VAT Number
						$_field					= array();
						$_field['key']			= 'invoice_vat_no';
						$_field['label']		= 'VAT Number';
						$_field['default']		= app_setting( $_field['key'], 'shop' );
						$_field['placeholder']	= 'Your VAT number, if any.';

						echo form_field( $_field );

						// --------------------------------------------------------------------------

						//	Company Number
						$_field					= array();
						$_field['key']			= 'invoice_company_no';
						$_field['label']		= 'Company Number';
						$_field['default']		= app_setting( $_field['key'], 'shop' );
						$_field['placeholder']	= 'Your company number.';

						echo form_field( $_field );

					?>
				</fieldset>
				<fieldset id="shop-settings-misc">
					<legend>Miscellaneous</legend>
					<?php

						//	Brand Listing
						$_field					= array();
						$_field['key']			= 'page_brand_listing';
						$_field['label']		= 'Brand Listing Page';
						$_field['default']		= app_setting( $_field['key'], 'shop' );

						echo form_field_boolean( $_field, 'The page shown when the brand URL is used, but no slug is specified. Renders all the populated brands and their SEO data.' );

						// --------------------------------------------------------------------------

						//	Category Listing
						$_field					= array();
						$_field['key']			= 'page_category_listing';
						$_field['label']		= 'Category Listing Page';
						$_field['default']		= app_setting( $_field['key'], 'shop' );

						echo form_field_boolean( $_field, 'The page shown when the category URL is used, but no slug is specified. Renders all the populated categories and their SEO data.' );

						// --------------------------------------------------------------------------

						//	Collection Listing
						$_field					= array();
						$_field['key']			= 'page_collection_listing';
						$_field['label']		= 'Collection Listing Page';
						$_field['default']		= app_setting( $_field['key'], 'shop' );

						echo form_field_boolean( $_field, 'The page shown when the collection URL is used, but no slug is specified. Renders all the active collections and their SEO data.' );

						// --------------------------------------------------------------------------

						//	Range Listing
						$_field					= array();
						$_field['key']			= 'page_range_listing';
						$_field['label']		= 'Range Listing Page';
						$_field['default']		= app_setting( $_field['key'], 'shop' );

						echo form_field_boolean( $_field, 'The page shown when the range URL is used, but no slug is specified. Renders all the active ranges and their SEO data.' );

						// --------------------------------------------------------------------------

						//	Sale Listing
						$_field					= array();
						$_field['key']			= 'page_sale_listing';
						$_field['label']		= 'Sale Listing Page';
						$_field['default']		= app_setting( $_field['key'], 'shop' );

						echo form_field_boolean( $_field, 'The page shown when the sale URL is used, but no slug is specified. Renders all the active sales and their SEO data.' );

						// --------------------------------------------------------------------------

						//	Tag Listing
						$_field					= array();
						$_field['key']			= 'page_tag_listing';
						$_field['label']		= 'Tag Listing Page';
						$_field['default']		= app_setting( $_field['key'], 'shop' );

						echo form_field_boolean( $_field, 'The page shown when the tag URL is used, but no slug is specified. Renders all the populated tags and their SEO data.' );

					?>
				</fieldset>
				<p style="margin-top:1em;margin-bottom:0;">
					<?=form_submit( 'submit', lang( 'action_save_changes' ), 'style="margin-bottom:0;"' )?>
				</p>
				<?=form_close()?>
			</div>

			<?php $_display = $this->input->post( 'update' ) == 'skin' ? 'active' : ''?>
			<div id="tab-skin" class="tab page <?=$_display?> skin">
				<?=form_open( NULL, 'style="margin-bottom:0;"' )?>
				<?=form_hidden( 'update', 'skin' )?>
				<p>
					The following Shop skins are available to use.
				</p>
				<hr />
				<?php

					if ( $skins ) :

						$_selected_skin = app_setting( 'skin', 'shop' ) ? app_setting( 'skin', 'shop' ) : 'getting-started';

						echo '<ul class="skins">';
						foreach( $skins AS $skin ) :

							$_name			= ! empty( $skin->name ) ? $skin->name : 'Untitled';
							$_description	= ! empty( $skin->description ) ? $skin->description : '';

							if ( file_exists( $skin->path . 'icon.png' ) ) :

								$_icon = $skin->url . 'icon.png';

							elseif ( file_exists( $skin->path . 'icon.jpg' ) ) :

								$_icon = $skin->url . 'icon.jpg';

							elseif ( file_exists( $skin->path . 'icon.gif' ) ) :

								$_icon = $skin->url . 'icon.gif';

							else :

								$_icon = NAILS_ASSETS_URL . 'img/admin/modules/settings/shop-skin-no-icon.png';

							endif;

							$_selected	= $skin->dir == $_selected_skin ? TRUE : FALSE;
							$_class		= $_selected ? 'selected' : '';

							echo '<li class="skin ' . $_class . '" rel="tipsy" title="' . $_description . '">';
								echo '<div class="icon">' . img( $_icon ) . '</div>';
								echo '<div class="name">' . $_name . '</div>';
								echo form_radio( 'skin', $skin->dir, $_selected );
							echo '</li>';

						endforeach;
						echo '</ul>';

						echo '<hr class="clearfix" />';

					else :

						echo '<p class="system-alert error no-close">';
							echo '<strong>Error:</strong> ';
							echo 'I\'m sorry, but I couldn\'t find any skins to use. This is a configuration error and should be raised with the developer.';
						echo '</p>';

					endif;

				?>
				<p>
					<?=form_submit( 'submit', lang( 'action_save_changes' ), 'style="margin-bottom:0;"' )?>
				</p>
				<?=form_close()?>

			</div>

			<?php $_display = $this->input->post( 'update' ) == 'paymentgateways' ? 'active' : ''?>
			<div id="tab-paymentgateway" class="tab page <?=$_display?> paymentgateway">
				<p>
					Set Payment Gateway credentials.
				</p>
				<hr />
				<?php

				if ( $payment_gateways ) :

					echo form_open( NULL, 'style="margin-bottom:0;"' );
					echo form_hidden( 'update', 'paymentgateways' );

					foreach ( $payment_gateways AS $pg ) :

						echo '<fieldset id="shop-settings-pg-' . $pg->slug . '">';
						echo '<legend>';
						if ( $pg->logo ) :

							echo img( array( 'src' => NAILS_ASSETS_URL . 'img/modules/shop/payment-gateway/' . $pg->logo, 'style' => 'margin-top:-10px;' ) );

						else :

							echo $pg->label;

						endif;
						echo '</legend>';

						//	Only superusers can change the 'enabled' status of a payment gateway
						if ( $user->is_superuser() ) :

							//	Enabled
							$_field					= array();
							$_field['key']			= 'paymentgateway[' . $pg->id . '][enabled]';
							$_field['label']		= 'Supported';
							$_field['default']		= $pg->enabled;

							echo form_field_boolean( $_field );

						endif;

						// --------------------------------------------------------------------------

						//	Account ID
						$_field					= array();
						$_field['key']			= 'paymentgateway[' . $pg->id . '][account_id]';
						$_field['label']		= 'Account ID';
						$_field['default']		= $pg->account_id;
						$_field['placeholder']	= 'The unique account identifier';

						echo form_field( $_field );

						// --------------------------------------------------------------------------

						//	API KEY
						$_field					= array();
						$_field['key']			= 'paymentgateway[' . $pg->id . '][api_key]';
						$_field['label']		= 'API Key';
						$_field['default']		= $pg->api_key;
						$_field['placeholder']	= 'The key for accessing this payment gateway\'s API';

						echo form_field( $_field );

						// --------------------------------------------------------------------------

						//	API Secret
						$_field					= array();
						$_field['key']			= 'paymentgateway[' . $pg->id . '][api_secret]';
						$_field['label']		= 'API Secret';
						$_field['default']		= $pg->api_secret;
						$_field['placeholder']	= 'The secret or password for accessing this payment gateway\'s API';

						echo form_field( $_field );

						// --------------------------------------------------------------------------

						if ( $user->is_superuser() ) :

							//	Sandbox Account ID
							$_field					= array();
							$_field['key']			= 'paymentgateway[' . $pg->id . '][sandbox_account_id]';
							$_field['label']		= 'Sandbox Account ID';
							$_field['default']		= $pg->sandbox_account_id;
							$_field['placeholder']	= 'The unique account identifier';

							echo form_field( $_field );

							// --------------------------------------------------------------------------

							//	Sandbox API KEY
							$_field					= array();
							$_field['key']			= 'paymentgateway[' . $pg->id . '][sandbox_api_key]';
							$_field['label']		= 'Sandbox API Key';
							$_field['default']		= $pg->sandbox_api_key;
							$_field['placeholder']	= 'The key for accessing this payment gateway\'s API';

							echo form_field( $_field );

							// --------------------------------------------------------------------------

							//	Sandbox API Secret
							$_field					= array();
							$_field['key']			= 'paymentgateway[' . $pg->id . '][sandbox_api_secret]';
							$_field['label']		= 'Sandbox API Secret';
							$_field['default']		= $pg->sandbox_api_secret;
							$_field['placeholder']	= 'The secret or password for accessing this payment gateway\'s API';

							echo form_field( $_field );

						endif;

						echo '</fieldset>';

					endforeach;
					echo '<p style="margin-top:1em;margin-bottom:0;">';
					echo form_submit( 'submit', lang( 'action_save_changes' ), 'style="margin-bottom:0;"' );
					echo '</p>';
					echo form_close();

				else :

					echo '<p class="system-alert message no-close">';
					echo '<strong>No Payment gateways have been enabled.</strong>';
					echo '<br />No payment gateways have been enabled for this site. Please contact the developers on ' . mailto( APP_DEVELOPER_EMAIL ) . ' for assistance.';
					echo '</p>';

				endif;

				?>
			</div>

			<?php $_display = $this->input->post( 'update' ) == 'currencies' ? 'active' : ''?>
			<div id="tab-currencies" class="tab page <?=$_display?> currencies">
				<?=form_open( NULL, 'style="margin-bottom:0;"' )?>
				<?=form_hidden( 'update', 'currencies' )?>
				<p>
					Configure supported currencies.
				</p>
				<hr />
				<fieldset id="shop-currencies-base">
					<legend>Base Currency</legend>
					<p>
						The base currency is the default currency of the shop. When you create a new product and define it's
						price, you are doing so in the base currency. You are free to change this but it will be reflected
						across the entire store, <em>change with <strong>extreme</strong> caution</em>.
					</p>
					<?php

						//	Base Currency
						$_field					= array();
						$_field['key']			= 'base_currency';
						$_field['label']		= 'Base Currency';
						$_field['default']		= app_setting( $_field['key'], 'shop' );

						echo form_dropdown( $_field['key'], $currencies_active_flat, set_value( $_field['key'], $_field['default'] ), 'class="select2"' );

					?>
				</fieldset>
				<fieldset id="shop-currencies-base">
					<legend>Supported Currencies</legend>
					<p>
						Define which currencies you wish to support in your store, the base currency must always be supported.
					</p>
					<?php

						echo '<select name="active_currencies[]" multiple="multiple" class="select2">';
						foreach ( $currencies_all_flat AS $currency ) :

							$_selected = $currency->is_active ? 'selected="selected"' : '';
							echo '<option value="'. $currency->id . '" ' . $_selected . '>' . $currency->code . ' - ' . $currency->label . '</option>';

						endforeach;
						echo '</select>';

					?>
				</fieldset>
				<p style="margin-top:1em;margin-bottom:0;">
					<?=form_submit( 'submit', lang( 'action_save_changes' ), 'style="margin-bottom:0;"' )?>
				</p>
				<?=form_close()?>
			</div>


			<?php $_display = $this->input->post( 'update' ) == 'shipping_methods' ? 'active' : ''?>
			<div id="tab-shipping-methods" class="tab page <?=$_display?> shipping-methods">
				<?=form_open( NULL, 'id="form-shipping-methods" style="margin-bottom:0;"' )?>
				<?=form_hidden( 'update', 'shipping_methods' )?>
				<p>
					Configure supported shipping methods.
				</p>
				<hr />
				<table id="existing-shipping-methods" class="settings-table">
					<thead>
						<tr>
							<th class="order-handle">&nbsp;</th>
							<th class="courier">Courier</th>
							<th class="method">Method</th>
							<th class="default_price">Price</th>
							<th class="default_price_additional">1+ Price</th>
							<th class="tax_rate">Tax Rate</th>
							<th class="notes">Notes</th>
							<th class="active">Active</th>
							<th class="default">Default</th>
							<th class="delete">&nbsp;</th>
						</tr>
					</thead>
					<tbody>
					<?php

						$_counter_ship = 0;
						foreach ( $shipping_methods AS $method ) :

							echo '<tr>';

							echo '<td class="order-handle">';
							echo '<input type="hidden" name="methods[' . $_counter_ship . '][id]" value="' . $method->id . '" />';
							echo '<input type="hidden" name="methods[' . $_counter_ship . '][order]" value="' . $method->order . '" class="order" />';
							echo '</td>';

							// --------------------------------------------------------------------------

							$_field = 'courier';

							echo '<td class="' . $_field . '">';
							echo form_input( 'methods[' . $_counter_ship . '][' . $_field . ']', set_value( 'methods[' . $_counter_ship . '][' . $_field . ']', $method->{$_field} ), 'class="table-cell"' );
							echo '</td>';

							// --------------------------------------------------------------------------

							$_field = 'method';

							echo '<td class="' . $_field . '">';
							echo form_input( 'methods[' . $_counter_ship . '][' . $_field . ']', set_value( 'methods[' . $_counter_ship . '][' . $_field . ']', $method->{$_field} ), 'class="table-cell"' );
							echo '</td>';

							// --------------------------------------------------------------------------

							$_field = 'default_price';

							echo '<td class="' . $_field . '">';
							echo form_input( 'methods[' . $_counter_ship . '][' . $_field . ']', set_value( 'methods[' . $_counter_ship . '][' . $_field . ']', $method->{$_field} ), 'class="table-cell"' );
							echo '</td>';

							// --------------------------------------------------------------------------

							$_field = 'default_price_additional';

							echo '<td class="' . $_field . '">';
							echo form_input( 'methods[' . $_counter_ship . '][' . $_field . ']', set_value( 'methods[' . $_counter_ship . '][' . $_field . ']', $method->{$_field} ), 'class="table-cell"' );
							echo '</td>';

							// --------------------------------------------------------------------------

							echo '<td class="tax_rate">';
							echo form_dropdown( 'methods[' . $_counter_ship . '][tax_rate_id]', $tax_rates_flat, set_value( 'methods[' . $_counter_ship . '][tax_rate_id]', $method->tax_rate->id ), 'class="select2"' );
							echo '</td>';

							// --------------------------------------------------------------------------

							$_field = 'notes';

							echo '<td class="' . $_field . '">';
							echo form_input( 'methods[' . $_counter_ship . '][' . $_field . ']', set_value( 'methods[' . $_counter_ship . '][' . $_field . ']', $method->{$_field} ), 'class="table-cell"' );
							echo '</td>';

							// --------------------------------------------------------------------------

							echo '<td class="active">';
							if ( $_POST ) :

								$_checked = isset( $_POST['methods'][$_counter_ship]['is_active'] ) ? TRUE : FALSE;

							else :

								$_checked = $method->is_active;

							endif;
							echo form_checkbox( 'methods[' . $_counter_ship . '][is_active]', TRUE, $_checked );
							echo '</td>';

							// --------------------------------------------------------------------------

							echo '<td class="default">';
							echo form_radio( 'default', $_counter_ship, set_radio( 'default', $_counter_ship, $method->is_default ) );
							echo '</td>';

							// --------------------------------------------------------------------------

							echo '<td class="delete">';
							echo '<a href="#" class="delete-row awesome small red">Delete</a>';
							echo '</td>';


							echo '</tr>';


						$_counter_ship++;
						endforeach;

					?>
					</tbody>
				</table>
				<p style="margin-bottom:0;">
					<a href="#" id="add-new-shipping" style="float:right" class="awesome green small">Add Shipping Method</a>
					<?=form_submit( 'submit', lang( 'action_save_changes' ), 'style="margin-bottom:0;"' )?>
				</p>
				<?=form_close()?>
			</div>


			<?php $_display = $this->input->post( 'update' ) == 'tax_rates' ? 'active' : ''?>
			<div id="tab-tax-rates" class="tab page <?=$_display?> tax-rates">
				<?=form_open( NULL, 'id="form-tax-rates" style="margin-bottom:0;"' )?>
				<?=form_hidden( 'update', 'tax_rates' )?>
				<p>
					Configure supported Tax Rates. Specify a customer facing label and a rate; the rate must be a decimal between 0 and 1. For example, to specify a tax rate of 17.5% the rate would be noted as 0.175.
				</p>
				<hr />
				<table id="existing-tax-rates" class="settings-table">
					<thead>
						<tr>
							<th class="label">Label</th>
							<th class="rate">Rate</th>
							<th class="delete">&nbsp;</th>
						</tr>
					</thead>
					<tbody>
					<?php

						$_counter_tax = 0;
						foreach ( $tax_rates AS $rate ) :

							echo '<tr>';

							$_field = 'label';

							echo '<td class="' . $_field . '">';
								echo '<input type="hidden" name="rates[' . $_counter_tax . '][id]" value="' . $rate->id . '" />';
								echo form_input( 'rates[' . $_counter_tax . '][' . $_field . ']', set_value( 'rates[' . $_counter_tax . '][' . $_field . ']', $rate->{$_field} ), 'placeholder="Specify the tax rate label, e.g VAT" class="table-cell"' );
							echo '</td>';

							// --------------------------------------------------------------------------

							$_field = 'rate';

							echo '<td class="' . $_field . '">';
								echo form_input( 'rates[' . $_counter_tax . '][' . $_field . ']', set_value( 'rates[' . $_counter_tax . '][' . $_field . ']', $rate->{$_field} ), 'placeholder="Specify the rate for this tax band, decimal between 0 and 1, e.g for 20% you\'d enter 0.2" class="table-cell"' );
							echo '</td>';

							// --------------------------------------------------------------------------

							echo '<td class="delete">';
								echo '<a href="#" class="delete-row awesome small red">Delete</a>';
							echo '</td>';


							echo '</tr>';


						$_counter_tax++;
						endforeach;

					?>
					</tbody>
				</table>
				<p style="margin-bottom:0;">
					<a href="#" id="add-new-tax-rate" style="float:right" class="awesome green small">Add Tax Rate</a>
					<?=form_submit( 'submit', lang( 'action_save_changes' ), 'style="margin-bottom:0;"' )?>
				</p>
				<?=form_close()?>
			</div>
		</section>
</div>

<script type="text/javascript">

	var _settings;

	$(function()
	{
		_settings = new NAILS_Admin_Shop_Settings();
		_settings.init();
	});

</script>

<script type="text/template" id="template-new-shipping">
<tr>
	<td class="order-handle">
		<input type="hidden" name="methods[{{counter}}][order]" value="" class="order" />
	</td>
	<td class="courier">
		<?=form_input( 'methods[{{counter}}][courier]', NULL, 'class="table-cell"' )?>
	</td>
	<td class="method">
		<?=form_input( 'methods[{{counter}}][method]', NULL, 'class="table-cell"' )?>
	</td>
	<td class="default_price">
		<?=form_input( 'methods[{{counter}}][default_price]', NULL, 'class="table-cell"' )?>
	</td>
	<td class="default_price_additional">
		<?=form_input( 'methods[{{counter}}][default_price_additional]', NULL, 'class="table-cell"' )?>
	</td>
	<td class="tax_rate">
		<?=form_dropdown( 'methods[{{counter}}][tax_rate_id]', $tax_rates_flat, NULL, 'class="select2"' )?>
	</td>
	<td class="notes">
		<?=form_input( 'methods[{{counter}}][notes]', NULL, 'class="table-cell"' )?>
	</td>
	<td class="active">
		<?=form_checkbox( 'methods[{{counter}}][is_active]', TRUE )?>
	</td>
	<td class="default">
		<?=form_radio( 'default', '{{counter}}' )?>
	</td>
	<td class="delete">
		<a href="#" class="delete-row awesome small red">Delete</a>
	</td>
</tr>
</script>
<script type="text/template" id="template-new-tax-rate">
<tr>
	<td class="label">
		<?=form_input( 'rates[{{counter}}][label]', NULL, 'placeholder="Specify the tax rate label, e.g VAT" class="table-cell"' )?>
	</td>
	<td class="rate">
		<?=form_input( 'rates[{{counter}}][rate]', NULL, 'placeholder="Specify the rate for this tax band, decimal between 0 and 1, e.g for 20% you\'d enter 0.2" class="table-cell"' )?>
	</td>
	<td class="delete">
		<a href="#" class="delete-row awesome small red">Delete</a>
	</td>
</tr>
</script>