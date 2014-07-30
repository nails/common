<?php

	$this->load->view( 'structure/header/blank' );

		?>
		<div class="container">
			<div class="row text-center" style="margin-top:1em;">
				<?php

					if ( $user->was_admin() ) :

						echo '<div class="alert alert-info text-left">';
							echo 'Logged in as <strong>' . active_user( 'first_name,last_name' ) . ' (' . active_user( 'email' ) . ')</strong>.';
							echo anchor( $this->session->userdata( 'admin_recovery' )->back_to_admin_url, 'Back to Admin', 'class="pull-right btn btn-sm btn-default" style="margin-top:-0.5em;"' );
						echo '</div>';

					endif;

				?>
				<h1>
					<?=anchor( '', APP_NAME, 'style="text-decoration:none;color:inherit;"' )?>
				</h1>
				<p>
					<?=NAILS_APP_STRAPLINE?>
				</p>
			</div><!-- /.row -->
			<hr />
			<?php

				if ( empty( $_NAILS_DEFAULT_HIDE_SYSTEM_ALERTS ) && ( $success || $error || $message || $notice ) ) :

					echo '<div class="container row">';
						echo $success	? '<p class="alert alert-success">' . $success . '</p>' : '';
						echo $error		? '<p class="alert alert-danger">' . $error . '</p>' : '';
						echo $message	? '<p class="alert alert-warning">' . $message . '</p>' : '';
						echo $notice	? '<p class="alert alert-info">' . $notice . '</p>' : '';
					echo '</div>';

				endif;