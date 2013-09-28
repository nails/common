<fieldset id="edit-user-emails">

	<legend><?=lang( 'accounts_edit_emails_legend' )?></legend>

	<div class="box-container">

		<table>
			<thead>
				<tr>
					<th><?=lang( 'accounts_edit_emails_th_email' )?></th>
					<th><?=lang( 'accounts_edit_emails_th_primary' )?></th>
					<th><?=lang( 'accounts_edit_emails_th_verified' )?></th>
					<th><?=lang( 'accounts_edit_emails_th_date_added' )?></th>
					<th><?=lang( 'accounts_edit_emails_th_date_verified' )?></th>
				</tr>
			</thead>
			<tbody>
			<?php

				foreach( $user_emails AS $email ) :

					echo '<tr>';
					echo '<td>' . mailto( $email->email ) . '</td>';
					echo '<td>' . ( $email->is_primary ? lang( 'yes' ) : lang( 'no' ) ) . '</td>';
					echo '<td>' . ( $email->is_verified ? lang( 'yes' ) : lang( 'no' ) ) . '</td>';
					echo '<td>' . user_datetime( $email->date_added ) . '</td>';
					echo '<td>' . ( $email->is_verified ? user_datetime( $email->date_added ) : lang( 'accounts_edit_emails_td_not_verified' ) ) . '</td>';
					echo '</tr>';

				endforeach;

			?>
			</tbody>
		</table>

	</div>

</fieldset>