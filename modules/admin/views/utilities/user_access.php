<p>
	Manage how groups of user's can interface with the site, e.g: grant a specific group access to admin and specify which parts of admin they can view.
</p>

<hr />

<table>

	<thead>
		<tr>
			<th>Name</th>
			<th>Default Homepage</th>
			<th>Options</th>
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
				<?=anchor( 'admin/utilities/edit_group/' . $group->id, 'Edit', 'class="awesome small"' )?>
			</td>
		</tr>
	
	<?php endforeach; ?>
	
	</tbody>

</table>