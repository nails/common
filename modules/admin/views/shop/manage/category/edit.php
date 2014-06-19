<div class="group-shop manage categories edit">
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
		Manage the shop's categories. Categories are like departments and should be used to organise
		similar products. Additionally, categories can be nested to more granularly organise items.
	</p>
	<?=$is_fancybox ? '' : '<hr />'?>
	<ul class="tabs disabled">
		<li class="tab">
			<?=anchor( 'admin/shop/manage/category' . $is_fancybox, 'Overview', 'class="confirm" data-title="Are you sure?" data-body="Any unsaved changes will be lost."' )?>
		</li>
		<li class="tab active">
			<?=anchor( 'admin/shop/manage/category/create' . $is_fancybox, 'Create Category' )?>
		</li>
	</ul>
	<section class="tabs pages">
		<div class="tab page active">
			<fieldset>
				<legend>Basic Details</legend>
				<p>
					These fields describe the category and help organise it into the shop's category hierarchy.
				</p>
				<?php

					// --------------------------------------------------------------------------

					$_field					= array();
					$_field['key']			= 'label';
					$_field['label']		= 'Label';
					$_field['required']		= TRUE;
					$_field['default']		= isset( $category->label ) ? $category->label : '';
					$_field['placeholder']	= 'The label to give your category';

					echo form_field( $_field );

					// --------------------------------------------------------------------------

					$_field				= array();
					$_field['key']		= 'parent_id';
					$_field['label']	= 'Parent';
					$_field['class']	= 'select2';
					$_field['default']	= isset( $category->parent_id ) ? $category->parent_id : '';

					$_options = array();

					foreach( $categories AS $cat ) :

						//	Category can't have itself as the parent
						if ( isset( $category->id ) && $category->id == $cat->id ) :

							continue;

						endif;

						$_breadcrumbs = array();
						foreach( $cat->breadcrumbs AS $bc ) :

							$_breadcrumbs[] = $bc->label;

						endforeach;

						$_options[$cat->id] = implode( ' &rsaquo; ', $_breadcrumbs );

					endforeach;

					asort( $_options );
					$_options = (array) 'No Parent' + $_options;
					echo form_field_dropdown( $_field, $_options );

					// --------------------------------------------------------------------------

					$_field					= array();
					$_field['key']			= 'description';
					$_field['label']		= 'Description';
					$_field['type']			= 'textarea';
					$_field['class']		= 'wysiwyg';
					$_field['placeholder']	= 'This text may be used on the category\'s overview page.';
					$_field['default']		= isset( $category->description ) ? $category->description : '';

					echo form_field( $_field );

				?>
			</fieldset>
			<fieldset>
				<legend>Search Engine Optimisation</legend>
				<p>
					These fields help describe the category to search engines. These fields won't be seen publicly.
				</p>
				<?php

					$_field					= array();
					$_field['key']			= 'seo_title';
					$_field['label']		= 'SEO Title';
					$_field['sub_label']	= 'Max. 150 characters';
					$_field['placeholder']	= 'An alternative, SEO specific title for the category.';
					$_field['default']		= isset( $category->seo_title ) ? $category->seo_title : '';

					echo form_field( $_field );

					// --------------------------------------------------------------------------

					$_field					= array();
					$_field['key']			= 'seo_description';
					$_field['label']		= 'SEO Description';
					$_field['sub_label']	= 'Max. 300 characters';
					$_field['type']			= 'textarea';
					$_field['placeholder']	= 'This text will be read by search engines when they\'re indexing the page. Keep this short and concise.';
					$_field['default']		= isset( $category->seo_description ) ? $category->seo_description : '';

					echo form_field( $_field );

					// --------------------------------------------------------------------------

					$_field					= array();
					$_field['key']			= 'seo_keywords';
					$_field['label']		= 'SEO Keywords';
					$_field['sub_label']	= 'Max. 150 characters';
					$_field['placeholder']	= 'These comma separated keywords help search engines understand the context of the page; stick to 5-10 words.';
					$_field['default']		= isset( $category->seo_keywords ) ? $category->seo_keywords : '';

					echo form_field( $_field );

				?>
			</fieldset>
			<p style="margin-top:1em;">
				<?=form_submit( 'submit', 'Save', 'class="awesome"' )?>
				<?=anchor( 'admin/shop/manage/category' . $is_fancybox, 'Cancel', 'class="awesome red confirm" data-title="Are you sure?" data-body="All unsaved changes will be lost."' )?>
			</p>
		</div>
	</section>
	<?=form_close();?>
</div>
<?php

	$this->load->view( 'admin/shop/manage/category/_footer' );