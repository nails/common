<div class="group-shop inventory edit">

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

		<div class="tab page basics active fieldset" id="tab-basics">
			<?php

				$_field					= array();
				$_field['key']			= 'type_id';
				$_field['label']		= 'Type';
				$_field['required']		= TRUE;
				$_field['class']		= 'type_id select2';
				$_field['id']			= 'type_id';
				$_field['info']			= '<a href="#" class="manage-types awesome orange small">Manage Product Types</a>';
				$_field['default']		= ! empty( $item->type->id ) ? $item->type->id : NULL;

				if ( count( $product_types_flat ) == 1 ) :

					reset( $product_types_flat );
					$_id = key( $product_types_flat );

					//	Only one product type, no need to render a drop down
					echo '<input type="hidden" name="' . $_field['key'] . '" value="' . $_id . '" class="' . $_field['key'] . '">';

				else :

					echo form_field_dropdown( $_field, $product_types_flat );

				endif;

				// --------------------------------------------------------------------------

				$_field					= array();
				$_field['key']			= 'title';
				$_field['label']		= 'Title';
				$_field['required']		= TRUE;
				$_field['placeholder']	= 'Give this product a title';
				$_field['default']		= ! empty( $item->title ) ? $item->title : '';

				echo form_field( $_field );

				// --------------------------------------------------------------------------

				$_field					= array();
				$_field['key']			= 'is_active';
				$_field['label']		= 'Is Active';
				$_field['default']		= TRUE;
				$_field['text_on']		= strtoupper( lang( 'yes' ) );
				$_field['text_off']		= strtoupper( lang( 'no' ) );
				$_field['default']		= ! empty( $item->is_active ) ? $item->is_active : TRUE;

				echo form_field_boolean( $_field );

				// --------------------------------------------------------------------------

				$_field				= array();
				$_field['key']		= 'brands[]';
				$_field['label']	= 'Brands';
				$_field['class']	= 'brands select2';
				$_field['info']		= '<a href="#" class="manage-brands awesome orange small">Manage Brands</a>';
				$_tip				= 'If this product contains multiple brands (e.g a hamper) specify them all here.';

				//	Defaults
				if ( $this->input->post( 'brands' ) ) :

					$_field['default'] = $this->input->post( 'brands' );

				elseif( ! empty( $item->brands ) ) :

					$_field['default'] = array();

					//	Build an array which matches the potential $_POST array
					foreach( $item->brands AS $brand ) :

						$_field['default'][] = $brand->id;

					endforeach;

				endif;

				echo form_field_dropdown_multiple( $_field, $brands, $_tip );

				// --------------------------------------------------------------------------

				$_field				= array();
				$_field['key']		= 'categories[]';
				$_field['label']	= 'Categories';
				$_field['class']	= 'categories select2';
				$_field['info']		= '<a href="#" class="manage-categories awesome orange small">Manage Categories</a>';
				$_tip				= 'Specify which categories this product falls into.';

				//	Defaults
				if ( $this->input->post( 'categories' ) ) :

					$_field['default'] = $this->input->post( 'categories' );

				elseif( ! empty( $item->categories ) ) :

					$_field['default'] = array();

					//	Build an array which matches the potential $_POST array
					foreach( $item->categories AS $category ) :

						$_field['default'][] = $category->id;

					endforeach;

				endif;

				echo form_field_dropdown_multiple( $_field, $categories, $_tip );

				// --------------------------------------------------------------------------

				$_field				= array();
				$_field['key']		= 'tags[]';
				$_field['label']	= 'Tags';
				$_field['class']	= 'tags select2';
				$_field['info']		= '<a href="#" class="manage-tags awesome orange small">Manage Tags</a>';
				$_tip				= 'Use tags to associate products together, e.g. events.';

				//	Defaults
				if ( $this->input->post( 'tags' ) ) :

					$_field['default'] = $this->input->post( 'tags' );

				elseif( ! empty( $item->tags ) ) :

					$_field['default'] = array();

					//	Build an array which matches the potential $_POST array
					foreach( $item->tags AS $tag ) :

						$_field['default'][] = $tag->id;

					endforeach;

				endif;

				echo form_field_dropdown_multiple( $_field, $tags, $_tip );

				// --------------------------------------------------------------------------

				$_field				= array();
				$_field['key']		= 'tax_rate_id';
				$_field['label']	= 'Tax Rate';
				$_field['class']	= 'tax_rate_id select2';
				$_field['required']	= TRUE;
				$_field['info']		= '<a href="#" class="manage-tax-rates awesome orange small">Manage Tax Rates</a>';
				$_field['default']	= ! empty( $item->tax_rate->id ) ? $item->tax_rate->id : NULL;

				echo form_field_dropdown( $_field, $tax_rates );

			?>
		</div>

		<div class="tab page description" id="tab-description">
		<?php

			$_field				= array();
			$_field['key']		= 'description';
			$_field['default']	= ! empty( $item->description ) ? $item->description : '';


			echo form_error( $_field['key'], '<p class="system-alert error no-close">', '</p>' );
			echo form_textarea( $_field['key'], set_value( $_field['key'], $_field['default'] ), 'class="wysiwyg"' );

		?>
		</div>

		<div class="tab page variations" id="tab-variations">
			<p>
				Variations allow you to offer the same product but with different attributes (e.g colours or sizes).
				Shoppers will be given the choice of which variation they wish to purchase. There must always be at
				least one variation of a product. Confused? <a href="#help-variation-examples" class="fancybox">See some examples</a>.
			</p>
			<div id="product-variations">
				<?php

					//	Data which will be passed to template
					$_data					= array();
					$_data['is_first']		= TRUE;
					$_data['is_php']		= TRUE;
					$_data['counter']		= 0;
					$_data['num_variants']	= 0;

					//	Render, if there's POST then make sure we render it enough times
					//	Otherwise check to see if there's $item data

					if ( $this->input->post( 'variation' ) ) :

						$_variations = $this->input->post( 'variation' );

					elseif( ! empty( $item->variations ) ) :

						$_variations = array();

						//	Build an array which matches the potential $_POST array
						foreach( $item->variations AS $variation ) :

							$_variations[] = $variation;

						endforeach;

					else :

						$_variations = array();

					endif;


					if ( ! empty( $_variations ) ) :

						foreach ( $_variations AS $variation ) :

							$_data['variation']		= $variation;
							$_data['num_variants']	= count( $_variations );

							$_template	= $this->load->view( 'admin/shop/inventory/utilities/template-mustache-inventory-variant', $_data, TRUE );

							echo $this->mustache->render( $_template, $_data );

							$_data['counter']++;
							$_data['is_first'] = FALSE;

						endforeach;

					else :

						$_template	= $this->load->view( 'admin/shop/inventory/utilities/template-mustache-inventory-variant', $_data, TRUE );

						echo $this->mustache->render( $_template, $_data );

					endif;

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

		<div class="tab page gallery" id="tab-gallery" >
			<p>
				Upload images to the product gallery. Once uploaded you can specify which variations are featured on the <a href="#" class="switch-to-variations">variations tab</a>.
				<small>
				<?php

					$_max_upload	= ini_get( 'upload_max_filesize' );
					$_max_upload	= return_bytes( $_max_upload );

					$_max_post		= ini_get( 'post_max_size' );
					$_max_post		= return_bytes( $_max_post );

					$_memory_limit	= ini_get( 'memory_limit' );
					$_memory_limit	= return_bytes( $_memory_limit );

					$_upload_mb		= min( $_max_upload, $_max_post, $_memory_limit );
					$_upload_mb		= format_bytes( $_upload_mb );

					echo 'Images only, max file size is ' . $_upload_mb . '.';

				?>
				</small>
			</p>
			<p>
				<input type="file" id="file_upload" />
			</p>
			<p class="system-alert notice no-close" id="upload-message" style="display:none">
				<strong>Please be patient while files upload.</strong>
				<br />Tabs have been disabled until uploads are complete.
			</p>
			<?php

				//	Render, if there's POST then make sure we render it enough times
				//	Otherwise check to see if there's $item data

				if ( $this->input->post( 'gallery' ) ) :

					$_gallery = $this->input->post( 'gallery' );

				elseif( ! empty( $item->gallery ) ) :

					$_gallery = $item->gallery;

				else :

					$_gallery = array();

				endif;

			?>
			<ul id="gallery-items" class="<?=! empty( $_gallery ) ? '' : 'empty' ?>">
				<li class="empty">
					No images, why not upload some?
				</li>
				<?php

					if ( ! empty( $_gallery ) ) :

						foreach( $_gallery AS $image ) :

							$this->load->view( 'admin/shop/inventory/utilities/template-mustache-gallery-item', array( 'object_id' => $image ) );

						endforeach;

					endif;

				?>
			</ul>
		</div>

		<div class="tab page attributes" id="tab-attributes">
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
					<?php

						//	Render, if there's POST then make sure we render it enough times
						//	Otherwise check to see if there's $item data

						if ( $this->input->post( 'attributes' ) ) :

							$_attributes = $this->input->post( 'attributes' );

						elseif( ! empty( $item->attributes ) ) :

							$_attributes = array();

							//	Build an array which matches the potential $_POST array
							foreach( $item->attributes AS $attribute ) :

								$_temp					= array();
								$_temp['attribute_id']	= $attribute->id;
								$_temp['value']			= $attribute->value;

								$_attributes[] = $_temp;

							endforeach;

						else :

							$_attributes = array();

						endif;

						if ( ! empty( $_attributes ) ) :

							$_counter = 0;
							foreach ( $_attributes AS $attribute ) :

								$_data = array( 'attribute' => $attribute, 'counter' => $_counter );

								$this->load->view( 'admin/shop/inventory/utilities/template-mustache-attribute', $_data );

								$_counter++;

							endforeach;

						endif;

					?>
				</tbody>
			</table>
			<p>
				<a href="#" id="product-attribute-add" class="awesome small green">Add Attribute</a>
				<a href="#" class="awesome small orange manage-attributes">Manage Attributes</a>
			</p>
		</div>

		<div class="tab page ranges-collections" id="tab-ranges-collections">
			<p>
				Specify which ranges and/or collections this product should appear in.
			</p>
			<p>
				A range is an actual line of stock, or a range of products from one of your
				suppliers. For example this might be the 'Jimi Hendrix' range from 'Vintage Rock Tees'.
			</p>
			<p>
				Collections offer you a unique way to combine stock into 'smart' categories,
				for example you might create collections for 'Gifts for Him', 'Gifts for Her',
				'Valentines Day Gifts', 'Stocking Fillers' etc.
			</p>
			<p>
				<strong>Ranges</strong>
			</p>
			<p>
				<select name="ranges[]" class="ranges select2" multiple="multiple" style="width:100%">
				<?php

					//	Render, if there's POST then make sure we render it enough times
					//	Otherwise check to see if there's $item data

					if ( $this->input->post( 'ranges' ) ) :

						$_selected = $this->input->post( 'ranges' );

					elseif( ! empty( $item->ranges ) ) :

						$_selected = array();

						//	Build an array which matches the potential $_POST array
						foreach( $item->ranges AS $range ) :

							$_selected[] = $range->id;

						endforeach;

					else :

						$_selected = array();

					endif;

					foreach ( $ranges AS $range ) :

						$_checked = array_search( $range->id, $_selected ) !== FALSE ? 'selected="selected"' : '';

						echo '<option value="' . $range->id . '" ' . $_checked . '>';
						if ( ! $range->is_active ) :

							echo '[INACTIVE] ';

						endif;
						echo $range->label;
						echo trim( $range->description ) ? ' - ' . word_limiter( trim( $range->description ), 25 ) : '';
						echo '</option>';

					endforeach;

				?>
				</select>
			</p>
			<p>
				<a href="#" class="awesome small orange manage-ranges">Manage Ranges</a>
			</p>

			<hr />

			<p>
				<strong>Collections</strong>
			</p>
			<p>
				<select name="collections[]" class="collections select2" multiple="multiple" style="width:100%">
				<?php

					//	Render, if there's POST then make sure we render it enough times
					//	Otherwise check to see if there's $item data

					if ( $this->input->post( 'collections' ) ) :

						$_selected = $this->input->post( 'collections' );

					elseif( ! empty( $item->collections ) ) :

						$_selected = array();

						//	Build an array which matches the potential $_POST array
						foreach( $item->collections AS $collection ) :

							$_selected[] = $collection->id;

						endforeach;

					else :

						$_selected = array();

					endif;

					foreach ( $collections AS $collection ) :

						$_checked = array_search( $collection->id, $_selected ) !== FALSE ? 'selected="selected"' : '';

						echo '<option value="' . $collection->id . '" ' . $_checked . '>';
						if ( ! $collection->is_active ) :

							echo '[INACTIVE] ';

						endif;
						echo $collection->label;
						echo $collection->description ? ' - ' . word_limiter( $collection->description, 25 ) : '';
						echo '</option>';

					endforeach;

				?>
				</select>
			</p>
			<p>
				<a href="#" class="awesome small orange manage-collections">Manage Collections</a>
			</p>
		</div>

		<div class="tab page seo" id="tab-seo">
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
					$_field['placeholder']	= 'Search Engine Optimised title';
					$_field['default']		= ! empty( $item->seo_title ) ? $item->seo_title : '';

					echo form_field( $_field, 'Keep this below 100 characters' );

					// --------------------------------------------------------------------------

					$_field					= array();
					$_field['key']			= 'seo_description';
					$_field['label']		= 'Description';
					$_field['placeholder']	= 'Search Engine Optimised description';
					$_field['type']			= 'textarea';
					$_field['default']		= ! empty( $item->seo_description ) ? $item->seo_description : '';

					echo form_field( $_field, 'Keep this relevant and below 140 characters' );

					// --------------------------------------------------------------------------

					$_field					= array();
					$_field['key']			= 'seo_keywords';
					$_field['label']		= 'Keywords';
					$_field['placeholder']	= 'Comma separated keywords';
					$_field['default']		= ! empty( $item->seo_keywords ) ? $item->seo_keywords : '';

					echo form_field( $_field, 'Comma seperated keywords. Try to keep to 10 or fewer.' );

				?>
			</fieldset>
		</div>

	</section>
	<p>
	<?php

		$_action = empty( $item->id ) ? lang( 'action_create' ) : lang( 'action_save_changes' );
		echo form_submit( 'submit', $_action );

	?>
	</p>
	<?=form_close()?>
