<div class="group-shop manage brands edit">
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
		Manage which brands are available for your products.
	</p>
	<?=$is_fancybox ? '' : '<hr />'?>
	<ul class="tabs disabled">
		<li class="tab">
			<?=anchor( 'admin/shop/manage/brand' . $is_fancybox, 'Overview', 'class="confirm" data-title="Are you sure?" data-body="Any unsaved changes will be lost."' )?>
		</li>
		<li class="tab active">
			<?=anchor( 'admin/shop/manage/brand/create' . $is_fancybox, 'Create Brand' )?>
		</li>
	</ul>
	<section class="tabs pages">
		<div class="tab page active">
			<fieldset>
				<legend>Basic Details</legend>
				<p>
					These fields describe the brand.
				</p>
				<?php

					// --------------------------------------------------------------------------

					$_field					= array();
					$_field['key']			= 'label';
					$_field['label']		= 'Label';
					$_field['required']		= TRUE;
					$_field['default']		= isset( $brand->label ) ? $brand->label : '';
					$_field['placeholder']	= 'The label to give your brand';

					echo form_field( $_field );

					// --------------------------------------------------------------------------

					$_field					= array();
					$_field['key']			= 'logo_id';
					$_field['label']		= 'Logo';
					$_field['default']		= isset( $brand->logo_id ) ? $brand->logo_id : '';
					$_field['bucket']		= 'shop-brand-logo';

					echo form_field_mm_image( $_field );

					// --------------------------------------------------------------------------

					$_field					= array();
					$_field['key']			= 'description';
					$_field['label']		= 'Description';
					$_field['type']			= 'textarea';
					$_field['class']		= 'wysiwyg';
					$_field['placeholder']	= 'This text may be used on the brand\'s overview page.';
					$_field['default']		= isset( $brand->description ) ? $brand->description : '';

					echo form_field( $_field );

					// --------------------------------------------------------------------------

					$_field					= array();
					$_field['key']			= 'is_active';
					$_field['label']		= 'Active';
					$_field['default']		= isset( $brand->is_active ) ? $brand->is_active : TRUE;

					echo form_field_boolean( $_field );

				?>
			</fieldset>
			<fieldset>
				<legend>Search Engine Optimisation</legend>
				<p>
					These fields help describe the brand to search engines. These fields won't be seen publicly.
				</p>
				<?php

					$_field					= array();
					$_field['key']			= 'seo_title';
					$_field['label']		= 'SEO Title';
					$_field['sub_label']	= 'Max. 150 characters';
					$_field['placeholder']	= 'An alternative, SEO specific title for the brand.';
					$_field['default']		= isset( $brand->seo_title ) ? $brand->seo_title : '';

					echo form_field( $_field );

					// --------------------------------------------------------------------------

					$_field					= array();
					$_field['key']			= 'seo_description';
					$_field['label']		= 'SEO Description';
					$_field['sub_label']	= 'Max. 300 characters';
					$_field['type']			= 'textarea';
					$_field['placeholder']	= 'This text will be read by search engines when they\'re indexing the page. Keep this short and concise.';
					$_field['default']		= isset( $brand->seo_description ) ? $brand->seo_description : '';

					echo form_field( $_field );

					// --------------------------------------------------------------------------

					$_field					= array();
					$_field['key']			= 'seo_keywords';
					$_field['label']		= 'SEO Keywords';
					$_field['sub_label']	= 'Max. 150 characters';
					$_field['placeholder']	= 'These comma separated keywords help search engines understand the context of the page; stick to 5-10 words.';
					$_field['default']		= isset( $brand->seo_keywords ) ? $brand->seo_keywords : '';

					echo form_field( $_field );

				?>
			</fieldset>
			<p style="margin-top:1em;">
				<?=form_submit( 'submit', 'Save', 'class="awesome"' )?>
				<?=anchor( 'admin/shop/manage/brand' . $is_fancybox, 'Cancel', 'class="awesome red confirm" data-title="Are you sure?" data-body="All unsaved changes will be lost."' )?>
			</p>
		</div>
	</section>
	<?=form_close();?>
</div>
<?php

	$this->load->view( 'admin/shop/manage/brand/_footer' );