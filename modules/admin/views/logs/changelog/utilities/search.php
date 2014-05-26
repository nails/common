<div class="search">
	<div class="mask"><?=img( NAILS_ASSETS_URL . 'img/loader/30px-TRANS.gif' )?></div>
	<?php

		$_form = array(
			'method'	=> 'GET'
		);
		echo form_open( NULL, $_form );

		// --------------------------------------------------------------------------

		echo '<span class="label">';

			//	Date range
			echo '<span class="text">Date range:</span>';
			echo '<input type="text" name="date_from" value="' . $this->input->get( 'date_from' ) . '" class="datetime from" placeholder="YYYY-MM-DD HH:MM:SS"/>';

			echo '<span class="text and">-</span>';
			echo '<input type="text" name="date_to" value="' . $this->input->get( 'date_to' ) . '" class="datetime to" placeholder="YYYY-MM-DD HH:MM:SS"/>';

			//	Per Page
			echo '<span class="text and">show</span>';
			$_options			= array();
			$_options[10]	= 10;
			$_options[25]	= 25;
			$_options[50]	= 50;
			$_options[75]	= 75;
			$_options[100]	= 100;

			echo form_dropdown( 'per_page', $_options, $pagination->per_page, 'class="select2" style="width:75px;"' );

			echo '<span class="text and">per page</span>';

		echo '</span>';

		// --------------------------------------------------------------------------

		echo form_submit( 'submit', 'Update Results', 'class="awesome green small"' );
		echo anchor( 'admin/logs/changelog', lang( 'action_reset' ), 'class="awesome small"' );

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