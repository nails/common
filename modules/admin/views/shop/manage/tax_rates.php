<div class="group-shop manage rates">
	<?php

		$_is_fancybox = $this->input->get( 'is_fancybox' ) ? '?is_fancybox=1' : '';

		// --------------------------------------------------------------------------

		if ( $_is_fancybox ) :

			echo '<h1>' . $page->title . '</h1>';

		endif;

	?>
	<p class="system-alert no-close">
		Manage which tax rates the shop supports.
	</p>
	<ul class="tabs">
		<li class="tab <?=empty( $show_tab ) || $show_tab == 'overview' || $show_tab == 'edit' ? 'active' : ''?>">
			<a href="#" data-tab="overview">Overview</a>
		</li>
		<li class="tab <?=! empty( $show_tab ) && $show_tab == 'create' ? 'active' : ''?>">
			<a href="#" data-tab="create">Create Tax Rate</a>
		</li>
	</ul>
	<section class="tabs pages">
		<div class="tab page <?=empty( $show_tab ) || $show_tab == 'overview' || $show_tab == 'edit' ? 'active' : ''?>" id="overview">
			<table>
				<thead>
					<tr>
						<th class="label">Label</th>
						<th class="rate">Rate</th>
						<th class="modified">Modified</th>
						<th class="actions">Actions</th>
					</tr>
				</thead>
				<tbody>
				<?php

					foreach( $rates AS $rate ) :

						echo '<tr>';
						echo '<td class="label">';
						echo $rate->label;
						echo '</td>';
						echo '<td class="count">';
						echo $rate->rate * 100 . '%';
						echo '</td>';
						echo $this->load->view( '_utilities/table-cell-datetime', array( 'datetime' => $rate->modified ), TRUE );
						echo '<td class="actions">';
						echo '<a href="#edit-' . $rate->id . '" class="edit-open awesome small">' . lang( 'action_edit' ) . '</a>';

						echo form_open( 'admin/shop/manage/tax_rates' . $_is_fancybox, 'class="delete"' );
						echo form_hidden( 'action', 'delete' );
						echo form_hidden( 'id', $rate->id );
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

				echo form_open( 'admin/shop/manage/tax_rates' . $_is_fancybox );
				echo form_hidden( 'action', 'create' );

				$_field				= array();
				$_field['key']		= 'label';
				$_field['label']	= 'Label';
				$_field['required']	= TRUE;

				echo form_field( $_field );

				// --------------------------------------------------------------------------

				$_field					= array();
				$_field['key']			= 'rate';
				$_field['label']		= 'Rate';
				$_field['sub_label']	= 'Expressed as decimal';

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

	foreach( $rates AS $rate ) :

		echo '<div id="edit-' . $rate->id . '" style="display:none">';
		echo '<div class="fieldset">';

		echo form_open( 'admin/shop/manage/tax_rates' . $_is_fancybox );
		echo form_hidden( 'action', 'edit' );
		echo form_hidden( 'id', $rate->id );

		$_field				= array();
		$_field['key']		= $rate->id . '[label]';
		$_field['label']	= 'Label';
		$_field['default']	= $rate->label;
		$_field['required']	= TRUE;

		echo form_field( $_field );

		// --------------------------------------------------------------------------

		$_field					= array();
		$_field['key']			= $rate->id . '[rate]';
		$_field['label']		= 'Rate';
		$_field['sub_label']	= 'Expressed as decimal';
		$_field['default']		= $rate->rate;

		echo form_field( $_field );

		// --------------------------------------------------------------------------

		echo form_field_submit( lang( 'action_save_changes' ), 'submit', 'class="awesome"' );

		echo form_close();
		echo '</div>';
		echo '</div>';

	endforeach;


	// --------------------------------------------------------------------------

	//	Set JS

	//	Add a dummy "no tax" option
	$_no_rate			= new stdClass();
	$_no_rate->id		= '';
	$_no_rate->label	= 'No Tax';
	$_no_rate->rates	= 0;

	array_unshift( $rates, $_no_rate );

	echo '<script type="text/javascript">';
	if ( validation_errors() && $this->input->post( 'action' ) == 'edit' ) :

		echo '$.fancybox.open({href:"#edit-' . $this->input->post( 'id' ) . '"});';

	endif;

	echo '$( \'a.edit-open\' ).fancybox({width:650, autoSize:false, afterShow : function(){ _nails.add_stripes(); } });';

	//	Set _DATA
	echo 'var _DATA = ' . json_encode( $rates ) . ';';
	echo '</script>';

?>