</div>

<script type="text/javascript">
	var _CREATE_EDIT;
	$(function(){

		_CREATE_EDIT	= new NAILS_Admin_Shop_Inventory_Create_Edit();
		_CREATE_EDIT.init(  <?=json_encode( $product_types )?>, '<?=$this->cdn->generate_api_upload_token( active_user( 'id' ) ) ?>' );

	});
</script>

<script type="text/template" id="template-variation">
<?php

	$_data					= array();
	$_data['is_first']		= FALSE;
	$_data['is_php']		= FALSE;
	$_data['counter']		= FALSE;
	$_data['variation']		= NULL;
	$_data['num_variants']	= NULL;

	$this->load->view( 'admin/shop/inventory/utilities/template-mustache-inventory-variant', $_data );

?>
</script>

<div id="dialog-confirm-delete" title="Confirm Delete" style="display:none;">
	<p>
		<span class="ui-icon ui-icon-alert" style="float: left; margin: 0 7px 0 0;"></span>
		This item will be removed from the interface and cannot be recovered.
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
<?php

	$this->load->view( 'admin/shop/inventory/utilities/template-mustache-gallery-item' );

?>
</script>

<script type="text/template" id="template-attribute">
<?php

	$_data = array( 'attribute' => NULL );
	$this->load->view( 'admin/shop/inventory/utilities/template-mustache-attribute', $_data );

