<div class="group-shop inventory create">

	<?=form_open( NULL, 'id="product-form"' )?>
	<ul class="tabs" data-tabgroup="main-product">
		<li class="tab active">
			<a href="#" data-tab="tab-basics">Product Info</a>
		</li>

		<li class="tab">
			<a href="#" id="tabber-description" data-tab="tab-description">Description</a>
		</li>

		<li class="tab">
			<a href="#" id="tabber-variations" data-tab="tab-variations">Variations</a>
		</li>

		<li class="tab">
			<a href="#" id="tabber-gallery" data-tab="tab-gallery">Gallery</a>
		</li>

		<li class="tab">
			<a href="#" id="tabber-attributes" data-tab="tab-attributes">Attributes</a>
		</li>

		<li class="tab">
			<a href="#" id="tabber-ranges-collections" data-tab="tab-ranges-collections">Ranges &amp; Collections</a>
		</li>

		<li class="tab">
			<a href="#" id="tabber-seo" data-tab="tab-seo">SEO</a>
		</li>
	</ul>

	<section class="tabs pages main-product">

		<div class="tab page basics fieldset" id="tab-basics" style="display:block;">
			<?php

				$_field					= array();
				$_field['key']			= 'type_id';
				$_field['label']		= 'Type';
				$_field['required']		= TRUE;
				$_field['class']		= 'type_id';
				$_field['id']			= 'type_id';

				echo form_field_dropdown( $_field, $product_types_flat );

				// --------------------------------------------------------------------------

				$_field					= array();
				$_field['key']			= 'title';
				$_field['label']		= 'Title';
				$_field['required']		= TRUE;
				$_field['placeholder']	= 'Give this product a title';

				echo form_field( $_field );

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
				$_field['key']			= 'brands[]';
				$_field['label']		= 'Brands';
				$_field['required']		= TRUE;
				$_field['class']		= 'brands';
				$_tip					= 'If this product contains multiple brands (e.g a hamper) specify them all here.';

				echo form_field_dropdown_multiple( $_field, $brands, $_tip );

				// --------------------------------------------------------------------------

				$_field					= array();
				$_field['key']			= 'categories[]';
				$_field['label']		= 'Categories';
				$_field['required']		= TRUE;
				$_field['class']		= 'categories';
				$_tip					= 'Specify which categories this product falls into.';

				echo form_field_dropdown_multiple( $_field, $categories, $_tip );

				// --------------------------------------------------------------------------

				$_field					= array();
				$_field['key']			= 'tags[]';
				$_field['label']		= 'Tags';
				$_field['required']		= TRUE;
				$_field['class']		= 'tags';
				$_tip					= 'Use tags to associate products together, e.g. events.';

				echo form_field_dropdown_multiple( $_field, $tags, $_tip );

				// --------------------------------------------------------------------------

				$_field					= array();
				$_field['key']			= 'tax_rate_id';
				$_field['label']		= 'Tax Rate';
				$_field['class']		= 'tax_rate_id';
				$_field['required']		= TRUE;

				echo form_field_dropdown( $_field, $tax_rates );

			?>
		</div>

		<div class="tab page description" id="tab-description" style="display:none">
			<?=form_error( 'body', '<p class="system-alert error no-close">', '</p>' )?>
			<textarea class="ckeditor" name="body"><?=set_value( 'body' )?></textarea>
			<p class="system-alert notice no-close" style="margin-top:10px;">
				<strong>Note:</strong> The editor's display might not be a true representation of the final layout
				due to application stylesheets on the front end which are not loaded here.
			</p>
		</div>

		<div class="tab page variations" id="tab-variations" style="display:none;">
			<p>
				Variations allow you to offer the same product but with different attributes (e.g colours or sizes).
				Shoppers will be given the choice of which variation they wish to purchase. There must always be at
				least one variation of a product. Confused? <a href="#help-variation-examples" class="fancybox">See some examples</a>.
			</p>
			<div id="product-variations">
				<?php

					//	Data which will be passed to template
					$_data		= array( 'is_first' => TRUE, 'counter' => 0 );

					//	Template, sort out the <script> issue
					$_template	= $this->load->view( 'admin/shop/inventory/utilities/template-mustache-inventory-variant', $_data, TRUE );

					//	Render
					echo $this->mustache->render( $_template, $_data );

				?>
			</div>
			<div class="add-variation-button enabled">
				<p class="enabled">
					<a href="#" id="product-variation-add" class="awesome green small">Add Variation</a>
				</p>
				<p class="disabled">
					<a class="awesome grey small">Add Variation</a>
					<span class="no-more-variations">The specified product type does not allow for any more variations to be added.</span>
				</p>
			</div>
		</div>

		<div class="tab page gallery" id="tab-gallery" style="display:none;">
			<p>
				Upload images to the product gallery. Once uploaded you can specify which variations are featured on the <a href="#" class="switch-to-variations">variations tab</a>.
				<small>
					Images only, max file size is 2MB.
				</small>
			</p>
			<p>
				<input type="file" id="file_upload" />
			</p>
			<p class="system-alert notice no-close" id="upload-message" style="display:none">
				<strong>Please be patient while files upload.</strong>
				<br />Tabs have been disabled until uploads are complete.
			</p>
			<ul id="gallery-items" class="empty">
				<li class="empty">
					No images, why not upload some?
				</li>
			</ul>
		</div>

		<div class="tab page attributes" id="tab-attributes" style="display:none;">
			<p>
				Specify specific product attributes, e..g for a pair of jeans you might specify a 'Style' attribute and give it a value of 'Bootcut'. Attributes should be common across all variations of the product.
			</p>
			<table>
				<thead>
					<tr>
						<th class="attribute">Attribute</th>
						<th class="value">Value</th>
						<th class="delete">&nbsp;</th>
					</tr>
				</thead>
				<tbody id="product-attributes">
				</tbody>
			</table>
			<p>
				<a href="#" id="product-attribute-add" class="awesome small green">Add Attribute</a>
			</p>
		</div>

		<div class="tab page ranges-collections" id="tab-ranges-collections" style="display:none;">
			<p>
				Specify which ranges and/or collections this product should appear in.
			</p>
			<p>
				<strong>Ranges</strong>
			</p>
			<select name="ranges[]" class="ranges" multiple="multiple" style="width:100%">
			<?php

				echo '<option value="Thingy1">Thingy1</option>';
				echo '<option value="Thingy2">Thingy2</option>';
				echo '<option value="Thingy3">Thingy3</option>';
				echo '<option value="Thingy4">Thingy4</option>';
				echo '<option value="Thingy5">Thingy5</option>';
				echo '<option value="Thingy6">Thingy6</option>';
				echo '<option value="Thingy7">Thingy7</option>';

			?>
			</select>

			<p>
				<strong>Collections</strong>
			</p>
			<select name="collections[]" class="collections" multiple="multiple" style="width:100%">
			<?php

				echo '<option value="Thingy1">Thingy1</option>';
				echo '<option value="Thingy2">Thingy2</option>';
				echo '<option value="Thingy3">Thingy3</option>';
				echo '<option value="Thingy4">Thingy4</option>';
				echo '<option value="Thingy5">Thingy5</option>';
				echo '<option value="Thingy6">Thingy6</option>';
				echo '<option value="Thingy7">Thingy7</option>';

			?>
			</select>
		</div>

		<div class="tab page seo" id="tab-seo" style="display:none;">
			<p>
				Define some meta information here which will help search engines understand the product. Keep it relevant and
				concise, trying too hard and 'keyword flooding' can have the opposite effect.
			</p>
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

		_CREATE	= new NAILS_Admin_Shop_Inventory_Add_Edit();
		_CREATE.init( '_CREATE', <?=json_encode( $product_types )?>, '<?=$this->cdn->generate_api_upload_token( active_user( 'id' ) ) ?>' );

	});
