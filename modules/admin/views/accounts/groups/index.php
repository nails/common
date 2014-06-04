<div class="group-accounts groups overview">

	<p>
	<?php

		echo lang( 'accounts_groups_index_intro' );
		echo user_has_permission( 'admin.accounts.can_create_group' ) ? anchor( 'admin/accounts/groups/create', 'Create Group', 'class="awesome small green right"' ) : '';

	?>
	</p>

	<hr />

	<table>

		<thead>
			<tr>
				<th><?=lang( 'accounts_groups_index_th_name' )?></th>
				<th><?=lang( 'accounts_groups_index_th_homepage' )?></th>
				<th><?=lang( 'accounts_groups_index_th_actions' )?></th>
			</tr>
		</thead>

		<tbody>

		<?php foreach ( $groups AS $group ) : ?>

			<tr>
				<td style="vertical-align:top;">
					<strong><?=$group->label?></strong>
					<small style="display:block;"><?=$group->description?></small>
				</td>
				<td style="vertical-align:top;">
					<span style="color:#ccc"><?=substr( site_url(), 0, -1 )?></span><?=$group->default_homepage?>
				</td>
				<td>
					<?=user_has_permission( 'admin.accounts.can_edit_group' ) ? anchor( 'admin/accounts/groups/edit/' . $group->id, lang( 'action_edit' ), 'class="awesome small"' ) : ''?>
					<?=user_has_permission( 'admin.accounts.can_delete_group' ) ? anchor( 'admin/accounts/groups/delete/' . $group->id, lang( 'action_delete' ), 'class="awesome small red confirm" data-body="This action is also not undoable." data-title="Confirm Delete"' ) : ''?>
				</td>
			</tr>

		<?php endforeach; ?>

		</tbody>

	</table>

</div>