<div class="search">
	<div class="mask"><?=img( NAILS_URL . 'img/loader/30px-TRANS.gif' )?></div>
	<?php

		$_form = array(
			'method'	=> 'GET'
		);
		echo form_open( NULL, $_form );

		// --------------------------------------------------------------------------

		//	User
		echo '<span class="label">';
			echo '<span class="text">Show only events created by the following user(s):</span>';
			echo '<select multiple="multiple" name="user_id[]" class="chosen user" data-placeholder="Choose some users. Leave blank to include all users.">';

			foreach ( $users AS $user ) :

				$_selected = $this->input->get( 'user_id' ) && array_search( $user->id, $this->input->get( 'user_id' ) ) !== FALSE ? 'selected="selected"' : '';

				echo '<option value="' . $user->id . '" ' . $_selected . '>';
				echo 'ID: ' . $user->id . ' - ';
				echo $user->first_name . ' ' . $user->last_name . ' - ';
				echo $user->email;
				echo '</option>';

			endforeach;

			echo '</select>';
		echo '</span>';

		// --------------------------------------------------------------------------

		//	Event type(s)
		echo '<span class="label">';
			echo '<span class="text">Show only events of type(s):</span>';
			echo '<select multiple="multiple" name="event_type[]" class="chosen type" data-placeholder="Choose some event types. Leave blank to include all event types.">';

			foreach ( $types AS $id => $label ) :

				$_selected = $this->input->get( 'event_type' ) && array_search( $id, $this->input->get( 'event_type' ) ) !== FALSE ? 'selected="selected"' : '';

				echo '<option value="' . $id . '" ' . $_selected . '>';
				echo $label;
				echo '</option>';

			endforeach;

			echo '</select>';
		echo '</span>';

		// --------------------------------------------------------------------------

		echo '<span class="label">';

			//	Date range
			echo '<span class="text">Date range:</span>';
			echo '<input type="text" name="date_from" value="' . $this->input->get( 'date_from' ) . '" class="datetime from" placeholder="YYYY-MM-DD HH:MM:SS"/>';

			echo '<span class="text and">-</span>';
			echo '<input type="text" name="date_to" value="' . $this->input->get( 'date_to' ) . '" class="datetime to" placeholder="YYYY-MM-DD HH:MM:SS"/>';

			//	Sort by
			echo '<span class="text and">sort by </span>';
			$_options					= array();
			$_options['e.created']		= 'Date';
			$_options['u.first_name']	= 'First Name';
			$_options['u.last_name']	= 'Surname';
			$_options['et.label']		= 'Event Type';

			echo form_dropdown( 'sort', $_options, $this->input->get( 'sort' ), 'class="chosen"' );

			//	Order by
			echo '<span class="text and">order by </span>';
			$_options			= array();
			$_options['desc']	= 'Descending';
			$_options['asc']	= 'Ascending';

			echo form_dropdown( 'order', $_options, $this->input->get( 'order' ), 'class="chosen"' );

			//	Per Page
			echo '<span class="text and">show</span>';
			$_options			= array();
			$_options[10]	= 10;
			$_options[25]	= 25;
			$_options[50]	= 50;
			$_options[75]	= 75;
			$_options[100]	= 100;

			echo form_dropdown( 'per_page', $_options, $this->input->get( 'per_page' ), 'class="chosen"' );

			echo '<span class="text and">per page</span>';

		echo '</span>';

		// --------------------------------------------------------------------------

		echo form_submit( 'submit', 'Update Results', 'class="awesome green small"' );
		echo anchor( 'admin/stats', lang( 'action_reset' ), 'class="awesome small"' );

		$_url = $_SERVER['REQUEST_URI'];
		$_url .= strpos( $_url, '?' ) !== FALSE ? '&dl=1' : '?dl=1';
		echo anchor( $_url, 'Download as CSV', 'class="awesome small right"' );

		// --------------------------------------------------------------------------

		echo form_close();

	?>
</div>

<hr />

<script type="text/javascript">
	$( function(){

		$( '.datetime.from' ).datetimepicker(
		{
			dateFormat : 'yy-mm-dd',
			timeFormat : 'HH:mm:ss',
			onClose : function(data)
			{
				if ( data.length && data != '<?=$this->input->get( 'date_from' )?>' )
				{
					$(this).closest( 'form' ).find( 'input[type=submit]' ).click();
				}
			}
		});

		$( '.datetime.to' ).datetimepicker(
		{
			dateFormat : 'yy-mm-dd',
			timeFormat : 'HH:mm:ss',
			onClose : function(data)
			{
				if ( data.length && data != '<?=$this->input->get( 'date_to' )?>' )
				{
					$(this).closest( 'form' ).find( 'input[type=submit]' ).click();
				}
			}
		});

	})
</script>