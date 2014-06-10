<div class="group-shop manage collections">
	<?php

		$_is_fancybox = $this->input->get( 'is_fancybox' ) ? '?is_fancybox=1' : '';

		// --------------------------------------------------------------------------

		if ( $_is_fancybox ) :

			echo '<h1>' . $page->title . '</h1>';

		endif;

	?>
	<p class="system-alert no-close">
		Manage which collections are available for your products. Products grouped together into a collection are deemed related and can have their own customised landing page.
	</p>
	<ul class="tabs">
		<li class="tab <?=empty( $show_tab ) || $show_tab == 'overview' || $show_tab == 'edit' ? 'active' : ''?>">
			<a href="#" data-tab="overview">Overview</a>
		</li>
		<li class="tab <?=! empty( $show_tab ) && $show_tab == 'create' ? 'active' : ''?>">
			<a href="#" data-tab="create">Create collection</a>
		</li>
	</ul>
	<section class="tabs pages">
		<div class="tab page <?=empty( $show_tab ) || $show_tab == 'overview' || $show_tab == 'edit' ? 'active' : ''?>" id="overview">
			<table>
				<thead>
					<tr>
						<th class="label">Label &amp; Description</th>
						<th class="count">Products</th>
						<th class="active">Active</th>
						<th class="modified">Modified</th>
						<th class="actions">Actions</th>
					</tr>
				</thead>
				<tbody>
				<?php

					foreach( $collections AS $collection ) :

						echo '<tr>';
						echo '<td class="label">';
						echo $collection->label;
						echo $collection->description ? '<small>' . $collection->description . '</small>' : '';
						echo '</td>';
						echo '<td class="count">' . $collection->product_count . '</td>';

						if ( $collection->is_active ) :

							echo '<td class="active success">';
								echo '<span class="ion-checkmark-circled"></span>';
							echo '</td>';

						else :

							echo '<td class="active error">';
								echo '<span class="ion-close-circled"></span>';
							echo '</td>';

						endif;

						echo $this->load->view( '_utilities/table-cell-datetime', array( 'datetime' => $collection->modified ), TRUE );
						echo '<td class="actions">';
						echo '<a href="#edit-' . $collection->id . '" class="edit-open awesome small">' . lang( 'action_edit' ) . '</a>';

						echo form_open( 'admin/shop/manage/collections' . $_is_fancybox, 'class="delete"' );
						echo form_hidden( 'action', 'delete' );
						echo form_hidden( 'id', $collection->id );
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

				echo form_open( 'admin/shop/manage/collections' . $_is_fancybox );
				echo form_hidden( 'action', 'create' );

				$_field					= array();
				$_field['key']			= 'label';
				$_field['label']		= 'Label';
				$_field['required']		= TRUE;
				$_field['placeholder']	= 'The collection\'s label';

				echo form_field( $_field );

				// --------------------------------------------------------------------------

				$_field					= array();
				$_field['key']			= 'description';
				$_field['label']		= 'Description';
				$_field['type']			= 'textarea';
				$_field['placeholder']	= 'The collection\'s description';

				echo form_field( $_field );

				// --------------------------------------------------------------------------

				$_field					= array();
				$_field['key']			= 'seo_title';
				$_field['label']		= 'SEO Title';
				$_field['placeholder']	= 'An alternative, SEO specific title for the collection.';

				echo form_field( $_field );

				// --------------------------------------------------------------------------

				$_field					= array();
				$_field['key']			= 'seo_description';
				$_field['label']		= 'SEO Description';
				$_field['type']			= 'textarea';
				$_field['placeholder']	= 'This text will be read by search engines when they\'re indexing the page. Keep this short and concise.';

				echo form_field( $_field );

				// --------------------------------------------------------------------------

				$_field					= array();
				$_field['key']			= 'seo_keywords';
				$_field['label']		= 'SEO Keywords';
				$_field['placeholder']	= 'These comma separated keywords help search engines understand the context of the page; stick to 5-10 words.';

				echo form_field( $_field );

				// --------------------------------------------------------------------------

				$_field				= array();
				$_field['key']		= 'is_active';
				$_field['label']	= 'Active On Site';
				$_field['default']	= TRUE;

				echo form_field_boolean( $_field );

				// --------------------------------------------------------------------------

				echo form_field_submit( lang( 'action_save' ), 'submit', 'class="awesome"' );

				// --------------------------------------------------------------------------

				echo form_close();

			?>
		</div>
	</section>
</div>
<?php

	foreach( $collections AS $collection ) :

		echo '<div id="edit-' . $collection->id . '" style="display:none">';
		echo '<div class="fieldset">';

		echo form_open( 'admin/shop/manage/collections' . $_is_fancybox );
		echo form_hidden( 'action', 'edit' );
		echo form_hidden( 'id', $collection->id );

		$_field					= array();
		$_field['key']			= $collection->id . '[label]';
		$_field['label']		= 'Label';
		$_field['default']		= $collection->label;
		$_field['required']		= TRUE;
		$_field['placeholder']	= 'The collection\'s label';

		echo form_field( $_field );

		// --------------------------------------------------------------------------

		$_field					= array();
		$_field['key']			= $collection->id . '[description]';
		$_field['label']		= 'Description';
		$_field['type']			= 'textarea';
		$_field['default']		= $collection->description;
		$_field['placeholder']	= 'The collection\'s description';

		echo form_field( $_field );

		// --------------------------------------------------------------------------

		$_field					= array();
		$_field['key']			= $collection->id . '[seo_title]';
		$_field['label']		= 'SEO Title';
		$_field['default']		= $collection->seo_title;
		$_field['placeholder']	= 'An alternative, SEO specific title for the collection.';

		echo form_field( $_field );

		// --------------------------------------------------------------------------

		$_field					= array();
		$_field['key']			= $collection->id . '[seo_description]';
		$_field['label']		= 'SEO Description';
		$_field['default']		= $collection->seo_description;
		$_field['type']			= 'textarea';
		$_field['placeholder']	= 'An SEO optimised description, optional';

		echo form_field( $_field );

		// --------------------------------------------------------------------------

		$_field					= array();
		$_field['key']			= $collection->id . '[seo_keywords]';
		$_field['label']		= 'SEO Keywords';
		$_field['default']		= $collection->seo_keywords;
		$_field['placeholder']	= 'This text will be read by search engines when they\'re indexing the page. Keep this short and concise.';

		echo form_field( $_field );

		// --------------------------------------------------------------------------

		$_field				= array();
		$_field['key']		= $collection->id . '[is_active]';
		$_field['label']	= 'Active On Site';
		$_field['default']	= $collection->is_active;

		echo form_field_boolean( $_field );

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
	foreach ( $collections AS &$collection ) :

		$collection->label  = $collection->label;
		$collection->label .= trim( $collection->description ) ? ' - ' . word_limiter( trim( $collection->description ), 25 ) : '';

		if ( ! $collection->is_active ) :

			$collection->label = '[INACTIVE] ' . $collection->label;

		endif;

	endforeach;
	echo 'var _DATA = ' . json_encode( $collections ) . ';';
	echo '</script>';

?>