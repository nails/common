<div class="group-shop manage tags">
	<?php

		$_is_fancybox = $this->input->get( 'is_fancybox' ) ? '?is_fancybox=1' : '';

		// --------------------------------------------------------------------------

		if ( $_is_fancybox ) :

			echo '<h1>' . $page->title . '</h1>';

		endif;

	?>
	<p class="system-alert no-close">
		Manage the which tags are available for your products. Tags help the shop determine related products.
	</p>
	<ul class="tabs">
		<li class="tab <?=empty( $show_tab ) || $show_tab == 'overview' || $show_tab == 'edit' ? 'active' : ''?>">
			<a href="#" data-tab="overview">Overview</a>
		</li>
		<li class="tab <?=! empty( $show_tab ) && $show_tab == 'create' ? 'active' : ''?>">
			<a href="#" data-tab="create">Create Tag</a>
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

					foreach( $tags AS $tag ) :

						echo '<tr>';
						echo '<td class="label">';
						echo $tag->label;
						echo $tag->description ? '<small>' . $tag->description . '</small>' : '';
						echo '</td>';
						echo '<td class="count">' . $tag->product_count . '</td>';
						echo $this->load->view( '_utilities/table-cell-datetime', array( 'datetime' => $tag->modified ), TRUE );
						echo '<td class="actions">';
						echo '<a href="#edit-' . $tag->id . '" class="edit-open awesome small">' . lang( 'action_edit' ) . '</a>';

						echo form_open( 'admin/shop/manage/tags' . $_is_fancybox, 'class="delete"' );
						echo form_hidden( 'action', 'delete' );
						echo form_hidden( 'id', $tag->id );
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

				echo form_open( 'admin/shop/manage/tags' . $_is_fancybox );
				echo form_hidden( 'action', 'create' );

				$_field				= array();
				$_field['key']		= 'label';
				$_field['label']	= 'Label';
				$_field['required']	= TRUE;

				echo form_field( $_field );

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

	foreach( $tags AS $tag ) :

		echo '<div id="edit-' . $tag->id . '" style="display:none">';
		echo '<div class="fieldset">';

		echo form_open( 'admin/shop/manage/tags' . $_is_fancybox );
		echo form_hidden( 'action', 'edit' );
		echo form_hidden( 'id', $tag->id );

		$_field				= array();
		$_field['key']		= $tag->id . '[label]';
		$_field['label']	= 'Label';
		$_field['default']	= $tag->label;
		$_field['required']	= TRUE;

		echo form_field( $_field );

		// --------------------------------------------------------------------------

		$_field				= array();
		$_field['key']		= $tag->id . '[description]';
		$_field['label']	= 'Description';
		$_field['type']		= 'textarea';
		$_field['default']	= $tag->description;

		echo form_field( $_field );

		// --------------------------------------------------------------------------

		$_field				= array();
		$_field['key']		= $tag->id . '[seo_description]';
		$_field['label']	= 'SEO Description';
		$_field['default']	= $tag->seo_description;

		echo form_field( $_field );

		// --------------------------------------------------------------------------

		$_field				= array();
		$_field['key']		= $tag->id . '[seo_keywords]';
		$_field['label']	= 'SEO Keywords';
		$_field['default']	= $tag->seo_keywords;

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
	echo 'var _DATA = ' . json_encode( $tags ) . ';';
	echo '</script>';

?>