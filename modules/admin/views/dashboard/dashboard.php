<div class="group-dashboard">
	<?php

		echo '<p>' . lang( 'dashboard_welcome_line_1', APP_NAME ) . '</p>';
		echo '<p>' . lang( 'dashboard_welcome_line_2' ) . '</p>';

		if ( $this->admin_help_model->count() ) :

			echo '<p>' . lang( 'dashboard_welcome_line_3', site_url( 'admin/dashboard/help' ) ) . '</p>';

		endif;

	?>
</div>