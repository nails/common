<div class="group-shop manage categories">
	<?php

		$_is_fancybox = $this->input->get( 'is_fancybox' ) ? '?is_fancybox=1' : '';

		// --------------------------------------------------------------------------

		if ( $_is_fancybox ) :

			echo '<h1>' . $page->title . '</h1>';

		endif;

	?>
	<p class="system-alert no-close">
		Manage the shop's categories. Categories are like departments and should be used to organise
		similar products. Additionally, categories can be nested to more granularly organise items.
	</p>
	<ul class="tabs">
		<li class="tab <?=empty( $show_tab ) || $show_tab == 'overview' || $show_tab == 'edit' ? 'active' : ''?>">
			<a href="#" data-tab="overview">Overview</a>
		</li>
		<li class="tab <?=! empty( $show_tab ) && $show_tab == 'create' ? 'active' : ''?>">
			<a href="#" data-tab="create">Create Category</a>
		</li>
	</ul>
	<section class="tabs pages">
		<div class="tab page <?=empty( $show_tab ) || $show_tab == 'overview' || $show_tab == 'edit' ? 'active' : ''?>" id="overview">
			<table>
				<thead>
					<tr>
						<th class="label">Label &amp; Description</th>
						<th class="count">Products</th>
						<th class="modified">Modified</th>
						<th class="actions">Actions</th>
					</tr>
				</thead>
				<tbody>
				<?php

					foreach( $categories AS $category ) :

						echo '<tr>';
						echo '<td class="label">';
						echo $category->label;

						$_label_nested = explode( '|', $category->label_nested );
						array_pop( $_label_nested );

						echo $_label_nested ? '<small>' . implode( ' &rsaquo; ', $_label_nested ) . '</small>' : '<small>Top Level Category</small>';
						echo $category->description ? '<small>' . $category->description . '</small>' : '';
						echo '</td>';
						echo '<td class="count">' . $category->product_count . '</td>';
						echo $this->load->view( '_utilities/table-cell-datetime', array( 'datetime' => $category->modified ), TRUE );
						echo '<td class="actions">';
						echo '<a href="#edit-' . $category->id . '" class="edit-open awesome small">' . lang( 'action_edit' ) . '</a>';

						echo form_open( 'admin/shop/manage/categories' . $_is_fancybox, 'class="delete"' );
						echo form_hidden( 'action', 'delete' );
						echo form_hidden( 'id', $category->id );
						echo form_submit( 'submit', lang( 'action_delete' ), 'class="awesome red small confirm"' );
						echo form_close();

						echo '</td>';
						echo '</tr>';

					endforeach;

				?>
				</tbody>
			</table>
		</div>

		<div class="tab page fieldset <?=! empty( $show_tab ) && $show_tab == 'create' ? 'active' : ''?>" id="create">
			<?php

				echo form_open( 'admin/shop/manage/categories' . $_is_fancybox );
				echo form_hidden( 'action', 'create' );

				$_field				= array();
				$_field['key']		= 'label';
				$_field['label']	= 'Label';
				$_field['required']	= TRUE;

				echo form_field( $_field );

				// --------------------------------------------------------------------------

				$_field				= array();
				$_field['key']		= 'parent_id';
				$_field['label']	= 'Parent';
				$_field['class']	= 'chosen';

				$_options = array();

				foreach( $categories AS $category ) :

					$_options[$category->id] = str_replace( '|', ' &rsaquo; ', $category->label_nested );

				endforeach;

				asort( $_options );
				$_options = (array) 'No Parent' + $_options;
				echo form_field_dropdown( $_field, $_options );

				// --------------------------------------------------------------------------

				$_field				= array();
				$_field['key']		= 'description';
				$_field['label']	= 'Description';
				$_field['type']		= 'textarea';

				echo form_field( $_field );

				// --------------------------------------------------------------------------

				$_field				= array();
				$_field['key']		= 'seo_description';
				$_field['label']	= 'SEO Description';
				$_field['type']		= 'textarea';

				echo form_field( $_field );

				// --------------------------------------------------------------------------

				$_field				= array();
				$_field['key']		= 'seo_keywords';
				$_field['label']	= 'SEO Keywords';

				echo form_field( $_field );

				// --------------------------------------------------------------------------

				echo form_field_submit( lang( 'action_save' ), 'submit', 'class="awesome"' );

				// --------------------------------------------------------------------------

				echo form_close();

			?>
		</div>
	</section>
</div>
<?php

	foreach( $categories AS $category ) :

		echo '<div id="edit-' . $category->id . '"  style="display:none">';
		echo '<div class="fieldset">';

		echo form_open( 'admin/shop/manage/categories' . $_is_fancybox );
		echo form_hidden( 'action', 'edit' );
		echo form_hidden( 'id', $category->id );

		$_field				= array();
		$_field['key']		= $category->id . '[label]';
		$_field['label']	= 'Label';
		$_field['default']	= $category->label;
		$_field['required']	= TRUE;

		echo form_field( $_field );

		// --------------------------------------------------------------------------

		$_field				= array();
		$_field['key']		= $category->id . '[parent_id]';
		$_field['label']	= 'Parent';
		$_field['class']	= 'chosen';
		$_field['default']	= $category->parent_id;

		echo form_field_dropdown( $_field, $_options );

		// --------------------------------------------------------------------------

		$_field				= array();
		$_field['key']		= $category->id . '[description]';
		$_field['label']	= 'Description';
		$_field['type']		= 'textarea';
		$_field['default']	= $category->description;

		echo form_field( $_field );

		// --------------------------------------------------------------------------

		$_field				= array();
		$_field['key']		= $category->id . '[seo_description]';
		$_field['label']	= 'SEO Description';
		$_field['type']		= 'textarea';
		$_field['default']	= $category->seo_description;

		echo form_field( $_field );

		// --------------------------------------------------------------------------

		$_field				= array();
		$_field['key']		= $category->id . '[seo_keywords]';
		$_field['label']	= 'SEO Keywords';
		$_field['default']	= $category->seo_keywords;

		echo form_field( $_field );

		// --------------------------------------------------------------------------

		echo form_field_submit( lang( 'action_save_changes' ), 'submit', 'class="awesome"' );

		echo form_close();
		echo '</div>';
		echo '</div>';

	endforeach;


	// --------------------------------------------------------------------------

	//	Set JS
	echo '<script type="text/javascript">';
	if ( validation_errors() && $this->input->post( 'action' ) == 'edit' ) :

		echo '$.fancybox.open({href:"#edit-' . $this->input->post( 'id' ) . '"});';

	endif;

	echo '$( \'a.edit-open\' ).fancybox({width:650, autoSize:false, afterShow : function(){ _nails.add_stripes(); } });';

	//	Set _DATA
	echo 'var _DATA = ' . json_encode( $categories ) . ';';
	echo '</script>';

?>