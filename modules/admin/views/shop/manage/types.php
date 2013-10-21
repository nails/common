<div class="group-shop manage types">
	<?php

		$_is_fancybox = $this->input->get( 'is_fancybox' ) ? '?is_fancybox=1' : '';

		// --------------------------------------------------------------------------

		if ( $_is_fancybox ) :

			echo '<h1>' . $page->title . '</h1>';

		endif;

	?>
	<p class="system-alert no-close">
		Product types control how the order is processed when a user completes checkout and payment
		is authorised. Most products can simply be considered a generic product, however, there are
		some cases when a product should be processed differently (e.g a download requires links to
		be generated). For the most part product types will be defined by the developer, however
		you may create your own for the sake of organisation in admin.
	</p>
	<ul class="tabs">
		<li class="tab <?=empty( $show_tab ) || $show_tab == 'overview' || $show_tab == 'edit' ? 'active' : ''?>">
			<a href="#" data-tab="overview">Overview</a>
		</li>
		<li class="tab <?=! empty( $show_tab ) && $show_tab == 'create' ? 'active' : ''?>">
			<a href="#" data-tab="create">Create Product Type</a>
		</li>
	</ul>
	<section class="tabs pages">
		<div class="tab page <?=empty( $show_tab ) || $show_tab == 'overview' || $show_tab == 'edit' ? 'active' : ''?>" id="overview">
			<table>
				<thead>
					<tr>
						<th class="label">Label &amp; Description</th>
						<th class="ipn">IPN Method</th>
						<th class="physical">Physical</th>
						<th class="max-po">Max Per Order</th>
						<th class="max-v">Max Variations</th>
						<th class="count">Products</th>
						<th class="modified">Modified</th>
						<th class="actions">Actions</th>
					</tr>
				</thead>
				<tbody>
				<?php

					foreach( $types AS $type ) :

						echo '<tr>';
						echo '<td class="label">';
						echo $type->label;
						echo $type->description ? '<small>' . $type->description . '</small>' : '';
						echo '</td>';
						echo '<td class="ipn">';
						echo $type->ipn_method ? '<code>' . $type->ipn_method . '()</code>' : '<span class="no-data">&mdash;</span>';
						echo '</td>';
						echo '<td class="physical">';
						echo $type->is_physical ? '<span class="yes">' . lang( 'yes' ) . '</span>' : '<span class="No">' . lang( 'no' ) . '</span>';
						echo '</td>';
						echo '<td class="max-po">';
						echo $type->max_per_order ? $type->max_per_order : 'Unlimited';
						echo '</td>';
						echo '<td class="max-v">';
						echo $type->max_variations ? $type->max_variations : 'Unlimited';
						echo '</td>';
						echo '<td class="count">' . $type->product_count . '</td>';
						echo $this->load->view( '_utilities/table-cell-datetime', array( 'datetime' => $type->modified ), TRUE );
						echo '<td class="actions">';
						echo '<a href="#edit-' . $type->id . '" class="fancybox awesome small">' . lang( 'action_edit' ) . '</a>';

						echo form_open( 'admin/shop/manage/types' . $_is_fancybox, 'class="delete"' );
						echo form_hidden( 'action', 'delete' );
						echo form_hidden( 'id', $type->id );
						echo form_submit( 'submit', lang( 'action_delete' ), 'class="awesome red small confirm"' );
						echo form_close();

						echo '</td>';
						echo '</tr>';

					endforeach;

				?>
				</tbody>
			</table>
		</div>

		<div class="tab page <?=! empty( $show_tab ) && $show_tab == 'create' ? 'active' : ''?>" id="create">
			<?php

				echo form_open( 'admin/shop/manage/types' . $_is_fancybox );
				echo form_hidden( 'action', 'create' );

				$_field				= array();
				$_field['key']		= 'label';
				$_field['label']	= 'Label';

				echo form_field( $_field );

				// --------------------------------------------------------------------------

				$_field				= array();
				$_field['key']		= 'description';
				$_field['label']	= 'Description';
				$_field['type']		= 'textarea';

				echo form_field( $_field );

				// --------------------------------------------------------------------------

				$_field				= array();
				$_field['key']		= 'is_physical';
				$_field['label']	= 'Is Physical';
				$_field['text_on']	= strtoupper( lang( 'yes' ) );
				$_field['text_off']	= strtoupper( lang( 'no' ) );
				$_field['default']	= TRUE;

				echo form_field_boolean( $_field );

				// --------------------------------------------------------------------------

				$_field				= array();
				$_field['key']		= 'ipn_method';
				$_field['label']	= 'IPN Method';

				echo form_field( $_field );

				// --------------------------------------------------------------------------

				$_field				= array();
				$_field['key']		= 'max_per_order';
				$_field['label']	= 'Max Per Order';

				echo form_field( $_field );

				// --------------------------------------------------------------------------

				$_field				= array();
				$_field['key']		= 'max_variations';
				$_field['label']	= 'Max Variations';

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

	foreach( $types AS $type ) :

		echo '<div id="edit-' . $type->id . '" style="display:none">';
		echo form_open( 'admin/shop/manage/types' . $_is_fancybox );
		echo form_hidden( 'action', 'edit' );
		echo form_hidden( 'id', $type->id );

		$_field				= array();
		$_field['key']		= $type->id . '[label]';
		$_field['label']	= 'Label';
		$_field['default']	= $type->label;

		echo form_field( $_field );

		// --------------------------------------------------------------------------

		$_field				= array();
		$_field['key']		= $type->id . '[description]';
		$_field['label']	= 'Description';
		$_field['type']		= 'textarea';
		$_field['default']	= $type->description;

		echo form_field( $_field );

		// --------------------------------------------------------------------------

		$_field				= array();
		$_field['key']		= $type->id . '[is_physical]';
		$_field['label']	= 'Is Physical';
		$_field['text_on']	= strtoupper( lang( 'yes' ) );
		$_field['text_off']	= strtoupper( lang( 'no' ) );
		$_field['default']	= $type->is_physical;

		echo form_field_boolean( $_field );

		// --------------------------------------------------------------------------

		$_field				= array();
		$_field['key']		= $type->id . '[ipn_method]';
		$_field['label']	= 'IPN Method';
		$_field['default']	= $type->ipn_method;

		echo form_field( $_field );

		// --------------------------------------------------------------------------

		$_field				= array();
		$_field['key']		= $type->id . '[max_per_order]';
		$_field['label']	= 'Max Per Order';
		$_field['default']	= $type->max_per_order;

		echo form_field( $_field );

		// --------------------------------------------------------------------------

		$_field				= array();
		$_field['key']		= $type->id . '[max_variations]';
		$_field['label']	= 'Max Variations';
		$_field['default']	= $type->max_variations;

		echo form_field( $_field );

		// --------------------------------------------------------------------------

		echo form_field_submit( lang( 'action_save_changes' ), 'submit', 'class="awesome"' );

		echo form_close();
		echo '</div>';

	endforeach;


	// --------------------------------------------------------------------------

	//	Set JS
	echo '<script type="text/javascript">';
	if ( validation_errors() && $this->input->post( 'action' ) == 'edit' ) :

		echo '$.fancybox.open({href:"#edit-' . $this->input->post( 'id' ) . '"});';

	endif;

	//	Set _DATA
	echo 'var _DATA = ' . json_encode( $types ) . ';';
	echo '</script>';

?>