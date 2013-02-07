<div class="search">
	<div class="mask"><?=img( NAILS_URL . '/img/loader/30px-000000-TRANS.gif' )?></div>
	<?php
	
		$_form = array(
			'method'	=> 'GET'
		);
		echo form_open( NULL, $_form );
		
		echo '<div class="search-text">';
		echo form_input( 'search', $this->input->get( 'search' ), 'placeholder="Type your search term and hit enter"' );
		echo '</div>';
		
		// --------------------------------------------------------------------------
		
		$_sort = array(
			'u.id' 			=> 'User ID',
			'um.first_name'	=> 'First Name, Last Name',
			'um.last_name'	=> 'Last Name, First Name',
			'u.email'		=> 'Email'
		);
		
		//	Add any custom search fields
		foreach ( $searchfields AS $field ) :
		
			$_sort[$field['col']] = $field['label'];
		
		endforeach;
		
		echo 'Sort results by ' . form_dropdown( 'sort', $_sort, $search->sort );
		
		// --------------------------------------------------------------------------
		
		$_order = array(
			'asc'	=> 'Ascending',
			'desc'	=> 'Descending'
		);
		echo 'and order results in ' . form_dropdown( 'order', $_order, $search->order ) . 'order, show';
		
		// --------------------------------------------------------------------------
		
		$_perpage = array(
			10 => 10,
			25 => 25,
			50 => 50,
			75 => 75,
			100 => 100
		);
		echo form_dropdown( 'per_page', $_perpage, $search->per_page );
		echo 'per page.';
		
		echo anchor( uri_string() . '?reset=true', 'reset', 'class="awesome small right"' );
		
		// --------------------------------------------------------------------------
		
		echo form_close();
	
	?>
</div>

<hr />