?>
</script>

<script type="text/template" id="template-shipping-option">
	<tr class="shipping-option">
		<td class="courier-method">
			<?php

				$_options = array
				(
					1 => 'Option one - method',
					2 => 'Option two - method',
					3 => 'Option three - method',
					4 => 'Option four - method'
				);

				echo form_dropdown( 'variation[{{counter}}][shipping][{{shipping_counter}}][0][courier_method_id]', $_options, NULL, 'class="shipping_methods"');

			?>
		</td>						<td class="price">
			<?php

				echo form_hidden( 'variation[{{counter}}][shipping][{{shipping_counter}}][0][currency_id]', SHOP_BASE_CURRENCY_ID );
				echo form_input( 'variation[{{counter}}][shipping][{{shipping_counter}}][0][price]', NULL, 'data-prefix="' . SHOP_BASE_CURRENCY_SYMBOL . '" placeholder="Price"' );

				//	Other currencies

				$_counter = 1;
				foreach ( $currencies AS $currency ) :

					if ( $currency->id != SHOP_BASE_CURRENCY_ID ) :


						echo form_hidden( 'variation[{{counter}}][shipping][{{shipping_counter}}][' . $_counter . '][currency_id]', $currency->id );
						echo form_input( 'variation[{{counter}}][shipping][{{shipping_counter}}][' . $_counter . '][price]', NULL, 'data-prefix="' . $currency->symbol . '" placeholder="Calculate automatically"' );

						$_counter++;

					endif;

				endforeach;

			?>
		</td>
		<td class="price-additional">
			<?php

				echo form_input( 'variation[{{counter}}][shipping][{{shipping_counter}}][0][price_additional]', NULL, 'data-prefix="' . SHOP_BASE_CURRENCY_SYMBOL . '" placeholder="Price"' );

				//	Other currencies

				$_counter = 1;
				foreach ( $currencies AS $currency ) :

					if ( $currency->id != SHOP_BASE_CURRENCY_ID ) :


						echo form_input( 'variation[{{counter}}][shipping][{{shipping_counter}}][' . $_counter . '][price_additional]', NULL, 'data-prefix="' . $currency->symbol . '" placeholder="Calculate automatically"' );

						$_counter++;

					endif;

				endforeach;

			?>
		</td>
		<td class="delete">
			<a href="#" class="delete-shipping">Delete</a>
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

