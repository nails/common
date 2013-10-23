<div class="search">
	<div class="mask"><?=img( NAILS_URL . 'img/loader/30px-TRANS.gif' )?></div>
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

		echo 'Status:';
		echo '<label>';
		echo form_checkbox( 'show[paid]', TRUE, ( isset( $search->show['paid'] ) && $search->show['paid'] ) );
		echo 'Paid';
		echo '</label>';

		echo '<label>';
		echo form_checkbox( 'show[unpaid]', TRUE, ( isset( $search->show['unpaid'] ) && $search->show['unpaid'] ) );
		echo 'Unpaid';
		echo '</label>';

		echo '<label>';
		echo form_checkbox( 'show[abandoned]', TRUE, ( isset( $search->show['abandoned'] ) && $search->show['abandoned'] ) );
		echo 'Abandoned';
		echo '</label>';

		echo '<label>';
		echo form_checkbox( 'show[cancelled]', TRUE, ( isset( $search->show['cancelled'] ) && $search->show['cancelled'] ) );
		echo 'Cancelled';
		echo '</label>';

		echo '<label>';
		echo form_checkbox( 'show[failed]', TRUE, ( isset( $search->show['failed'] ) && $search->show['failed'] ) );
		echo 'Failed';
		echo '</label>';

		echo '<span style="margin-left:10px;">Fulfilled:</span>';
		echo '<label>';
		echo form_checkbox( 'fulfilled[fulfilled]', TRUE, ( isset( $search->fulfilled['fulfilled'] ) && $search->fulfilled['fulfilled'] ) );
		echo 'Yes';
		echo '</label>';

		echo '<label>';
		echo form_checkbox( 'fulfilled[unfulfilled]', TRUE, ( isset( $search->fulfilled['unfulfilled'] ) && $search->fulfilled['unfulfilled'] ) );
		echo 'No';
		echo '</label>';


		echo anchor( uri_string() . '?reset=true', lang( 'action_reset' ), 'class="awesome small right"' );
		echo form_submit( 'submit', lang( 'action_search' ), 'class="awesome small right"' );


		// --------------------------------------------------------------------------

		echo form_close();

	?>
</div>

<hr />