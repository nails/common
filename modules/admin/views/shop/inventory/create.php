<div class="group-shop inventory create">

	<?=form_open()?>
	<ul class="tabs">
		<li class="tab">
			<a href="#" data-tab="tab-basics">Product Info</a>
		</li>

		<li class="tab">
			<a href="#" data-tab="tab-variations">Variations</a>
		</li>

		<li class="tab active">
			<a href="#" data-tab="tab-gallery">Gallery</a>
		</li>

		<li class="tab">
			<a href="#" data-tab="tab-shipping">Shipping</a>
		</li>

		<li class="tab">
			<a href="#" data-tab="tab-seo">SEO</a>
		</li>
	</ul>

	<section class="tabs pages">

		<div class="tab page basics" id="tab-basics" style="display:none;">
			<fieldset id="shop-inventory-create-basic">
				<legend>Basic Information</legend>
				<?php

					$_field					= array();
					$_field['key']			= 'type_id';
					$_field['label']		= 'Type';
					$_field['required']		= TRUE;
					$_field['class']		= 'chosen';

					echo form_field_dropdown( $_field, $product_types );

					// --------------------------------------------------------------------------

					$_field					= array();
					$_field['key']			= 'title';
					$_field['label']		= 'Title';
					$_field['required']		= TRUE;
					$_field['placeholder']	= 'Give this product a title';

					echo form_field( $_field );

					// --------------------------------------------------------------------------

					$_field					= array();
					$_field['key']			= 'description';
					$_field['label']		= 'Description';
					$_field['required']		= TRUE;
					$_field['placeholder']	= 'Give this product a description';
					$_field['type']			= 'textarea';

					echo form_field( $_field );

					// --------------------------------------------------------------------------

					$_field					= array();
					$_field['key']			= 'price';
					$_field['label']		= 'Price';
					$_field['required']		= TRUE;
					$_field['placeholder']	= 'Set the product\'s price (in ' . SHOP_BASE_CURRENCY_CODE . ', no symbol)';

					echo form_field( $_field );

					// --------------------------------------------------------------------------

					$_field					= array();
					$_field['key']			= 'sale_price';
					$_field['label']		= 'Sale Price';
					$_field['required']		= TRUE;
					$_field['placeholder']	= 'Set the product\'s sale price (in ' . SHOP_BASE_CURRENCY_CODE . ', no symbol)';

					echo form_field( $_field );

					// --------------------------------------------------------------------------

					$_field					= array();
					$_field['key']			= 'sale_start';
					$_field['label']		= 'Sale Starts';
					$_field['required']		= TRUE;
					$_field['placeholder']	= 'YYYY-MM-DD HH:MM:SS';

					echo form_field( $_field, 'Define when the product goes on sale' );

					// --------------------------------------------------------------------------

					$_field					= array();
					$_field['key']			= 'sale_end';
					$_field['label']		= 'Sale Ends';
					$_field['required']		= TRUE;
					$_field['placeholder']	= 'YYYY-MM-DD HH:MM:SS';

					echo form_field( $_field, 'Define when the product goes off sale' );

					// --------------------------------------------------------------------------

					$_field					= array();
					$_field['key']			= 'tax_rate_id';
					$_field['label']		= 'Tax Rate';
					$_field['required']		= TRUE;
					$_field['class']		= 'chosen';

					echo form_field_dropdown( $_field, $tax_rates );

					// --------------------------------------------------------------------------

					$_field					= array();
					$_field['key']			= 'is_active';
					$_field['label']		= 'Is Active';
					$_field['required']		= TRUE;
					$_field['text_on']		= strtoupper( lang( 'yes' ) );
					$_field['text_off']		= strtoupper( lang( 'no' ) );

					echo form_field_boolean( $_field );

					// --------------------------------------------------------------------------

					$_field					= array();
					$_field['key']			= 'quantity_available';
					$_field['label']		= 'Quantity Available';
					$_field['required']		= TRUE;
					$_field['placeholder']	= 'Define the number of units available for sale, leave blank if unlimited.';

					echo form_field( $_field );
				?>
			</fieldset>

			<fieldset id="shop-inventory-create-meta">
				<legend>Extended Information</legend>
			</fieldset>

		</div>

		<div class="tab page variations" id="tab-variations" style="display:none;">
			<p>
				Variations allow you to offer the same product but with different attributes (e.g colours).
				A variation can also affect the base price.
			</p>
			<table id="product-variations">
				<thead>
					<tr>
						<th class="handle">&nbsp;</th>
						<th class="label">Label</th>
						<th class="price-adjustment">Price Adjustment</th>
						<th class="delete">&nbsp;</th>
					</tr>
				</thead>
				<tbody>
				</tbody>
			</table>
			<p>
				<a href="#" id="product-variation-add" class="awesome green small">Add Variation</a>
			</p>
		</div>

		<div class="tab page gallery" id="tab-gallery" style="display:block;">
			<p>
				Upload images to the product gallery.
				<small>
					Images only, max file size is 2MB.
				</small>
			</p>
			<p class="system-alert message no-close">
				<strong>Uploads will work like this:</strong>
				<br />1. Uploads are automatic/immediate
				<br />2. On each successful upload a new gallery item should be created containing the returned upload ID
				<br />3. Mega bonus points for a progress bar which renders over the image (like Vimeo, using onUploadProgress). Maybe hide the queue and somehow mimic the progress bar.
				<br />4. Form is submitted like normal
			</p>
			<p>
				<input type="file" id="uploadify" />
			</p>
			<ul id="gallery-items">
				<li class="gallery-item crunching">
					<a href="#" class="remove"></a>
					<div class="progress" style="height:100%"></div>
				</li>
				<li class="gallery-item">
					<a href="#" class="remove"></a>
					<div class="progress" style="height:0%"></div>
				</li>
				<li class="gallery-item">
					<a href="#" class="remove"></a>
					<div class="progress" style="height:0%"></div>
				</li>
			</ul>
		</div>

		<div class="tab page shipping" id="tab-shipping" style="display:none;">
			<p>
				By default the product will inherit the global shipping options as defined in the shop settings.
				If you wish to override these defaults for this product then please add options here.
			</p>
		</div>

		<div class="tab page seo" id="tab-seo" style="display:none;">
			<fieldset id="shop-inventory-create-seo">
				<legend>Search Engine Optimisation</legend>
				<?php

					$_field					= array();
					$_field['key']			= 'seo_title';
					$_field['label']		= 'Title';
					$_field['required']		= TRUE;
					$_field['placeholder']	= 'Search Engine Optimised title';

					echo form_field( $_field, 'Keep this relevant and below 60 characters' );

					// --------------------------------------------------------------------------

					$_field					= array();
					$_field['key']			= 'seo_description';
					$_field['label']		= 'Description';
					$_field['required']		= TRUE;
					$_field['placeholder']	= 'Search Engine Optimised description';
					$_field['type']			= 'textarea';

					echo form_field( $_field, 'Keep this relevant and below 140 characters' );


					// --------------------------------------------------------------------------

					$_field					= array();
					$_field['key']			= 'seo_keywords';
					$_field['label']		= 'Keywords';
					$_field['required']		= TRUE;
					$_field['placeholder']	= 'Comma separated keywords';

					echo form_field( $_field, 'Comma seperated keywords. Try to keep to 10 or fewer.' );

				?>
			</fieldset>
		</div>

	</section>
	<p>
		<?=form_submit( 'submit', lang( 'action_create' ) )?>
	</p>
	<?=form_close()?>
</div>

<script type="text/javascript">

	var _CREATE;
	$(function(){

		_CREATE =  new NAILS_Admin_Shop_Inventory_Add();
		_CREATE.init();

	});

</script>

<script type="text/template" id="template-product-variation">
	<tr>
		<td class="handle">
			<input type="hidden" name="variation[{{counter}}][order]" class="order" />
		</td>
		<td class="label">
			<?=form_input( 'variation[{{counter}}][label]', NULL, 'placeholder="Specify a label for this variation"' )?>
		</td>
		<td class="price-adjustment">
			<?=form_input( 'variation[{{counter}}][price_adjustment]', NULL, 'placeholder="Price adjustment"' )?>
		</td>
		<td class="delete">
			<a href="#" class="delete awesome small red">Delete</a>
		</td>
	</tr>
</script>
<script type="text/template" id="template-uploadify">
	<li class="uploadify-item">
	
	</li>
</script>