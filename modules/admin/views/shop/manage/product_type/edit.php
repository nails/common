<div class="group-shop manage product-type edit">
	<?php

		if ( $is_fancybox ) :

			echo '<h1>' . $page->title . '</h1>';
			$_class = 'system-alert';

		else :

			$_class = '';

		endif;

		echo form_open( uri_string() . $is_fancybox );

	?>
	<p class="<?=$_class?>">
		Product types control how the order is processed when a user completes checkout and
		payment is authorised. Most products can simply be considered a generic product, however,
		there are some cases when a product should be processed differently (e.g a download
		requires links to be generated). For the most part product types will be defined by
		the developer, however you may create your own for the sake of organisation in admin.
	</p>
	<?=$is_fancybox ? '' : '<hr />'?>
	<ul class="tabs disabled">
		<li class="tab">
			<?=anchor( 'admin/shop/manage/product_type' . $is_fancybox, 'Overview', 'class="confirm" data-title="Are you sure?" data-body="Any unsaved changes will be lost."' )?>
		</li>
		<li class="tab active">
			<?=anchor( 'admin/shop/manage/product_type/create' . $is_fancybox, 'Create Product Type' )?>
		</li>
	</ul>
	<section class="tabs pages">
		<div class="tab page active">
			<fieldset>
				<legend>Basic Details</legend>
				<p>
					These fields describe the product type.
				</p>
				<?php

					// --------------------------------------------------------------------------

					$_field					= array();
					$_field['key']			= 'label';
					$_field['label']		= 'Label';
					$_field['required']		= TRUE;
					$_field['default']		= isset( $product_type->label ) ? $product_type->label : '';
					$_field['placeholder']	= 'The name of this type of product, e.g. Books.';

					echo form_field( $_field );

					// --------------------------------------------------------------------------

					$_field					= array();
					$_field['key']			= 'description';
					$_field['label']		= 'Description';
					$_field['type']			= 'textarea';
					$_field['placeholder']	= 'Describe the product type clearly, mainly for the benefit of other admins.';
					$_field['default']		= isset( $product_type->description ) ? $product_type->description : '';

					echo form_field( $_field );

					// --------------------------------------------------------------------------

					$_field					= array();
					$_field['key']			= 'is_physical';
					$_field['label']		= 'Is Physical';
					$_field['default']		= isset( $product_type->is_physical ) ? $product_type->is_physical : TRUE;

					echo form_field_boolean( $_field );

				?>
			</fieldset>
			<fieldset>
				<legend>Search Engine Optimisation</legend>
				<p>
					These fields help describe the product type to search engines. These fields won't be seen publicly.
				</p>
				<?php

					$_field					= array();
					$_field['key']			= 'seo_title';
					$_field['label']		= 'SEO Title';
					$_field['sub_label']	= 'Max. 150 characters';
					$_field['placeholder']	= 'An alternative, SEO specific title for the product type.';
					$_field['default']		= isset( $product_type->seo_title ) ? $product_type->seo_title : '';

					echo form_field( $_field );

					// --------------------------------------------------------------------------

					$_field					= array();
					$_field['key']			= 'seo_description';
					$_field['label']		= 'SEO Description';
					$_field['sub_label']	= 'Max. 300 characters';
					$_field['type']			= 'textarea';
					$_field['placeholder']	= 'This text will be read by search engines when they\'re indexing the page. Keep this short and concise.';
					$_field['default']		= isset( $product_type->seo_description ) ? $product_type->seo_description : '';

					echo form_field( $_field );

					// --------------------------------------------------------------------------

					$_field					= array();
					$_field['key']			= 'seo_keywords';
					$_field['label']		= 'SEO Keywords';
					$_field['sub_label']	= 'Max. 150 characters';
					$_field['placeholder']	= 'These comma separated keywords help search engines understand the context of the page; stick to 5-10 words.';
					$_field['default']		= isset( $product_type->seo_keywords ) ? $product_type->seo_keywords : '';

					echo form_field( $_field );

				?>
			</fieldset>
			<fieldset>
				<legend>Adavcned Configurations</legend>
				<p class="system-alert message">
					These fields provide granular control over the product type's behaviour. Setting or changing
					these values will alter the way the shop behaves.
					<br /><strong>Use with extreme caution</strong>.
				</p>
				<?php

					// --------------------------------------------------------------------------

					$_field					= array();
					$_field['key']			= 'max_per_order';
					$_field['label']		= 'Max Per Order';
					$_field['required']		= TRUE;
					$_field['default']		= isset( $product_type->max_per_order ) ? $product_type->max_per_order : '';
					$_field['placeholder']	= 'Maximum number of times this particular product can be added to the basket. Specify 0 for unlimited.';

					echo form_field( $_field, 'Limit the number of times an individual product can be added to an order. This only applies to a single product, i.e. an item with a limit of 1 can only be added once, but multiple (different) products of the same type can be added, but only once each. Specify 0 for unlimited.' );

					// --------------------------------------------------------------------------

					$_field					= array();
					$_field['key']			= 'max_variations';
					$_field['label']		= 'Max Variations';
					$_field['placeholder']	= 'The maximum number of variations this type of product can have. Specify 0 for unlimited.';
					$_field['default']		= isset( $product_type->max_variations ) ? $product_type->max_variations : '';

					echo form_field( $_field, 'Define the number of variations this product can have. Specify 0 for unlimited variations.' );

					// --------------------------------------------------------------------------

					$_field					= array();
					$_field['key']			= 'ipn_method';
					$_field['label']		= 'IPN Method';
					$_field['placeholder']	= 'The IPN method to call upon notification of successfull payment.';
					$_field['default']		= isset( $product_type->ipn_method ) ? $product_type->ipn_method : '';

					echo form_field( $_field, 'This method should be callable within the scope of `shop_order_model`. Do not include the `_process` method name prefix here.' );

				?>
			</fieldset>
			<p style="margin-top:1em;">
				<?=form_submit( 'submit', 'Save', 'class="awesome"' )?>
				<?=anchor( 'admin/shop/manage/product_type' . $is_fancybox, 'Cancel', 'class="awesome red confirm" data-title="Are you sure?" data-body="All unsaved changes will be lost."' )?>
			</p>
		</div>
	</section>
	<?=form_close();?>
</div>
<?php

	$this->load->view( 'admin/shop/manage/product_type/_footer' );