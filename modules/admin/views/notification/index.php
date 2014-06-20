<div class="group-notification overview">
	<p>
		Configure who gets email notifications when certain events happen on site.
		Separate multiple email addresses using a comma; leaving blank will disable
		the notification
	</p>
	<hr />
	<?php

		if ( $notifications ) :

			echo form_open();

			foreach( $notifications AS $grouping => $noti ) :

				echo '<fieldset>';
					echo $noti->label ? '<legend>' . $noti->label . '</legend>' : '';
					echo $noti->description ? '<p>' . $noti->description . '</p><hr />' : '';

					echo '<table>';
						echo '<thead>';
							echo '<tr>';
								echo '<th class="event-label">Event Name</th>';
								echo '<th class="value">Value</th>';
							echo '</tr>';
						echo '<thead>';
						echo '<tbody>';

						foreach( $noti->options AS $key => $label ) :

							$_default = implode( ', ', $this->app_notification_model->get( $key, $grouping ) );

							echo '<tr>';
								echo '<td class="event-label">';
									echo $label;
								echo '</td>';
								echo '<td class="value">';
									$_value = isset( $_POST['notification'][$grouping][$key] ) ? $_POST['notification'][$grouping][$key] : $_default;
									echo form_input( 'notification[' . $grouping . '][' . $key . ']', $_value, 'placeholder="Separate multiple email addresses using a comma"' );
								echo '</td>';
							echo '</tr>';

						endforeach;

						echo '</tbody>';
					echo '</table>';
				echo '</fieldset>';

			endforeach;

			echo '<p>';
				echo form_submit( 'submit', lang( 'action_save_changes' ) );
			echo '</p>';

			echo form_close();

		else :

			echo '<p class="system-alert">';
				echo 'Sorry, there are no configurable notifications.';
			echo '</p>';

		endif;

	?>
</div>