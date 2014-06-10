<div class="group-members groups overview">

	<p>
	<?php

		echo lang( 'accounts_groups_index_intro' );
		if ( user_has_permission( 'admin.accounts.can_create_group' ) ) :

			echo anchor( 'admin/accounts/groups/create', 'Create Group', 'class="awesome small green right"' );

		endif;

	?>
	</p>

	<hr />

	<table>

		<thead>
			<tr>
				<th class="label"><?=lang( 'accounts_groups_index_th_name' )?></th>
				<th class="homepage"><?=lang( 'accounts_groups_index_th_homepage' )?></th>
				<th class="default"><?=lang( 'accounts_groups_index_th_default' )?></th>
				<th class="actions"><?=lang( 'accounts_groups_index_th_actions' )?></th>
			</tr>
		</thead>

		<tbody>

		<?php foreach ( $groups AS $group ) : ?>

			<tr>
				<td class="label">
					<strong><?=$group->label?></strong>
					<small style="display:block;"><?=$group->description?></small>
				</td>
				<td class="homepage">
					<span style="color:#ccc"><?=substr( site_url(), 0, -1 )?></span><?=$group->default_homepage?>
				</td>
				<?php

					if ( $group->is_default ) :

						echo '<td class="default success">';
							echo '<span class="ion-checkmark-circled"></span>';
						echo '</td>';

					else :

						echo '<td class="default error">';
							echo '<span class="ion-close-circled"></span>';
						echo '</td>';

					endif;

				?>
				<td class="actions">
				<?php

					if ( user_has_permission( 'admin.accounts.can_edit_group' ) ) :

						echo anchor( 'admin/accounts/groups/edit/' . $group->id, lang( 'action_edit' ), 'class="awesome small"' );

					endif;

					if ( user_has_permission( 'admin.accounts.can_delete_group' ) ) :

						echo anchor( 'admin/accounts/groups/delete/' . $group->id, lang( 'action_delete' ), 'class="awesome small red confirm" data-body="This action is also not undoable." data-title="Confirm Delete"' );

					endif;

					if ( user_has_permission( 'admin.accounts.can_set_default_group' ) && ! $group->is_default ) :

						echo anchor( 'admin/accounts/groups/set_default/' . $group->id, lang( 'accounts_groups_index_action_set_default' ), 'class="awesome green small"' );

					endif;

				?>
				</td>
			</tr>

		<?php endforeach; ?>

		</tbody>

	</table>

</div>