</script>

<script type="text/template" id="template-variation">
<?php

	$_data		= array( 'is_first' => FALSE );

	$this->load->view( 'admin/shop/inventory/utilities/template-mustache-inventory-variant', $_data );

?>
</script>

<div id="dialog-confirm-delete-variation" title="Confirm Delete" style="display:none;">
	<p>
		<span class="ui-icon ui-icon-alert" style="float: left; margin: 0 7px 0 0;"></span>
		This variation will be removed from the interface and cannot be recovered.
		<strong>Are you sure?</strong>
	</p>
</div>

<div id="dialog-confirm-delete-image" title="Confirm Delete" style="display:none;">
	<p>
		<span class="ui-icon ui-icon-alert" style="float: left; margin: 0 7px 0 0;"></span>
		This image will be removed from the interface and cannot be recovered.
		<strong>Are you sure?</strong>
	</p>
</div>

<script type="text/template" id="template-uploadify">
	<li class="gallery-item uploadify-queue-item" id="${fileID}" data-instance_id="${instanceID}" data-file_id="${fileID}">
		<a href="#" data-instance_id="${instanceID}" data-file_id="${fileID}" class="remove"></a>
		<div class="progress" style="height:0%"></div>
		<div class="data data-cancel">CANCELLED</div>
	</li>
