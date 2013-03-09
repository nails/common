<div class="group-utilities user-access">

	<p><?=lang( 'utilities_user_access_intro' )?></p>
	
	<hr />
	
	<table>

		<thead>
			<tr>
				<th><?=lang( 'utilities_user_access_th_name' )?></th>
				<th><?=lang( 'utilities_user_access_th_homepage' )?></th>
				<th><?=lang( 'utilities_user_access_th_actions' )?></th>
			</tr>
		</thead>
		
		<tbody>
		
		<?php foreach ( $groups AS $group ) : ?>
		
			<tr>
				<td style="vertical-align:top;">
					<strong><?=$group->display_name?></strong>
					<small style="display:block;"><?=$group->description?></small>
				</td>
				<td style="vertical-align:top;">
					<span style="color:#ccc"><?=substr( site_url(), 0, -1 )?></span><?=$group->default_homepage?>
				</td>
				<td>
					<?=anchor( 'admin/utilities/edit_group/' . $group->id, lang( 'action_edit' ), 'class="awesome small"' )?>
				</td>
			</tr>
		
		<?php endforeach; ?>
		
		</tbody>
	
	</table>
	
</div>