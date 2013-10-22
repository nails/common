<div class="group-shop manage brands">
	<?php

		$_is_fancybox = $this->input->get( 'is_fancybox' ) ? '?is_fancybox=1' : '';

		// --------------------------------------------------------------------------

		if ( $_is_fancybox ) :

			echo '<h1>' . $page->title . '</h1>';

		endif;

	?>
	<p class="system-alert no-close">
		Manage brands recognised by the system. Assigning brands to products allows shoppers
		to more easily find items they might be interested in.
	</p>
	<ul class="tabs">
		<li class="tab <?=empty( $show_tab ) || $show_tab == 'overview' || $show_tab == 'edit' ? 'active' : ''?>">
			<a href="#" data-tab="overview">Overview</a>
		</li>
		<li class="tab <?=! empty( $show_tab ) && $show_tab == 'create' ? 'active' : ''?>">
			<a href="#" data-tab="create">Create Brand</a>
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

					foreach( $brands AS $brand ) :

						echo '<tr>';
						echo '<td class="label">';
						echo $brand->logo_id ? img( cdn_thumb( $brand->logo_id, 32, 32 ) ) : '';
						echo $brand->label;
						echo $brand->description ? '<small>' . $brand->description . '</small>' : '';
						echo '</td>';
						echo '<td class="count">' . $brand->product_count . '</td>';
						echo $this->load->view( '_utilities/table-cell-datetime', array( 'datetime' => $brand->modified ), TRUE );
						echo '<td class="actions">';
						echo '<a href="#edit-' . $brand->id . '" class="edit-open awesome small">' . lang( 'action_edit' ) . '</a>';

						echo form_open( 'admin/shop/manage/brands' . $_is_fancybox, 'class="delete"' );
						echo form_hidden( 'action', 'delete' );
						echo form_hidden( 'id', $brand->id );
						echo form_submit( 'submit', lang( 'action_delete' ), 'class="awesome red small confirm"' );
						echo form_close();

						echo '</td>';
						echo '</tr>';

					endforeach;

				?>
				</tbody>
			</table>
		</div>

		<div class="tab page fieldset  <?=! empty( $show_tab ) && $show_tab == 'create' ? 'active' : ''?>" id="create">
			<?php

				echo form_open( 'admin/shop/manage/brands' . $_is_fancybox );
				echo form_hidden( 'action', 'create' );

				$_field				= array();
				$_field['key']		= 'label';
				$_field['label']	= 'Label';
				$_field['required']	= TRUE;

				echo form_field( $_field );

				// --------------------------------------------------------------------------

				$_field				= array();
				$_field['key']		= 'logo_id';
				$_field['label']	= 'Logo';
				$_field['bucket']	= 'shop-brand-logos';

				echo form_field_mm_image( $_field );

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

	foreach( $brands AS $brand ) :

		echo '<div id="edit-' . $brand->id . '" style="display:none">';
		echo '<div class="fieldset">';

		echo form_open( 'admin/shop/manage/brands' . $_is_fancybox );
		echo form_hidden( 'action', 'edit' );
		echo form_hidden( 'id', $brand->id );

		$_field				= array();
		$_field['key']		= $brand->id . '[label]';
		$_field['label']	= 'Label';
		$_field['default']	= $brand->label;
		$_field['required']	= TRUE;

		echo form_field( $_field );

		// --------------------------------------------------------------------------

		$_field				= array();
		$_field['key']		= $brand->id . '[logo_id]';
		$_field['label']	= 'Logo';
		$_field['bucket']	= 'shop-brand-logos';
		$_field['default']	= $brand->logo_id;

		echo form_field_mm_image( $_field );

		// --------------------------------------------------------------------------

		$_field				= array();
		$_field['key']		= $brand->id . '[description]';
		$_field['label']	= 'Description';
		$_field['type']		= 'textarea';
		$_field['default']	= $brand->description;

		echo form_field( $_field );

		// --------------------------------------------------------------------------

		$_field				= array();
		$_field['key']		= $brand->id . '[seo_description]';
		$_field['label']	= 'SEO Description';
		$_field['default']	= $brand->seo_description;

		echo form_field( $_field );

		// --------------------------------------------------------------------------

		$_field				= array();
		$_field['key']		= $brand->id . '[seo_keywords]';
		$_field['label']	= 'SEO Keywords';
		$_field['default']	= $brand->seo_keywords;

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
	echo 'var _DATA = ' . json_encode( $brands ) . ';';
	echo '</script>';

?>