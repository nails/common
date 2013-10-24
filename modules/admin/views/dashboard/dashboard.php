<div class="group-dashboard">
	<?php

		echo '<p>' . lang( 'dashboard_welcome_line_1', APP_NAME ) . '</p>';
		echo '<p>' . lang( 'dashboard_welcome_line_2' ) . '</p>';

		if ( $this->admin_help_model->count() ) :

			echo '<p>' . lang( 'dashboard_welcome_line_3', site_url( 'admin/dashboard/help' ) ) . '</p>';

		endif;

		$_field				= array();
		$_field['label']	= 'Some Images';
		$_field['key']		= 'someimages';
		$_field['bucket']	= 'some-bucket';
		$_field['default']	= array( 10,11,12);
		echo form_field_multiimage( $_field );

	?>
</div>