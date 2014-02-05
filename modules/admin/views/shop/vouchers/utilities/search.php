<div class="search">
	<div class="mask"><?=img( NAILS_ASSETS_URL . 'img/loader/30px-TRANS.gif' )?></div>
	<?php

		$_form = array(
			'method'	=> 'GET'
		);
		echo form_open( NULL, $_form );

		echo '<div class="search-text">';
		echo form_input( 'search', $this->input->get( 'search' ), 'autocomplete="off" placeholder="' . lang( 'admin_search_placeholder' ) . '"' );
		echo '</div>';

		// --------------------------------------------------------------------------

		$_sort = array();
		foreach ( $sortfields AS $field ) :

			$_sort[$field['col']] = $field['label'];

		endforeach;

		echo lang( 'admin_search_sort' ) . form_dropdown( 'sort', $_sort, $search->sort );

		// --------------------------------------------------------------------------

		$_order = array(
			'asc'	=> 'Ascending',
			'desc'	=> 'Descending'
		);
		echo lang( 'admin_search_order_1' ) . form_dropdown( 'order', $_order, $search->order ) . lang( 'admin_search_order_2' );

		// --------------------------------------------------------------------------

		$_perpage = array(
			10 => 10,
			25 => 25,
			50 => 50,
			75 => 75,
			100 => 100
		);
		echo form_dropdown( 'per_page', $_perpage, $search->per_page );
		echo lang( 'admin_search_per_page' );

		// --------------------------------------------------------------------------

		echo '<hr />';

		echo 'Type:';
		echo '<label>';
		echo form_checkbox( 'show[normal]', TRUE, ( isset( $search->show['normal'] ) && $search->show['normal'] ) );
		echo 'Normal';
		echo '</label>';

		echo '<label>';
		echo form_checkbox( 'show[limited_use]', TRUE, ( isset( $search->show['limited_use'] ) && $search->show['limited_use'] ) );
		echo 'Limited Use';
		echo '</label>';

		echo '<label>';
		echo form_checkbox( 'show[gift_card]', TRUE, ( isset( $search->show['gift_card'] ) && $search->show['gift_card'] ) );
		echo 'Gift Card';
		echo '</label>';

		echo anchor( uri_string() . '?reset=true', lang( 'action_reset' ), 'class="awesome small right"' );
		echo form_submit( 'submit', lang( 'action_search' ), 'class="awesome small right"' );


		// --------------------------------------------------------------------------

		echo form_close();

	?>
</div>

<hr />