</script>

<script type="text/template" id="template-gallery-item">
	<li class="gallery-item crunching">
		<div class="crunching"></div>
		<?=form_hidden( 'gallery[]' )?>
	</li>
</script>

<script type="text/template" id="template-attribute">
	<tr class="attribute">
		<td class="attribute">
			<?php

				$_options	= array();
				$_options[]	= '';
				$_options[1231]	= 'something or other';
				$_options[4562]	= 'Something else';
				$_options[7893]	= 'Another thing';
				$_options[1234]	= 'something or other';
				$_options[4565]	= 'Something else';
				$_options[7895]	= 'Another thing';
				$_options[1236]	= 'something or other';
				$_options[4567]	= 'Something else';
				$_options[7898]	= 'Another thing';

				$_selected	= NULL;

				echo form_dropdown( 'attribute[0][attribute]', $_options, $_selected	, 'class="attributes"' );

			?>
		</td>
		<td class="value">
			<?=form_input( 'attribute[0][attribute]', '', 'placeholder="Specify the value"' )?>
		</td>
		<td class="delete">
			<a href="#" class="delete">Delete</a>
		</td>
	</tr>
</script>

<div id="help-variation-examples" style="display:none;">
	<h1>Variations Explained</h1>
	<p>
		When the same product can be sold in multiple styles, colours
		or sizes then a variation should be created for each item. This
		allows the system to keep track of the different variants of items
		which have been sold. Each product must contain at least one variation
		(i.e the first variant is the original product).
	</p>
	<p>
		Additionally, when images are uploaded you can specify if a variant appears in
		the image - this allows the front end shop to only show relevant images when the
		shopper changes the variant they want.
	</p>
	<p>
		Remember that how you use variations is not set in stone, it's purely a system
		to give the shopper a choice from within a single product, rather than going back
		and forth between multiple products.
	</p>
	<h2>Examples</h2>
	<p>
		The following examples are designed to help you choose how you might classify items,
		remember, ultimately it's your own decision as to how items are grouped and should seem
		natural as a shopper.
	</p>
	<h3>Clothing</h3>
	<p>
		Because clothing can come in both a size and a colour it can be confusing as to how to
		properly distingush between them. We suggest that you group these together like so:
	</p>
	<ul>
		<li><strong>Product: Ladies Jeans</strong></li>
		<li>Variant: Black - Size 8</li>
		<li>Variant: Black - Size 10</li>
		<li>Variant: Black - Size 12</li>
		<li>Variant: Red - Size 8</li>
		<li>Variant: Red - Size 10</li>
		<li>Variant: Red - Size 12</li>
	</ul>
	<p>
		This way, the system knows exactly how many pairs of red ladies jeans there are in a size 8,
		etc. Also, each variation can have it's own SKU which can help warehouse operators correctly
		find the right items. Furthermore, when ordered alphabetically the products which you'd naturally
		expect to be together are grouped nicely.
	</p>
	<h3>Books</h3>
	<p>
		Books, do not come in sizes and colours like clothing, however they do have alternate versions:
		paperback and hardback.
	</p>
	<p>
		These versions also affect the price, which is why versions can define their own price point. In
		the same vein, hardback books can be heavier which'll affect shipping costs.
	</p>
	<ul>
		<li><strong>Product: Harry Potter and the Philosopher's Stone</strong></li>
		<li>Variant: Paperback, £11.99</li>
		<li>Variant: Hardback, £16.99</li>
		<li>Variant: Signed Copy, £20.99, only 1 copy available</li>
		<li>Variant: Special Edition - free wand, £17.99</li>
	</ul>
	<p>
		As you can see the same book can be sold in different formats. Each variant can also define it's own meta information, such as ISBN.
	</p>
	<p>
		<strong>Please note:</strong> a different <em>edition</em> of the book is a different product and should be sold as such.
	</p>
</div>

